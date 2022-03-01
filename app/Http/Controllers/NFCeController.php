<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendaCaixa;
use App\Models\Venda;
use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\NFe\Danfce;
use NFePHP\DA\NFe\Cupom;
use NFePHP\DA\Legacy\FilesFolders;
use App\Models\ConfigNota;
use App\Models\ConfigCaixa;
use App\Helpers\StockMove;
use App\Models\Usuario;
use App\Services\NFCeService;
class NFCeController extends Controller
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
	
	public function gerar(Request $request){

		$vendaId = $request->vendaId;
		$venda = VendaCaixa::
		where('id', $vendaId)
		->first();

		if(valida_objeto($venda)){

			$config = ConfigNota::
			where('empresa_id', $this->empresa_id)
			->first();

			$cnpj = str_replace(".", "", $config->cnpj);
			$cnpj = str_replace("/", "", $cnpj);
			$cnpj = str_replace("-", "", $cnpj);
			$cnpj = str_replace(" ", "", $cnpj);

			$nfe_service = new NFCeService([
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

			if($venda->estado == 'REJEITADO' || $venda->estado == 'DISPONIVEL'){
				header('Content-type: text/html; charset=UTF-8');

				$nfce = $nfe_service->gerarNFCe($vendaId);
				if(!isset($nfce['erros_xml'])){
					$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
					$signed = $nfe_service->sign($nfce['xml']);
			// file_put_contents($public.'xml_nfce/'.$venda->id.'.xml',$signed);
					$resultado = $nfe_service->transmitirNfce($signed, $nfce['chave']);
					// return response()->json($resultado, 401);

					if(substr($resultado, 0, 4) != 'Erro'){
						$venda->chave = $nfce['chave'];
						$venda->path_xml = $nfce['chave'] . '.xml';
						$venda->estado = 'APROVADO';

						$venda->NFcNumero = $nfce['nNf'];
						$venda->save();
					}else{
						$venda->estado = 'REJEITADO';
						$venda->save();
					}
					echo json_encode($resultado);
				}else{
					return response()->json($nfce['erros_xml'], 401);
				}

			}else{
				echo json_encode("Apro");
			}
		}else{
			return response()->json("Não permitido", 403);
		}

	}

	public function xmlTemp($id){

		$venda = VendaCaixa::find($id);
		if(valida_objeto($venda)){

			$config = ConfigNota::
			where('empresa_id', $this->empresa_id)
			->first();

			$cnpj = str_replace(".", "", $config->cnpj);
			$cnpj = str_replace("/", "", $cnpj);
			$cnpj = str_replace("-", "", $cnpj);
			$cnpj = str_replace(" ", "", $cnpj);

			$nfe_service = new NFCeService([
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

			$nfce = $nfe_service->gerarNFCe($id);
			if(!isset($nfce['erros_xml'])){

				$signed = $nfe_service->sign($nfce['xml']);
				return response($signed)
				->header('Content-Type', 'application/xml');
			// file_put_contents($public.'xml_nfce/'.$venda->id.'.xml',$signed);

			}else{
				return response()->json("Não permitido", 403);
			}

		}
	}

	public function imprimir($id){
		$venda = VendaCaixa::
		where('id', $id)
		->first();
		if(valida_objeto($venda)){

			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
			if(file_exists($public.'xml_nfce/'.$venda->chave.'.xml')){
				try {
					$xml = file_get_contents($public.'xml_nfce/'.$venda->chave.'.xml');

					$config = ConfigNota::
					where('empresa_id', $this->empresa_id)
					->first();

					if($venda->tipo_pagamento == 17){
						$this->gerarPix($config, $venda);
					}

					if($config->logo){
						$logo = 'data://text/plain;base64,'. base64_encode(file_get_contents($public.'logos/' . $config->logo));
					}else{
						$logo = null;
					}

					$usuario = Usuario::find(get_id_user());
					$danfce = new Danfce($xml, $venda);
					if($usuario->config){
						$danfce->setPaperWidth($usuario->config->impressora_modelo);
					}
					$pdf = $danfce->render($logo);

			// header('Content-Type: application/pdf');
			// echo $pdf;
					return response($pdf)
					->header('Content-Type', 'application/pdf');

				} catch (\Exception $e) {
					echo $e->getMessage();
				}
			}else{
				echo "Arquivo XML não encontrado!!";
			}
		}else{
			return redirect('/403');
		}
	}

	public function baixarXml($id){
		$venda = VendaCaixa::
		where('id', $id)
		->first();
		if(valida_objeto($venda)){

			try {

				$public = getenv('SERVIDOR_WEB') ? 'public/' : '';

				return response()->download($public.'xml_nfce/'.$venda->chave.'.xml');
			} catch (\Exception $e) {
				echo $e->getMessage();
			}
		}else{
			return redirect('/403');
		}
	}

	public function imprimirNaoFiscal($id){
		$venda = VendaCaixa::
		where('id', $id)
		->first();
		if(valida_objeto($venda)){

			$config = ConfigNota::
			where('empresa_id', $this->empresa_id)
			->first();

			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
			$pathLogo = $public.'logos/' . $config->logo;
			if($venda->tipo_pagamento == 17){
				$this->gerarPix($config, $venda);
			}
			$usuario = Usuario::find(get_id_user());
			$cupom = new Cupom($venda, $pathLogo, $config, $usuario->config ? $usuario->config->impressora_modelo : 80);
			$cupom->monta();
			$pdf = $cupom->render();

		// header('Content-Type: application/pdf');
		// echo $pdf;
			return response($pdf)
			->header('Content-Type', 'application/pdf');
		}else{
			return redirect('/403');
		}
	}

	private function gerarPix($config, $venda){
		$configCaixa = ConfigCaixa::
		where('usuario_id', $venda->usuario_id)
		->first();

		if($configCaixa == null || $configCaixa->mercadopago_access_token == ""){
			return 0;
		}

		$cnpj = str_replace(" ", "", $config->cnpj);
		$nome = explode(" ", $config->razao_social);

		try{
			\MercadoPago\SDK::setAccessToken($configCaixa->mercadopago_access_token);

			$payment = new \MercadoPago\Payment();

			$payment->transaction_amount = (float)$venda->valor_total;
			$payment->description = "Venda PDV";
			$payment->payment_method_id = "pix";

			$cep = str_replace("-", "", $config->cep);
			$payment->payer = array(
				"email" => $config->email,
				"first_name" => $nome[0],
				"last_name" => $nome[1],
				"identification" => array(
					"type" => strlen($cnpj) == 14 ? 'CNPJ' : 'CPF',
					"number" => $cnpj
				),
				"address"=>  array(
					"zip_code" => str_replace("-", "", $config->cep),
					"street_name" => $config->logradouro,
					"street_number" => $config->numero,
					"neighborhood" => $config->bairro,
					"city" => $config->cidade,
					"federal_unit" => $config->uf
				)
			);

			$payment->save();

			if($payment->transaction_details){
				$venda->qr_code_base64 = $payment->point_of_interaction->transaction_data->qr_code_base64;
				$venda->save();
			}else{
				echo $payment->error;
				die;
			}
		}catch(\Exception $e){
			
		}

	}

	public function imprimirNaoFiscalCredito($id){
		$venda = Venda::
		where('id', $id)
		->first();
		if(valida_objeto($venda)){

			$config = ConfigNota::
			where('empresa_id', $this->empresa_id)
			->first();

			$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
			$pathLogo = $public.'logos/' . $config->logo;

			$cupom = new Cupom($venda, $pathLogo, $config);
			$cupom->monta();
			$pdf = $cupom->render();

		// header('Content-Type: application/pdf');
		// echo $pdf;
			return response($pdf)
			->header('Content-Type', 'application/pdf');
		}else{
			return redirect('/403');
		}
	}

	public function cancelar(Request $request){

		$config = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();

		$cnpj = str_replace(".", "", $config->cnpj);
		$cnpj = str_replace("/", "", $cnpj);
		$cnpj = str_replace("-", "", $cnpj);
		$cnpj = str_replace(" ", "", $cnpj);
		$nfe_service = new NFCeService([
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


		$nfce = $nfe_service->cancelarNFCe($request->id, $request->justificativa);

		if(!isset($nfce['cStat'])){
			return response()->json($nfce, 404);
		}
		if($nfce['retEvento']['infEvento']['cStat'] == 135){
			$venda = VendaCaixa::
			where('id', $request->id)
			->first();
			$venda->estado = 'CANCELADO';
			$venda->valor_total = 0;
			$venda->save();
			// if($venda){
			// 	$stockMove = new StockMove();

			// 	foreach($venda->itens as $i){
			// 		$stockMove->pluStock($i->produto_id, 
			// 			$i->quantidade, -50); // -50 na altera valor compra
			// 	}
			// }
			return response()->json($nfce, 200);

		}else{
			return response()->json($nfce, 401);
		}


	}

	public function deleteVenda($id){
		$venda = VendaCaixa::where('id', $id)
		->first();
		if(valida_objeto($venda)){
			echo json_encode($result);
		}else{

		}
	}

	public function consultar($id){
		$venda = VendaCaixa::find($id);

		if(valida_objeto($venda)){

			$config = ConfigNota::
			where('empresa_id', $this->empresa_id)
			->first();

			$cnpj = str_replace(".", "", $config->cnpj);
			$cnpj = str_replace("/", "", $cnpj);
			$cnpj = str_replace("-", "", $cnpj);
			$cnpj = str_replace(" ", "", $cnpj);
			try{
				$nfe_service = new NFCeService([
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

				$c = $nfe_service->consultarNFCe($venda);

				return response()->json($c, 200);
			}catch(\Exception $r){
				return response()->json($e->getMessage(), 401);

			}
		}else{
			return response()->json("Não permitido!", 403);
		}
	}

	public function detalhes($id){
		$venda = VendaCaixa::find($id);

		if(valida_objeto($venda)){

			$value = session('user_logged');

			return view('frontBox/detalhes')
			->with('venda', $venda)
			->with('adm', $value['adm'])
			->with('title', 'Alterar estado');
		}else{
			return response()->json("Não permitido!", 403);
		}
	}

	public function estadoFiscal($id){
		$venda = VendaCaixa::find($id);

		if(valida_objeto($venda)){

			$value = session('user_logged');
			
			return view('frontBox/alterar_estado_fiscal')
			->with('venda', $venda)
			->with('adm', $value['adm'])
			->with('title', 'Alterar estado');
		}else{
			return response()->json("Não permitido!", 403);
		}
	}

	public function estadoFiscalStore(Request $request){
		try{
			$venda = VendaCaixa::find($request->venda_id);
			$estado = $request->estado;
			$data = str_replace("/", "-", $request->data);

			if($data){
				$venda->data_registro = \Carbon\Carbon::parse($data)->format('Y-m-d H:i:s');
				$venda->created_at = \Carbon\Carbon::parse($data)->format('Y-m-d H:i:s');
			}

			$venda->estado = $estado;

			if ($request->hasFile('file')){
				$public = getenv('SERVIDOR_WEB') ? 'public/' : '';

				$xml = simplexml_load_file($request->file);
				$chave = substr($xml->NFe->infNFe->attributes()->Id, 3, 44);

				$file = $request->file;
				$file->move(public_path('xml_nfce'), $chave.'.xml');
				$venda->chave = $chave;
				$venda->NFcNumero = (int)$xml->NFe->infNFe->ide->nNF;
			}

			$venda->save();
			session()->flash("mensagem_sucesso", "Estado alterado");

		}catch(\Exception $e){
			session()->flash("mensagem_erro", "Erro: " . $e->getMessage());

		}
		return redirect()->back();
	}

	public function inutilizar(Request $request){
		try{

			$config = ConfigNota::
			where('empresa_id', $this->empresa_id)
			->first();

			$cnpj = str_replace(".", "", $config->cnpj);
			$cnpj = str_replace("/", "", $cnpj);
			$cnpj = str_replace("-", "", $cnpj);
			$cnpj = str_replace(" ", "", $cnpj);


			$nfe_service = new NFCeService([
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

		// echo json_encode($request->justificativa);
			$result = $nfe_service->inutilizar($config, $request->nInicio, $request->nFinal, 
				$request->justificativa);

			echo json_encode($result);
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);

		}
	}

}
