<?php

namespace App\Observers;

use App\Models\Pedido;
use Illuminate\Support\Facades\Mail;

class PedidoObserver
{
    /**
     * Handle the Pedido "updated" event.
     */
    public function updated(Pedido $pedido): void
    {
        if ($pedido->status == 'aprovado' || $pedido->status == 'cancelado') {          
            Mail::raw('O status do seu pedido foi atualizado para: '.$pedido->status, function ($message) {
                $message->to('fabiomuonlinec@gmail.com')->subject('Status do pedido Onfly');
            });
        }
    }
   
}
