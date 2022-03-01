<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Remessa;
use App\Models\Boleto;
use App\Models\ContaReceber;
use App\Models\Empresa;
use App\Helpers\BoletoHelper;

class RemessaController extends Controller
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
		$remessas = Remessa::orderBy('id', 'desc')
		->where('empresa_id', $this->empresa_id)
		->get();

		return view('boletos/remessas')
		->with('remessas', $remessas)
		->with('title', 'Remessas');
	}

	public function boletosSemRemessa(){
		$boletos = Boleto::
		select('boletos.*')
		->join('conta_recebers', 'conta_recebers.id' , '=', 'boletos.conta_id')
		->orderBy('boletos.id', 'desc')
		->where('conta_recebers.empresa_id', $this->empresa_id)
		->limit(100)
		->get();

		$temp = [];
		foreach($boletos as $b){
			if(!$b->itemRemessa){
				array_push($temp, $b);
			}
		}

		return view('boletos/boletos_sem_remessa')
		->with('boletos', $temp)
		->with('title', 'Boletos sem remessa');
	}

	public function gerarRemessaMulti($boletos){
		$boletos = explode(",", $boletos);
		$temp = [];
		
		foreach($boletos as $b){
			$boleto = Boleto::find($b);
			if(!$boleto->itemRemessa){
				array_push($temp, $boleto);
			}else{
				session()->flash("mensagem_erro", "Algum dos boletos selecionados esta com remessa gerada!");
				return redirect()->back();
			}
		}

		$bancoId = $temp[0]->banco_id;

		foreach($temp as $t){
			if($t->banco_id != $bancoId){
				session()->flash("mensagem_erro", "Informe os boletos para o mesmo banco para gerar a remessa!");
				return redirect()->back();
			}
		}

		$empresa = Empresa::find($this->empresa_id);
		$boletoHelper = new BoletoHelper($empresa);

		$result = $boletoHelper->gerarRemessaMulti($temp);

	}

	public function ver($id){
		$remessa = Remessa::find($id);

		return view('boletos/ver_remessa')
		->with('remessa', $remessa)
		->with('title', 'Boletos sem remessa');
	}

	public function delete($id){
		try{
			$remessa = Remessa::find($id)->delete();

			session()->flash("mensagem_sucesso", "Remessa removida!");
		}catch(\Exception $e){
			session()->flash("mensagem_erro", "Erro ao remover: " . $e->getMessage());
		}
		return redirect()->back();
	}

	public function download($id){
		try{
			$remessa = Remessa::find($id);
			if(valida_objeto($remessa)){
				$public = getenv('SERVIDOR_WEB') ? 'public/' : '';

				$file = public_path('remessas')."/$remessa->nome_arquivo.txt";
				if(file_exists($file)){
					header('Content-Type: application/txt');
					header('Content-Disposition: attachment; filename="'.$remessa->nome_arquivo.'.txt"');
					readfile($file);
				}else{
					session()->flash("mensagem_erro", "Arquivo não encontrado!!");
					return redirect('/contasReceber');
				}
			}else{
				return redirect('/403');
			}
		}catch(\Exception $e){
			session()->flash("mensagem_erro", "Erro ao baixar: " . $e->getMessage());
		}
	}

}
