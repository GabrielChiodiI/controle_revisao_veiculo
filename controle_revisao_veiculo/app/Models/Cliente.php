<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    public $timestamps = false;

    protected $fillable = ['nome','sobrenome','cpf','telefone','email','data_nascimento','sexo'];

    public function getRouteKeyName() { return 'id_cliente'; } // binding por id_cliente

    public function veiculos()
    {
        return $this->hasMany(Veiculo::class, 'fk_cliente_id_cliente', 'id_cliente');
    }
}
