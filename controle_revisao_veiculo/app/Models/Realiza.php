<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Realiza extends Model
{
    protected $table = 'realizas';
    public $incrementing = false; // Chave primária composta, sem auto-incremento
    public $timestamps = false;   // Sem campos created_at/updated_at

    protected $primaryKey = ['fk_veiculo_placa', 'fk_revisao_id_revisao'];

    protected $fillable = [
        'fk_veiculo_placa',
        'fk_revisao_id_revisao'
    ];
}
