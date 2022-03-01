<?php

namespace App\Http\Middleware;

use Closure;
use Response;
use App\Models\UsuarioAcesso;
use App\Models\Usuario;
use App\Models\Empresa;

class UsuariosLogado
{
	public function handle($request, Closure $next){
		$usr = $this->usuarioExiste($request->login, $request->senha);
		
		if($usr == null){
			session()->flash('mensagem_login', 'Credencial(s) incorreta(s)!');
			return redirect('/login')->with('login', $request->login);
		}

		// if(strtolower($request->login) == getenv("USERMASTER")){
		if(isSuper($request->login)){
			return $next($request);
		}

		$empresa_id = $usr->empresa_id;
		$empresa = Empresa::find($empresa_id);

		if(!$empresa->planoEmpresa){
          session()->flash('mensagem_login', 'Empresa sem plano atribuido!!');
          return redirect('/login');
        }

		if($empresa->planoEmpresa->plano->maximo_usuario_simultaneo == -1){
			return $next($request);
		}

		$acessos = UsuarioAcesso::
		select('usuario_acessos.*')
		->join('usuarios', 'usuarios.id' , '=', 'usuario_acessos.usuario_id')
		->where('status', 0)
		->where('usuarios.empresa_id', $empresa_id)
		->get();

		foreach($acessos as $a){
			if($a->usuario->login == $request->login){
				return $next($request);
			}
		}

		$cont = sizeof($acessos);

		if($cont < $empresa->planoEmpresa->plano->maximo_usuario_simultaneo){
			return $next($request);
		} else {
			session()->flash('mensagem_login', 'Limite de usuários logados atingido!!');
			return redirect()->back();
		}
	}

	private function usuarioExiste($usuario, $senha){
		return Usuario::
		where('login', $usuario)
		->where('senha', md5($senha))
		->first();
	}

}
