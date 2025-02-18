<?php

namespace Database\Factories;

use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    public function definition()
    {
        return [
            'user_name' => fake()->name(),
            'user_id' => 1,
            'destino' => 'Belo Horizonte',        
            'data_partida' => $this->faker->date(),
            'data_retorno' => $this->faker->date(),
            'status' => 'aprovado',
        ];
    }
}
