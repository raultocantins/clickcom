<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfigNota;
use App\Models\Certificado;
use App\Models\Contrato;
use App\Models\Empresa;
use setasign\Fpdi\TcpdfFpdi;
use Fpdf\Fpdf;
use NFePHP\Common\Certificate;

class AssinarContratoController extends Controller
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
		$contrato = Contrato::first();
		$empresa = Empresa::find($this->empresa_id);

		$texto = $this->preparaTexto($contrato->texto, $empresa);

		return view('contrato/mostrar')
		->with('texto', $texto)
		->with('title', 'Contrato');
	}

	private function preparaTexto($texto, $empresa){

		$texto = str_replace("{{nome}}", $empresa->nome, $texto);
		$texto = str_replace("{{rua}}", $empresa->rua, $texto);
		$texto = str_replace("{{numero}}", $empresa->numero, $texto);
		$texto = str_replace("{{bairro}}", $empresa->bairro, $texto);
		$texto = str_replace("{{email}}", $empresa->email, $texto);
		$texto = str_replace("{{cidade}}", $empresa->cidade, $texto);
		$texto = str_replace("{{cnpj}}", $empresa->cnpj, $texto);
		$texto = str_replace("{{data}}", date("d/m/Y H:i"), $texto);

		return $texto;
	}

	public function assinar(Request $request){

		if(!$request->aceito){
			session()->flash("mensagem_erro", "Aceite os termos!");
			return redirect()->back();
		}

		$config = ConfigNota::where('empresa_id', $this->empresa_id)
		->first();

		$empresa = Empresa::find($this->empresa_id);

		if($config == null){
			session()->flash("mensagem_erro", "Configure o emitente!");
			return redirect()->back();
		}

		$certificado = Certificado::
		where('empresa_id', $this->empresa_id)
		->first();

		if($certificado == null && getenv("CONTRATO_CERTIFICADO") == 1){
			session()->flash("mensagem_erro", "Configure o certificado!");
			return redirect()->back();
		}

		try{
			$cnpj = str_replace(".", "", $empresa->cnpj);
			$cnpj = str_replace("/", "", $cnpj);
			$cnpj = str_replace("-", "", $cnpj);
			$cnpj = str_replace(" ", "", $cnpj);
			if(getenv("CONTRATO_CERTIFICADO") == 1){
				$cert = Certificate::readPfx($certificado->arquivo, $certificado->senha);
				$pdf = new TcpdfFpdi();

				$info = array(
					'Name' => 'Marcos Bueno',
					'Date' => date("Y.m.d H:i:s"),
					'Reason' => 'Descreva o motivo da assinatura',
					'ContactInfo' => '43920004769',
				);

				$pdf->setSourceFile(public_path('contratos/'.$cnpj.'.pdf'));

				$publicKey = $cert->publicKey;

				$pdf->setSignature($cert->__toString(), $cert->privateKey, $certificado->senha, '', 2, $info);
				$pdf->SetFont('helvetica', '', 12);
				$pdf->AddPage();

				$pdf->Text(10, 255, "Assinado com certificado digital: " . $publicKey->commonName);

				$pdf->Text(10, 260,"Inicio: " . $publicKey->validFrom->format('d/m/y H:i:s') . " | Expiração:" . $publicKey->validTo->format('d/m/y H:i:s'));
				$pdf->Text(10, 265, "Data da assinatura: " . date('d/m/y H:i:s'));
				$tplId = $pdf->importPage(1);

				$pdf->setSignatureAppearance(180, 60, 15, 15);
				$pdf->addEmptySignatureAppearance(180, 80, 15, 15);
				$pdf->useTemplate($tplId, 0, 0);

				$pdf->Output(public_path('contratos/'.$cnpj.'.pdf'), 'F');
				$empresa = Empresa::find($this->empresa_id);
				$contrato = $empresa->contrato;
				$contrato->status = 1;
				$contrato->save();
				session()->flash("mensagem_sucesso", "Contrato assinado!");
				return redirect('/graficos');
			}else{
				$pdf = new TcpdfFpdi();

				$info = array(
					'Name' => 'Marcos Bueno',
					'Date' => date("Y.m.d H:i:s"),
					'Reason' => 'Descreva o motivo da assinatura',
					'ContactInfo' => '43920004769',
				);

				$pdf->setSourceFile(public_path('contratos/'.$cnpj.'.pdf'));

				$pdf->SetFont('helvetica', '', 12);
				$pdf->AddPage();

				$pdf->Text(10, 255, "Contrato assinado $cnpj");
				$pdf->Text(10, 265, "Data da assinatura: " . date('d/m/y H:i:s'));
				$tplId = $pdf->importPage(1);

				// $pdf->setSignatureAppearance(180, 60, 15, 15);
				// $pdf->addEmptySignatureAppearance(180, 80, 15, 15);
				$pdf->useTemplate($tplId, 0, 0);

				$pdf->Output(public_path('contratos/'.$cnpj.'.pdf'), 'F');
				$empresa = Empresa::find($this->empresa_id);
				$contrato = $empresa->contrato;
				$contrato->status = 1;
				$contrato->save();
				session()->flash("mensagem_sucesso", "Contrato assinado!");
				return redirect('/graficos');
			}

		}catch(\Exception $e){
			echo $e->getMessage();
		}

	}
}
