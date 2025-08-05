<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peca extends Model
{
    protected $table = 'pecas';
    protected $primaryKey = 'codigo';
    public $timestamps = false;

    protected $fillable = [
        'descricao',
        'quantidade',
        'preco',
    ];
}