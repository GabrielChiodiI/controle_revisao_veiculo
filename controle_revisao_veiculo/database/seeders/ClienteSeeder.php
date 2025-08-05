<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('clientes')->insert([
            ['nome' => 'João', 'sobrenome' => 'Silva', 'cpf' => '12345678901', 'telefone' => '11999990001', 'email' => 'joao1@exemplo.com', 'data_nascimento' => '1990-01-01', 'sexo' => 'Masculino'],
            ['nome' => 'Maria', 'sobrenome' => 'Oliveira', 'cpf' => '12345678902', 'telefone' => '11999990002', 'email' => 'maria2@exemplo.com', 'data_nascimento' => '1989-02-02', 'sexo' => 'Feminino'],
            ['nome' => 'Carlos', 'sobrenome' => 'Santos', 'cpf' => '12345678903', 'telefone' => '11999990003', 'email' => 'carlos3@exemplo.com', 'data_nascimento' => '1985-03-03', 'sexo' => 'Masculino'],
            ['nome' => 'Ana', 'sobrenome' => 'Pereira', 'cpf' => '12345678904', 'telefone' => '11999990004', 'email' => 'ana4@exemplo.com', 'data_nascimento' => '1995-04-04', 'sexo' => 'Feminino'],
            ['nome' => 'Bruno', 'sobrenome' => 'Souza', 'cpf' => '12345678905', 'telefone' => '11999990005', 'email' => 'bruno5@exemplo.com', 'data_nascimento' => '1991-05-05', 'sexo' => 'Masculino'],
            ['nome' => 'Fernanda', 'sobrenome' => 'Alves', 'cpf' => '12345678906', 'telefone' => '11999990006', 'email' => 'fernanda6@exemplo.com', 'data_nascimento' => '1992-06-06', 'sexo' => 'Feminino'],
            ['nome' => 'Rafael', 'sobrenome' => 'Lima', 'cpf' => '12345678907', 'telefone' => '11999990007', 'email' => 'rafael7@exemplo.com', 'data_nascimento' => '1987-07-07', 'sexo' => 'Masculino'],
            ['nome' => 'Juliana', 'sobrenome' => 'Costa', 'cpf' => '12345678908', 'telefone' => '11999990008', 'email' => 'juliana8@exemplo.com', 'data_nascimento' => '1994-08-08', 'sexo' => 'Feminino'],
            ['nome' => 'Eduardo', 'sobrenome' => 'Ramos', 'cpf' => '12345678909', 'telefone' => '11999990009', 'email' => 'eduardo9@exemplo.com', 'data_nascimento' => '1993-09-09', 'sexo' => 'Masculino'],
            ['nome' => 'Patricia', 'sobrenome' => 'Barros', 'cpf' => '12345678910', 'telefone' => '11999990010', 'email' => 'patricia10@exemplo.com', 'data_nascimento' => '1996-10-10', 'sexo' => 'Feminino'],
        ]);
    }
}
