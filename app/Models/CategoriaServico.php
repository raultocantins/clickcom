<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaServico extends Model
{
    protected $fillable = [
        'nome', 'empresa_id'
    ];
}
