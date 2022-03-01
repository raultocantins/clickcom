<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProdutoEcommerce;
use App\Models\PedidoEcommerce;
use App\Models\ConfigEcommerce;
use App\Models\Variation;
use App\Models\ItemPedidoEcommerce;
use Illuminate\Support\Str;

class CarrinhoController extends Controller
{
	public function itens(Request $request){
		$cart = json_decode($request->ids);

		$produtos = [];
		foreach($cart as $i){
			// if(isset($i->variacao_id)){
			// 	$variacao = Variation::find($i->variacao_id);
			// }
			$produto = ProdutoEcommerce::find($i->id);
			$produto->produto;
			// foreach($produto->variations as $v){
			// 	$v->media;
			// }
			array_push($produtos, $produto);
		}
		return response()->json($produtos, 200);
	}

	public function salvarPedido(Request $request){
		try{
			$data = $request->data;

			$cliente = $data['cliente'];
			$carrinho = $data['carrinho'];
			$endereco = $data['endereco'];
			$total = $data['total'];
			$valorFrete = $data['valor_frete'];
			$tpFrete = $data['tipo_frete'];

			$pedidoData = [
				'cliente_id' => $cliente['id'],
				'endereco_id' => $endereco['id'],
				'status' => 0,
				'valor_total' => $total + $valorFrete,
				'valor_frete' => $valorFrete,
				'tipo_frete' => $tpFrete,
				'venda_id' => 0,
				'numero_nfe' => 0,
				'empresa_id' => $request->empresa_id,
				'observacao' => '',
				'rand_pedido' => '',
				'link_boleto' => '',
				'qr_code_base64' => '',
				'qr_code' => '',
				'transacao_id' => '',
				'forma_pagamento' => '',
				'status_pagamento' => '', 
				'status_detalhe' => '',
				'status_preparacao' => '',
				'codigo_rastreio' => '',
				'token' => Str::random(20)
			];

			$rsPedido = PedidoEcommerce::create($pedidoData);

			foreach($carrinho as $i){
				$itemData = [
					'pedido_id' => $rsPedido->id,
					'produto_id' => $i['id'],
					'quantidade' => $i['quantidade'],
					'variacao_id' => $i['variacao_id']
				];

				ItemPedidoEcommerce::create($itemData);
			}

			return response()->json($rsPedido->token, 200);
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}

	}

	public function getPedido(Request $request){
		try{
			$pedido = PedidoEcommerce::
			where('token', $request->token)
			->first();
			$pedido->endereco;
			$pedido->cliente;
			$descricao = "";
			foreach($pedido->itens as $i){
				$i->produto;
				if($i->produto->type == 'variable'){
					$variacao = Variation::find($i->variacao_id);
					$variacao->media;
					$i->produto->variacao = $variacao;
				}

				$descricao .= $i->quantidade . " x " . $i->produto->name . " "; 
			}
			$pedido->descricao = $descricao;

			$config = ConfigEcommerce::
			where('empresa_id', $request->empresa_id)
			->first();

			$pedido->mensagem_agradecimento = $config->mensagem_agradecimento;

			return response()->json($pedido, 200);
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}
	}

	public function processarPagamentoCartao(Request $request){
		$data = $request->data;

		$config = ConfigEcommerce::
		where('empresa_id', $request->empresa_id)
		->first();

		$pedido = PedidoEcommerce::find($data['pedido_id']);

		\MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);
		$payment = new \MercadoPago\Payment();

		$payment->transaction_amount = $pedido->valor_total;
		$payment->description = $data['description'];
		$payment->token = $data['id'];
		$payment->installments = (int)$data['installments'];
		$payment->payment_method_id = $data['paymentMethodId'];

		$payer = new \MercadoPago\Payer();
		$payer->email = $data['email'];
		$payer->identification = array(
			"type" => $data['docType'] ?? 'CPF',
			"number" => $data['docNumber']
		);

		$payment->payer = $payer;

		$payment->save();

		if($payment->error){

			$error = $this->trataErros($payment->error);
			return response()->json($error, 401);

		}else{
			$pedido->transacao_id = $payment->id;
			$pedido->status_pagamento = $payment->status;
			$pedido->forma_pagamento = 'Cartão';
			$pedido->status_detalhe = $payment->status_detail;

			$pedido->status = 1;
			$pedido->save();

			$dataSuccess = [
				'id' => $payment->id,
				'status' => $payment->status
			];
			return response()->json($dataSuccess, 200);
		}

	}

	public function processarPagamentoPix(Request $request){
		$data = $request->data;

		$config = ConfigEcommerce::
		where('empresa_id', $request->empresa_id)
		->first();

		$pedido = PedidoEcommerce::find($data['pedido_id']);

		\MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);
		$payment = new \MercadoPago\Payment();

		$payment->transaction_amount = $pedido->valor_total;
		$payment->description = $data['description'];
		$payment->payment_method_id = "pix";

		$cep = str_replace("-", "", $config->cep);
		$payment->payer = array(
			"email" => $data['payerEmail'],
			"first_name" => $data['payerFirstName'],
			"last_name" => $data['payerLastName'],
			"identification" => array(
				"type" => $data['docType'] ?? 'CPF',
				"number" => $data['docNumber']
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

		if($payment->error){

			$error = $this->trataErros($payment->error);
			return response()->json($error, 401);

		}else{
			$pedido->transacao_id = $payment->id;
			$pedido->status_pagamento = $payment->status;
			$pedido->forma_pagamento = 'PIX';
			$pedido->status_detalhe = $payment->status_detail;
			$pedido->link_boleto = '';

			$pedido->qr_code_base64 = $payment->point_of_interaction->transaction_data->qr_code_base64;
			$pedido->qr_code = $payment->point_of_interaction->transaction_data->qr_code;


			$pedido->status = 1; //criado;
			$pedido->save();
			$dataSuccess = [
				'id' => $payment->id,
				'status' => $payment->status,
				'qr_code_base64' => $pedido->qr_code_base64,
				'qr_code' => $pedido->qr_code,
			];

			return response()->json($dataSuccess, 200);
		}
	}

	public function processarPagamentoBoleto(Request $request){
		$data = $request->data;

		$config = ConfigEcommerce::
		where('empresa_id', $request->empresa_id)
		->first();

		$pedido = PedidoEcommerce::find($data['pedido_id']);

		\MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);
		$payment = new \MercadoPago\Payment();

		$payment->transaction_amount = $pedido->valor_total;
		$payment->description = $data['description'];
		$payment->payment_method_id = "bolbradesco";

		$cep = str_replace("-", "", $config->cep);
		$payment->payer = array(
			"email" => $data['payerEmail'],
			"first_name" => $data['payerFirstName'],
			"last_name" => $data['payerLastName'],
			"identification" => array(
				"type" => $data['docType'] ?? 'CPF',
				"number" => $data['docNumber']
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

		if($payment->error){

			$error = $this->trataErros($payment->error);
			return response()->json($error, 401);

		}else{
			$pedido->transacao_id = $payment->id;
			$pedido->status_pagamento = $payment->status;
			$pedido->forma_pagamento = 'Boleto';
			$pedido->status_detalhe = $payment->status_detail;
			$pedido->link_boleto = $payment->transaction_details->external_resource_url;

			$pedido->status = 1; //criado;
			$pedido->save();
			$dataSuccess = [
				'id' => $payment->id,
				'status' => $payment->status
			];
			return response()->json($dataSuccess, 200);
		}
	}

	private function trataErros($error){

		foreach($error->causes as $e){
			if($e->code == 4033){
				return "Parcelas inválidas";
			}
		}
		// return $error;
		return "Erro desconhecido!";
	}

	public function getStatusPix(Request $request){
		$pedido = PedidoEcommerce::
		where('token', $request->token)
		->first();

		$config = ConfigEcommerce::
		where('empresa_id', $request->empresa_id)
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

	public function calcularFrete(Request $request){
		$cep = $request->cep;
		$carrinho = json_decode($request->cart);

		$config = ConfigEcommerce::
		where('empresa_id', $request->empresa_id)
		->first();

		$pedido = $this->montaPedido($carrinho);
		$total = 0;

		foreach($pedido as $i){
			$total += $i->quantidade * $i->valor_ecommerce;
		}

		$calc = $this->calculaFretePorCep($cep, $pedido, $config);

		// $e->preco_sedex = $calc['preco_sedex'];
		// $e->prazo_sedex = $calc['prazo_sedex'];
		// $e->preco = $calc['preco'];
		// $e->prazo = $calc['prazo'];

		if($total > $config->frete_gratis_valor){
			$calc['frete_gratis'] = 1;
		}

		return response()->json($calc, 200);
	}

	private function montaPedido($carrinho){
		$temp = [];
		foreach($carrinho as $c){
			$produto = ProdutoEcommerce::find($c->id);
			$produto->quantidade = $c->quantidade;
			array_push($temp, $produto);
		}
		return $temp;
	}

	private function calculaFretePorCep($cep, $carrinho, $config){

		$cepDestino = $cep;

		$cepOrigem = str_replace("-", "", $config->cep);

		$somaPeso = $this->somaPeso($carrinho);
		$dimensoes = $this->somaDimensoes($carrinho);


		$stringUrl = "&sCepOrigem=$cepOrigem&sCepDestino=$cepDestino&nVlPeso=$somaPeso";

		$stringUrl .= "&nVlComprimento=".$dimensoes['comprimento']."&nVlAltura=".$dimensoes['altura']."&nVlLargura=".$dimensoes['largura']."&nCdServico=04014";


		$url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCdAvisoRecebimento=n&sCdMaoPropria=n&nVlValorDeclarado=0&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3&nCdFormato=1" . $stringUrl;

		$unparsedResult = file_get_contents($url);
		$parsedResult = simplexml_load_string($unparsedResult);

		$stringUrl = "&sCepOrigem=$cepOrigem&sCepDestino=$cepDestino&nVlPeso=$somaPeso";

		$stringUrl .= "&nVlComprimento=".$dimensoes['comprimento']."&nVlAltura=".$dimensoes['altura']."&nVlLargura=".$dimensoes['largura']."&nCdServico=04510";

		$url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCdAvisoRecebimento=n&sCdMaoPropria=n&nVlValorDeclarado=0&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3&nCdFormato=1" . $stringUrl;

		$unparsedResultSedex = file_get_contents($url);
		$parsedResultSedex = simplexml_load_string($unparsedResultSedex);

		$retorno = array(
			'preco_sedex' => strval($parsedResult->cServico->Valor),
			'prazo_sedex' => strval($parsedResult->cServico->PrazoEntrega),

			'preco' => strval($parsedResultSedex->cServico->Valor),
			'prazo' => strval($parsedResultSedex->cServico->PrazoEntrega)
		);

		return $retorno;
	}

	public function somaPeso($carrinho){
		$soma = 0;
		foreach($carrinho as $i){
			$soma += $i->quantidade * $i->weight;
		}
		return $soma;
	}

	public function somaDimensoes($carrinho){
		$data = [
			'comprimento' => 0,
			'altura' => 0,
			'largura' => 0
		];
		foreach($carrinho as $key => $i){
			if($i->comprimento > $data['comprimento']){
				$data['comprimento'] = $i->comprimento;
			}

			// if($i->produto->produto->altura > $data['altura']){
			$data['altura'] += $i->altura;
			// }

			if($i->largura > $data['largura']){
				$data['largura'] = $i->largura;
			}

			$data['largura'] = $data['largura'];
		}
		return $data;
	}

}
