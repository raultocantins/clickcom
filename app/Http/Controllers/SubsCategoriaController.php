<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategoria;
use App\Models\Categoria;

class SubsCategoriaController extends Controller
{
	public function __construct(){
		$this->middleware(function ($request, $next) {
			$value = session('user_logged');
			if(!$value){
				return redirect("/login");
			}
			return $next($request);
		});
	}

	public function index($categoria_id){

		$subs = SubCategoria::
		where('categoria_id', $categoria_id)
		->get();

		$categoria = Categoria::find($categoria_id);
		if(valida_objeto($categoria)){
			return view('subs/list')
			->with('subs', $subs)
			->with('categoria', $categoria)
			->with('title', 'SubCategoria');
		}else{
			return redirect('/403');
		}
	}

	public function new($categoria_id){
		$categoria = Categoria::find($categoria_id);
		if(valida_objeto($categoria)){

			return view('subs/register')
			->with('categoria', $categoria)
			->with('title', 'Cadastrar Subcategoria');
		}else{
			return redirect('/403');
		}
	}

	public function save(Request $request){

		$sub = new SubCategoria();
		$this->_validate($request);

		$result = $sub->create($request->all());

		$msgSucesso = "Subcategoria cadastrada com sucesso";

		if($result){
			session()->flash("mensagem_sucesso", $msgSucesso);
		}else{
			session()->flash('mensagem_erro', 'Erro ao cadastrar!');
		}

		return redirect('/subcategorias/list/'.$request->categoria_id);
	}

	public function edit($id){
		$sub = new SubCategoria(); 

		$resp = $sub
		->where('id', $id)->first();  

		if(valida_objeto($resp->categoria)){
			return view('subs/register')
			->with('sub', $resp)
			->with('categoria', $resp->categoria)
			->with('title', 'Editar Subcategoria');
		}else{
			return redirect('/403');
		}

	}

	public function update(Request $request){
		$sub = new SubCategoria();

		$id = $request->input('id');
		$resp = $sub
		->where('id', $id)->first(); 

		$this->_validate($request);

		$resp->nome = $request->input('nome');

		$result = $resp->save();
		if($result){
			session()->flash('mensagem_sucesso', 'Subcategoria editada com sucesso!');
		}else{
			session()->flash('mensagem_erro', 'Erro ao editar subcategoria!');
		}

		return redirect('/subcategorias/list/'.$resp->categoria_id); 
	}

	public function delete($id){
		try{


			$sub = SubCategoria
			::where('id', $id)
			->first();

			$categoriaId = $sub->categoria_id;
			if(valida_objeto($sub->categoria)){
				if($sub->delete()){
					session()->flash('mensagem_sucesso', 'Registro removido!');
				}else{

					session()->flash('mensagem_erro', 'Erro!');
				}
				return redirect('/subcategorias/list/'.$categoriaId);
			}else{
				return redirect('403');
			}
		}catch(\Exception $e){
			return view('errors.sql')
			->with('title', 'Erro ao deletar SubCategoria')
			->with('motivo', $e->getMessage());
		}
	}


	private function _validate(Request $request){
		$rules = [
			'nome' => 'required|max:50'
		];

		$messages = [
			'nome.required' => 'O campo nome é obrigatório.',
			'nome.max' => '50 caracteres maximos permitidos.'
		];
		$this->validate($request, $rules, $messages);
	}

	public function quickSave(Request $request){
        try{

            $res = SubCategoria::create(
                [
                    'nome' => $request->nome,
                    'categoria_id' => $request->categoria_id,
                ]
            );
            return response()->json($res, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }

}
