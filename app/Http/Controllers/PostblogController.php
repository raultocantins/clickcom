<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostBlogEcommerce;
use App\Models\CategoriaPostBlogEcommerce;
use App\Models\AutorPostBlogEcommerce;
use Illuminate\Support\Str;

class PostBlogController extends Controller
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
		$posts = PostBlogEcommerce::
		where('empresa_id', $this->empresa_id)
		->get();

		return view('postBlog/list')
		->with('posts', $posts)
		->with('title', 'Posts');
	}

	public function new(){
		$autores = AutorPostBlogEcommerce::
		where('empresa_id', $this->empresa_id)
		->get();
		$categorias = CategoriaPostBlogEcommerce::
		where('empresa_id', $this->empresa_id)
		->get();

		return view('postBlog/register')
		->with('autores', $autores)
		->with('categorias', $categorias)
		->with('contratoJs', true)
		->with('title', 'Cadastrar Post');
	}

	public function save(Request $request){

		$category = new PostBlogEcommerce();
		$this->_validate($request);

		$nomeImagem = "";
		if($request->hasFile('file')){
    		//unlink anterior
			$file = $request->file('file');
			$nomeImagem = Str::random(20).".png";
			$upload = $file->move(public_path('ecommerce/posts'), $nomeImagem);
		}
		$request->merge(['img' => $nomeImagem]);
		$request->merge(['tags' => $request->tags ?? '']);
		$result = $category->create($request->all());

		if($result){
			session()->flash("mensagem_sucesso", "Cadastrado com sucesso!!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao cadastrar post.');
		}

		return redirect('/postBlog');
	}

	public function edit($id){
		$post = new PostBlogEcommerce(); 

		$resp = $post
		->where('id', $id)->first();  

		if(valida_objeto($resp)){
			$autores = AutorPostBlogEcommerce::
			where('empresa_id', $this->empresa_id)
			->get();
			$categorias = CategoriaPostBlogEcommerce::
			where('empresa_id', $this->empresa_id)
			->get();

			return view('postBlog/register')
			->with('post', $resp)
			->with('categorias', $categorias)
			->with('autores', $autores)
			->with('contratoJs', true)
			->with('title', 'Editar Post');
		}else{
			return redirect('/403');
		}

	}

	public function update(Request $request){
		$categoria = new PostBlogEcommerce();

		$id = $request->input('id');
		$resp = $categoria
		->where('id', $id)->first(); 

		$this->_validate($request, true);

		$nomeImagem = $resp->img;
		if($request->hasFile('file')){
    		//unlink anterior

			if($resp->img != "" && file_exists(public_path('ecommerce/posts'). "/".$resp->img)){
				unlink(public_path('ecommerce/posts'). "/".$resp->img);
			}
			$file = $request->file('file');
			$nomeImagem = Str::random(20).".png";
			$upload = $file->move(public_path('ecommerce/posts'), $nomeImagem);
		}

		$resp->titulo = $request->input('titulo');
		$resp->texto = $request->input('texto');
		$resp->tags = $request->input('tags');
		$resp->categoria_id = $request->input('categoria_id');
		$resp->autor_id = $request->input('autor_id');
		$resp->img = $nomeImagem;

		$result = $resp->save();
		if($result){
			session()->flash('mensagem_sucesso', 'Categoria editada com sucesso!');
		}else{
			session()->flash('mensagem_erro', 'Erro ao editar categoria!');
		}

		return redirect('/postBlog'); 
	}

	public function delete($id){
		try{
			$categoria = CategoriaProdutoEcommerce
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
				return redirect('/categoriaEcommerce');
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
			'titulo' => 'required|max:50',
			'categoria_id' => 'required',
			'autor_id' => 'required',
			'tags' => 'max:100',
			'file' => !$update ? 'required' : ''
		];

		$messages = [
			'titulo.required' => 'O campo título é obrigatório.',
			'titulo.max' => '50 caracteres maximos permitidos.',
			'tags.max' => '100 caracteres maximos permitidos.',

			'categoria_id.required' => 'O campo categoria é obrigatório.',
			'autor_id.required' => 'O campo autor é obrigatório.',
			'texto.required' => 'O campo texto é obrigatório.',

			'file.required' => 'O campo imagem é obrigatório.',
		];
		$this->validate($request, $rules, $messages);
	}
}
