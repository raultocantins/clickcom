<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contrato;
use App\Models\Empresa;
use App\Models\EmpresaContrato;
use Dompdf\Dompdf;

class ContratoController extends Controller
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

		$contrato = Contrato::first();

		return view('contrato/register')
		->with('contratoJs', true)
		->with('contract', $contrato)
		->with('title', 'Contrato');
	}

	public function save(Request $request){
		Contrato::create($request->all());

		session()->flash("mensagem_sucesso", "Contrato salvo!!");
		return redirect()->back();
	}

	public function update(Request $request){
		$contrato = Contrato::first();

		$contrato->texto = $request->texto;
		$contrato->save();

		session()->flash("mensagem_sucesso", "Contrato alterado!!");
		return redirect()->back();
	}

	public function impressao(){
		$contrato = Contrato::first();
		$texto = $contrato->texto;

		$domPdf = new Dompdf(["enable_remote" => true]);
		$domPdf->loadHtml($texto);

		$pdf = ob_get_clean();

		$domPdf->setPaper("A4");
		$domPdf->render();
		$domPdf->stream("contrato_modelo.pdf");
	}

	public function gerarContrato($empresa_id){
		try{
			$contrato = Contrato::first();

			if($contrato == null){
				session()->flash("mensagem_erro", "Cadastre o contrato!!");
				return redirect('/contrato');
			}
			$empresa = Empresa::find($empresa_id);

			$texto = $this->preparaTexto($contrato->texto, $empresa);

			$domPdf = new Dompdf(["enable_remote" => true]);
			$domPdf->loadHtml($texto);

			$pdf = ob_get_clean();

			$domPdf->setPaper("A4");
			$domPdf->render();
		// $domPdf->stream("contrato_modelo.pdf");
			$output = $domPdf->output();
			$cnpj = str_replace("/", "", $empresa->cnpj);
			$cnpj = str_replace(".", "", $cnpj);
			$cnpj = str_replace("-", "", $cnpj);
			$cnpj = str_replace(" ", "", $cnpj);

			if(!is_dir(public_path('contratos'))){
				mkdir(public_path('contratos'), 0777, true);
			}
			file_put_contents(public_path('contratos/'.$cnpj.'.pdf'), $output);

			EmpresaContrato::create(
				[
					'empresa_id' => $empresa->id, 'status' => 0
				]
			);

			session()->flash("mensagem_sucesso", "Contrato criado!");
			return redirect()->back();
		}catch(\Exception $e){
			echo $e->getMessage();
		}

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

	public function download($empresa_id){
		$empresa = Empresa::find($empresa_id);
		$cnpj = str_replace("/", "", $empresa->cnpj);
		$cnpj = str_replace(".", "", $cnpj);
		$cnpj = str_replace("-", "", $cnpj);
		$cnpj = str_replace(" ", "", $cnpj);
		return response()->download(public_path('contratos/'.$cnpj.'.pdf'));

	}
}
