<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfigNota;
use App\Models\Certificado;
use App\Models\Cidade;
use App\Models\NaturezaOperacao;
use App\Services\NFService;
use NFePHP\Common\Certificate;
use Mail;

class ConfigNotaController extends Controller
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

	function sanitizeString($str){
		return preg_replace('{\W}', ' ', preg_replace('{ +}', ' ', strtr(
			utf8_decode(html_entity_decode($str)),
			utf8_decode('ÀÁÃÂÉÊÍÓÕÔÚÜÇÑàáãâéêíóõôúüçñ'),
			'AAAAEEIOOOUUCNaaaaeeiooouucn')));
	}

	public function index(){
		try{
			$naturezas = NaturezaOperacao::
			where('empresa_id', $this->empresa_id)
			->get();
			$tiposPagamento = ConfigNota::tiposPagamento();
			$tiposFrete = ConfigNota::tiposFrete();
			$listaCSTCSOSN = ConfigNota::listaCST();
			$listaCSTPISCOFINS = ConfigNota::listaCST_PIS_COFINS();
			$listaCSTIPI = ConfigNota::listaCST_IPI();

			$config = ConfigNota::
			where('empresa_id', $this->empresa_id)
			->first();
			$certificado = Certificado::
			where('empresa_id', $this->empresa_id)
			->first();

			$cUF = ConfigNota::estados();

			$infoCertificado = null;
			if($certificado != null){
				$infoCertificado = $this->getInfoCertificado($certificado);
			}

			$soapDesativado = !extension_loaded('soap');
			$cidades = Cidade::all();	

			return view('configNota/index')
			->with('config', $config)
			->with('naturezas', $naturezas)
			->with('tiposPagamento', $tiposPagamento)
			->with('tiposFrete', $tiposFrete)
			->with('infoCertificado', $infoCertificado)
			->with('soapDesativado', $soapDesativado)
			->with('listaCSTCSOSN', $listaCSTCSOSN)
			->with('listaCSTPISCOFINS', $listaCSTPISCOFINS)
			->with('listaCSTIPI', $listaCSTIPI)
			->with('cUF', $cUF)
			->with('cidades', $cidades)
			->with('testeJs', true)
			->with('configJs', true)
			->with('certificado', $certificado)
			->with('title', 'Configurar Emitente');
		}catch(\Exception $e){
			echo $e->getMessage();
			echo "<br><a href='/configNF/deleteCertificado'>Remover Certificado</a>";
		}
	}

	private function getInfoCertificado($certificado){

		$infoCertificado = Certificate::readPfx($certificado->arquivo, $certificado->senha);

		$publicKey = $infoCertificado->publicKey;

		$inicio =  $publicKey->validFrom->format('Y-m-d H:i:s');
		$expiracao =  $publicKey->validTo->format('Y-m-d H:i:s');

		return [
			'serial' => $publicKey->serialNumber,
			'inicio' => \Carbon\Carbon::parse($inicio)->format('d-m-Y H:i'),
			'expiracao' => \Carbon\Carbon::parse($expiracao)->format('d-m-Y H:i'),
			'id' => $publicKey->commonName
		];

	}

	public function save(Request $request){
		$this->_validate($request);
		$uf = $request->uf;

		$nomeImagem = "";

		if($request->hasFile('file')){
			$file = $request->file('file');

			$extensao = $file->getClientOriginalExtension();
			$rand = rand(0, 999999);
			$nomeImagem = md5($file->getClientOriginalName()).$rand.".".$extensao;
			$upload = $file->move(public_path('logos'), $nomeImagem);
		}

		$cidade = Cidade::find($request->cidade);
		$codMun = $cidade->codigo;
		$uf = $cidade->uf;
		$cUF = ConfigNota::getCodUF($uf);
		$municipio = $cidade->nome;
		if($request->id == 0){
			
			$result = ConfigNota::create([
				'razao_social' => strtoupper($this->sanitizeString($request->razao_social)),
				'nome_fantasia' => strtoupper($this->sanitizeString($request->nome_fantasia)),
				'cnpj' => $request->cnpj,
				'ie' => $request->ie,
				'logradouro' => strtoupper($this->sanitizeString($request->logradouro)),
				'numero' => strtoupper($this->sanitizeString($request->numero)),
				'bairro' => strtoupper($this->sanitizeString($request->bairro)),
				'cep' => $request->cep,
				'email' => $request->email,
				'municipio' => strtoupper($municipio),
				'codMun' => $codMun,
				'codPais' => '1058',
				'UF' => $uf,
				'pais' => 'BRASIL',
				'fone' => $this->sanitizeString($request->fone),
				'CST_CSOSN_padrao' => $request->CST_CSOSN_padrao, 
				'CST_COFINS_padrao' => $request->CST_COFINS_padrao, 
				'CST_PIS_padrao' => $request->CST_PIS_padrao, 
				'CST_IPI_padrao' => $request->CST_IPI_padrao, 
				'frete_padrao' => $request->frete_padrao, 
				'tipo_pagamento_padrao' => $request->tipo_pagamento_padrao, 
				'nat_op_padrao' => $request->nat_op_padrao ?? 0, 
				'ambiente' => $request->ambiente, 
				'cUF' => $cUF,
				'ultimo_numero_nfe' => $request->ultimo_numero_nfe, 
				'ultimo_numero_nfce' => $request->ultimo_numero_nfce, 
				'ultimo_numero_cte' => $request->ultimo_numero_cte, 
				'ultimo_numero_mdfe' => $request->ultimo_numero_mdfe,
				'numero_serie_nfe' => $request->numero_serie_nfe,
				'numero_serie_nfce' => $request->numero_serie_nfce,
				'numero_serie_cte' => $request->numero_serie_cte,
				'csc' => $request->csc,
				'csc_id' => $request->csc_id,
				'certificado_a3' => $request->certificado_a3 ? true: false,
				'empresa_id' => $request->empresa_id,
				'inscricao_municipal' => $request->inscricao_municipal ?? '',
				'aut_xml' => $request->aut_xml ?? '',
				'logo' => $nomeImagem,
				'campo_obs_nfe' => $request->campo_obs_nfe ?? '',
				'percentual_lucro_padrao' => $request->percentual_lucro_padrao ?? 0,
				'senha_remover' => $request->senha_remover ? md5($request->senha_remover) : ''
			]);
		}else{
			$config = ConfigNota::
			where('empresa_id', $this->empresa_id)
			->first();

			$config->razao_social = strtoupper($this->sanitizeString($request->razao_social));
			$config->nome_fantasia = strtoupper($this->sanitizeString($request->nome_fantasia));
			$config->cnpj = $this->sanitizeString($request->cnpj);
			$config->ie = $this->sanitizeString($request->ie);
			$config->logradouro = strtoupper($this->sanitizeString($request->logradouro));
			$config->numero = strtoupper($this->sanitizeString($request->numero));
			$config->bairro = strtoupper($this->sanitizeString($request->bairro));
			$config->cep = $request->cep;
			$config->municipio = strtoupper($this->sanitizeString($municipio));
			$config->codMun = $codMun;
			$config->UF = $uf;
			$config->fone = $request->fone;
			$config->email = $request->email;

			$config->CST_CSOSN_padrao = $request->CST_CSOSN_padrao;
			$config->CST_COFINS_padrao = $request->CST_COFINS_padrao;
			$config->CST_PIS_padrao = $request->CST_PIS_padrao;
			$config->CST_IPI_padrao = $request->CST_IPI_padrao;
			
			$config->frete_padrao = $request->frete_padrao;
			$config->tipo_pagamento_padrao = $request->tipo_pagamento_padrao;
			$config->nat_op_padrao = $request->nat_op_padrao ?? 0;
			$config->percentual_lucro_padrao = $request->percentual_lucro_padrao ?? 0;
			$config->ambiente = $request->ambiente;
			$config->cUF = $cUF;
			$config->ultimo_numero_nfe = $request->ultimo_numero_nfe;
			$config->ultimo_numero_nfce = $request->ultimo_numero_nfce; 
			$config->ultimo_numero_cte = $request->ultimo_numero_cte;
			$config->ultimo_numero_mdfe = $request->ultimo_numero_mdfe;
			$config->numero_serie_nfe = $request->numero_serie_nfe;
			$config->numero_serie_nfce = $request->numero_serie_nfce;
			$config->numero_serie_cte = $request->numero_serie_cte;
			$config->csc = $request->csc;
			$config->csc_id = $request->csc_id;
			$config->campo_obs_nfe = $request->campo_obs_nfe ?? '';
			if($request->senha_remover != ""){
				$config->senha_remover = md5($request->senha_remover);
			}
			$config->casas_decimais = $request->casas_decimais;
			$config->certificado_a3 = $request->certificado_a3 ? true : false;

			$config->inscricao_municipal = $request->inscricao_municipal;
			$config->aut_xml = $request->aut_xml;
			if($request->hasFile('file')){
				$config->logo = $nomeImagem;
			}


			$result = $config->save();
		}

		$value = session('user_logged');

		$value['ambiente'] = $request->ambiente == 1 ? 'Produção' : 'Homologação';

		session()->put('user_logged', $value);

		if($result){
			session()->flash("mensagem_sucesso", "Configurado com sucesso!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao configurar!');
		}

		return redirect('/configNF');
	}


	private function _validate(Request $request){
		$rules = [
			'razao_social' => 'required|max:60',
			'nome_fantasia' => 'required|max:60',
			'cnpj' => 'required',
			'ie' => 'required',
			'logradouro' => 'required|max:80',
			'numero' => 'required|max:10',
			'bairro' => 'required|max:50',
			'fone' => 'required|max:20',
			'email' => 'required|email|max:60',
			'cep' => 'required',
			// 'municipio' => 'required',
			// 'codMun' => 'required',
			// 'uf' => 'required|max:2|min:2',
			'ultimo_numero_nfe' => 'required',
			'ultimo_numero_nfce' => 'required',
			'ultimo_numero_cte' => 'required',
			'ultimo_numero_mdfe' => 'required',
			'numero_serie_nfe' => 'required|max:3',
			'numero_serie_nfce' => 'required|max:3',
			'numero_serie_cte' => 'required|max:3',
			'csc' => 'required',
			'csc_id' => 'required',
			'file' => 'max:2000',
		];

		$messages = [
			'razao_social.required' => 'O Razão social nome é obrigatório.',
			'razao_social.max' => '60 caracteres maximos permitidos.',
			'nome_fantasia.required' => 'O campo Nome Fantasia é obrigatório.',
			'nome_fantasia.max' => '60 caracteres maximos permitidos.',
			'cnpj.required' => 'O campo CNPJ é obrigatório.',
			'logradouro.required' => 'O campo Logradouro é obrigatório.',
			'ie.required' => 'O campo Inscrição Estadual é obrigatório.',
			'logradouro.max' => '80 caracteres maximos permitidos.',
			'numero.required' => 'O campo Numero é obrigatório.',
			'cep.required' => 'O campo CEP é obrigatório.',
			'municipio.required' => 'O campo Municipio é obrigatório.',
			'numero.max' => '10 caracteres maximos permitidos.',
			'bairro.required' => 'O campo Bairro é obrigatório.',
			'bairro.max' => '50 caracteres maximos permitidos.',
			'fone.required' => 'O campo Telefone é obrigatório.',
			'fone.max' => '20 caracteres maximos permitidos.',

			'uf.required' => 'O campo UF é obrigatório.',
			'uf.max' => 'UF inválida.',
			'uf.min' => 'UF inválida.',

			'pais.required' => 'O campo Pais é obrigatório.',
			'codPais.required' => 'O campo Código do Pais é obrigatório.',
			'codMun.required' => 'O campo Código do Municipio é obrigatório.',
			'rntrc.max' => '12 caracteres maximos permitidos.',
			'ultimo_numero_nfe.required' => 'Campo obrigatório.',
			'ultimo_numero_nfe.required' => 'Campo obrigatório.',
			'ultimo_numero_nfce.required' => 'Campo obrigatório.',
			'ultimo_numero_cte.required' => 'Campo obrigatório.',
			'ultimo_numero_mdfe.required' => 'Campo obrigatório.',
			'numero_serie_nfe.required' => 'Campo obrigatório.',
			'numero_serie_nfe.max' => 'Maximo de 3 Digitos.',
			'numero_serie_nfce.required' => 'Campo obrigatório.',
			'numero_serie_nfce.max' => 'Maximo de 3 Digitos.',
			'numero_serie_cte.required' => 'Campo obrigatório.',
			'numero_serie_cte.max' => 'Maximo de 3 Digitos.',
			'csc.required' => 'O CSC é obrigatório.',
			'csc_id.required' => 'O CSCID é obrigatório.',
			'file.max' => 'Upload de até 2000KB.',

			'email.required' => 'Campo obrigatório.',
			'email.max' => 'Máximo de 60caracteres.',
			'email.email' => 'Email inválido.',

		];

		$this->validate($request, $rules, $messages);
	}

	public function certificado(){
		return view('configNota/upload')
		->with('title', 'Upload de Certificado');
	}

	public function download(){
		$certificado = Certificado::
		where('empresa_id', $this->empresa_id)
		->first();
		// echo "Senha: " . $certificado->senha;
		try{
			file_put_contents(public_path('cd.bin'), $certificado->arquivo);
			return response()->download(public_path('cd.bin'));
		}catch(\Exception $e){
			echo $e->getMessage();
		}

	}

	public function senha(){
		$certificado = Certificado::
		where('empresa_id', $this->empresa_id)
		->first();
		echo "Senha: " . $certificado->senha;

	}
	
	public function saveCertificado(Request $request){

		if($request->hasFile('file') && strlen($request->senha) > 0){
			$file = $request->file('file');
			$temp = file_get_contents($file);

			$extensao = $file->getClientOriginalExtension();

			$config = ConfigNota::
			where('empresa_id', $request->empresa_id)
			->first();

			$cnpj = str_replace(" ", "", $config->cnpj);
			$cnpj = str_replace(".", "", $cnpj);
			$cnpj = str_replace("-", "", $cnpj);
			$cnpj = str_replace("/", "", $cnpj);
			$fileName = "$cnpj.$extensao";
			
			$res = Certificado::create([
				'senha' => $request->senha,
				'arquivo' => $temp,
				'empresa_id' => $request->empresa_id
			]);

			if(getenv("CERTIFICADO_ARQUIVO") == 1){
				$file->move(public_path('certificados'), $fileName);
			}

			if($res){
				session()->flash("mensagem_sucesso", "Upload de certificado realizado!");
				return redirect('/configNF');
				
			}
		}else{
			session()->flash("mensagem_erro", "Envie o arquivo e senha por favor!");
			return redirect('/configNF/certificado');
		}
	}

	public function deleteCertificado(){
		Certificado::
		where('empresa_id', $this->empresa_id)
		->delete();
		session()->flash("mensagem_sucesso", "Certificado Removido!");
		return redirect('configNF');
	}

	public function teste(){
		try{
			$config = ConfigNota::
			where('empresa_id', $this->empresa_id)
			->first();

			$cnpj = str_replace(".", "", $config->cnpj);
			$cnpj = str_replace("/", "", $cnpj);
			$cnpj = str_replace("-", "", $cnpj);
			$cnpj = str_replace(" ", "", $cnpj);

			$nfe_service = new NFService([
				"atualizacao" => date('Y-m-d h:i:s'),
				"tpAmb" => (int)$config->ambiente,
				"razaosocial" => $config->razao_social,
				"siglaUF" => $config->UF,
				"cnpj" => $cnpj,
				"schemes" => "PL_009_V4",
				"versao" => "4.00",
				"tokenIBPT" => "AAAAAAA",
				"CSC" => $config->csc,
				"CSCid" => $config->csc_id
			]);

			$uf = $config->UF;
			$res = $nfe_service->consultaCadastro($cnpj, $uf);
			if($res['erro'] == false){
				return response()->json($res['json'], 200);
			}else{
				return response()->json($res['json'], 401);
			}
		}catch (\Exception $e) {
			return response()->json($e->getMessage(), 401);
		}

	}

	public function testeEmail(){

		$mailDriver = getenv("MAIL_HOST");
		$mailHost = getenv("MAIL_DRIVER");
		$mailPort = getenv("MAIL_PORT");
		$mailUsername = getenv("MAIL_USERNAME");
		$mailPass = getenv("MAIL_PASSWORD");
		$mailCpt = getenv("MAIL_ENCRYPTION");
		$mailName = getenv("MAIL_NAME");

		if($mailDriver == '') return response()->json("Configure no .env MAIL_HOST", 403);
		if($mailHost == '') return response()->json("Configure no .env MAIL_DRIVER", 403);
		if($mailPort == '') return response()->json("Configure no .env MAIL_PORT", 403);
		if($mailUsername == '') return response()->json("Configure no .env MAIL_USERNAME", 403);
		if($mailPass == '') return response()->json("Configure no .env MAIL_PASSWORD", 403);
		if($mailCpt == '') return response()->json("Configure no .env MAIL_ENCRYPTION", 403);
		if($mailName == '') return response()->json("Configure no .env MAIL_NAME", 403);

		try{
			Mail::send('mail.teste', [], function($m){
				$nomeEmail = getenv("MAIL_NAME");
				$mail = getenv("MAIL_USERNAME");
				$nomeEmail = str_replace("_", " ", $nomeEmail);
				$m->from(getenv('MAIL_USERNAME'), $nomeEmail);
				$m->subject('Teste de email');
				$m->to($mail);
			});
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 403);
		}

	}

	public function removeLogo($id){
		$config = ConfigNota::find($id);

		$config->logo = '';
		$config->save();
		session()->flash("mensagem_sucesso", "Logo removida!");
		return redirect('/configNF');
	}

	public function verificaSenha(Request $request){
		
		$config = ConfigNota::
		where('senha_remover', md5($request->senha))
		->where('empresa_id', $this->empresa_id)
		->first();

		if($config != null){
			return response()->json("ok", 200);
		}else{
			return response()->json("", 401);
		}
	}
	
}
