<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\ProdutoEcommerce;
use App\Models\Categoria;
use App\Models\ConfigNota;
use App\Models\Tributacao;
use App\Models\ImagemProdutoEcommerce;
use App\Models\CategoriaProdutoEcommerce;
use Illuminate\Support\Str;

class ProdutoEcommerceController extends Controller
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
		$produtos = ProdutoEcommerce::
		select('produto_ecommerces.*')
		->join('produtos', 'produtos.id' , '=', 'produto_ecommerces.produto_id')
		->where('produtos.empresa_id', $this->empresa_id)

		->orderBy('produto_ecommerces.id', 'desc')
		->groupBy('produtos.referencia_grade')
		->paginate(40);

		// foreach($produtos as $p){
		// 	if($p->produto->grade){
		// 		$p->produto->nome .= " (" . $p->produto->str_grade . ")";
		// 	}
		// }

		$categorias = CategoriaProdutoEcommerce::
		where('empresa_id',$this->empresa_id)
		->get();

		return view('produtoEcommerce/list')
		->with('produtos', $produtos)
		->with('categorias', $categorias)
		->with('links', true)
		->with('title', 'Produtos de Ecommerce');
	}

	public function new(){
		$produtos = Produto::
		where('empresa_id', $this->empresa_id)
		->orderBy('nome')
		->groupBy('referencia_grade')
		->get();

		foreach($produtos as $p){
			$p->nome .= " " . $p->str_grade;
		}

		$categorias = CategoriaProdutoEcommerce::
		where('empresa_id', $this->empresa_id)
		->get();

		$tributacao = Tributacao::
        where('empresa_id', $this->empresa_id)
        ->first();

        $natureza = Produto::
        firstNatureza($this->empresa_id);

        $categoria = Categoria::
        where('empresa_id', $this->empresa_id)
        ->first();
        if($categoria == null){
            //nao tem categoria
            session()->flash('mensagem_erro', 'Cadastre ao menos uma categoria!');
            return redirect('/categorias');
        }

        if($natureza == null){

            session()->flash('mensagem_erro', 'Cadastre uma natureza de operação!');
            return redirect('/naturezaOperacao');
        }

        if($tributacao == null){

            session()->flash('mensagem_erro', 'Informe a tributação padrão!');
            return redirect('/tributos');
        }

		if(sizeof($categorias) == 0){
			session()->flash("mensagem_erro", "Cadastre uma categoria para o ecommerce!");
			return redirect('/categoriaEcommerce/new');
		}

		$config = ConfigNota::
        where('empresa_id', $this->empresa_id)
        ->first();

        if($config == null){
            session()->flash('mensagem_erro', 'Informe a configuração do emitente');
            return redirect('/configNF');
        }

		return view('produtoEcommerce/register')
		->with('title', 'Cadastrar Produto para Ecommerce')
		->with('categorias', $categorias)
		->with('produtos', $produtos)
		->with('contratoJs', true);
	}

	public function edit($id){
		$produtos = Produto::
		where('empresa_id', $this->empresa_id)
		->orderBy('nome')
		->groupBy('referencia_grade')
		->get();

		foreach($produtos as $p){
			$p->nome .= " " . $p->str_grade;
		}

		$categorias = CategoriaProdutoEcommerce::
		where('empresa_id', $this->empresa_id)
		->get();

		$produto = ProdutoEcommerce::find($id);	

		if($produto->produto->grade){
			session()->flash("mensagem_erro", "Produto do tipo grade edite por aqui!");
			return redirect('/produtoEcommerce/listGrade/'.$produto->produto->referencia_grade);
		}	

		if(sizeof($categorias) == 0){
			session()->flash("mensagem_erro", "Cadastre uma categoria para o ecommerce!");
			return redirect('/categoriaEcommerce/new');
		}

		return view('produtoEcommerce/register')
		->with('title', 'Editar Produto para Ecommerce')
		->with('categorias', $categorias)
		->with('produtos', $produtos)
		->with('produto', $produto)
		->with('contratoJs', true);
	}

	public function editGrade($id){
		$produtos = Produto::
		where('empresa_id', $this->empresa_id)
		->orderBy('nome')
		->get();

		foreach($produtos as $p){
			$p->nome .= " " . $p->str_grade;
		}

		$categorias = CategoriaProdutoEcommerce::
		where('empresa_id', $this->empresa_id)
		->get();

		$produto = ProdutoEcommerce::find($id);	

		if(sizeof($categorias) == 0){
			session()->flash("mensagem_erro", "Cadastre uma categoria para o ecommerce!");
			return redirect('/categoriaEcommerce/new');
		}

		return view('produtoEcommerce/register')
		->with('title', 'Editar Produto para Ecommerce')
		->with('categorias', $categorias)
		->with('produtos', $produtos)
		->with('produto', $produto)
		->with('contratoJs', true);
	}

	public function listGrade($referecia){
		$produtos = ProdutoEcommerce::
		select('produto_ecommerces.*')
		->join('produtos', 'produtos.id' , '=', 'produto_ecommerces.produto_id')
		->where('produtos.empresa_id', $this->empresa_id)
		->where('produtos.referencia_grade', $referecia)

		->orderBy('produto_ecommerces.id', 'desc')
		->get();

		foreach($produtos as $p){
			if($p->produto->grade){
				$p->produto->nome .= " (" . $p->produto->str_grade . ")";
			}
		}

		return view('produtoEcommerce/list_grade')
		->with('produtos', $produtos)
		->with('links', true)
		->with('title', 'Produtos de Ecommerce Grade');
	}

	public function save(Request $request){

		$produto = $request->input('produto');
		$categoria = Categoria::where('empresa_id', $this->empresa_id)->first();

		$tributacao = Tributacao::where('empresa_id', $this->empresa_id)->first();
		$pId = $request->produto_id != 'null' ? $request->produto_id : null;

		if(strlen($request->produto) > 0){
			$pId = 0;
		}
		$request->merge([ 'produto_id' => $pId]);
		$this->_validate($request);
		
		if($request->produto){
            //novo produto
			$config = ConfigNota::where('empresa_id', $this->empresa_id)->first();
			$natureza = Produto::firstNatureza($this->empresa_id);

			$arr = [
				'nome' => $produto,
				'categoria_id' => $categoria->id,
				'cor' => '',
				'valor_venda' => str_replace(",", ".", $request->valor),
				'NCM' => $tributacao->ncm_padrao,
				'CST_CSOSN' => $config->CST_CSOSN_padrao,
				'CST_PIS' => $config->CST_PIS_padrao,
				'CST_COFINS' => $config->CST_COFINS_padrao,
				'CST_IPI' => $config->CST_IPI_padrao,
				'unidade_compra' => 'UN',
				'unidade_venda' => 'UN',
				'composto' => 0,
				'codBarras' => 'SEM GTIN',
				'conversao_unitaria' => 1,
				'valor_livre' => 0,
				'perc_icms' => $tributacao->icms,
				'perc_pis' => $tributacao->pis,
				'perc_cofins' => $tributacao->cofins,
				'perc_ipi' => $tributacao->ipi,
				'CFOP_saida_estadual' => $natureza->CFOP_saida_estadual,
				'CFOP_saida_inter_estadual' => $natureza->CFOP_saida_inter_estadual,
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
				'tela_id' => NULL,
				'largura' => __replace($request->largura),
				'comprimento' => __replace($request->altura),
				'altura' => __replace($request->altura),
				'peso_liquido' => __replace($request->peso_liquido),
				'peso_bruto' => __replace($request->peso_bruto),
				'empresa_id' => $this->empresa_id,
				'referencia_grade' => Str::random(20)
			];
			$produto = Produto::create($arr);
			$pId = $produto->id;

		}

		$request->merge([ 'status' => $request->input('status') ? true : false ]);
		$request->merge([ 'controlar_estoque' => $request->input('controlar_estoque') ? true : false ]);
		$request->merge([ 'destaque' => $request->input('destaque') ? true : false ]);

		$request->merge([ 'descricao' => $request->input('descricao') ?? '']);
		$request->merge([ 'valor' => __replace($request->valor)]);
		
		$request->merge([ 'produto' => $request->produto_id != 'null' ? 'true' : '']);
		$request->merge([ 'percentual_desconto_view' => $request->percentual_desconto_view ?? 0]);
		$request->merge([ 'produto_id' => $pId]);

		$result = ProdutoEcommerce::create($request->all());

		//atualiza dimensões

		$produto = Produto::find($pId);

		$produto->largura = __replace($request->largura);
		$produto->comprimento = __replace($request->comprimento);
		$produto->altura = __replace($request->altura);
		$produto->peso_liquido = __replace($request->peso_liquido);
		$produto->peso_bruto = __replace($request->peso_bruto);

		$produto->save();

		$nomeImagem = "";
		if($request->hasFile('file')){
    		//unlink anterior
			$file = $request->file('file');
			$extensao = $file->getClientOriginalExtension();

			$nomeImagem = Str::random(20).".".$extensao;
			$upload = $file->move(public_path('ecommerce/produtos'), $nomeImagem);

			ImagemProdutoEcommerce::create(
				[
					'produto_id' => $result->id, 
					'img' => $nomeImagem
				]
			);
		}

		if($result){
			session()->flash("mensagem_sucesso", "Produto cadastrado com sucesso!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao cadastrar produto!');
		}

		return redirect('/produtoEcommerce');
	}

	private function _validate(Request $request){

		$rules = [
			'produto_id' => $request->id > 0 ? '' : 'required',
			'descricao' => 'required',
			'valor' => 'required',
		];

		$messages = [
			'produto_id.required' => 'O campo produto é obrigatório.',

			'descricao.required' => 'O campo descricao é obrigatório.',
			'descricao.min' => 'Minimo de 20 caracteres',

			'valor.required' => 'O campo valor é obrigatório.',
		];

		$this->validate($request, $rules, $messages);
	}

	public function alterarStatus($id){
		$produto = ProdutoEcommerce::find($id); 

		$produto->status = !$produto->status;
		$produto->save();
		echo json_encode($produto);
	}

	public function alterarDestaque($id){
		$produto = ProdutoEcommerce::find($id); 

		$produto->destaque = !$produto->destaque;
		$produto->save();
		echo json_encode($produto);
	}

	public function alterarControlarEstoque($id){
		$produto = ProdutoEcommerce::find($id); 

		$produto->controlar_estoque = !$produto->controlar_estoque;
		$produto->save();
		echo json_encode($produto);
	}

	public function delete($id){
		$produto = ProdutoEcommerce
		::where('id', $id)
		->first();
		if(valida_objeto($produto)){
			foreach ($produto->galeria as $g) {
				$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
				if(file_exists($public . 'ecommerce/produtos/'.$g->img))
					unlink($public . 'ecommerce/produtos/'.$g->img);
			}

			if($produto->delete()){
				session()->flash('mensagem_sucesso', 'Registro removido!');
			}else{
				session()->flash('mensagem_erro', 'Erro!');
			}
			return redirect('/produtoEcommerce');
		}else{
			return redirect('/403');
		}
	}

	public function pesquisa(Request $request){
		$pesquisa = $request->pesquisa;
		$categoria_id = $request->categoria_id;
		$produtos = ProdutoEcommerce::
		select('produto_ecommerces.*')
		->join('produtos', 'produto_ecommerces.produto_id', '=', 'produtos.id')
		->where('produto_ecommerces.empresa_id', $this->empresa_id)
		->where('produtos.nome', 'LIKE', "%$pesquisa%");

		if($categoria_id){
			$produtos->where('produto_ecommerces.categoria_id', $categoria_id);
		}
		$produtos = $produtos->get();

		$categorias = CategoriaProdutoEcommerce::
		where('empresa_id', $this->empresa_id)
		->get();

		return view('produtoEcommerce/list')
		->with('produtos', $produtos)
		->with('categorias', $categorias)
		->with('pesquisa', $pesquisa)
		->with('categoria_id', $categoria_id)
		->with('links', true)
		->with('title', 'Produtos de Ecommerce');
	}

	public function galeria($id){
        $produto = new ProdutoEcommerce(); //Model

        $resp = $produto
        ->where('id', $id)->first();  
        if(valida_objeto($resp)){
        	return view('produtoEcommerce/galery')
        	->with('produto', $resp)
        	->with('title', 'Galeria de Produto');
        }else{
        	return redirect('/403');
        }
    }

    public function deleteImagem($id){
    	$imagem = ImagemProdutoEcommerce
    	::where('id', $id)
    	->first();

    	if(valida_objeto($imagem->produto)){

    		$public = getenv('SERVIDOR_WEB') ? 'public/' : '';
    		if(file_exists($public . 'ecommerce/produtos/'.$imagem->img))
    			unlink($public . 'ecommerce/produtos/'.$imagem->img);

    		if($imagem->delete()){
    			session()->flash('mensagem_sucesso', 'Imagem removida!');
    		}else{
    			session()->flash('mensagem_erro', 'Erro!');
    		}
    		return redirect('/produtoEcommerce/galeria/'.$imagem->produto_id);
    	}
    }

    public function saveImagem(Request $request){

    	$file = $request->file('file');
    	$produtoDeliveryId = $request->id;

    	$extensao = $file->getClientOriginalExtension();
    	$nomeImagem = md5($file->getClientOriginalName()).".".$extensao;
    	$request->merge([ 'img' => $nomeImagem ]);
    	$request->merge([ 'produto_id' => $produtoDeliveryId ]);

    	$upload = $file->move(public_path('ecommerce/produtos'), $nomeImagem);

    	$result = ImagemProdutoEcommerce::create($request->all());

    	if($result){

    		session()->flash("mensagem_sucesso", "Imagem cadastrada com sucesso!");
    	}else{

    		session()->flash('mensagem_erro', 'Erro ao cadastrar produto!');
    	}

    	return redirect('/produtoEcommerce/galeria/'.$produtoDeliveryId );
    }

    public function update(Request $request){
    	$produto = new ProdutoEcommerce();

    	$id = $request->input('id');
    	$resp = $produto
    	->where('id', $id)->first(); 

    	$this->_validate($request);

    	$resp->categoria_id = $request->categoria_id;

    	$resp->descricao = $request->descricao ?? '';
    	$resp->percentual_desconto_view = $request->percentual_desconto_view ?? 0;
    	$resp->cep = $request->cep ?? '';
    	$resp->valor = str_replace(",", ".", $request->valor);


    	$resp->controlar_estoque = $request->input('controlar_estoque') ? true : false;
    	$resp->status = $request->input('status') ? true : false;
    	$resp->destaque = $request->input('destaque') ? true : false;

    	$result = $resp->save();


    	$produto = Produto::find($resp->produto_id);

    	$produto->largura = __replace($request->largura);
    	$produto->comprimento = __replace($request->comprimento);
    	$produto->altura = __replace($request->altura);
    	$produto->peso_liquido = __replace($request->peso_liquido);
    	$produto->peso_bruto = __replace($request->peso_bruto);

    	$produto->save();

    	if($result){
    		session()->flash('mensagem_sucesso', 'Produto editado com sucesso!');
    	}else{
    		session()->flash('mensagem_erro', 'Erro ao editar produto!');
    	}

    	if($produto->grade){
    		return redirect('/produtoEcommerce/listGrade/'.$produto->referencia_grade); 
    	}
    	return redirect('/produtoEcommerce'); 
    }

}
