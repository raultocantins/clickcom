<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoDescarga extends Model
{
	protected $fillable = [
		'mdfe_id', 'tp_unid_transp', 'id_unid_transp', 'quantidade_rateio', 'cidade_id'
	];

	public function cidade(){
		return $this->belongsTo(Cidade::class, 'cidade_id');
	}

	public function cte(){
		return $this->hasOne('App\Models\CTeDescarga', 'info_id', 'id');
	}

	public function nfe(){
		return $this->hasOne('App\Models\NFeDescarga', 'info_id', 'id');
	}

	public function lacresTransp(){
		return $this->hasMany('App\Models\LacreTransporte', 'info_id', 'id');
	}

	public function unidadeCarga(){
		return $this->hasOne('App\Models\UnidadeCarga', 'info_id', 'id');
	}

	public function lacresUnidCarga(){
		return $this->hasMany('App\Models\LacreUnidadeCarga', 'info_id', 'id');
	}

}
