<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ClienteEcommerce;
use App\Models\ConfigEcommerce;
use App\Models\EnderecoEcommerce;
use App\Models\ProdutoEcommerce;
use App\Models\Empresa;
use Illuminate\Support\Str;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class ClienteController extends Controller
{
	public function salvar(Request $request){
		try{
			$cliente = $request->cliente;

			$data = [
				'nome' => $cliente['nome'],
				'sobre_nome' => $cliente['sobre_nome'],
				'cpf' => $cliente['cpf'],
				'ie' => $cliente['ie'] ?? '',
				'email' => $cliente['email'],
				'telefone' => $cliente['telefone'],
				'senha' => md5($cliente['senha']),
				'status' => 1,
				'token' => Str::random(20),
				'empresa_id' => $request->empresa_id
			];
			$result = ClienteEcommerce::create($data);

			$dataEndereco = [
				'rua' => $cliente['rua'],
				'numero' => $cliente['numero'],
				'bairro' => $cliente['bairro'],
				'cep' => $cliente['cep'],
				'cidade' => $cliente['cidade'],
				'uf' => $cliente['uf'],
				'complemento' => $cliente['complemento'] ?? '',
				'cliente_id' => $result->id
			];

			$enderecoResult = EnderecoEcommerce::create($dataEndereco);

			$res = [
				'token' => $result->token,
				'nome' => $result->nome
			];
			return response()->json($res, 200);
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}
	}

	public function atualizar(Request $request){
		try{
			$cliente = $request->cliente;
			
			$cli = ClienteEcommerce::
			where('token', $cliente['token'])
			->first();

			$cli->nome = $cliente['nome'];
			$cli->sobre_nome = $cliente['sobre_nome'];
			$cli->email = $cliente['email'];
			$cli->telefone = $cliente['telefone'];

			$cli->save();
			return response()->json("ok", 200);
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}
	}

	public function cadastroDuplicado(Request $request){
		try{
			$data = $request->data;

			$msgArray = [];

			$emailDup = ClienteEcommerce::
			where('email', $data['email'])
			->where('empresa_id', $request->empresa_id)
			->first();

			if($emailDup != null){
				$msg = "Email já cadastrado";
				array_push($msgArray, $msg);
			}

			$cpfDup = ClienteEcommerce::
			where('cpf', $data['cpf'])
			->where('empresa_id', $request->empresa_id)
			->first();

			if($cpfDup != null){
				$msg = "Documento já cadastrado";
				array_push($msgArray, $msg);
			}

			return response()->json($msgArray, 200);
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}
	}

	private function montaPedido($carrinho){
		$temp = [];
		foreach($carrinho as $c){
			$produto = Product::find($c->id);
			$produto->quantidade = $c->quantidade;
			array_push($temp, $produto);
		}
		return $temp;
	}

	public function findWithCart(Request $request){
		$token = $request->token;
		$carrinho = json_decode($request->carrinho);

		$config = ConfigEcommerce::
		where('empresa_id', $request->empresa_id)
		->first();

		try{
			$cliente = ClienteEcommerce::
			where('token', $token)
			->first();

			$pedido = $this->montaPedido($carrinho);
			$total = 0;

			foreach($pedido as $i){
				$total += $i->quantidade * $i->valor_ecommerce;
			}

			foreach($cliente->enderecos as $e){

				$calc = $this->calculaFreteEnderecos($e, $pedido, $config);

				$e->preco_sedex = $calc['preco_sedex'];
				$e->prazo_sedex = $calc['prazo_sedex'];
				$e->preco = $calc['preco'];
				$e->prazo = $calc['prazo'];

				if($total > $config->frete_gratis_valor){
					$e->frete_gratis = 1;
				}

			}
			return response()->json($cliente, 200);

		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}
	}

	private function calculaFreteEnderecos($endereco, $carrinho, $config){

		$cepDestino = $endereco->cep;

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
			$soma += $i->quantidade * (float)$i->weight;
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

	public function findWithData(Request $request){
		$token = $request->token;

		$config = ConfigEcommerce::
		where('empresa_id', $request->empresa_id)
		->first();

		try{
			$cliente = ClienteEcommerce::
			where('token', $token)
			->first();

			$cliente->enderecos;
			$pTemp = $cliente->pedidos();
			foreach($pTemp as $p){
				foreach($p->itens as $i){
					$i->produto;
				}
			}
			$cliente->pedidos = $pTemp;
			return response()->json($cliente, 200);

		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}
	}

	public function alterarSenha(Request $request){
		$token = $request->token;
		$novaSenha = $request->novaSenha;
		try{
			$cliente = ClienteEcommerce::
			where('token', $token)
			->first();

			$cliente->senha = md5($novaSenha);
			$cliente->save();
			return response()->json("ok", 200);

		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}
	}

	public function login(Request $request){
		$email = $request->email;
		$senha = $request->senha;
		try{
			$cliente = ClienteEcommerce::
			where('email', $email)
			->where('senha', md5($senha))
			->first();

			if($cliente == null){
				return response()->json("email e/ou senha inválido(s)!", 404);
			}

			if($cliente->status == false){
				return response()->json("usuário inativo!", 404);
			}

			$res = [
				'token' => $cliente->token,
				'nome' => $cliente->nome
			];
			return response()->json($res, 200);

		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}
	}

	public function esqueciMinhaSenha(Request $request){
		$config = Empresa::find($request->empresa_id);

		$mail = new PHPMailer(true);

		$cliente = ClienteEcommerce::
		where('email', $request->email)
		->first();

		try{

			if($cliente != null){

				$senha = Str::random(4);

				$mail->SMTPDebug = 0;
				$mail->isSMTP(true);   
				$mail->Host       = $config->email_settings['mail_host'];
				$mail->SMTPAuth   = true;
				$mail->Username   = $config->email_settings['mail_username'];
				$mail->Password   = $config->email_settings['mail_password'];
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
				$mail->Port       = $config->email_settings['mail_port']; 
				$mail->SMTPSecure = $config->email_settings['mail_encryption'];

				$mail->setFrom($config->email_settings['mail_username'], 
					$config->email_settings['mail_from_name']);
				$mail->addAddress($request->email); 
				$mail->isHTML(true);
				$mail->CharSet = 'UTF-8';

				$mail->Subject = 'Redefinir senha';

				$mail->Body = view('mail.redefinir')
				->with('senha', $senha)
				->with('nome', "$cliente->nome $cliente->sobre_nome");

				$cliente->senha = md5($senha);
				$cliente->save();

				$mail->send();
				return response()->json("ok2", 200);

			}else{
				return response()->json("ok", 200);
			}


		} catch (Exception $e) {
			$msg = "Erro ao enviar email: {$mail->ErrorInfo}";
			return  response()->json($msg, 401);
		}
	}
}
