<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaPostBlogEcommerce;
use Illuminate\Support\Str;

class CategoriaPostController extends Controller
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
		$categorias = CategoriaPostBlogEcommerce::
		where('empresa_id', $this->empresa_id)
		->get();

		return view('categoriaPost/list')
		->with('categorias', $categorias)
		->with('title', 'Categorias');
	}

	public function new(){
		return view('categoriaPost/register')
		->with('title', 'Cadastrar Categoria Post');
	}

	public function save(Request $request){

		$category = new CategoriaPostBlogEcommerce();
		$this->_validate($request);

		
		$result = $category->create($request->all());

		if($result){
			session()->flash("mensagem_sucesso", "Cadastrado com sucesso!!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao cadastrar categoria.');
		}

		return redirect('/categoriaPosts');
	}

	public function edit($id){
		$categoria = new CategoriaPostBlogEcommerce(); 

		$resp = $categoria
		->where('id', $id)->first();  

		if(valida_objeto($resp)){
			return view('categoriaPost/register')
			->with('categoria', $resp)
			->with('title', 'Editar Categoria');
		}else{
			return redirect('/403');
		}

	}

	public function update(Request $request){
		$categoria = new CategoriaPostBlogEcommerce();

		$id = $request->input('id');
		$resp = $categoria
		->where('id', $id)->first(); 

		$this->_validate($request);

		$resp->nome = $request->input('nome');

		$result = $resp->save();
		if($result){
			session()->flash('mensagem_sucesso', 'Categoria editada com sucesso!');
		}else{
			session()->flash('mensagem_erro', 'Erro ao editar categoria!');
		}

		return redirect('/categoriaPosts'); 
	}

	public function delete($id){
		try{
			$categoria = CategoriaPostBlogEcommerce
			::where('id', $id)
			->first();

			if(valida_objeto($categoria)){ 
				if($categoria->img != "" && file_exists(public_path("ecommerce/categorias/").$categoria->img)){
					unlink(public_path("ecommerce/categorias/").$categoria->img);
				}

				if($categoria->delete()){
					session()->flash('mensagem_sucesso', 'Registro removido!');
				}else{

					session()->flash('mensagem_erro', 'Erro!');
				}
				return redirect('/categoriaPosts');
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
		];

		$messages = [
			'nome.required' => 'O campo nome é obrigatório.',
			'nome.max' => '50 caracteres maximos permitidos.'
		];
		$this->validate($request, $rules, $messages);
	}
}
