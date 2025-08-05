<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PrecisaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('precisas')->insert([
            ['fk_peca_codigo' => 1, 'fk_servico_id_servico' => 1],
            ['fk_peca_codigo' => 2, 'fk_servico_id_servico' => 2],
            ['fk_peca_codigo' => 3, 'fk_servico_id_servico' => 3],
            ['fk_peca_codigo' => 4, 'fk_servico_id_servico' => 4],
            ['fk_peca_codigo' => 5, 'fk_servico_id_servico' => 5],
            ['fk_peca_codigo' => 6, 'fk_servico_id_servico' => 6],
            ['fk_peca_codigo' => 7, 'fk_servico_id_servico' => 7],
            ['fk_peca_codigo' => 8, 'fk_servico_id_servico' => 8],
            ['fk_peca_codigo' => 9, 'fk_servico_id_servico' => 9],
            ['fk_peca_codigo' => 10, 'fk_servico_id_servico' => 10],
        ]);
    }
}
