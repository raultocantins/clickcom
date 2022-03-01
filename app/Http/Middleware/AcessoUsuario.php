<?php

namespace App\Http\Middleware;

use Closure;
use Response;
use App\Models\UsuarioAcesso;

class AcessoUsuario
{
	public function handle($request, Closure $next){
		$value = session('user_logged');

		$acesso = UsuarioAcesso::
		select('usuario_acessos.*')
		->where('usuario_id', $value['id'])
		->join('usuarios', 'usuarios.id' , '=', 'usuario_acessos.usuario_id')
		->where('status', 0)
		->where('usuarios.empresa_id', $request->empresa_id)
		->get();

		if(sizeof($acesso) > 1){
			$resultAtivo = $this->getUsuariosAtivos($acesso, $value['hash']);
			if($resultAtivo){
				$acesso = UsuarioAcesso::
				where('hash', $value['hash'])
				->where('status', 0)
				->first();
				$acesso->updated_at = date('Y-m-d H:i:s');
				$acesso->save();
				return $next($request);
			}else{
				$this->desativaOutrasSessao($acesso, $value['hash']);

				session()->forget('user_logged');
				session()->flash('mensagem_login', 'Já existe uma sessão ativa com outro usuário.');
				return redirect("/login");
			}
		}else if(sizeof($acesso) == 1){
			return $next($request);
			
			$acesso = $acesso[0];
			if($value['hash'] == $acesso->hash){
				$acesso->updated_at = date('Y-m-d H:i:s');
				$acesso->save();
				return $next($request);
			}

		}else{
			session()->forget('user_logged');
			session()->flash('mensagem_login', 'Sessão não encontrada ('.(sizeof($acesso)+1).').');
			return redirect("/login");
		}

	}

	private function getUsuariosAtivos($acessos, $hash){
		$logado = 0;
		$outroLogado = 0;
		foreach($acessos as $a){
			$agora = date('Y-m-d H:i:s');
			$dif = strtotime($agora) - strtotime($a->updated_at);
			$minutos = $dif/60;

			if($hash == $a->hash){
				$logado = $minutos;
			}else{
				$outroLogado = $minutos;
			}

		}

		if($logado < $outroLogado) return true;
		else return false;
	}

	private function desativaOutrasSessao($acessos, $hash){
		foreach($acessos as $a){
			if($hash != $a->hash){
				$a->status = 1;
				$a->save();
			}
		}
	}
}
