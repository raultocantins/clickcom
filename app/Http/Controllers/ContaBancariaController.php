<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContaBancaria;
use App\Models\Cidade;

class ContaBancariaController extends Controller
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

	public function index(){
		$contas = ContaBancaria::
		where('empresa_id', $this->empresa_id)
		->get();

		return view('contaBancaria/list')
		->with('contas', $contas)
		->with('title', 'Contas Bancárias');
	}

	public function new(){
		$cidades = Cidade::all();
		return view('contaBancaria/register')
		->with('cidades', $cidades)
		->with('title', 'Conta Bancária');
	}

	public function save(Request $request){
		$this->_validate($request);
		try{
			$request->merge([ 'padrao' => $request->padrao ? true : false ]);

			$request->merge([ 'juros' => $request->juros ?? 0 ]);
			$request->merge([ 'multa' => $request->multa ?? 0 ]);
			$request->merge([ 'juros_apos' => $request->juros_apos ?? 0 ]);

			if($request->padrao){
				$contas = ContaBancaria::where('empresa_id', $this->empresa_id)->get();
				foreach($contas as $c){
					$c->padrao = 0;
					$c->save();
				}
			}

			ContaBancaria::create($request->all());

			// ContaBancaria::create([
			// 	'banco' => $request->banco,
			// 	'agencia' => $request->agencia,
			// 	'conta' => $request->conta,
			// 	'titular' => $request->titular,
			// 	'padrao' => $request->padrao,
			// 	'empresa_id' => $this->empresa_id
			// ]);
			session()->flash('mensagem_sucesso', 'Conta adicionada!');
		}catch(\Excpetion $e){
			session()->flash('mensagem_erro', 'Erro ao realizar cadastro: ' . $e->getMessage());
		}
		return redirect('/contaBancaria');
	}

	public function edit($id){

		$contas = ContaBancaria::
		where('empresa_id', $this->empresa_id)
		->get();

		$conta = ContaBancaria::find($id);
		if(valida_objeto($conta)){

			$cidades = Cidade::all();
			
			return view('contaBancaria/register')
			->with('conta', $conta)
			->with('cidades', $cidades)
			->with('contas', $contas)
			->with('title', 'Conta Bancária');
		}else{
			return redirect('/403');
		}
	}

	public function update(Request $request){
		$conta = ContaBancaria::find($request->id);

		if($request->padrao){
			$contas = ContaBancaria::where('empresa_id', $this->empresa_id)->get();
			foreach($contas as $c){
				$c->padrao = 0;
				$c->save();
			}
		}

		try{
			$conta->agencia = $request->agencia;
			$conta->conta = $request->conta;
			$conta->banco = $request->banco;
			$conta->titular = $request->titular;
			$conta->cnpj = $request->cnpj;
			$conta->endereco = $request->endereco;
			$conta->cidade_id = $request->cidade_id;
			$conta->padrao = $request->padrao ? true : false;

			$conta->carteira = $request->carteira;
			$conta->convenio = $request->convenio;
			$conta->juros = $request->juros;
			$conta->multa = $request->multa;
			$conta->juros_apos = $request->juros_apos;
			$conta->tipo = $request->tipo;

			$conta->save();
			session()->flash('mensagem_sucesso', 'Conta atualizada!');
		}catch(\Excpetion $e){
			session()->flash('mensagem_erro', 'Erro ao realizar update: ' . $e->getMessage());
		}
		return redirect('/contaBancaria');

	}

	public function delete($id){
		$conta = ContaBancaria::find($id);
		if(valida_objeto($conta)){
			if($conta->delete()){

				session()->flash('mensagem_sucesso', 'Registro removido!');
			}else{

				session()->flash('mensagem_erro', 'Erro!');
			}
			return redirect('/contaBancaria');
		}else{
			return redirect('/403');
		}
	}

	private function _validate(Request $request){

		$rules = [
			'banco' => 'required',
			'agencia' => 'required|min:3',
			'conta' => 'required|min:4',
			'titular' => 'required|min:6',
			'cnpj' => 'required|min:18',
			'endereco' => 'required|max:50',
			'cep' => 'required',
			'carteira' => 'required',
			'convenio' => 'required',
			'bairro' => 'required|max:30',
		];

		$messages = [
			'banco.required' => 'O campo banco é obrigatório.',
			'agencia.required' => 'O campo agência é obrigatório.',
			'conta.required' => 'O campo conta é obrigatório.',
			'titular.required' => 'O campo títular é obrigatório.',
			'cnpj.required' => 'O campo CNPJ é obrigatório.',
			'carteira.required' => 'O campo carteira é obrigatório.',
			'convenio.required' => 'O campo convênio é obrigatório.',
			'endereco.required' => 'O campo endereço é obrigatório.',
			'cep.required' => 'O campo CEP é obrigatório.',
			'bairro.required' => 'O campo bairro é obrigatório.',

			'agencia.min' => 'Informe no mínimo 3 caracteres.',
			'conta.min' => 'Informe no mínimo 4 caracteres.',
			'titular.min' => 'Informe nome e sobre nome.',
			'cnpj.min' => 'Informe o cnpj corretamente.',
			'endereco.max' => 'Informe no máximo 50 caracteres.',
			'bairro.max' => 'Informe no máximo 30 caracteres.',

		];

		$this->validate($request, $rules, $messages);
	}

	public function find($id){
		try{
			$conta = ContaBancaria::find($id);
			return response()->json($conta, 200);
		}catch(\Excpetion $e){
			return response()->json("Erro: " . $e->getMessage(), 401);
		}
	}
}
