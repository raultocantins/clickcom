<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Estoque;
use App\Models\Categoria;
use App\Models\ConfigNota;
use App\Models\Tributacao;
use App\Rules\EAN13;
use App\Rules\ValidaValor;
use App\Helpers\StockMove;
use App\Helpers\ProdutoGrade;
use App\Models\CategoriaProdutoDelivery;
use App\Models\ProdutoDelivery;
use App\Models\ProdutoListaPreco;
use App\Models\ImagensProdutoDelivery;
use App\Models\ItemDfe;
use App\Models\Marca;
use App\Models\SubCategoria;
use App\Models\AlteracaoEstoque;
use Dompdf\Dompdf;
use App\Models\DivisaoGrade;
use Illuminate\Support\Str;
use App\Models\CategoriaProdutoEcommerce;
use App\Models\ProdutoEcommerce;
use App\Models\ImagemProdutoEcommerce;
use App\Imports\ProdutoImport;
use Maatwebsite\Excel\Facades\Excel;
use setasign\Fpdi\TcpdfFpdi;

class ProductController extends Controller
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
        $produtos = Produto::
        where('empresa_id', $this->empresa_id)
        ->groupBy('referencia_grade')
        ->orderBy('inativo')
        ->orderBy('nome', 'asc')
        ->paginate(15);

        $totalGeralPrdutos = sizeof(Produto::
        where('empresa_id', $this->empresa_id)
        ->groupBy('referencia_grade')
        ->get());

        $categorias = Categoria:: 
        where('empresa_id', $this->empresa_id)
        ->get();

        $produtos = $this->setaEstoque($produtos);

        return view('produtos/list')
        ->with('produtos', $produtos)
        ->with('links', true)
        ->with('totalGeralPrdutos', $totalGeralPrdutos)
        ->with('categorias', $categorias)
        ->with('title', 'Produtos');
    }

    private function setaEstoque($produtos){
        foreach($produtos as $p){
            $estoque = Estoque::where('produto_id', $p->id)->first();
            if($p->grade){
                $quantidade = Produto::produtosDaGradeSomaEstoque($p->referencia_grade);
                $p->estoque_atual = $quantidade;

            }else{
                $p->estoque_atual = $estoque == null ? 0 : $estoque->quantidade;
            }
        }
        return $produtos;
    }

    private function incluiDigito($code){
        $weightflag = true;
        $sum = 0;
        for ($i = strlen($code) - 1; $i >= 0; $i--) {
            $sum += (int)$code[$i] * ($weightflag?3:1);
            $weightflag = !$weightflag;
        }
        return $code . (10 - ($sum % 10)) % 10;
    }

    public function gerarCodigoEan(){
        try{
            $rand = rand(11111, 99999);
            $code = $this->incluiDigito('7891000'.$rand);
            return response()->json($code, 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }

    public function new(Request $request){
        $categoria = Categoria::
        where('empresa_id', $request->empresa_id)
        ->first();
        if($categoria == null){
            //nao tem categoria
            session()->flash('mensagem_erro', 'Cadastre ao menos uma categoria!');
            return redirect('/categorias');
        }

        $anps = Produto::lista_ANP();
        $natureza = Produto::
        firstNatureza($request->empresa_id);

        if($natureza == null){

            session()->flash('mensagem_erro', 'Cadastre uma natureza de operação!');
            return redirect('/naturezaOperacao');
        }

        $categorias = Categoria::
        where('empresa_id', $request->empresa_id)
        ->get();

        $categoriasDelivery = [];

        $listaCST_PIS_COFINS = Produto::listaCST_PIS_COFINS();
        $listaCST_IPI = Produto::listaCST_IPI();
        $tributacao = Tributacao::
        where('empresa_id', $request->empresa_id)
        ->first();

        if($tributacao == null){
            session()->flash('mensagem_erro', 'Informe a tributação padrão!');
            return redirect('/tributos');
        }

        if($tributacao->regime == 1){
            $listaCSTCSOSN = Produto::listaCST();
        }else{
            $listaCSTCSOSN = Produto::listaCSOSN();
        }

        $unidadesDeMedida = Produto::unidadesMedida();
        $config = ConfigNota::
        where('empresa_id', $request->empresa_id)
        ->first();

        if($config == null){
            session()->flash('mensagem_erro', 'Informe a configuração do emitente!');
            return redirect('/configNF');
        }

        $divisoes = DivisaoGrade::
        where('empresa_id', $request->empresa_id)
        ->where('sub_divisao', false)
        ->get();

        $subDivisoes = DivisaoGrade::
        where('empresa_id', $request->empresa_id)
        ->where('sub_divisao', true)
        ->get();

        $categoriasEcommerce = CategoriaProdutoEcommerce::
        where('empresa_id', $request->empresa_id)
        ->get();

        $marcas = Marca::
        where('empresa_id', $request->empresa_id)
        ->get();

        $subs = SubCategoria::
        select('sub_categorias.*')
        ->join('categorias', 'categorias.id', '=', 'sub_categorias.categoria_id')
        ->where('empresa_id', $request->empresa_id)
        ->get();

        return view('produtos/register')
        ->with('categorias', $categorias)
        ->with('categoriasEcommerce', $categoriasEcommerce)
        ->with('unidadesDeMedida', $unidadesDeMedida)
        ->with('listaCSTCSOSN', $listaCSTCSOSN)
        ->with('listaCST_PIS_COFINS', $listaCST_PIS_COFINS)
        ->with('listaCST_IPI', $listaCST_IPI)
        ->with('marcas', $marcas)
        ->with('anps', $anps)
        ->with('subs', $subs)
        ->with('config', $config)
        ->with('divisoes', $divisoes)
        ->with('subDivisoes', $subDivisoes)
        ->with('tributacao', $tributacao)
        ->with('natureza', $natureza)
        ->with('categoriasDelivery', $categoriasDelivery)
        ->with('produtoJs', true)
        ->with('gradeJs', true)
        ->with('contratoJs', true)
        ->with('title', 'Cadastrar Produto');
    }

    public function save(Request $request){
        $produto = new Produto();

        $this->_validate($request);
        if($request->ecommerce){
            $this->_validateEcommerce($request);
        }

        $anps = Produto::lista_ANP();
        $descAnp = '';

        foreach($anps as $key => $a){
            if($key == $request->anp){
                $descAnp = $a;
            }
        }

        $request->merge([ 'composto' => $request->input('composto') ? true : false ]);
        $request->merge([ 'inativo' => $request->input('inativo') ? true : false ]);
        $request->merge([ 'valor_livre' => $request->input('valor_livre') ? true : false ]);
        $request->merge([ 'gerenciar_estoque' => $request->input('gerenciar_estoque') ? true : false ]);
        $request->merge([ 'reajuste_automatico' => $request->input('reajuste_automatico') ? true : false ]);
        $request->merge([ 'valor_venda' => str_replace(",", ".", $request->input('valor_venda'))]);
        $request->merge([ 'percentual_lucro' => str_replace(",", ".", $request->input('percentual_lucro'))]);
        $request->merge([ 'valor_compra' => str_replace(",", ".", $request->input('valor_compra'))]);
        $request->merge([ 'conversao_unitaria' => $request->input('conversao_unitaria') ? 
            $request->input('conversao_unitaria') : 1]);
        $request->merge([ 'codBarras' => $request->input('codBarras') ?? 'SEM GTIN']);
        $request->merge([ 'CST_CSOSN' => $request->input('CST_CSOSN') ?? '0']);
        $request->merge([ 'CST_CSOSN_EXP' => $request->input('CST_CSOSN_EXP') ?? '']);
        $request->merge([ 'CST_PIS' => $request->input('CST_PIS') ?? '0']);
        $request->merge([ 'CST_COFINS' => $request->input('CST_COFINS') ?? '0']);
        $request->merge([ 'CST_IPI' => $request->input('CST_IPI') ?? '0']);
        $request->merge([ 'codigo_anp' => $request->anp != '' ? $request->anp : '']);
        $request->merge([ 'descricao_anp' => $request->anp != '' ? $request->anp : '']);

        $request->merge([ 'perc_glp' => $request->perc_glp != '' ? __replace($request->perc_glp) : '']);
        $request->merge([ 'perc_gnn' => $request->perc_gnn != '' ? __replace($request->perc_gnn) : '']);
        $request->merge([ 'perc_gni' => $request->perc_gni != '' ? __replace($request->perc_gni) : '']);
        $request->merge([ 'valor_partida' => $request->valor_partida != '' ? __replace($request->valor_partida) : '']);
        $request->merge([ 'unidade_tributavel' => $request->unidade_tributavel != '' ? 
            $request->unidade_tributavel : '']);
        $request->merge([ 'quantidade_tributavel' => $request->quantidade_tributavel != '' ? __replace($request->quantidade_tributavel) : '']);

        $request->merge([ 'cListServ' => $request->cListServ ?? '']);
        $request->merge([ 'alerta_vencimento' => $request->alerta_vencimento ?? 0]);
        $request->merge([ 'imagem' => '' ]);
        $request->merge([ 'estoque_minimo' => $request->estoque_minimo ?? 0]);
        $request->merge([ 'referencia_balanca' => $request->referencia_balanca ?? 0]);
        $request->merge([ 'referencia' => $request->referencia ?? '']);

        $request->merge([ 'largura' => $request->largura ?? 0]);
        $request->merge([ 'comprimento' => $request->comprimento ?? 0]);
        $request->merge([ 'altura' => $request->altura ?? 0]);
        $request->merge([ 'peso_liquido' => $request->peso_liquido ?? 0]);
        $request->merge([ 'peso_bruto' => $request->peso_bruto ?? 0]);
        $request->merge([ 'limite_maximo_desconto' => 
            $request->limite_maximo_desconto ?? 0]);
        $request->merge([ 'perc_icms' => $request->perc_icms ? __replace($request->perc_icms) : 0]);
        $request->merge([ 'perc_pis' => $request->perc_pis ? __replace($request->perc_pis) : 0]);
        $request->merge([ 'perc_cofins' => $request->perc_cofins ? __replace($request->perc_cofins) : 0]);
        $request->merge([ 'perc_ipi' => $request->perc_ipi ? __replace($request->perc_ipi) : 0]);
        $request->merge([ 'pRedBC' => $request->pRedBC ? __replace($request->pRedBC) : 0]);
        $request->merge([ 'cBenef' => $request->cBenef ? $request->cBenef : '']);
        $request->merge([ 'CEST' => $request->CEST ?? '']);

        $request->merge([ 'perc_icms_interestadual' => $request->perc_icms_interestadual ? __replace($request->perc_icms_interestadual) : 0]);
        $request->merge([ 'perc_icms_interno' => $request->perc_icms_interno ? __replace($request->perc_icms_interno) : 0]);
        $request->merge([ 'perc_fcp_interestadual' => $request->perc_fcp_interestadual ? __replace($request->perc_fcp_interestadual) : 0]);

        $request->merge([ 'renavam' => $request->renavam ?? '']);
        $request->merge([ 'placa' => $request->placa ?? '']);
        $request->merge([ 'chassi' => $request->chassi ?? '']);
        $request->merge([ 'combustivel' => $request->combustivel ?? '']);
        $request->merge([ 'ano_modelo' => $request->ano_modelo ?? '']);
        $request->merge([ 'cor_veiculo' => $request->cor_veiculo ?? '']);

        $request->merge([ 'lote' => $request->lote ?? '']);
        $request->merge([ 'vencimento' => $request->vencimento ?? '']);

        $request->merge([ 'valor_locacao' => $request->valor_locacao ? __replace($request->valor_locacao) : 0 ]);

        if(!$request->grade){

            $request->merge([ 'referencia_grade' => Str::random(20)]);
            $request->merge([ 'grade' => false ]);
            $request->merge([ 'str_grade' => '' ]);

            $result = $produto->create($request->all());
            $produto = Produto::find($result->id);
            $nomeImagem = $this->salveImagemProduto($request, $produto); 

            if($request->ecommerce){
                $this->salvarProdutoEcommerce($request, $produto, $nomeImagem);
            }


        //     if($request->atribuir_delivery){
        //         $this->salvarProdutoNoDelivery($request, $produto); 
        // // salva o produto no delivery
        //     }


            $mensagem_sucesso = "Produto cadastrado com sucesso!";
            $estoque = $request->estoque;
            if($estoque){
                $estoque = __replace($request->estoque);
                $data = [
                    'produto_id' => $produto->id,
                    'usuario_id' => get_id_user(),
                    'quantidade' => $estoque,
                    'tipo' => 'incremento',
                    'observacao' => '',
                    'empresa_id' => $this->empresa_id
                ];

                AlteracaoEstoque::create($data);
                $stockMove = new StockMove();
                $result = $stockMove->pluStock($produto->id, 
                    $estoque, str_replace(",", ".", $produto->valor_compra));
                $mensagem_sucesso = "Produto cadastrado com sucesso, e atribuido estoque!";
            }

            if($result){
                session()->flash("mensagem_sucesso", $mensagem_sucesso);
            }else{
                session()->flash('mensagem_erro', 'Erro ao cadastrar produto!');
            }
            return redirect('/produtos');

        }else{

            $produtoGrade = new ProdutoGrade();

            $nomeImagem = "";
            if($request->hasFile('file')){
                $nomeImagem = $this->salveImagemProdutoTemp($request); 
            }
            $res = $produtoGrade->salvar($request, $nomeImagem);

            if($res == "ok"){
                session()->flash("mensagem_sucesso", "Produto cadastrado como grade!");
            }else{
                session()->flash('mensagem_erro', 'Erro ao cadastrar produto, confira a grade!');
            }

            return redirect('/produtos');
        }
    }

    public function edit($id){
        $natureza = Produto::firstNatureza($this->empresa_id);
        $anps = Produto::lista_ANP();

        if($natureza == null){
            session()->flash('mensagem_erro', 'Cadastre uma natureza de operação!');
            return redirect('/naturezaOperacao');
        }

        $produto = new Produto(); 

        // $listaCSTCSOSN = Produto::listaCSTCSOSN();
        $listaCST_PIS_COFINS = Produto::listaCST_PIS_COFINS();
        $listaCST_IPI = Produto::listaCST_IPI();

        $categorias = Categoria::
        where('empresa_id', $this->empresa_id)
        ->get();

        $unidadesDeMedida = Produto::unidadesMedida();
        $config = ConfigNota::
        where('empresa_id', $this->empresa_id)
        ->first();

        $tributacao = Tributacao::
        where('empresa_id', $this->empresa_id)
        ->first();

        if($tributacao == null){
            session()->flash('mensagem_erro', 'Informe a tributação padrão!');
            return redirect('/tributos');
        }

        $resp = $produto
        ->where('id', $id)->first();  

        $categoriasDelivery = [];

        if($tributacao->regime == 1){
            $listaCSTCSOSN = Produto::listaCST();
        }else{
            $listaCSTCSOSN = Produto::listaCSOSN();
        }

        if($tributacao == null){

            session()->flash('mensagem_erro', 'Informe a tributação padrão!');
            return redirect('tributos');
        }

        $divisoes = DivisaoGrade::
        where('empresa_id', $this->empresa_id)
        ->where('sub_divisao', false)
        ->get();

        $subDivisoes = DivisaoGrade::
        where('empresa_id', $this->empresa_id)
        ->where('sub_divisao', true)
        ->get();

        $marcas = Marca::
        where('empresa_id', $this->empresa_id)
        ->get();

        $subs = SubCategoria::
        select('sub_categorias.*')
        ->join('categorias', 'categorias.id', '=', 'sub_categorias.categoria_id')
        ->where('empresa_id', $this->empresa_id)
        ->get();

        if(valida_objeto($resp)){
            if(!$resp->grade){
                return view('produtos/register')
                ->with('produto', $resp)
                ->with('config', $config)
                ->with('tributacao', $tributacao)
                ->with('marcas', $marcas)
                ->with('subs', $subs)
                ->with('natureza', $natureza)
                ->with('divisoes', $divisoes)
                ->with('subDivisoes', $subDivisoes)
                ->with('listaCSTCSOSN', $listaCSTCSOSN)
                ->with('listaCST_PIS_COFINS', $listaCST_PIS_COFINS)
                ->with('listaCST_IPI', $listaCST_IPI)
                ->with('categoriasDelivery', $categoriasDelivery)
                ->with('anps', $anps)
                ->with('unidadesDeMedida', $unidadesDeMedida)
                ->with('categorias', $categorias)
                ->with('produtoJs', true)
                ->with('gradeJs', true)
                ->with('title', 'Editar Produto');
            }else{
                return view('produtos/register_grade')
                ->with('produto', $resp)
                ->with('config', $config)
                ->with('tributacao', $tributacao)
                ->with('marcas', $marcas)
                ->with('subs', $subs)
                ->with('natureza', $natureza)
                ->with('divisoes', $divisoes)
                ->with('subDivisoes', $subDivisoes)
                ->with('listaCSTCSOSN', $listaCSTCSOSN)
                ->with('listaCST_PIS_COFINS', $listaCST_PIS_COFINS)
                ->with('listaCST_IPI', $listaCST_IPI)
                ->with('categoriasDelivery', $categoriasDelivery)
                ->with('anps', $anps)
                ->with('unidadesDeMedida', $unidadesDeMedida)
                ->with('categorias', $categorias)
                ->with('produtoJs', true)
                ->with('gradeJs', true)
                ->with('title', 'Editar Produto Grade');
            }
        }else{
            return redirect('/403');
        }

    }

    public function editGrade($id){
        $natureza = Produto::firstNatureza($this->empresa_id);
        $anps = Produto::lista_ANP();

        if($natureza == null){
            session()->flash('mensagem_erro', 'Cadastre uma natureza de operação!');
            return redirect('/naturezaOperacao');
        }

        $produto = new Produto(); 

        // $listaCSTCSOSN = Produto::listaCSTCSOSN();
        $listaCST_PIS_COFINS = Produto::listaCST_PIS_COFINS();
        $listaCST_IPI = Produto::listaCST_IPI();

        $categorias = Categoria::
        where('empresa_id', $this->empresa_id)
        ->get();

        $unidadesDeMedida = Produto::unidadesMedida();
        $config = ConfigNota::
        where('empresa_id', $this->empresa_id)
        ->first();

        $tributacao = Tributacao::
        where('empresa_id', $this->empresa_id)
        ->first();

        $resp = $produto
        ->where('id', $id)->first();  

        $categoriasDelivery = [];

        if($tributacao->regime == 1){
            $listaCSTCSOSN = Produto::listaCST();
        }else{
            $listaCSTCSOSN = Produto::listaCSOSN();
        }

        if($tributacao == null){

            session()->flash('mensagem_erro', 'Informe a tributação padrão!');
            return redirect('tributos');
        }

        $divisoes = DivisaoGrade::
        where('empresa_id', $this->empresa_id)
        ->where('sub_divisao', false)
        ->get();

        $subDivisoes = DivisaoGrade::
        where('empresa_id', $this->empresa_id)
        ->where('sub_divisao', true)
        ->get();

        $marcas = Marca::
        where('empresa_id', $this->empresa_id)
        ->get();

        $subs = SubCategoria::
        select('sub_categorias.*')
        ->join('categorias', 'categorias.id', '=', 'sub_categorias.categoria_id')
        ->where('empresa_id', $this->empresa_id)
        ->get();

        if(valida_objeto($resp)){
            return view('produtos/register')
            ->with('produto', $resp)
            ->with('config', $config)
            ->with('marcas', $marcas)
            ->with('subs', $subs)
            ->with('tributacao', $tributacao)
            ->with('natureza', $natureza)
            ->with('divisoes', $divisoes)
            ->with('subDivisoes', $subDivisoes)
            ->with('listaCSTCSOSN', $listaCSTCSOSN)
            ->with('listaCST_PIS_COFINS', $listaCST_PIS_COFINS)
            ->with('listaCST_IPI', $listaCST_IPI)
            ->with('categoriasDelivery', $categoriasDelivery)
            ->with('anps', $anps)
            ->with('unidadesDeMedida', $unidadesDeMedida)
            ->with('categorias', $categorias)
            ->with('produtoJs', true)
            ->with('title', 'Editar Produto');
        }else{
            return redirect('/403');
        }

    }

    private function salveImagemProduto($request, $produto){
        if($request->hasFile('file')){

            $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
            //unlink anterior
            if(file_exists($public.'imgs_produtos/'.$produto->imagem) && $produto->imagem != '')
                unlink($public.'imgs_produtos/'.$produto->imagem);

            $file = $request->file('file');

            $extensao = $file->getClientOriginalExtension();
            $nomeImagem = Str::random(25) . ".".$extensao;

            $upload = $file->move(public_path('imgs_produtos'), $nomeImagem);
            $produto->imagem = $nomeImagem;
            $produto->save();

            return $nomeImagem;
        }else{
            return "";
        }
    }

    private function salveImagemProdutoTemp($request){
        if($request->hasFile('file')){

            $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
            //unlink anterior

            $file = $request->file('file');

            $extensao = $file->getClientOriginalExtension();
            $nomeImagem = md5($file->getClientOriginalName()).".".$extensao;

            $upload = $file->move(public_path('imgs_produtos'), $nomeImagem);

            return $nomeImagem;
        }else{
            return "";
        }
    }

    public function pesquisa(Request $request){
        $pesquisa = $request->input('pesquisa');

        // $produtos = Produto::where('nome', 'LIKE', "%$pesquisa%")
        // ->where('empresa_id', $request->empresa_id)->get();

        $produtos = Produto::
        where('nome', 'LIKE', "%$pesquisa%")
        ->where('empresa_id', $this->empresa_id)
        ->groupBy('referencia_grade')
        ->orderBy('inativo')
        ->orderBy('id', 'desc')
        ->paginate(15);

        $categorias = Categoria::all();
        $produtos = $this->setaEstoque($produtos);


        return view('produtos/list')
        ->with('categorias', $categorias)
        ->with('produtos', $produtos)
        ->with('title', 'Filtro Produto');
    }

    public function filtroCategoria(Request $request){
        $categoria = $request->input('categoria');
        $estoque = $request->input('estoque');
        $pesquisa = $request->input('pesquisa');

        $porCodigoBarras = is_numeric($pesquisa);

        if($porCodigoBarras == 1){
            $query = Produto::where('codBarras', $pesquisa);
        }else{
            $query = Produto::where('nome', 'LIKE', "%$pesquisa%");
        }
        if($categoria != '-'){
            $query = Produto::where('categoria_id', $categoria);
        }

        $query->where('empresa_id', $request->empresa_id)
        ->groupBy('referencia_grade')
        ->orderBy('inativo')
        ->orderBy('id', 'desc');

        $produtos = $query->get();

        if($estoque != '--'){
            $temp = [];
            foreach($produtos as $p){
                if($estoque == 1){
                    if($p->estoque && $p->estoque->quantidade > 0){
                        array_push($temp, $p);
                    }
                }else{
                    if(!$p->estoque || $p->estoque->quantidade < 0){
                        array_push($temp, $p);
                    }
                }
            }
            $produtos = $temp;
        }

        $categorias = Categoria::
        where('empresa_id', $this->empresa_id)
        ->get();

        $categoria = Categoria::find($categoria);
        $produtos = $this->setaEstoque($produtos);

        if(sizeof($produtos) == 1 && $porCodigoBarras){
            return redirect('/produtos/edit/'.$produtos[0]->id);
        }

       
        return view('produtos/list')
        ->with('produtos', $produtos)
        ->with('categorias', $categorias)
        ->with('paraImprimir', true)
        ->with('categoria', $request->categoria)
        ->with('estoque', $estoque)
        ->with('pesquisa', $pesquisa)
        ->with('title', 'Filtro Produto');
    }

    public function relatorio(Request $request){
        $categoria = $request->input('categoria');
        $estoque = $request->input('estoque');
        $pesquisa = $request->input('pesquisa');

        $porCodigoBarras = is_numeric($pesquisa);

        if($porCodigoBarras == 1){
            $query = Produto::where('codBarras', $pesquisa);

        }else{
            $query = Produto::where('nome', 'LIKE', "%$pesquisa%");
        }
        if($categoria != '-'){
            $query = Produto::where('categoria_id', $categoria);
        }

        $query->where('empresa_id', $request->empresa_id);

        $produtos = $query->get();


        if($estoque != '-'){
            $temp = [];
            foreach($produtos as $p){
                if($estoque == 1){
                    if($p->estoque && $p->estoque->quantidade > 0){
                        array_push($temp, $p);
                    }
                }else{
                    if(!$p->estoque || $p->estoque->quantidade < 0){
                        array_push($temp, $p);
                    }
                }
            }
            $produtos = $temp;
        }

        $p = view('produtos/relatorio_produtos')
        ->with('produtos', $produtos);

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("relatorio produtos.pdf");


    }

    public function receita($id){
        $resp = Produto::
        where('id', $id)
        ->first();  

        $produtos = Produto::where('empresa_id', $this->empresa_id)->get();

        return view('produtos/receita')
        ->with('produto', $resp)
        ->with('produtos', $produtos)
        ->with('produtoJs', true)
        ->with('title', 'Receita do Produto');

    }

    public function update(Request $request){

        $product = new Produto();

        $id = $request->input('id');
        $resp = $product
        ->where('id', $id)->first(); 

        $this->_validate($request);

        $anps = Produto::lista_ANP();
        $descAnp = '';
        foreach($anps as $key => $a){
            if($key == $request->anp){
                $descAnp = $a;
            }
        }

        $resp->nome = $request->input('nome');
        $resp->categoria_id = $request->input('categoria_id');
        $resp->sub_categoria_id = $request->input('sub_categoria_id');
        $resp->marca_id = $request->input('marca_id');
        $resp->cor = $request->input('cor');
        $resp->valor_venda = str_replace(",", ".", $request->input('valor_venda'));
        $resp->valor_compra = str_replace(",", ".", $request->input('valor_compra'));

        $resp->percentual_lucro = str_replace(",", ".", $request->input('percentual_lucro'));
        $resp->NCM = $request->input('NCM');
        $resp->CEST = $request->input('CEST') ?? '';

        $resp->CST_CSOSN = $request->input('CST_CSOSN');
        $resp->CST_CSOSN_EXP = $request->input('CST_CSOSN_EXP');

        $resp->CST_PIS = $request->input('CST_PIS');
        $resp->CST_COFINS = $request->input('CST_COFINS');
        $resp->CST_IPI = $request->input('CST_IPI');
        // $resp->CFOP = $request->input('CFOP');
        $resp->unidade_venda = $request->input('unidade_venda');
        $resp->unidade_compra = $request->input('unidade_compra');
        $resp->conversao_unitaria = $request->input('conversao_unitaria') ? $request->input('conversao_unitaria') : $resp->conversao_unitaria;
        $resp->codBarras = $request->input('codBarras') ?? 'SEM GTIN';

        $resp->perc_icms = $request->perc_icms ? __replace($request->perc_icms) : 0;
        $resp->perc_pis = $request->perc_pis ? __replace($request->perc_pis) : 0;
        $resp->perc_cofins = $request->perc_cofins ? __replace($request->perc_cofins) : 0;
        $resp->perc_ipi = $request->perc_ipi ? __replace($request->perc_ipi) : 0;
        $resp->perc_iss = $request->perc_iss ? __replace($request->perc_iss) : 0;
        $resp->cListServ = $request->input('cListServ');

        $resp->CFOP_saida_estadual = $request->input('CFOP_saida_estadual');
        $resp->CFOP_saida_inter_estadual = $request->input('CFOP_saida_inter_estadual');
        $resp->codigo_anp = $request->input('anp') ?? '';
        $resp->perc_glp = $request->perc_glp ? __replace($request->perc_glp) : 0;
        $resp->perc_gnn = $request->perc_gnn ? __replace($request->perc_gnn) : 0;
        $resp->perc_gni = $request->perc_gni ? __replace($request->perc_gni) : 0;
        $resp->valor_partida = $request->valor_partida ? 
        __replace($request->valor_partida) : 0;

        $resp->quantidade_tributavel = $request->quantidade_tributavel ? 
        __replace($request->quantidade_tributavel) : 0;

        $resp->unidade_tributavel = $request->unidade_tributavel ?? '';

        $resp->descricao_anp = $request->anp ?? '';
        $resp->alerta_vencimento = $request->alerta_vencimento;
        $resp->origem = $request->origem;

        $resp->referencia = $request->referencia;
        $resp->referencia_balanca = $request->referencia_balanca;

        $resp->composto = $request->composto ? true : false;
        $resp->valor_livre = $request->valor_livre ? true : false;
        $resp->gerenciar_estoque = $request->gerenciar_estoque ? true : false;
        $resp->reajuste_automatico = $request->reajuste_automatico ? true : false;
        $resp->inativo = $request->inativo ? true : false;
        $resp->estoque_minimo = $request->estoque_minimo;

        $resp->pRedBC = __replace($request->pRedBC);
        $resp->cBenef = $request->cBenef;

        $resp->largura = $request->largura;
        $resp->comprimento = $request->comprimento;
        $resp->altura = $request->altura;
        $resp->peso_liquido = __replace($request->peso_liquido);
        $resp->peso_bruto = __replace($request->peso_bruto);
        $resp->limite_maximo_desconto = $request->limite_maximo_desconto;

        $resp->perc_icms_interestadual = $request->perc_icms_interestadual ? __replace($request->perc_icms_interestadual) : 0;
        $resp->perc_icms_interno = $request->perc_icms_interno ? __replace($request->perc_icms_interno) : 0;
        $resp->perc_fcp_interestadual = $request->perc_fcp_interestadual ? __replace($request->perc_fcp_interestadual) : 0;


        $resp->renavam = $request->renavam ?? '';
        $resp->placa = $request->placa ?? '';
        $resp->chassi = $request->chassi ?? '';
        $resp->combustivel = $request->combustivel ?? '';
        $resp->ano_modelo = $request->ano_modelo ?? '';
        $resp->cor_veiculo = $request->cor_veiculo ?? '';
        $resp->valor_locacao = $request->valor_locacao ? 
        __replace($request->valor_locacao) : 0;

        $resp->lote = $request->lote ?? '';
        $resp->vencimento = $request->vencimento ?? '';

        // $resp->percentual_lucro = __replace($request->percentual_lucro);

        $result = $resp->save();

        if($request->grade){

            $combinacoes = json_decode($request->combinacoes);

            $resp->grade = 1;
            $resp->str_grade = $combinacoes[0]->titulo;
            $result = $resp->save();

            $produtoGrade = new ProdutoGrade();

            $nomeImagem = "";
            if($request->hasFile('file')){
                $nomeImagem = $this->salveImagemProdutoTemp($request); 
            }
            $res = $produtoGrade->update($request, $nomeImagem, $resp->referencia_grade);

            if($res == "ok"){
                $mensagem_sucesso = "Produto editado com sucesso, alterado para grade!";
            }else{
                session()->flash('mensagem_erro', 'Erro ao editar produto, confira a grade!');
                return redirect('/produtos');
            }
        }else{
            $this->salveImagemProduto($request, $resp);

            $produto = $resp;
            $mensagem_sucesso = 'Produto editado com sucesso!';

            $estoque = $request->estoque;
            $stockMove = new StockMove();

            if($estoque){
                $estoque = __replace($request->estoque);
                if(!$produto->estoque){
                    $data = [
                        'produto_id' => $produto->id,
                        'usuario_id' => get_id_user(),
                        'quantidade' => $estoque,
                        'tipo' => 'incremento',
                        'observacao' => '',
                        'empresa_id' => $this->empresa_id
                    ];

                    AlteracaoEstoque::create($data);
                    $result = $stockMove->pluStock($produto->id, 
                        $estoque, str_replace(",", ".", $produto->valor_venda));
                    $mensagem_sucesso = "Produto editado com sucesso, e estoque atribuido!";
                }else{

                    if($produto->estoque->quantidade > $estoque || $produto->estoque->quantidade < $estoque){
                    //alterar

                        $tipo = '';
                        $valorAlterar = 0;
                        $estoqueAtual = $produto->estoque->quantidade;
                        if($estoqueAtual > $estoque){
                            $tipo = 'reducao';
                            $valorAlterar = $estoqueAtual - $estoque;
                        }else{
                            $tipo = 'incremento';
                            $valorAlterar = $estoque - $estoqueAtual;

                        }
                        $data = [
                            'produto_id' => $produto->id,
                            'usuario_id' => get_id_user(),
                            'quantidade' => $valorAlterar,
                            'tipo' => $tipo,
                            'observacao' => '',
                            'empresa_id' => $this->empresa_id
                        ];

                        AlteracaoEstoque::create($data);
                        if($produto->estoque->quantidade > $estoque){
                            $stockMove->pluStock($produto->id, 
                                $valorAlterar, str_replace(",", ".", $produto->valor_venda));
                        }else{
                            $stockMove->pluStock($produto->id, 
                                $valorAlterar, str_replace(",", ".", $produto->valor_venda));
                        }

                        $mensagem_sucesso = "Produto editado com sucesso, e atualizado estoque!";

                    }
                }
            }
        }
        if($result){
            if($request->atribuir_delivery){
                $this->updateProdutoNoDelivery($request, $resp);
            }
            session()->flash('mensagem_sucesso', $mensagem_sucesso);
        }else{
            session()->flash('mensagem_erro', 'Erro ao editar produto!');
        }
        if($resp->grade){
            return redirect('/produtos/grade/'.$resp->id); 
        }
        return redirect('/produtos'); 
    }

    public function delete($id){
        try{
            $produto = Produto
            ::where('id', $id)
            ->first();

            if(valida_objeto($produto)){

                $public = getenv('SERVIDOR_WEB') ? 'public/' : '';

                if(file_exists($public.'imgs_produtos/'.$produto->imagem) && $produto->imagem != '')
                    unlink($public.'imgs_produtos/'.$produto->imagem);

                try{
                    if($produto->grade){
                        $produtos = Produto::
                        where('referencia_grade', $produto->referencia_grade)
                        ->delete();
                    }else{
                        $produto->delete();
                    }


                    session()->flash('mensagem_sucesso', 'Registro removido!');
                }catch(\Exception $e){
                    session()->flash('mensagem_erro', 'Erro: ' . $e->getMessage());
                }
                return redirect('/produtos');


            }else{
                return redirect('/403');
            }
        }catch(\Exception $e){
            return view('errors.sql')
            ->with('title', 'Erro ao deletar produto')
            ->with('motivo', 'Não é possivel remover produtos, presentes vendas, compras ou pedidos!');
        }
    }

    private function _validate(Request $request){
        $rules = [
            'nome' => 'required|max:100',
            'valor_venda' => ['required', new ValidaValor],
            'valor_compra' => ['required', new ValidaValor],
            'categoria_id' => 'required',
            'percentual_lucro' => 'required',
            'NCM' => 'required|min:10',
            'perc_icms' => 'required',
            'perc_pis' => 'required',
            'perc_cofins' => 'required',
            'perc_ipi' => 'required',
            'codBarras' => [],
            'CFOP_saida_estadual' => 'required',
            'CFOP_saida_inter_estadual' => 'required',
            'file' => 'max:700',
            'lote' => 'max:10',
            'vencimento' => 'max:10',
            // 'CEST' => 'required'
        ];

        $messages = [
            'nome.required' => 'O campo nome é obrigatório.',
            'categoria_id.required' => 'O campo categoria é obrigatório.',
            'NCM.required' => 'O campo NCM é obrigatório.',
            'NCM.min' => 'NCM precisa de 8 digitos.',
            // 'CFOP.required' => 'O campo CFOP é obrigatório.',
            'CEST.required' => 'O campo CEST é obrigatório.',
            'valor_venda.required' => 'O campo valor de venda é obrigatório.',
            'valor_compra.required' => 'O campo valor de compra é obrigatório.',
            'percentual_lucro.required' => 'O campo % lucro é obrigatório.',
            'nome.max' => '100 caracteres maximos permitidos.',
            'perc_icms.required' => 'O campo %ICMS é obrigatório.',
            'perc_pis.required' => 'O campo %PIS é obrigatório.',
            'perc_cofins.required' => 'O campo %COFINS é obrigatório.',
            'perc_ipi.required' => 'O campo %IPI é obrigatório.',
            'CFOP_saida_estadual.required' => 'Campo obrigatório.',
            'CFOP_saida_inter_estadual.required' => 'Campo obrigatório.',
            'file.max' => 'Arquivo muito grande maximo 300 Kb',
            'lote.max' => '10 caracteres maximos permitidos.',
            'vencimento.max' => '10 caracteres maximos permitidos.',

        ];
        $this->validate($request, $rules, $messages);
    }

    public function all(){
        $products = Produto::all();
        $arr = array();
        foreach($products as $p){
            $arr[$p->id. ' - ' .$p->nome . ($p->cor != '--' ? ' | COR: ' . $p->cor : '') . ($p->referencia != '' ? ' | REF: ' . $p->referencia : '')] = null;
                //array_push($arr, $temp);
        }
        echo json_encode($arr);
    }

    public function getUnidadesMedida(){
        $unidades = Produto::unidadesMedida();
        echo json_encode($unidades);
    }

    public function composto(){
        $products = Produto::
        where('composto', true)
        ->get();
        $arr = array();
        foreach($products as $p){
            $arr[$p->id. ' - ' .$p->nome . ($p->cor != '--' ? ' | Cor: ' . $p->cor : '') . ($p->referencia != '' ? ' | REF: ' . $p->referencia : '')] = null;
                //array_push($arr, $temp);
        }
        echo json_encode($arr);
    }

    public function naoComposto(){
        $products = Produto::
        where('composto', false)
        ->get();
        $arr = array();
        foreach($products as $p){
            $arr[$p->id. ' - ' .$p->nome . ($p->cor != '--' ? ' | Cor: ' . $p->cor : '') . ($p->referencia != '' ? ' | REF: ' . $p->referencia : '')] = null;
                //array_push($arr, $temp);
        }
        echo json_encode($arr);
    }

    public function getValue(Request $request){
        $id = $request->input('id');
        $product = Product::
        where('id', $id)
        ->first();
        echo json_encode($product->value_sale);
    }

    public function getProduto($id){
        $produto = Produto::
        where('id', $id)
        ->first();
        if($produto->delivery){
            foreach($produto->delivery->pizza as $tp){
                $tp->tamanho;
            }
        }
        if($produto->ecommerce){
            $produto->ecommerce;
        }
        echo json_encode($produto);
    }

    public function getProdutoCodigoReferencia($codigo){
        $produto = Produto::
        where('referencia_balanca', $codigo)
        ->first();

        if($produto != null){
            return response()->json($produto, 200);
        }else{
            return response()->json("Nada encontrado!", 401);
        }

    }

    public function getProdutoVenda($id, $listaId){
        $produto = Produto::
        where('id', $id)
        ->first();
        if($produto->delivery){
            foreach($produto->delivery->pizza as $tp){
                $tp->tamanho;
            }
        }

        if($listaId > 0){
            $lista = ProdutoListaPreco::
            where('lista_id', $listaId)
            ->where('produto_id', $produto->id)
            ->first();

            if($lista->valor > 0){
                $produto->valor_venda = (string) $lista->valor;
            }
        }

        $estoque = Estoque::where('produto_id', $id)->first();
        $produto->estoque_atual = $estoque != null ? $estoque->quantidade : 0; 
        echo json_encode($produto);
    }

    public function getProdutoCodBarras($cod){
        $produto = Produto::
        where('codBarras', $cod)
        ->where('empresa_id', $this->empresa_id)
        ->first();

        echo json_encode($produto);
    }

    public function salvarProdutoDaNota(Request $request){
        //echo json_encode($request->produto);
        $produto = $request->produto;
        $natureza = Produto::firstNatureza($this->empresa_id);

        $valorVenda = str_replace(".", "", $produto['valorVenda']);
        $valorVenda = str_replace(",", ".", $valorVenda);

        $valorCompra = $produto['valorCompra'];

        $cfop = $produto['cfop'];
        $digito = substr($cfop, 0, 1);

        $cfopEstadual = '';
        $cfopInterEstadual = '';
        if($digito == '5'){
            $cfopEstadual = $cfop;
            $cfopInterEstadual = '6'. substr($cfop, 1, 4);

        }else{
            $cfopInterEstadual = $cfop;
            $cfopEstadual = '6'. substr($cfop, 1, 4);
        } 

        $result = Produto::create([
            'nome' => $produto['nome'],
            'NCM' => $produto['ncm'],
            // 'CFOP' => $produto['cfop'],
            'valor_venda' => $valorVenda,
            'valor_compra' => $produto['valorCompra'],
            'valor_livre' => false,
            'cor' => $produto['cor'],
            'percentual_lucro' => $produto['percentual_lucro'] ?? 0,
            'referencia' => $produto['referencia'],
            'conversao_unitaria' => (int) $produto['conversao_unitaria'],
            'categoria_id' => $produto['categoria_id'],
            'unidade_compra' => $produto['unidadeCompra'],
            'unidade_venda' => $produto['unidadeVenda'],
            'codBarras' => $produto['codBarras'] ?? 'SEM GTIN',
            'composto' => false,
            'CST_CSOSN' => $produto['CST_CSOSN'],
            'CST_PIS' => $produto['CST_PIS'],
            'CST_COFINS' => $produto['CST_COFINS'],        
            'CST_IPI' => $produto['CST_IPI'],
            'perc_icms' => $produto['perc_icms'],
            'perc_pis' => $produto['perc_pis'],
            'perc_cofins' => $produto['perc_cofins'],
            'perc_ipi' => $produto['perc_ipi'],
            'CFOP_saida_estadual' => $cfopEstadual,
            'CFOP_saida_inter_estadual' => $cfopInterEstadual,
            'codigo_anp' => '', 
            'descricao_anp' => '',
            'cListServ' => '',
            'imagem' => '',
            'alerta_vencimento' => 0,
            'referencia' => $produto['referencia'],
            'empresa_id' => $this->empresa_id,
            'gerenciar_estoque' => getenv("PRODUTO_GERENCIAR_ESTOQUE"),
            'reajuste_automatico' => 0,
            'limite_maximo_desconto' => 0,
            'grade' => 0,
            'referencia_grade' => Str::random(20)
        ]);

        echo json_encode($result);  
    }

    public function salvarProdutoDaNotaComEstoque(Request $request){
        //echo json_encode($request->produto);
        $produto = $request->produto;
        $natureza = Produto::firstNatureza($this->empresa_id);
        $valorVenda = str_replace(",", ".", $produto['valorVenda']);

        $valorCompra = $produto['valorCompra'];

        $cfop = $produto['cfop'];
        $digito = substr($cfop, 0, 1);

        $cfopEstadual = '';
        $cfopInterEstadual = '';
        if($digito == '5'){
            $cfopEstadual = $cfop;
            $cfopInterEstadual = '6'. substr($cfop, 1, 4);

        }else{
            $cfopInterEstadual = $cfop;
            $cfopEstadual = '6'. substr($cfop, 1, 4);
        } 

        $result = Produto::create([
            'nome' => $produto['nome'],
            'NCM' => $produto['ncm'],
            'valor_venda' => $valorVenda,
            'valor_compra' => $valorCompra,
            'percentual_lucro' => $produto['percentual_lucro'] ?? 0,
            'valor_livre' => false,
            'cor' => $produto['cor'],
            'conversao_unitaria' => (float)$produto['conversao_unitaria'],
            'categoria_id' => $produto['categoria_id'],
            'unidade_compra' => $produto['unidadeCompra'],
            'unidade_venda' => $produto['unidadeVenda'],
            'codBarras' => $produto['codBarras'] ?? 'SEM GTIN',
            'composto' => false,
            'CST_CSOSN' => $produto['CST_CSOSN'],
            'CST_PIS' => $produto['CST_PIS'],
            'CST_COFINS' => $produto['CST_COFINS'],        
            'CST_IPI' => $produto['CST_IPI'],
            'perc_icms' => 0,
            'perc_pis' => 0,
            'perc_cofins' => 0,
            'perc_ipi' => 0,
            'CFOP_saida_estadual' => $cfopEstadual,
            'CFOP_saida_inter_estadual' => $cfopInterEstadual,
            'codigo_anp' => '', 
            'descricao_anp' => '',
            'cListServ' => '',
            'imagem' => '',
            'alerta_vencimento' => 0,
            'referencia' => $produto['referencia'],
            'empresa_id' => $this->empresa_id,
            'gerenciar_estoque' => getenv("PRODUTO_GERENCIAR_ESTOQUE"),
            'reajuste_automatico' => 0,
            'limite_maximo_desconto' => 0,
            'grade' => 0,
            'referencia_grade' => Str::random(20)
        ]);

        ItemDfe::create(
            [
                'numero_nfe' => $produto['numero_nfe'],
                'produto_id' => $result->id,
                'empresa_id' => $this->empresa_id
            ]
        );

        $stockMove = new StockMove();
        $stockMove->pluStock($result->id, ($produto['quantidade']*(float)$produto['conversao_unitaria']), $valorCompra);

        echo json_encode($result);  
    }

    public function setEstoque(Request $request){
        $stockMove = new StockMove();
        $stockMove->pluStock($request->produto, $request->quantidade, $request->valor);

        $produto = Produto::find($request->produto);
        $perc = $produto->percentual_lucro;


        $produto->valor_compra = $request->valor;
        if($produto->reajuste_automatico){
            $produto->valor_venda = $request->valor + 
            (($request->valor*$produto->percentual_lucro)/100);
        }

        $produto->save();
        ItemDfe::create(
            [
                'numero_nfe' => $request->numero_nfe,
                'produto_id' => $request->produto,
                'empresa_id' => $this->empresa_id
            ]
        );
        echo json_encode("ok");  
    }

    private function salvarProdutoNoDelivery($request, $produto){
        $this->_validateDelivery($request);

        $categoria = CategoriaProdutoDelivery::
        where('id', $request->categoria_delivery_id)
        ->first();

        $valor = 0;
        if(strpos($categoria->nome, 'izza') !== false){
            //pizza nao seta valor por aqui
        }else{
            $valor = str_replace(",", ".", $request->valor_venda);
        }

        $produtoDelivery = [
            'status' => 1 ,
            'produto_id' => $produto->id,
            'destaque' => $request->input('destaque') ? true : false,
            'descricao' => $request->descricao ?? '',
            'ingredientes' => $request->ingredientes ?? '',
            'limite_diario' => $request->limite_diario,
            'categoria_id' => $categoria->id,
            'valor' => $valor,
            'valor_anterior' => 0,
            'empresa_id' => $this->empresa_id
        ];

        $result = ProdutoDelivery::create($produtoDelivery);
        $produtoDelivery = ProdutoDelivery::find($result->id);
        if($result){
            $this->salveImagemProdutoDelivery($request, $produtoDelivery);
        }

    }

    private function salvarProdutoEcommerce($request, $produto, $nomeImagem){
        // $this->_validateEcommerce($request);

        $produtoEcommerce = [
            'produto_id' => $produto->id,
            'categoria_id' => $request->categoria_ecommerce_id,
            'empresa_id' => $this->empresa_id,
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

    private function updateProdutoNoDelivery($request, $produto){
        // $this->_validateDelivery($request);
        $produtoDelivery = $produto->delivery;
        if($produtoDelivery){
            $catPizza = false;
            $categoria = CategoriaProdutoDelivery::
            where('id', $request->categoria_delivery_id)
            ->first();

            $valor = 0;
            if($categoria && strpos($categoria->nome, 'izza') !== false){

            }else{
                $valor = str_replace(",", ".", $request->valor_venda);
            }

            $produtoDelivery->destaque = $request->input('destaque') ? true : false;
            $produtoDelivery->descricao = $request->input('descricao') ?? $produtoDelivery->descricao;
            $produtoDelivery->ingredientes = $request->input('ingredientes') ?? $produtoDelivery->ingredientes;
            $produtoDelivery->limite_diario = $request->input('limite_diario') ?? $produtoDelivery->limite_diario;
            $produtoDelivery->categoria_id = $request->input('categoria_delivery_id') ?? $produtoDelivery->categoria_delivery_id;
            $produtoDelivery->valor = $request->input('valor') ?? $valor;

            $result = $produtoDelivery->save();

            if($result){
                $this->salveImagemProdutoDelivery($request, $produtoDelivery);
            }
        }else{
            $this->salvarProdutoNoDelivery($request, $produto);
        }

    }

    private function _validateEcommerce(Request $request){

        if($request->ecommerce){
            $rules = [
                'valor_ecommerce' => 'required',
                'categoria_ecommerce_id' => 'required',
                'descricao' => 'required|min:20',
                'valor_ecommerce' => 'required',
                'largura' => 'required',
                'altura' => 'required',
                'comprimento' => 'required',
                'peso_liquido' => 'required',
                'peso_bruto' => 'required'
            ];
        }else{
            $rules = [];
        }

        $messages = [

            'valor_ecommerce.required' => 'O campo valor é obrigatório.',
            'categoria_ecommerce_id.required' => 'O campo categoria é obrigatório.',
            'descricao.required' => 'O campo descricao é obrigatório.',
            'descricao.min' => 'Minimo de 20 caracteres',

            'valor.required' => 'O campo valor é obrigatório.',

            'valor_ecommerce.required' => 'O campo valor é obrigatório.',
            'largura.required' => 'O campo largura é obrigatório.',
            'altura.required' => 'O campo altura é obrigatório.',
            'comprimento.required' => 'O campo comprimento é obrigatório.',
            'peso_liquido.required' => 'O campo peso liquido é obrigatório.',
            'peso_bruto.required' => 'O campo peso bruto é obrigatório.',

        ];

        $this->validate($request, $rules, $messages);
    }

    private function _validateDelivery(Request $request){
        $rules = [
            'ingredientes' => 'max:255',
            'descricao' => 'max:255',
            'limite_diario' => 'required'
        ];

        $messages = [
            'ingredientes.required' => 'O campo ingredientes é obrigatório.',
            'ingredientes.max' => '255 caracteres maximos permitidos.',
            'descricao.required' => 'O campo descricao é obrigatório.',
            'descricao.max' => '255 caracteres maximos permitidos.',
            'limite_diario.required' => 'O campo limite diário é obrigatório'
        ];

        $this->validate($request, $rules, $messages);
    }

    private function salveImagemProdutoDelivery($request, $produtoDelivery){
        if($request->hasFile('file')){
            $public = getenv('SERVIDOR_WEB') ? 'public/' : '';

            $file = $request->file('file');

            $extensao = $file->getClientOriginalExtension();
            $nomeImagem = md5($file->getClientOriginalName()).".".$extensao;

            // $upload = $file->move(public_path('imagens_produtos'), $nomeImagem);
            // if(file_exists($public.'imgs_produtos/'.$nomeImagem)){
            copy($public.'imgs_produtos/'.$nomeImagem, $public.'imagens_produtos/'.$nomeImagem);
            // }else{
            //     $file->move(public_path('imagens_produtos'), $nomeImagem);
            // }

            if(sizeof($produtoDelivery->galeria) == 0){
                //cadastrar
                ImagensProdutoDelivery::create(
                    [
                        'produto_id' => $produtoDelivery->id,
                        'path' => $nomeImagem
                    ]
                );
            }else{
                //ja tem
                $galeria = $produtoDelivery->galeria[0];
                $galeria->path = $nomeImagem;
                $galeria->save();
            }

        }else{

        }
    }

    private function salveImagemProdutoEcommerce($nomeImagem, $produtoEcommerce){

        if($nomeImagem != ""){
            copy(public_path('imgs_produtos/').$nomeImagem, public_path('ecommerce/produtos/').$nomeImagem);
            // $upload = $file->move(public_path('ecommerce/produtos'), $nomeImagem);

            ImagemProdutoEcommerce::create(
                [
                    'produto_id' => $produtoEcommerce->id, 
                    'img' => $nomeImagem
                ]
            );

        }else{

        }
    }

    public function movimentacao($id){
        $produto = Produto::find($id);

        $movimentacoes = $produto->movimentacoes();

        return view('produtos/movimentacoes')
        ->with('movimentacoes', $movimentacoes)
        ->with('produto', $produto)
        ->with('title', 'Movimentações');

    }

    public function movimentacaoImprimir($id){
        $produto = Produto::find($id);
        if(valida_objeto($produto)){

            $movimentacoes = $produto->movimentacoes();

            $p = view('produtos/relatorio_movimentacoes')
            ->with('produto', $produto)
            ->with('movimentacoes', $movimentacoes);

        // return $p;

            $domPdf = new Dompdf(["enable_remote" => true]);
            $domPdf->loadHtml($p);

            $pdf = ob_get_clean();

            $domPdf->setPaper("A4", "landscape");
            $domPdf->render();
            $domPdf->stream("relatorio movimentações.pdf");
        }else{
            return redirect('/403');
        }

    }

    public function importacao(){
        $zip_loaded = extension_loaded('zip') ? true : false;
        if ($zip_loaded === false) {
            session()->flash('mensagem_erro', "Por favor instale/habilite o PHP zip para importar");
            return redirect()->back();
        }
        $categoria = Categoria::where('empresa_id', $this->empresa_id)->first();
        if($categoria == null){
            session()->flash('mensagem_erro', 'Cadastre uma categoria!!');
            return redirect('/categorias');
        }

        $config = ConfigNota::where('empresa_id', $this->empresa_id)->first();
        if($config == null){
            session()->flash('mensagem_erro', 'Cadastre o emitente!!');
            return redirect('/configNF');
        }

        $trib = Tributacao::where('empresa_id', $this->empresa_id)->first();
        if($trib == null){
            session()->flash('mensagem_erro', 'Cadastre uma tributação padrão!!');
            return redirect('/tributos');
        }
        return view('produtos/importacao')
        ->with('title', 'Importação de produto');
    }

    public function downloadModelo(){
        try{
            $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
            return response()->download($public.'files/import_products_csv_template.xlsx');
        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }

    public function importacaoStore(Request $request){
        if ($request->hasFile('file')) {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            $rows = Excel::toArray(new ProdutoImport, $request->file);

            $retornoErro = $this->validaArquivo($rows);

            print_r($retornoErro);

            if($retornoErro == ""){
                //armazenar no bd

                $teste = [];
                $tributacao = Tributacao::where('empresa_id', $this->empresa_id)->first();
                $config = ConfigNota::where('empresa_id', $this->empresa_id)->first();
                $categoria = Categoria::where('empresa_id', $this->empresa_id)->first();

                $cont = 0;

                foreach($rows as $row){
                    foreach($row as $key => $r){
                        if($r[0] != 'NOME*'){
                            try{
                                $objeto = $this->preparaObjeto($r, $tributacao, $config, $categoria->id);

                                // print_r($objeto);
                                // die;

                                if($objeto['categoria'] != ''){
                                    $cat = Categoria::where('nome', $objeto['categoria'])
                                    ->where('empresa_id', $this->empresa_id)
                                    ->first();

                                    if($cat == null){

                                        $cat = Categoria::create(
                                            [
                                                'nome' => $objeto['categoria'],
                                                'empresa_id' => $this->empresa_id
                                            ]
                                        );
                                        $objeto['categoria_id'] = $cat->id;
                                    }else{
                                        $objeto['categoria_id'] = $cat->id;
                                    }
                                }else{
                                    $objeto['categoria_id'] = $categoria->id;
                                }

                                $prod = Produto::create($objeto);

                                if($objeto['estoque'] > 0){
                                    $stockMove = new StockMove();
                                    $result = $stockMove->pluStock($prod->id, 
                                        $objeto['estoque'], str_replace(",", ".", $prod->valor_venda));
                                }

                                $cont++;

                            }catch(\Exception $e){
                                echo $e->getMessage();
                                die;
                                session()->flash('mensagem_erro', $e->getMessage());
                                return redirect()->back();
                            }
                        }
                    }
                }

                session()->flash('mensagem_sucesso', "Produtos inseridos: $cont!!");
                return redirect('/produtos');

            }else{
                session()->flash('mensagem_erro', $retornoErro);
                return redirect()->back();
            }

        }else{
            session()->flash('mensagem_erro', 'Nenhum Arquivo!!');
            return redirect()->back();
        }

    }

    private function validaNumero($numero){
        if(strlen($numero) == 1){
            return "0".$numero;
        }
        return $numero;
    }

    private function preparaObjeto($r, $tributacao, $config, $categoria){

        $arr = [
            'nome' => $r[0],
            'categoria' => $r[2],
            'cor' => $r[1] ?? '',
            'valor_venda' => __replace($r[3]),
            'NCM' => $r[5] != "" ? $r[5] : $tributacao->ncm_padrao,
            'CEST' => $r[7] ?? '',
            'CST_CSOSN' => $r[8] != "" ? $this->validaNumero($r[8]) : $config->CST_CSOSN_padrao,
            'CST_PIS' => $r[9] != "" ? $this->validaNumero($r[9]) : $config->CST_PIS_padrao, 
            'CST_COFINS' => $r[10] != "" ? $this->validaNumero($r[10]) : $config->CST_COFINS_padrao,
            'CST_IPI' => $r[11] != "" ? $r[11] : $config->CST_IPI_padrao,
            'unidade_compra' => $r[12] != "" ? $r[12] : 'UN',
            'unidade_venda' => $r[13] != "" ? $r[13] : 'UN',
            'composto' => $r[15] != "" ? $r[15] : 0,
            'codBarras' => $r[6] != "" ? $r[6] : 'SEM GTIN', 
            'conversao_unitaria' => $r[14] != "" ? $r[14] : 1,
            'valor_livre' => $r[16] != "" ? $r[16] : 0,
            'perc_icms' => $r[17] != "" ? $r[17] : $tributacao->icms,
            'perc_pis' => $r[18] != "" ? $r[18] : $tributacao->pis,
            'perc_cofins' => $r[19] != "" ? $r[19] : $tributacao->cofins,
            'perc_ipi' => $r[20] != "" ? $r[20] : $tributacao->ipi,
            'CFOP_saida_estadual' => $r[22] != "" ? $r[22] : '5101',
            'CFOP_saida_inter_estadual' => $r[23] != "" ? $r[23] : '6101',
            'codigo_anp' => $r[24] ??'',
            'descricao_anp' => $r[25]?? '',
            'perc_iss' => $r[21] ?? 0,
            'cListServ' => '',
            'imagem' => '',
            'alerta_vencimento' => $r[26] != "" ? $r[26] : 0,
            'valor_compra' => __replace($r[4]),
            'gerenciar_estoque' => $r[27] != "" ? $r[27] : 0,
            'estoque_minimo' => $r[28] != "" ? $r[28] : 0,
            'referencia' => $r[29] ?? '',
            'empresa_id' => $this->empresa_id, 
            'largura' => $r[30] != "" ? $r[30] : 0,
            'comprimento' => $r[31] != "" ? $r[31] : 0,
            'altura' => $r[32] != "" ? $r[32] : 0,
            'peso_liquido' => $r[33] != "" ? $r[33] : 0,
            'peso_bruto' => $r[34] != "" ? $r[34] : 0,
            'limite_maximo_desconto' => $r[35] != "" ? $r[35] : 0,
            'pRedBC' => $r[36] ?? '',
            'cBenef' => $r[37] ?? '',
            'percentual_lucro' => 0,
            'CST_CSOSN_EXP' => '', 
            'referencia_grade' => Str::random(20),
            'grade' => 0,
            'str_grade' => 0,
            'perc_glp' => 0,
            'perc_gnn' => 0,
            'perc_gni' => 0,
            'valor_partida' => 0,
            'unidade_tributavel' => '',
            'quantidade_tributavel' => 0,
            'perc_icms_interestadual' => 0,
            'perc_icms_interno' => 0,
            'perc_fcp_interestadual' => 0,
            'inativo' => 0,
            'estoque' => $r[38] != "" ? $r[38] : 0,

        ];
        return $arr;

    }

    private function validaArquivo($rows){
        $cont = 0;
        $msgErro = "";
        foreach($rows as $row){
            foreach($row as $key => $r){

                $nome = $r[0];
                $valorVenda = $r[3];
                $valorCompra = $r[4];

                if(strlen($nome) == 0){
                    $msgErro .= "Coluna nome em branco na linha: $cont | "; 
                }

                if(strlen($valorVenda) == 0){
                    $msgErro .= "Coluna valor venda em branco na linha: $cont | "; 
                }

                if(strlen($valorCompra) == 0){
                    $msgErro .= "Coluna valor compra em branco na linha: $cont"; 
                }

                if($msgErro != ""){
                    return $msgErro;
                }
                $cont++;
            }
        }

        return $msgErro;
    }

    public function duplicar($id){
        $natureza = Produto::firstNatureza($this->empresa_id);
        $anps = Produto::lista_ANP();

        if($natureza == null){
            session()->flash('mensagem_erro', 'Cadastre uma natureza de operação!');
            return redirect('/naturezaOperacao');
        }

        $produto = new Produto();
        // $listaCSTCSOSN = Produto::listaCSTCSOSN();
        $listaCST_PIS_COFINS = Produto::listaCST_PIS_COFINS();
        $listaCST_IPI = Produto::listaCST_IPI();

        $categorias = Categoria::
        where('empresa_id', $this->empresa_id)
        ->get();

        $unidadesDeMedida = Produto::unidadesMedida();
        $config = ConfigNota::
        where('empresa_id', $this->empresa_id)
        ->first();

        $tributacao = Tributacao::
        where('empresa_id', $this->empresa_id)
        ->first();

        $resp = $produto
        ->where('id', $id)->first();  

        $categoriasDelivery = [];

        if($tributacao->regime == 1){
            $listaCSTCSOSN = Produto::listaCST();
        }else{
            $listaCSTCSOSN = Produto::listaCSOSN();
        }

        if($tributacao == null){
            session()->flash('mensagem_erro', 'Informe a tributação padrão!');
            return redirect('tributos');
        }

        if(valida_objeto($resp)){
            return view('produtos/duplicar')
            ->with('produto', $resp)
            ->with('config', $config)
            ->with('tributacao', $tributacao)
            ->with('natureza', $natureza)
            ->with('listaCSTCSOSN', $listaCSTCSOSN)
            ->with('listaCST_PIS_COFINS', $listaCST_PIS_COFINS)
            ->with('listaCST_IPI', $listaCST_IPI)
            ->with('categoriasDelivery', $categoriasDelivery)
            ->with('anps', $anps)
            ->with('unidadesDeMedida', $unidadesDeMedida)
            ->with('categorias', $categorias)
            ->with('produtoJs', true)
            ->with('title', 'Duplicar Produto');
        }else{
            return redirect('/403');
        }

    }

    public function grade($id){
        $produto = Produto::find($id);
        if(valida_objeto($produto)){
            $produtos = Produto::produtosDaGrade($produto->referencia_grade);

            $produtos = $this->setaEstoqueGrade($produtos);
            return view('produtos/grade')
            ->with('produtos', $produtos)
            ->with('title', 'Grade');
        }else{
            return redirect('/403');
        }
    }

    private function setaEstoqueGrade($produtos){
        foreach($produtos as $p){
            $estoque = Estoque::where('produto_id', $p->id)->first();
            $p->estoque_atual = $estoque == null ? 0 : $estoque->quantidade;
        }
        return $produtos;
    }

    public function quickSave(Request $request){
        //echo json_encode($request->produto);
        $produto = $request->data;
        $natureza = Produto::firstNatureza($this->empresa_id);

        $valorVenda = __replace($produto['valor_venda']);
        $valorCompra = __replace($produto['valor_compra']);

        try{
            $result = Produto::create([
                'nome' => $produto['nome'],
                'NCM' => $produto['NCM'],
                'valor_venda' => $valorVenda,
                'valor_compra' => $valorCompra,
                'valor_livre' => false,
                'cor' => '',
                'conversao_unitaria' => 1,
                'categoria_id' => $produto['categoria_id'],
                'unidade_compra' => $produto['unidade_compra'],
                'unidade_venda' => $produto['unidade_venda'],
                'codBarras' => $produto['codBarras'] ?? 'SEM GTIN',
                'composto' => false,
                'CST_CSOSN' => $produto['CST_CSOSN'],
                'CST_PIS' => $produto['CST_PIS'],
                'CST_COFINS' => $produto['CST_COFINS'],        
                'CST_IPI' => $produto['CST_IPI'],
                'CST_CSOSN_EXP' => $produto['CST_CSOSN_EXP'] ?? '',
                'perc_icms' => $produto['perc_icms'],
                'perc_pis' => $produto['perc_pis'],
                'perc_cofins' => $produto['perc_cofins'],
                'perc_ipi' => $produto['perc_ipi'],
                'perc_iss' => $produto['perc_iss'],
                'pRedBC' => $produto['pRedBC'],
                'cBenef' => $produto['cBenef'] ?? '',
                'CFOP_saida_estadual' => $produto['CFOP_saida_estadual'],
                'CFOP_saida_inter_estadual' => $produto['CFOP_saida_inter_estadual'],
                'codigo_anp' => '', 
                'descricao_anp' => '',
                'cListServ' => '',
                'imagem' => '',
                'alerta_vencimento' => $produto['alerta_vencimento'] ?? 0,
                'referencia' => $produto['referencia'] ?? '',
                'empresa_id' => $this->empresa_id,
                'gerenciar_estoque' => $produto['gerenciar_estoque'],
                'limite_maximo_desconto' => $produto['limite_maximo_desconto'] ?? 0,
                'perc_icms_interestadual' => $produto['perc_icms_interestadual'] ?? 0,
                'perc_icms_interno' => $produto['perc_icms_interno'] ?? 0,
                'perc_fcp_interestadual' => $produto['perc_fcp_interestadual'] ?? 0,
                'CEST' => $produto['CEST'] ?? '',
                'grade' => 0,
                'referencia_grade' => Str::random(20),
                'percentual_lucro' => $produto['percentual_lucro']
            ]);
            return response()->json($result, 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }

    }

    public function atualizarGradeCompleta(Request $request){
        $product = new Produto();

        $id = $request->input('id');
        $resp = $product
        ->where('id', $id)->first(); 

        $gradeCompleta = $product->
        where('referencia_grade', $resp->referencia_grade)
        ->get();

        $this->_validate($request);

        $anps = Produto::lista_ANP();
        $descAnp = '';
        foreach($anps as $key => $a){
            if($key == $request->anp){
                $descAnp = $a;
            }
        }

        try{
            foreach($gradeCompleta as $g){
                $resp = $g;

                $resp->nome = $request->input('nome');
                $resp->categoria_id = $request->input('categoria_id');
                $resp->sub_categoria_id = $request->input('sub_categoria_id');
                $resp->marca_id = $request->input('marca_id');
                $resp->cor = $request->input('cor');
                if($request->check_valor_venda){
                    $resp->valor_venda = str_replace(",", ".", $request->input('valor_venda'));
                }

                if($request->check_valor_compra){
                    $resp->valor_compra = str_replace(",", ".", $request->input('valor_compra'));
                }

                $resp->percentual_lucro = str_replace(",", ".", $request->input('percentual_lucro'));
                $resp->NCM = $request->input('NCM');
                $resp->CEST = $request->input('CEST') ?? '';

                $resp->CST_CSOSN = $request->input('CST_CSOSN');
                $resp->CST_CSOSN_EXP = $request->input('CST_CSOSN_EXP');

                $resp->CST_PIS = $request->input('CST_PIS');
                $resp->CST_COFINS = $request->input('CST_COFINS');
                $resp->CST_IPI = $request->input('CST_IPI');
        // $resp->CFOP = $request->input('CFOP');
                $resp->unidade_venda = $request->input('unidade_venda');
                $resp->unidade_compra = $request->input('unidade_compra');
                $resp->conversao_unitaria = $request->input('conversao_unitaria') ? $request->input('conversao_unitaria') : $resp->conversao_unitaria;
                $resp->codBarras = $request->input('codBarras') ?? 'SEM GTIN';

                $resp->perc_icms = $request->perc_icms ? __replace($request->perc_icms) : 0;
                $resp->perc_pis = $request->perc_pis ? __replace($request->perc_pis) : 0;
                $resp->perc_cofins = $request->perc_cofins ? __replace($request->perc_cofins) : 0;
                $resp->perc_ipi = $request->perc_ipi ? __replace($request->perc_ipi) : 0;
                $resp->perc_iss = $request->perc_iss ? __replace($request->perc_iss) : 0;
                $resp->cListServ = $request->input('cListServ');

                $resp->CFOP_saida_estadual = $request->input('CFOP_saida_estadual');
                $resp->CFOP_saida_inter_estadual = $request->input('CFOP_saida_inter_estadual');
                $resp->codigo_anp = $request->input('anp') ?? '';
                $resp->perc_glp = $request->perc_glp ? __replace($request->perc_glp) : 0;
                $resp->perc_gnn = $request->perc_gnn ? __replace($request->perc_gnn) : 0;
                $resp->perc_gni = $request->perc_gni ? __replace($request->perc_gni) : 0;
                $resp->valor_partida = $request->valor_partida ? 
                __replace($request->valor_partida) : 0;

                $resp->quantidade_tributavel = $request->quantidade_tributavel ? 
                __replace($request->quantidade_tributavel) : 0;

                $resp->unidade_tributavel = $request->unidade_tributavel ?? '';

                $resp->descricao_anp = $request->anp ?? '';
                $resp->alerta_vencimento = $request->alerta_vencimento;

                $resp->referencia = $request->referencia;
                $resp->referencia_balanca = $request->referencia_balanca;

                $resp->composto = $request->composto ? true : false;
                $resp->valor_livre = $request->valor_livre ? true : false;
                $resp->gerenciar_estoque = $request->gerenciar_estoque ? true : false;
                $resp->reajuste_automatico = $request->reajuste_automatico ? true : false;
                $resp->inativo = $request->inativo ? true : false;
                $resp->estoque_minimo = $request->estoque_minimo;

                $resp->pRedBC = __replace($request->pRedBC);
                $resp->cBenef = $request->cBenef;

                $resp->largura = $request->largura;
                $resp->comprimento = $request->comprimento;
                $resp->altura = $request->altura;
                $resp->peso_liquido = __replace($request->peso_liquido);
                $resp->peso_bruto = __replace($request->peso_bruto);
                $resp->limite_maximo_desconto = $request->limite_maximo_desconto;

                $resp->perc_icms_interestadual = $request->perc_icms_interestadual ? __replace($request->perc_icms_interestadual) : 0;
                $resp->perc_icms_interno = $request->perc_icms_interno ? __replace($request->perc_icms_interno) : 0;
                $resp->perc_fcp_interestadual = $request->perc_fcp_interestadual ? __replace($request->perc_fcp_interestadual) : 0;


                $resp->renavam = $request->renavam ?? '';
                $resp->placa = $request->placa ?? '';
                $resp->chassi = $request->chassi ?? '';
                $resp->combustivel = $request->combustivel ?? '';
                $resp->ano_modelo = $request->ano_modelo ?? '';
                $resp->cor_veiculo = $request->cor_veiculo ?? '';

                $resp->lote = $request->lote ?? '';
                $resp->vencimento = $request->vencimento ?? '';

                // $this->salveImagemProduto($request, $resp);
                if($request->hasFile('file')){
                    $nomeImagem = $this->salveImagemProdutoTemp($request); 
                    $resp->imagem = $nomeImagem;
                }

                $result = $resp->save();

                $estoque = $request->estoque;
                $stockMove = new StockMove();

                if($estoque && $request->check_estoque){
                    $estoque = __replace($request->estoque);
                    if(!$produto->estoque){
                        $data = [
                            'produto_id' => $produto->id,
                            'usuario_id' => get_id_user(),
                            'quantidade' => $estoque,
                            'tipo' => 'incremento',
                            'observacao' => '',
                            'empresa_id' => $this->empresa_id
                        ];

                        AlteracaoEstoque::create($data);
                        $result = $stockMove->pluStock($produto->id, 
                            $estoque, str_replace(",", ".", $produto->valor_venda));
                        $mensagem_sucesso = "Produto editado com sucesso, e estoque atribuido!";
                    }else{

                        if($produto->estoque->quantidade > $estoque || $produto->estoque->quantidade < $estoque){
                    //alterar

                            $tipo = '';
                            $valorAlterar = 0;
                            $estoqueAtual = $produto->estoque->quantidade;
                            if($estoqueAtual > $estoque){
                                $tipo = 'reducao';
                                $valorAlterar = $estoqueAtual - $estoque;
                            }else{
                                $tipo = 'incremento';
                                $valorAlterar = $estoque - $estoqueAtual;

                            }
                            $data = [
                                'produto_id' => $produto->id,
                                'usuario_id' => get_id_user(),
                                'quantidade' => $valorAlterar,
                                'tipo' => $tipo,
                                'observacao' => '',
                                'empresa_id' => $this->empresa_id
                            ];

                            AlteracaoEstoque::create($data);
                            if($produto->estoque->quantidade > $estoque){
                                $stockMove->pluStock($produto->id, 
                                    $valorAlterar, str_replace(",", ".", $produto->valor_venda));
                            }else{
                                $stockMove->pluStock($produto->id, 
                                    $valorAlterar, str_replace(",", ".", $produto->valor_venda));
                            }

                        }
                    }
                }
            }
            session()->flash('mensagem_sucesso', 'Grade alterada!');
        }catch(\Exception $e){
            session()->flash('mensagem_erro', 'Erro ao editar ' . $e->getMessage());

        }
        return redirect('/produtos');
    }

    public function autocomplete(Request $request){
        try{
            $produtos = Produto::
            where('empresa_id', $this->empresa_id)
            ->where('nome', 'LIKE', "%$request->pesquisa%")
            ->get();

            $refs = Produto::
            where('empresa_id', $this->empresa_id)
            ->where('referencia', 'LIKE', "%$request->pesquisa%")
            ->get();

            // array_push($produtos, $refs);
            $temp = [];
            foreach($produtos as $p){
                $p->listaPreco;
                $p->estoqueAtual = $p->estoqueAtual();
                array_push($temp, $p);
            }

            foreach($refs as $p){
                $p->listaPreco;
                $p->estoqueAtual = $p->estoqueAtual();
                array_push($temp, $p);
            }


            return response()->json($temp, 200);

        }catch(\Exception $e){
            return response($e->getMessage(), 401);
        }
    }

    public function autocompleteProduto(Request $request){

        if($request->lista_id == 0){
            $produto = Produto::
            where('empresa_id', $this->empresa_id)
            ->where('id', $request->id)
            ->first();

            $produto->estoqueAtual = $produto->estoqueAtual();
            return response($produto, 200);
        }else{
            $produtoListaPreco = ProdutoListaPreco::
            where('produto_id', $request->id)
            ->where('lista_id', $request->lista_id)
            ->first();

            $produto = $produtoListaPreco->produto;
            $produto->estoqueAtual = $produto->estoqueAtual();
            $produto->valor_venda = $produtoListaPreco->valor;
            return response($produto, 200);

        }

    }

    // public function zebra($id){

    //     $nome = "Cerv Original 350ml";
    //     $codigo = "7891000777794";
    //     $valor = "4,50";
    //     $unidade = "Un";

    //     $generatorPNG = new \Picqer\Barcode\BarcodeGeneratorPNG();

    //     $bar_code = $generatorPNG->getBarcode($codigo, $generatorPNG::TYPE_EAN_13);
    //     file_put_contents("etiqueta.png", $bar_code);
    //     $pdf = new TcpdfFpdi('P', 'mm', 'A4');
    //     $pdf->AddPage('L', [30, 50]);
    //     $pdf->SetMargins(0,0,0, false);


    //     $pdf->SetFont('helvetica', '', 10);

    //     // $pdf->Image("etiqueta.png", 10, 1, 30);
    //     $pdf->Text(1, 5, $nome);
    //     $pdf->Text(1, 10, $codigo);


    //     $pdf->Output();
    // }

    public function etiqueta($id){
        try{

            $produto = Produto::find($id);
            if(valida_objeto($produto)){

                return view('produtos/etiqueta')
                ->with('title', 'Gerar etiqueta')
                ->with('produto', $produto);
            }else{
                return redirect('/403');
            }
        }catch(\Exception $e){
            session()->flash('mensagem_erro', 'Erro: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function etiquetaStore(Request $request){

        $this->_validateEtiqueta($request);
        try{

            $files = glob(public_path("barcode/*")); 

            foreach($files as $file){ 
                if(is_file($file)) {
                    unlink($file); 
                }
            }

            $produto = Produto::find($request->produto_id);
            $nome = $produto->nome;
            $codigo = $produto->codBarras;
            $valor = $produto->valor_venda;
            $unidade = $produto->unidade_venda;

            if($codigo == "" || $codigo == "SEM GTIN" || $codigo == "sem gtin"){
                session()->flash('mensagem_erro', 'Produto sem código de barras definido');
                return redirect()->back();
            }

            $data = [
                'nome_empresa' => $request->nome_empresa ? true : false,
                'nome_produto' => $request->nome_produto ? true : false,
                'valor_produto' => $request->valor_produto ? true : false,
                'cod_produto' => $request->cod_produto ? true : false,
                'nome' => $nome,
                'codigo' => $produto->id,
                'valor' => $valor,
                'unidade' => $unidade,
                'empresa' => $produto->empresa->nome
            ];

            $generatorPNG = new \Picqer\Barcode\BarcodeGeneratorPNG();

            $bar_code = $generatorPNG->getBarcode($codigo, $generatorPNG::TYPE_EAN_13);
            $rand = rand(1000, 9999);
            file_put_contents(public_path("barcode")."/$rand.png", $bar_code);

            $qtdLinhas = $request->qtd_linhas;
            $qtdTotal = $request->qtd_etiquetas;


            return view('produtos/print')
            ->with('altura', $request->altura)
            ->with('largura', $request->largura)
            ->with('rand', $rand)
            ->with('quantidade', $qtdTotal)
            ->with('distancia_topo', $request->dist_topo)
            ->with('distancia_lateral', $request->dist_lateral)
            ->with('quantidade_por_linhas', $qtdLinhas)
            ->with('tamanho_fonte', $request->tamanho_fonte)
            ->with('tamanho_codigo', $request->tamanho_codigo)
            ->with('data', $data);
        }catch(\Exception $e){
            session()->flash('mensagem_erro', 'Erro: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    private function _validateEtiqueta(Request $request){
        $rules = [
            'largura' => 'required',
            'altura' => 'required',
            'qtd_linhas' => 'required',
            'dist_lateral' => 'required',
            'dist_topo' => 'required',
            'qtd_etiquetas' => 'required',
            'tamanho_fonte' => 'required',
            'tamanho_codigo' => 'required',
        ];

        $messages = [
            'largura.required' => 'Campo obrigatório.',
            'altura.required' => 'Campo obrigatório.',
            'qtd_linhas.required' => 'Campo obrigatório.',
            'dist_lateral.required' => 'Campo obrigatório.',
            'dist_topo.required' => 'Campo obrigatório.',
            'qtd_etiquetas.required' => 'Campo obrigatório.',
            'tamanho_fonte.required' => 'Campo obrigatório.',
            'tamanho_codigo.required' => 'Campo obrigatório.',

        ];
        $this->validate($request, $rules, $messages);
    }

}
