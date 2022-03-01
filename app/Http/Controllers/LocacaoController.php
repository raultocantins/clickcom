<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Locacao;
use App\Models\ConfigNota;
use App\Models\ItemLocacao;
use Dompdf\Dompdf;
use Dompdf\Options;

class LocacaoController extends Controller
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
		$locacoes = Locacao::
		where('empresa_id', $this->empresa_id)
		->orderBy('id', 'desc')
		->get();

		return view('locacao/list')
		->with('locacoes', $locacoes)
		->with('title', 'Locações');
	}

	public function pesquisa(Request $request){
		$locacoes = Locacao::
		select('locacaos.*')
		->where('locacaos.empresa_id', $this->empresa_id);

		if($request->cliente){
			$locacoes->join('clientes', 'clientes.id', '=', 'locacaos.cliente_id')
			->where('clientes.razao_social', 'LIKE', "%$request->cliente%");
		}

		if($request->data_inicial && $request->data_final){

			$dataInicial = $this->parseDate($request->data_inicial);
			$dataFinal = $this->parseDate($request->data_final, true);
			$locacoes->whereBetween('inicio', [
				$dataInicial, 
				$dataFinal
			]);
		}

		if($request->estado){
			$locacoes->where('status', $request->estado);
		}

		$locacoes = $locacoes->orderBy('locacaos.id', 'desc')
		->get();

		return view('locacao/list')
		->with('locacoes', $locacoes)
		->with('cliente', $request->cliente)
		->with('dataInicial', $request->data_inicial)
		->with('dataFinal', $request->data_final)
		->with('estado', $request->estado)
		->with('pesquisa', true)
		->with('title', 'Locações');
	}

	public function relatorio(Request $request){
		$locacoes = Locacao::
		select('locacaos.*')
		->where('locacaos.empresa_id', $this->empresa_id);

		if($request->cliente){
			$locacoes->join('clientes', 'clientes.id', '=', 'locacaos.cliente_id')
			->where('clientes.razao_social', 'LIKE', "%$request->cliente%");
		}

		if($request->data_inicial && $request->data_final){

			$dataInicial = $this->parseDate($request->data_inicial);
			$dataFinal = $this->parseDate($request->data_final, true);
			$locacoes->whereBetween('inicio', [
				$dataInicial, 
				$dataFinal
			]);
		}

		if($request->estado){
			$locacoes->where('status', $request->estado);
		}

		$locacoes = $locacoes->orderBy('locacaos.id', 'desc')
		->get();

		$config = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();

		$p = view('locacao/print')
		->with('locacoes', $locacoes)
		->with('cliente', $request->cliente)
		->with('dataInicial', $request->data_inicial)
		->with('dataFinal', $request->data_final)
		->with('estado', $request->estado)
		->with('config', $config);

		// return $p;

		$options = new Options();
		$options->set('isRemoteEnabled', TRUE);
		$domPdf = new Dompdf($options);

		$domPdf->loadHtml($p);


		$domPdf->setPaper("A4");
		$domPdf->render();
			// $domPdf->stream("orcamento.pdf", ["Attachment" => false]);
		$domPdf->stream("relatorio_locacao.pdf");

		
	}

	public function novo(){

		$clientes = Cliente::
		where('empresa_id', $this->empresa_id)
		->get();

		$config = ConfigNota::
		where('empresa_id', $this->empresa_id)
		->first();

		return view('locacao/register')
		->with('title', 'Nova locação')
		->with('config', $config)
        ->with('pessoaFisicaOuJuridica', true)
		->with('clientes', $clientes);

	}

	public function edit($id){

		$clientes = Cliente::
		where('empresa_id', $this->empresa_id)
		->get();

		$locacao = Locacao::find($id);

		if(valida_objeto($locacao)){

			return view('locacao/register')
			->with('title', 'Editar locação')
			->with('locacao', $locacao)
			->with('clientes', $clientes);
		}else{
			return redirect('/403');
		}

	}

	public function salvar(Request $request){
		$this->_validate($request);

		if($request->id > 0){
			//update

			$locacao = Locacao::find($request->id);

			$locacao->observacao = $request->observacao ?? '';
			$locacao->inicio = $this->parseDate($request->inicio);
			$locacao->fim = $this->parseDate($request->fim);
			$locacao->cliente_id = $request->cliente_id;

			try{

				$locacao->save();
				return redirect('/locacao/itens/'. $locacao->id);
			}catch(\Exception $e){
				session()->flash('mensagem_erro', 'Erro: ' . $e->getMessage());
				return redirect()->back();
			}
		}else{

			$request->merge(['observacao' => $request->observacao ?? '']);
			$request->merge(['inicio' => $this->parseDate($request->inicio) ]);
			$request->merge(['fim' => $this->parseDate($request->fim) ]);
			try{

				$l = Locacao::create($request->all());
				return redirect('/locacao/itens/'. $l->id);
			}catch(\Exception $e){
				session()->flash('mensagem_erro', 'Erro: ' . $e->getMessage());
				return redirect()->back();
			}
		}

	}

	private function parseDate($date, $plusDay = false){
		if($plusDay == false)
			return date('Y-m-d', strtotime(str_replace("/", "-", $date)));
		else
			return date('Y-m-d', strtotime("+1 day",strtotime(str_replace("/", "-", $date))));
	}

	private function _validate(Request $request){
		$rules = [
			'cliente_id' => 'required|numeric|min:1',
			'inicio' => 'required',
			'fim' => 'required'
		];

		$messages = [
			'cliente_id.required' => 'O campo cliente é obrigatório.',
			'cliente_id.min' => 'O campo cliente é obrigatório.',
			'inicio.required' => 'O campo inicio é obrigatório.',
			'fim.required' => 'O campo fim é obrigatório.',
		];
		$this->validate($request, $rules, $messages);
	}

	public function itens($id){
		$locacao = Locacao::find($id);
		if(valida_objeto($locacao)){

			$produtos = Produto::
			where('empresa_id', $this->empresa_id)
			->where('valor_locacao', '>', 0)
			->get();

			return view('locacao/itens')
			->with('title', 'Locação itens')
			->with('produtos', $produtos)
			->with('locacao', $locacao);
		}else{
			return redirect('/403');
		}
	}

	public function validaEstoque($produto_id, $locacao_id){
		try{
			$produto = Produto::find($produto_id);
			$locacao = Locacao::find($locacao_id);

			$estoqueTotal = $produto->estoqueAtual();

			$itensUsados = ItemLocacao::
			select('item_locacaos.*')
			->join('locacaos', 'locacaos.id', '=', 'item_locacaos.locacao_id')
			->where('locacaos.status', 0)
			->whereBetween('locacaos.inicio', [
				$locacao->inicio . " 00:00:00", 
				$locacao->fim . " 23:59:00"
			])
			->where('item_locacaos.produto_id', $produto_id)
			->get();

			$valor_locacao = $produto->valor_locacao;
			$qtdDisponivel = $produto->estoqueAtual() - sizeof($itensUsados);

			$arr = [
				'valor_locacao' => $valor_locacao,
				'quantidade' => $qtdDisponivel
			];

			return response()->json($arr, 200);

		}catch(\Exception $e){
			return response()->json("erro: ". $e->getMessage(), 401);
		}

	}

	public function salvarItem(Request $request){
		$this->_validateItem($request);

		$request->merge(['observacao' => $request->observacao ?? '']);
		try{

			$locacao = Locacao::find($request->locacao_id);
			$l = ItemLocacao::create($request->all());

			$locacao->total = $this->somaItens($locacao);
			$locacao->save();
			session()->flash('mensagem_sucesso', 'Item adicionado');

			return redirect()->back();
		}catch(\Exception $e){
			session()->flash('mensagem_erro', 'Erro: ' . $e->getMessage());
			return redirect()->back();
		}
	}

	private function somaItens($locacao){
		$total = 0;
		foreach($locacao->itens as $i){
			$total += $i->valor;
		}
		return $total;
	}

	public function delete($id){
		$locacao = Locacao::find($id);

		if(valida_objeto($locacao)){

			$locacao->delete();

			session()->flash('mensagem_sucesso', 'Registro removido');
			return redirect()->back();
		}else{
			return redirect('/403');
		}

	}

	public function saveObs(Request $request){
		try{
			$locacao = Locacao::find($request->id);

			$locacao->observacao = $request->observacao;
			$locacao->save();
			session()->flash('mensagem_sucesso', 'Registro removido');
		}catch(\Exception $e){
			session()->flash('mensagem_erro', 'Erro: ' . $e->getMessage());
		}

		return redirect()->back();

	}

	public function deleteItem($id){
		$item = ItemLocacao::find($id);
		$locacao = Locacao::find($item->locacao_id);
		if(valida_objeto($locacao)){

			$item->delete();
			$locacao->total = $this->somaItens($locacao);
			$locacao->save();
			session()->flash('mensagem_sucesso', 'Item removido');

			return redirect()->back();
		}else{
			return redirect('/403');
		}

	}

	private function _validateItem(Request $request){
		$rules = [
			'produto_id' => 'required|numeric|min:1',
			'valor' => 'required'
		];

		$messages = [
			'produto_id.required' => 'O campo produto é obrigatório.',
			'produto_id.min' => 'O campo produto é obrigatório.',
			'valor.required' => 'O campo valor é obrigatório.',
		];
		$this->validate($request, $rules, $messages);
	}

	public function alterarStatus($id){
		try{
			$locacao = Locacao::find($id);
			if(valida_objeto($locacao)){

				$locacao->status = true;
				$locacao->save();
				session()->flash('mensagem_sucesso', 'Status alterado');

				return redirect()->back();
			}else{
				return redirect('/403');
			}
		}catch(\Exception $e){
			session()->flash('mensagem_erro', 'Erro: ' . $e->getMessage());
			return redirect()->back();
		}
	}

	public function comprovante($id){
		try{
			$locacao = Locacao::find($id);
			if(valida_objeto($locacao)){

				$config = ConfigNota::
				where('empresa_id', $this->empresa_id)
				->first();

				$p = view('locacao/comprovante2')
				->with('config', $config)
				->with('locacao', $locacao);

				// return $p;

				$options = new Options();
				$options->set('isRemoteEnabled', TRUE);
				$domPdf = new Dompdf($options);

				$domPdf->loadHtml($p);


				$domPdf->setPaper("A4");
				$domPdf->render();
			// $domPdf->stream("orcamento.pdf", ["Attachment" => false]);
				$domPdf->stream("relatorio_locacao_$locacao->id.pdf");

			}else{
				return redirect('/403');
			}
		}catch(\Exception $e){
			session()->flash('mensagem_erro', 'Erro: ' . $e->getMessage());
			return redirect()->back();
		}
	}

}
