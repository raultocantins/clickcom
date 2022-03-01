<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PedidoEcommerce;
class ClienteEcommerce extends Model
{
    protected $fillable = [
		'nome', 'sobre_nome', 'cpf', 'email', 'senha', 'status', 'empresa_id', 'telefone', 
		'ie', 'token'
	];

	public function enderecos(){
		return $this->hasMany('App\Models\EnderecoEcommerce', 'cliente_id', 'id');
	}

	public function pedidos(){
		return PedidoEcommerce::
		where('cliente_id', $this->id)
		->where('valor_total', '>', 0)
		->get();
	}
}
