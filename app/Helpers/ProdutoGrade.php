<?php

namespace App\Helpers;

use App\Models\Estoque;
use App\Models\Produto;
use Illuminate\Support\Str;
use App\Models\AlteracaoEstoque;
use App\Models\ProdutoEcommerce;
use App\Models\ImagemProdutoEcommerce;
use App\Helpers\StockMove;

class ProdutoGrade {
	
	public function salvar($request, $nomeImagem, $randUpdate = null){

		if($randUpdate == null){
			$rand = Str::random(20);
		}else{
			$rand = $randUpdate;
		}

		$combinacoes = json_decode($request->combinacoes);
		if(!$combinacoes) return "erro";

		foreach($combinacoes as $key => $comb){
			$request->merge([ 'valor_venda' => str_replace(",", ".", $comb->valor)]);
			$request->merge([ 'codBarras' => $comb->cod_barras ? str_replace(",", ".", $comb->cod_barras) : 'SEM GTIN']);
			$request->merge([ 'referencia_grade' => $rand]);
			$request->merge([ 'grade' => true ]);
			$request->merge([ 'referencia' => $request->referencia ?? '' ]);
			$request->merge([ 'imagem' => $nomeImagem ]);
			$request->merge([ 'imagem' => $nomeImagem ]);
			$request->merge([ 'str_grade' => $comb->titulo ]);
			$request->merge([ 'CEST' => $request->CEST ?? '']);
			$request->merge([ 'unidade_tributavel' => $request->unidade_tributavel != '' ? 
				$request->unidade_tributavel : '']);
			$request->merge([ 'quantidade_tributavel' => $request->quantidade_tributavel != '' ? __replace($request->quantidade_tributavel) : '']);
			$request->merge([ 'renavam' => $request->renavam ?? '']);
			$request->merge([ 'placa' => $request->placa ?? '']);
			$request->merge([ 'chassi' => $request->chassi ?? '']);
			$request->merge([ 'combustivel' => $request->combustivel ?? '']);
			$request->merge([ 'ano_modelo' => $request->ano_modelo ?? '']);
			$request->merge([ 'cor_veiculo' => $request->cor_veiculo ?? '']);
			$request->merge([ 'CST_CSOSN_EXP' => $request->input('CST_CSOSN_EXP') ?? '']);
			$request->merge([ 'cBenef' => $request->cBenef ? $request->cBenef : '']);

			try{
				$produto = Produto::create($request->all());
				if($request->ecommerce){
					$this->salvarProdutoEcommerce($request, $produto, $nomeImagem);
				}
				$estoque = __replace($comb->quantidade);

				if($estoque > 0){
					$data = [
						'produto_id' => $produto->id,
						'usuario_id' => get_id_user(),
						'quantidade' => $estoque,
						'tipo' => 'incremento',
						'observacao' => '',
						'empresa_id' => $request->empresa_id
					];
					AlteracaoEstoque::create($data);
					$stockMove = new StockMove();
					$result = $stockMove->pluStock($produto->id, 
						$estoque, str_replace(",", ".", $produto->valor_venda));
				}
			}catch(\Exception $e){
				echo $e->getMessage();
				die;
				return $e->getMessage();
			}
		}
		return "ok";
	}

	public function update($request, $nomeImagem, $randUpdate = null){

		if($randUpdate == null){
			$rand = Str::random(20);
		}else{
			$rand = $randUpdate;
		}

		$combinacoes = json_decode($request->combinacoes);
		if(!$combinacoes) return "erro";

		foreach($combinacoes as $key => $comb){
			if($key > 0 && $randUpdate != null){
				$request->merge([ 'valor_venda' => str_replace(",", ".", $comb->valor)]);
				$request->merge([ 'codBarras' => $comb->cod_barras ? str_replace(",", ".", $comb->cod_barras) : 'SEM GTIN']);
				$request->merge([ 'referencia_grade' => $rand]);
				$request->merge([ 'grade' => true ]);
				$request->merge([ 'referencia' => $request->referencia ?? '' ]);
				$request->merge([ 'imagem' => $nomeImagem ]);
				$request->merge([ 'imagem' => $nomeImagem ]);
				$request->merge([ 'str_grade' => $comb->titulo ]);
				$request->merge([ 'CEST' => $request->CEST ?? '']);
				$request->merge([ 'unidade_tributavel' => $request->unidade_tributavel != '' ? 
					$request->unidade_tributavel : '']);
				$request->merge([ 'quantidade_tributavel' => $request->quantidade_tributavel != '' ? __replace($request->quantidade_tributavel) : '']);
				$request->merge([ 'renavam' => $request->renavam ?? '']);
				$request->merge([ 'placa' => $request->placa ?? '']);
				$request->merge([ 'chassi' => $request->chassi ?? '']);
				$request->merge([ 'combustivel' => $request->combustivel ?? '']);
				$request->merge([ 'ano_modelo' => $request->ano_modelo ?? '']);
				$request->merge([ 'cor_veiculo' => $request->cor_veiculo ?? '']);
				$request->merge([ 'CST_CSOSN_EXP' => $request->input('CST_CSOSN_EXP') ?? '']);
				$request->merge([ 'cBenef' => $request->cBenef ? $request->cBenef : '']);

				try{
					$produto = Produto::create($request->all());
					if($request->ecommerce){
						$this->salvarProdutoEcommerce($request, $produto, $nomeImagem);
					}
					$estoque = __replace($comb->quantidade);

					if($estoque > 0){
						$data = [
							'produto_id' => $produto->id,
							'usuario_id' => get_id_user(),
							'quantidade' => $estoque,
							'tipo' => 'incremento',
							'observacao' => '',
							'empresa_id' => $request->empresa_id
						];
						AlteracaoEstoque::create($data);
						$stockMove = new StockMove();
						$result = $stockMove->pluStock($produto->id, 
							$estoque, str_replace(",", ".", $produto->valor_venda));
					}
				}catch(\Exception $e){
					echo $e->getMessage();
					die;
					return $e->getMessage();
				}
			}
		}
		return "ok";
	}

	private function salvarProdutoEcommerce($request, $produto, $nomeImagem){
        // $this->_validateEcommerce($request);

		$produtoEcommerce = [
			'produto_id' => $produto->id,
			'categoria_id' => $request->categoria_ecommerce_id,
			'empresa_id' => $request->empresa_id,
			'descricao' => $request->descricao,
			'controlar_estoque' => $request->input('controlar_estoque') ? true : false,
			'status' => $request->input('status') ? true : false ,
			'valor' => __replace($request->valor_ecommerce),
			'destaque' => $request->input('destaque') ? true : false
		];

		$result = ProdutoEcommerce::create($produtoEcommerce);
		$produtoEcommerce = ProdutoEcommerce::find($result->id);
		if($result){
			$this->salveImagemProdutoEcommerce($nomeImagem, $produtoEcommerce);
		}

	}

	private function salveImagemProdutoEcommerce($nomeImagem, $produtoEcommerce){

		if($nomeImagem != ""){

			$extensao = substr($nomeImagem, strlen($nomeImagem)-4, strlen($nomeImagem));
			$novoNome = Str::random(20) . $extensao;
			copy(public_path('imgs_produtos/').$nomeImagem, public_path('ecommerce/produtos/').$novoNome);
            // $upload = $file->move(public_path('ecommerce/produtos'), $nomeImagem);

			ImagemProdutoEcommerce::create(
				[
					'produto_id' => $produtoEcommerce->id, 
					'img' => $novoNome
				]
			);

		}else{

		}
	}

}