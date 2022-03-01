<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\UsuarioAcesso;

class Usuario extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome', 'senha', 'login', 'adm', 'ativo', 'img', 'empresa_id', 'permissao', 'email',
        'somente_fiscal'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'senha'
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function funcionario(){
        return $this->hasOne('App\Models\Funcionario', 'usuario_id', 'id');
    }

    public function config(){
        return $this->hasOne('App\Models\ConfigCaixa', 'usuario_id', 'id');
    }

    public function acessos(){
        return $this->hasMany('App\Models\UsuarioAcesso', 'usuario_id', 'id');
    }

    public function ultimoAcesso(){
        $acesso = UsuarioAcesso::
        where('usuario_id', $this->id)
        ->where('status', 1)
        ->orderBy('id', 'desc')
        ->first();

        return $acesso;
    }

    public function acesso(){
        $acesso = UsuarioAcesso::
        where('usuario_id', $this->id)
        ->where('status', 0)
        ->orderBy('id', 'desc')
        ->first();

        return $acesso;
    }
}
