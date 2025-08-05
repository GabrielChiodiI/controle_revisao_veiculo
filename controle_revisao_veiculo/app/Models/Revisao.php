<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revisao extends Model
{
    protected $table = 'revisoes';
    protected $primaryKey = 'id_revisao';
    public $timestamps = false;

    protected $fillable = [
        'data_inicio',
        'data_fim',
        'quilometragem',
    ];
}
