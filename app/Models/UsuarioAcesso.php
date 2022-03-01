<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioAcesso extends Model
{
    protected $fillable = [
        'usuario_id', 'status', 'hash', 'ip_address'
    ];

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
