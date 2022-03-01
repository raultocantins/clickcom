<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\UsuarioAcesso;
use App\Helpers\Menu;

class UsuarioController extends Controller
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

	public function lista(){
		$usuarios = Usuario::
		where('empresa_id', $this->empresa_id)
		->get();
		return view('usuarios/list')
		->with('usuarios', $usuarios)
		->with('title', 'Lista de Usuários');
	}

	public function new(){
		$value = session('user_logged');
		$usuario = Usuario::find($value['id']);
		$permissoesAtivas = $usuario->empresa->permissao;
		$permissoesDoUsuario = [];
		$permissoesAtivas = json_decode($permissoesAtivas);
		$permissoesUsuario = [];

		if($value['super']){
			$permissoesAtivas = $this->detalhesMaster();
		}

		$menu = new Menu();
		$menu = $menu->getMenu();

		for($i=0; $i < sizeof($menu); $i++){
			$temp = false;
			foreach($menu[$i]['subs'] as $s){
				if(in_array($s['rota'], $permissoesAtivas)){
					$temp = true;
				}
			}
			$menu[$i]['ativo'] = $temp;
		}

		return view('usuarios/register')
		->with('usuarioJs', true)
		->with('permissoesAtivas', $permissoesAtivas)
		->with('permissoesUsuario', $permissoesUsuario)
		->with('menuAux', $menu)
		->with('permissoesDoUsuario', $permissoesDoUsuario)
		->with('title', 'Cadastrar Usuário');
	}

	private function detalhesMaster(){
		$menu = new Menu();
		$menu = $menu->getMenu();
		$temp = [];
		foreach($menu as $m){
			foreach($m['subs'] as $s){
				array_push($temp, $s['rota']);
			}
		}
		return $temp;
	}

	public function edit($id){
		$value = session('user_logged');
		
		$usuario = Usuario::
		where('id', $id)
		->first();
		if(valida_objeto($usuario)){

			$permissoesAtivas = $usuario->empresa->permissao;
			$permissoesUsuario = $usuario->permissao;
			$permissoesDoUsuario = [];
			$permissoesAtivas = json_decode($permissoesAtivas);
			$permissoesUsuario = json_decode($permissoesUsuario);

			if($value['super']){
				$permissoesAtivas = $this->detalhesMaster();
			}

			$menu = new Menu();
			$menu = $menu->getMenu();


			for($i=0; $i < sizeof($menu); $i++){
				$temp = false;
				foreach($menu[$i]['subs'] as $s){
					if(in_array($s['rota'], $permissoesAtivas)){
						$temp = true;
					}
				}
				$menu[$i]['ativo'] = $temp;
			}

			return view('usuarios/register')
			->with('usuarioJs', true)
			->with('usuario', $usuario)
			->with('permissoesAtivas', $permissoesAtivas)
			->with('permissoesUsuario', $permissoesUsuario)
			->with('menuAux', $menu)
			->with('title', 'Editar Usuários');
		}else{
			return redirect('/403');
		}
	}

	private function validaPermissao($request){
		$menu = new Menu();
		$arr = $request->all();
		$arr = (array) ($arr);
		$menu = $menu->getMenu();
		$temp = [];
		foreach($menu as $m){
			foreach($m['subs'] as $s){
				if(isset($arr[$s['rota']])){
					array_push($temp, $s['rota']);
				}
			}
		}

		return $temp;

	}

	public function save(Request $request){

		$this->_validate($request);

		$permissao = $this->validaPermissao($request);

		$result = Usuario::create([
			'nome' => $request->nome,
			'login' => $request->login,
			'senha' => md5($request->senha),
			'adm' => $request->adm ? true : false,
			'ativo' => true,
			'email' => $request->email,
			'permissao' => json_encode($permissao),
			'empresa_id' => $this->empresa_id
		]);

		if($result){
			session()->flash("mensagem_sucesso", "Usuário salvo!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao criar usuário!');
		}

		return redirect('/usuarios');
	}

	public function update(Request $request){

		$this->_validate($request, true);
		$permissao = $this->validaPermissao($request);

		$usr = Usuario::
		where('id', $request->id)
		->first();

		$usr->nome = $request->nome;
		$usr->login = $request->login;
		$usr->email = $request->email;
		if($request->senha){
			$usr->senha = md5($request->senha);
		}
		
		$usr->adm = $request->adm ? true : false;
		$usr->somente_fiscal = $request->somente_fiscal ? true : false;
		$usr->permissao = json_encode($permissao);

		$result = $usr->save();
		if($result){
			session()->flash("mensagem_sucesso", "Usuário atualizado!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao atualizar usuário!');
		}

		return redirect('/usuarios');
	}

	public function delete($id){
		$usuario = Usuario::
		where('id', $id)
		->first();

		$usuarios = Usuario::
		where('empresa_id', $this->empresa_id)
		->get();

		if(sizeof($usuarios) == 1){
			session()->flash('mensagem_erro', 'Não é possivel remover o ultimo usuário!');
			return redirect()->back();
		}
		if(valida_objeto($usuario)){

			if($usuario->delete()){
				session()->flash("mensagem_sucesso", "Usuário removido!");
			}else{
				session()->flash('mensagem_erro', 'Erro ao remover usuário!');
			}

			return redirect('/usuarios');
		}else{
			return redirect('/403');
		}
	}


	private function _validate(Request $request, $update = false){
		$rules = [
			'nome' => 'required',
			'email' => 'required|email',
			'login' => ['required', \Illuminate\Validation\Rule::unique('usuarios')->ignore($request->id)],
			'senha' => !$update ? 'required' : '',
		];

		$messages = [
			'nome.required' => 'O campo nome é obrigatório.',
			'email.required' => 'O campo email é obrigatório.',
			'email.email' => 'Email inválido',
			'login.required' => 'O campo login é obrigatório.',
			'senha.required' => 'O campo senha é obrigatório',
			'login.unique' => 'Usuário já cadastrado no sistema.'
		];

		$this->validate($request, $rules, $messages);
	}

	public function setTema(Request $request){
		$tema = $request->tema;
		$tema_menu = $request->tema_menu;
		$id = $value = session('user_logged')['id'];
		$usuario = Usuario::find($id);
		$usuario->tema = $tema;
		$usuario->tema_menu = $tema_menu;
		$usuario->save();
		session()->flash("mensagem_sucesso", "Tema salvo!");
		return redirect()->back();

	}

	public function historico($id){
		$usuario = Usuario::find($id);

		if(valida_objeto($usuario)){

			$acessos = UsuarioAcesso::
			where('usuario_id', $id)
			->paginate(50);
			
			return view('usuarios/historico')
			->with('usuario', $usuario)
			->with('acessos', $acessos)
			->with('title', 'Histórico de Usuário');
		}else{
			return redirect('/403');
		}
	}
}
