<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ContemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('contems')->insert([
            ['fk_servico_id_servico' => 1, 'fk_revisao_id_revisao' => 1],
            ['fk_servico_id_servico' => 2, 'fk_revisao_id_revisao' => 2],
            ['fk_servico_id_servico' => 3, 'fk_revisao_id_revisao' => 3],
            ['fk_servico_id_servico' => 4, 'fk_revisao_id_revisao' => 4],
            ['fk_servico_id_servico' => 5, 'fk_revisao_id_revisao' => 5],
            ['fk_servico_id_servico' => 6, 'fk_revisao_id_revisao' => 6],
            ['fk_servico_id_servico' => 7, 'fk_revisao_id_revisao' => 7],
            ['fk_servico_id_servico' => 8, 'fk_revisao_id_revisao' => 8],
            ['fk_servico_id_servico' => 9, 'fk_revisao_id_revisao' => 9],
            ['fk_servico_id_servico' => 10, 'fk_revisao_id_revisao' => 10],
        ]);
    }
}
