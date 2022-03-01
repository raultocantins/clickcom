<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venda;
use App\Models\ConfigNota;
use Dompdf\Dompdf;

class CarneController extends Controller
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
		$venda = Venda::find($request->id);

		$juros = __replace($request->juros);
		$multa = __replace($request->multa);

		foreach($venda->duplicatas as $dp){
			$dp->juros = $juros;
			$dp->multa = $multa;
			$dp->save();
		}
		$config = ConfigNota::
		where('empresa_id', $venda->empresa_id)
		->first();

		$p = view('vendas/carne')
		->with('config', $config)
		->with('venda', $venda);

		// return $p;

		$domPdf = new Dompdf(["enable_remote" => true]);
		$domPdf->loadHtml($p);

		$pdf = ob_get_clean();

		$domPdf->setPaper("A4");
		$domPdf->render();
		$domPdf->stream("relatorio_venda.pdf");

	}
}
