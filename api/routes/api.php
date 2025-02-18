<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PedidoController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::post("/auth/login", [AuthController::class, "login"])->name("login");
Route::post("/auth/cadastrar", [AuthController::class, "cadastrar"])->name("cadastrar");
Route::post("/auth/logout", [AuthController::class, "logout"])->middleware("apitoken");
Route::post("/auth/acesso", [AuthController::class, "acesso"])->middleware("apitoken");

Route::group(["middleware" => "apitoken", "prefix" => "pedidos"], function () {
    Route::post("obterPedidos", [PedidoController::class, "obterPedidos"]);
    Route::post("obterUsuarios", [PedidoController::class, "obterUsuarios"]);    
    Route::post("criarPedido", [PedidoController::class, "criarPedido"]);    
    Route::post("alterarStatus", [PedidoController::class, "alterarStatus"]);  
    Route::post("obterPedidoPorId", [PedidoController::class, "obterPedidoPorId"]);      
    Route::post("cancelarPedido", [PedidoController::class, "cancelarPedido"]);      
});

