<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PedidoService;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\Pedido;
use App\Models\User;
use Carbon\Carbon;


class PedidoController extends Controller
{     
    //1 - Criar um pedido de viagem: Um pedido deve incluir o ID do pedido,
    //o nome do solicitante, o destino, a data de ida, a data de volta e o status (solicitado, aprovado, cancelado).
    public function criarPedido(Request $request, PedidoService $pedidoService)
    {   
        // Validação dos dados
        $fields = Validator::make($request->all(), [
            'data_partida' => 'required|date',
            'data_retorno' => 'required|date',
            'destino' => 'required',
            'user_id' => 'sometimes|required|exists:users,id', // o user_id só é obrigatório para administrador do sistema
            'id' => 'sometimes|required|exists:pedido,id', // o id só é obrigatório quando for editar o pedido
        ]);

        // Retorna erros de validação
        if ($fields->fails()) {
            return response()->json(["status" => false, "message" => "Para prosseguir, complete os campos obrigatórios."], 422);   
        }

        // Verifica se a data de partira e menor que a data de retorno
        if ($request->input('data_partida') > $request->input('data_retorno')) {
            return response()->json(["status" => false, "message" => "A data de partida é anterior à data de retorno. Por favor, verifique as datas informadas."], 422); 
        }

        $user = Auth::user();
        //Se for administrador, pode criar pedido para qualquer usuario
        //Se o parametro user_id nao for informado, o pedido vai ser criado com os dados do usuário logado
        if ($user->nivel_acesso == 0 && $request->has('user_id')) {
            $user = User::find($request->input('user_id'));
            if (!$user) {
                return response()->json(["status" => false, "message" => "Usuario não encontrado. "], 422);   
            }
        } 

        try {
            $pedido = $pedidoService->criarPedido($request->all(), $user);      
            return response()->json(["status" => true, "message" => "Sua viagem foi salva com sucesso."], 200);
        } catch (\Exception $e) {
            return response()->json(["status" => false, "message" => $e->getMessage()], 500);
        }               
    }

    //2 - Atualizar o status de um pedido de viagem: Possibilitar a atualização do status para "aprovado" ou "cancelado".
    //(nota: o usuário que fez o pedido não pode alterar o status do mesmo)
    public function alterarStatus(Request $request, PedidoService $pedidoService)
    { 
        // Validação dos dados       
        $fields = Validator::make($request->all(), [
            'status' => 'required|in:solicitado,aprovado,cancelado', 
            'id' => 'required|exists:pedido,id',        
        ]);

        // Retorna erros de validação
        if ($fields->fails()) {
            return response()->json(["status" => false, "message" => "Para prosseguir, complete os campos obrigatórios. "], 422);   
        }   
        
        try {
            $pedido = $pedidoService->alterarStatus(Auth::user(), $request->input('id'), $request->input('status'));  
            return response()->json(["status" => true, "message" => "Seu status foi salvo com sucesso."], 200);
        } catch (\Exception $e) {
            // Em caso de erro
            return response()->json(["status" => false, "message" => $e->getMessage()], 500);
        } 
    }

    //3 - Consultar um pedido de viagem: Retornar as informações detalhadas de um pedido de viagem com base no ID fornecido.
    public function obterPedidoPorId(Request $request, PedidoService $pedidoService)
    {
        // Validação dos dados
        $fields = Validator::make($request->all(), [  
            'id' => 'required|exists:pedido,id',       
        ]);

        // Retorna erros de validação
        if ($fields->fails()) {
            return response()->json(["status" => false, "message" => "Para prosseguir, informe corretamente o id do pedido. "], 422);   
        }      

        $user = Auth::user();
        try {
            $pedidos = $pedidoService->obterPedidoPorId($user, $request->input('id'));
            return response()->json(["status" => true, "message" => "Pedido listado com sucesso.", "pedidos" => $pedidos], 200);
        } catch (\Exception $e) {
            // Em caso de erro
            return response()->json(["status" => false, "message" => $e->getMessage()], 500);
        }    
       
    }   

    //Cancelar pedido de viagem após aprovação: 
    public function cancelarPedido(Request $request, PedidoService $pedidoService)
    {                
        // Validação dos dados
        $fields = Validator::make($request->all(), [  
            'id' => 'required|exists:pedido,id',          
        ]);

        // Retorna erros de validação
        if ($fields->fails()) {
            return response()->json(["status" => false, "message" => "Para prosseguir, informe corretamente o id do pedido. "], 422);   
        }

        try {
            $pedidoService->cancelarPedido(Auth::user(), $request->input('id'));
            return response()->json(["status" => true, "message" => "Seu pedido foi cancelado com sucesso."], 200);
        } catch (\Exception $e) {
            // Em caso de erro
            return response()->json(["status" => false, "message" => $e->getMessage()], 500);
        }  
       
    } 

    //4 - Listar todos os pedidos de viagem: Retornar todos os pedidos de
    //viagem cadastrados, com a opção de filtrar por status.
    //5 - Filtragem por período e destino: Adicionar filtros para listar pedidos de viagem
    //por período de tempo (ex: pedidos feitos ou com datas de viagem dentro de uma faixa de datas) e/ou por destino.
    public function obterPedidos(Request $request)
    {             
        try {
            // Validação dos dados, somente quanto tiver filtros
            $fields = Validator::make($request->all(), [
                'data_partida' => 'sometimes|required|date',
                'data_retorno' => 'sometimes|required|date',
                'destino' => 'sometimes|required',
                'status' => 'sometimes|required',
                'user_id' => 'sometimes|required|exists:users,id', 
                'id' => 'sometimes|required|exists:pedido,id',                 
            ]);

            // Retorna erros de validação
            if ($fields->fails()) {
                return response()->json(["status" => false, "message" => "Para prosseguir, complete os campos obrigatórios. "], 422); 
            }

            // Obtém o nivel de acesso do usuário logado
            $nivelAcesso = Auth::user()->nivel_acesso;

            //Obter pedido e filtros
            $filtros = $request->filtros;          
            $pedidos = (new Pedido)->listarPedidos($filtros,$nivelAcesso);          
          
            return response()->json(["status" => true, "message" => "Pedidos foram listados com sucesso.", "pedidos" => $pedidos], 200);
        } catch (\Exception $e) {
            // Em caso de erro
            return response()->json(["status" => false, "message" => "Houve um problema ao processar sua solicitação. Por favor, tente novamente."], 400);
        } 
    }  

    public function obterUsuarios(Request $request)
    {        
        try {          
            // Obtém o nivel de acesso do usuário logado
            $nivelAcesso = Auth::user()->nivel_acesso;

            //Se for Administrador
            if($nivelAcesso == 0){
                // Obtém todos os usuários do banco de dados  
                $users = User::all(); 
            }else{
                //Obtém apenas o usuario logado
                $users = [Auth::user()];
            }                              
          
            return response()->json(["status" => true, "message" => "Seus usuarios foram listados com sucesso.", "users" => $users, "nivelAcesso" => $nivelAcesso], 200);
        } catch (\Exception $e) {
            // Em caso de erro
            return response()->json(["status" => false, "message" => "Houve um problema ao processar sua solicitação. Por favor, tente novamente."], 400);
        }       
       
    }  

    //6 - Notificação de aprovação ou cancelamento: Sempre que um pedido for aprovado ou cancelado,
    //uma notificação deve ser enviada para o usuário que solicitou o pedido.
    //Esta função é executada pelo Observer de Pedidos (PedidoObserver) e é acionada pelos eventos de atualização do Models Pedido.
    
}
