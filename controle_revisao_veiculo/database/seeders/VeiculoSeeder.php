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
            // João Silva com 3 veículos
            ['placa' => 'ABC1A11', 'marca' => 'Fiat',      'modelo' => 'Uno',      'ano' => 2010, 'fk_cliente_id_cliente' => 1],
            ['placa' => 'DEF2B22', 'marca' => 'Fiat',      'modelo' => 'Palio',    'ano' => 2012, 'fk_cliente_id_cliente' => 1],
            ['placa' => 'XYZ9X99', 'marca' => 'Fiat',      'modelo' => 'Argo',     'ano' => 2018, 'fk_cliente_id_cliente' => 1],

            // Maria com 2 veículos
            ['placa' => 'GHI3C33', 'marca' => 'Ford',      'modelo' => 'Ka',       'ano' => 2015, 'fk_cliente_id_cliente' => 2],
            ['placa' => 'JKL4D44', 'marca' => 'Ford',      'modelo' => 'Fiesta',   'ano' => 2013, 'fk_cliente_id_cliente' => 2],

            // João Costa com 1 veículo
            ['placa' => 'MNO5E55', 'marca' => 'Chevrolet', 'modelo' => 'Onix',     'ano' => 2017, 'fk_cliente_id_cliente' => 3],

            // Ana Pereira com 2 veículos
            ['placa' => 'PQR6F66', 'marca' => 'Toyota',    'modelo' => 'Corolla',  'ano' => 2019, 'fk_cliente_id_cliente' => 4],
            ['placa' => 'STU7G77', 'marca' => 'Toyota',    'modelo' => 'Etios',    'ano' => 2018, 'fk_cliente_id_cliente' => 4],

            // Bruno Souza com 2 veículos
            ['placa' => 'VWX8H88', 'marca' => 'Volkswagen','modelo' => 'Gol',      'ano' => 2011, 'fk_cliente_id_cliente' => 5],
            ['placa' => 'YZA9I99', 'marca' => 'Volkswagen','modelo' => 'Polo',     'ano' => 2015, 'fk_cliente_id_cliente' => 5],
            
            // Mais exemplos, clientes 6, 7, 8, 9, 10 com 1 veículo cada
            ['placa' => 'BCD0J00', 'marca' => 'Peugeot',   'modelo' => '208',      'ano' => 2019, 'fk_cliente_id_cliente' => 6],
            ['placa' => 'FGH1K23', 'marca' => 'Honda',     'modelo' => 'Civic',    'ano' => 2017, 'fk_cliente_id_cliente' => 7],
            ['placa' => 'QWE1L23', 'marca' => 'Hyundai',   'modelo' => 'HB20',     'ano' => 2016, 'fk_cliente_id_cliente' => 8],
            ['placa' => 'RTY1M23', 'marca' => 'Nissan',    'modelo' => 'March',    'ano' => 2013, 'fk_cliente_id_cliente' => 9],
            ['placa' => 'UIO1N23', 'marca' => 'Renault',   'modelo' => 'Sandero',  'ano' => 2018, 'fk_cliente_id_cliente' => 10],
        ]);
    }
}
