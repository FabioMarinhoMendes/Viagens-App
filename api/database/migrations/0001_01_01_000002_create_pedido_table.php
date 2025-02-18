<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedido', function (Blueprint $table) {
            $table->id();
            $table->string('user_name', 80);
            $table->unsignedBigInteger('user_id');
            $table->string('destino', 80);
            $table->date('data_partida');
            $table->date('data_retorno');
            $table->enum('status', ['solicitado', 'aprovado', 'cancelado'])->default('solicitado');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users'); // Chave estrangeira para a tabela de usuários        
        });

      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido');
    }
};
