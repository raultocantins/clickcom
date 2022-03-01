<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plano;
use App\Models\PlanoEmpresa;
use App\Models\PerfilAcesso;

class PlanoController extends Controller
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
		$planos = Plano::all();

		return view('planos/list')
		->with('planos', $planos)
		->with('title', 'Planos');
	}

	public function new(){
		$perfis = PerfilAcesso::all();

		return view('planos/register')
		->with('perfis', $perfis)
		->with('contratoJs', true)
		->with('title', 'Novo Plano');
	}

	public function editar($id){
		$plano = Plano::find($id);
		$perfis = PerfilAcesso::all();

		return view('planos/register')
		->with('plano', $plano)
		->with('perfis', $perfis)
		->with('contratoJs', true)
		->with('title', 'Editar Plano');
	}

	public function delete($id){
		$plano = Plano::find($id);

		$planoEmpresa = PlanoEmpresa::where('plano_id', $id)->first();

		if($planoEmpresa != null){
			session()->flash("mensagem_erro", "Não é possivel remover um plano atrelado com uma empresa.");
		}else{
			$plano->delete();
			session()->flash("mensagem_sucesso", "Plano removido com sucesso.");

		}
		return redirect('/planos');
	}

	public function save(Request $request){
		$this->_validate($request);
		$request->merge([ 'delivery' => $request->delivery ? 1 : 0]);
		$request->merge([ 'valor' => __replace($request->valor)]);
		$request->merge([ 'descricao' => $request->descricao ?? '']);
		$request->merge([ 'maximo_evento' => $request->maximo_evento ?? 0]);
		$request->merge([ 'armazenamento' => $request->armazenamento ?? 0]);
        $request->merge([ 'visivel' => $request->visivel ? true : false ]);

		if($request->hasFile('file')){
			$file = $request->file;
			$extensao = $file->getClientOriginalExtension();
			$nomeImagem = md5($file->getClientOriginalName() . rand(0,100)).".".$extensao;
			$request->merge([ 'img' => $nomeImagem ]);

			$upload = $file->move(public_path('imgs_planos'), $nomeImagem);

		}

		Plano::create($request->all());
		session()->flash("mensagem_sucesso", "Plano cadastrado com sucesso.");
		return redirect('/planos');
	}

	public function update(Request $request){
		$this->_validate($request);

		$plano = Plano::find($request->id);

		$plano->nome = $request->nome;
		$plano->valor = __replace($request->valor);
		$plano->maximo_clientes = $request->maximo_clientes;
		$plano->maximo_produtos = $request->maximo_produtos;
		$plano->maximo_fornecedores = $request->maximo_fornecedores;
		$plano->maximo_nfes = $request->maximo_nfes;
		$plano->maximo_nfces = $request->maximo_nfces;
		$plano->maximo_cte = $request->maximo_cte;
		$plano->maximo_mdfe = $request->maximo_mdfe;
		$plano->armazenamento = $request->armazenamento ?? 0;
		$plano->maximo_evento = $request->maximo_evento;
		$plano->maximo_usuario = $request->maximo_usuario;
		$plano->perfil_id = $request->perfil_id;
		$plano->intervalo_dias = $request->intervalo_dias;
		$plano->maximo_usuario_simultaneo = $request->maximo_usuario_simultaneo;
		
		$plano->delivery = $request->delivery ? 1 : 0;
		$plano->visivel = $request->visivel ? true : false;
		$plano->descricao = $request->descricao;

		if($request->hasFile('file')){
			$file = $request->file;
			$extensao = $file->getClientOriginalExtension();
			$nomeImagem = md5($file->getClientOriginalName() . rand(0,100)).".".$extensao;

			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
			if(file_exists($public . 'imgs_planos/'.$plano->img) && $plano->img != "")
				unlink($public . 'imgs_planos/'.$plano->img);

			$plano->img = $nomeImagem;
			$upload = $file->move(public_path('imgs_planos'), $nomeImagem);
			
		}
		$plano->save();
		session()->flash("mensagem_sucesso", "Plano atualizado com sucesso.");
		return redirect('/planos');
	}

	private function _validate(Request $request){
		$rules = [
			'nome' => 'required',
			'valor' => 'required',
			'intervalo_dias' => 'required',
			'maximo_clientes' => 'required',
			'maximo_produtos' => 'required',
			'maximo_fornecedores' => 'required',
			'maximo_nfes' => 'required',
			'maximo_nfces' => 'required',
			'maximo_cte' => 'required',
			'maximo_mdfe' => 'required',
			'maximo_mdfe' => 'required',
			'maximo_usuario' => 'required',
			'armazenamento' => 'required',
			// 'descricao' => 'required',
		];

		$messages = [
			'nome.required' => 'Campo obrigatório.',
			'valor.required' => 'Campo obrigatório.',
			'intervalo_dias.required' => 'Campo obrigatório.',
			'maximo_clientes.required' => 'Campo obrigatório.',
			'maximo_produtos.required' => 'Campo obrigatório.',
			'maximo_fornecedores.required' => 'Campo obrigatório.',
			'maximo_nfes.required' => 'Campo obrigatório.',
			'maximo_nfces.required' => 'Campo obrigatório.',
			'maximo_cte.required' => 'Campo obrigatório.',
			'maximo_mdfe.required' => 'Campo obrigatório.',
			'maximo_usuario.required' => 'Campo obrigatório.',
			'descricao.required' => 'Campo obrigatório.',
			'armazenamento.required' => 'Defina um valor zero ou mais.',
		];
		$this->validate($request, $rules, $messages);
	}

}
