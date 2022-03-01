<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CidadeDelivery;
class CidadeDeliveryController extends Controller
{
	protected $empresa_id = null;
	public function __construct(){
		$this->middleware(function ($request, $next) {
			$this->empresa_id = $request->empresa_id;
			$value = session('user_logged');
			if(!$value){
				return redirect("/login");
			}

			if(!$value['super']){
				return redirect('/graficos');
			}
			return $next($request);
		});
	}

	public function index(){
		$cidades = CidadeDelivery::all();
		return view('cidadeDelivery/list')
		->with('cidades', $cidades)
		->with('title', 'Cidade Delivery');
	}

	public function new(){
		return view('cidadeDelivery/register')
		->with('title', 'Cadastrar Cidade');
	}

	public function save(Request $request){
		$cidade = new CidadeDelivery();
		$this->_validate($request);

		$result = $cidade->create($request->all());

		if($result){
			session()->flash("mensagem_sucesso", "Cidade cadastrada com sucesso.");
		}else{
			session()->flash('mensagem_erro', 'Erro ao cadastrar cidade.');
		}

		return redirect('/cidadeDelivery');
	}

	public function edit($id){
		$cidade = new CidadeDelivery(); 

		$resp = $cidade
		->where('id', $id)->first();  

		return view('cidadeDelivery/register')
		->with('cidade', $resp)
		->with('title', 'Editar Cidade');

	}

	public function update(Request $request){
		$cidade = new CidadeDelivery();

		$id = $request->input('id');
		$resp = $cidade
		->where('id', $id)->first(); 

		$this->_validate($request);


		$resp->nome = $request->input('nome');

		$result = $resp->save();
		if($result){
			session()->flash('mensagem_sucesso', 'Cidade editada com sucesso!');
		}else{
			session()->flash('mensagem_erro', 'Erro ao editar cidade!');
		}

		return redirect('/cidadeDelivery'); 
	}

	public function delete($id){

		$delete = CidadeDelivery
		::where('id', $id)
		->delete();
		if($delete){
			session()->flash('mensagem_sucesso', 'Registro removido!');
		}else{
			session()->flash('mensagem_erro', 'Erro!');
		}
		return redirect('/cidadeDelivery');

	}


	private function _validate(Request $request){
		$rules = [
			'nome' => 'required|max:50',
		];

		$messages = [
			'nome.required' => 'O campo nome é obrigatório.',
			'nome.max' => '50 caracteres maximos permitidos.'
		];
		$this->validate($request, $rules, $messages);
	}
}
