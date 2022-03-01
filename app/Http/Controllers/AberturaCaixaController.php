<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AberturaCaixa;
use App\Models\VendaCaixa;
use App\Models\Venda;
use App\Models\SuprimentoCaixa;
use App\Models\SangriaCaixa;
use Dompdf\Dompdf;

class AberturaCaixaController extends Controller
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

	public function abrir(Request $request){

		$ultimaVendaNfce = VendaCaixa::
		where('empresa_id', $this->empresa_id)
		->orderBy('id', 'desc')->first();

		$ultimaVendaNfe = Venda::
		where('empresa_id', $this->empresa_id)
		->orderBy('id', 'desc')->first();

		$verify = $this->verificaAberturaCaixa();
		if($verify == -1){
			$result = AberturaCaixa::create([
				'usuario_id' => get_id_user(),
				'valor' => str_replace(",", ".", $request->valor),
				'empresa_id' => $this->empresa_id,
				'primeira_venda_nfe' => $ultimaVendaNfe != null ? 
				$ultimaVendaNfe->id : 0,
				'primeira_venda_nfce' => $ultimaVendaNfce != null ? 
				$ultimaVendaNfce->id : 0,
				'status' => 0
			]);
			echo json_encode($result);
		}else{
			echo json_encode(true);
		}
	}

	public function verificaHoje(){
		echo json_encode($this->verificaAberturaCaixa());
	}

	public function diaria(){
		date_default_timezone_set('America/Sao_Paulo');
		$hoje = date("Y-m-d") . " 00:00:00";
		$amanha = date('Y-m-d', strtotime('+1 days')). " 00:00:00";
		$abertura = AberturaCaixa::
		whereBetween('data_registro', [$hoje, 
			$amanha])
		->where('empresa_id', $this->empresa_id)
		->first();

		echo json_encode($abertura);
	}

	private function setUsuario($sangrias){
		for($aux = 0; $aux < count($sangrias); $aux++){
			$sangrias[$aux]['nome_usuario'] = $sangrias[$aux]->usuario->nome;
		}
		return $sangrias;
	}

	private function verificaAberturaCaixa(){

		$ab = AberturaCaixa::where('ultima_venda_nfce', 0)
		->where('empresa_id', $this->empresa_id)
		->where('status', 0)
		->orderBy('id', 'desc')->first();

		$ab2 = AberturaCaixa::where('ultima_venda_nfe', 0)
		->where('empresa_id', $this->empresa_id)
		->where('status', 0)
		->orderBy('id', 'desc')->first();

		// echo $ab;
		// die();

		if($ab != null && $ab2 == null){
			return $ab->valor;
		}else if($ab == null && $ab2 != null){
			$ab2->valor;
		}else if($ab != null && $ab2 != null){
			if(strtotime($ab->created_at) > strtotime($ab2->created_at)){
				$ab->valor;
			}else{
				$ab2->valor;
			}
		}else{
			return -1;
		}

		if($ab != null) return $ab->valor;
		else return -1;

		// date_default_timezone_set('America/Sao_Paulo');
		// $dataHoje = date("Y-m-d");
		// $ab = AberturaCaixa::
		// orderBy('id', 'desc')
		// ->first();
		// if($ab){
		// 	$ultimaDataAbertura = substr($ab->data_registro, 0, 10);
		// 	if($ultimaDataAbertura == $dataHoje) return $ab->valor;
		// 	else return -1;
		// }else{
		// 	return -1;
		// }
	}



	//view do caixa

	public function index(){

		$abertura = $this->verificaAberturaCaixa();
		$ultimaFechadaNfce = AberturaCaixa::where('ultima_venda_nfce', '>', 0)
		->where('empresa_id', $this->empresa_id)
		->orderBy('id', 'desc')->first();

		$ultimaFechadaNfe = AberturaCaixa::where('ultima_venda_nfe', '>', 0)
		->where('empresa_id', $this->empresa_id)
		->orderBy('id', 'desc')->first();

		$ultimaVendaNfce = VendaCaixa::
		where('empresa_id', $this->empresa_id)
		->orderBy('id', 'desc')->first();

		$ultimaVendaNfe = Venda::
		where('empresa_id', $this->empresa_id)
		->orderBy('id', 'desc')->first();

		$vendas = [];
		$somaTiposPagamento = [];

		$caixa = [];

		if($abertura != -1){
			$caixa = $this->getCaixaAberto();
		}
		
		$ab = AberturaCaixa::where('ultima_venda_nfce', 0)
		->where('ultima_venda_nfe', 0)
		->where('empresa_id', $this->empresa_id)
		->where('status', 0)
		->orderBy('id', 'desc')->first();

		return view('caixa/index')
		->with('vendas', $vendas)
		->with('abertura', $ab)
		->with('caixaJs', true)
		->with('caixa', $caixa)
		->with('title', 'Caixa');

	}

	private function getCaixaAberto(){
		$aberturaNfe = AberturaCaixa::where('ultima_venda_nfe', 0)
		->where('empresa_id', $this->empresa_id)
		->orderBy('id', 'desc')->first();

		// echo $aberturaNfe;
		// echo $this->empresa_id;
		// die();

		$aberturaNfce = AberturaCaixa::where('ultima_venda_nfce', 0)
		->where('empresa_id', $this->empresa_id)
		->orderBy('id', 'desc')->first();

		$ultimaVendaCaixa = VendaCaixa::
		where('empresa_id', $this->empresa_id)
		->orderBy('id', 'desc')->first();

		$ultimaVenda = Venda::
		where('empresa_id', $this->empresa_id)
		->orderBy('id', 'desc')->first();

		$vendas = [];
		$somaTiposPagamento = [];
		if($ultimaVendaCaixa != null || $ultimaVenda != null){
			$ultimaVendaCaixa = $ultimaVendaCaixa != null ? $ultimaVendaCaixa->id : 0;
			$ultimaVenda = $ultimaVenda != null ? $ultimaVenda->id : 0;

			$vendasPdv = VendaCaixa
			::whereBetween('id', [($aberturaNfce != null ? $aberturaNfce->primeira_venda_nfce+1 : 0), 
				$ultimaVendaCaixa])
			->where('empresa_id', $this->empresa_id)
			->get();

			$vendas = Venda
			::whereBetween('id', [($aberturaNfe != null ? $aberturaNfe->primeira_venda_nfe+1 : 0), 
				$ultimaVenda])
			->where('empresa_id', $this->empresa_id)
			->get();

			$vendas = $this->agrupaVendas($vendas, $vendasPdv);
			$somaTiposPagamento = $this->somaTiposPagamento($vendas);

		}

		$suprimentos = SuprimentoCaixa::
		whereBetween('created_at', [
			$aberturaNfe->created_at, 
			date('Y-m-d H:i:s')
		])
		->where('empresa_id', $this->empresa_id)
		->get();

		$sangrias = SangriaCaixa::
		whereBetween('created_at', [$aberturaNfe->created_at, 
			date('Y-m-d H:i:s')])
		->where('empresa_id', $this->empresa_id)
		->get();

		return [
			'vendas' => $vendas,
			'sangrias' => $sangrias,
			'suprimentos' => $suprimentos,
			'somaTiposPagamento' => $somaTiposPagamento
		];
	}

	private function agrupaVendas($vendas, $vendasPdv){
		$temp = [];
		foreach($vendas as $v){
			$v->tipo = 'VENDA';
			array_push($temp, $v);
		}

		foreach($vendasPdv as $v){
			$v->tipo = 'PDV';
			array_push($temp, $v);
		}

		return $temp;
	}

	private function somaTiposPagamento($vendas){
		$tipos = $this->preparaTipos();

		foreach($vendas as $v){

			if($v->estado != 'CANCELADO'){

				if(isset($tipos[$v->tipo_pagamento])){
				// $tipos[$v->tipo_pagamento] += $v->valor_total;
					
					if($v->tipo_pagamento != 99){
						$tipos[$v->tipo_pagamento] += $v->valor_total;
					}else{
						if($v->valor_pagamento_1 > 0){
							$tipos[$v->tipo_pagamento_1] += $v->valor_pagamento_1;
						}
						if($v->valor_pagamento_2 > 0){
							$tipos[$v->tipo_pagamento_2] += $v->valor_pagamento_2;
						}
						if($v->valor_pagamento_3 > 0){
							$tipos[$v->tipo_pagamento_3] += $v->valor_pagamento_3;
						}
					}
				}
			}
		}
		return $tipos;

	}

	private function preparaTipos(){
		$temp = [];
		foreach(VendaCaixa::tiposPagamento() as $key => $tp){
			$temp[$key] = 0;
		}
		return $temp;
	}

	public function list(){
		$aberturas = AberturaCaixa::
		where('empresa_id', $this->empresa_id)
		->where('ultima_venda_nfe', '>', 0)
		->orWhere('ultima_venda_nfce', '>', 0)
		->where('empresa_id', $this->empresa_id)
		->orderBy('id', 'desc')
		->get();

		return view('caixa/list')
		->with('aberturas', $aberturas)
		->with('title', 'Lista de Caixas');
	}

	public function filtro(Request $request){

		$aberturas = AberturaCaixa::
		where('empresa_id', $this->empresa_id)
		->whereBetween('created_at', [
			$this->parseDate($request->data_inicial), 
			$this->parseDate($request->data_final, true)
		])
		->where('ultima_venda_nfe', '>', 0)

		->orWhere('ultima_venda_nfce', '>', 0)
		->whereBetween('created_at', [
			$this->parseDate($request->data_inicial), 
			$this->parseDate($request->data_final, true)
		])
		->where('empresa_id', $this->empresa_id)

		->orderBy('id', 'desc')
		->get();

		return view('caixa/list')
		->with('aberturas', $aberturas)
		->with('dataInicial', $request->data_inicial)
		->with('dataFinal', $request->data_final)
		->with('title', 'Lista de Caixas');
	}

	private function parseDate($date, $plusDay = false){
		if($plusDay == false)
			return date('Y-m-d', strtotime(str_replace("/", "-", $date)));
		else
			return date('Y-m-d', strtotime("+1 day",strtotime(str_replace("/", "-", $date))));
	}

	public function detalhes($id){
		$abertura = AberturaCaixa::find($id);
		$aberturas = AberturaCaixa::
		where('empresa_id', $this->empresa_id)
		->get();

		if(valida_objeto($abertura)){

			$aberturaAnterior = AberturaCaixa::find($id-1);

			$fim = $abertura->updated_at;
			$inicio = $aberturaAnterior == null ? '2016-01-01' : $aberturaAnterior->updated_at;


			$vendasPdv = VendaCaixa
			::whereBetween('id', [
				$abertura->primeira_venda_nfce+1, 
				$abertura->ultima_venda_nfce
			])
			->where('empresa_id', $this->empresa_id)
			->get();

			$vendas = Venda
			::whereBetween('id', [
				$abertura->primeira_venda_nfe+1, 
				$abertura->ultima_venda_nfe
			])
			->where('empresa_id', $this->empresa_id)
			->get();

			$vendas = $this->agrupaVendas($vendas, $vendasPdv);
			$somaTiposPagamento = $this->somaTiposPagamento($vendas);

			$suprimentos = SuprimentoCaixa::
			whereBetween('created_at', [$inicio, 
				$fim])
			->where('empresa_id', $this->empresa_id)
			->get();

			$sangrias = SangriaCaixa::
			whereBetween('created_at', [$inicio, 
				$fim])
			->where('empresa_id', $this->empresa_id)
			->get();

			return view('caixa/detalhes')
			->with('abertura', $abertura)
			->with('vendas', $vendas)
			->with('suprimentos', $suprimentos)
			->with('sangrias', $sangrias)
			->with('somaTiposPagamento', $somaTiposPagamento)
			->with('title', 'Detalhes Caixa');
		}else{
			return redirect('/403');
		}
	}

	public function imprimir($id){
		$abertura = AberturaCaixa::find($id);
		$aberturas = AberturaCaixa::
		where('empresa_id', $this->empresa_id)
		->get();

		if(valida_objeto($abertura)){

			$aberturaAnterior = AberturaCaixa::find($id-1);

			$fim = $abertura->updated_at;
			$inicio = $aberturaAnterior == null ? '2016-01-01' : $aberturaAnterior->updated_at;


			$vendasPdv = VendaCaixa
			::whereBetween('id', [
				$abertura->primeira_venda_nfce+1, 
				$abertura->ultima_venda_nfce
			])
			->where('empresa_id', $this->empresa_id)
			->get();

			$vendas = Venda
			::whereBetween('id', [
				$abertura->primeira_venda_nfe+1, 
				$abertura->ultima_venda_nfe
			])
			->where('empresa_id', $this->empresa_id)
			->get();

			$vendas = $this->agrupaVendas($vendas, $vendasPdv);
			$somaTiposPagamento = $this->somaTiposPagamento($vendas);

			$suprimentos = SuprimentoCaixa::
			whereBetween('created_at', [$inicio, 
				$fim])
			->where('empresa_id', $this->empresa_id)
			->get();

			$sangrias = SangriaCaixa::
			whereBetween('created_at', [$inicio, 
				$fim])
			->where('empresa_id', $this->empresa_id)
			->get();

			$p = view('caixa/relatorio')
			->with('abertura', $abertura)
			->with('vendas', $vendas)
			->with('suprimentos', $suprimentos)
			->with('sangrias', $sangrias)
			->with('somaTiposPagamento', $somaTiposPagamento)
			->with('title', 'Detalhes Caixa');

			// return $p;

			$domPdf = new Dompdf(["enable_remote" => true]);
			$domPdf->loadHtml($p);

			$pdf = ob_get_clean();

			$domPdf->setPaper("A4", "landscape");
			$domPdf->render();
			$domPdf->stream("relatorio caixa.pdf");
		}else{
			return redirect('/403');
		}
	}

}
