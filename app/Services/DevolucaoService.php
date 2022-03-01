<?php

namespace App\Services;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use App\Models\Venda;
use App\Models\ConfigNota;
use App\Models\Certificado;
use NFePHP\NFe\Complements;
use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\Legacy\FilesFolders;
use NFePHP\Common\Soap\SoapCurl;
use App\Models\Tributacao;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

class DevolucaoService{

	private $config; 
	private $tools;
	protected $empresa_id = null;

	public function __construct($config, $modelo){
		//$json = json_encode($config);
		$value = session('user_logged');
		$this->empresa_id = $value['empresa'];
		$certificado = Certificado::
		where('empresa_id', $this->empresa_id)
		->first();
		// print_r($certificado->arquivo);
		$this->config = $config;
		$this->tools = new Tools(json_encode($config), Certificate::readPfx($certificado->arquivo, $certificado->senha));
		$this->tools->model($modelo);
		
	}

	private function validate_EAN13Barcode($ean)
	{

		$sumEvenIndexes = 0;
		$sumOddIndexes  = 0;

		$eanAsArray = array_map('intval', str_split($ean));

		if (!$this->has13Numbers($eanAsArray)) {
			return false;
		};

		for ($i = 0; $i < count($eanAsArray)-1; $i++) {
			if ($i % 2 === 0) {
				$sumOddIndexes  += $eanAsArray[$i];
			} else {
				$sumEvenIndexes += $eanAsArray[$i];
			}
		}

		$rest = ($sumOddIndexes + (3 * $sumEvenIndexes)) % 10;

		if ($rest !== 0) {
			$rest = 10 - $rest;
		}

		return $rest === $eanAsArray[12];
	}

	private function has13Numbers(array $ean)
	{
		return count($ean) === 13;
	}

	public function gerarDevolucao($devolucao){

		$config = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first(); // iniciando os dados do emitente NF

		$tributacao = Tributacao::
		where('empresa_id', $this->empresa_id)
		->first(); // iniciando tributos

		$nfe = new Make();
		$stdInNFe = new \stdClass();
		$stdInNFe->versao = '4.00'; 
		$stdInNFe->Id = null; 
		$stdInNFe->pk_nItem = ''; 

		$infNFe = $nfe->taginfNFe($stdInNFe);

		$vendaLast = Venda::lastNF();
		$lastNumero = $vendaLast > 0 ? $vendaLast : 0;
		
		$stdIde = new \stdClass();
		$stdIde->cUF = $config->cUF;
		$stdIde->cNF = rand(11111,99999);
		// $stdIde->natOp = $venda->natureza->natureza;
		$stdIde->natOp = $devolucao->natureza->natureza;

		// $stdIde->indPag = 1; //NÃO EXISTE MAIS NA VERSÃO 4.00 // forma de pagamento

		$stdIde->mod = 55;
		$stdIde->serie = $config->numero_serie_nfe;
		$stdIde->nNF = (int)$lastNumero+1;
		$stdIde->dhEmi = date("Y-m-d\TH:i:sP");
		$stdIde->dhSaiEnt = date("Y-m-d\TH:i:sP");
		$stdIde->tpNF = $devolucao->tipo;
		$stdIde->idDest = $config->UF != $devolucao->fornecedor->cidade->uf ? 2 : 1;
		$stdIde->cMunFG = $config->codMun;
		$stdIde->tpImp = 1;
		$stdIde->tpEmis = 1;
		$stdIde->cDV = 0;
		$stdIde->tpAmb = $config->ambiente;
		$stdIde->finNFe = 4; // 4 - devolucao
		$stdIde->indFinal = 1;
		$stdIde->indPres = 1;
		if($config->ambiente == 2){
			$stdIde->indIntermed = 0;
		}
		$stdIde->procEmi = '0';
		$stdIde->verProc = '3.10.31';
		// $stdIde->dhCont = null;
		// $stdIde->xJust = null;


		//
		$tagide = $nfe->tagide($stdIde);

		$stdEmit = new \stdClass();
		$stdEmit->xNome = $config->razao_social;
		$stdEmit->xFant = $config->nome_fantasia;

		$ie = str_replace(".", "", $config->ie);
		$ie = str_replace("/", "", $ie);
		$ie = str_replace("-", "", $ie);
		$stdEmit->IE = $ie;
		// $stdEmit->CRT = $tributacao->regime == 0 ? 1 : 3;
		// $stdEmit->CRT = ($tributacao->regime == 0 || $tributacao->regime == 2) ? 1 : 3;
		$regime = $devolucao->itens[0]->cst_csosn >= 101 ? 1 : 3;
		$stdEmit->CRT = $regime;


		$cnpj = str_replace(".", "", $config->cnpj);
		$cnpj = str_replace("/", "", $cnpj);
		$cnpj = str_replace("-", "", $cnpj);
		$cnpj = str_replace(" ", "", $cnpj);
		if(strlen($cnpj) == 14){
			$stdEmit->CNPJ = $cnpj;
		}else{
			$stdEmit->CPF = $cnpj;
		}

		$emit = $nfe->tagemit($stdEmit);

		// ENDERECO EMITENTE
		$stdEnderEmit = new \stdClass();
		$stdEnderEmit->xLgr = $config->logradouro;
		$stdEnderEmit->nro = $config->numero;
		$stdEnderEmit->xCpl = "";
		$stdEnderEmit->xBairro = $config->bairro;
		$stdEnderEmit->cMun = $config->codMun;
		$stdEnderEmit->xMun = $config->municipio;
		$stdEnderEmit->UF = $config->UF;

		$cep = str_replace("-", "", $config->cep);
		$cep = str_replace(".", "", $cep);
		$stdEnderEmit->CEP = $cep;
		$stdEnderEmit->cPais = $config->codPais;
		$stdEnderEmit->xPais = $config->pais;

		$enderEmit = $nfe->tagenderEmit($stdEnderEmit);

		// DESTINATARIO
		$stdDest = new \stdClass();
		$stdDest->xNome = $devolucao->fornecedor->razao_social;

		if($devolucao->fornecedor->ie_rg == 'ISENTO'){
			$stdDest->indIEDest = "2";
		}else{
			$stdDest->indIEDest = "1";
		}
		


		$cnpj_cpf = str_replace(".", "", $devolucao->fornecedor->cpf_cnpj);
		$cnpj_cpf = str_replace("/", "", $cnpj_cpf);
		$cnpj_cpf = str_replace("-", "", $cnpj_cpf);

		if(strlen($cnpj_cpf) == 14){
			$stdDest->CNPJ = $cnpj_cpf;
			$ie = str_replace(".", "", $devolucao->fornecedor->ie_rg);
			$ie = str_replace("/", "", $ie);
			$ie = str_replace("-", "", $ie);
			$stdDest->IE = $ie;
		}
		else{
			$stdDest->CPF = $cnpj_cpf;
		} 

		$dest = $nfe->tagdest($stdDest);

		$stdEnderDest = new \stdClass();
		$stdEnderDest->xLgr = $devolucao->fornecedor->rua;
		$stdEnderDest->nro = $devolucao->fornecedor->numero;
		$stdEnderDest->xCpl = "";
		$stdEnderDest->xBairro = $devolucao->fornecedor->bairro;
		$stdEnderDest->cMun = $devolucao->fornecedor->cidade->codigo;
		$stdEnderDest->xMun = strtoupper($this->retiraAcentos($devolucao->fornecedor->cidade->nome));
		$stdEnderDest->UF = $devolucao->fornecedor->cidade->uf;

		$cep = str_replace("-", "", $devolucao->fornecedor->cep);
		$cep = str_replace(".", "", $cep);
		$stdEnderDest->CEP = $cep;
		$stdEnderDest->cPais = "1058";
		$stdEnderDest->xPais = "BRASIL";

		$enderDest = $nfe->tagenderDest($stdEnderDest);

		$somaProdutos = 0;
		$somaICMS = 0;
		$somaIPI = 0;
		$somaST = 0;
		//PRODUTOS
		$itemCont = 0;

		$totalItens = count($devolucao->itens);
		$somaFrete = 0;
		$somaAcrescimo = 0;

		$std = new \stdClass();
		$std->refNFe = $devolucao->chave_nf_entrada;
		$nfe->tagrefNFe($std);
		
		$VBC = 0;
		foreach($devolucao->itens as $i){
			$itemCont++;

			$stdProd = new \stdClass();
			$stdProd->item = $itemCont;

			$cod = $this->validate_EAN13Barcode($i->codBarras);

			// $stdProd->cEAN = $i->codBarras;
			// $stdProd->cEANTrib = $i->codBarras;
			$stdProd->cEAN = $cod ? $i->codBarras : 'SEM GTIN';
			$stdProd->cEANTrib = $cod ? $i->codBarras : 'SEM GTIN';

			$stdProd->cProd = $i->cod;
			$stdProd->xProd = $i->nome;
			$ncm = $i->ncm;
			$ncm = str_replace(".", "", $ncm);
			$stdProd->NCM = $ncm;
			if($devolucao->tipo == 1){
				$stdProd->CFOP = $config->UF != $devolucao->fornecedor->cidade->uf ?
				$devolucao->natureza->CFOP_saida_inter_estadual : $devolucao->natureza->CFOP_saida_estadual;
			}else{
				$stdProd->CFOP = $config->UF != $devolucao->fornecedor->cidade->uf ?
				$devolucao->natureza->CFOP_entrada_inter_estadual : $devolucao->natureza->CFOP_entrada_estadual;
			}
			$stdProd->uCom = $i->unidade_medida;
			$stdProd->qCom = $i->quantidade;
			$stdProd->vUnCom = ($i->valor_unit);
			$stdProd->vProd = $this->format(($i->quantidade * $i->valor_unit));
			$stdProd->uTrib = $i->unidade_medida;
			$stdProd->qTrib = $i->quantidade;
			$stdProd->vUnTrib = ($i->valor_unit);
			if($i->vFrete > 0){
				$stdProd->vFrete = $this->format($i->vFrete);
			}

			$stdProd->indTot = 1;
			$somaProdutos += ($i->quantidade * $i->valor_unit);

			if($devolucao->despesa_acessorias > 0){
				if($itemCont < sizeof($devolucao->itens)){
					$totalVenda = $devolucao->valor_devolvido;

					$media = (((($stdProd->vProd-$totalVenda)/$totalVenda))*100);
					$media = 100 - ($media * -1);

					$tempAcrescimo = ($devolucao->despesa_acessorias*$media)/100;
					$somaAcrescimo+=$tempAcrescimo;
					if($tempAcrescimo > 0.1)
						$stdProd->vOutro = $this->format($tempAcrescimo);
				}else{
					if($devolucao->despesa_acessorias - $somaAcrescimo > 0.1)
						$stdProd->vOutro = $this->format($devolucao->despesa_acessorias - $somaAcrescimo);
				}
			}


			// if($devolucao->vDesc > 0){
			// 	$stdProd->vDesc = $this->format($devolucao->vDesc/$totalItens);
			// }

			// if($venda->frete){
			// 	if($venda->frete->valor > 0){
			// 		$somaFrete += $vFt = $venda->frete->valor/$totalItens;
			// 		$stdProd->vFrete = $this->format($vFt);
			// 	}
			// }

			$prod = $nfe->tagprod($stdProd);

		//TAG IMPOSTO

			$stdImposto = new \stdClass();
			$stdImposto->item = $itemCont;

			$imposto = $nfe->tagimposto($stdImposto);

			// ICMS
			if($regime == 3){ // regime normal

				//$venda->produto->CST
				$stdICMS = new \stdClass();
				$stdICMS->item = $itemCont; 
				$stdICMS->orig = 0;
				$stdICMS->CST = $i->cst_csosn;
				$stdICMS->modBC = 0;
				if($i->pRedBC == 0){

					if($i->cst_csosn == 60){
						$VBC += $stdICMS->vBC = 0;

						$stdICMS->pST = 0;
						$stdICMS->vBCSTRet = 0;
						$stdICMS->vICMSSubstituto = 0;
						$stdICMS->vICMSSTRet = 0;
						$stdICMS->vBCSTDest = 0;
						$stdICMS->vICMSSTDest = 0;
						$stdICMS->pRedBCEfet = 0;
						$stdICMS->vBCEfet = $stdProd->vProd;
						// $stdICMS->pICMSEfet = 17;
						// $stdICMS->vICMSEfet = 4088.50;
					}else{
						if($i->cst_csosn == 40 || $i->cst_csosn == 41){
							$stdICMS->vBCSTRet = 0;
							$stdICMS->vICMSSTRet = 0;
							$stdICMS->vBCSTDest = 0;
							$stdICMS->vICMSSTDest = 0;
						}
						$VBC += $stdICMS->vBC = $stdProd->vProd + $stdProd->vOutro + $i->vFrete;

					}

					if($config->UF != $devolucao->fornecedor->cidade->uf){
						$stdICMS->pST = 0;
						$stdICMS->vBCSTRet = 0;
						$stdICMS->vICMSSubstituto = 0;
						$stdICMS->vICMSSTRet = 0;
						$stdICMS->vBCSTDest = 0;
						$stdICMS->vICMSSTDest = 0;
						$stdICMS->pRedBCEfet = 0;
					}

					$stdICMS->pICMS = $this->format($i->perc_icms);
					$stdICMS->vICMS = $stdICMS->vBC * ($stdICMS->pICMS/100);
					$stdICMS->pRedBC = $this->format($i->pRedBC);


					if($i->modBCST > 0){
						$stdICMS->modBCST = (int)$i->modBCST;
					}
					if($i->vBCST > 0){
						$stdICMS->vBCST = $i->vBCST;
					}
					if($i->pICMSST > 0){
						$stdICMS->pICMSST = $i->pICMSST;
					}
					if($i->vICMSST > 0){
						$somaST += $stdICMS->vICMSST = $i->vICMSST;
					}

					if($stdICMS->CST == '41'){
						$ICMS = $nfe->tagICMSST($stdICMS);
					}else{
						$ICMS = $nfe->tagICMS($stdICMS);
					}
					
				}else{
					$tempB = 100-$i->pRedBC;
					$VBC += $stdICMS->vBC = ($stdProd->vProd * ($tempB/100));
					$stdICMS->pICMS = $this->format($i->perc_icms);
					$stdICMS->vICMS = ($stdProd->vProd * ($tempB/100)) * ($stdICMS->pICMS/100);
					$stdICMS->pRedBC = $this->format($i->pRedBC);
				}

				// $somaICMS += 0;
				// $ICMS = $nfe->tagICMS($stdICMS);
				$somaICMS += $stdICMS->vICMS;

			}else{ // regime simples

				//$venda->produto->CST CSOSN
				
				$stdICMS = new \stdClass();
				
				$stdICMS->item = $itemCont; 
				$stdICMS->orig = 0;
				$stdICMS->CSOSN = $i->cst_csosn;
				$stdICMS->pCredSN = $this->format($i->perc_icms);
				$stdICMS->vCredICMSSN = $this->format($i->perc_icms);
				$ICMS = $nfe->tagICMSSN($stdICMS);

				$somaICMS = 0;
			}

			
			$stdPIS = new \stdClass();//PIS
			$stdPIS->item = $itemCont; 
			$stdPIS->CST = $i->cst_pis;
			$stdPIS->vBC = $i->perc_pis > 0 ? ($stdProd->vProd + $stdProd->vOutro) : 0.00;
			$stdPIS->pPIS = $this->format($i->perc_pis);
			$stdPIS->vPIS = $this->format($stdPIS->vBC * 
				($i->perc_pis/100));
			$PIS = $nfe->tagPIS($stdPIS);


			$stdCOFINS = new \stdClass();//COFINS
			$stdCOFINS->item = $itemCont; 
			$stdCOFINS->CST = $i->cst_cofins;
			$stdCOFINS->vBC = $i->perc_cofins > 0 ? ($stdProd->vProd + $stdProd->vOutro) : 0.00;
			$stdCOFINS->pCOFINS = $this->format($i->perc_cofins);
			$stdCOFINS->vCOFINS = $this->format($stdCOFINS->vBC * 
				($i->perc_cofins/100));
			$COFINS = $nfe->tagCOFINS($stdCOFINS);

			
			if($i->perc_ipi > 0){
				$std = new \stdClass();
				$std->item = $itemCont; 
				$std->clEnq = null;
				$std->CNPJProd = null;
				$std->cSelo = null;
				$std->qSelo = null;
				$std->cEnq = '999'; 
				$std->CST = $i->cst_ipi;
				$std->vBC = $this->format($stdProd->vProd + $stdProd->vOutro + $i->vFrete);
				$std->pIPI = $this->format($i->perc_ipi);
				$somaIPI += $std->vIPI = $this->format($std->vBC * ($i->perc_ipi/100));
				$std->qUnid = null;
				$std->vUnid = null;

				$nfe->tagIPI($std);
			}

			// $stdComb = new \stdClass();
			// $stdComb->item = $itemCont; 
			// $stdComb->cProdANP = '820101013';
			// $stdComb->descANP = 'OLEO DIESEL B S500 ADITIVADO'; 
			// $stdComb->UFCons = 'MT'; 
			// $nfe->tagcomb($stdComb);

		}

		$stdICMSTot = new \stdClass();
		$stdICMSTot->vBC = $this->format($VBC);
		$stdICMSTot->vICMS = $this->format($somaICMS);
		$stdICMSTot->vICMSDeson = 0.00;
		$stdICMSTot->vBCST = 0.00;
		$stdICMSTot->vST = $this->format($somaST);
		// $stdICMSTot->vProd = $this->format($VBC-$devolucao->despesa_acessorias);

		$stdICMSTot->vFrete = 0.00;

		$stdICMSTot->vSeg = 0.00;
		$stdICMSTot->vDesc = 0.00;
		// $stdICMSTot->vDesc = $this->format($devolucao->vDesc);
		$stdICMSTot->vII = 0.00;
		$stdICMSTot->vIPI = 0.00;
		$stdICMSTot->vPIS = 0.00;
		$stdICMSTot->vCOFINS = 0.00;
		$stdICMSTot->vOutro = $this->format($devolucao->despesa_acessorias);
		
		if($devolucao->vFrete > 0){
			$stdICMSTot->vNF = 
			$this->format(($somaProdutos + $somaIPI + $devolucao->despesa_acessorias) + $devolucao->vFrete+$stdICMSTot->vST);
		} 
		else{
			$stdICMSTot->vNF = $this->format($somaProdutos - $devolucao->vDesc + $somaIPI+$stdICMSTot->vST);
		} 

		$stdICMSTot->vTotTrib = 0.00;
		$ICMSTot = $nfe->tagICMSTot($stdICMSTot);


		$stdTransp = new \stdClass();
		$stdTransp->modFrete = $devolucao->frete_tipo;

		$transp = $nfe->tagtransp($stdTransp);


		// if($devolucao->transportadora_nome != ''){
		// 	$std = new \stdClass();

		// 	$std->xNome = $devolucao->transportadora_nome;

		// 	$std->xEnder = $devolucao->transportadora_endereco;
		// 	$std->xMun = $devolucao->transportadora_cidade;
		// 	$std->UF = $devolucao->transportadora_uf;


		// 	$cnpj_cpf = $devolucao->transportadora_cpf_cnpj;
		// 	$cnpj_cpf = str_replace(".", "", $cnpj_cpf);
		// 	$cnpj_cpf = str_replace("/", "", $cnpj_cpf);
		// 	$cnpj_cpf = str_replace("-", "", $cnpj_cpf);

		// 	if(strlen($cnpj_cpf) == 14) $std->CNPJ = $cnpj_cpf;
		// 	else $std->CPF = $cnpj_cpf;

		// 	$nfe->tagtransporta($std);
		// }

		if($devolucao->transportadora_id != null){
			$std = new \stdClass();

			$std->xNome = $devolucao->transportadora->razao_social;

			$std->xEnder = $devolucao->transportadora->logradouro;
			$std->xMun = $devolucao->transportadora->cidade->nome;
			$std->UF = $devolucao->transportadora->cidade->uf;


			$cnpj_cpf = $devolucao->transportadora->cnpj_cpf;
			$cnpj_cpf = str_replace(".", "", $cnpj_cpf);
			$cnpj_cpf = str_replace("/", "", $cnpj_cpf);
			$cnpj_cpf = str_replace("-", "", $cnpj_cpf);

			if(strlen($cnpj_cpf) == 14) $std->CNPJ = $cnpj_cpf;
			else $std->CPF = $cnpj_cpf;

			$nfe->tagtransporta($std);
		}

		if($devolucao->veiculo_uf != '' && $devolucao->veiculo_placa != ''){
			$std = new \stdClass();

			$placa = str_replace("-", "", $devolucao->veiculo_placa);
			$std->placa = strtoupper($placa);
			$std->UF = $devolucao->veiculo_uf;

			$nfe->tagveicTransp($std);
		}


		if($devolucao->frete_peso_bruto > 0 && $devolucao->frete_peso_liquido > 0){

			$stdVol = new \stdClass();
			$stdVol->item = 1;
			$stdVol->qVol = $devolucao->frete_quantidade;
			$stdVol->esp = $devolucao->frete_especie;

			$stdVol->nVol = $devolucao->frete_numero;
			$stdVol->pesoL = $devolucao->frete_peso_liquido;
			$stdVol->pesoB = $devolucao->frete_peso_bruto;
			$vol = $nfe->tagvol($stdVol);
		}




		// $stdResp = new \stdClass();
		// $stdResp->CNPJ = '08543628000145'; 
		// $stdResp->xContato= 'Slym';
		// $stdResp->email = 'marcos05111993@gmail.com'; 
		// $stdResp->fone = '43996347016'; 

		// $nfe->taginfRespTec($stdResp);


	//Fatura

		// $stdFat = new \stdClass();
		// $stdFat->nFat = (int)$lastNumero+1;
		// $stdFat->vOrig = $this->format($somaProdutos);
		// $stdFat->vDesc = $this->format($venda->desconto);
		// $stdFat->vLiq = $this->format($somaProdutos-$venda->desconto);

		// $fatura = $nfe->tagfat($stdFat);


	//Duplicata

		// if(count($venda->duplicatas) > 0){
		// 	$contFatura = 1;
		// 	foreach($venda->duplicatas as $ft){
		// 		$stdDup = new \stdClass();
		// 		$stdDup->nDup = "00".$contFatura;
		// 		$stdDup->dVenc = substr($ft->data_vencimento, 0, 10);
		// 		$stdDup->vDup = $this->format($ft->valor_integral);

		// 		$nfe->tagdup($stdDup);
		// 		$contFatura++;
		// 	}
		// }else{
		// 	$stdDup = new \stdClass();
		// 	$stdDup->nDup = '001';
		// 	$stdDup->dVenc = Date('Y-m-d');
		// 	$stdDup->vDup =  $this->format($somaProdutos-$venda->desconto);

		// 	$nfe->tagdup($stdDup);
		// }



		$stdPag = new \stdClass();
		$pag = $nfe->tagpag($stdPag);

		$stdDetPag = new \stdClass();


		$stdDetPag->tPag = '90';
		$stdDetPag->vPag = 0.00; 

		$stdDetPag->indPag = '0'; // sem pagamento 

		$detPag = $nfe->tagdetPag($stdDetPag);

		$stdInfoAdic = new \stdClass();
		$stdInfoAdic->infCpl = $this->retiraAcentos($devolucao->observacao);

		$infoAdic = $nfe->taginfAdic($stdInfoAdic);

		$std = new \stdClass();
		$std->CNPJ = getenv('RESP_CNPJ'); //CNPJ da pessoa jurídica responsável pelo sistema utilizado na emissão do documento fiscal eletrônico
		$std->xContato= getenv('RESP_NOME'); //Nome da pessoa a ser contatada
		$std->email = getenv('RESP_EMAIL'); //E-mail da pessoa jurídica a ser contatada
		$std->fone = getenv('RESP_FONE'); //Telefone da pessoa jurídica/física a ser contatada
		
		
		$nfe->taginfRespTec($std);

		// if($nfe->montaNFe()){
		// 	$arr = [
		// 		'chave' => $nfe->getChave(),
		// 		'xml' => $nfe->getXML(),
		// 		'nNf' => $stdIde->nNF
		// 	];
		// 	return $arr;
		// } else {
		// 	throw new Exception("Erro ao gerar NFe");
		// }

		try{
			$nfe->montaNFe();
			$arr = [
				'chave' => $nfe->getChave(),
				'xml' => $nfe->getXML(),
				'nNf' => $stdIde->nNF
			];
			return $arr;
		}catch(\Exception $e){
			return [
				'erros_xml' => $nfe->getErrors()
			];
		}

	}

	private function retiraAcentos($texto){
		return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/", "/(ç)/", "/(Ç)/"),explode(" ","a A e E i I o O u U n N c C"),$texto);
	}

	public function sign($xml){
		return $this->tools->signNFe($xml);
	}

	public function transmitir($signXml, $chave){
		try{
			$idLote = str_pad(100, 15, '0', STR_PAD_LEFT);
			$resp = $this->tools->sefazEnviaLote([$signXml], $idLote);

			$st = new Standardize();
			$std = $st->toStd($resp);
			sleep(2);

			if ($std->cStat != 103) {

				return "[$std->cStat] - $std->xMotivo";
			}
			sleep(3);

			$recibo = $std->infRec->nRec; 
			$protocolo = $this->tools->sefazConsultaRecibo($recibo);
			sleep(3);
			//return $protocolo;
			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
			try {
				$xml = Complements::toAuthorize($signXml, $protocolo);
				header('Content-type: text/xml; charset=UTF-8');
				file_put_contents($public.'xml_devolucao/'.$chave.'.xml',$xml);
				return $recibo;
				// $this->printDanfe($xml);
			} catch (\Exception $e) {
				return "Erro: " . $st->toJson($protocolo);
			}

		} catch(\Exception $e){
			return "Erro: ".$e->getMessage() ;
		}

	}	


	public function consultar($devolucao){
		try {
			
			$this->tools->model('55');

			$chave = $devolucao->chave_gerada;
			$response = $this->tools->sefazConsultaChave($chave);

			$stdCl = new Standardize($response);
			$arr = $stdCl->toArray();

			// $arr = json_decode($json);
			return json_encode($arr);

		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}

	public function format($number, $dec = 2){
		$number = (float) $number;
		return number_format($number, $dec, ".", "");
	}

	public function cancelar($devolucao, $justificativa){
		try {

			$chave = $devolucao->chave_gerada;
			$response = $this->tools->sefazConsultaChave($chave);
			$stdCl = new Standardize($response);
			$arr = $stdCl->toArray();
			sleep(2);
				// return $arr;
			$xJust = $justificativa;
			
			$nProt = $arr['protNFe']['infProt']['nProt'];

			$response = $this->tools->sefazCancela($chave, $xJust, $nProt);
			sleep(3);
			$stdCl = new Standardize($response);
			$std = $stdCl->toStd();
			$arr = $stdCl->toArray();
			$json = $stdCl->toJson();

			if ($std->cStat != 128) {
        //TRATAR
			} else {
				$cStat = $std->retEvento->infEvento->cStat;
				$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
				if ($cStat == '101' || $cStat == '135' || $cStat == '155' ) {
            //SUCESSO PROTOCOLAR A SOLICITAÇÂO ANTES DE GUARDAR
					$xml = Complements::toAuthorize($this->tools->lastRequest, $response);
					file_put_contents($public.'xml_devolucao_cancelada/'.$chave.'.xml',$xml);

					return $json;
				} else {
            //houve alguma falha no evento 
            //TRATAR
					return $json;	
				}
			}    
		} catch (\Exception $e) {
			return $e->getMessage();
    //TRATAR
		}
	}

	public function cartaCorrecao($devolucao, $correcao){
		
		try {

			$chave = $devolucao->chave_gerada;
			$xCorrecao = $correcao;
			$nSeqEvento = $devolucao->sequencia_cce+1;
			$response = $this->tools->sefazCCe($chave, $xCorrecao, $nSeqEvento);
			sleep(2);

			$stdCl = new Standardize($response);

			$std = $stdCl->toStd();

			$arr = $stdCl->toArray();

			$json = $stdCl->toJson();

			if ($std->cStat != 128) {
        //TRATAR
			} else {
				$cStat = $std->retEvento->infEvento->cStat;
				if ($cStat == '135' || $cStat == '136') {
					$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
            //SUCESSO PROTOCOLAR A SOLICITAÇÂO ANTES DE GUARDAR
					$xml = Complements::toAuthorize($this->tools->lastRequest, $response);
					file_put_contents($public.'xml_devolucao_correcao/'.$chave.'.xml',$xml);

					$devolucao->sequencia_cce = $devolucao->sequencia_cce + 1;
					$devolucao->save();
					return $json;

				} else {
            //houve alguma falha no evento 
					return ['erro' => true, 'data' => $arr, 'status' => 402];	
            //TRATAR
				}
			}    
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

}