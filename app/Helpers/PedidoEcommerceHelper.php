<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use App\Models\PedidoEcommerce;
use App\Models\ItemPedidoEcommerce;
use App\Models\CurtidaProdutoEcommerce;

class PedidoEcommerceHelper {

	/*
	$empresa_id
	$produto_id
	$quantidade
	*/
	public function addProduto($data){
		$typeUser = $this->getUser();

		if($typeUser == 'temp'){
			$pedido = $this->setPedidoRand($data);
		}else{
			$pedido = $this->setPedido($data);
		}
	}

	private function setPedido($data){
		$user = $this->getUserLogado();

		$pedido = PedidoEcommerce::
		where('cliente_id', $user['cliente_id'])
		->where('valor_total', '0')
		->first();

		if($pedido == null){
			$pedido = $this->criaPedido($data, $user['cliente_id']);
			$this->addItem($data, $pedido);
		}else{
			$this->addItem($data, $pedido);
		}

		return $pedido;
	}

	private function setPedidoRand($data){
		$userTemp = $this->getUserTemp();

		$pedido = PedidoEcommerce::
		where('rand_pedido', $userTemp['rand'])
		->first();

		if($pedido == null){
			$pedido = $this->criaPedidoRand($data, $userTemp['rand']);
			$this->addItem($data, $pedido);
		}else{
			$this->addItem($data, $pedido);
		}

		return $pedido;
	}

	private function criaPedido($data, $cliente_id){
		$pedido = [
			'cliente_id' => $cliente_id,
			'endereco_id' => null,
			'status' => 0,
			'valor_total' => 0,
			'valor_frete' => 0,
			'tipo_frete' => '',
			'venda_id' => 0,
			'numero_nfe' => 0,
			'empresa_id' => $data['empresa_id'],
			'observacao' => '',
			'rand_pedido' => ''
		];
		return PedidoEcommerce::create($pedido);
	}

	private function criaPedidoRand($data, $rand){
		$pedido = [
			'cliente_id' => null,
			'endereco_id' => null,
			'status' => 0,
			'valor_total' => 0,
			'valor_frete' => 0,
			'tipo_frete' => '',
			'venda_id' => 0,
			'numero_nfe' => 0,
			'empresa_id' => $data['empresa_id'],
			'observacao' => '',
			'rand_pedido' => $rand
		];
		return PedidoEcommerce::create($pedido);
	}

	private function addItem($data, $pedido){
		$item = [
			'pedido_id' => $pedido->id,
			'produto_id' => $data['produto_id'],
			'quantidade' => $data['quantidade']
		];
		ItemPedidoEcommerce::create($item);
	}

	public function getUser(){

		$user = $this->getUserLogado();

		if($user == null){
			$userTemp = $this->getUserTemp();
			if($userTemp == null){

				$randUser = Str::random(20);
				$ob = [
					'rand' => $randUser,
					'start' => date('H:i:s')
				];
				session(['user_temp' => $ob]);
				return 'temp';
			}else{
				return 'temp';
			}
		}else{
			//usuario logado
			return 'logado';
		}
	}

	public function getUserLogado(){
		$usr = session('user_ecommerce');
		return $usr;
	}

	public function getUserTemp(){
		$usr = session('user_temp');
		return $usr;
	}

	public function getProdutosCurtidos(){
		$user = $this->getUserLogado();
		if($user == null) return 0;

		$produtos = CurtidaProdutoEcommerce::
		where('cliente_id', $user['cliente_id'])
		->get();

		return sizeof($produtos);
	}

	public function setUserEcommerce($clienteId){
		$ob = [
			'cliente_id' => $clienteId,
			'start' => date('H:i:s')
		];
		session()->forget('user_temp');
		session(['user_ecommerce' => $ob]);
	}

	public function logoff(){
		session()->forget('user_ecommerce');
	}

	public function getCarrinho(){
		$user = $this->getUserLogado();
		$userTemp = $this->getUserTemp();

		$pedido = null;
		if($userTemp != null){
			$pedido = PedidoEcommerce::
			where('rand_pedido', $userTemp['rand'])
			->where('status', 0)
			->first();
		}else if($user != null){

			$pedido = PedidoEcommerce::
			where('cliente_id', $user['cliente_id'])
			->where('status', 0)
			->first();
		}

		return $pedido;
	}

}