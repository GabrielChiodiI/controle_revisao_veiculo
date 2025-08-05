<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    protected $table = 'servicos';
    protected $primaryKey = 'id_servico';
    public $timestamps = false;

    protected $fillable = [
        'descricao',
        'valor_mao_de_obra',
    ];
}