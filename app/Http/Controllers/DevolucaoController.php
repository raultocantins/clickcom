<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devolucao;
use App\Models\ItemDevolucao;
use App\Models\Fornecedor;
use App\Models\Cidade;
use App\Models\Produto;
use App\Models\Tributacao;
use App\Models\Transportadora;
use App\Models\NaturezaOperacao;
use App\Models\ConfigNota;
use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\NFe\Daevento;
use App\Services\DevolucaoService;
use App\Helpers\StockMove;

class DevolucaoController extends Controller
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

	public function index(){
		$devolucoes = Devolucao::
		where('empresa_id', $this->empresa_id)
		->orderBy('id', 'desc')
		->paginate(20);

		return view('devolucao/list')
		->with('devolucoes', $devolucoes)
		->with('devolucaoNF', true)
		->with('links', true)
		->with('title', 'Lista de Devoluções');
	}

	public function new(){
		return view('devolucao/new')
		->with('title', 'Nova Devolução');
	}

	private function validaChave($chave){
		$chave = substr($chave, 3, 44);
		$cp = Devolucao::
		where('empresa_id', $this->empresa_id)
		->where('chave_nf_entrada', $chave)
		->where('estado', 1)
		->first();
		return $cp == null ? true : false;
	}

	public function renderizarXml(Request $request){
		if ($request->hasFile('file')){
			$arquivo = $request->hasFile('file');
			$xml = simplexml_load_file($request->file);

			if(!isset($xml->NFe->infNFe)){
				session()->flash('mensagem_erro', 'Este xml não é uma NFe');
				return redirect("/devolucao/nova");
			}
			if(!$this->validaChave($xml->NFe->infNFe->attributes()->Id)){
				session()->flash('mensagem_erro', 'Este XML de devolução já esta incluido no sistema com estado aprovado!');
				// return redirect("/devolucao/nova");
			}

			$cidade = Cidade::getCidadeCod($xml->NFe->infNFe->emit->enderEmit->cMun);
			$dadosEmitente = [
				'cpf' => $xml->NFe->infNFe->emit->CPF,
				'cnpj' => $xml->NFe->infNFe->emit->CNPJ,  				
				'razaoSocial' => $xml->NFe->infNFe->emit->xNome, 				
				'nomeFantasia' => $xml->NFe->infNFe->emit->xFant,
				'logradouro' => $xml->NFe->infNFe->emit->enderEmit->xLgr,
				'numero' => $xml->NFe->infNFe->emit->enderEmit->nro,
				'bairro' => $xml->NFe->infNFe->emit->enderEmit->xBairro,
				'cep' => $xml->NFe->infNFe->emit->enderEmit->CEP,
				'fone' => $xml->NFe->infNFe->emit->enderEmit->fone,
				'ie' => $xml->NFe->infNFe->emit->IE,
				'cidade_id' => $cidade->id
			];

			$transportadora = null;
			$transportadoraDoc = null;

			if($xml->NFe->infNFe->transp->transporta){
				$transp = $xml->NFe->infNFe->transp->transporta;
				$transportadoraDoc = (int)$transp->CNPJ;

				$vol = $xml->NFe->infNFe->transp->vol;
				$modFrete = $xml->NFe->infNFe->transp->modFrete;

				$transportadora = [
					'transportadora_nome' => (string)$transp->xNome,
					'transportadora_cidade' => (string)$transp->xMun,
					'transportadora_uf' => (string)$transp->UF,
					'transportadora_cpf_cnpj' => (string)$transp->CNPJ,
					'transportadora_ie' => (int)$transp->IE,
					'transportadora_endereco' => (string)$transp->xEnder,
					'frete_quantidade' => (float)$vol->qVol,
					'frete_especie' => (string)$vol->esp,
					'frete_marca' => '',
					'frete_numero' => 0,
					'frete_tipo' => (int)$modFrete,
					'veiculo_placa' => '',
					'veiculo_uf' => '',
					'frete_peso_bruto' => (float)$vol->pesoB, 
					'frete_peso_liquido' => (float)$vol->pesoL,
					'despesa_acessorias' => (float)$xml->NFe->infNFe->total->ICMSTot->vOutro
				];

					// print_r($transportadora);
					// die;

			}

			$vFrete = number_format((double) $xml->NFe->infNFe->total->ICMSTot->vFrete, 
				2, ",", ".");

			$vDesc = number_format((double) $xml->NFe->infNFe->total->ICMSTot->vDesc, 2, ",", ".");

			$idFornecedor = 0;
			$fornecedorEncontrado = $this->verificaFornecedor($dadosEmitente['cnpj']);
			$dadosAtualizados = [];
			if($fornecedorEncontrado){
				$idFornecedor = $fornecedorEncontrado->id;
				$dadosAtualizados = $this->verificaAtualizacao($fornecedorEncontrado, $dadosEmitente);
			}else{

				array_push($dadosAtualizados, "Fornecedor cadastrado com sucesso");
				$idFornecedor = $this->cadastrarFornecedor($dadosEmitente);
			}

			$idTransportadora = 0;

			if($transportadoraDoc != null){

				$transportadoraEncontrada = $this->verificaTransportadora($transportadoraDoc);

				if($transportadoraEncontrada){
					$idTransportadora = $transportadoraEncontrada->id;
				}else{
					array_push($dadosAtualizados, 
						"Transportadora cadastrada com sucesso");
					$idTransportadora = $this->cadastrarTransportadora($transportadora);
				}
			}


			$seq = 0;
			$itens = [];
			$contSemRegistro = 0;

			$config = ConfigNota::
			where('empresa_id', $this->empresa_id)
			->first();

			$tributacao = Tributacao::
			where('empresa_id', $this->empresa_id)
			->first();

			foreach($xml->NFe->infNFe->det as $item) {
					// var_dump($item);
					// $item = [
					// 	'codigo' => $item->prod->cProd,
					// 	'xProd' => $item->prod->xProd,
					// 	'NCM' => $item->prod->NCM,
					// 	'CFOP' => $item->prod->CFOP,
					// 	'uCom' => $item->prod->uCom,
					// 	'vUnCom' => $item->prod->vUnCom,
					// 	'qCom' => $item->prod->qCom,
					// 	'codBarras' => $item->prod->cEAN,
					// 	'cst_csosn' => $config->CST_CSOSN_padrao,
					// 	'cst_pis' => $config->CST_PIS_padrao,
					// 	'cst_cofins' => $config->CST_COFINS_padrao,
					// 	'cst_ipi' => $config->CST_IPI_padrao,
					// 	'perc_icms' => $tributacao->icms,
					// 	'perc_pis' => $tributacao->pis,
					// 	'perc_cofins' => $tributacao->cofins,
					// 	'perc_ipi' => $tributacao->ipi

					// ];

				$trib = Devolucao::getTrib($item->imposto);
				$item = [
					'codigo' => $item->prod->cProd,
					'xProd' => $item->prod->xProd,
					'NCM' => $item->prod->NCM,
					'vFrete' => $item->prod->vFrete,
					'CFOP' => $item->prod->CFOP,
					'uCom' => $item->prod->uCom,
					'vUnCom' => $item->prod->vUnCom,
					'qCom' => $item->prod->qCom,
					'codBarras' => $item->prod->cEAN ?? '',
					'cst_csosn' => $trib['cst_csosn'],
					'cst_pis' => $trib['cst_pis'],
					'cst_cofins' => $trib['cst_cofins'],
					'cst_ipi' => $trib['cst_ipi'],
					'perc_icms' => $trib['pICMS'],
					'perc_pis' => $trib['pPIS'],
					'perc_cofins' => $trib['pCOFINS'],
					'perc_ipi' => $trib['pIPI'],
					'pRedBC' => $trib['pRedBC'],
					'modBCST' => $trib['modBCST'],
					'vBCST' => $trib['vBCST'],
					'pICMSST' => $trib['pICMSST'],
					'vICMSST' => $trib['vICMSST'],
					'pMVAST' => $trib['pMVAST'],
				];
				
				array_push($itens, $item);
			}

			$chave = substr($xml->NFe->infNFe->attributes()->Id, 3, 44);
			$dadosNf = [
				'chave' => $chave,
				'vProd' => $xml->NFe->infNFe->total->ICMSTot->vProd,
				'indPag' => $xml->NFe->infNFe->ide->indPag,
				'nNf' => $xml->NFe->infNFe->ide->nNF,
				'vFrete' => $vFrete,
				'vDesc' => $vDesc,
			];


			//Pagamento
			$fatura = [];
			if (!empty($xml->NFe->infNFe->cobr->dup))
			{
				foreach($xml->NFe->infNFe->cobr->dup as $dup) {
					$titulo = $dup->nDup;
					$vencimento = $dup->dVenc;
					$vencimento = explode('-', $vencimento);
					$vencimento = $vencimento[2]."/".$vencimento[1]."/".$vencimento[0];
					$vlr_parcela = number_format((double) $dup->vDup, 2, ",", ".");	

					$parcela = [
						'numero' => $titulo,
						'vencimento' => $vencimento,
						'valor_parcela' => $vlr_parcela
					];
					array_push($fatura, $parcela);
				}
			}

			//upload
			$file = $request->file;
			$nameArchive = $chave . ".xml" ;

			$pathXml = $file->move(public_path('xml_devolucao_entrada'), $nameArchive);

            //fim upload

			$config = ConfigNota::
			where('empresa_id', $this->empresa_id)
			->first();

			$naturezas = NaturezaOperacao::
			where('empresa_id', $this->empresa_id)
			->get();

			$transportadoras = Transportadora::
			where('empresa_id', $this->empresa_id)
			->get();

			$tipoFrete = 0;
			if($transportadora != null){
				$tipoFrete = $transportadora['frete_tipo'];
			}

			return view('devolucao/visualizaNota')
			->with('title', 'Devolução')
			->with('itens', $itens)
			->with('fatura', $fatura)
			->with('tipoFrete', $tipoFrete)
			->with('devolucaoJs', true)
			->with('pathXml', $nameArchive)
			->with('idFornecedor', $idFornecedor)
			->with('dadosNf', $dadosNf)
			->with('naturezas', $naturezas)
			->with('config', $config)
			->with('transportadora', $transportadora)
			->with('dadosEmitente', $dadosEmitente)
			->with('transportadoras', $transportadoras)
			->with('idTransportadora', $idTransportadora)
			->with('dadosAtualizados', $dadosAtualizados);
			

		}else{

			session()->flash('mensagem_erro', 'XML inválido!');
			return redirect("/devolucao/nova");
		}
	}

	private function verificaFornecedor($cnpj){
		$forn = Fornecedor::verificaCadastrado($this->formataCnpj($cnpj));
		return $forn;
	}

	private function verificaTransportadora($cnpj){
		$transp = Transportadora::verificaCadastrado($cnpj);
		return $transp;
	}

	private function cadastrarFornecedor($fornecedor){
		$result = Fornecedor::create([
			'razao_social' => $fornecedor['razaoSocial'],
			'nome_fantasia' => $fornecedor['nomeFantasia'],
			'rua' => $fornecedor['logradouro'],
			'numero' => $fornecedor['numero'],
			'bairro' => $fornecedor['bairro'],
			'cep' => $this->formataCep($fornecedor['cep']),
			'cpf_cnpj' => $this->formataCnpj($fornecedor['cnpj']),
			'ie_rg' => $fornecedor['ie'],
			'celular' => '*',
			'telefone' => $this->formataTelefone($fornecedor['fone']),
			'email' => '*',
			'cidade_id' => $fornecedor['cidade_id'],
			'empresa_id' => $this->empresa_id
		]);
		return $result->id;
	}

	private function cadastrarTransportadora($transp){
		
		$cidade = Cidade::
		where('nome', $transp['transportadora_cidade'])
		->first();

		if($cidade == null){
			$cidade = Cidade::
			where('uf', $transp['transportadora_uf'])
			->first();
		}

		$result = Transportadora::create([
			'razao_social' => $transp['transportadora_nome'],
			'cnpj_cpf' => $transp['transportadora_cpf_cnpj'],
			'logradouro' => $transp['transportadora_endereco'],
			'cidade_id' => $cidade->id,
			'empresa_id' => $this->empresa_id
		]);

		return $result->id;
	}

	private function formataCnpj($cnpj){
		$temp = substr($cnpj, 0, 2);
		$temp .= ".".substr($cnpj, 2, 3);
		$temp .= ".".substr($cnpj, 5, 3);
		$temp .= "/".substr($cnpj, 8, 4);
		$temp .= "-".substr($cnpj, 12, 2);
		return $temp;
	}

	private function formataCep($cep){
		$temp = substr($cep, 0, 5);
		$temp .= "-".substr($cep, 5, 3);
		return $temp;
	}

	private function formataTelefone($fone){
		$temp = substr($fone, 0, 2);
		$temp .= " ".substr($fone, 2, 4);
		$temp .= "-".substr($fone, 4, 4);
		return $temp;
	}

	private function verificaAtualizacao($fornecedorEncontrado, $dadosEmitente){
		$dadosAtualizados = [];

		$verifica = $this->dadosAtualizados('Razao Social', $fornecedorEncontrado->razao_social,
			$dadosEmitente['razaoSocial']);
		if($verifica) array_push($dadosAtualizados, $verifica);

		$verifica = $this->dadosAtualizados('Nome Fantasia', $fornecedorEncontrado->nome_fantasia,
			$dadosEmitente['nomeFantasia']);
		if($verifica) array_push($dadosAtualizados, $verifica);

		$verifica = $this->dadosAtualizados('Rua', $fornecedorEncontrado->rua,
			$dadosEmitente['logradouro']);
		if($verifica) array_push($dadosAtualizados, $verifica);

		$verifica = $this->dadosAtualizados('Numero', $fornecedorEncontrado->numero,
			$dadosEmitente['numero']);
		if($verifica) array_push($dadosAtualizados, $verifica);

		$verifica = $this->dadosAtualizados('Bairro', $fornecedorEncontrado->bairro,
			$dadosEmitente['bairro']);
		if($verifica) array_push($dadosAtualizados, $verifica);

		$verifica = $this->dadosAtualizados('IE', $fornecedorEncontrado->ie_rg,
			$dadosEmitente['ie']);
		if($verifica) array_push($dadosAtualizados, $verifica);

		$this->atualizar($fornecedorEncontrado, $dadosEmitente);
		return $dadosAtualizados;
	}

	private function dadosAtualizados($campo, $anterior, $atual){
		if($anterior != $atual){
			return $campo . " atualizado";
		} 
		return false;
	}

	private function atualizar($fornecedor, $dadosEmitente){
		$fornecedor->razao_social = $dadosEmitente['razaoSocial'];
		$fornecedor->nome_fantasia = $dadosEmitente['nomeFantasia'];
		$fornecedor->rua = $dadosEmitente['logradouro'];
		$fornecedor->ie_rg = $dadosEmitente['ie'];
		$fornecedor->bairro = $dadosEmitente['bairro'];
		$fornecedor->numero = $dadosEmitente['numero'];
		$fornecedor->save();
	}

	public function salvar(Request $request){
		$data = $request->data;
		$transportadora = $data['transportadora'];

		$devolucao = Devolucao::create([
			'fornecedor_id' => $data['fornecedorId'],
			'usuario_id' => get_id_user(),
			'natureza_id' => $data['natureza'],
			'valor_integral' => str_replace(",", ".", $data['valor_integral']),
			'valor_devolvido' => str_replace(",", ".", $data['valor_devolvido']),
			'motivo' => $data['motivo'] ?? '',
			'observacao' => $data['obs'] ?? '',
			'estado' => 0,
			'devolucao_parcial' => $data['devolucao_parcial'] == true ? 1 : 0,
			'chave_nf_entrada' => $data['xmlEntrada'],
			'nNf' => $data['nNf'],
			'vFrete' => str_replace(",", ".", $data['vFrete']),
			'vDesc' => str_replace(",", ".", $data['vDesc']),
			'chave_gerada' => '',
			'numero_gerado' => 0,
			'tipo' => $data['tipo'],
			'empresa_id' => $this->empresa_id,
			'transportadora_nome' => $transportadora['transportadora_nome'] ?? '',
			'transportadora_cidade' => $transportadora['transportadora_cidade'] ?? '',
			'transportadora_uf' => $transportadora['transportadora_uf'] ?? '',
			'transportadora_cpf_cnpj' => $transportadora['transportadora_cpf_cnpj'] ?? '',
			'transportadora_ie' => $transportadora['transportadora_ie'] ?? '',
			'transportadora_endereco' => $transportadora['transportadora_endereco'] ?? '',
			'frete_quantidade' => $data['qtd'] ? __replace($data['qtd']) : 0,
			'frete_especie' => $data['especie'] ?? '',
			'frete_marca' => $transportadora['frete_marca'] ?? '',
			'frete_numero' => $transportadora['frete_numero'] ?? 0,
			'frete_tipo' => $data['tipoFrete'] ?? 0,
			'veiculo_placa' => $data['placa'] ?? '',
			'veiculo_uf' => $data['ufPlaca'] ?? '',
			'frete_peso_bruto' => $data['pBruto'] ? __replace($data['pBruto']) : 0, 
			'frete_peso_liquido' => $data['pLiquido'] ? __replace($data['pLiquido']) : 0,
			'despesa_acessorias' => $data['vOutros'] ? __replace($data['vOutros']) : 0,
			'transportadora_id' => $data['transportadora_id'] > 0 ? $data['transportadora_id'] : NULL
		]);

		//salvar itens
		$stockMove = new StockMove();
		foreach($data['itens'] as $i){
			$item = ItemDevolucao::create([
				'cod' => $i['codigo'],
				'nome' => $i['xProd'],
				'ncm' => $i['NCM'],
				'cfop' => $i['CFOP'],
				'valor_unit' => $i['vUnCom'],
				'vFrete' => $i['vFrete'] ?? 0,
				'quantidade' => $i['qCom'],
				'item_parcial' => $i['parcial'],
				'unidade_medida' => $i['uCom'],
				'codBarras' => $i['codBarras'] ?? '',
				'devolucao_id' => $devolucao->id,
				'cst_csosn' => $i['cst_csosn'],
				'cst_pis' => $i['cst_pis'],
				'cst_cofins' => $i['cst_cofins'],
				'cst_ipi' => $i['cst_ipi'] ?? '99',
				'perc_icms' => $i['perc_icms'],
				'perc_pis' => $i['perc_pis'],
				'perc_cofins' => $i['perc_cofins'],
				'perc_ipi' => $i['perc_ipi'],
				'pRedBC' => $i['pRedBC'] ?? 0,
				'modBCST' => $i['modBCST'] ?? 0,
				'vBCST' => $i['vBCST'] ?? 0,
				'pICMSST' => $i['pICMSST'] ?? 0,
				'vICMSST' => $i['vICMSST'] ?? 0,
				'pMVAST' => $i['pMVAST'] ?? 0
			]);

			if(getenv("DEVOLUCAO_ALTERA_ESTOQUE") == 1){
				$produto = Produto::where('nome', $i['xProd'])->first();
				if($produto != null){
					$stockMove->downStock(
						(int) $produto->id, (float) str_replace(",", ".", $i['qCom']));
				}
			}
		}

		echo json_encode($data['itens']);
	}

	public function ver($id){
		$devolucao = Devolucao::
		where('id', $id)
		->first();

		if(valida_objeto($devolucao)){
		// $xml = file_get_contents('xml_devolucao/'.$devolucao->chave_gerada.'.xml');
			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';

			$xml = simplexml_load_file($public.'xml_devolucao/'.$devolucao->chave_gerada.'.xml');

			$cidade = Cidade::getCidadeCod($xml->NFe->infNFe->emit->enderEmit->cMun);
			$dadosEmitente = [
				'cpf' => $xml->NFe->infNFe->emit->CPF,
				'cnpj' => $xml->NFe->infNFe->emit->CNPJ,  				
				'razaoSocial' => $xml->NFe->infNFe->emit->xNome, 				
				'nomeFantasia' => $xml->NFe->infNFe->emit->xFant,
				'logradouro' => $xml->NFe->infNFe->emit->enderEmit->xLgr,
				'numero' => $xml->NFe->infNFe->emit->enderEmit->nro,
				'bairro' => $xml->NFe->infNFe->emit->enderEmit->xBairro,
				'cep' => $xml->NFe->infNFe->emit->enderEmit->CEP,
				'fone' => $xml->NFe->infNFe->emit->enderEmit->fone,
				'ie' => $xml->NFe->infNFe->emit->IE,
				'cidade_id' => $cidade->id
			];

			$vFrete = number_format((double) $xml->NFe->infNFe->total->ICMSTot->vFrete, 
				2, ",", ".");
			$vDesc = number_format((double) $xml->NFe->infNFe->total->ICMSTot->vDesc, 2, ",", ".");

			$chave = substr($xml->NFe->infNFe->attributes()->Id, 3, 44);
			$dadosNf = [
				'chave' => $chave,
				'vProd' => $xml->NFe->infNFe->total->ICMSTot->vProd,
				'indPag' => $xml->NFe->infNFe->ide->indPag,
				'nNf' => $xml->NFe->infNFe->ide->nNF,
				'vFrete' => $vFrete,
				'vDesc' => $vDesc,
			];

			return view('devolucao/ver')
			->with('dadosNf', $dadosNf)
			->with('dadosEmitente', $dadosEmitente)
			->with('devolucao', $devolucao)
			->with('title', 'Ver Devolução');
		}else{
			return redirect('/403');
		}

	}

	public function downloadXmlEntrada($id){
		$devolucao = Devolucao::
		where('id', $id)
		->first();
		if(valida_objeto($devolucao)){
			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
			return response()->download($public.'xml_devolucao_entrada/'.$devolucao->chave_nf_entrada.'.xml');
		}else{
			return redirect('/403');
		}

	}

	public function downloadXmlDevolucao($id){
		$devolucao = Devolucao::
		where('id', $id)
		->first();
		if(valida_objeto($devolucao)){
			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
			return response()->download($public . 'xml_devolucao/'.$devolucao->chave_gerada.'.xml');
		}else{
			return redirect('/403');
		}

	}

	public function delete($id){
		$devolucao = Devolucao::
		where('id', $id)
		->first();
		if(valida_objeto($devolucao)){
			$stockMove = new StockMove();

			foreach($devolucao->itens as $i){

				if(getenv("DEVOLUCAO_ALTERA_ESTOQUE") == 1){
					$produto = Produto::where('nome', $i->nome)->first();
					if($produto != null){
						$stockMove->pluStock(
							(int) $produto->id, (float) str_replace(",", ".", $i->quantidade));
					}
				}
			}

			$devolucao->delete();

			session()->flash("mensagem_sucesso", "Deletado com sucesso!");
			return redirect('/devolucao');
		}else{
			return redirect('/403');
		}
	}

	public function imprimir($id){
		$devolucao = Devolucao::
		where('id', $id)
		->first();
		if(valida_objeto($devolucao)){
			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
			if($devolucao->estado == 1){
				if(file_exists($public .'xml_devolucao/'.$devolucao->chave_gerada.'.xml')){
					$xml = file_get_contents($public .'xml_devolucao/'.$devolucao->chave_gerada.'.xml');

					$config = ConfigNota::
					where('empresa_id', $this->empresa_id)
					->first();

					if($config->logo){
						$logo = 'data://text/plain;base64,'. base64_encode(file_get_contents($public.'logos/' . $config->logo));
					}else{
						$logo = null;
					}

					try {
						$danfe = new Danfe($xml);
						// $id = $danfe->monta($logo);
						$pdf = $danfe->render($logo);
						header('Content-Type: application/pdf');

						return response($pdf)
						->header('Content-Type', 'application/pdf');
					} catch (InvalidArgumentException $e) {
						echo "Ocorreu um erro durante o processamento :" . $e->getMessage();
					}  
				}else{
					echo "arquivo XML não encontrado!!";
				}
			}else if($devolucao->estado == 3){
				$xml = file_get_contents($public .'xml_devolucao_cancelada/'.$devolucao->chave_gerada.'.xml');

				$logo = 'data://text/plain;base64,'. base64_encode(file_get_contents($public .'imgs/logo.jpg'));

				$dadosEmitente = $this->getEmitente();
				try {
					$danfe = new Daevento($xml, $dadosEmitente);
					// $id = $danfe->monta($logo);
					$pdf = $danfe->render($logo);
					header('Content-Type: application/pdf');

					return response($pdf)
					->header('Content-Type', 'application/pdf');
				} catch (InvalidArgumentException $e) {
					echo "Ocorreu um erro durante o processamento :" . $e->getMessage();
				} 
			}
		}else{
			return redirect('/403');
		}
	}

	private function getEmitente(){
		$config = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();
		return [
			'razao' => $config->razao_social,
			'logradouro' => $config->logradouro,
			'numero' => $config->numero,
			'complemento' => '',
			'bairro' => $config->bairro,
			'CEP' => $config->cep,
			'municipio' => $config->municipio,
			'UF' => $config->UF,
			'telefone' => $config->telefone,
			'email' => ''
		];
	}


	//envio sefaz

	public function enviarSefaz(Request $request){
		$devolucao = Devolucao::
		where('id', $request->devolucao_id)
		->where('empresa_id', $this->empresa_id)
		->first();

		$config = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();

		$cnpj = str_replace(".", "", $config->cnpj);
		$cnpj = str_replace("/", "", $cnpj);
		$cnpj = str_replace("-", "", $cnpj);
		$cnpj = str_replace(" ", "", $cnpj);

		$nfe_dev = new DevolucaoService([
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
		], 55);

		if($devolucao->estado == 0 || $devolucao->estado == 2){
			header('Content-type: text/html; charset=UTF-8');

			$dev = $nfe_dev->gerarDevolucao($devolucao);
			if(!isset($dev['erros_xml'])){
			// file_put_contents('xml/teste2.xml', $nfe['xml']);

				$signed = $nfe_dev->sign($dev['xml']);
				$resultado = $nfe_dev->transmitir($signed, $dev['chave']);

				if(substr($resultado, 0, 4) != 'Erro'){
					$devolucao->chave_gerada = $dev['chave'];
					$devolucao->estado = 1;

					$devolucao->numero_gerado = $dev['nNf'];
					$devolucao->save();
				}else{
					$devolucao->estado = 2;
					$devolucao->save();
				}
				echo json_encode($resultado);
			}else{
				return response()->json($dev['erros_xml'][0], 401);

			}

		}else{
			echo json_encode(false);
		}
	}

	public function consultar(Request $request){
		$config = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();

		$cnpj = str_replace(".", "", $config->cnpj);
		$cnpj = str_replace("/", "", $cnpj);
		$cnpj = str_replace("-", "", $cnpj);
		$cnpj = str_replace(" ", "", $cnpj);
		$nfe_dev = new DevolucaoService([
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
		], 55);

		$devolucao = Devolucao::find($request->id);
		$c = $nfe_dev->consultar($devolucao);
		echo json_encode($c);
	}

	public function cancelar(Request $request){
		$devolucao = Devolucao::
		where('id', $request->devolucao_id)
		->where('empresa_id', $this->empresa_id)
		->first();

		$config = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();

		$cnpj = str_replace(".", "", $config->cnpj);
		$cnpj = str_replace("/", "", $cnpj);
		$cnpj = str_replace("-", "", $cnpj);
		$cnpj = str_replace(" ", "", $cnpj);

		$nfe_dev = new DevolucaoService([
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
		], 55);


		$resultado = $nfe_dev->cancelar($devolucao, $request->justificativa);
		if($this->isJson($resultado)){
			
			$devolucao->estado = 3;
			$devolucao->save();
			return response()->json($resultado, 200);

		}
		
		return response()->json($resultado, 401);
	}

	private function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	public function filtro(Request $request){
		$dataInicial = $request->data_inicial;
		$dataFinal = $request->data_final;
		$fornecedor = $request->fornecedor;

		if($dataInicial && !$dataFinal || !$dataInicial && $dataFinal){
			session()->flash("mensagem_erro", "Informe as duas datas para filtrar, não somente uma!");
			return redirect('/devolucao');
		}

		$devolucoes = Devolucao::
		select('devolucaos.*');

		if($dataInicial && $dataFinal){
			$devolucoes->whereBetween('devolucaos.created_at', [
				$this->parseDate($dataInicial),
				$this->parseDate($dataFinal, true)
			]);
		}
		if($fornecedor){
			$devolucoes->join('fornecedors', 'fornecedors.id' , '=', 'devolucaos.fornecedor_id')
			->where('fornecedors.razao_social', 'LIKE', "%$fornecedor%");

		}

		$devolucoes = $devolucoes->where('devolucaos.empresa_id', $this->empresa_id)
		->get();

		return view('devolucao/list')
		->with('devolucoes', $devolucoes)
		->with('devolucaoNF', true)
		->with('title', 'Lista de Devoluções');

	}

	private function parseDate($date, $plusDay = false){
		if($plusDay == false)
			return date('Y-m-d', strtotime(str_replace("/", "-", $date)));
		else
			return date('Y-m-d', strtotime("+1 day",strtotime(str_replace("/", "-", $date))));
	}

	public function xmltemp($id){
		$devolucao = Devolucao::
		where('id', $id)
		->first();

		$config = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();

		if($config == null){
			session()->flash('mensagem_erro', 'Configure o emitente!!');
			return redirect('/configNF');

		}

		$cnpj = str_replace(".", "", $config->cnpj);
		$cnpj = str_replace("/", "", $cnpj);
		$cnpj = str_replace("-", "", $cnpj);
		$cnpj = str_replace(" ", "", $cnpj);

		$nfe_dev = new DevolucaoService([
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
		], 55);


		header('Content-type: text/html; charset=UTF-8');

		$dev = $nfe_dev->gerarDevolucao($devolucao);
		if(!isset($dev['erros_xml'])){

			return response($dev['xml'])
			->header('Content-Type', 'application/xml');
		}else{
			foreach($dev['erros_xml'] as $e){
				echo $e;
			}
		}

	}

	public function danfeTemp($id){
		$devolucao = Devolucao::
		where('id', $id)
		->first();

		$config = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();

		if($config == null){
			session()->flash('mensagem_erro', 'Configure o emitente!!');
			return redirect('/configNF');

		}

		$cnpj = str_replace(".", "", $config->cnpj);
		$cnpj = str_replace("/", "", $cnpj);
		$cnpj = str_replace("-", "", $cnpj);
		$cnpj = str_replace(" ", "", $cnpj);

		$nfe_dev = new DevolucaoService([
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
		], 55);


		header('Content-type: text/html; charset=UTF-8');

		$dev = $nfe_dev->gerarDevolucao($devolucao);
		if(!isset($dev['erros_xml'])){

			$config = ConfigNota::
			where('empresa_id', $this->empresa_id)
			->first();
			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
			
			if($config->logo){
				$logo = 'data://text/plain;base64,'. base64_encode(file_get_contents($public.'logos/' . $config->logo));
			}else{
				$logo = null;
			}

			try {
				$danfe = new Danfe($dev['xml']);
				// $id = $danfe->monta($logo);
				$pdf = $danfe->render($logo);
				header('Content-Type: application/pdf');

				return response($pdf)
				->header('Content-Type', 'application/pdf');
			} catch (InvalidArgumentException $e) {
				echo "Ocorreu um erro durante o processamento :" . $e->getMessage();
			}  


		}else{
			foreach($dev['erros_xml'] as $e){
				echo $e;
			}
		}
	}

	public function cartaCorrecao(Request $request){

		$config = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();

		$cnpj = str_replace(".", "", $config->cnpj);
		$cnpj = str_replace("/", "", $cnpj);
		$cnpj = str_replace("-", "", $cnpj);
		$cnpj = str_replace(" ", "", $cnpj);

		$devolucao = Devolucao::find($request->id);

		$nfe_dev = new DevolucaoService([
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
		], 55);

		$devolucao = $nfe_dev->cartaCorrecao($devolucao, $request->correcao);
		echo json_encode($devolucao);
	}

	public function imprimirCce($id){
		$devolucao = Devolucao::
		where('id', $id)
		->where('empresa_id', $this->empresa_id)
		->first();

		if($devolucao->sequencia_cce > 0){

			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
			if(file_exists($public.'xml_devolucao_correcao/'.$devolucao->chave_gerada.'.xml')){
				$xml = file_get_contents($public.'xml_devolucao_correcao/'.$devolucao->chave_gerada.'.xml');

				$config = ConfigNota::
				where('empresa_id', $this->empresa_id)
				->first();

				if($config->logo){
					$logo = 'data://text/plain;base64,'. base64_encode(file_get_contents($public.'logos/' . $config->logo));
				}else{
					$logo = null;
				}

				$dadosEmitente = $this->getEmitente();

				try {
					$daevento = new Daevento($xml, $dadosEmitente);
					$daevento->debugMode(true);
					$pdf = $daevento->render($logo);

					return response($pdf)
					->header('Content-Type', 'application/pdf');
				} catch (InvalidArgumentException $e) {
					echo "Ocorreu um erro durante o processamento :" . $e->getMessage();
				}  
			}else{
				echo "Arquivo XML não encontrado!!";
			}
		}else{
			echo "<center><h1>Este documento não possui evento de correção!<h1></center>";
		}
	}

	public function imprimirCancela($id){
		$devolucao = Devolucao::
		where('id', $id)
		->where('empresa_id', $this->empresa_id)
		->first();

		if($devolucao->estado == 3){

			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
			if(file_exists($public.'xml_devolucao_cancelada/'.$devolucao->chave_gerada.'.xml')){
				$xml = file_get_contents($public.'xml_devolucao_cancelada/'.$devolucao->chave_gerada.'.xml');

				$config = ConfigNota::
				where('empresa_id', $this->empresa_id)
				->first();

				if($config->logo){
					$logo = 'data://text/plain;base64,'. base64_encode(file_get_contents($public.'logos/' . $config->logo));
				}else{
					$logo = null;
				}

				$dadosEmitente = $this->getEmitente();

				try {
					$daevento = new Daevento($xml, $dadosEmitente);
					$daevento->debugMode(true);
					$pdf = $daevento->render($logo);

					return response($pdf)
					->header('Content-Type', 'application/pdf');
				} catch (InvalidArgumentException $e) {
					echo "Ocorreu um erro durante o processamento :" . $e->getMessage();
				}  
			}else{
				echo "Arquivo XML não encontrado!!";
			}
		}else{
			echo "<center><h1>Este documento não possui evento de correção!<h1></center>";
		}
	}
}
