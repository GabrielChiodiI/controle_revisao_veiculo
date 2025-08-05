<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('servicos')->insert([
            ['descricao' => 'Troca de óleo', 'valor_mao_de_obra' => 100.0],
            ['descricao' => 'Alinhamento', 'valor_mao_de_obra' => 80.0],
            ['descricao' => 'Balanceamento', 'valor_mao_de_obra' => 70.0],
            ['descricao' => 'Troca de pneu', 'valor_mao_de_obra' => 50.0],
            ['descricao' => 'Revisão dos freios', 'valor_mao_de_obra' => 150.0],
            ['descricao' => 'Revisão elétrica', 'valor_mao_de_obra' => 200.0],
            ['descricao' => 'Troca de filtro de ar', 'valor_mao_de_obra' => 60.0],
            ['descricao' => 'Reparo da suspensão', 'valor_mao_de_obra' => 220.0],
            ['descricao' => 'Recarga de ar-condicionado', 'valor_mao_de_obra' => 120.0],
            ['descricao' => 'Troca de bateria', 'valor_mao_de_obra' => 180.0],
        ]);
    }
}
