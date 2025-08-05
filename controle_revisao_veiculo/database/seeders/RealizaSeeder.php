<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RealizaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('realizas')->insert([
            ['fk_veiculo_placa' => 'ABC1A11', 'fk_revisao_id_revisao' => 1],
            ['fk_veiculo_placa' => 'DEF2B22', 'fk_revisao_id_revisao' => 2],
            ['fk_veiculo_placa' => 'GHI3C33', 'fk_revisao_id_revisao' => 3],
            ['fk_veiculo_placa' => 'JKL4D44', 'fk_revisao_id_revisao' => 4],
            ['fk_veiculo_placa' => 'MNO5E55', 'fk_revisao_id_revisao' => 5],
            ['fk_veiculo_placa' => 'PQR6F66', 'fk_revisao_id_revisao' => 6],
            ['fk_veiculo_placa' => 'STU7G77', 'fk_revisao_id_revisao' => 7],
            ['fk_veiculo_placa' => 'VWX8H88', 'fk_revisao_id_revisao' => 8],
            ['fk_veiculo_placa' => 'YZA9I99', 'fk_revisao_id_revisao' => 9],
            ['fk_veiculo_placa' => 'BCD0J00', 'fk_revisao_id_revisao' => 10],
        ]);
    }
}
