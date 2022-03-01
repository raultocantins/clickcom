<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ItemPedidoEcommerce;

class PedidoEcommerce extends Model
{
	protected $fillable = [
		'cliente_id', 'endereco_id', 'status', 'valor_total', 'valor_frete', 'tipo_frete', 
		'venda_id', 'numero_nfe', 'empresa_id', 'observacao', 'rand_pedido', 'link_boleto',
		'qr_code_base64', 'qr_code', 'transacao_id', 'forma_pagamento', 'status_pagamento', 
		'status_detalhe', 'status_preparacao', 'codigo_rastreio'
	];

	public function itens(){
		return $this->hasMany('App\Models\ItemPedidoEcommerce', 'pedido_id', 'id');
	}

	public function venda(){
		return $this->hasOne('App\Models\Venda', 'pedido_ecommerce_id', 'id');
	}

	public function cliente(){
		return $this->belongsTo(ClienteEcommerce::class, 'cliente_id');
	}

	public function endereco(){
		return $this->belongsTo(EnderecoEcommerce::class, 'endereco_id');
	}

	public function somaItens(){
		$soma = 0;
		foreach($this->itens as $i){
			$soma += $i->quantidade * $i->produto->valor;
		}
		return $soma;
	}

	public function somaItensPorCep($cep){
		$itensPedido = ItemPedidoEcommerce::
		select('item_pedido_ecommerces.*')
		->join('produto_ecommerces', 'produto_ecommerces.id', '=', 
			'item_pedido_ecommerces.produto_id')
		->where('item_pedido_ecommerces.pedido_id', $this->id)
		->where('produto_ecommerces.cep', $cep)
		->get();
		$soma = 0;
		foreach($itensPedido as $i){
			$soma += $i->quantidade * $i->produto->valor;
		}
		return $soma;
	}

	public function somaPeso(){
		$soma = 0;
		foreach($this->itens as $i){
			$soma += $i->quantidade * $i->produto->produto->peso_bruto;
		}
		return $soma;
	}

	public function somaPesoPorCep($cep){
		$itensPedido = ItemPedidoEcommerce::
		select('item_pedido_ecommerces.*')
		->join('produto_ecommerces', 'produto_ecommerces.id', '=', 
			'item_pedido_ecommerces.produto_id')
		->where('item_pedido_ecommerces.pedido_id', $this->id)
		->where('produto_ecommerces.cep', $cep)
		->get();
		$soma = 0;
		foreach($itensPedido as $i){
			$soma += $i->quantidade * $i->produto->produto->peso_bruto;
		}
		return $soma;
	}

	public function somaDimensoes(){
		$data = [
			'comprimento' => 0,
			'altura' => 0,
			'largura' => 0
		];
		foreach($this->itens as $key => $i){
			if($i->produto->produto->comprimento > $data['comprimento']){
				$data['comprimento'] = $i->produto->produto->comprimento;
			}

			// if($i->produto->produto->altura > $data['altura']){
			$data['altura'] += $i->produto->produto->altura;
			// }

			if($i->produto->produto->largura > $data['largura']){
				$data['largura'] = $i->produto->produto->largura;
			}

			$data['largura'] = $data['largura'];
		}
		return $data;
	}

	public function somaDimensoesPorCep($cep){
		$data = [
			'comprimento' => 0,
			'altura' => 0,
			'largura' => 0
		];

		$itensPedido = ItemPedidoEcommerce::
		select('item_pedido_ecommerces.*')
		->join('produto_ecommerces', 'produto_ecommerces.id', '=', 
			'item_pedido_ecommerces.produto_id')
		->where('item_pedido_ecommerces.pedido_id', $this->id)
		->where('produto_ecommerces.cep', $cep)
		->get();
		foreach($itensPedido as $key => $i){
			if($i->produto->produto->comprimento > $data['comprimento']){
				$data['comprimento'] = $i->produto->produto->comprimento;
			}

			// if($i->produto->produto->altura > $data['altura']){
			$data['altura'] += $i->produto->produto->altura;
			// }

			if($i->produto->produto->largura > $data['largura']){
				$data['largura'] = $i->produto->produto->largura;
			}

			$data['largura'] = $data['largura'];
		}
		return $data;
	}

	public function getCepsDoPedido($cepOrigem){
		$ceps = [];
		foreach($this->itens as $key => $i){
			if(!in_array($i->produto->cep, $ceps)){
				if($i->produto->cep != ""){
					array_push($ceps, $i->produto->cep);
				}
			}
		}

		return $ceps;
	}

}
