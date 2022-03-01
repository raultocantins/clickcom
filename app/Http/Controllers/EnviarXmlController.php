<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venda;
use App\Models\ItemVenda;
use App\Models\VendaCaixa;
use App\Models\ItemVendaCaixa;
use App\Models\Cte;
use App\Models\Mdfe;
use App\Models\ConfigNota;
use App\Models\Compra;
use App\Models\Devolucao;
use App\Models\Empresa;
use App\Models\EscritorioContabil;
use Mail;
use Dompdf\Dompdf;
use Dompdf\Options;

class EnviarXmlController extends Controller
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
		return view('enviarXml/list')
		->with('title', 'Enviar XML');
	}

	private function getCnpjEmpresa(){
		$empresa = Empresa::find($this->empresa_id);
		$cnpj = $empresa->configNota->cnpj;

		$cnpj = str_replace(".", "", $cnpj);
		$cnpj = str_replace("/", "", $cnpj);
		$cnpj = str_replace("-", "", $cnpj);
		$cnpj = str_replace(" ", "", $cnpj);

		return $cnpj;
	}

	public function filtro(Request $request){
		$xml = Venda::
		whereBetween('updated_at', [
			$this->parseDate($request->data_inicial), 
			$this->parseDate($request->data_final, true)])
		->where('empresa_id', $this->empresa_id);

		$estado = $request->estado;
		if($estado == 1){
			$xml->where('estado', 'APROVADO');
		}else{
			$xml->where('estado', 'CANCELADO');
		}
		$xml = $xml->get();

		$public = getenv('SERVIDOR_WEB') ? 'public/' : '';

		try{
			if(count($xml) > 0){

				$cnpj = $this->getCnpjEmpresa();
				// $zip_file = 'zips/xml_'.$cnpj.'.zip';
				$zip_file = public_path('zips') . '/xml_'.$cnpj.'.zip';

				$zip = new \ZipArchive();
				$zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

				if($estado == 1){
					foreach($xml as $x){
						if(file_exists($public.'xml_nfe/'.$x->chave. '.xml'))
							$zip->addFile($public.'xml_nfe/'.$x->chave. '.xml', $x->path_xml);
					}
				}else{
					foreach($xml as $x){
						if(file_exists($public.'xml_nfe_cancelada/'.$x->chave. '.xml'))
							$zip->addFile($public.'xml_nfe_cancelada/'.$x->chave. '.xml', $x->path_xml);
					}
				}
				$zip->close();
			}
		}catch(\Exception $e){
		}

		try{
			$xmlCte = Cte::
			whereBetween('updated_at', [
				$this->parseDate($request->data_inicial), 
				$this->parseDate($request->data_final, true)])
			->where('empresa_id', $this->empresa_id);

			$estado = $request->estado;
			if($estado == 1){
				$xmlCte->where('estado', 'APROVADO');
			}else{
				$xmlCte->where('estado', 'CANCELADO');
			}
			$xmlCte = $xmlCte->get();

			if(count($xmlCte) > 0){

				$cnpj = $this->getCnpjEmpresa();
				// $zip_file = $public.'xmlcte.zip';
				// $zip_file = 'zips/xmlcte_'.$cnpj.'.zip';
				$zip_file = public_path('zips') . '/xmlcte_'.$cnpj.'.zip';


				$zip = new \ZipArchive();
				$zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

				if($estado == 1){
					foreach($xmlCte as $x){
						if(file_exists($public.'xml_cte/'.$x->chave. '.xml'))
							$zip->addFile($public.'xml_cte/'.$x->chave. '.xml', $x->path_xml);
					}
				}else{
					foreach($xmlCte as $x){
						if(file_exists($public.'xml_cte_cancelada/'.$x->chave. '.xml'))
							$zip->addFile($public.'xml_cte_cancelada/'.$x->chave. '.xml', $x->path_xml);
					}
				}
				$zip->close();


			}
		}catch(\Exception $e){

		}

		try{
			$xmlNfce = VendaCaixa::
			whereBetween('updated_at', [
				$this->parseDate($request->data_inicial), 
				$this->parseDate($request->data_final, true)])
			->where('empresa_id', $this->empresa_id);

			if($estado == 1){
				$xmlNfce->where('estado', 'APROVADO');
			}else{
				$xmlNfce->where('estado', 'CANCELADO');
			}
			$xmlNfce = $xmlNfce->get();

			if(sizeof($xmlNfce) > 0){

				// $zip_file = $public.'xmlnfce.zip';
				$cnpj = $this->getCnpjEmpresa();

				// $zip_file = 'zips/xmlnfce_'.$cnpj.'.zip';
				$zip_file = public_path('zips') . '/xmlnfce_'.$cnpj.'.zip';

				$zip = new \ZipArchive();
				$zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

				if($estado == 1){
					foreach($xmlNfce as $x){
						if(file_exists($public.'xml_nfce/'.$x->chave. '.xml'))
							$zip->addFile($public.'xml_nfce/'.$x->chave. '.xml', $x->chave. '.xml');
					}
				}else{
					foreach($xmlNfce as $x){
						if(file_exists($public.'xml_nfce_cancelada/'.$x->chave. '.xml'))
							$zip->addFile($public.'xml_nfce_cancelada/'.$x->chave. '.xml', $x->chave. '.xml');
					}
				}
				$zip->close();
			}
		}catch(\Exception $e){

		}

		$xmlMdfe = Mdfe::
		whereBetween('updated_at', [
			$this->parseDate($request->data_inicial), 
			$this->parseDate($request->data_final, true)])
		->where('empresa_id', $this->empresa_id);

		$estado = $request->estado;
		if($estado == 1){
			$xmlMdfe->where('estado', 'APROVADO');
		}else{
			$xmlMdfe->where('estado', 'CANCELADO');
		}
		$xmlMdfe = $xmlMdfe->get();

		if(count($xmlMdfe) > 0){
			try{

				// $zip_file = $public.'xmlmdfe.zip';
				$cnpj = $this->getCnpjEmpresa();

				// $zip_file = 'zips/xmlmdfe_'.$cnpj.'.zip';
				$zip_file = public_path('zips') . '/xmlmdfe_'.$cnpj.'.zip';


				$zip = new \ZipArchive();
				$zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
				if($estado == 1){
					foreach($xmlMdfe as $x){
						if(file_exists($public.'xml_mdfe/'.$x->chave. '.xml')){
							$zip->addFile($public.'xml_mdfe/'.$x->chave. '.xml', $x->chave. '.xml');
						}
					}
				}else{
					foreach($xmlMdfe as $x){
						if(file_exists($public.'xml_mdfe_cancelada/'.$x->chave. '.xml')){
							$zip->addFile($public.'xml_mdfe_cancelada/'.$x->chave. '.xml', $x->chave. '.xml');
						}
					}
				}
				$zip->close();

			}catch(\Exception $e){
				// echo $e->getMessage();
			}

		}

		//nfe entrada
		$xmlEntrada = Compra::
		whereBetween('updated_at', [
			$this->parseDate($request->data_inicial), 
			$this->parseDate($request->data_final, true)])
		->where('empresa_id', $this->empresa_id);

		if($estado == 1){
			$xmlEntrada->where('estado', 'APROVADO');
		}else{
			$xmlEntrada->where('estado', 'CANCELADO');
		}
		$xmlEntrada = $xmlEntrada->get();

		if(count($xmlEntrada) > 0){

			try{

				// $zip_file = $public.'xmlmdfe.zip';
				$cnpj = $this->getCnpjEmpresa();

				// $zip_file = 'zips/xmlmdfe_'.$cnpj.'.zip';
				$zip_file = public_path('zips') . '/xmlEntrada_'.$cnpj.'.zip';


				$zip = new \ZipArchive();
				$zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

				if($estado == 1){
					foreach($xmlEntrada as $x){
						if(file_exists($public.'xml_entrada_emitida/'.$x->chave. '.xml')){
							$zip->addFile($public.'xml_entrada_emitida/'.$x->chave. '.xml', $x->chave. '.xml');
						}
					}
				}else{
					foreach($xmlEntrada as $x){
						if(file_exists($public.'xml_nfe_entrada_cancelada/'.$x->chave. '.xml')){
							$zip->addFile($public.'xml_nfe_entrada_cancelada/'.$x->chave. '.xml', $x->chave. '.xml');
						}
					}
				}
				$zip->close();

			}catch(\Exception $e){
				// echo $e->getMessage();
			}

		}

		$xmlDevolucao = Devolucao::
		whereBetween('updated_at', [
			$this->parseDate($request->data_inicial), 
			$this->parseDate($request->data_final, true)])
		->where('empresa_id', $this->empresa_id);
		// 1- Aprovado, 3 - Cancelado
		if($estado == 1){
			$xmlDevolucao->where('estado', 1);
		}else{
			$xmlDevolucao->where('estado', 3);
		}
		$xmlDevolucao = $xmlDevolucao->get();

		if(count($xmlDevolucao) > 0){

			try{

				// $zip_file = $public.'xmlmdfe.zip';
				$cnpj = $this->getCnpjEmpresa();

				// $zip_file = 'zips/xmlmdfe_'.$cnpj.'.zip';
				$zip_file = public_path('zips') . '/xmlDevolucao_'.$cnpj.'.zip';


				$zip = new \ZipArchive();
				$zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

				if($estado == 1){
					foreach($xmlDevolucao as $x){
						if(file_exists($public.'xml_devolucao/'.$x->chave_gerada. '.xml')){
							$zip->addFile($public.'xml_devolucao/'.$x->chave_gerada. '.xml', $x->chave_gerada. '.xml');
						}
					}
				}else{
					foreach($xmlDevolucao as $x){
						if(file_exists($public.'xml_devolucao_cancelada/'.$x->chave_gerada. '.xml')){
							$zip->addFile($public.'xml_devolucao_cancelada/'.$x->chave_gerada. '.xml', $x->chave_gerada. '.xml');
						}
					}
				}
				$zip->close();

			}catch(\Exception $e){
				// echo $e->getMessage();
			}

		}

		$dataInicial = str_replace("/", "-", $request->data_inicial);
		$dataFinal = str_replace("/", "-", $request->data_final);

		return view('enviarXml/list')
		->with('xml', $xml)
		->with('xmlNfce', $xmlNfce)
		->with('xmlCte', $xmlCte)
		->with('xmlMdfe', $xmlMdfe)
		->with('estado', $request->estado)
		->with('xmlEntrada', $xmlEntrada)
		->with('xmlDevolucao', $xmlDevolucao)
		->with('dataInicial', $dataInicial)
		->with('dataFinal', $dataFinal)
		->with('title', 'Enviar XML');
	}

	public function download(){
		// $public = getenv('SERVIDOR_WEB') ? 'public/' : '';

		$cnpj = $this->getCnpjEmpresa();
		$file = public_path('zips') . '/xml_'.$cnpj.'.zip';

		// $file = $public."zips/xml_".$this->empresa_id.".zip";

		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="xmls_nfe_'.$cnpj.'.zip"');
		readfile($file);

		// return redirect('/enviarXml');

	}

	public function downloadEntrada(){
		// $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
		// $file = $public."xmlnfce.zip";
		// $file = $public."zips/xmlnfce_".$this->empresa_id.".zip";
		$cnpj = $this->getCnpjEmpresa();
		$file = public_path('zips') . '/xmlEntrada_'.$cnpj.'.zip';

		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="xml_entrada_'.$cnpj.'.zip"');
		readfile($file);

		// return redirect('/enviarXml');
	}

	public function downloadDevolucao(){
		// $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
		// $file = $public."xmlnfce.zip";
		// $file = $public."zips/xmlnfce_".$this->empresa_id.".zip";
		$cnpj = $this->getCnpjEmpresa();
		$file = public_path('zips') . '/xmlDevolucao_'.$cnpj.'.zip';

		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="xml_entrada_'.$cnpj.'.zip"');
		readfile($file);

		// return redirect('/enviarXml');
	}

	public function downloadNfce(){
		// $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
		// $file = $public."xmlnfce.zip";
		// $file = $public."zips/xmlnfce_".$this->empresa_id.".zip";

		$cnpj = $this->getCnpjEmpresa();
		$file = public_path('zips') . '/xmlnfce_'.$cnpj.'.zip';

		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="xmls_nfce_'.$cnpj.'.zip"');
		readfile($file);

		// return redirect('/enviarXml');
	}

	public function downloadCte(){
		// $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
		// $file = $public."xmlcte.zip";
		// $file = $public."zips/xmlcte_".$this->empresa_id.".zip";

		$cnpj = $this->getCnpjEmpresa();
		$file = public_path('zips') . '/xmlcte_'.$cnpj.'.zip';

		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="xmls_cte_'.$cnpj.'.zip"');
		readfile($file);

		// return redirect('/enviarXml');
	}

	public function downloadMdfe(){
		// $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
		// $file = $public."xmlmdfe.zip";
		// $file = $public."zips/xmlmdfe_".$this->empresa_id.".zip";

		$cnpj = $this->getCnpjEmpresa();
		$file = public_path('zips') . '/xmlmdfe_'.$cnpj.'.zip';

		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="xmls_mdfe_'.$cnpj.'.zip"');
		readfile($file);

		// return redirect('/enviarXml');
	}

	private function parseDate($date, $plusDay = false){
		if($plusDay == false)
			return date('Y-m-d', strtotime(str_replace("/", "-", $date)));
		else
			return date('Y-m-d', strtotime("+1 day",strtotime(str_replace("/", "-", $date))));
	}

	public function email($dataInicial, $dataFinal){

		$empresa = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();
		Mail::send('mail.xml', ['data_inicial' => $dataInicial, 'data_final' => $dataFinal,
			'empresa' => $empresa->razao_social, 'cnpj' => $empresa->cnpj, 'tipo' => 'NFe'], function($m){
				// $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
				$cnpj = $this->getCnpjEmpresa();
				$fileDir = public_path('zips') . '/xml_'.$cnpj.'.zip';

				$escritorio = EscritorioContabil::first();
				if($escritorio == null){
					echo "<h1>Configure o email do escritório <a target='_blank' href='/escritorio'>aqui</a></h1>";
					die();
				}
				$nomeEmail = getenv('MAIL_NAME');
				$nomeEmail = str_replace("_", " ", $nomeEmail);
				$m->from(getenv('MAIL_USERNAME'), $nomeEmail);
				$m->subject('Envio de XML');
				$m->attach($fileDir);
				$m->to($escritorio->email);
			});
		echo '<h1>Email enviado</h1>';
	}

	public function emailEntrada($dataInicial, $dataFinal){

		$empresa = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();
		Mail::send('mail.xml', ['data_inicial' => $dataInicial, 'data_final' => $dataFinal,
			'empresa' => $empresa->razao_social, 'cnpj' => $empresa->cnpj, 'tipo' => 'NFe Entrada'], function($m){
				// $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
				$cnpj = $this->getCnpjEmpresa();
				$fileDir = public_path('zips') . '/xmlEntrada_'.$cnpj.'.zip';

				$escritorio = EscritorioContabil::first();
				if($escritorio == null){
					echo "<h1>Configure o email do escritório <a target='_blank' href='/escritorio'>aqui</a></h1>";
					die();
				}
				$nomeEmail = getenv('MAIL_NAME');
				$nomeEmail = str_replace("_", " ", $nomeEmail);
				$m->from(getenv('MAIL_USERNAME'), $nomeEmail);
				$m->subject('Envio de XML');
				$m->attach($fileDir);
				$m->to($escritorio->email);
			});
		echo '<h1>Email enviado</h1>';
	}

	public function emailDevolucao($dataInicial, $dataFinal){

		$empresa = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();
		Mail::send('mail.xml', ['data_inicial' => $dataInicial, 'data_final' => $dataFinal,
			'empresa' => $empresa->razao_social, 'cnpj' => $empresa->cnpj, 'tipo' => 'NFe Devolução'], function($m){
				// $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
				$cnpj = $this->getCnpjEmpresa();
				$fileDir = public_path('zips') . '/xmlDevolucao_'.$cnpj.'.zip';

				$escritorio = EscritorioContabil::first();
				if($escritorio == null){
					echo "<h1>Configure o email do escritório <a target='_blank' href='/escritorio'>aqui</a></h1>";
					die();
				}
				$nomeEmail = getenv('MAIL_NAME');
				$nomeEmail = str_replace("_", " ", $nomeEmail);
				$m->from(getenv('MAIL_USERNAME'), $nomeEmail);
				$m->subject('Envio de XML');
				$m->attach($fileDir);
				$m->to($escritorio->email);
			});
		echo '<h1>Email enviado</h1>';
	}

	public function emailNfce($dataInicial, $dataFinal){

		$empresa = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();
		Mail::send('mail.xml', ['data_inicial' => $dataInicial, 'data_final' => $dataFinal,
			'empresa' => $empresa->razao_social, 'cnpj' => $empresa->cnpj, 'tipo' => 'NFCe'], function($m){
				$escritorio = EscritorioContabil::first();
				if($escritorio == null){
					echo "<h1>Configure o email do escritório <a target='_blank' href='/escritorio'>aqui</a></h1>";
					die();
				}
				// $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
				$cnpj = $this->getCnpjEmpresa();
				$fileDir = public_path('zips') . '/xmlnfce_'.$cnpj.'.zip';

				$nomeEmail = getenv('MAIL_NAME');
				$nomeEmail = str_replace("_", " ", $nomeEmail);
				$m->from(getenv('MAIL_USERNAME'), $nomeEmail);
				$m->subject('Envio de XML');
				$m->attach($fileDir);
				$m->to($escritorio->email);

			});
		echo '<h1>Email enviado</h1>';

	}

	public function emailCte($dataInicial, $dataFinal){
		
		$empresa = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();
		Mail::send('mail.xml', ['data_inicial' => $dataInicial, 'data_final' => $dataFinal,
			'empresa' => $empresa->razao_social, 'cnpj' => $empresa->cnpj, 'tipo' => 'CTe'], function($m){
				$escritorio = EscritorioContabil::first();


				if($escritorio == null){
					echo "<h1>Configure o email do escritório <a target='_blank' href='/escritorio'>aqui</a></h1>";
					die();
				}
				// $public = getenv('SERVIDOR_WEB') ? 'public/' : '';

				$cnpj = $this->getCnpjEmpresa();
				$fileDir = public_path('zips') . '/xmlcte_'.$cnpj.'.zip';

				$nomeEmail = getenv('MAIL_NAME');
				$nomeEmail = str_replace("_", " ", $nomeEmail);
				$m->from(getenv('MAIL_USERNAME'), $nomeEmail);
				$m->subject('Envio de XML');
				$m->attach($fileDir);
				$m->to($escritorio->email);

			});
		echo '<h1>Email enviado</h1>';

	}

	public function emailMdfe($dataInicial, $dataFinal){
		
		$empresa = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();
		Mail::send('mail.xml', ['data_inicial' => $dataInicial, 'data_final' => $dataFinal,
			'empresa' => $empresa->razao_social, 'cnpj' => $empresa->cnpj, 'tipo' => 'MDFe'], function($m){
				$escritorio = EscritorioContabil::first();
				if($escritorio == null){
					echo "<h1>Configure o email do escritório <a target='_blank' href='/escritorio'>aqui</a></h1>";
					die();
				}
				// $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
				$cnpj = $this->getCnpjEmpresa();
				$fileDir = public_path('zips') . '/xmlmdfe_'.$cnpj.'.zip';

				$nomeEmail = getenv('MAIL_NAME');
				$nomeEmail = str_replace("_", " ", $nomeEmail);
				$m->from(getenv('MAIL_USERNAME'), $nomeEmail);
				$m->subject('Envio de XML');
				$m->attach($fileDir);
				$m->to($escritorio->email);

			});
		echo '<h1>Email enviado</h1>';

	}

	public function filtroCfop(Request $request){
		return view('enviarXml/filtro_cfop')
		->with('title', 'Filtro');
		
	}

	public function filtroCfopGet(Request $request){
		if($request->data_inicial && $request->data_final){
			$somaTotalVendas = 0;
			$cfop = $request->cfop;
			if(strlen($cfop) == 4){
				$itensVenda = ItemVenda::
			// select('item_vendas.id', \DB\Raw('sum(quantidade)'))
				selectRaw('sum(quantidade) AS qtd, sum(quantidade*valor) AS total, item_vendas.*')
				->join('vendas', 'vendas.id', '=', 'item_vendas.venda_id')
				->where('vendas.empresa_id', $this->empresa_id)
				->where('vendas.estado', 'APROVADO')
				->where('item_vendas.cfop', $cfop)
				->whereBetween('item_vendas.created_at', [
					$this->parseDate($request->data_inicial) . " 00:00:00", 
					$this->parseDate($request->data_final) . " 23:59:59", 
				])
				->groupBy('item_vendas.produto_id')
				->get();

				$itensVendaCaixa = ItemVendaCaixa::
			// select('item_vendas.id', \DB\Raw('sum(quantidade)'))
				selectRaw('sum(quantidade) AS qtd, sum(quantidade*valor) AS total, item_venda_caixas.*')
				->join('venda_caixas', 'venda_caixas.id', '=', 'item_venda_caixas.venda_caixa_id')
				->where('venda_caixas.empresa_id', $this->empresa_id)
				->where('venda_caixas.estado', 'APROVADO')
				->where('item_venda_caixas.cfop', $cfop)
				->whereBetween('item_venda_caixas.created_at', [
					$this->parseDate($request->data_inicial) . " 00:00:00", 
					$this->parseDate($request->data_final) . " 23:59:59", 
				])
				->groupBy('item_venda_caixas.produto_id')
				->get();


				$itens = $this->uneObjetos($itensVenda, $itensVendaCaixa);
				$somaTotalVendas = $this->somaTotalVendas($this->parseDate($request->data_inicial), $this->parseDate($request->data_final));

				// $somaTotalVendas = 0;
				return view('enviarXml/filtro_cfop')
				->with('itens', $itens)
				->with('somaTotalVendas', $somaTotalVendas)
				->with('cfop', $request->cfop)
				->with('dataInicial', $request->data_inicial)
				->with('dataFinal', $request->data_final)
				->with('title', 'Filtro');
			}else{
				//agrupar CFOP
				$cfops = $this->getCfops($this->parseDate($request->data_inicial) . " 00:00:00",
					$this->parseDate($request->data_final) . " 23:59:59");
				$itens = [];
				foreach($cfops as $cfop){
					$itensVenda = ItemVenda::
					selectRaw('sum(quantidade) AS qtd, sum(quantidade*valor) AS total, item_vendas.*')
					->join('vendas', 'vendas.id', '=', 'item_vendas.venda_id')
					->where('vendas.empresa_id', $this->empresa_id)
					->where('vendas.estado', 'APROVADO')
					->where('item_vendas.cfop', $cfop)
					->whereBetween('item_vendas.created_at', [
						$this->parseDate($request->data_inicial) . " 00:00:00", 
						$this->parseDate($request->data_final) . " 23:59:59", 
					])
					->groupBy('item_vendas.produto_id')
					->get();

					$itensVendaCaixa = ItemVendaCaixa::
					selectRaw('sum(quantidade) AS qtd, sum(quantidade*valor) AS total, item_venda_caixas.*')
					->join('venda_caixas', 'venda_caixas.id', '=', 'item_venda_caixas.venda_caixa_id')
					->where('venda_caixas.empresa_id', $this->empresa_id)
					->where('venda_caixas.estado', 'APROVADO')
					->where('item_venda_caixas.cfop', $cfop)
					->whereBetween('item_venda_caixas.created_at', [
						$this->parseDate($request->data_inicial) . " 00:00:00", 
						$this->parseDate($request->data_final) . " 23:59:59", 
					])
					->groupBy('item_venda_caixas.produto_id')
					->get();


					$temp = $this->uneObjetos($itensVenda, $itensVendaCaixa);
					$somaTotalVendas = $this->somaTotalVendas($this->parseDate($request->data_inicial), $this->parseDate($request->data_final));

					array_push($itens, [
						'cfop' => $cfop,
						'itens' => $temp
					]);


				}

				return view('enviarXml/filtro_cfop_group')
				->with('itens', $itens)
				->with('somaTotalVendas', $somaTotalVendas)
				->with('cfop', $request->cfop)
				->with('dataInicial', $request->data_inicial)
				->with('dataFinal', $request->data_final)
				->with('title', 'Filtro');
			}

			
			
		}else{
			session()->flash('mensagem_erro', 'Informe data inicial e final');

			return redirect('enviarXml/filtroCfop');
		}
	}

	private function getCfops($dataInicial, $dataFinal){
		$cfops = [];

		$itensVenda = ItemVenda::
		selectRaw('distinct(item_vendas.cfop) as cfop')
		->join('vendas', 'vendas.id', '=', 'item_vendas.venda_id')
		->where('vendas.empresa_id', $this->empresa_id)
		->where('vendas.estado', 'APROVADO')
		->whereBetween('item_vendas.created_at', [
			$dataInicial, 
			$dataFinal, 
		])
		->get();


		$itensVendaCaixa = ItemVendaCaixa::
		selectRaw('distinct(item_venda_caixas.cfop) as cfop')
		->join('venda_caixas', 'venda_caixas.id', '=', 'item_venda_caixas.venda_caixa_id')
		->where('venda_caixas.empresa_id', $this->empresa_id)
		->where('venda_caixas.estado', 'APROVADO')
		->whereBetween('item_venda_caixas.created_at', [
			$dataInicial, 
			$dataFinal, 
		])
		->groupBy('item_venda_caixas.produto_id')
		->get();

		foreach($itensVenda as $i){
			if($i->cfop != "0"){
				if(!in_array($i->cfop, $cfops)){
					array_push($cfops, $i->cfop);
				}
			}
		}

		foreach($itensVendaCaixa as $i){
			if($i->cfop != "0"){
				if(!in_array($i->cfop, $cfops)){
					array_push($cfops, $i->cfop);
				}
			}
		}

		return $cfops;
	}

	private function somaTotalVendas($dataInicial, $dataFinal){

		$vendas = Venda::
			// select('item_vendas.id', \DB\Raw('sum(quantidade)'))
		selectRaw('sum(valor_total) AS soma')
		->where('empresa_id', $this->empresa_id)
		->where('estado', 'APROVADO')
		->whereBetween('created_at', [
			$dataInicial . " 00:00:00", 
			$dataFinal . " 23:59:59", 
		])
		->first();

		$vendasCaixa = VendaCaixa::
			// select('item_vendas.id', \DB\Raw('sum(quantidade)'))
		selectRaw('sum(valor_total) AS soma')
		->where('empresa_id', $this->empresa_id)
		->where('estado', 'APROVADO')
		->whereBetween('created_at', [
			$dataInicial . " 00:00:00", 
			$dataFinal . " 23:59:59", 
		])
		->first();

		return $vendas->soma + $vendasCaixa->soma;
	}

	private function uneObjetos($vendas, $vendaCaixas){
		$temp = [];
		foreach($vendas as $v){
			array_push($temp, $v);
		}

		foreach($vendaCaixas as $v){
			$inserido = false;
			for($i=0; $i<sizeof($temp); $i++){
				if($v->produto_id == $temp[$i]->produto_id){
					$temp[$i]->quantidade += $v->quantidade;
					$temp[$i]->total += $v->total;
					$inserido = true;
				}
			}

			if($inserido == false){
				array_push($temp, $v);
			}
		}
		return $temp;
	}

	public function filtroCfopImprimir(Request $request){
		$dataInicial = $request->dataInicial;
		$dataFinal = $request->dataFinal;
		$cfop = $request->cfop;
		$percentual = $request->percentual;
		$somaTotalVendas = $request->somaTotalVendas;

		$itensVenda = ItemVenda::
			// select('item_vendas.id', \DB\Raw('sum(quantidade)'))
		selectRaw('sum(quantidade) AS qtd, sum(quantidade*valor) AS total, item_vendas.*')
		->join('vendas', 'vendas.id', '=', 'item_vendas.venda_id')
		->where('vendas.empresa_id', $this->empresa_id)
		->where('vendas.estado', 'APROVADO')
		->where('item_vendas.cfop', $cfop)
		->whereBetween('item_vendas.created_at', [
			$this->parseDate($dataInicial) . " 00:00:00", 
			$this->parseDate($dataFinal) . " 23:59:59", 
		])
		->groupBy('item_vendas.produto_id')
		->get();

		$itensVendaCaixa = ItemVendaCaixa::
			// select('item_vendas.id', \DB\Raw('sum(quantidade)'))
		selectRaw('sum(quantidade) AS qtd, sum(quantidade*valor) AS total, item_venda_caixas.*')
		->join('venda_caixas', 'venda_caixas.id', '=', 'item_venda_caixas.venda_caixa_id')
		->where('venda_caixas.empresa_id', $this->empresa_id)
		->where('venda_caixas.estado', 'APROVADO')
		->where('item_venda_caixas.cfop', $cfop)
		->whereBetween('item_venda_caixas.created_at', [
			$this->parseDate($dataInicial) . " 00:00:00", 
			$this->parseDate($dataFinal) . " 23:59:59", 
		])
		->groupBy('item_venda_caixas.produto_id')
		->get();


		$itens = $this->uneObjetos($itensVenda, $itensVendaCaixa);

		$config = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();

		$p = view('enviarXml/print')
		->with('objeto', $itens)
		->with('dataInicial', $dataInicial)
		->with('dataFinal', $dataFinal)
		->with('cfop', $cfop)
		->with('percentual', $percentual)
		->with('somaTotalVendas', $somaTotalVendas)
		
		->with('config', $config);

		// return $p;

		$options = new Options();
		$options->set('isRemoteEnabled', TRUE);
		$domPdf = new Dompdf($options);

		$domPdf->loadHtml($p);

		$domPdf->setPaper("A4");
		$domPdf->render();
			// $domPdf->stream("orcamento.pdf", ["Attachment" => false]);
		$domPdf->stream("relatorio_$cfop.pdf");
	}

	public function filtroCfopImprimirGroup(Request $request){
		$dataInicial = $request->dataInicial;
		$dataFinal = $request->dataFinal;
		$cfop = $request->cfop;
		$percentual = $request->percentual;
		$somaTotalVendas = $request->somaTotalVendas;
		$itens = [];

		$cfops = $this->getCfops($this->parseDate($dataInicial) . " 00:00:00",
			$this->parseDate($dataFinal) . " 23:59:59");

		$itens = [];
		foreach($cfops as $cfop){
			$itensVenda = ItemVenda::
			selectRaw('sum(quantidade) AS qtd, sum(quantidade*valor) AS total, item_vendas.*')
			->join('vendas', 'vendas.id', '=', 'item_vendas.venda_id')
			->where('vendas.empresa_id', $this->empresa_id)
			->where('vendas.estado', 'APROVADO')
			->where('item_vendas.cfop', $cfop)
			->whereBetween('item_vendas.created_at', [
				$this->parseDate($dataInicial) . " 00:00:00", 
				$this->parseDate($dataFinal) . " 23:59:59", 
			])
			->groupBy('item_vendas.produto_id')
			->get();

			$itensVendaCaixa = ItemVendaCaixa::
			selectRaw('sum(quantidade) AS qtd, sum(quantidade*valor) AS total, item_venda_caixas.*')
			->join('venda_caixas', 'venda_caixas.id', '=', 'item_venda_caixas.venda_caixa_id')
			->where('venda_caixas.empresa_id', $this->empresa_id)
			->where('venda_caixas.estado', 'APROVADO')
			->where('item_venda_caixas.cfop', $cfop)
			->whereBetween('item_venda_caixas.created_at', [
				$this->parseDate($dataInicial) . " 00:00:00", 
				$this->parseDate($dataFinal) . " 23:59:59", 
			])
			->groupBy('item_venda_caixas.produto_id')
			->get();


			$temp = $this->uneObjetos($itensVenda, $itensVendaCaixa);
			$somaTotalVendas = $this->somaTotalVendas($this->parseDate($request->data_inicial), $this->parseDate($request->data_final));

			array_push($itens, [
				'cfop' => $cfop,
				'itens' => $temp
			]);

		}

		


		$config = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();

		$p = view('enviarXml/print_group')
		->with('objeto', $itens)
		->with('dataInicial', $dataInicial)
		->with('dataFinal', $dataFinal)
		->with('cfop', $cfop)
		->with('cfop', $cfop)
		->with('percentual', $percentual)
		->with('somaTotalVendas', $somaTotalVendas)
		->with('config', $config);



		// return $p;

		$options = new Options();
		$options->set('isRemoteEnabled', TRUE);
		$domPdf = new Dompdf($options);

		$domPdf->loadHtml($p);

		$domPdf->setPaper("A4");
		$domPdf->render();
			// $domPdf->stream("orcamento.pdf", ["Attachment" => false]);
		$domPdf->stream("relatorio_$cfop.pdf");
	}

}
