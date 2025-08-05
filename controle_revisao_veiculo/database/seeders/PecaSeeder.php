<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PecaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pecas')->insert([
            ['descricao' => 'Óleo 5W30', 'quantidade' => 50, 'preco' => 35.00],
            ['descricao' => 'Pneu Aro 14', 'quantidade' => 20, 'preco' => 200.00],
            ['descricao' => 'Filtro de ar', 'quantidade' => 40, 'preco' => 25.00],
            ['descricao' => 'Pastilha de freio', 'quantidade' => 35, 'preco' => 90.00],
            ['descricao' => 'Bateria 60Ah', 'quantidade' => 15, 'preco' => 350.00],
            ['descricao' => 'Filtro de óleo', 'quantidade' => 45, 'preco' => 18.00],
            ['descricao' => 'Correia dentada', 'quantidade' => 25, 'preco' => 120.00],
            ['descricao' => 'Amortecedor', 'quantidade' => 30, 'preco' => 160.00],
            ['descricao' => 'Velas de ignição', 'quantidade' => 50, 'preco' => 22.00],
            ['descricao' => 'Kit embreagem', 'quantidade' => 10, 'preco' => 500.00],
        ]);
    }
}
