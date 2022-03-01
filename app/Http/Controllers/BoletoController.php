<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContaReceber;
use App\Models\Boleto;
use App\Models\Empresa;
use App\Models\ContaBancaria;
use App\Helpers\BoletoHelper;

class BoletoController extends Controller
{

	protected $empresa_id = null;
	public function __construct(){
		$this->middleware(function ($request, $next) {
			$this->empresa_id = $request->empresa_id;
			$value = session('user_logged');
			if(!$value){
				return redirect("/login");
			}
			return $next($request);
		});
	}

	public function gerar($contaId){
		$contaReceber = ContaReceber::find($contaId);

		if($contaReceber->venda_id != null){
			$cliente = $contaReceber->venda->cliente;
		}else if($contaReceber->cliente_id != null){
			$cliente = $contaReceber->cliente;
		}else{
			session()->flash("mensagem_erro", "Conta sem nenhum cliente definido!");
			return redirect('/contasReceber');
		}

		$contasBancarias = ContaBancaria::
		where('empresa_id', $this->empresa_id)
		->get();

		if(sizeof($contasBancarias) == 0){
			session()->flash("mensagem_erro", "Cadastre uma conta bancária!");
			return redirect('/contaBancaria');
		}

		$contaPadrao = ContaBancaria::
		where('empresa_id', $this->empresa_id)
		->where('padrao', true)
		->first();

		return view('boletos/create')
		->with('cliente', $cliente)
		->with('contaReceber', $contaReceber)
		->with('contaPadrao', $contaPadrao)
		->with('contasBancarias', $contasBancarias)
		->with('title', 'Gerar boleto');
	}

	public function gerarStore(Request $request){
		$this->_validate($request);

		$data = [
			'banco_id' => $request->banco_id,
			'conta_id' => $request->conta_id,
			'numero' => $request->numero,
			'numero_documento' => $request->numero_documento,
			'carteira' => $request->carteira,
			'convenio' => $request->convenio,
			'linha_digitavel' => '',
			'nome_arquivo' => '',
			'juros' => $request->juros ?? 0,
			'multa' => $request->multa ?? 0,
			'juros_apos' => $request->juros_apos ?? 0,
			'instrucoes' => $request->instrucoes ?? "",
			'logo' => $request->logo ? true : false,
			'tipo' => $request->tipo,
			'codigo_cliente' => $request->codigo_cliente ?? '',
			'posto' => $request->posto ?? ''
		];

		$boleto = Boleto::create($data);

		$empresa = Empresa::find($this->empresa_id);
		$boletoHelper = new BoletoHelper($empresa);

		$result = $boletoHelper->gerar($boleto);


		if(!isset($result['erro'])){

			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';

			$link = "$public/boletos/$result";

			session()->flash('mensagem_sucesso', 'boleto gerado!');
			session()->flash('link', $link);

			return redirect('/contasReceber');

		}else{
			$boleto->delete();
			session()->flash("mensagem_erro", $result['mensagem']);
			return redirect()->back();
		}
	}

	public function gerarStoreMulti(Request $request){
		$data = $request->objeto;
		$banco = $data['banco'];
		$carteira = $data['carteira'];
		$convenio = $data['convenio'];
		$contas = $data['contas'];
		$usarLogo = $data['usar_logo'];
		$tipo = $data['tipo'];
		$codigoCliente = $data['codigo_cliente'] ?? '';
		$posto = $data['posto'] ?? '';

		$boletos = [];

		foreach($contas as $conta){
			$data = [
				'banco_id' => $banco,
				'conta_id' => $conta['id'],
				'numero' => $conta['numero_boleto'],
				'numero_documento' => $conta['numero_documento'],
				'carteira' => $carteira,
				'convenio' => $convenio,
				'linha_digitavel' => '',
				'nome_arquivo' => '',
				'juros' => $conta['juros'],
				'multa' => $conta['multa'],
				'juros_apos' => $conta['juros_apos'],
				'instrucoes' => "",
				'logo' => $usarLogo,
				'tipo' => $tipo,
				'codigo_cliente' => $codigoCliente,
				'posto' => $posto
			];

			array_push($boletos, $data);
		}

		$empresa = Empresa::find($this->empresa_id);
		$boletoHelper = new BoletoHelper($empresa);

		$result = $boletoHelper->simular($boletos);

		if(isset($result['erro'])){
			// session()->flash("mensagem_sucesso", "Boletos gerados!");
			return response()->json($result, 401);
		}else{
			$result = $this->gerarMultiStore($boletos);
			return response()->json($result, 200);
		}
	}

	private function gerarMultiStore($boletos){
		$empresa = Empresa::find($this->empresa_id);
		$boletoHelper = new BoletoHelper($empresa);
		foreach($boletos as $b){
			$boleto = Boleto::create($b);
			$result = $boletoHelper->gerar($boleto);
		}
		return $result;
	}

	private function _validate(Request $request){
		$contaBancaria = ContaBancaria::find($request->banco_id);

		$rules = [
			'banco_id' => 'required',
			'numero' => 'required',
			'numero_documento' => 'required',
			'carteira' => 'required',
			'convenio' => 'required|min:4|max:7',
			'posto' => ($contaBancaria != null && $contaBancaria->banco == 'Sicredi') ? 'required' : '',
			'codigo_cliente' => ($contaBancaria != null && $contaBancaria->banco == 'Sicredi' && $contaBancaria->banco == 'Caixa Econônica Federal') ? 'required' : '',
		];

		$messages = [
			'banco_id.required' => 'O campo banco é obrigatório.',
			'numero.required' => 'O campo número é obrigatório.',
			'numero_documento.required' => 'O campo número do documento é obrigatório.',
			'carteira.required' => 'O campo carteira é obrigatório.',
			'convenio.required' => 'O campo convênio é obrigatório.',

			'posto.required' => 'O campo posto é obrigatório.',
			'codigo_cliente.required' => 'O campo posto é obrigatório.',

			'convenio.min' => 'O código do convênio precisa ter 4, 6 ou 7 dígitos!',
			'convenio.max' => 'O código do convênio precisa ter 4, 6 ou 7 dígitos!',

		];

		$this->validate($request, $rules, $messages);
	}

	public function imprimir($id){
		$boleto = Boleto::find($id);
		if(valida_objeto($boleto->conta)){
			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
			$link = "$public/boletos/$boleto->nome_arquivo.pdf";
			$file = public_path('boletos')."/$boleto->nome_arquivo.pdf";
			if(file_exists($file)){
				return redirect($link);
			}else{
				session()->flash("mensagem_erro", "Arquivo não encontrado!!");
				return redirect('/contasReceber');
			}
		}else{
			return redirect('/403');
		}
	}

	public function gerarMultiplos($contas){
		$contas = explode(",", $contas);
		$temp = [];
		foreach($contas as $c){

			$conta = ContaReceber::find($c);
			if($conta->boleto){
				session()->flash("mensagem_erro", "Existe um boleto gerado, presente nessas contas informadas");
				return redirect('/contasReceber');
			}
			if(valida_objeto($conta)){
				array_push($temp, $conta);
			}
		}

		$this->validaContas($temp);

		$arrJson = [];

		foreach($temp as $key => $t){
			$a = [
				'cont' => $key+1,
				'id' => $t->id,
				'numero_documento' => '',
				'numero_boleto' => '',
				'juros' => 0,
				'multa' => 0,
				'juros_apos' => 0,
			];
			array_push($arrJson, $a);
		}

		$contaPadrao = ContaBancaria::
		where('empresa_id', $this->empresa_id)
		->where('padrao', true)
		->first();

		$contasBancarias = ContaBancaria::
		where('empresa_id', $this->empresa_id)
		->get();

		return view('boletos/create_multi')
		->with('contas', $temp)
		->with('contaPadrao', $contaPadrao)
		->with('contasBancarias', $contasBancarias)
		->with('arrJson', $arrJson)
		->with('title', 'Gerar boletos');

	}

	private function validaContas($contas){

		$contasBancarias = ContaBancaria::
		where('empresa_id', $this->empresa_id)
		->get();

		if(sizeof($contasBancarias) == 0){
			session()->flash("mensagem_erro", "Cadastre uma conta bancária!");
			return redirect('/contaBancaria');
		}

		foreach($contas as $c){
			if($c->venda_id != null){
				$cliente = $c->venda->cliente;
			}else if($c->cliente_id != null){
				$cliente = $c->cliente;
			}else{
				session()->flash("mensagem_erro", "Conta $c->id - R$ $c->valor_integral, sem nenhum cliente definido!");
				return redirect('/contasReceber');
			}
		}
	}

	public function gerarRemessa($id){
		$boleto = Boleto::find($id);
		if($boleto != null){
			$empresa = Empresa::find($this->empresa_id);
			$boletoHelper = new BoletoHelper($empresa);
			$boleto = $boletoHelper->gerarRemessa($boleto);
		}else{
			session()->flash("mensagem_erro", "Gere o boleto!");
				return redirect('/contasReceber');
		}
	}

}
