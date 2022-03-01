<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\Categoria;
use App\Models\Produto;
use Illuminate\Support\Str;

class MigradorController extends Controller
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
	
	public function index($empresa_id){

		return view('migrador/index')
		->with('empresa_id', $empresa_id)
		->with('title', 'Migrador');
	}

	public function save(Request $request){
		if ($request->hasFile('file')) {

			$empresa_id = $request->empresa_id;
			$data = file_get_contents($request->file);
			$collection = explode("\n", $data);

			if(str_contains($data, 'MySQL-Front 6.0')){

				$ProdNaoInserido = 0;
				$CliNaoInserido = 0;
				foreach($collection as $c){
					if(str_contains($c, 'INSERT')){

						$linhas = explode("(", $c);
						array_pop($linhas);
						if(str_contains($linhas[0], 'tbl_cliente')){

							foreach($linhas as $l){
								$temp = explode(",", $l);

								// echo "<pre>";
								// print_r($temp);
								// echo "</pre>";
								if(!str_contains($l, 'tbl_cliente')){
									$res = $this->insereCliente2($temp, $empresa_id);
									
									if($res == false){
										$CliNaoInserido++;
									}

									// echo $res; 
									// die;

								}
							}
						}

						if(str_contains($linhas[0], 'tbl_produto')){

							foreach($linhas as $l){
								$temp = explode(",", $l);

								// echo "<pre>";
								// print_r($temp);
								// echo "</pre>";
								if(!str_contains($l, 'tbl_produto')){
									$res = $this->insereProduto2($temp, $empresa_id);
									if($res == false) $ProdNaoInserido++;
								}
							}
						}

						// die();

					}

				}
			}else{

				foreach($collection as $c){

					if(str_contains($c, 'INSERT')){
						if(str_contains($c, 'tbl_cliente')){
							$this->insereCliente($c, $empresa_id);
						}

						if(str_contains($c, 'tbl_produto')){

							$this->insereProduto($c, $empresa_id);
						}
					}
				}
			}
			$msg = 'Sucesso! ';
			if($ProdNaoInserido > 0){
				$msg .= " porém $ProdNaoInserido produto(s) não inserido(s)";
			}
			if($CliNaoInserido > 0){
				$msg .= " porém $CliNaoInserido cliente(s) não inserido(s)";
			}

			session()->flash('mensagem_sucesso', $msg);

			return redirect('/migrador/'.$empresa_id);
		}else{
			session()->flash('mensagem_erro', 'Nenhum Arquivo!!');
			return redirect()->back();
		}
	}

	private function insereCliente($linha, $empresa_id){

		$arr = explode(",", $linha);
		
		$cidade = Cidade::where('codigo', $this->__replace($arr[16]))->first();

		$data = [
			'razao_social' => $this->__replace($arr[4]),
			'nome_fantasia' => $this->__replace($arr[4]),
			'bairro' => $this->__replace($arr[15]),
			'numero' => $this->__replace($arr[14]),
			'rua' => $this->__replace($arr[13]),
			'cpf_cnpj' => $this->__replace($arr[2]),
			'telefone' => '',
			'celular' => '',
			'email' => '',
			'cep' => $arr[18],
			'ie_rg' => $arr[64] == 'Fisica' ? '' : $this->__replace($arr[3]),
			'consumidor_final' => 1,
			'limite_venda' => 0,
			'cidade_id' => $cidade != null ? $cidade->id : 1,
			'contribuinte' => $arr[64] == 'Fisica' ? 0 : 1,
			'rua_cobranca' => '',
			'numero_cobranca' => '',
			'bairro_cobranca' => '',
			'cep_cobranca' => '',
			'cidade_cobranca_id' => NULL,
			'empresa_id' => $empresa_id,
			'cod_pais' => 1058,
			'id_estrangeiro' => ''
		];

		Cliente::create($data);

	}

	private function insereCliente2($arr, $empresa_id){
		if(isset($arr[16])){
			try{
				$cidade = Cidade::where('codigo', $this->__replace($arr[16]))->first();
			}catch(Exception $e){
				$cidade = Cidade::where('codigo', $this->__replace($arr[17]))->first();
			}

			try{

				$cnpj = "";
				if(is_numeric($this->__replace($arr[5]))){
					$cnpj = $this->__replace($arr[5]);
				}else if(is_numeric($this->__replace($arr[6]))){
					$cnpj = $this->__replace($arr[6]);
				}
				$cliente = Cliente::
				where('cpf_cnpj', $cnpj)
				->where('empresa_id', $empresa_id)
				->first();

				if($cliente == null){
					$data = [
						'razao_social' => $this->__replace($arr[4]),
						'nome_fantasia' => $this->__replace($arr[4]),
						'bairro' => $this->__replace($arr[15]),
						'numero' => $this->__replace($arr[14]),
						'rua' => $this->__replace($arr[13]),
						'cpf_cnpj' => $cnpj,
						'telefone' => '',
						'celular' => '',
						'email' => '',
						'cep' => $arr[18],
						'ie_rg' => $arr[64] == 'Fisica' ? '' : $this->__replace($arr[3]),
						'consumidor_final' => 1,
						'limite_venda' => 0,
						'cidade_id' => $cidade != null ? $cidade->id : 1,
						'contribuinte' => $arr[64] == 'Fisica' ? 0 : 1,
						'rua_cobranca' => '',
						'numero_cobranca' => '',
						'bairro_cobranca' => '',
						'cep_cobranca' => '',
						'cidade_cobranca_id' => NULL,
						'empresa_id' => $empresa_id,
						'cod_pais' => 1058,
						'id_estrangeiro' => ''
					];

					Cliente::create($data);
				}
				return true;
			}catch(\Exception $e){
				return false;
			}
		}else{
			return true;
		}
	}

	private function insereProduto($linha, $empresa_id){

		$categoria = Categoria::where('nome', 'Geral')->first();
		if($categoria == null){
			$categoria = Categoria::create(
				[
					'nome' => 'Geral',
					'empresa_id' => $empresa_id

				]
			);
		}
		$arr = explode(",", $linha);
		// echo "<pre>";
		// print_r($arr);
		// echo "</pre>";

		$cfop = substr($arr[199], 1, 5);

		$cfopestadual = "";
		$cfopinterestadual = "";
		if($cfop[0] == 5){
			$cfopestadual = $cfop;
			$cfopinterestadual = "6".substr($cfop, 1, 4);
		}else{
			$cfopestadual = "5".substr($cfop, 1, 4);
			$cfopinterestadual = $cfop;
		}


		$data = [
			'nome' => $this->__replace($arr[1]),
			'categoria_id' => $categoria->id,
			'cor' => '',
			'valor_venda' => $this->__replace($arr[36]),
			'NCM' => $this->__replace($arr[175]),
			'CST_CSOSN' => 101,
			'CST_PIS' => 49,
			'CST_COFINS' => 49,
			'CST_IPI' => 99,
			'unidade_compra' => $this->__replace($arr[2]),
			'unidade_venda' => $this->__replace($arr[32]),
			'composto' => 0,
			'codBarras' => '',
			'conversao_unitaria' => 1,
			'valor_livre' => 0,
			'perc_icms' => $this->__replace($arr[20]),
			'perc_pis' => $this->__replace($arr[169]),
			'perc_cofins' => $this->__replace($arr[161]),
			'perc_ipi' => $this->__replace($arr[18]),
			'CFOP_saida_estadual' => $cfopestadual,
			'CFOP_saida_inter_estadual' => $cfopinterestadual,
			'codigo_anp' => '', 
			'descricao_anp' => '',
			'perc_iss' => 0,
			'cListServ' => '',
			'imagem' => '',
			'alerta_vencimento' => 0,
			'valor_compra' => 0,
			'gerenciar_estoque' => 0,
			'estoque_minimo' => 0,
			'referencia' => '',
			'empresa_id' => $empresa_id,
			'largura' => 0,
			'comprimento' => 0,
			'altura' => 0,
			'peso_liquido' => 0,
			'peso_bruto' => 0,
			'limite_maximo_desconto' => 0,
			'pRedBC' => 0,
			'cBenef' => '',
			'grade' => 0,
			'referencia_grade' => Str::random(20)
		];

		// print_r($data);
		Produto::create($data);
	}

	private function insereProduto2($arr, $empresa_id){

		$categoria = Categoria::where('nome', 'Geral')->first();
		if($categoria == null){
			$categoria = Categoria::create(
				[
					'nome' => 'Geral',
					'empresa_id' => $empresa_id

				]
			);
		}
		
		try{
			$cfop = substr($arr[197], 1, 5);


			$cfopestadual = "";
			$cfopinterestadual = "";
			if($cfop[0] == 5){
				$cfopestadual = $cfop;
				$cfopinterestadual = "6".substr($cfop, 1, 4);
			}else{
				$cfopestadual = "5".substr($cfop, 1, 4);
				$cfopinterestadual = $cfop;
			}


			$data = [
				'nome' => $this->__replace($arr[1]),
				'categoria_id' => $categoria->id,
				'cor' => '',
				'valor_venda' => $this->__replace($arr[36]),
				'NCM' => $this->__replace($arr[173]),
				'CST_CSOSN' => 101,
				'CST_PIS' => 49,
				'CST_COFINS' => 49,
				'CST_IPI' => 99,
				'unidade_compra' => $this->__replace($arr[2]),
				'unidade_venda' => $this->__replace($arr[32]),
				'composto' => 0,
				'codBarras' => '',
				'conversao_unitaria' => 1,
				'valor_livre' => 0,
				'perc_icms' => $this->__replace($arr[20]),
				'perc_pis' => $this->__replace($arr[167]),
				'perc_cofins' => $this->__replace($arr[159]),
				'perc_ipi' => $this->__replace($arr[18]),
				'CFOP_saida_estadual' => $cfopestadual,
				'CFOP_saida_inter_estadual' => $cfopinterestadual,
				'codigo_anp' => '', 
				'descricao_anp' => '',
				'perc_iss' => 0,
				'cListServ' => '',
				'imagem' => '',
				'alerta_vencimento' => 0,
				'valor_compra' => 0,
				'gerenciar_estoque' => 0,
				'estoque_minimo' => 0,
				'referencia' => '',
				'empresa_id' => $empresa_id,
				'largura' => 0,
				'comprimento' => 0,
				'altura' => 0,
				'peso_liquido' => 0,
				'peso_bruto' => 0,
				'limite_maximo_desconto' => 0,
				'pRedBC' => 0,
				'cBenef' => '',
				'grade' => 0,
				'referencia_grade' => Str::random(20)
			];

		// print_r($data);
		// die();
			Produto::create($data);
			return true;
		}catch(\Exception $e){
			echo $e->getMessage();
			return false;
		}
	}

	private function __replace($string){
		$string = str_replace("'", '', $string);
		return str_replace('"', '', $string);
	}
}
