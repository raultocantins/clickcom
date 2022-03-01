<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendaCaixa;
use App\Models\Venda;
use App\Helpers\StockMove;
use App\Models\ConfigNota;
use App\Models\NaturezaOperacao;
use App\Models\Categoria;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Tributacao;
use App\Models\Usuario;
use App\Models\Certificado;
use App\Models\ListaPreco;
use App\Models\AberturaCaixa;
use App\Models\ProdutoPizza;
use App\Models\CreditoVenda;
use App\Models\ConfigCaixa;
use App\Models\ItemVendaCaixa;

class FrontBoxController extends Controller
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

        $countProdutos = Produto::
        where('empresa_id', $this->empresa_id)
        ->where('inativo', false)
        ->count();

        if($countProdutos > getenv("ASSINCRONO_PRODUTOS")){
        // if($countProdutos > 10){
            $view = $this->pdvAssincrono();
            return $view;
        }else{

            $config = ConfigNota::
            where('empresa_id', $this->empresa_id)
            ->first();

            $naturezas = NaturezaOperacao::
            where('empresa_id', $this->empresa_id)
            ->get();

            $categorias = Categoria::
            where('empresa_id', $this->empresa_id)
            ->get();

            $produtos = Produto::
            where('empresa_id', $this->empresa_id)
            ->where('inativo', false)
            ->get();

            $produtosGroup = Produto::
            where('empresa_id', $this->empresa_id)
            ->where('inativo', false)
            ->where('valor_venda', '>', 0)
            ->groupBy('referencia_grade')
            ->get();

            $tributacao = Tributacao::
            where('empresa_id', $this->empresa_id)
            ->get();

            $tiposPagamento = VendaCaixa::tiposPagamento();
            $config = ConfigNota::
            where('empresa_id', $this->empresa_id)
            ->first();

            $certificado = Certificado::
            where('empresa_id', $this->empresa_id)
            ->first();

            $usuario = Usuario::find(get_id_user());

            if(count($naturezas) == 0 || count($produtos) == 0 || $config == null || count($categorias) == 0 || $tributacao == null){

                return view("frontBox/alerta")
                ->with('produtos', count($produtos))
                ->with('categorias', count($categorias))
                ->with('naturezas', $naturezas)
                ->with('config', $config)
                ->with('tributacao', $tributacao)
                ->with('title', "Validação para Emitir");
            }else{

                if($config->nat_op_padrao == 0){

                    session()->flash('mensagem_erro', 'Informe a natureza de operação para o PDV!');
                    return redirect('/configNF');
                }else{

                    $tiposPagamentoMulti = VendaCaixa::tiposPagamentoMulti();

                    $produtos = Produto::
                    where('empresa_id', $this->empresa_id)
                    ->where('inativo', false)
                    ->where('valor_venda', '>', 0)
                    ->orderBy('nome')
                    ->get();

                    foreach($produtos as $p){
                        $p->listaPreco;
                        $estoque_atual = 0;
                        if($p->estoque){
                            if($p->unidade_venda == 'UN' || $p->unidade_venda == 'UNID'){
                                $estoque_atual = number_format($p->estoque->quantidade);
                            }else{
                                $estoque_atual = $p->estoque->quantidade;
                            }
                        }
                        $p->estoque_atual = $estoque_atual;
                        if($p->grade){
                            $p->nome .= " $p->str_grade";
                        }
                    }

                    foreach($produtosGroup as $p){
                        $p->listaPreco;
                        $estoque_atual = 0;
                        if($p->estoque){
                            if($p->unidade_venda == 'UN' || $p->unidade_venda == 'UNID'){
                                $estoque_atual = number_format($p->estoque->quantidade);
                            }else{
                                $estoque_atual = $p->estoque->quantidade;
                            }
                        }
                        $p->estoque_atual = $estoque_atual;

                    }

                    $categorias = Categoria::
                    where('empresa_id', $this->empresa_id)
                    ->orderBy('nome')->get();

                    $clientes = Cliente::orderBy('razao_social')
                    ->where('empresa_id', $this->empresa_id)
                    ->get();

                    foreach($clientes as $c){
                        $c->totalEmAberto = 0;
                        $soma = $this->getTotalContaCredito($c);
                        if($soma != null){
                            $c->totalEmAberto = $soma->total;
                        }
                    }

                    $atalhos = ConfigCaixa::
                    where('usuario_id', get_id_user())
                    ->first();

                    $view = 'main';
                    if($atalhos != null && $atalhos->modelo_pdv == 1){
                        $view = 'main2';
                    }
                    $listas = ListaPreco::where('empresa_id', $this->empresa_id)->get();


                    return view('frontBox/'.$view)
                    ->with('frenteCaixa', true)
                    ->with('tiposPagamento', $tiposPagamento)
                    ->with('config', $config)
                    ->with('certificado', $certificado)
                    ->with('listaPreco', $listas)
                    ->with('atalhos', $atalhos)
                    ->with('disableFooter', true)
                    ->with('usuario', $usuario)
                    ->with('produtos', $produtos)
                    ->with('produtosGroup', $produtosGroup)
                    ->with('clientes', $clientes)
                    ->with('categorias', $categorias)
                    ->with('tiposPagamentoMulti', $tiposPagamentoMulti)
                    ->with('title', 'Frente de Caixa');
                }
            }
        }
    }

    private function produtosMaisVendidos(){
        $itens = ItemVendaCaixa::
        selectRaw('item_venda_caixas.*, count(quantidade) as qtd')
        ->join('venda_caixas', 'venda_caixas.id', '=', 'item_venda_caixas.venda_caixa_id')
        ->where('venda_caixas.empresa_id', $this->empresa_id)
        ->groupBy('item_venda_caixas.produto_id')
        ->orderBy('qtd')
        ->limit(20)
        ->get();

        $produtos = [];
        foreach($itens as $i){
            $p = Produto::find($i->produto_id);
            array_push($produtos, $p);
        }
        return $produtos;
    }

    protected function pdvAssincrono(){
        $config = ConfigNota::
        where('empresa_id', $this->empresa_id)
        ->first();

        $naturezas = NaturezaOperacao::
        where('empresa_id', $this->empresa_id)
        ->get();

        $categorias = Categoria::
        where('empresa_id', $this->empresa_id)
        ->get();
        $tributacao = Tributacao::
        where('empresa_id', $this->empresa_id)
        ->get();

        $tiposPagamento = VendaCaixa::tiposPagamento();
        $config = ConfigNota::
        where('empresa_id', $this->empresa_id)
        ->first();

        $certificado = Certificado::
        where('empresa_id', $this->empresa_id)
        ->first();

        $usuario = Usuario::find(get_id_user());
        if(count($naturezas) == 0 || $config == null || count($categorias) == 0 || $tributacao == null){

            $p = view("frontBox/alerta")
            ->with('produtos', count($produtos))
            ->with('categorias', count($categorias))
            ->with('naturezas', $naturezas)
            ->with('config', $config)
            ->with('tributacao', $tributacao)
            ->with('title', "Validação para Emitir");

            return $p;
        }else{
            $tiposPagamentoMulti = VendaCaixa::tiposPagamentoMulti();
            $categorias = Categoria::
            where('empresa_id', $this->empresa_id)
            ->orderBy('nome')->get();

            $clientes = Cliente::orderBy('razao_social')
            ->where('empresa_id', $this->empresa_id)
            ->get();

            foreach($clientes as $c){
                $c->totalEmAberto = 0;
                $soma = $this->getTotalContaCredito($c);
                if($soma != null){
                    $c->totalEmAberto = $soma->total;
                }
            }

            $atalhos = ConfigCaixa::
            where('usuario_id', get_id_user())
            ->first();

            $listas = ListaPreco::where('empresa_id', $this->empresa_id)->get();

            $produtosMaisVendidos = $this->produtosMaisVendidos();

            $p = view('frontBox/pdv_assincrono')
            ->with('tiposPagamento', $tiposPagamento)
            ->with('config', $config)
            ->with('certificado', $certificado)
            ->with('listaPreco', $listas)
            ->with('produtosMaisVendidos', $produtosMaisVendidos)
            ->with('atalhos', $atalhos)
            ->with('disableFooter', true)
            ->with('usuario', $usuario)
            ->with('clientes', $clientes)
            ->with('categorias', $categorias)
            ->with('tiposPagamentoMulti', $tiposPagamentoMulti)
            ->with('title', 'Frente de Caixa');
            return $p;
        }
    }

    private function getTotalContaCredito($cliente){
        return CreditoVenda::
        selectRaw('sum(vendas.valor_total) as total')
        ->join('vendas', 'vendas.id', '=', 'credito_vendas.venda_id')
        ->where('credito_vendas.cliente_id', $cliente->id)
        ->where('status', 0)
        ->first();
    }

    private function cancelarNFCe($venda){
        $config = ConfigNota::
        where('empresa_id', $this->empresa_id)
        ->first();

        $cnpj = str_replace(".", "", $config->cnpj);
        $cnpj = str_replace("/", "", $cnpj);
        $cnpj = str_replace("-", "", $cnpj);
        $cnpj = str_replace(" ", "", $cnpj);
        $nfe_service = new NFeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => 2,
            "razaosocial" => $config->razao_social,
            "siglaUF" => $config->UF,
            "cnpj" => $cnpj,
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
            "tokenIBPT" => "AAAAAAA",
            "CSC" => "XTZOH6COASX5DYLKBUZXG5TABFG7ZFTQVSA2",
            "CSCid" => "000001"
        ], 65);

        $nfce = $nfe_service->cancelarNFCe($venda->id, "Troca de produtos requisitada pelo cliente");
        return is_array($nfce);
    }

    public function deleteVenda($id){
        $venda = VendaCaixa
        ::where('id', $id)
        ->first();

        if(valida_objeto($venda)){
            $stockMove = new StockMove();

            foreach($venda->itens as $i){
                if($i->produto->receita){
                    $receita = $i->produto->receita;
                    foreach($receita->itens as $rec){

                        if($i->itemPedido != NULL && $i->itemPedido->tamanho != NULL){
                            $totalSabores = count($i->itemPedido->sabores);
                            $produtoPizza = ProdutoPizza::
                            where('produto_id', $i->produto->delivery->id)
                            ->where('tamanho_id', $i->itemPedido->tamanho->id)
                            ->first();

                            $stockMove->pluStock(
                                $rec->produto_id, $i->quantidade 
                      * 
                                ((($rec->quantidade/$totalSabores)/$receita->pedacos)*$produtoPizza->tamanho->pedacos)/$receita->rendimento
                            );

                        }else{
                            $stockMove->pluStock($rec->produto_id, 
                                $i->quantidade);
                        }
                    }
                }else{
                    $stockMove->pluStock($i->produto_id, 
                        $i->quantidade); // -50 na altera valor compra
                }
            }

            if($venda->delete()){
                session()->flash("mensagem_sucesso", "Venda removida com sucesso!");
            }else{
                session()->flash('mensagem_erro', 'Erro ao remover venda!');
            }
            return redirect('/frenteCaixa/devolucao');
        }else{
            return redirect('/403');
        }

    }

    public function list(){
        // $vendas = VendaCaixa::
        // orderBy('id', 'desc')
        // ->get();

        $vendas = VendaCaixa::filtroData(
            $this->parseDate(date("Y-m-d")),
            $this->parseDate(date("Y-m-d"), true)
        );

        $somaTiposPagamento = $this->somaTiposPagamento($vendas);

        return view('frontBox/list')
        ->with('vendas', $vendas)
        ->with('frenteCaixa', true)
        ->with('somaTiposPagamento', $somaTiposPagamento)
        ->with('info', "Lista de vendas de Hoje: " . date("d/m/Y") )
        ->with('title', 'Lista de Vendas na Frente de Caixa');
    }

    private function somaTiposPagamento($vendas){
        $tipos = $this->preparaTipos();

        foreach($vendas as $v){
            if(isset($tipos[$v->tipo_pagamento])){
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
        return $tipos;

    }

    private function preparaTipos(){
        $temp = [];
        foreach(VendaCaixa::tiposPagamento() as $key => $tp){
            $temp[$key] = 0;
        }
        return $temp;
    }

    public function devolucao(){
        $vendas = VendaCaixa::
        orderBy('id', 'desc')
        ->where('empresa_id', $this->empresa_id)
        ->limit(20)
        ->get();

        $config = ConfigNota::
        where('empresa_id', $this->empresa_id)
        ->first();

        return view('frontBox/devolucao')
        ->with('config', $config)
        ->with('vendas', $vendas)
        ->with('frenteCaixa', true)
        ->with('nome', '')
        ->with('nfce', '')
        ->with('valor', '')
        ->with('data', '')
        ->with('info', "Lista das ultimas 20 vendas")
        ->with('title', 'Devolução NFCe');
    }

    public function filtro(Request $request){
        $dataInicial = $request->data_inicial;
        $dataFinal = $request->data_final;

        $vendas = VendaCaixa::filtroData(
            $this->parseDate($dataInicial),
            $this->parseDate($dataFinal, true)
        );

        $somaTiposPagamento = $this->somaTiposPagamento($vendas);

        return view('frontBox/list')
        ->with('vendas', $vendas)
        ->with('dataInicial', $dataInicial)
        ->with('somaTiposPagamento', $somaTiposPagamento)
        ->with('info', "Lista de vendas período: $dataInicial até $dataFinal")
        ->with('dataFinal', $dataFinal)
        ->with('frenteCaixa', true)
        ->with('info', "Lista das ultimas 20 vendas")
        ->with('title', 'Filtro de Vendas na Frente de Caixa');
    }


    private function parseDate($date, $plusDay = false){
        if($plusDay == false)
            return date('Y-m-d', strtotime(str_replace("/", "-", $date)));
        else
            return date('Y-m-d', strtotime("+1 day",strtotime(str_replace("/", "-", $date))));
    }



    public function filtroCliente(Request $request){

        $vendas = VendaCaixa::filtroCliente($request->nome);
        return view('frontBox/devolucao')
        ->with('vendas', $vendas)
        ->with('frenteCaixa', true)
        ->with('valor', '')
        ->with('nome', $request->nome)
        ->with('nfce', '')
        ->with('data', '')
        ->with('info', "Filtro cliente: $request->nome")

        ->with('title', 'Filtro por cliente');
    }


    public function filtroNFCe(Request $request){

        $vendas = VendaCaixa::filtroNFCe($request->nfce);
        return view('frontBox/devolucao')
        ->with('vendas', $vendas)
        ->with('frenteCaixa', true)
        ->with('valor', '')
        ->with('nfce', $request->nfce)
        ->with('nome', '')
        ->with('data', '')
        ->with('info', "Filtro NFCE: $request->nfce")
        ->with('title', 'Filtro por NFCe');
    }

    public function filtroData(Request $request){

        $vendas = VendaCaixa::filtroData2($request->data);

        return view('frontBox/devolucao')
        ->with('vendas', $vendas)
        ->with('frenteCaixa', true)
        ->with('valor', '')
        ->with('data', $request->data)
        ->with('nome', '')
        ->with('nfce', '')
        ->with('info', "Filtro Data: $request->data")
        ->with('title', 'Filtro por Data');
    }

    public function filtroValor(Request $request){

        $valor = __replace($request->valor);

        $vendas = VendaCaixa::filtroValor($valor);
        return view('frontBox/devolucao')
        ->with('vendas', $vendas)
        ->with('frenteCaixa', true)
        ->with('nfce', '')
        ->with('valor', $valor)
        ->with('nome', '')
        ->with('data', '')
        ->with('info', "Filtro valor: $request->valor")

        ->with('title', 'Filtro por Valor');
    }

    public function fechar(){
        $aberturaNfe = AberturaCaixa::where('ultima_venda_nfe', 0)
        ->where('empresa_id', $this->empresa_id)
        ->orderBy('id', 'desc')->first();

        $aberturaNfce = AberturaCaixa::where('ultima_venda_nfce', 0)
        ->where('empresa_id', $this->empresa_id)
        ->orderBy('id', 'desc')->first();

        $ultimaFechadaNfe = AberturaCaixa::where('ultima_venda_nfe', '>', 0)
        ->where('empresa_id', $this->empresa_id)
        ->orderBy('id', 'desc')->first();

        $ultimaFechadaNfce = AberturaCaixa::where('ultima_venda_nfce', '>', 0)
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
            ::whereBetween('id', [($ultimaFechadaNfce != null ? $ultimaFechadaNfce->ultima_venda_nfce+1 : 0), 
                $ultimaVendaCaixa])
            ->where('empresa_id', $this->empresa_id)
            ->get();

            $vendas = Venda
            ::whereBetween('id', [($ultimaFechadaNfe != null ? $ultimaFechadaNfe->ultima_venda_nfe+1 : 0), 
                $ultimaVenda])
            ->where('empresa_id', $this->empresa_id)
            ->get();

            $vendas = $this->agrupaVendas($vendas, $vendasPdv);
            $somaTiposPagamento = $this->somaTiposPagamento($vendas);

        }
        if($aberturaNfe == null && $aberturaNfce == null){
            return redirect('/frenteCaixa')->with('erro', 'O caixa esta fechado!!');
        }else{
            return view('frontBox/fechar_caixa')
            ->with('vendas', $vendas)
            ->with('abertura', $aberturaNfe != null ? $aberturaNfe : $aberturaNfce)
            ->with('somaTiposPagamento', $somaTiposPagamento)
            ->with('title', 'Fechar caixa');
        }
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

    public function fecharPost(Request $request){
        $id = $request->abertura_id;
        $abertura = AberturaCaixa::find($id);

        $ultimaVendaCaixa = VendaCaixa::
        where('empresa_id', $this->empresa_id)
        ->orderBy('id', 'desc')->first();

        $ultimaVenda = Venda::
        where('empresa_id', $this->empresa_id)
        ->orderBy('id', 'desc')->first();

        $abertura->ultima_venda_nfce = $ultimaVendaCaixa != null ? 
        $ultimaVendaCaixa->id : 0;
        $abertura->ultima_venda_nfe = $ultimaVenda != null ? $ultimaVenda->id : 0;
        $abertura->status = true;
        $abertura->save();
        session()->flash("mensagem_sucesso", "Caixa fechado com sucesso!");

        if(isset($request->redirect)){
            return redirect($request->redirect);
        }
        return redirect('frenteCaixa/list');
    }

    public function fechamentos(){
        $aberturas = AberturaCaixa::where('ultima_venda', '>', 0)
        ->where('empresa_id', $this->empresa_id)->get();
        $arr = [];

        for($i = 0; $i < sizeof($aberturas); $i++){
            $atual = $aberturas[$i]->ultima_venda;
            if($i == 0){
                $anterior = 0;
            }else{
                $anterior = $aberturas[$i-1]->ultima_venda;
            }
            $vendas = VendaCaixa
            ::whereBetween('id', [$anterior+1, 
                $atual])
            ->get();

            $total = 0;
            foreach($vendas as $v){
                $total += $v->valor_total;
            }

            $temp = [
                'inicio' => \Carbon\Carbon::parse($aberturas[$i]->created_at)->format('d/m/Y H:i:s'),
                'fim' => \Carbon\Carbon::parse($aberturas[$i]->updated_at)->format('d/m/Y H:i:s'),
                'total' => $total,
                'id' => $aberturas[$i]->id
            ];

            array_push($arr, $temp);
        }

        usort($arr, function ($a, $b) {
            return ($a['id'] < $b['id']) ? 1 : -1;
        });

        return view('frontBox/fechamentos')
        ->with('fechamentos', $arr)
        ->with('title', 'Lista de Caixas');
    }

    public function listaFechamento($id){
        $aberturas = AberturaCaixa::
        where('empresa_id', $this->empresa_id)
        ->get();
        $abertura = null;
        $inicio = 0;
        $fim = 0;

        for($i = 0; $i < sizeof($aberturas); $i++){
            if($aberturas[$i]->id == $id){
                $abertura = $aberturas[$i];
                if($i > 0){
                    $inicio = $aberturas[$i-1]->ultima_venda +1;
                }

                $fim = $aberturas[$i]->ultima_venda;
            }
        }

        $vendas = [];
        $somaTiposPagamento = [];


        $vendas = VendaCaixa
        ::whereBetween('id', [$inicio, 
            $fim])
        ->get();

        $somaTiposPagamento = $this->somaTiposPagamento($vendas);

        return view('frontBox/lista_fecha_caixa')
        ->with('vendas', $vendas)
        ->with('abertura', $abertura)
        ->with('somaTiposPagamento', $somaTiposPagamento)
        ->with('title', 'Detalhe fecha caixa');
    }

    public function config(){

        $config = ConfigCaixa::
        where('usuario_id', get_id_user())
        ->first();

        return view('frontBox/config')
        ->with('config', $config)
        ->with('title', 'Configuração Caixa');
    }

    public function configSave(Request $request){
        // $usuario = Usuario::find(get_id_user());
        $config = ConfigCaixa::
        where('usuario_id', get_id_user())
        ->first();

        if($config == null){
            $data = [
                'finalizar' => $request->finalizar ?? '',
                'reiniciar' => $request->reiniciar ?? '',
                'editar_desconto' => $request->editar_desconto ?? '',
                'editar_acrescimo' => $request->editar_acrescimo ?? '',
                'editar_observacao' => $request->editar_observacao ?? '', 
                'setar_valor_recebido' => $request->setar_valor_recebido ?? '',
                'forma_pagamento_dinheiro' => $request->forma_pagamento_dinheiro ?? '',
                'forma_pagamento_debito' => $request->forma_pagamento_debito ?? '',
                'forma_pagamento_credito' => $request->forma_pagamento_credito ?? '',
                'setar_quantidade' => $request->setar_quantidade ?? '',
                'forma_pagamento_pix' => $request->forma_pagamento_pix ?? '',
                'setar_leitor' => $request->setar_leitor ?? '',
                'finalizar_fiscal' => $request->finalizar_fiscal ?? '',
                'finalizar_nao_fiscal' => $request->finalizar_nao_fiscal ?? '',
                'valor_recebido_automatico' => 0,
                'modelo_pdv' => $request->modelo_pdv,
                'balanca_valor_peso' => $request->balanca_valor_peso,
                'balanca_digito_verificador' => $request->balanca_digito_verificador ?? 5,
                'valor_recebido_automatico' => 0,
                'impressora_modelo' => $request->impressora_modelo ?? 80,
                'usuario_id' => get_id_user(),
                'mercadopago_public_key' => $request->mercadopago_public_key ?? '',
                'mercadopago_access_token' => $request->mercadopago_access_token ?? ''
            ];

            ConfigCaixa::create($data);
            session()->flash("mensagem_sucesso", "Configuração salva!");

        }else{
            $config->finalizar = $request->finalizar ?? '';
            $config->reiniciar = $request->reiniciar ?? '';
            $config->editar_desconto = $request->editar_desconto ?? '';
            $config->editar_acrescimo = $request->editar_acrescimo ?? '';
            $config->setar_quantidade = $request->setar_quantidade ?? '';
            $config->editar_observacao = $request->editar_observacao ?? '';
            $config->setar_valor_recebido = $request->setar_valor_recebido ?? '';
            $config->forma_pagamento_dinheiro = $request->forma_pagamento_dinheiro ?? '';
            $config->forma_pagamento_debito = $request->forma_pagamento_debito ?? '';
            $config->forma_pagamento_credito = $request->forma_pagamento_credito ?? '';
            $config->forma_pagamento_pix = $request->forma_pagamento_pix ?? '';
            $config->setar_leitor = $request->setar_leitor ?? '';
            $config->finalizar_fiscal = $request->finalizar_fiscal ?? '';
            $config->finalizar_nao_fiscal = $request->finalizar_nao_fiscal ?? '';
            $config->balanca_digito_verificador = $request->balanca_digito_verificador ?? '';
            $config->valor_recebido_automatico = $request->valor_recebido_automatico ?? '';
            $config->valor_recebido_automatico = $request->valor_recebido_automatico ? true : false;

            $config->balanca_valor_peso = $request->balanca_valor_peso;
            $config->modelo_pdv = $request->modelo_pdv;
            $config->balanca_digito_verificador = $request->balanca_digito_verificador ?? 5;
            $config->mercadopago_public_key = $request->mercadopago_public_key ?? '';
            $config->mercadopago_access_token = $request->mercadopago_access_token ?? '';
            $config->impressora_modelo = $request->impressora_modelo ?? 80;

            $config->save();
            session()->flash("mensagem_sucesso", "Configuração editada!");

        }

        return redirect()->back();
    }

}
