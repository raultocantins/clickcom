<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdutoDelivery;
use App\Models\CategoriaProdutoDelivery;
use App\Models\ImagensProdutoDelivery;
use App\Models\TamanhoPizza;
use App\Models\ProdutoPizza;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\Tributacao;
use App\Models\ConfigNota;
use App\Models\ClienteDelivery;

class DeliveryConfigProdutoController extends Controller
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
        $produtos = ProdutoDelivery::
        where('empresa_id', $this->empresa_id)
        ->paginate(40);

        return view('produtoDelivery/list')
        ->with('produtos', $produtos)
        ->with('produtoJs', true)
        ->with('links', true)
        ->with('title', 'Produtos de Delivery');
    }

    public function pesquisa(Request $request){
        $pesquisa = $request->pesquisa;
        $produtos = ProdutoDelivery::
        join('produtos', 'produto_deliveries.produto_id', '=', 'produtos.id')
        ->where('produtos.empresa_id', $this->empresa_id)
        ->where('produtos.nome', 'LIKE', "%$pesquisa%")
        ->paginate(40);

        return view('produtoDelivery/list')
        ->with('produtos', $produtos)
        ->with('produtoJs', true)
        ->with('links', true)
        ->with('title', 'Produtos de Delivery');
    }

    public function new(){
        $produtos = Produto::
        where('empresa_id', $this->empresa_id)
        ->orderBy('nome')
        ->get();

        $tamanhos = TamanhoPizza::
        where('empresa_id', $this->empresa_id)
        ->get();
        $categorias = CategoriaProdutoDelivery::
        where('empresa_id', $this->empresa_id)
        ->get();

        if(sizeof($categorias) == 0){
            session()->flash("mensagem_erro", "Cadastre uma categoria para o delivery!");
            return redirect('/deliveryCategoria/new');
        }

        return view('produtoDelivery/register')
        ->with('title', 'Cadastrar Produto para Delivery')
        ->with('categorias', $categorias)
        ->with('produtos', $produtos)
        ->with('tamanhos', $tamanhos)
        ->with('produtoJs', true);
    }

    public function save(Request $request){

        $produto = $request->input('produto');
        $categoria = Categoria::where('empresa_id', $this->empresa_id)->first();
        $tributacao = Tributacao::where('empresa_id', $this->empresa_id)->first();
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
                'largura' => 0,
                'comprimento' => 0,
                'altura' => 0,
                'peso_liquido' => 0,
                'peso_bruto' => 0,
                'empresa_id' => $this->empresa_id
            ];
            $produto = Produto::create($arr);
            
        }

        $catPizza = false;


        $request->merge([ 'status' => $request->input('status') ? true : false ]);
        $request->merge([ 'destaque' => $request->input('destaque') ? true : false ]);
        $request->merge([ 'ingredientes' => $request->input('ingredientes') ?? '']);
        $request->merge([ 'descricao' => $request->input('descricao') ?? '']);
        $request->merge([ 'produto_id' => $request->produto_id != 'null' ? $request->produto_id : $produto->id]);


        if(strpos(strtolower($categoria->nome), 'izza') !== false){
            $request->merge([ 'valor' => 0]);
            $request->merge([ 'valor_anterior' => 0]);

        }else{
            $request->merge([ 'valor' => str_replace(",", ".", $request->valor)]);
            $request->merge([ 'valor_anterior' => str_replace(",", ".", $request->valor_anterior ?? 0)]);
        }

        $this->_validate($request);


        $result = ProdutoDelivery::create($request->all());

        if(strpos(strtolower($categoria->nome), 'izza') !== false){
            $tamanhosPizza = TamanhoPizza::all();

            foreach($tamanhosPizza as $t){
                $res = ProdutoPizza::create([
                    'produto_id' => $result->id,
                    'tamanho_id' => $t->id,
                    'valor' => str_replace(",", ".", $request->input('valor_'.$t->nome))
                ]);
            }

        }

        if($result){

            session()->flash("mensagem_sucesso", "Produto cadastrado com sucesso!");
        }else{

            session()->flash('mensagem_erro', 'Erro ao cadastrar produto!');
        }

        return redirect('/deliveryProduto');
    }

    public function saveImagem(Request $request){

        $file = $request->file('file');
        $produtoDeliveryId = $request->id;

        $extensao = $file->getClientOriginalExtension();
        $nomeImagem = md5($file->getClientOriginalName()).".".$extensao;
        $request->merge([ 'path' => $nomeImagem ]);
        $request->merge([ 'produto_id' => $produtoDeliveryId ]);

        $produtoDelivery = ProdutoDelivery::find($produtoDeliveryId);

        $upload = $file->move(public_path('imagens_produtos'), $nomeImagem);

        $result = ImagensProdutoDelivery::create($request->all());

        if($result){

            session()->flash("mensagem_sucesso", "Imagem cadastrada com sucesso!");
        }else{

            session()->flash('mensagem_erro', 'Erro ao cadastrar produto!');
        }

        return redirect('/deliveryProduto/galeria/'.$produtoDeliveryId );

    }

    public function edit($id){
        $tamanhos = TamanhoPizza::
        where('empresa_id', $this->empresa_id)
        ->get();
        $produto = new ProdutoDelivery();
        $categorias = CategoriaProdutoDelivery::
        where('empresa_id', $this->empresa_id)
        ->get();
        $produtos = Produto::orderBy('nome')->
        where('empresa_id', $this->empresa_id)
        ->get();

        $resp = $produto
        ->where('id', $id)->first();  
        if(valida_objeto($resp)){

            return view('produtoDelivery/register')
            ->with('produto', $resp)
            ->with('categorias', $categorias)
            ->with('tamanhos', $tamanhos)
            ->with('produtos', $produtos)
            ->with('produtoJs', true)
            ->with('title', 'Editar Produto de Delivery');
        }else{
            return redirect('/403');
        }

    }


    public function alterarDestaque($id){
        $produto = new ProdutoDelivery(); //Model
        $categorias = CategoriaProdutoDelivery::
        where('empresa_id', $this->empresa_id)
        ->get();
        $resp = $produto
        ->where('id', $id)->first(); 

        $resp->destaque = !$resp->destaque;
        $resp->save(); 

        echo json_encode($resp);
    }

    public function alterarStatus($id){
        $produto = new ProdutoDelivery(); //Model
        $categorias = CategoriaProdutoDelivery::
        where('empresa_id', $this->empresa_id)
        ->get();
        $resp = $produto
        ->where('id', $id)->first();  

        $resp->status = !$resp->status;
        $resp->save();
        echo json_encode($resp);

    }


    public function galeria($id){
        $produto = new ProdutoDelivery(); //Model

        $resp = $produto
        ->where('id', $id)->first();  
        if(valida_objeto($resp)){
            return view('produtoDelivery/galery')
            ->with('produto', $resp)
            ->with('title', 'Galeria de Produto');
        }else{
            return redirect('/403');
        }
    }

    public function update(Request $request){
    	$produto = new ProdutoDelivery();

    	$id = $request->input('id');
    	$resp = $produto
    	->where('id', $id)->first(); 

        $this->_validate($request);

        $resp->categoria_id = $request->categoria_id;
        $resp->ingredientes = $request->ingredientes ?? '';
        $resp->descricao = $request->descricao ?? '';
        $resp->valor = str_replace(",", ".", $request->valor);
        $resp->valor_anterior = str_replace(",", ".", $request->valor_anterior);
        $resp->limite_diario = $request->limite_diario;
        $resp->destaque = $request->input('destaque') ? true : false;
        $resp->status = $request->input('status') ? true : false;


        $controlUpdatePizza = [];
        foreach($resp->pizza as $p){
            $p->valor = str_replace(",", ".", $request->input('valor_'.$p->tamanho->nome));
            $p->save();
            array_push($controlUpdatePizza, $p->tamanho->id);
        }
        if(strpos(strtolower($resp->categoria->nome), 'izza') !== false){
            $tamanhosPizza = TamanhoPizza::
            where('empresa_id', $this->empresa_id)
            ->get();
            if(count($tamanhosPizza) > count($resp->pizza)){
            //precisa inserir tambem
                foreach($tamanhosPizza as $t){
                    if(!in_array($t->id, $controlUpdatePizza)){
                    //entao insere
                        $res = ProdutoPizza::create([
                            'produto_id' => $resp->id,
                            'tamanho_id' => $t->id,
                            'valor' => str_replace(",", ".", $request->input('valor_'.$t->nome))
                        ]);
                    }
                }
            }
        }
        

        $result = $resp->save();
        if($result){
            session()->flash('mensagem_sucesso', 'Produto editado com sucesso!');
        }else{
            session()->flash('mensagem_erro', 'Erro ao editar produto!');
        }

        return redirect('/deliveryProduto'); 
    }

    public function delete($id){
        $produto = ProdutoDelivery
        ::where('id', $id)
        ->first();
        if(valida_objeto($produto)){
            foreach ($produto->galeria as $g) {
                $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
                if(file_exists($public . 'imagens_produtos/'.$g->path))
                    unlink($public . 'imagens_produtos/'.$g->path);
            }

            if($produto->delete()){
                session()->flash('mensagem_sucesso', 'Registro removido!');
            }else{
                session()->flash('mensagem_erro', 'Erro ao remover!');
            }
            return redirect('/deliveryProduto');
        }else{
            return redirect('/403');
        }
    }

    public function deleteImagem($id){
        $imagem = ImagensProdutoDelivery
        ::where('id', $id)
        ->first();

        if(valida_objeto($imagem->produto)){

            $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
            if(file_exists($public . 'imagens_produtos/'.$imagem->path))
                unlink($public . 'imagens_produtos/'.$imagem->path);

            if($imagem->delete()){
                session()->flash('mensagem_sucesso', 'Imagem removida!');
            }else{
                session()->flash('mensagem_erro', 'Erro!');
            }
            return redirect('/deliveryProduto/galeria/'.$imagem->produto_id);
        }
    }


    private function _validate(Request $request, $fileExist = true){

        $catPizza = false;
        $categoria = CategoriaProdutoDelivery::
        where('id', $request->categoria_id)
        ->first();

        if(strpos(strtolower($categoria->nome), 'izza') !== false){
            $catPizza = true;
        }

        $rules = [
            'produto' => $request->id > 0 ? '' : 'required',
            'ingredientes' => 'max:255',
            'descricao' => 'max:255',
            'valor' => $catPizza ? 'required' : '',
            'limite_diario' => 'required'
        ];

        $messages = [
            'produto.required' => 'O campo produto é obrigatório.',
            'produto.min' => 'Selecione um produto.',
            'ingredientes.required' => 'O campo ingredientes é obrigatório.',
            'ingredientes.max' => '255 caracteres maximos permitidos.',
            'descricao.required' => 'O campo descricao é obrigatório.',
            'descricao.max' => '255 caracteres maximos permitidos.',
            'valor.required' => 'O campo valor é obrigatório.',
            'limite_diario.required' => 'O campo limite diário é obrigatório',
        ];

        if($catPizza){
            $tamanhosPizza = TamanhoPizza::
            where('empresa_id', $this->empresa_id)
            ->get();

            foreach($tamanhosPizza as $t){
                $rules['valor_'.$t->nome] = 'required';
                $messages['valor_'.$t->nome.'.required'] = 'Campo obrigatório ' . $t->nome;
            }
        }

        $this->validate($request, $rules, $messages);
    }

    public function push($id){
        $produto = ProdutoDelivery::
        where('id', $id)
        ->first();
        if(valida_objeto($produto)){
            $clientes = ClienteDelivery::orderBy('nome')
            ->where('empresa_id', $this->empresa_id)
            ->get();

            return view('push/new')
            ->with('pushJs', true)
            ->with('titulo', $this->randomTitles())
            ->with('clientes', $clientes)
            ->with('mensagem', $this->randomMensagem($produto))
            ->with('imagem', isset($produto->galeria[0]) ? $produto->galeria[0]->path : '')
            ->with('referencia', $produto->id)
            ->with('title', 'Nova Push');
        }else{
            return redirect('/403');
        }
    }

    private function randomTitles(){
        $titles = [
            'Mega oferta de Hoje',
            'Promoção imperdivel',
            'Não perca isso',
            'Não deixe de comprar'
        ];
        return $titles[rand(0,3)];
    }

    private function randomMensagem($produto){
        $messages = [
            $produto->produto->nome.' por apenas, R$ '.$produto->valor,
            $produto->produto->nome. ' de R$'. $produto->valor_anterior.' por apenas R$'. 
            $produto->valor,
            'Peca já o seu '.$produto->produto->nome. ' o melhor :)',
            'Promoção de hoje '. $produto->produto->nome. ' venha conferir'
        ];
        return $messages[rand(0,3)];
    }
}
