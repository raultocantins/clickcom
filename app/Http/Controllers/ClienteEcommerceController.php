<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClienteEcommerce;
use Illuminate\Support\Str;

class ClienteEcommerceController extends Controller
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
		$clientes = ClienteEcommerce::
		where('empresa_id', $this->empresa_id)
		->get();

		return view('clienteEcommerce/list')
		->with('clientes', $clientes)
		->with('title', 'Clientes');
	}

	public function new(){
		return view('clienteEcommerce/register')
		->with('title', 'Cadastrar Cliente');
	}

	public function edit($id){

		$cliente = ClienteEcommerce::find($id);
		if(valida_objeto($cliente)){

			return view('clienteEcommerce/register')
			->with('cliente', $cliente)
			->with('title', 'Editar Cliente');
		}else{
			return redirect('/403');
		}
	}

	public function save(Request $request){

		$this->_validate($request);

		$request->merge(['senha' => md5($request->senha)]);
		$request->merge(['status' => 1]);
		$request->merge(['token' => Str::random(20)]);

		$result = ClienteEcommerce::create($request->all());
		if($result){
			session()->flash("mensagem_sucesso", "Cliente salvo!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao criar cliente!');
		}

		return redirect('/clienteEcommerce');
	}

	public function update(Request $request){

		$this->_validate($request, true);

		$cliente = ClienteEcommerce::find($request->id);

		$cliente->nome = $request->nome;
		$cliente->sobre_nome = $request->sobre_nome;
		$cliente->telefone = $request->telefone;
		$cliente->email = $request->email;
		$cliente->cpf = $request->cpf;
		if($request->senha){
			$cliente->senha = md5($request->senha);
		}

		$result = $cliente->save();
		if($result){
			session()->flash("mensagem_sucesso", "Cliente atualizado!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao atualizar cliente!');
		}

		return redirect('/clienteEcommerce');
	}

	private function _validate(Request $request, $update = false){
		$rules = [
			'nome' => 'required',
			'sobre_nome' => 'required',
			'email' => 'required',
			'telefone' => 'required',
			'cpf' => 'required',
			'senha' => !$update ? 'required' : '',
		];

		$messages = [
			'nome.required' => 'O campo nome é obrigatório.',
			'sobre_nome.required' => 'O campo sobre nome é obrigatório.',
			'senha.required' => 'O campo senha é obrigatório',
			'email.required' => 'O campo email é obrigatório',
			'telefone.required' => 'O campo telefone é obrigatório',
			'cpf.required' => 'O campo documento é obrigatório',

		];

		$this->validate($request, $rules, $messages);
	}
}
