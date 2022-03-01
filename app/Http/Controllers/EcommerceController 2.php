<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfigEcommerce;
use App\Models\CategoriaProdutoEcommerce;
use App\Models\CarrosselEcommerce;
use App\Models\ProdutoEcommerce;
use App\Models\PostBlogEcommerce;
use App\Models\CategoriaPostBlogEcommerce;
use App\Models\ContatoEcommerce;
use App\Models\InformativoEcommerce;
use App\Models\PedidoEcommerce;
use App\Models\ItemPedidoEcommerce;
use App\Models\ClienteEcommerce;
use App\Models\EnderecoEcommerce;
use App\Models\CurtidaProdutoEcommerce;
use App\Rules\ValidaDocumento;
use App\Helpers\PedidoEcommerceHelper;
use Illuminate\Support\Str;
use Mail;

class EcommerceController extends Controller
{

	public function index($link = null){

		$link = strtolower($link);
		$dadosDefault = $this->getDadosDefault($link);
		$carrossel = CarrosselEcommerce::
		where('empresa_id', $dadosDefault['config']->empresa_id)
		->get();

		$produtosEmDestaque = $this->produtosEmDestaque($dadosDefault['config']->empresa_id);
		$categoriasEmDestaque = $this->categoriaDestesProdutos($produtosEmDestaque);

		$categorias = CategoriaProdutoEcommerce::
		where('empresa_id', $dadosDefault['config']->empresa_id)
		->inRandomOrder()
		->get();

		return view($dadosDefault['template'].'/home')
		->with('categorias', $categorias)
		->with('carrossel', $carrossel)
		->with('default', $dadosDefault)
		->with('produtosEmDestaque', $produtosEmDestaque)
		->with('categoriasEmDestaque', $categoriasEmDestaque)
		->with('rota', $dadosDefault['rota'])
		->with('title', 'Home');
	}

	private function produtosEmDestaque($empresa_id){
		$produtos = ProdutoEcommerce::
		select('produto_ecommerces.*')
		->join('produtos', 'produtos.id' , '=', 
			'produto_ecommerces.produto_id')
		->where('produto_ecommerces.empresa_id', $empresa_id)
		->where('produto_ecommerces.destaque', true)
		->where('produto_ecommerces.status', true)
		->groupBy('produtos.referencia_grade')
		->get();

		$produtoEcommerceHelper = new PedidoEcommerceHelper();
		$user = $produtoEcommerceHelper->getUserLogado();

		foreach($produtos as $p){
			if($user){
				$c = CurtidaProdutoEcommerce::
				where('produto_id', $p->id)
				->where('cliente_id', $user['cliente_id'])
				->first();

				$p->curtido = $c != null;
			}else{
				$p->curtido = false;
			}
		}

		return $this->limitaEstoque($produtos);
	}

	private function limitaEstoque($produtos){
		$temp = [];
		foreach($produtos as $p){
			if($p->controlar_estoque){
				if($p->produto->estoque){
					if($p->produto->estoque->quantidade > 0){
						array_push($temp, $p);
					}
				}
			}else{
				array_push($temp, $p);
			}

		}
		return $temp;
	}

	private function categoriaDestesProdutos($produtos){
		$categorias = [];

		foreach($produtos as $p){
			if(!in_array($p->categoria_id, $categorias)){
				array_push($categorias, $p->categoria_id);
			}
		}

		$objeto = [];
		foreach($categorias as $c){
			$categoria = CategoriaProdutoEcommerce::find($c);
			array_push($objeto, $categoria);
		}
		return $objeto;
	}

	private function getConfig($link){
		$config = ConfigEcommerce::
		where('link', $link)
		->first();

		return $config;
	}

	private function getConfigEmpresaId($id){
		$config = ConfigEcommerce::
		where('empresa_id', $id)
		->first();

		return $config;
	}

	private function getActive(){
		$uri = $_SERVER['REQUEST_URI'];
		$uri = explode("/", $uri);

		$active = "";
		if(isset($uri[3])){
			if($uri[3] == 'categorias') $active = 'categorias';
			elseif($uri[3] == '1') $active = 'categorias';
			elseif($uri[3] == '2') $active = 'categorias';
			// elseif($uri[3] == 'carrinho') $active = 'categorias';
			elseif($uri[3] == 'contato') $active = 'contato';
			elseif($uri[3] == 'blog') $active = 'blog';

			// echo $uri[3];
		}else{
			$active = "home";
		}

		return $active;
	}

	private function getDadosDefault($link){

		$config = $this->getConfig($link);

		$categorias = CategoriaProdutoEcommerce::
		where('empresa_id', $config->empresa_id)
		->get();

		$produtoEcommerceHelper = new PedidoEcommerceHelper();
		$carrinho = $produtoEcommerceHelper->getCarrinho();
		$curtidas = $produtoEcommerceHelper->getProdutosCurtidos();

		$postBlogExists = PostBlogEcommerce::
		where('empresa_id', $config->empresa_id)
		->exists();

		$active = $this->getActive();
		return [
			'config' => $config,
			'template' => $config->tema_ecommerce,
			'categorias' => $categorias,
			'curtidas' => $curtidas,
			'active' => $active,
			'carrinho' => $carrinho,
			'postBlogExists' => $postBlogExists,
			'rota' => '/loja/' . strtolower($config->link)
		];
	}

	public function categorias($link){
		$link = strtolower($link);
		$dadosDefault = $this->getDadosDefault($link);

		$categorias = CategoriaProdutoEcommerce::
		where('empresa_id', $dadosDefault['config']->empresa_id)
		->get();

		return view($dadosDefault['template'].'/categorias')
		->with('default', $dadosDefault)
		->with('categorias', $categorias)
		->with('rota', $dadosDefault['rota'])
		->with('title', 'Categorias');
	}

	public function produtosDaCategoria($link, $id){
		$link = strtolower($link);
		$dadosDefault = $this->getDadosDefault($link);

		$produtos = ProdutoEcommerce::
		select('produto_ecommerces.*')
		->where('produto_ecommerces.empresa_id', $dadosDefault['config']->empresa_id)
		->where('produto_ecommerces.categoria_id', $id)
		->join('categoria_produto_ecommerces', 'categoria_produto_ecommerces.id' , '=', 
			'produto_ecommerces.categoria_id')
		->where('produto_ecommerces.status', 1)
		->get();

		$produtos = $this->limitaEstoque($produtos);

		// print_r($produtos);
		// die;

		$categoria = CategoriaProdutoEcommerce::find($id);

		return view($dadosDefault['template'].'/produtos_categoria')
		->with('default', $dadosDefault)
		->with('produtos', $produtos)
		->with('categoria', $categoria)
		->with('shop', true)
		->with('rota', $dadosDefault['rota'])
		->with('title', 'Produtos por categoria');
	}

	public function blog($link){
		$config = $this->getConfig($link);
		$dadosDefault = $this->getDadosDefault($link);

		$categoriasPost = CategoriaPostBlogEcommerce::
		where('empresa_id', $config->empresa_id)
		->get();

		$posts = PostBlogEcommerce::
		where('empresa_id', $config->empresa_id)
		->orderBy('id', 'desc')
		->limit(6)
		->get();

		$postsRecentes = PostBlogEcommerce::
		where('empresa_id', $config->empresa_id)
		->orderBy('id', 'desc')
		->limit(6)
		->get();

		return view($dadosDefault['template'].'/blog_index')
		->with('default', $dadosDefault)
		->with('posts', $posts)
		->with('blog', true)
		->with('postsRecentes', $postsRecentes)
		->with('rota', $dadosDefault['rota'])
		->with('categoriasPost', $categoriasPost)
		->with('title', 'Blog');
	}

	public function contato($link){
		$config = $this->getConfig($link);
		$dadosDefault = $this->getDadosDefault($link);

		return view($dadosDefault['template'].'/contato')
		->with('default', $dadosDefault)
		->with('contato', true)
		->with('rota', $dadosDefault['rota'])
		->with('title', 'Contato');
	}

	public function verPost($link, $postId){
		$config = $this->getConfig($link);
		$dadosDefault = $this->getDadosDefault($link);

		$post = PostBlogEcommerce::find($postId);
		$categoriasPost = CategoriaPostBlogEcommerce::
		where('empresa_id', $config->empresa_id)
		->get();

		$postsRecentes = PostBlogEcommerce::
		where('empresa_id', $config->empresa_id)
		->orderBy('id', 'desc')
		->limit(6)
		->get();

		return view($dadosDefault['template'].'/blog_view_post')
		->with('default', $dadosDefault)
		->with('post', $post)
		->with('blog', true)
		->with('postsRecentes', $postsRecentes)
		->with('rota', $dadosDefault['rota'])
		->with('categoriasPost', $categoriasPost)
		->with('title', 'Blog - ' . $post->titulo);
	}

	public function saveContato(Request $request){
		$empresaId = $request->empresa_id;
		$this->_validate($request);

		$contato = ContatoEcommerce::create(
			$request->all()
		);
		if($contato){
			session()->flash('mensagem_sucesso', 'Obrigado por entrar em contato, em breve retornaremos!');
		}else{
			session()->flash('mensagem_erro', 'Erro!');
		}

		return redirect()->back();
	}

	private function _validate(Request $request){
		$doc = $request->cpf_cnpj;

		$rules = [
			'nome' => 'required|max:50',
			'email' => 'required|max:100|email',
			'texto' => 'required',
		];

		$messages = [
			'nome.required' => 'O campo nome é obrigatório.',
			'nome.max' => '50 caracteres maximos permitidos.',
			'email.required' => 'O campo email é obrigatório.',
			'email.max' => '100 caracteres maximos permitidos.',
			'email.email' => 'Email inválido.',
			'texto.required' => 'O campo texto é obrigatório.',

		];

		$this->validate($request, $rules, $messages);
	}

	public function saveInformativo(Request $request){

		$this->_validateInfo($request);

		$c = InformativoEcommerce::where('email', $request->email_info)
		->where('empresa_id', $request->empresa_id)
		->first();

		if($c != null){
			session()->flash('mensagem_sucesso', 'Seu email já está includo, brigado por se inscrever!');
			return redirect()->back();
		}

		$contato = InformativoEcommerce::create(
			[
				'email' => $request->email_info,
				'empresa_id' => $request->empresa_id
			]
		);
		if($contato){
			session()->flash('mensagem_sucesso', 'Obrigado por se inscrever!');
		}else{
			session()->flash('mensagem_erro', 'Erro!');
		}

		return redirect()->back();
	}

	private function _validateInfo(Request $request){
		$doc = $request->cpf_cnpj;

		$rules = [
			'email_info' => 'required|max:100|email'
		];

		$messages = [
			'email_info.required' => 'O campo email é obrigatório.',
			'email_info.max' => '100 caracteres maximos permitidos.',
			'email_info.email' => 'Email inválido.'
		];

		$this->validate($request, $rules, $messages);
	}

	public function verProduto($link, $produtoId){
		$config = $this->getConfig($link);
		$dadosDefault = $this->getDadosDefault($link);

		$produto = ProdutoEcommerce::find($produtoId);

		$curtida = false;
		$produtoEcommerceHelper = new PedidoEcommerceHelper();
		$user = $produtoEcommerceHelper->getUserLogado();

		if($user){
			$curtida = CurtidaProdutoEcommerce::
			where('produto_id', $produtoId)
			->where('cliente_id', $user['cliente_id'])
			->first();

			if($curtida != null){
				$curtida = true;
			}
		}

		$variacoes = [];

		$categoria = CategoriaProdutoEcommerce::find($produto->categoria_id);

		if($produto->produto->grade){
			$variacoes = ProdutoEcommerce::
			select('produto_ecommerces.*')
			->join('produtos', 'produtos.id', '=', 'produto_ecommerces.produto_id')
			->where('produtos.referencia_grade', $produto->produto->referencia_grade)
			->get();
			
			return view($dadosDefault['template'].'/produto_view_grade')
			->with('default', $dadosDefault)
			->with('produto', $produto)
			->with('variacoes', $variacoes)
			->with('curtida', $curtida)
			->with('categoria', $categoria)
			->with('rota', $dadosDefault['rota'])
			->with('title', $produto->produto->nome);

		}else{

			return view($dadosDefault['template'].'/produto_view')
			->with('default', $dadosDefault)
			->with('categoria', $categoria)
			->with('produto', $produto)
			->with('variacoes', $variacoes)
			->with('product', true)
			->with('curtida', $curtida)
			->with('rota', $dadosDefault['rota'])
			->with('title', $produto->produto->nome);
		}
		
	}

	public function addProduto(Request $request){

		$data = [
			'empresa_id' => $request->empresa_id,
			'produto_id' => $request->produto_id,
			'quantidade' => $request->quantidade
		];
		$produtoEcommerceHelper = new PedidoEcommerceHelper();
		try{
			$produtoEcommerceHelper->addProduto($data);
			session()->flash('mensagem_sucesso', 'Item adicionado!!');

		}catch(\Exception $e){
			session()->flash('mensagem_erro', 'Erro ' . $e->getMessage());
		}

		$config = ConfigEcommerce::
		where('empresa_id', $request->empresa_id)
		->first();

		return redirect('/loja/' . strtolower($config->link) . '/carrinho');
	}

	public function carrinho($link){
		$config = $this->getConfig($link);
		$dadosDefault = $this->getDadosDefault($link);

		$carrinho = $dadosDefault['carrinho'];

		if($carrinho){
			$carrinho->valor_frete = 0;
			$carrinho->observacao = "";
			$carrinho->save();
		}

		return view($dadosDefault['template'].'/carrinho')
		->with('default', $dadosDefault)
		->with('carrinhoJs', true)
		->with('cart', true)

		->with('rota', $dadosDefault['rota'])
		->with('title', 'Carrinho');
	}

	public function curtidas($link){
		$config = $this->getConfig($link);
		$dadosDefault = $this->getDadosDefault($link);

		$produtoEcommerceHelper = new PedidoEcommerceHelper();
		$user = $produtoEcommerceHelper->getUserLogado();

		if($user == null){
			session()->flash('mensagem_erro', 'Faça o login!!');
			return redirect()->back();
		}

		$curtidas = CurtidaProdutoEcommerce::
		where('cliente_id', $user['cliente_id'])
		->get();

		return view($dadosDefault['template'].'/curtidas')
		->with('default', $dadosDefault)
		->with('curtidas', $curtidas)
		->with('cart', true)
		->with('rota', $dadosDefault['rota'])
		->with('title', 'Produtos Favoritos');
	}

	public function deleteItemCarrinho($link, $id){
		try{
			$item = ItemPedidoEcommerce::
			find($id)->delete();
			session()->flash('mensagem_sucesso', 'Item removido!!');

		}catch(\Exception $e){
			session()->flash('mensagem_erro', 'Erro ao remover item!!');
		}

		return redirect()->back();
	}

	public function atualizaItem(Request $request){
		$id = $request->id;
		$quantidade = $request->quantidade;

		try{
			$item = ItemPedidoEcommerce::
			find($id);

			$item->quantidade = $quantidade;
			$item->save();
			return response()->json($item, 200);
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}
	}

	public function calculaFrete(Request $request){

		if(getenv("CEP_PRODUTO_ECOMMERCE") == 0){

			$retorno = $this->calculaFreteNormal($request);
			return response()->json($retorno, 200);
		}else{
			$retorno = $this->calculaFreteCepProduto($request);
			return response()->json($retorno, 200);
		}
	}

	private function calculaFreteNormal($request){
		$cepDestino = str_replace("-", "", $request->cep);
		$pedidoId = $request->pedido_id;

		$pedido = PedidoEcommerce::find($pedidoId);

		$config = ConfigEcommerce::
		where('empresa_id', $pedido->empresa_id)
		->first();

		$cepOrigem = str_replace("-", "", $config->cep);

		$somaPeso = $pedido->somaPeso();
		$dimensoes = $pedido->somaDimensoes();

		$stringUrl = "&sCepOrigem=$cepOrigem&sCepDestino=$cepDestino&nVlPeso=$somaPeso";

		$stringUrl .= "&nVlComprimento=".$dimensoes['comprimento']."&nVlAltura=".$dimensoes['altura']."&nVlLargura=".$dimensoes['largura']."&nCdServico=04510";


		$url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCdAvisoRecebimento=n&sCdMaoPropria=n&nVlValorDeclarado=0&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3&nCdFormato=1" . $stringUrl;

		$unparsedResult = file_get_contents($url);
		$parsedResult = simplexml_load_string($unparsedResult);

		$stringUrl = "&sCepOrigem=$cepOrigem&sCepDestino=$cepDestino&nVlPeso=$somaPeso";

		$stringUrl .= "&nVlComprimento=".$dimensoes['comprimento']."&nVlAltura=".$dimensoes['altura']."&nVlLargura=".$dimensoes['largura']."&nCdServico=04014";

		$url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCdAvisoRecebimento=n&sCdMaoPropria=n&nVlValorDeclarado=0&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3&nCdFormato=1" . $stringUrl;

		$unparsedResultSedex = file_get_contents($url);
		$parsedResultSedex = simplexml_load_string($unparsedResultSedex);

		$retorno = array(
			'preco_sedex' => strval($parsedResultSedex->cServico->Valor),
			'prazo_sedex' => strval($parsedResultSedex->cServico->PrazoEntrega),

			'preco' => strval($parsedResult->cServico->Valor),
			'prazo' => strval($parsedResult->cServico->PrazoEntrega)
		);

		if($pedido->somaItens() > $config->frete_gratis_valor){
			$retorno['frete_gratis'] = true;
		}
		return $retorno;
	}

	private function calculaFreteCepProduto($request){
		$cepDestino = str_replace("-", "", $request->cep);
		$pedidoId = $request->pedido_id;

		$pedido = PedidoEcommerce::find($pedidoId);
		$config = ConfigEcommerce::
		where('empresa_id', $pedido->empresa_id)
		->first();

		$ceps = $pedido->getCepsDoPedido($config->cep);

		$retorno = [
			'preco_sedex' => 0,
			'prazo_sedex' => 0,
			'preco' => 0,
			'prazo' => 0
		];
		if(sizeof($ceps) > 0){
			for($i=0; $i < sizeof($ceps); $i++){
				$cepOrigem = str_replace("-", "", $ceps[$i]);

				$somaPeso = $pedido->somaPesoPorCep($ceps[$i]);
				$dimensoes = $pedido->somaDimensoesPorCep($ceps[$i]);

				$stringUrl = "&sCepOrigem=$cepOrigem&sCepDestino=$cepDestino&nVlPeso=$somaPeso";

				$stringUrl .= "&nVlComprimento=".$dimensoes['comprimento']."&nVlAltura=".$dimensoes['altura']."&nVlLargura=".$dimensoes['largura']."&nCdServico=04510";


				$url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCdAvisoRecebimento=n&sCdMaoPropria=n&nVlValorDeclarado=0&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3&nCdFormato=1" . $stringUrl;

				$unparsedResult = file_get_contents($url);
				$parsedResult = simplexml_load_string($unparsedResult);

				$stringUrl = "&sCepOrigem=$cepOrigem&sCepDestino=$cepDestino&nVlPeso=$somaPeso";

				$stringUrl .= "&nVlComprimento=".$dimensoes['comprimento']."&nVlAltura=".$dimensoes['altura']."&nVlLargura=".$dimensoes['largura']."&nCdServico=04014";

				$url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCdAvisoRecebimento=n&sCdMaoPropria=n&nVlValorDeclarado=0&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3&nCdFormato=1" . $stringUrl;

				$unparsedResultSedex = file_get_contents($url);
				$parsedResultSedex = simplexml_load_string($unparsedResultSedex);

				// $retorno = array(
				// 	'preco_sedex' => strval($parsedResult->cServico->Valor),
				// 	'prazo_sedex' => strval($parsedResult->cServico->PrazoEntrega),

				// 	'preco' => strval($parsedResultSedex->cServico->Valor),
				// 	'prazo' => strval($parsedResultSedex->cServico->PrazoEntrega)
				// );

				$valorSedex = (strval($parsedResultSedex->cServico->Valor));
				$valorSedex = (float)str_replace(",", ".", $valorSedex);

				$retorno['preco_sedex'] += $valorSedex;

				$valorPac = (strval($parsedResult->cServico->Valor));
				$valorPac = (float)str_replace(",", ".", $valorPac);

				$retorno['preco'] += $valorPac;

				if($retorno['prazo_sedex'] < strval($parsedResultSedex->cServico->PrazoEntrega)){
					$retorno['prazo_sedex'] = strval($parsedResultSedex->cServico->PrazoEntrega);
				}

				if($retorno['prazo'] < strval($parsedResult->cServico->PrazoEntrega)){
					$retorno['prazo'] = strval($parsedResult->cServico->PrazoEntrega);
				}
				// $retorno['prazo_sedex'] = strval($parsedResult->cServico->PrazoEntrega);


			}

			$retorno['preco_sedex'] = number_format($retorno['preco_sedex'], 2, ',', '.') . "";
			$retorno['preco'] = number_format($retorno['preco'], 2, ',', '.') . "";

			return $retorno;

		}else{
			return $this->calculaFreteNormal($request);
		}
	}

	public function setaFrete(Request $request){
		$pedidoId = $request->pedido_id;
		$tipo = $request->tipo;
		$valor = $request->valor;
		$cep = $request->cep;
		try{
			$pedido = PedidoEcommerce::find($pedidoId);
			$pedido->tipo_frete = $tipo;
			$pedido->observacao = $cep;
			$pedido->valor_frete = __replace($valor);

			$pedido->save();
			return response()->json($pedido, 200);
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}

	}

	public function checkout(Request $request,$link){
		$config = $this->getConfig($link);
		$dadosDefault = $this->getDadosDefault($link);

		$tipoFrete = $request->tp_frete;

		if(!session('user_ecommerce')){
			if( $dadosDefault['carrinho']== null){
				return redirect($dadosDefault['rota']);
			}

			$cep = preg_replace("/[^0-9]/", "", $dadosDefault['carrinho']->observacao);
			if($cep != ""){
				$url = "http://viacep.com.br/ws/$cep/xml/";
				$enderecoCep = simplexml_load_file($url);
			}else{
				$enderecoCep = null;
			}

			return view($dadosDefault['template'].'/checkout')
			->with('default', $dadosDefault)
			->with('carrinhoJs', true)
			->with('cart', true)
			->with('contato', true)
			->with('enderecoCep', $enderecoCep)
			->with('rota', $dadosDefault['rota'])
			->with('title', 'Checkout');
		}else{
			return redirect($dadosDefault['rota'] . '/endereco?tipo_frete='.$tipoFrete);
		}
	}

	public function checkoutStore(Request $request){
		$this->_validateCheckout($request);

		$pedido = PedidoEcommerce::find($request->pedido_id);

		$dataCliente = [
			'nome' => $request->nome,
			'sobre_nome' => $request->sobre_nome,
			'cpf' => $request->cpf,
			'ie' => $request->ie ?? '',
			'email' => $request->email,
			'telefone' => $request->telefone,
			'senha' => md5($request->senha),
			'status' => 1,
			'empresa_id' => $request->empresa_id
		];

		$cliente = ClienteEcommerce::create($dataCliente);

		$dataEndereco = [
			'rua' => $request->rua,
			'numero' => $request->numero,
			'bairro' => $request->bairro,
			'cep' => $request->cep,
			'cidade' => $request->cidade,
			'uf' => $request->uf,
			'complemento' => $request->complemento ?? '',
			'cliente_id' => $cliente->id
		];

		$endereco = EnderecoEcommerce::create($dataEndereco);

		$pedido->observacao = $request->observacao ?? '';
		$pedido->cliente_id = $cliente->id;
		$pedido->endereco_id = $endereco->id;

		$pedido->save();

		$produtoEcommerceHelper = new PedidoEcommerceHelper();
		$produtoEcommerceHelper->setUserEcommerce($cliente->id);

		$config = $this->getConfigEmpresaId($pedido->empresa_id);

		return redirect('/loja/' . strtolower($config->link) . '/endereco');
	}

	private function _validateCheckout(Request $request){
		$doc = $request->cpf_cnpj;

		$rules = [
			'nome' => 'required|max:30',
			'sobre_nome' => 'required|max:30',
			'cpf' => ['required', \Illuminate\Validation\Rule::unique('cliente_ecommerces')->ignore($request->id), new ValidaDocumento],
			'email' => ['required', 'max:60', 'email', \Illuminate\Validation\Rule::unique('cliente_ecommerces')->ignore($request->id)],
			'senha' => 'required|min:6',
			'ie' => $request->tp_doc == 'cnpj' ? 'required' : '',

			'rua' => 'required|max:60',
			'numero' => 'required|max:10',
			'bairro' => 'required|max:30',
			'cidade' => 'required|max:30',
			'telefone' => 'required|max:15',

			'uf' => 'required|max:2|min:2',
			'cep' => 'required|max:9|min:9',
			'complemento' => 'max:30'
		];

		$messages = [
			'nome.required' => 'O campo nome é obrigatório.',
			'nome.max' => '30 caracteres maximos permitidos.',
			'sobre_nome.required' => 'O campo sobre nome é obrigatório.',
			'sobre_nome.max' => '30 caracteres maximos permitidos.',
			'cpf.required' => 'O campo CPF é obrigatório.',
			'cpf.unique' => 'CPF já cadastrado no sistema.',
			'email.required' => 'O campo email é obrigatório.',
			'email.max' => '60 caracteres maximos permitidos.',
			'email.unique' => 'email já cadastrado no sistema.',
			'senha.required' => 'O campo senha é obrigatório.',
			'senha.min' => 'Use uma senha com no minímo 6 caracteres.',

			'telefone.required' => 'O campo telefone é obrigatório.',
			'telefone.max' => '15 caracteres maximos permitidos.',

			'rua.required' => 'O campo rua é obrigatório.',
			'rua.max' => '60 caracteres maximos permitidos.',
			'numero.required' => 'O campo número é obrigatório.',
			'numero.max' => '10 caracteres maximos permitidos.',
			'bairro.required' => 'O campo bairro é obrigatório.',
			'bairro.max' => '30 caracteres maximos permitidos.',
			'cidade.required' => 'O campo cidade é obrigatório.',
			'cidade.max' => '30 caracteres maximos permitidos.',

			'uf.required' => 'O campo uf é obrigatório.',
			'uf.max' => 'UF inválido.',
			'uf.min' => 'UF inválido.',

			'cep.required' => 'O campo CEP é obrigatório.',
			'cep.max' => 'CEP inválido.',
			'cep.min' => 'CEP inválido.',

			'complemento.max' => '30 caracteres maximos permitidos.',

			'ie.required' => 'O campo IE é obrigatório.',

		];

		$this->validate($request, $rules, $messages);
	}

	public function logoff($link){
		$dadosDefault = $this->getDadosDefault($link);
		$produtoEcommerceHelper = new PedidoEcommerceHelper();
		$produtoEcommerceHelper->logoff();

		session()->flash('mensagem_sucesso', 'Logoff realizado!!');
		return redirect($dadosDefault['rota']);
	}

	// public function pagamento($link){
	// 	$dadosDefault = $this->getDadosDefault($link);
	// 	$carrinho = $dadosDefault['carrinho'];

	// 	$descricao = $this->getDescricao($carrinho);

	// 	$total = $carrinho->somaItens();

	// 	return view('ecommerce/pay')
	// 	->with('default', $dadosDefault)
	// 	->with('carrinho', $carrinho)
	// 	->with('descricao', $descricao)
	// 	->with('total', $total)
	// 	->with('payJs', true)
	// 	->with('rota', $dadosDefault['rota'])
	// 	->with('title', 'Pagamento do Pedido');
	// }

	public function pagamento(Request $request){
		$empresa_id = $request->empresa_id;
		$pedido_id = $request->pedido_id;
		$tipo = $request->tipo;

		if(!$request->endereco ){
			session()->flash('mensagem_erro', 'Selecione o endereço');
			return redirect()->back();
		}


		$endereco = json_decode($request->endereco);

		$valorFrete = 0;

		if($tipo == 'sedex'){
			$valorFrete = __replace($endereco->preco_sedex);
		}else if($tipo == 'pac'){
			$valorFrete = __replace($endereco->preco);
		}
		
		$pedido = PedidoEcommerce::find($pedido_id);

		$pedido->endereco_id = $endereco->id;

		$total = $pedido->somaItens();
		$total = $pedido->somaItens() + $valorFrete;

		$pedido->valor_frete = $valorFrete;
		$pedido->valor_total = $total;

		$pedido->save();

		$config = $this->getConfigEmpresaId($empresa_id);
		$dadosDefault = $this->getDadosDefault($config->link);

		$descricao = $this->getDescricao($pedido);

		return view($dadosDefault['template'].'/pay')
		->with('default', $dadosDefault)
		->with('carrinho', $pedido)
		->with('cliente', $pedido->cliente)
		->with('descricao', $descricao)
		->with('total', $total)
		->with('payJs', true)
		->with('cart', true)
		->with('rota', $dadosDefault['rota'])
		->with('title', 'Pagamento do Pedido');
	}

	private function getDescricao($carrinho){
		$descricao = "";

		foreach($carrinho->itens as $i){
			$descricao .= "$i->quantidade x " . $i->produto->produto->nome . " ";
		}

		return $descricao;
	}

	public function login($link){
		$dadosDefault = $this->getDadosDefault($link);

		if(!session('user_ecommerce')){
			return view($dadosDefault['template'].'/login')
			->with('default', $dadosDefault)
			->with('contato', true)
			->with('rota', $dadosDefault['rota'])
			->with('title', 'Login');
		}else{
			$id = session('user_ecommerce')['cliente_id'];
			$cliente = ClienteEcommerce::find($id);
			return view($dadosDefault['template'].'/area_cliente')
			->with('default', $dadosDefault)
			->with('cliente', $cliente)
			->with('contato', true)
			
			->with('rota', $dadosDefault['rota'])
			->with('title', 'Area do Cliente');
		}
	}

	public function loginPost(Request $request){

		$cliente = ClienteEcommerce::
		where('email', $request->email)
		->where('senha', md5($request->senha))
		->first();

		if($cliente == null){
			session()->flash('mensagem_erro', 'Email e/ou senha não encontrado!');
			return redirect()->back();
		}

		$produtoEcommerceHelper = new PedidoEcommerceHelper();
		$pedido = $produtoEcommerceHelper->getCarrinho();
		if($pedido){
			
			$pedido->cliente_id = $cliente->id;
			$pedido->save();
		}
		$produtoEcommerceHelper->setUserEcommerce($cliente->id);

		$config = $this->getConfigEmpresaId($cliente->empresa_id);

		session()->flash('mensagem_sucesso', "Bem vindo(a) $cliente->nome!");
		return redirect('/loja/' . strtolower($config->link));

	}

	public function esquecisenha($link){
		$dadosDefault = $this->getDadosDefault($link);

		return view($dadosDefault['template'].'/esquecisenha')
		->with('default', $dadosDefault)
		->with('contato', true)
		->with('rota', $dadosDefault['rota'])
		->with('title', 'Login');
	}

	public function esquecisenhaPost(Request $request){

		$cliente = ClienteEcommerce::
		where('email', $request->email)
		->first();

		session()->flash('mensagem_sucesso', 'Se o email existir foi enviado uma nova senha para você!');


		if($cliente != null){
			$this->sendEmail($cliente);

			$config = ConfigEcommerce::
			where('empresa_id', $cliente->empresa_id)
			->first();

			return redirect("/loja/$config->link");
		}else{
			return redirect()->back();
		}

		
	}

	private function sendEmail($cliente){

		$senha = Str::random(4);
		$cliente->senha = md5($senha);
		$cliente->save();
		$config = ConfigEcommerce::
		where('empresa_id', $cliente->empresa_id)
		->first();

		Mail::send('mail.esqueci_senha', ['senha' => $senha, 'nome' => $cliente->nome, 
			'empresa' => $config->nome], function($m) use ($cliente, $config){

				$nomeEmail = $config->nome;
				$m->from(getenv('MAIL_USERNAME'), $nomeEmail);
				$m->subject('Recuperação de senha');
				$m->to($cliente->email);
			});
	}

	public function ecommerceUpdateCliente(Request $request){
		$cliente = ClienteEcommerce::find($request->id);
		$cliente->nome = $request->nome;
		$cliente->sobre_nome = $request->sobre_nome;
		$cliente->telefone = $request->telefone;
		$cliente->email = $request->email;

		$cliente->save();
		session()->flash('mensagem_sucesso', 'Dados alterados!');
		return redirect()->back();
	}

	public function ecommerceUpdateSenha(Request $request){

		if($request->senha != $request->repita_senha){
			session()->flash('mensagem_erro', 'Senhas digitas não coincidem!');
		}else{
			$cliente = ClienteEcommerce::find($request->id);
			$cliente->senha = md5($request->senha);
			$cliente->save();
			session()->flash('mensagem_sucesso', 'Senha alterada!');
		}
		return redirect()->back();
	}

	public function ecommerceSaveEndereco(Request $request){

		try{

			if($request->endereco_id == 0){
				$data = [
					'rua' => $request->rua,
					'numero' => $request->numero,
					'bairro' => $request->bairro,
					'cep' => $request->cep,
					'cidade' => $request->cidade,
					'uf' => $request->uf,
					'complemento' => $request->complemento ?? '',
					'cliente_id' => $request->id
				];

				$endereco = EnderecoEcommerce::create($data);

				session()->flash('mensagem_sucesso', 'Endereço cadastrado!');
			}else{

				$endereco = EnderecoEcommerce::find($request->endereco_id);

				$endereco->rua = $request->rua;
				$endereco->numero = $request->numero;
				$endereco->bairro = $request->bairro;
				$endereco->cep = $request->cep;
				$endereco->cidade = $request->cidade;
				$endereco->uf = $request->uf;
				$endereco->complemento = $request->complemento ?? '';

				$endereco->save();
				session()->flash('mensagem_sucesso', 'Endereço atualizado!');

			}

		}catch(\Exception $e){
			session()->flash('mensagem_erro', 'Erro ao cadastrar endereço!');
		}

		return redirect()->back();
	}

	public function endereco(Request $request, $link){
		$dadosDefault = $this->getDadosDefault($link);
		$carrinho = $dadosDefault['carrinho'];

		$tipoFrete = $request->tipo_frete;

		if(!$carrinho){
			session()->flash('mensagem_erro', 'Adicione um item ao carrinho!');
			return redirect()->back();
		}

		if(!$carrinho->cliente){
			session()->flash('mensagem_erro', 'Realize o login para continuar!');
			return redirect($dadosDefault['rota'] . '/login');
		}

		$cliente = $carrinho->cliente;
		$total = $carrinho->somaItens();

		$enderecos = $cliente->enderecos;

		// print_r($enderecos);

		foreach($enderecos as $e){
			$calc = $this->calculaFreteEnderecos($e, $carrinho);

			$e->preco_sedex = $calc['preco_sedex'];
			$e->prazo_sedex = $calc['prazo_sedex'];
			$e->preco = $calc['preco'];
			$e->prazo = $calc['prazo'];

			if($total > $dadosDefault['config']->frete_gratis_valor){
				$e->frete_gratis = 1;
			}

		}


		return view($dadosDefault['template'].'/selecionar_endereco')
		->with('default', $dadosDefault)
		->with('carrinho', $carrinho)
		->with('enderecos', $enderecos)
		->with('cliente', $cliente)
		->with('total', $total)
		->with('contato', true)
		->with('tipoFrete', $tipoFrete)
		->with('rota', $dadosDefault['rota'])
		->with('title', 'Selecionar o Endereço');
	}

	private function calculaFreteEnderecos($endereco, $carrinho){

		$cepDestino = $endereco->cep;

		$config = ConfigEcommerce::
		where('empresa_id', $carrinho->empresa_id)
		->first();

		$cepOrigem = str_replace("-", "", $config->cep);

		$somaPeso = $carrinho->somaPeso();
		$dimensoes = $carrinho->somaDimensoes();

		$stringUrl = "&sCepOrigem=$cepOrigem&sCepDestino=$cepDestino&nVlPeso=$somaPeso";

		$stringUrl .= "&nVlComprimento=".$dimensoes['comprimento']."&nVlAltura=".$dimensoes['altura']."&nVlLargura=".$dimensoes['largura']."&nCdServico=04014";


		$url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCdAvisoRecebimento=n&sCdMaoPropria=n&nVlValorDeclarado=0&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3&nCdFormato=1" . $stringUrl;

		$unparsedResult = file_get_contents($url);
		$parsedResult = simplexml_load_string($unparsedResult);

		$stringUrl = "&sCepOrigem=$cepOrigem&sCepDestino=$cepDestino&nVlPeso=$somaPeso";

		$stringUrl .= "&nVlComprimento=".$dimensoes['comprimento']."&nVlAltura=".$dimensoes['altura']."&nVlLargura=".$dimensoes['largura']."&nCdServico=04510";

		$url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCdAvisoRecebimento=n&sCdMaoPropria=n&nVlValorDeclarado=0&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3&nCdFormato=1" . $stringUrl;

		$unparsedResultSedex = file_get_contents($url);
		$parsedResultSedex = simplexml_load_string($unparsedResultSedex);

		$retorno = array(
			'preco_sedex' => strval($parsedResult->cServico->Valor),
			'prazo_sedex' => strval($parsedResult->cServico->PrazoEntrega),

			'preco' => strval($parsedResultSedex->cServico->Valor),
			'prazo' => strval($parsedResultSedex->cServico->PrazoEntrega)
		);

		return $retorno;
	}

	public function curtirProduto($link, $id){
		$link = strtolower($link);
		$dadosDefault = $this->getDadosDefault($link);

		$produtoEcommerceHelper = new PedidoEcommerceHelper();
		$user = $produtoEcommerceHelper->getUserLogado();

		if($user == null){
			session()->flash('mensagem_erro', 'Faça o login para adicionar aos favoritos!');
			return redirect('/loja/' . $link . '/login');
		}

		try{
			$produto = ProdutoEcommerce::find($id);

			$c = CurtidaProdutoEcommerce::
			where('produto_id', $produto->id)
			->where('cliente_id', $user['cliente_id'])
			->first();

			if($c != null){
				$c->delete();
				session()->flash('mensagem_erro', $produto->produto->nome . " removido da sua lista de favoritos!");
			}else{
				$data = [
					'produto_id' => $produto->id,
					'cliente_id' => $user['cliente_id']
				];
				CurtidaProdutoEcommerce::create($data);
				session()->flash('mensagem_sucesso', $produto->produto->nome . " adicionado a sua lista de favoritos!");
			}

		}catch(\Exception $e){
			session()->flash('mensagem_erro', 'Erro: ' . $e->getMessage());
		}
		return redirect()->back();
	}

	public function pedidoDetalhe($link, $id){
		$produtoEcommerceHelper = new PedidoEcommerceHelper();
		$user = $produtoEcommerceHelper->getUserLogado();
		$pedido = PedidoEcommerce::
		where('id', $id)
		->where('cliente_id', $user['cliente_id'])
		->first();

		$dadosDefault = $this->getDadosDefault($link);

		$config = $this->getConfig($link);

		$default = $this->getDadosDefault($link);

		return view($dadosDefault['template'].'/pedido_detalhe')
		->with('pedido', $pedido)
		->with('default', $default)
		->with('cart', true)
		->with('rota', $default['rota'])
		->with('title', 'Pedido finalizado');
	}

	public function pesquisa(Request $request, $link){
		$link = strtolower($link);
		$dadosDefault = $this->getDadosDefault($link);

		$produtos = ProdutoEcommerce::
		select('produto_ecommerces.*')
		->join('produtos', 'produtos.id' , '=', 'produto_ecommerces.produto_id')
		->where('produtos.nome', 'LIKE', "%$request->pesquisa%")
		->where('produto_ecommerces.empresa_id', $dadosDefault['config']->empresa_id)
		->join('categoria_produto_ecommerces', 'categoria_produto_ecommerces.id' , '=', 
			'produto_ecommerces.categoria_id')
		->where('produto_ecommerces.status', 1)
		->paginate(15);

		return view($dadosDefault['template'].'/produtos_categoria')
		->with('default', $dadosDefault)
		->with('produtos', $produtos)
		->with('pesquisa', $request->pesquisa)
		->with('categoria', null)
		->with('shop', true)
		
		->with('rota', $dadosDefault['rota'])
		->with('title', 'Pesquisa');
	}

}
