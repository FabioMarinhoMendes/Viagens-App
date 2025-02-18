<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PedidoAtualizado extends Mailable
{
    use Queueable, SerializesModels;
  
    public $mensagem;

    /**
     * Create a new message instance.
     */
    public function __construct()    {
       
        $this->mensagem = $mensagem;
    }

    public function build()
    {
        return $this->from('fabiomuonlinec@gmail.com', 'Onfly')
                    ->subject('Atualização do status do seu pedido')
                    ->html($this->mensagem); 
    }
}
