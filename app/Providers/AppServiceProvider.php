<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ItemCompra;
use App\Models\Produto;
use App\Models\Usuario;
use App\Models\UsuarioAcesso;
use App\Models\Empresa;
use App\Models\PlanoEmpresa;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\Estoque;
use App\Models\PedidoEcommerce;
use Illuminate\Http\Request;
use App\Helpers\Menu;
use Illuminate\Pagination\Paginator;
use \DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        view()->composer('*',function($view){

            $menu = new Menu();

            // print_r($menu->getMenu());

            // die();

            $tema = null;
            $tema_menu = null;
            $video_url = $this->getVideoUrl();
            // $video_url = "";

            $ultimoAcesso = null;
            $value = session('user_logged');
            $empresa_id = null;
            $upgrade = false;
            $casasDecimais = 2;
            $armazenamento = 0;
            $totalParaArmazenar = 0;
            $percentualArmazenamento = 0;
            $logo = "";

            if($value){
                $empresa_id = $value['empresa'];
                $usuario = Usuario::find($value['id']);
                if($usuario == null){
                    return redirect('/login/logoff');
                }
                $tema = $usuario->tema;
                $tema_menu = $usuario->tema_menu;
                $empresa = Empresa::find($empresa_id);

                if($empresa->configNota){
                    $casasDecimais = $empresa->configNota->casas_decimais;

                    if($empresa->configNota->logo != ""){
                        $logo = $empresa->configNota->logo;
                    }
                }

                if($value['super']){
                    $contrato = 1;
                }else{
                    $upgrade = $this->getUpgrade($empresa);
                    
                    if($empresa->contrato){
                        $contrato = $empresa->contrato->status;

                    }else{
                        $contrato = 1;
                    }

                    //verifica plano do tipo armazenamento
                    if($empresa->planoEmpresa->plano->armazenamento > 0){
                        $armazenamento = number_format($this->totalArmazenamento($empresa)/1000, 2);
                        $totalParaArmazenar = $empresa->planoEmpresa->plano->armazenamento;

                        $percentualArmazenamento = 100-(($totalParaArmazenar-$armazenamento)/$totalParaArmazenar*100);
                        
                    }
                }



            }else{
                $contrato = 1;
            }

            $alertas = [];
            $semValidade = $this->verificaItensSemValidade($empresa_id);
            if($semValidade) {
                array_push($alertas, 
                    [
                        'msg' => 'Existe itens em estoque sem cadastro de data de validade!',
                        'titulo' => 'Alerta validade',
                        'link' => '/compras/produtosSemValidade'
                    ]
                );
            }

            $alertaValidade = $this->verificaValidadeProdutos($empresa_id);
            if($alertaValidade) {
                array_push($alertas, 
                    [
                        'msg' => 'Existe Produtos com validade próxima!',
                        'titulo' => 'Validade próxima',
                        'link' => '/compras/validadeAlerta'
                    ]
                );
            }

            $somaContas = $this->verificaContasPagar($empresa_id);
            if($somaContas > 0) {
                $dataHoje = date('d/m/Y', strtotime("-". getenv('ALERTA_CONTAS_DIAS') ." days",strtotime(date('Y-m-d'))));
                $dataFutura = date('d/m/Y', strtotime("+". getenv('ALERTA_CONTAS_DIAS') ." days",strtotime(date('Y-m-d'))));
                array_push($alertas, 
                    [
                        'msg' => 'Contas a pagar R$'.number_format($somaContas, 2),
                        'titulo' => 'Alerta contas',
                        'link' => '/contasPagar/filtro?fornecedor=&data_inicial='.$dataHoje.'&data_final='.$dataFutura.'&status=todos'
                    ]
                );
            }


            $somaContas = $this->verificaContasReceber($empresa_id);
            if($somaContas > 0) {
                $dataHoje = date('d/m/Y', strtotime("-". getenv('ALERTA_CONTAS_DIAS') ." days",strtotime(date('Y-m-d'))));
                $dataFutura = date('d/m/Y', strtotime("+". getenv('ALERTA_CONTAS_DIAS') ." days",strtotime(date('Y-m-d'))));
                array_push($alertas, 
                    [
                        'msg' => 'Contas a receber R$'.number_format($somaContas, 2),
                        'titulo' => 'Receber',
                        'link' => '/contasReceber/filtro?cliente=&data_inicial='.$dataHoje.'&data_final='.$dataFutura.'&status=todos'
                    ]
                );
            }

            if (\Schema::hasTable('produtos')){

                $produtos = Produto::
                where('empresa_id', $empresa_id)
                ->get();

                $contDesfalque = 0;
                foreach($produtos as $p){
                    if($p->estoque_minimo > 0){
                        $estoque = Estoque::
                        where('produto_id', $p->id)
                        ->where('empresa_id', $empresa_id)
                        ->first();
                        $temp = null;
                        if($estoque == null){
                            $contDesfalque++;
                        }else{
                            $contDesfalque++;
                        }
                    }
                }

                if($contDesfalque > 0){
                    array_push($alertas, 
                        [
                            'msg' => 'Produtos com estoque minimo: ' . $contDesfalque,
                            'titulo' => 'Alerta estoque',
                            'link' => '/relatorios/filtroEstoqueMinimo'
                        ]
                    );
                }

            }

            $countPedidos = $this->verificaPedidosEcommerce($empresa_id);

            if($countPedidos > 0) {

                array_push($alertas, 
                    [
                        'msg' => 'Pedidos novos: ' . $countPedidos,
                        'titulo' => 'Pedido Ecommerce',
                        'link' => '/pedidosEcommerce/filtro?cliente=&data_inicial=&data_final=&estado=0'
                    ]
                );
            }
            
            $rotaAtiva = $this->rotaAtiva();
            $ultimoAcesso = null;

            if($value){
                $usuario = Usuario::find($value['id']);

                if($usuario){
                    $ultimoAcesso = $usuario->ultimoAcesso();
                }
            }

            $uri = $_SERVER['REQUEST_URI'];
            $uri = explode("/", $uri);
            $uri = "/".$uri[1];

            $view->with('alertas', $alertas);
            $view->with('rotaAtiva', $rotaAtiva);
            $view->with('menu', $menu->preparaMenu());
            $view->with('tema', $tema);
            $view->with('tema_menu', $tema_menu);
            $view->with('logo', $logo);
            $view->with('armazenamento', $armazenamento);
            $view->with('totalParaArmazenar', $totalParaArmazenar);
            $view->with('percentualArmazenamento', $percentualArmazenamento);
            $view->with('video_url', $video_url);
            $view->with('ultimoAcesso', $ultimoAcesso);
            $view->with('upgrade', $upgrade);
            $view->with('uri', $uri);
            $view->with('casasDecimais', $casasDecimais);
            $view->with('contrato', $contrato);
        });
}

private function getUpgrade($empresa){
    $empresa = Empresa::find($empresa->id);

    $expiracao = $empresa->planoEmpresa->expiracao;

    $alertaDias = getenv('ALERTA_PAGAMENTO_DIAS');

    $strValidade = strtotime($expiracao);
    $strHoje = strtotime(date('Y-m-d'));
    $dif = $strValidade - $strHoje;
    $dif = $dif/24/60/60;

    if($dif <= $alertaDias) return true;
    return false;
}

private function getVideoUrl(){
    if (isset($_SERVER['REQUEST_URI'])){ 
        $uri = $_SERVER['REQUEST_URI'];
        // echo $uri;
        // die;
        // $uri = explode("/", $uri);
        // $uri = $uri[1];

        $menu = new Menu();

        return $menu->getUrlVideo($uri);
    }
}

private function rotaAtiva(){
    if (isset($_SERVER['REQUEST_URI'])){ 
        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode("/", $uri);
        $uri = $uri[1];

        $rotaDeCadastros = [
            'categorias', 'produtos', 'clientes', 'fornecedores', 'transportadoras', 
            'funcionarios', 'categoriasServico', 'servicos', 'categoriasConta', 
            'veiculos', 'usuarios', 'marcas', 'contaBancaria', 'acessores', 'gruposCliente',
            'listaDePrecos'
        ];

        $rotaDeEntradas = [
            'compraFiscal', 'compraManual', 'compras', 'cotacao', 'dfe'
        ];

        $rotaDeEstoque = [
            'estoque'
        ];

        $rotaFinanceiro = [
            'contasPagar', 'contasReceber', 'fluxoCaixa', 'graficos'
        ];

        $rotaConfig = [
            'configNF', 'escritorio', 'naturezaOperacao', 'tributos', 'enviarXml'
        ];

        $rotaPedidos = [
            'pedidos', 'deliveryComplemento', 'telasPedido', 'controleCozinha', 'mesas'
        ];

        $rotaVenda = [
            'caixa', 'vendas', 'frenteCaixa', 'orcamentoVenda', 'ordemServico', 'vendasEmCredito', 'devolucao', 'agendamentos'
        ];

        $rotaCTe = [
            'cte', 'categoriaDespesa'
        ];

        $rotaMDFe = [
            'mdfe'
        ];

        $rotaEvento = [
            'eventos'
        ];

        $rotaLocacao = [
            'locacao'
        ];

        $rotaRelatorio = [
            'relatorios',
            'dre'
        ];

        $rotaEcommerce = [
            'categoriaEcommerce', 'produtoEcommerce', 'configEcommerce', 
            'carrosselEcommerce', 'pedidosEcommerce', 'autorPost', 'categoriaPosts',
            'postBlog', 'contatoEcommerce', 'clienteEcommerce', 'informativoEcommerce'
        ];

        if(in_array($uri, $rotaDeCadastros)) return 'Cadastros';
        if(in_array($uri, $rotaDeEntradas)) return 'Entradas';
        if(in_array($uri, $rotaDeEstoque)) return 'Estoque';
        if(in_array($uri, $rotaFinanceiro)) return 'Financeiro';
        if(in_array($uri, $rotaConfig)) return 'Configurações';
        if(in_array($uri, $rotaVenda)) return 'Vendas';
        if(in_array($uri, $rotaCTe)) return 'CT-e';
        if(in_array($uri, $rotaMDFe)) return 'MDF-e';
        if(in_array($uri, $rotaEvento)) return 'Eventos';
        if(in_array($uri, $rotaRelatorio)) return 'Relatórios';
        if(in_array($uri, $rotaLocacao)) return 'Locação';
        if(in_array($uri, $rotaPedidos)) return 'Pedidos';
        if(in_array($uri, $rotaEcommerce)) return 'Ecommerce';

    }else{
        return "";
    }
}

private function verificaItensSemValidade($empresa_id){
    if (\Schema::hasTable('produtos')){
        $produtos = Produto::select('id')
        ->where('alerta_vencimento', '>', 0)
        ->where('empresa_id', $empresa_id)
        ->get();
        $itensCompra = ItemCompra::
        select('item_compras.*')
        ->where('validade', NULL)
        ->join('compras', 'compras.id', '=', 'item_compras.compra_id')
        ->where('compras.empresa_id', $empresa_id)
        ->limit(100)->get();


        foreach($itensCompra as $i){
            foreach($produtos as $p){
                if($p->id == $i->produto_id){
                    return true;
                }
            }
        }
        return false;
    }
}

private function verificaValidadeProdutos($empresa_id){
    if (\Schema::hasTable('item_compras')){

        $dataHoje = date('Y-m-d', strtotime("-30 days",strtotime(date('Y-m-d'))));
        $dataFutura = date('Y-m-d', strtotime("+30 days",strtotime(date('Y-m-d'))));

        $itens = ItemCompra::
        select('item_compras.*')
        ->join('compras', 'compras.id', '=', 'item_compras.compra_id')
        ->whereBetween('validade', [$dataHoje, $dataFutura])
        ->where('compras.empresa_id', $empresa_id)
        ->limit(300)->get();


        foreach($itens as $i){
            $strValidade = strtotime($i->validade);
            $strHoje = strtotime(date('Y-m-d'));
            $dif = $strValidade - $strHoje;
            $dif = $dif/24/60/60;
            if($dif <= $i->produto->alerta_vencimento) return true;
        }

        return false;
    }
}

private function verificaContasPagar($empresa_id){

    if (\Schema::hasTable('conta_pagars')){
        $dataHoje = date('Y-m-d', strtotime("-". getenv('ALERTA_CONTAS_DIAS') ." days",strtotime(date('Y-m-d'))));
        $dataFutura = date('Y-m-d', strtotime("+". getenv('ALERTA_CONTAS_DIAS') ." days",strtotime(date('Y-m-d'))));

        $somaContas = ContaPagar::
        selectRaw('sum(valor_integral) as valor')
        ->whereBetween('data_vencimento', [$dataHoje, $dataFutura])
        ->where('status', 0)
        ->where('empresa_id', $empresa_id)
        ->first();

        return $somaContas->valor ?? 0;
    }
}

private function verificaContasReceber($empresa_id){
    if (\Schema::hasTable('conta_recebers')){
        $dataHoje = date('Y-m-d', strtotime("-". getenv('ALERTA_CONTAS_DIAS') ." days",strtotime(date('Y-m-d'))));
        $dataFutura = date('Y-m-d', strtotime("+". getenv('ALERTA_CONTAS_DIAS') ." days",strtotime(date('Y-m-d'))));

        $somaContas = ContaReceber::
        selectRaw('sum(valor_integral) as valor')
        ->whereBetween('data_vencimento', [$dataHoje, $dataFutura])
        ->where('status', 0)
        ->where('empresa_id', $empresa_id)
        ->first();

        return $somaContas->valor ?? 0;
    }
}

private function verificaPedidosEcommerce($empresa_id){
    if (\Schema::hasTable('pedido_ecommerces')){

        $pedidos = PedidoEcommerce::
        where('status_preparacao', 0)
        ->where('valor_total', '>', 0)
        ->where('empresa_id', $empresa_id)
        ->get();

        return sizeof($pedidos);
    }
    return 0;
}

private function totalArmazenamento($empresa){
    $armazenamento = $empresa->planoEmpresa->plano->armazenamento;
    $tabelasArmazenamento = tabelasArmazenamento();

    $soma = 0;
    foreach($tabelasArmazenamento as $key => $t){
        try{
            $res = DB::table($key)
            ->select(DB::raw('count(*) as linhas'))
            ->where('empresa_id', $empresa->id)
            ->first();

            $soma += $res->linhas * $t;
        }catch(\Exception $e){

        }
    }

    return $soma;
}


}
