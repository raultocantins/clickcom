<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriaDestaqueMasterDelivery;
use App\Models\ProdutoDestaqueMasterDelivery;
use App\Models\ProdutoDelivery;

class DestaqueDeliveryMasterController extends Controller
{
	public function index(){
		$produtos = ProdutoDestaqueMasterDelivery::all();

		return view('produtosDestaque/list')
		->with('produtos', $produtos)
		->with('title', 'Produtos em destaque');
	}

	public function novoProduto(){
		$categorias = CategoriaDestaqueMasterDelivery::all();

		if(sizeof($categorias) == 0){
			session()->flash('mensagem_erro', 'Cadastre uma categoria primeiro!');
			return redirect('/categoriasParaDestaque/new');
		}

		$produtos = ProdutoDelivery::all();

		if(sizeof($produtos) == 0){
			session()->flash('mensagem_erro', 'Nenhum produto de delivery cadastrado!');
			return redirect()->back();
		}

		return view('produtosDestaque/register')
		->with('categorias', $categorias)
		->with('produtos', $produtos)
		->with('title', 'Produtos em destaque');
	}

	public function saveProduto(Request $request){
		try{
			ProdutoDestaqueMasterDelivery::create($request->all());
			session()->flash('mensagem_sucesso', 'Produto cadastrado como destaque!');
		}catch(\Exception $e){
			session()->flash('mensagem_erro', $e->getMessage());
		}
		return redirect('/produtosDestaque');
	}

    //categorias
	public function listaCategoria(){
		$categorias = CategoriaDestaqueMasterDelivery::all();

		return view('categoriasDestaque/list')
		->with('categorias', $categorias)
		->with('title', 'Categorias de destaque');
	}

	public function newCategoria(){
		return view('categoriasDestaque/register')
		->with('title', 'Categoria de destaque');
	}

	public function saveCategoria(Request $request){
		$categoria = new CategoriaDestaqueMasterDelivery();
		$this->_validateCategoria($request);

		$result = $categoria->create($request->all());

		if($result){
			session()->flash("mensagem_sucesso", "Categoria cadastrada com sucesso.");
		}else{
			session()->flash('mensagem_erro', 'Erro ao cadastrar categoria.');
		}

		return redirect('/categoriasParaDestaque');
	}

	private function _validateCategoria(Request $request){
		$rules = [
			'nome' => 'required|max:50'
		];

		$messages = [
			'nome.required' => 'O campo nome é obrigatório.',
			'nome.max' => '50 caracteres maximos permitidos.'
		];
		$this->validate($request, $rules, $messages);
	}

	public function editCategoria($id){
		$categoria = new CategoriaDestaqueMasterDelivery(); 

		$resp = $categoria
		->where('id', $id)->first();  

		return view('categoriasDestaque/register')
		->with('categoria', $resp)
		->with('title', 'Editar Categoria de Destaque');
	}

	public function updateCategoria(Request $request){
		$categoria = new CategoriaDestaqueMasterDelivery();

		$id = $request->input('id');
		$resp = $categoria
		->where('id', $id)->first(); 

		$this->_validateCategoria($request);


		$resp->nome = $request->input('nome');

		$result = $resp->save();
		if($result){
			session()->flash('mensagem_sucesso', 'Categoria editada com sucesso!');
		}else{
			session()->flash('mensagem_erro', 'Erro ao editar categoria!');
		}

		return redirect('/categoriasParaDestaque'); 
	}

	public function deleteCategoria($id){
		$resp = CategoriaDestaqueMasterDelivery
		::where('id', $id)
		->first();

		if($resp->delete()){
			session()->flash('mensagem_sucesso', 'Registro removido!');
		}else{
			session()->flash('mensagem_erro', 'Erro!');
		}
		return redirect('/categoriasParaDestaque');

	}
}
