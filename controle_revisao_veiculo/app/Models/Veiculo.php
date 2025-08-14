<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    protected $table = 'veiculos';
    protected $primaryKey = 'placa';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['placa','marca','modelo','ano','fk_cliente_id_cliente'];

    public function getRouteKeyName() { return 'placa'; }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'fk_cliente_id_cliente', 'id_cliente');
    }
}

