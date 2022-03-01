<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanoEmpresa extends Model
{
    protected $fillable = [
        'empresa_id', 'plano_id', 'expiracao'
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function plano(){
        return $this->belongsTo(Plano::class, 'plano_id');
    }

    public function payment(){
		return $this->hasOne('App\Models\Payment', 'plano_id', 'id');
	}
}
