<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Acessor;
use App\Models\Funcionario;
use App\Models\Cliente;
use App\Models\Cidade;

class AcessorController extends Controller
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

	public function index(Request $request){

		$acessores = Acessor::
		where('empresa_id', $request->empresa_id)
		->get();

		return view('acessor/list')
		->with('acessores', $acessores)
		->with('title', 'Acessores');
	}

	public function new(){
		$estados = Cliente::estados();

		$funcionarios = Funcionario::
		where('empresa_id', $this->empresa_id)
		->get();

		$cidades = Cidade::all();

		return view('acessor/register')
		->with('pessoaFisicaOuJuridica', true)
		->with('funcionarios', $funcionarios)
		->with('cidades', $cidades)
		->with('estados', $estados)
		->with('title', 'Cadastrar Acessor');
	}

	public function save(Request $request){

		$this->_validate($request);

		$request->merge(['percentual_comissao' => $request->percentual_comissao ? __replace($request->percentual_comissao) : 0]);
		$request->merge(['ativo' => $request->ativo ? 1 : 0]);
		$dataRegsitro = $this->parseDate($request->input('data_registro'));
		$request->merge([ 'data_registro' => $dataRegsitro]);

		$result = Acessor::create($request->all());

		if($result){
			session()->flash("mensagem_sucesso", "Acessor adicionado!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao cadastrar acessor.');
		}

		return redirect('/acessores');
	}

	private function parseDate($date){
		return date('Y-m-d', strtotime(str_replace("/", "-", $date)));
	}

	public function edit($id){

		$resp = Acessor::find($id);  

		$estados = Cliente::estados();

		$funcionarios = Funcionario::
		where('empresa_id', $this->empresa_id)
		->get();

		$cidades = Cidade::all();

		if(valida_objeto($resp)){
			return view('acessor/register')
			->with('pessoaFisicaOuJuridica', true)
			->with('acessor', $resp)
			->with('estados', $estados)
			->with('funcionarios', $funcionarios)
			->with('cidades', $cidades)
			->with('title', 'Editar Acessor');
		}else{
			return redirect('/403');
		}

	}

	public function update(Request $request){

		$id = $request->input('id');
		$resp = Acessor::
		where('id', $id)->first(); 

		$this->_validate($request);

		$resp->razao_social = $request->razao_social;
		$resp->cpf_cnpj = $request->cpf_cnpj;
		$resp->rua = $request->rua;
		$resp->numero = $request->numero;
		$resp->bairro = $request->bairro;
		$resp->cep = $request->cep;
		$resp->cidade_id = $request->cidade_id;
		$resp->email = $request->email;
		$resp->telefone = $request->telefone;
		$resp->data_registro = $this->parseDate($request->input('data_registro'));
		$resp->percentual_comissao = $request->percentual_comissao ? __replace($request->percentual_comissao) : 0;
		$resp->funcionario_id = $request->funcionario_id;
		$resp->ativo = $request->ativo ? true : false;

		$result = $resp->save();
		if($result){
			session()->flash('mensagem_sucesso', 'Acessor editado com sucesso!');
		}else{
			session()->flash('mensagem_erro', 'Erro ao editar acessor!');
		}

		return redirect('/acessores'); 
	}

	public function delete($id){
		try{
			$acessor = Acessor
			::where('id', $id)
			->first();
			if(valida_objeto($acessor)){
				if($acessor->delete()){
					session()->flash('mensagem_sucesso', 'Registro removido!');
				}else{

					session()->flash('mensagem_erro', 'Erro!');
				}
				return redirect('/acessores');
			}else{
				return redirect('403');
			}
		}catch(\Exception $e){
			return view('errors.sql')
			->with('title', 'Erro ao deletar acessor')
			->with('motivo', $e->getMessage());
		}
	}


	private function _validate(Request $request){
		$doc = $request->cpf_cnpj;

		$rules = [
			'razao_social' => 'required|max:100',
			'cpf_cnpj' => strlen($doc) > 14 ? 'required|min:18' : 'required|min:14',
			'rua' => 'required|max:80',
			'numero' => 'required|max:10',
			'bairro' => 'required|max:50',
			'telefone' => 'max:20',
			'email' => 'max:40',
			'cep' => 'required|min:9',
			'cidade_id' => 'required',
			'data_registro' => 'required'

		];

		$messages = [
			'razao_social.required' => 'Campo obrigatório.',
			'razao_social.max' => '100 caracteres maximos permitidos.',
			'cpf_cnpj.required' => 'O campo CPF/CNPJ é obrigatório.',
			'cpf_cnpj.min' => strlen($doc) > 14 ? 'Informe 14 números para CNPJ.' : 'Informe 14 números para CPF.',
			'rua.required' => 'O campo Rua é obrigatório.',
			'rua.max' => '80 caracteres maximos permitidos.',
			'numero.required' => 'O campo Numero é obrigatório.',
			'cep.required' => 'O campo CEP é obrigatório.',
			'cep.min' => 'CEP inválido.',
			'cidade_id.required' => 'O campo Cidade é obrigatório.',
			'numero.max' => '10 caracteres maximos permitidos.',
			'bairro.required' => 'O campo Bairro é obrigatório.',
			'bairro.max' => '50 caracteres maximos permitidos.',
			'telefone.required' => 'O campo Telefone é obrigatório.',
			'telefone.max' => '20 caracteres maximos permitidos.',
			'email.required' => 'O campo Email é obrigatório.',
			'email.max' => '40 caracteres maximos permitidos.',
			'email.email' => 'Email inválido.',
			'data_registro.required' => 'Campo obrigatório.',

		];
		$this->validate($request, $rules, $messages);
	}

	public function list($id){
		$grupo = GrupoCliente::find($id);

		return view('grupoCliente/clientes')
		->with('grupo', $grupo)
		->with('title', 'Lista de clientes');
	}
}
