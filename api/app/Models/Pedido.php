<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Pedido extends Model
{
    use HasFactory;

    protected $table = "pedido";   
    protected $primaryKey = "id";    

    // Se um pedido pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class); 
    }

    public function listarPedidos($filtros, $nivelAcesso)
    {
        $pedidos = $this->where(function ($query) use ($filtros, $nivelAcesso){
            if($nivelAcesso == 1){
                $query->where('user_id', Auth::id());
            }
            if (!is_null($filtros)){
                if (isset($filtros["id"]) && $filtros["id"] != '' ){
                    $query->where('id', $filtros["id"]);
                }
                if (isset($filtros["data_partida"]) && $filtros["data_partida"] != '' ){
                    $query->where('data_partida', $filtros["data_partida"]);
                }
                if (isset($filtros["data_retorno"]) && $filtros["data_retorno"] != '' ){
                    $query->where('data_retorno', $filtros["data_retorno"]);
                }
                if (isset($filtros["destino"]) && $filtros["destino"] != '' ){
                    $query->where('destino', $filtros["destino"]);
                }
                if (isset($filtros["user_id"]) && $filtros["user_id"] != '' ){
                    $query->where('user_id', $filtros["user_id"]);
                }
                if (isset($filtros["status"]) && $filtros["status"] != '' && $filtros["status"] != 'geral'){
                    $query->where('status', $filtros["status"]);
                }                
            }
        })
        ->orderBy("id", "desc")
        ->get();    
        return $pedidos;
    }
}
