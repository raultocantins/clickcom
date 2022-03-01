<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DivisaoGrade;

class DivisaoGradeController extends Controller
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

	public function index(Request $request){

		$divisoes = DivisaoGrade::
		where('empresa_id', $request->empresa_id)
		->orderBy('sub_divisao', 'asc')
		->get();

		return view('divisaoGrade/list')
		->with('divisoes', $divisoes)
		->with('title', 'Grade');
	}

	public function new(){
		return view('divisaoGrade/register')
		->with('title', 'Cadastrar Divisão de Grade');
	}

	public function save(Request $request){

		$this->_validate($request);

		$request->merge(['sub_divisao' => $request->sub_divisao ? true : false]);
		$result = DivisaoGrade::create($request->all());

		if($result){
			session()->flash("mensagem_sucesso", "Divisão grade cadastrada!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao cadastrar.');
		}

		return redirect('/divisaoGrade');
	}

	public function edit($id){
		$divisao = new DivisaoGrade(); 

		$divisaoGrade = $divisao
		->where('id', $id)->first();  

		if(valida_objeto($divisaoGrade)){
			return view('divisaoGrade/register')
			->with('divisaoGrade', $divisaoGrade)
			->with('title', 'Editar Divisão de Grade');
		}else{
			return redirect('/403');
		}

	}

	public function update(Request $request){
		$divisao = new DivisaoGrade();

		$id = $request->input('id');
		$resp = $divisao
		->where('id', $id)->first(); 

		$this->_validate($request);

		$resp->nome = $request->input('nome');
		$resp->sub_divisao = $request->sub_divisao ? true : false;

		$result = $resp->save();
		if($result){
			session()->flash('mensagem_sucesso', 'Divisão editada com sucesso!');
		}else{
			session()->flash('mensagem_erro', 'Erro ao editar!');
		}

		return redirect('/divisaoGrade'); 
	}

	public function delete($id){
		try{
			$divisao = DivisaoGrade
			::where('id', $id)
			->first();
			if(valida_objeto($divisao)){
				if($divisao->delete()){
					session()->flash('mensagem_sucesso', 'Registro removido!');
				}else{

					session()->flash('mensagem_erro', 'Erro!');
				}
				return redirect('/divisaoGrade');
			}else{
				return redirect('403');
			}
		}catch(\Exception $e){
			return view('errors.sql')
			->with('title', 'Erro ao deletar divisão')
			->with('motivo', $e->getMessage());
		}
	}


	private function _validate(Request $request){
		$rules = [
			'nome' => 'required|max:30'
		];

		$messages = [
			'nome.required' => 'O campo nome é obrigatório.',
			'nome.max' => '50 caracteres maximos permitidos.'
		];
		$this->validate($request, $rules, $messages);
	}

}

