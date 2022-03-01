<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AutorPostBlogEcommerce;

class AutorPostController extends Controller
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
		$autores = AutorPostBlogEcommerce::
		where('empresa_id', $this->empresa_id)
		->get();

		return view('autorBlog/list')
		->with('autores', $autores)
		->with('title', 'Autores Post');
	}

	public function new(){
		return view('autorBlog/register')
		->with('title', 'Cadastrar Autor');
	}

	public function save(Request $request){

		$autor = new AutorPostBlogEcommerce();
		$this->_validate($request);

		$nomeImagem = "";
		if($request->hasFile('file')){
    		//unlink anterior
			$file = $request->file('file');
			$nomeImagem = Str::random(20).".png";
			$upload = $file->move(public_path('ecommerce/autores'), $nomeImagem);
		}
		$request->merge(['img' => $nomeImagem]);
		$result = $autor->create($request->all());

		if($result){
			session()->flash("mensagem_sucesso", "Cadastrado com sucesso!!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao cadastrar autor.');
		}

		return redirect('/autorPost');
	}

	public function edit($id){
		$autor = new AutorPostBlogEcommerce(); 

		$resp = $autor
		->where('id', $id)->first();  

		if(valida_objeto($resp)){
			return view('autorBlog/register')
			->with('autor', $resp)
			->with('title', 'Editar Autor');
		}else{
			return redirect('/403');
		}

	}

	public function update(Request $request){
		$categoria = new AutorPostBlogEcommerce();

		$id = $request->input('id');
		$resp = $categoria
		->where('id', $id)->first(); 

		$this->_validate($request, true);

		$nomeImagem = $resp->img;
		if($request->hasFile('file')){
    		//unlink anterior

			if($resp->img != "" && file_exists(public_path('ecommerce/autores'). "/".$resp->img)){
				unlink(public_path('ecommerce/autores'). "/".$resp->img);
			}
			$file = $request->file('file');
			$nomeImagem = Str::random(20).".png";
			$upload = $file->move(public_path('ecommerce/autores'), $nomeImagem);
		}

		$resp->nome = $request->input('nome');
		$resp->tipo = $request->input('tipo');
		$resp->img = $nomeImagem;

		$result = $resp->save();
		if($result){
			session()->flash('mensagem_sucesso', 'Autor editado com sucesso!');
		}else{
			session()->flash('mensagem_erro', 'Erro ao editar autor!');
		}

		return redirect('/autorPost'); 
	}

	public function delete($id){
		try{
			$autor = AutorPostBlogEcommerce
			::where('id', $id)
			->first();

			if(valida_objeto($autor)){ 
				if($autor->img != "" && file_exists(public_path("ecommerce/autores/").$autor->img)){
					unlink(public_path("ecommerce/autores/").$autor->img);
				}

				if($autor->delete()){
					session()->flash('mensagem_sucesso', 'Registro removido!');
				}else{

					session()->flash('mensagem_erro', 'Erro!');
				}
				return redirect('/autorPost');
			}else{
				return redirect('403');
			}
		}catch(\Exception $e){
			return view('errors.sql')
			->with('title', 'Erro ao deletar categoria')
			->with('motivo', $e->getMessage());
		}
	}


	private function _validate(Request $request, $update = false){
		$rules = [
			'nome' => 'required|max:50',
			'tipo' => 'required|max:50',
			'file' => !$update ? 'required' : ''
		];

		$messages = [
			'nome.required' => 'O campo nome é obrigatório.',
			'tipo.required' => 'O campo tipo é obrigatório.',
			'file.required' => 'O campo imagem é obrigatório.',
			'nome.max' => '50 caracteres maximos permitidos.',
			'tipo.max' => '50 caracteres maximos permitidos.'
		];
		$this->validate($request, $rules, $messages);
	}

}
