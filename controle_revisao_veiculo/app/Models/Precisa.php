<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Precisa extends Model
{
    protected $table = 'precisas';
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ['fk_peca_codigo', 'fk_servico_id_servico'];

    protected $fillable = [
        'fk_peca_codigo',
        'fk_servico_id_servico',
    ];
}
