<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RevisaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('revisoes')->insert([
            ['data_inicio' => '2025-01-10 08:00:00', 'data_fim' => '09:00:00', 'quilometragem' => 20000],
            ['data_inicio' => '2025-01-11 09:00:00', 'data_fim' => '10:15:00', 'quilometragem' => 15000],
            ['data_inicio' => '2025-01-12 10:00:00', 'data_fim' => '11:00:00', 'quilometragem' => 22000],
            ['data_inicio' => '2025-01-13 11:00:00', 'data_fim' => '12:00:00', 'quilometragem' => 12000],
            ['data_inicio' => '2025-01-14 08:30:00', 'data_fim' => '09:45:00', 'quilometragem' => 18500],
            ['data_inicio' => '2025-01-15 09:20:00', 'data_fim' => '10:30:00', 'quilometragem' => 25000],
            ['data_inicio' => '2025-01-16 10:45:00', 'data_fim' => '11:55:00', 'quilometragem' => 14000],
            ['data_inicio' => '2025-01-17 13:00:00', 'data_fim' => '14:00:00', 'quilometragem' => 17000],
            ['data_inicio' => '2025-01-18 14:10:00', 'data_fim' => '15:20:00', 'quilometragem' => 30000],
            ['data_inicio' => '2025-01-19 15:15:00', 'data_fim' => '16:25:00', 'quilometragem' => 27000],
        ]);
    }
}
