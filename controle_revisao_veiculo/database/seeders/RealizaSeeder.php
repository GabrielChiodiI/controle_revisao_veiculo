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
            // Veículo ABC1A11 com 3 revisões
            ['fk_veiculo_placa' => 'ABC1A11', 'fk_revisao_id_revisao' => 1],
            ['fk_veiculo_placa' => 'ABC1A11', 'fk_revisao_id_revisao' => 2],
            ['fk_veiculo_placa' => 'ABC1A11', 'fk_revisao_id_revisao' => 3],

            // Veículo DEF2B22 com 2 revisões
            ['fk_veiculo_placa' => 'DEF2B22', 'fk_revisao_id_revisao' => 4],
            ['fk_veiculo_placa' => 'DEF2B22', 'fk_revisao_id_revisao' => 5],

            // Veículo GHI3C33 com 2 revisões
            ['fk_veiculo_placa' => 'GHI3C33', 'fk_revisao_id_revisao' => 6],
            ['fk_veiculo_placa' => 'GHI3C33', 'fk_revisao_id_revisao' => 7],

            // Veículo JKL4D44 com 1 revisão
            ['fk_veiculo_placa' => 'JKL4D44', 'fk_revisao_id_revisao' => 8],

            // Outros veículos com pelo menos 1 revisão
            ['fk_veiculo_placa' => 'MNO5E55', 'fk_revisao_id_revisao' => 9],
            ['fk_veiculo_placa' => 'PQR6F66', 'fk_revisao_id_revisao' => 10],

            // Veículo XYZ9X99 com 2 revisões
            ['fk_veiculo_placa' => 'XYZ9X99', 'fk_revisao_id_revisao' => 11],
            ['fk_veiculo_placa' => 'XYZ9X99', 'fk_revisao_id_revisao' => 12],

            // Veículo VWX8H88 com 1 revisão
            ['fk_veiculo_placa' => 'VWX8H88', 'fk_revisao_id_revisao' => 13],
        ]);
    }
}
