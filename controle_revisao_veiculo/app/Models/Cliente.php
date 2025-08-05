<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'sobrenome',
        'cpf',
        'telefone',
        'email',
        'data_nascimento',
        'sexo',
    ];

    // Relacionamento: Cliente tem muitos Veiculos
    public function veiculos()
    {
        return $this->hasMany(Veiculo::class, 'fk_cliente_id_cliente', 'id_cliente');
    }
}
