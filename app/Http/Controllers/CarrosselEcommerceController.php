<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarrosselEcommerce;
use Illuminate\Support\Str;

class CarrosselEcommerceController extends Controller
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
		$carrossels = CarrosselEcommerce::
		where('empresa_id', $this->empresa_id)
		->get();

		return view('carrosselEcommerce/list')
		->with('carrossels', $carrossels)
		->with('title', 'Carrossel Ecommerce');
	}

	public function new(){
		return view('carrosselEcommerce/register')
		->with('title', 'Cadastrar Carrossel');
	}

	public function save(Request $request){

		$category = new CarrosselEcommerce();
		$this->_validate($request);

		$nomeImagem = "";
		if($request->hasFile('file')){
    		//unlink anterior
			$file = $request->file('file');
			$nomeImagem = Str::random(20).".png";
			$upload = $file->move(public_path('ecommerce/carrossel'), $nomeImagem);
		}
		$request->merge(['img' => $nomeImagem]);
		$request->merge(['link_acao' => $request->link_acao ?? '']);
		$request->merge(['nome_botao' => $request->nome_botao ?? '']);
		$result = $category->create($request->all());

		if($result){
			session()->flash("mensagem_sucesso", "Cadastrado com sucesso!!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao cadastrar categoria.');
		}

		return redirect('/carrosselEcommerce');
	}

	public function edit($id){
		$carrossel = new CarrosselEcommerce(); 

		$resp = $carrossel
		->where('id', $id)->first();  

		if(valida_objeto($resp)){
			return view('carrosselEcommerce/register')
			->with('carrossel', $resp)
			->with('title', 'Editar Carrossel');
		}else{
			return redirect('/403');
		}

	}

	public function update(Request $request){
		$carrossel = new CarrosselEcommerce();

		$id = $request->input('id');
		$resp = $carrossel
		->where('id', $id)->first(); 

		$this->_validate($request, true);

		$nomeImagem = $resp->img;
		if($request->hasFile('file')){
    		//unlink anterior

			if($resp->img != "" && file_exists(public_path('ecommerce/carrossel'). "/".$resp->img)){
				unlink(public_path('ecommerce/carrossel'). "/".$resp->img);
			}
			$file = $request->file('file');
			$nomeImagem = Str::random(20).".png";
			$upload = $file->move(public_path('ecommerce/carrossel'), $nomeImagem);
		}

		$resp->titulo = $request->input('titulo');
		$resp->link_acao = $request->input('link_acao');
		$resp->descricao = $request->input('descricao') ?? '';
		$resp->nome_botao = $request->input('nome_botao') ?? '';
		$resp->cor_titulo = $request->input('cor_titulo') ?? '';
		$resp->cor_descricao = $request->input('cor_descricao') ?? '';
		$resp->img = $nomeImagem;

		$result = $resp->save();
		if($result){
			session()->flash('mensagem_sucesso', 'Carrossel editada com sucesso!');
		}else{
			session()->flash('mensagem_erro', 'Erro ao editar carrossel!');
		}

		return redirect('/carrosselEcommerce'); 
	}

	public function delete($id){
		try{
			$carrossel = CarrosselEcommerce
			::where('id', $id)
			->first();

			if(valida_objeto($carrossel)){ 
				if($carrossel->img != "" && file_exists(public_path("ecommerce/carrossel/").$carrossel->img)){
					unlink(public_path("ecommerce/carrossel/").$carrossel->img);
				}

				if($carrossel->delete()){
					session()->flash('mensagem_sucesso', 'Registro removido!');
				}else{

					session()->flash('mensagem_erro', 'Erro!');
				}
				return redirect('/carrosselEcommerce');
			}else{
				return redirect('403');
			}
		}catch(\Exception $e){
			return view('errors.sql')
			->with('title', 'Erro ao deletar carrossel')
			->with('motivo', $e->getMessage());
		}
	}


	private function _validate(Request $request, $update = false){
		$rules = [
			'titulo' => 'required|max:30',
			'descricao' => 'required|max:200',
			'link_acao' => 'max:200',
			'nome_botao' => 'max:20',
			'file' => !$update ? 'required' : ''
		];

		$messages = [
			'titulo.required' => 'O campo título é obrigatório.',
			'titulo.max' => '30 caracteres maximos permitidos.',
			'descricao.required' => 'O campo descrição é obrigatório.',
			'descricao.max' => '200 caracteres maximos permitidos.',
			'nome_botao.max' => '20 caracteres maximos permitidos.',

			'link_acao.max' => '200 caracteres maximos permitidos.',
			'file.required' => 'O campo imagem é obrigatório.',
		];
		$this->validate($request, $rules, $messages);
	}
}
