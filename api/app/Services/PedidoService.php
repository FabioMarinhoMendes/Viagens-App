<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PedidoService
{
    public function criarPedido(array $data, User $user)
    {   
        if (array_key_exists('id', $data)) {
            // Verifica se o pedido esta sendo alterado ou criado
            $pedido = Pedido::find($data['id']);            

            if(!$pedido){
                $pedido = new Pedido();
            }else{
                //Se for cliente e pedido estiver cancelado, não pode ser editado           
                if($pedido->status == 'cancelado' && $user->nivel_acesso == 1){
                    throw new \Exception('Pedido cancelado não pode ser editado.');     
                }
            }
        }else{
            $pedido = new Pedido();
        }
   
        $pedido->data_partida = $data['data_partida'];
        $pedido->data_retorno = $data['data_retorno'];
        $pedido->destino = $data['destino'];
        $pedido->status = 'solicitado';
        $pedido->user_id = $user->id;
        $pedido->user_name = $user->name;
        $pedido->save();

        return $pedido;
    }

    public function alterarStatus(User $user, int $pedidoId, string $status)
    {
        $pedido = Pedido::find($pedidoId);      

        if(!$pedido){
            throw new \Exception('Pedido não encontrado.');     
        }      

        $this->validarAlteracaoStatus($pedido, $user, $status);
  
        $pedido->status = $status;
        $pedido->save();

        return $pedido;
    }

    private function validarAlteracaoStatus(Pedido $pedido, User $user, string $status)
    {
        if ($user->nivel_acesso  == 1) {
            if ($pedido->user_id === $user->id) {
                throw new \Exception('O usuário que fez o pedido não pode alterar o status do mesmo.');
            }
    
            if ($status === 'solicitado') {
                throw new \Exception('O usuário pode alterar o status apenas para Aprovado ou Cancelado.');
            }        
    
            if ($status === 'cancelado' && $pedido->status === 'cancelado') {
                throw new \Exception('Este pedido já está cancelado. Verifique seus pedidos para mais informações.');
            }
    
            if (!$this->podeCancelarPedidoAprovado($pedido)) {
                throw new \Exception('Informamos que o prazo de 24 horas para cancelamento foi excedido, não sendo mais possível prosseguir com a solicitação.');
            }
        } 
    }

    public function cancelarPedido(User $user, int $pedidoId)
    {
        $pedido = Pedido::find($pedidoId);      

        if(!$pedido){
            throw new \Exception('Pedido não encontrado.');     
        }

        if ($pedido->status === 'cancelado') {
            throw new \Exception('Este pedido já está cancelado. Verifique seus pedidos para mais informações.');
        }

        if ($user->nivel_acesso == 1 && !$this->podeCancelarPedidoAprovado($pedido)) {
            throw new \Exception('Informamos que o prazo de 24 horas para cancelamento foi excedido, não sendo mais possível prosseguir com a solicitação.');
        }

        $pedido->status = 'cancelado';
        $pedido->save();
    }

    public function obterPedidoPorId(User $user, int $pedidoId)
    {
        $pedido = Pedido::find($pedidoId);

        if(!$pedido){
            throw new \Exception('Pedido não encontrado.');     
        }     

        if ($user->nivel_acesso == 1 && $pedido->user_id !== $user->id) {
            throw new \Exception('Usuário não autorizado a ver este pedido.');
        }

        return $pedido;
    }    

    private function podeCancelarPedidoAprovado(Pedido $pedido)
    {
        //Verifica se o pedido existe e está aprovado
        if ($pedido && $pedido->status == 'aprovado') {
            //Verifica o tempo limite, se é hoje a data de partida e se a viagem ja aconteceu, se for, não pode cancelar   
            $dataAprovacao = Carbon::parse($pedido->data_partida);
            $agora = Carbon::now();  
            if ($dataAprovacao->diffInHours($agora) > 24 || date('Y-m-d') >= $pedido->data_partida) {
                return false;
            } 
        }
       
        return true;
    }
}