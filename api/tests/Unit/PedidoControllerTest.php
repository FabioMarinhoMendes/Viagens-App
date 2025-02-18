<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Models\User;
use App\Models\Pedido;
use App\Services\PedidoService;
use App\Http\Controllers\PedidoController;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class PedidoControllerTest extends TestCase
{
    use DatabaseTransactions;

    private PedidoService $pedidoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pedidoService = new PedidoService();
    }


    // Login

    # php artisan test --filter=PedidoControllerTest::test_login_com_jwt
    public function test_login_com_jwt()
    {
        //Cria um usuário para teste
        $usuario = User::factory()->create();
        $dados = [
            'email' => $usuario->email,
            'password' => 'password' // Senha padrão do Factory
        ];

        $response = $this->postJson('/api/auth/login', $dados);       
        $response->assertStatus(200);

        //Verifica se a resposta tem o token JWT
        $response->assertJsonStructure([
            'token'
        ]);
      
        // Verifica se o usuário foi autenticado
        $this->assertAuthenticatedAs($usuario);
    }


    //Criar Pedido

    # php artisan test --filter=PedidoControllerTest::test_criar_pedido
    public function test_criar_pedido()
    {        
        $user = User::factory()->create();
        $dadosPedido = [
            'data_partida' => '2025-02-17',
            'data_retorno' => '2025-02-17',
            'destino' => 'Cerejeiras',
        ];      
        
        $pedido = $this->pedidoService->criarPedido($dadosPedido, $user);

        $this->assertInstanceOf(Pedido::class, $pedido);
        $this->assertEquals($dadosPedido['data_partida'], $pedido->data_partida);
        $this->assertEquals($dadosPedido['data_retorno'], $pedido->data_retorno);
        $this->assertEquals($dadosPedido['destino'], $pedido->destino);
        $this->assertEquals('solicitado', $pedido->status); // Verifica se o pedido foi gerado corretamente com o status 'solicitado'
        $this->assertEquals($user->id, $pedido->user_id);
        $this->assertEquals($user->name, $pedido->user_name);

    }

    # Verifica se o pedido esta sendo alterado corretamente
    # php artisan test --filter=PedidoControllerTest::test_criar_pedido_verificar_exception
    public function test_criar_pedido_verificar_exception()
    {        
        $user = User::factory()->create();
        $pedido = Pedido::factory()->create(['status' => 'cancelado']);

        // Verifica se o pedido foi criado
        $this->assertDatabaseHas('pedido', [
            'id' => $pedido->id,
        ]); 
    
        $dadosPedido = [
            'data_partida' => '2025-02-17',
            'data_retorno' => '2025-02-17',
            'destino' => 'Cerejeiras',          
            'id' => $pedido->id, //se existir o id como parametro, o pedido vai ser alterado
        ];      

        // Espera uma exceção
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Pedido cancelado não pode ser editado.');
        
        $pedido = $this->pedidoService->criarPedido($dadosPedido, $user);
    }

    
    //Alterar Status

    # php artisan test --filter=PedidoControllerTest::test_alterar_status_falha_nivel_acesso
    public function test_alterar_status_falha_nivel_acesso()
    {        
        $user = User::factory()->create();
        $user->nivel_acesso = 1;
        $pedido = Pedido::factory()->create(['user_id' => $user->id]);

        $dadosPedido = [
            'status' => 'cancelado',              
            'id' => $pedido->id,      
        ];      
        
        // Espera uma exceção
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('O usuário que fez o pedido não pode alterar o status do mesmo.');

        $pedido = $this->pedidoService->alterarStatus($user, $dadosPedido['id'], $dadosPedido['status']);     
    }

    # php artisan test --filter=PedidoControllerTest::test_alterar_status_falha_administrador
    public function test_alterar_status_falha_exception()
    {        
        $user = User::factory()->create();
        $user->nivel_acesso = 1;
        $pedido = Pedido::factory()->create(['user_id' => $user->id]);

        $dadosPedido = [
            'status' => 'solicitado', // Se status 'solicidado', gera uma exception       
            'id' => $pedido->id,      
        ];      
        
        // Espera uma exceção
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('O usuário que fez o pedido não pode alterar o status do mesmo.');

        $pedido = $this->pedidoService->alterarStatus($user, $dadosPedido['id'], $dadosPedido['status']);    
    }


    //Obter Pedido

    # php artisan test --filter=PedidoControllerTest::test_obter_pedido_por_id
    public function test_obter_pedido_por_id()
    {       
        $user = User::factory()->create();   
        $pedido = Pedido::factory()->create(['user_id' => $user->id]);       
      
        $dadosPedido = [                        
            'id' => $pedido->id,      
        ];         

        $pedido = $this->pedidoService->obterPedidoPorId($user, $dadosPedido['id']);       
        $this->assertInstanceOf(Pedido::class, $pedido);       
    }

    # php artisan test --filter=PedidoControllerTest::test_obter_pedido_por_id_falha
    public function test_obter_pedido_por_id_falha()
    {       
        $user = User::factory()->create();   
        $user->id = 2; 
        $pedido = Pedido::factory()->create();       
      
        $dadosPedido = [                        
            'id' => $pedido->id,      
        ];                   

        // Espera uma exceção
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Usuário não autorizado a ver este pedido.');

        $pedido = $this->pedidoService->obterPedidoPorId($user, $dadosPedido['id']);        
    }
  
    //Cancelar Pedido

    # Verifica se a Exception referente ao prazo de 24 horas, esta correta
    # php artisan test --filter=PedidoControllerTest::test_cancelar_pedido_tempo_para_cancelar_excedido_falha
    public function test_cancelar_pedido_tempo_para_cancelar_excedido_falha()
    {    
        $user = User::factory()->create();   
        $pedido = Pedido::factory()->create(['user_id' => $user->id]);       
      
        // Espera uma exceção
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Informamos que o prazo de 24 horas para cancelamento foi excedido, não sendo mais possível prosseguir com a solicitação.');

        $pedido = $this->pedidoService->cancelarPedido($user, $pedido->id);                  
    } 
    
    # Verifica se a Exception referente ao status cancelado, esta correta
    # php artisan test --filter=PedidoControllerTest::test_cancelar_pedido_status_cancelado_falha
    public function test_cancelar_pedido_status_cancelado_falha()
    {    
        $user = User::factory()->create();   
        $pedido = Pedido::factory()->create(['user_id' => $user->id, 'status' => 'cancelado']);       
      
        // Espera uma exceção
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Este pedido já está cancelado. Verifique seus pedidos para mais informações.');

        $pedido = $this->pedidoService->cancelarPedido($user, $pedido->id); 
               
    } 
    

}
