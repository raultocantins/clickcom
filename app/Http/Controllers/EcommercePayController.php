<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfigEcommerce;
use App\Models\PostBlogEcommerce;
use App\Models\PedidoEcommerce;
use App\Models\CategoriaProdutoEcommerce;
use App\Helpers\PedidoEcommerceHelper;

use Illuminate\Support\Str;

class EcommercePayController extends Controller
{

	public function paymentCartao(Request $request){

		$pedido = PedidoEcommerce::find($request->carrinho_id);
		$config = ConfigEcommerce::
		where('empresa_id', $pedido->empresa_id)
		->first();

		\MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

		$payment = new \MercadoPago\Payment();
		$payment->transaction_amount = $request->transactionAmount;
		$payment->description = $request->description;
		$payment->token = $request->token;
		$payment->installments = (int)$request->installments;
		$payment->payment_method_id = $request->paymentMethodId;

		$payer = new \MercadoPago\Payer();
		$payer->email = $request->email;
		$payer->identification = array(
			"type" => $request->docType,
			"number" => $request->docNumber
		);
		$payment->payer = $payer;
		$payment->save();

		if($payment->error){

			// $error = $this->trataErros($payment->error);
			// return response()->json($error, 401);

			session()->flash("mensagem_erro", $payment->error);
			return redirect()->back();

		}else{
			$pedido->transacao_id = $payment->id;
			$pedido->status_pagamento = $payment->status;
			$pedido->forma_pagamento = 'CARTÃO';
			$pedido->status_detalhe = $payment->status_detail;
			$pedido->hash = Str::random(20);
			$pedido->status = 1;
			$pedido->valor_total = $request->total_pag;
			
			$pedido->save();

			return redirect('/ecommercePay/finalizado/'.$pedido->hash);
		}
		print_r($request->all());

	}
	public function paymentBoleto(Request $request){

		$pedido = PedidoEcommerce::find($request->carrinho_id);
		$config = ConfigEcommerce::
		where('empresa_id', $pedido->empresa_id)
		->first();

		\MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

		$payment = new \MercadoPago\Payment();

		$payment->transaction_amount = (float)$request->transactionAmount;
		$payment->description = $request->description;
		$payment->payment_method_id = "bolbradesco";

		$cep = str_replace("-", "", $config->cep);
		$payment->payer = array(
			"email" => $request->payerEmail,
			"first_name" => $request->payerFirstName,
			"last_name" => $request->payerLastName,
			"identification" => array(
				"type" => $request->docType,
				"number" => $request->docNumber
			),
			"address"=>  array(
				"zip_code" => $cep,
				"street_name" => $config->rua,
				"street_number" => $config->numero,
				"neighborhood" => $config->bairro,
				"city" => $config->cidade,
				"federal_unit" => $config->uf
			)
		);

		// echo "<pre>";
		// print_r($payment);
		// echo "</pre>";

		// die;

		$payment->save();

		if($payment->transaction_details){

			$pedido->transacao_id = $payment->id;
			$pedido->status_pagamento = $payment->status;
			$pedido->forma_pagamento = 'Boleto';
			$pedido->valor_total = $request->total_pag;
			$pedido->status_detalhe = $payment->status_detail;
			$pedido->link_boleto = $payment->transaction_details->external_resource_url;
			$pedido->hash = Str::random(20);

			$pedido->status = 1; //criado;
			$pedido->save();

			return redirect('/ecommercePay/finalizado/'.$pedido->hash);
		}else{
			
			session()->flash("mensagem_erro", $payment->error);
			return redirect()->back();
		}

	}

	public function paymentPix(Request $request){

		$pedido = PedidoEcommerce::find($request->carrinho_id);
		$config = ConfigEcommerce::
		where('empresa_id', $pedido->empresa_id)
		->first();

		\MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

		$payment = new \MercadoPago\Payment();

		$payment->transaction_amount = (float)$request->transactionAmount;
		$payment->description = $request->description;
		$payment->payment_method_id = "pix";

		$cep = str_replace("-", "", $config->cep);
		$payment->payer = array(
			"email" => $request->payerEmail,
			"first_name" => $request->payerFirstName,
			"last_name" => $request->payerLastName,
			"identification" => array(
				"type" => $request->docType,
				"number" => $request->docNumber
			),
			"address"=>  array(
				"zip_code" => $cep,
				"street_name" => $config->rua,
				"street_number" => $config->numero,
				"neighborhood" => $config->bairro,
				"city" => $config->cidade,
				"federal_unit" => $config->uf
			)
		);

		$payment->save();

		if($payment->transaction_details){

			$pedido->transacao_id = $payment->id;
			$pedido->status_pagamento = $payment->status;
			$pedido->forma_pagamento = 'Pix';
			$pedido->status_detalhe = $payment->status_detail;
			$pedido->link_boleto = '';
			$pedido->valor_total = $request->total_pag;
			$pedido->hash = Str::random(20);

			$pedido->qr_code_base64 = $payment->point_of_interaction->transaction_data->qr_code_base64;
			$pedido->qr_code = $payment->point_of_interaction->transaction_data->qr_code;

			$pedido->status = 1; //criado;
			$pedido->save();

			return redirect('/ecommercePay/finalizado/'.$pedido->hash);
		}else{
			
			session()->flash("mensagem_erro", $payment->error);
			return redirect()->back();
		}

	}

	public function finalizado($hash){
		$pedido = PedidoEcommerce::
		where('hash', $hash)
		->first();

		$config = ConfigEcommerce::
		where('empresa_id', $pedido->empresa_id)
		->first();

		\MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

		if($pedido){
			$payStatus = \MercadoPago\Payment::find_by_id($pedido->transacao_id);

			if($payStatus->status != $pedido->status_pagamento){
				$pedido->status_pagamento = $payStatus->status;

				if($payStatus->status == "approved"){
					$pedido->status = 2; 
				}else{
					$pedido->status = 1; 
				}

				$pedido->save();
			}
		}

		$link = $config->link;

		$config = $this->getConfig($link);

		$default = $this->getDadosDefault($link);

		return view($default['template'].'/pedido_finalizado')
		->with('pedido', $pedido)
		->with('default', $default)
		->with('cart', true)
		->with('rota', $default['rota'])
		->with('title', 'Pedido finalizado');
	}

	private function getDadosDefault($link){

		$config = $this->getConfig($link);

		$categorias = CategoriaProdutoEcommerce::
		where('empresa_id', $config->empresa_id)
		->get();

		$produtoEcommerceHelper = new PedidoEcommerceHelper();
		$carrinho = $produtoEcommerceHelper->getCarrinho();
		$curtidas = $produtoEcommerceHelper->getProdutosCurtidos();

		$postBlogExists = PostBlogEcommerce::
		where('empresa_id', $config->empresa_id)
		->exists();
		$active = $this->getActive();
		return [
			'config' => $config,
			'template' => $config->tema_ecommerce,
			'categorias' => $categorias,
			'curtidas' => $curtidas,
			'carrinho' => $carrinho,
			'active' => $active,
			'postBlogExists' => $postBlogExists,
			'rota' => '/loja/' . strtolower($config->link)
		];
	}

	private function getConfig($link){
		$config = ConfigEcommerce::
		where('link', $link)
		->first();

		return $config;
	}

	public function consultaPagamento($transacao_id){

		$pedido = PedidoEcommerce::
		where('transacao_id', $transacao_id)
		->first();

		$config = ConfigEcommerce::
		where('empresa_id', $pedido->empresa_id)
		->first();

		\MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

		if($pedido){
			$payStatus = \MercadoPago\Payment::find_by_id($pedido->transacao_id);

			if($payStatus->status == "approved"){
				$pedido->status_pagamento = "approved";
				$pedido->status = 2; // confirmado o pagamento;
				$pedido->save();
			}
			return response()->json($payStatus->status, 200);

		}else{
			return response()->json("erro", 404);
		}
	}

	private function getActive(){
		$uri = $_SERVER['REQUEST_URI'];
		$uri = explode("/", $uri);

		$active = "";
		if(isset($uri[3])){
			if($uri[3] == 'categorias') $active = 'categorias';
			elseif($uri[3] == '1') $active = 'categorias';
			elseif($uri[3] == '2') $active = 'categorias';
			// elseif($uri[3] == 'carrinho') $active = 'categorias';
			elseif($uri[3] == 'contato') $active = 'contato';
			elseif($uri[3] == 'blog') $active = 'blog';

			// echo $uri[3];
		}else{
			$active = "home";
		}

		return $active;
	}
}
