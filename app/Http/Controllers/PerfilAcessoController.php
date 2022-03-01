<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerfilAcesso;
use App\Models\Plano;
use App\Models\Empresa;
use App\Helpers\Menu;

class PerfilAcessoController extends Controller
{
	public function __construct(){
		$this->middleware(function ($request, $next) {
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
		$perfis = PerfilAcesso::all();

		return view('perfil/list')
		->with('perfis', $perfis)
		->with('title', 'Perfis de Acesso');
	}

	public function new(){

		$permissoesAtivas = [];

		return view('perfil/register')
		->with('permissoesAtivas', $permissoesAtivas)
		->with('title', 'Perfis de Acesso');
	}

	private function validaPermissao($request){
		$menu = new Menu();
		$arr = $request->all();
		$arr = (array) ($arr);
		$menu = $menu->getMenu();
		$temp = [];
		foreach($menu as $m){
			foreach($m['subs'] as $s){
				// $nome = str_replace("", "_", $s['rota']);
				if(isset($arr[$s['rota']])){
					array_push($temp, $s['rota']);
				}

				if(strlen($s['rota']) > 60){
					$rt = str_replace(".", "_", $s['rota']);
					// $rt = str_replace(":", "_", $s['rota']);
					// echo $rt . "<br>";


					foreach($arr as $key => $a){
						if($key == $rt){
							array_push($temp, $rt);
						}
					}
				}
			}
		}
		return $temp;
	}

	public function save(Request $request){
		$this->_validate($request);

		$permissao = $this->validaPermissao($request);

		$data = [
			'nome' => $request->nome,
			'permissao' => json_encode($permissao)
		];

		$result = PerfilAcesso::create($data);

		session()->flash("mensagem_sucesso", "Perfil cadastrado");
		return redirect('/perfilAcesso');
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

	public function edit($id){
		$perfil = PerfilAcesso::find($id);
		$permissoesAtivas = $perfil->permissao;
		$permissoesAtivas = json_decode($permissoesAtivas);

		return view('perfil/register')
		->with('permissoesAtivas', $permissoesAtivas)
		->with('perfil', $perfil)
		->with('title', 'Perfis de Acesso');
	}

	public function delete($id){
		$perfil = PerfilAcesso::find($id);

		$plano = Plano::
		where('perfil_id', $id)
		->first();

		$empresa = Empresa::
		where('perfil_id', $id)
		->first();

		if($plano != null){
			session()->flash("mensagem_erro", "Perfil está vinculado a um plano");
			return redirect()->back();
		}

		if($empresa != null){
			session()->flash("mensagem_erro", "Perfil está vinculado a uma empresa");
			return redirect()->back();
		}

		$perfil->delete();
		session()->flash("mensagem_sucesso", "Perfil removido");

		return redirect()->back();
	}

	public function update(Request $request){
		$this->_validate($request);

		$perfil = PerfilAcesso::find($request->id);

		$permissao = $this->validaPermissao($request);


		$perfil->nome = $request->nome;
		$perfil->permissao = json_encode($permissao);
		$perfil->save();

		session()->flash("mensagem_sucesso", "Perfil atualizado");
		return redirect('/perfilAcesso');
	}
}
