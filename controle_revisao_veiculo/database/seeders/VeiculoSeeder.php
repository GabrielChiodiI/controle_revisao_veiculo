<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VeiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('veiculos')->insert([
            ['placa' => 'ABC1A11', 'marca' => 'Fiat', 'modelo' => 'Uno', 'ano' => 2010, 'fk_cliente_id_cliente' => 1],
            ['placa' => 'DEF2B22', 'marca' => 'Ford', 'modelo' => 'Ka', 'ano' => 2012, 'fk_cliente_id_cliente' => 2],
            ['placa' => 'GHI3C33', 'marca' => 'Chevrolet', 'modelo' => 'Onix', 'ano' => 2015, 'fk_cliente_id_cliente' => 3],
            ['placa' => 'JKL4D44', 'marca' => 'Volkswagen', 'modelo' => 'Gol', 'ano' => 2011, 'fk_cliente_id_cliente' => 4],
            ['placa' => 'MNO5E55', 'marca' => 'Renault', 'modelo' => 'Sandero', 'ano' => 2018, 'fk_cliente_id_cliente' => 5],
            ['placa' => 'PQR6F66', 'marca' => 'Toyota', 'modelo' => 'Corolla', 'ano' => 2020, 'fk_cliente_id_cliente' => 6],
            ['placa' => 'STU7G77', 'marca' => 'Honda', 'modelo' => 'Civic', 'ano' => 2017, 'fk_cliente_id_cliente' => 7],
            ['placa' => 'VWX8H88', 'marca' => 'Hyundai', 'modelo' => 'HB20', 'ano' => 2016, 'fk_cliente_id_cliente' => 8],
            ['placa' => 'YZA9I99', 'marca' => 'Nissan', 'modelo' => 'March', 'ano' => 2013, 'fk_cliente_id_cliente' => 9],
            ['placa' => 'BCD0J00', 'marca' => 'Peugeot', 'modelo' => '208', 'ano' => 2019, 'fk_cliente_id_cliente' => 10],
        ]);
    }
}
