<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClienteEcommerce;
use App\Models\EnderecoEcommerce;
use App\Models\Cidade;

class EnderecoEcommerceController extends Controller
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

	public function index($id){
		$cliente = ClienteEcommerce::find($id);

		return view('enderecosEcommerce/list')
		->with('cliente', $cliente)
		->with('title', 'Endereços');
	}

	public function edit($id){
		$endereco = EnderecoEcommerce::find($id);

		if(valida_objeto($endereco->cliente)){

			$cidades = Cidade::all();
			return view('enderecosEcommerce/register')
			->with('endereco', $endereco)
			->with('cidades', $cidades)
			->with('title', 'Editar Endereço');
		}else{
			return redirect('/403');
		}
	}

	public function update(Request $request){
		
		$this->_validate($request);

		$endereco = EnderecoEcommerce::find($request->id);

		$endereco->rua = $request->rua;
		$endereco->numero = $request->numero;
		$endereco->bairro = $request->bairro;
		$endereco->cep = $request->cep;
		$endereco->cidade = $request->cidade;

		$result = $endereco->save();
		if($result){
			session()->flash("mensagem_sucesso", "Endereço atualizado!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao atualizar endereço!');
		}

		return redirect('/enderecosEcommerce/'.$endereco->cliente_id);
	}

	private function _validate(Request $request){
		$rules = [
			'rua' => 'required',
			'numero' => 'required',
			'bairro' => 'required',
			'cep' => 'required',
			'cidade' => 'required',
		];

		$messages = [
			'rua.required' => 'O campo rua é obrigatório.',
			'numero.required' => 'O campo número é obrigatório.',
			'bairro.required' => 'O campo bairro é obrigatório',
			'cep.required' => 'O campo CEP é obrigatório',
			'cidade.required' => 'O campo cidade é obrigatório'

		];

		$this->validate($request, $rules, $messages);
	}
}
