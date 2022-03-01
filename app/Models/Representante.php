<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Representante extends Model
{
    use HasFactory;

    protected $fillable = [
		'nome', 'rua', 'telefone', 'email', 'numero', 'bairro', 'cidade', 'cpf_cnpj', 
		'usuario_id', 'status', 'comissao', 'acesso_xml', 'limite_cadastros'
	];

	public function usuario(){
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function empresas(){
		return $this->hasMany('App\Models\RepresentanteEmpresa', 'representante_id', 'id');
    }

}
