<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContaReceber;
use App\Models\CategoriaConta;
use App\Models\Cliente;
use Dompdf\Dompdf;

class ContaReceberController extends Controller
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
		$contas = ContaReceber::
		where('empresa_id', $this->empresa_id)
		->whereBetween('data_vencimento', [date("Y-m-d"), 
			date('Y-m-d', strtotime('+1 month'))])
		->orderBy('data_vencimento', 'desc')
		->where('status', 0)
		->get();

		$categorias = CategoriaConta::
		where('empresa_id', $this->empresa_id)
		->where('tipo', 'receber')
		->get();

		$somaContas = $this->somaCategoriaDeContas($contas);
		return view('contaReceber/list')
		->with('contas', $contas)
		->with('graficoJs', true)
		->with('categorias', $categorias)
		->with('somaContas', $somaContas)
		->with('infoDados', "Dos próximos 30 dias")
		->with('title', 'Contas a Receber');
	}

	private function somaCategoriaDeContas($contas){
		$arrayCategorias = $this->criaArrayDecategoriaDeContas();
		$temp = [];
		foreach($contas as $c){
			foreach($arrayCategorias as $a){
				if($c->categoria->nome == $a){
					if(isset($temp[$a])){
						$temp[$a] = $temp[$a] + $c->valor_integral;
					}else{
						$temp[$a] = $c->valor_integral;
					}
				}
			}
		}

		return $temp;
	}

	private function criaArrayDecategoriaDeContas(){
		$categorias = CategoriaConta::
		where('empresa_id', $this->empresa_id)
		->where('tipo', 'receber')
		->get();
		$temp = [];
		foreach($categorias as $c){
			array_push($temp, $c->nome);
		}

		return $temp;
	}

	public function filtro(Request $request){
		
		$dataInicial = $request->data_inicial;
		$dataFinal = $request->data_final;
		$cliente = $request->cliente;
		$status = $request->status;
		$contas = [];

		$contas = ContaReceber::
		select('conta_recebers.*');

		if($cliente){
			$contas->join('clientes', 'clientes.id' , '=', 'conta_recebers.cliente_id');
			$contas->where('clientes.razao_social', 'LIKE', "%$cliente%");

		}
		if($dataInicial && $dataFinal){

			if($request->tipo_filtro_data == 1){
				$contas->whereBetween('conta_recebers.data_vencimento', 
					[
						$this->parseDate($dataInicial),
						$this->parseDate($dataFinal)
					]
				);
			}else{
				$contas->whereBetween('conta_recebers.created_at', 
					[
						$this->parseDate($dataInicial),
						$this->parseDate($dataFinal, true)
					]
				);
			}
		}
		
		if($status != 'todos'){
			if($status == 'pago'){
				$contas->where('status', true);
			} else if($status == 'pendente'){
				$contas->where('status', false);
			}
		}

		if($request->categoria != 'todos'){
			$contas->where('categoria_id', $request->categoria);
		}

		$contas->where('conta_recebers.empresa_id', $this->empresa_id);
		
		$contas = $contas->get();

		$somaContas = $this->somaCategoriaDeContas($contas);

		$categorias = CategoriaConta::
		where('empresa_id', $this->empresa_id)
		->where('tipo', 'receber')
		->get();


		return view('contaReceber/list')
		->with('contas', $contas)
		->with('cliente', $cliente)
		->with('categorias', $categorias)
		->with('tipo_filtro_data', $request->tipo_filtro_data)
		->with('categoria', $request->categoria)
		->with('dataInicial', $dataInicial)
		->with('dataFinal', $dataFinal)
		->with('status', $status)
		->with('somaContas', $somaContas)
		->with('graficoJs', true)
		->with('paraImprimir', true)
		->with('infoDados', "Contas filtradas")
		->with('title', 'Filtro Contas a Receber');
	}

	public function salvarParcela(Request $request){
		$parcela = $request->parcela;

		$valorParcela = str_replace(".", "", $parcela['valor_parcela']);
		$valorParcela = str_replace(",", ".", $valorParcela);

		$result = ContaReceber::create([
			'venda_id' => $parcela['compra_id'],
			'data_vencimento' => $this->parseDate($parcela['vencimento']),
			'data_recebimento' => $this->parseDate($parcela['vencimento']),
			'valor_integral' => $valorParcela,
			'valor_recebido' => 0,
			'status' => false,
			'referencia' => $parcela['referencia'],
			'categoria_id' => 1,
			'empresa_id' => $this->empresa_id
		]);
		echo json_encode($parcela);
	}

	public function save(Request $request){
		
		if(strlen($request->recorrencia) == 5){
			echo $request->recorrencia;
			$valid = $this->validaRecorrencia($request->recorrencia);
			if(!$valid){
				session()->flash('mensagem_erro', 'Valor recorrente inválido!');
				return redirect('/contasReceber/new');
			}
		}
		$clienteId = NULL;
		if($request->cliente_id != ""){
			$clienteId = $request->cliente_id;
		}

		$this->_validate($request);
		$result = ContaReceber::create([
			'venda_id' => null,
			'data_vencimento' => $this->parseDate($request->vencimento),
			'data_recebimento' => $this->parseDate($request->vencimento),
			'valor_integral' => str_replace(",", ".", $request->valor),
			'valor_recebido' => $request->status ? str_replace(",", ".", $request->valor) : 0,
			'status' => $request->status ? true : false,
			'referencia' => $request->referencia,
			'categoria_id' => $request->categoria_id,
			'empresa_id' => $this->empresa_id,
			'cliente_id' => $clienteId
		]);
		
		$loopRecorrencia = $this->calculaRecorrencia($request->recorrencia);
		if($loopRecorrencia > 0){
			$diaVencimento = substr($request->vencimento, 0, 2);
			$proximoMes = substr($request->vencimento, 3, 2);
			$ano = substr($request->vencimento, 6, 4);

			while($loopRecorrencia > 0){
				$proximoMes = $proximoMes == 12 ? 1 : $proximoMes+1;
				$proximoMes = $proximoMes < 10 ? "0".$proximoMes : $proximoMes;
				if($proximoMes == 1)  $ano++;
				$d = $diaVencimento . "/".$proximoMes . "/" . $ano;

				$result = ContaReceber::create([
					'venda_id' => null,
					'data_vencimento' => $this->parseDate($d),
					'data_recebimento' => $this->parseDate($d),
					'valor_integral' => str_replace(",", ".", $request->valor),
					'valor_recebido' => 0,
					'status' => false,
					'referencia' => $request->referencia,
					'categoria_id' => $request->categoria_id,
					'empresa_id' => $this->empresa_id
				]);
				$loopRecorrencia--;
			}
		}

		session()->flash('mensagem_sucesso', 'Registro inserido!');

		return redirect('/contasReceber');
	}

	public function update(Request $request){
		$this->_validate($request);
		$conta = ContaReceber::
		where('id', $request->id)
		->first();

		$conta->data_vencimento = $this->parseDate($request->vencimento);
		$conta->referencia = $request->referencia;
		$conta->valor_integral = str_replace(",", ".", $request->valor);
		$conta->categoria_id = $request->categoria_id;
		if(isset($request->cliente_id)){
			$conta->cliente_id = $request->cliente_id;
		}

		$result = $conta->save();

		if($result){

			session()->flash('mensagem_sucesso', 'Registro atualizado!');
		}else{

			session()->flash('mensagem_erro', 'Ocorreu um erro!');
		}

		return redirect('/contasReceber');

	}

	private function calculaRecorrencia($recorrencia){
		if(strlen($recorrencia) == 5){
			$dataAtual = date("Y-m");
			$dif = strtotime($this->parseRecorrencia($recorrencia)) - strtotime($dataAtual);

			$meses = floor($dif / (60 * 60 * 24 * 30));

			return $meses;
		}
		return 0;
	}

	public function validaRecorrencia($rec){
		$mesAutal = date('m');
		$anoAtual = date('y');
		$temp = explode("/", $rec);

		if($anoAtual > $temp[1]) return false;
		if((int)$temp[0] <= $mesAutal && $anoAtual == $temp[1]) return false;

		return true;
	}

	private function _validate(Request $request){
		$rules = [
			'referencia' => 'required',
			'valor' => 'required',
			'categoria_id' => 'required',
			'vencimento' => 'required',
		];

		$messages = [
			'referencia.required' => 'O campo referencia é obrigatório.',
			'valor.required' => 'O campo valor é obrigatório.',
			'categoria_id.required' => 'O campo categoria é obrigatório.',
			'vencimento.required' => 'O campo vencimento é obrigatório.'
		];
		$this->validate($request, $rules, $messages);
	}

	public function new(){
		$categorias = CategoriaConta::
		where('empresa_id', $this->empresa_id)
		->where('tipo', 'receber')
		->get();

		$clientes = Cliente::
		where('empresa_id', $this->empresa_id)
		->get();

		return view('contaReceber/register')
		->with('categorias', $categorias)
		->with('clientes', $clientes)
		->with('title', 'Cadastrar Contas a Receber');
	}

	public function edit($id){
		$categorias = CategoriaConta::
		where('empresa_id', $this->empresa_id)
		->where('tipo', 'receber')
		->get();

		$conta = ContaReceber::
		where('id', $id)
		->first();

		if($conta->venda_caixa_id != null){
			$conta->cliente_id = $conta->vendaCaixa->cliente_id;
			$conta->save();
		}
		if($conta->venda_id != null){
			$conta->cliente_id = $conta->venda->cliente_id;
			$conta->save();
		}

		$conta = ContaReceber::
		where('id', $id)
		->first();

		if($conta->boleto){
			session()->flash('mensagem_erro', 'Conta já possui boleto emitido!');
			return redirect('/contasReceber');
		}

		$clientes = Cliente::
		where('empresa_id', $this->empresa_id)
		->get();

		if(valida_objeto($conta)){
			return view('contaReceber/register')
			->with('conta', $conta)
			->with('categorias', $categorias)
			->with('clientes', $clientes)
			->with('title', 'Editar Contas a Pagar');
		}else{
			return redirect('/403');
		}
	}

	public function receber($id){
		$categorias = CategoriaConta::
		where('empresa_id', $this->empresa_id)
		->where('tipo', 'receber')
		->get();
		$conta = ContaReceber::
		where('id', $id)
		->first();

		if(valida_objeto($conta)){
			return view('contaReceber/receber')
			->with('conta', $conta)
			->with('categorias', $categorias)
			->with('title', 'Receber Conta');
		}else{
			return redirect('/403');
		}
	}

	public function receberConta(Request $request){
		$conta = ContaReceber::
		where('id', $request->id)
		->first();
		if(valida_objeto($conta)){
			// $valor = str_replace(".", "", $request->valor);
			// $valor = str_replace(",", ".", $valor);

			// $conta->status = true;
			// $conta->valor_recebido = $request->valor;
			// $conta->data_recebimento = date("Y-m-d");

			// $result = $conta->save();
			// if($result){

			// 	session()->flash('mensagem_sucesso', 'Conta recebida!');
			// }else{

			// 	session()->flash('mensagem_erro', 'Erro!');
			// }
			// return redirect('/contasReceber');



			// inicio codigo valor divergente 

			if($conta->valor_integral != $request->valor){
				$valor = __replace($request->valor);

				if($conta->venda_id != null){
					$contasParaReceber = ContaReceber::
					select('conta_recebers.*')
					->join('vendas', 'vendas.id' , '=', 'conta_recebers.venda_id')

					->where('conta_recebers.status', false)
					->where('conta_recebers.id', '!=', $conta->id)
					->where('vendas.cliente_id', $conta->venda->cliente_id)
					->get();

					if($conta->valor_integral > $request->valor){
						$contasParaReceber = [];
					}

					return view('contaReceber/valorDivergente')
					->with('conta', $conta)
					->with('valor', $valor)
					->with('receberConta', true)
					->with('contasParaReceber', $contasParaReceber)
					->with('title', 'Receber Conta');
				}else{

					$contasParaReceber = [];


					return view('contaReceber/valorDivergente')
					->with('conta', $conta)
					->with('valor', $valor)
					->with('receberConta', true)
					->with('contasParaReceber', $contasParaReceber)
					->with('title', 'Receber Conta');

				}

			}else{

				$conta->status = true;
				$conta->valor_recebido = $request->valor;
				$conta->data_recebimento = date("Y-m-d");

				$result = $conta->save();
				if($result){
					session()->flash('mensagem_sucesso', 'Conta recebida!');
				}else{

					session()->flash('mensagem_erro', 'Erro!');
				}
				return redirect('/contasReceber');
			}

			// fim codigo valor divergente 
		}else{
			return redirect('/403');
		}
	}

	public function delete($id){
		$conta = ContaReceber
		::where('id', $id)
		->first();
		if($conta->venda_id != null){
			session()->flash('mensagem_erro', 'Esta conta esta vinculada a uma venda!');
			return redirect('/contasReceber');
		}
		
		if($conta->boleto){
			session()->flash('mensagem_erro', 'Conta já possui boleto emitido!');
			return redirect('/contasReceber');
		}
		
		if(valida_objeto($conta)){
			if($conta->delete()){

				session()->flash('mensagem_sucesso', 'Registro removido!');
			}else{

				session()->flash('mensagem_erro', 'Erro!');
			}
			return redirect()->back();
		}else{
			return redirect('/403');
		}
	}

	private function parseDate($date, $plusDay = false){
		if($plusDay == false)
			return date('Y-m-d', strtotime(str_replace("/", "-", $date)));
		else
			return date('Y-m-d', strtotime("+1 day",strtotime(str_replace("/", "-", $date))));
	}

	private function parseRecorrencia($rec){
		$temp = explode("/", $rec);
		$rec = "01/".$temp[0]."/20".$temp[1];
		//echo $rec;
		return date('Y-m', strtotime(str_replace("/", "-", $rec)));
	}


	public function relatorio(Request $request){
		$dataInicial = $request->data_inicial;
		$dataFinal = $request->data_final;
		$cliente = $request->cliente;
		$status = $request->status;
		$contas = null;

		$contas = ContaReceber::
		select('conta_recebers.*');


		if($cliente){
			$contas->join('clientes', 'clientes.id' , '=', 'conta_recebers.cliente_id');
			$contas->where('clientes.razao_social', 'LIKE', "%$cliente%");

		}
		if($dataInicial && $dataFinal){

			if($request->tipo_filtro_data == 1){
				$contas->whereBetween('conta_recebers.data_vencimento', 
					[
						$this->parseDate($dataInicial),
						$this->parseDate($dataFinal)
					]
				);
			}else{
				$contas->whereBetween('conta_recebers.created_at', 
					[
						$this->parseDate($dataInicial),
						$this->parseDate($dataFinal, true)
					]
				);
			}
		}
		
		if($status != 'todos'){
			if($status == 'pago'){
				$contas->where('status', true);
			} else if($status == 'pendente'){
				$contas->where('status', false);
			}
		}

		if($request->categoria != 'todos'){
			$contas->where('categoria_id', $request->categoria);
		}

		$contas = $contas->get();


		$p = view('relatorios/relatorio_contas_receber')
		->with('data_inicial', $request->data_inicial)
		->with('data_final', $request->data_final)
		->with('contas', $contas);

		// return $p;

		$domPdf = new Dompdf(["enable_remote" => true]);
		$domPdf->loadHtml($p);

		$pdf = ob_get_clean();

		$domPdf->setPaper("A4", "landscape");
		$domPdf->render();
		$domPdf->stream("relatorio contas a receber.pdf");

	}

	public function receberSomente(Request $request){
		$conta = ContaReceber::find($request->id);
		$valor = __replace($request->valor);

		$conta->status = true;
		$conta->valor_recebido = $request->valor;
		$conta->data_recebimento = date("Y-m-d");

		$result = $conta->save();
		if($result){

			session()->flash('mensagem_sucesso', 'Conta recebida!');
		}else{

			session()->flash('mensagem_erro', 'Erro!');
		}
		return redirect('/contasReceber');
	}

	public function receberComDivergencia(Request $request){
		$conta = ContaReceber::find($request->id);
		$valor = __replace($request->valor);

		$res = ContaReceber::create([
			'venda_id' => $conta->venda_id,
			'venda_caixa_id' => $conta->venda_caixa_id,
			'cliente_id' => $conta->cliente_id,
			'data_vencimento' => $conta->data_vencimento,
			'data_recebimento' => $conta->data_recebimento,
			'valor_integral' => $conta->valor_integral - $valor,
			'valor_recebido' => 0,
			'status' => false,
			'referencia' => $conta->referencia,
			'categoria_id' => $conta->categoria_id,
			'empresa_id' => $this->empresa_id,
		]);

		$conta->status = true;
		$conta->valor_recebido = $request->valor;
		$conta->valor_integral = $request->valor;
		$conta->data_recebimento = date("Y-m-d");

		$result = $conta->save();
		if($result){
			$id = $res->id;
			session()->flash('mensagem_sucesso', 'Conta recebida parcialmente, uma nova foi criada com ID: ' . $id);
		}else{

			session()->flash('mensagem_erro', 'Erro!');
		}
		return redirect('/contasReceber');
	}

	public function receberComOutros(Request $request){
		$conta = ContaReceber::find($request->id);
		$valor = $request->valor;
		$temp = "";
		$somaParaTroco = $conta->valor_integral;
		try{
			if(isset($request->contas)){
				$contasMais = explode(",", $request->contas);
				print_r($contasMais);
				foreach($contasMais as $key => $c){
					$ctemp = ContaReceber::find($c);
					$ctemp->status = true;
					$ctemp->valor_recebido = $ctemp->valor_integral;
					$ctemp->data_recebimento = date("Y-m-d");
					$ctemp->save();

					$temp .= " $c" . (sizeof($contasMais)-1 > $key ? "," : "");

					$somaParaTroco += $ctemp->valor_integral;
				}


			}

			$conta->status = true;
			$conta->valor_recebido = $conta->valor_integral;
			$conta->data_recebimento = date("Y-m-d");
			$conta->save();

			$troco = $valor - $somaParaTroco;

			$msg = "Sucesso conta(s) com ID: $conta->id, " . $temp . " recebida(s)";

			if($troco > 0){
				$msg .= " , valor de troco: R$ " . number_format($troco, 2);
			}
			session()->flash('mensagem_sucesso', $msg);

			return redirect('/contasReceber');

		}catch(\Exception $e){
			session()->flash('mensagem_erro', 'Ocorreu um erro ao receber: ' . $e->getMessage());

		}
	}

	public function detalhesVenda($contaId){
		$conta = ContaReceber::find($contaId);
		if(valida_objeto($conta)){

			if($conta->venda_id != null){
				// venda nfe
				return redirect('/vendas/detalhar/'.$conta->venda_id);
			}else{
				// venda pdv
				return redirect('/nfce/detalhes/'.$conta->venda_caixa_id);
			}
		}else{
			return redirect('/403');
		}
	}
}
