<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Representante;
use App\Models\RepresentanteEmpresa;
use App\Helpers\Menu;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Models\FinanceiroRepresentante;

class RepresentanteController extends Controller
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
		$representantes = Representante::
		orderBy('status', 'desc')
		->orderBy('id', 'desc')
		->get();

		return view('representantes/list')
		->with('representantes', $representantes)
		->with('title', 'Representante');
	}

	public function filtro(Request $request){
		$representantes = Representante::
		where('nome', 'LIKE', "%$request->nome%");

		if($request->status != 'TODOS'){
			$representantes->where('status', $request->status);
		}
		$representantes = $representantes->orderBy('status', 'desc')
		->orderBy('id', 'desc')
		->get();

		return view('representantes/list')
		->with('representantes', $representantes)
		->with('status', $request->status)
		->with('nome', $request->nome)
		->with('title', 'Representante');
	}

	public function novo(){
		$empresas = Empresa::
		where('tipo_representante', 1)
		->orderBy('nome', 'desc')
		->get();

		if(sizeof($empresas) == 0){
			session()->flash("mensagem_erro", "Cadastre uma empresa do tipo representante!");
			return redirect()->back();
		}
		return view('representantes/register')
		->with('title', 'Novo Representante')
		->with('empresas', $empresas)
		->with('empresaJs', true);
	}

	public function save(Request $request){

		$this->_validate($request);

		try{

			$data = [
				'nome' => $request->nome_usuario, 
				'senha' => md5($request->senha),
				'login' => $request->login,
				'adm' => 1,
				'ativo' => 1,
				'permissao' => json_encode($this->validaPermissao()),
				'img' => '',
				'empresa_id' => $request->empresa,
				'status' => 1
			];

			$usuario = Usuario::create($data);

			if($usuario){
				$data = [
					'nome' => $request->nome,
					'rua' => $request->rua,
					'numero' => $request->numero,
					'bairro' => $request->bairro,
					'cidade' => $request->cidade,
					'telefone' => $request->telefone,
					'email' => $request->email,
					'cnpj' => $request->cnpj,
					'status' => 1,
					'comissao' => __replace($request->comissao),
					'usuario_id' => $usuario->id
				];

				$empresa = Representante::create($data);
			}
			session()->flash("mensagem_sucesso", "Representante cadastrada!");
			return redirect('/representantes');
		}catch(\Exception $e){
			session()->flash("mensagem_erro", "Erro ao cadastrar: " . $e->getMessage());
			return redirect('/representantes');
		}
	}

	private function validaPermissao(){
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

	private function _validate(Request $request){
		$rules = [
			'nome' => 'required',
			'cnpj' => 'required',
			'rua' => 'required',
			'numero' => 'required',
			'bairro' => 'required',
			'cidade' => 'required',
			'login' => 'required|unique:usuarios',
			'senha' => 'required',
			'telefone' => 'required',
			'comissao' => 'required',
			'limite_cadastros' => 'required',
			'email' => 'required',
			'nome_usuario' => 'required',
			'empresa' => 'required',
		];

		$messages = [
			'nome.required' => 'Campo obrigatório.',
			'empresa.required' => 'Campo obrigatório.',
			'cnpj.required' => 'Campo obrigatório.',
			'rua.required' => 'Campo obrigatório.',
			'numero.required' => 'Campo obrigatório.',
			'bairro.required' => 'Campo obrigatório.',
			'cidade.required' => 'Campo obrigatório.',
			'login.required' => 'Campo obrigatório.',
			'telefone.required' => 'Campo obrigatório.',
			'email.required' => 'Campo obrigatório.',
			'limite_cadastros.required' => 'Campo obrigatório.',
			'senha.required' => 'Campo obrigatório.',
			'nome_usuario.required' => 'Campo obrigatório.',
			'comissao.required' => 'Informe a comissão.',
			'login.unique' => 'Usuário já cadastrado no sistema.'
		];

		$this->validate($request, $rules, $messages);
	}

	public function detalhes($id){
		$representante = Representante::find($id);
		$hoje = date('Y-m-d');
		$planoExpirado = false;

		$permissoesAtivas = $representante->usuario->permissao;

		// print_r($permissoesAtivas);
		// die;
		$permissoesAtivas = json_decode($permissoesAtivas);

		$value = session('user_logged');

		return view('representantes/detalhes')
		->with('representante', $representante)
		->with('planoExpirado', $planoExpirado)
		->with('permissoesAtivas', $permissoesAtivas)
		->with('empresaJs', true)
		->with('title', 'Detalhes representante');
	}

	public function update(Request $request){
		$representante = Representante::find($request->id);

		$permissao = $this->validaPermissao2($request);

		// print_r($permissao);
		// die;

		$representante->nome = $request->nome;
		$representante->rua = $request->rua;
		$representante->numero = $request->numero;
		$representante->bairro = $request->bairro;
		$representante->cidade = $request->cidade;
		$representante->telefone = $request->telefone;
		$representante->email = $request->email;
		$representante->cpf_cnpj = $request->cnpj;
		$representante->comissao = __replace($request->comissao);
		$representante->status = $request->status ? 1 : 0;
		$representante->acesso_xml = $request->acesso_xml ? 1 : 0;
		$representante->limite_cadastros = $request->limite_cadastros;

		$representante->save();

		$usuario = $representante->usuario;
		$usuario->permissao = json_encode($permissao);
		$usuario->save();

		session()->flash("mensagem_sucesso", "Dados atualizados!");
		return redirect()->back();
	}

	private function validaPermissao2($request){
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

	public function delete($id){
		$representante = Representante::find($id)->first();
		if(sizeof($representante->empresas) == 0){
			$representante->delete();
			session()->flash("mensagem_sucesso", "Representante removido!");
			return redirect('representantes');
		}
	}

	public function empresas($id){

		$empresas = Empresa::
		where('tipo_representante', 0)
		->orderBy('nome', 'desc')
		->get();

		$representante = Representante::find($id);

		$temp = [];

		foreach($empresas as $e){
			$res = RepresentanteEmpresa
			::where('representante_id', $id)
			->where('empresa_id', $e->id)
			->first();

			if($res == null){
				array_push($temp, $e);
			}
		}

		return view('representantes/empresas')
		->with('representante', $representante)
		->with('empresas', $temp)
		->with('title', 'Empresas representante');

	}

	public function saveEmpresa(Request $request){
		$this->_validate2($request);

		try{
			RepresentanteEmpresa::create(
				[
					'representante_id' => $request->id,
					'empresa_id' => $request->empresa
				]
			);
			session()->flash("mensagem_sucesso", "Empresa atribuida!");
			return redirect()->back();
		}catch(\Exception $e){
			session()->flash("mensagem_sucesso", "Erro: ". $e->getMessage());
			return redirect()->back();
		}
	}

	private function _validate2(Request $request){
		$rules = [
			'empresa' => 'required|numeric',
		];

		$messages = [
			'empresa.required' => 'Campo obrigatório.',
			'empresa.numeric' => 'Campo obrigatório.',
		];

		$this->validate($request, $rules, $messages);
	}

	public function deleteAttr($id){
		$rep = RepresentanteEmpresa::find($id)->delete();
		session()->flash("mensagem_sucesso", "Empresa desatribuida!");
		return redirect()->back();
	}

	public function alterarSenha($id){
		$representante = Representante::find($id);
		return view('representantes/alterar_senha')
		->with('representante', $representante)
		->with('title', 'Alteração de senha');
	}

	public function alterarSenhaPost(Request $request){
		$representante = Representante::find($request->id);
		$senha = $request->senha;

		$u = $representante->usuario;
		$u->senha = md5($senha);
		$u->save();

		session()->flash("mensagem_sucesso", "Senhas alteradas!");
		return redirect('/representantes/detalhes/' . $representante->id);
	}

	// public function financeiro($id){
	// 	$representante = Representante::find($id);

	// 	$pagamentos = $this->montaPagamentos($representante->empresas, 
	// 		$representante->comissao);

	// 	$pagamentos = $this->ordenaPorData($pagamentos);

	// 	return view('representantes/financeiro')
	// 	->with('representante', $representante)
	// 	->with('pagamentos', $pagamentos)
	// 	->with('title', 'Todos os pagamentos');
	// }

	public function financeiro($id){
		$representante = Representante::find($id);

		$pagamentos = FinanceiroRepresentante::
		select('financeiro_representantes.*')
		->join('representante_empresas', 'representante_empresas.id', '=', 
			'financeiro_representantes.representante_empresa_id')
		->join('representantes', 'representantes.id', '=', 'representante_empresas.representante_id')
		->orderBy('financeiro_representantes.created_at')
		->get();
		
		return view('representantes/financeiro')
		->with('representante', $representante)
		->with('pagamentos', $pagamentos)
		->with('title', 'Todos os pagamentos');
	}
	
	public function filtroFinanceiro(Request $request){
		$representante = Representante::find($request->rep_id);

		$pagamentos = FinanceiroRepresentante::
		select('financeiro_representantes.*')
		->join('representante_empresas', 'representante_empresas.id', '=', 
			'financeiro_representantes.representante_empresa_id')
		->join('representantes', 'representantes.id', '=', 'representante_empresas.representante_id')
		->orderBy('financeiro_representantes.created_at');

		if($request->nome){
			$pagamentos->join('empresas', 'empresas.id', '=', 'representante_empresas.empresa_id');
			$pagamentos->where('empresas.nome', 'LIKE', "%$request->nome%");
		}
		

		if($request->data_inicial && $request->data_final){
			$pagamentos->whereBetween('financeiro_representantes.created_at', [
				$this->parseDate($request->data_inicial), 
				$this->parseDate($request->data_final, true)
			]);
		}

		if($request->status != 'TODOS'){
			$pagamentos->where('financeiro_representantes.pagamento_comissao', $request->status);
		}

		$pagamentos = $pagamentos->get();
		return view('representantes/financeiro')
		->with('representante', $representante)
		->with('pagamentos', $pagamentos)
		->with('nome', $request->nome)
		->with('data_inicial', $request->data_inicial)
		->with('data_final', $request->data_final)
		->with('status', $request->status)
		->with('title', 'Todos os pagamentos');
	}

	private function parseDate($date, $plusDay = false){
		if($plusDay == false)
			return date('Y-m-d', strtotime(str_replace("/", "-", $date)));
		else
			return date('Y-m-d', strtotime("+1 day",strtotime(str_replace("/", "-", $date))));
	}

	public function pagarComissao($id){
		try{
			$fin = FinanceiroRepresentante::find($id);
			$fin->pagamento_comissao = true;
			$fin->save();
			session()->flash("mensagem_sucesso", "Pagamento confirmado!");

		}catch(\Exception $e){
			session()->flash("mensagem_erro", "Erro: " . $e->getMessage());
		}
		return redirect()->back();
	}

}
