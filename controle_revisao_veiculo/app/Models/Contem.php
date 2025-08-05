<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contem extends Model
{
    protected $table = 'contems';
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ['fk_servico_id_servico', 'fk_revisao_id_revisao'];

    protected $fillable = [
        'fk_servico_id_servico',
        'fk_revisao_id_revisao',
    ];
}

