<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaMasterDelivery;

class CategoriaMasterDeliveryController extends Controller
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
		$categorias = CategoriaMasterDelivery::all();

		return view('categoriaMaster/list')
		->with('categorias', $categorias)
		->with('title', 'Categorias de Delivery');
	}

	public function new(){
		return view('categoriaMaster/register')
		->with('title', 'Cadastrar Categoria para Delivery');
	}

	public function save(Request $request){

		$category = new CategoriaMasterDelivery();

		$this->_validate($request);

		$file = $request->file('file');

		$extensao = $file->getClientOriginalExtension();
		$nomeImagem = md5($file->getClientOriginalName()).".".$extensao;
		$request->merge([ 'img' => $nomeImagem ]);

		$upload = $file->move(public_path('categorias_delivery'), $nomeImagem);

		if(!$upload){

			session()->flash('mensagem_erro', 'Erro ao realizar upload da imagem.');
		}else{

			$result = $category->create($request->all());
			if($result){

				session()->flash("mensagem_sucesso", "Categoria cadastrada com sucesso.");
			}else{

				session()->flash('mensagem_erro', 'Erro ao cadastrar categoria.');
			}
		}

		return redirect('/categoriaMasterDelivery');
	}

	public function edit($id){
		$categoria = new CategoriaMasterDelivery();
		$resp = $categoria
		->where('id', $id)->first();  
		return view('categoriaMaster/register')
		->with('categoria', $resp)
		->with('title', 'Editar Categoria de Delivery');
		

	}

	public function update(Request $request){
		$categoria = new CategoriaMasterDelivery();

		$id = $request->input('id');
		$resp = $categoria
		->where('id', $id)->first(); 

		$anterior = CategoriaMasterDelivery::where('id', $id)
		->first();
		if($request->hasFile('file')){
    		//unlink anterior
			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
			if(file_exists($public . 'categorias_delivery/'.$anterior->img))
				unlink($public . 'categorias_delivery/'.$anterior->img);

			$file = $request->file('file');

			$extensao = $file->getClientOriginalExtension();
			$nomeImagem = md5($file->getClientOriginalName()).".".$extensao;

			$upload = $file->move(public_path('categorias_delivery'), $nomeImagem);
			$request->merge([ 'img' => $nomeImagem ]);
		}else{
			$request->merge([ 'img' => $anterior->path ]);
		}

		$this->_validate($request, false);

		$resp->nome = $request->input('nome');

		$result = $resp->save();
		if($result){
			session()->flash('mensagem_sucesso', 'Categoria editada com sucesso!');
		}else{
			session()->flash('mensagem_erro', 'Erro ao editar categoria!');
		}

		return redirect('/categoriaMasterDelivery'); 
	}

	public function delete($id){
		$categoria = CategoriaMasterDelivery
		::where('id', $id)
		->first();

		$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
		if(file_exists($public . 'categorias_delivery/'.$categoria->img)){
			try{
				unlink($public . 'categorias_delivery/'.$categoria->img);
			}catch(\Exception $e){
				
			}
		}
		if($categoria->delete()){

			session()->flash('mensagem_sucesso', 'Registro removido!');
		}else{

			session()->flash('mensagem_erro', 'Erro!');
		}
		return redirect('/categoriaMasterDelivery');

	}

	private function _validate(Request $request, $fileExist = true){
		$rules = [
			'nome' => 'required|max:30',
			'file' => $fileExist ? 'required' : ''
		];

		$messages = [
			'nome.required' => 'O campo nome é obrigatório.',
			'nome.max' => '50 caracteres maximos permitidos.',
			'file.required' => 'O campo imagem é obrigatório.'
		];
		$this->validate($request, $rules, $messages);
	}

}
