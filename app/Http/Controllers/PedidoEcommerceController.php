<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoEcommerce;
use App\Models\Cidade;
use App\Models\Venda;
use App\Models\ItemVenda;
use App\Models\ConfigNota;
use App\Models\Cliente;
use App\Models\NaturezaOperacao;
use App\Models\Transportadora;
use App\Models\Frete;
use App\Models\ConfigEcommerce;
use Dompdf\Dompdf;

class PedidoEcommerceController extends Controller
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

    public function verificaPagamentos(){
        $pedidos = PedidoEcommerce::
        where('empresa_id', $this->empresa_id)
        ->limit(100)
        ->get();

        $config = ConfigEcommerce::
        where('empresa_id', $this->empresa_id)
        ->first();
        try{
            \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);

            $count = 0;
            foreach($pedidos as $p){
                if($p->transacao_id){
                    $payStatus = \MercadoPago\Payment::find_by_id($p->transacao_id);

                    if($p->status_detalhe != $payStatus->status){
                        $p->status_pagamento = $payStatus->status;
                        $p->status_detalhe = $payStatus->status_detail;

                        if($payStatus->status == "approved"){
                            $p->status == 2;
                        }else{
                            $p->status == 3;
                        }

                        $p->save();
                    }
                }
            }
            session()->flash("mensagem_sucesso", "Consulta realizada com sucesso!!");


        }catch(\Exception $e){
            session()->flash("mensagem_erro", "Erro: " . $e->getMessage());
        }

        return redirect()->back();
    } 

    public function index(){
    	$pedidos = PedidoEcommerce::
    	where('empresa_id', $this->empresa_id)
    	->where('status', '!=', 0)
        ->orderBy('id', 'desc')
        ->paginate(40);

        // $this->verificaPagamentos();

        return view('pedidoEcommerce/list')
        ->with('pedidos', $pedidos)
        ->with('title', 'Pedidos Ecommerce');
    }

    public function filtro(Request $request){
        $pedidos = PedidoEcommerce::
        select('pedido_ecommerces.*')
        ->where('pedido_ecommerces.empresa_id', $this->empresa_id)
        ->join('cliente_ecommerces', 'cliente_ecommerces.id' , '=', 'pedido_ecommerces.cliente_id')

        ->where('pedido_ecommerces.status', '!=', 0)
        ->orderBy('pedido_ecommerces.id', 'desc');

        if($request->data_inicial && $request->data_final){
            $pedidos->whereBetween('pedido_ecommerces.created_at', [
                $this->parseDate($request->data_inicial) . " 00:00:00", 
                $this->parseDate($request->data_final) . " 23:59:59"
            ]);
        }

        if($request->cliente){
            $pedidos->where('cliente_ecommerces.nome', 'LIKE', "%$request->cliente%");
        }

        if($request->estado != 'TODOS'){
            $pedidos->where('pedido_ecommerces.status_preparacao', $request->estado);
        }

        $pedidos = $pedidos->get();

        return view('pedidoEcommerce/list')
        ->with('pedidos', $pedidos)
        ->with('title', 'Pedidos Ecommerce');
    }

    private function parseDate($date, $plusDay = false){
        return date('Y-m-d', strtotime(str_replace("/", "-", $date)));
    }

    public function detalhar($id){
    	$pedido = PedidoEcommerce::find($id);

    	return view('pedidoEcommerce/detalhe')
    	->with('pedido', $pedido)
    	->with('title', 'Pedido Ecommerce');

    }

    public function gerarNFe($id){
        $pedido = PedidoEcommerce::find($id);

        $erros = [];
        $doc = $pedido->cliente->cpf;

        if(strlen($doc) == 14){
            if(!$this->validaCPF($doc)){
                array_push($erros, "CPF cliente inválido");
            }
        }

        if(strlen($doc) == 18){
            if(!$this->validaCNPJ($doc)){
                array_push($erros, "CNPJ cliente inválido");
            }
        }

        $cidade = Cidade::
        where('nome', $pedido->endereco->cidade)
        ->first();

        if($cidade == null){
            array_push($erros, "Cidade cliente inválida");
        }

        $cidades = Cidade::all();

        $naturezas = NaturezaOperacao::
        where('empresa_id', $this->empresa_id)
        ->get();

        $transportadoras = Transportadora::
        where('empresa_id', $this->empresa_id)
        ->get();

        return view('pedidoEcommerce/emitir_nfe')
        ->with('pedido', $pedido)
        ->with('erros', $erros)
        ->with('cidade', $cidade)
        ->with('cidades', $cidades)
        ->with('naturezas', $naturezas)
        ->with('transportadoras', $transportadoras)
        ->with('title', 'Emitir NFe');

    }

    private function validaCPF($cpf){

        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
    // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }

    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

    // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    private function validaCNPJ($cnpj){

        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

    // Valida tamanho
        if (strlen($cnpj) != 14)
            return false;

    // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;   

    // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return false;

    // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }

    public function salvarVenda(Request $request){
        $pedido = PedidoEcommerce::find($request->id);

        $cliente = $this->salvarCliente($pedido);

        $transportadora = $request->transportadora ?? NULL;
        $natureza = $request->natureza;
        $frete = $this->criaFrete($request);

        $tipoPagamento = '01';
        if($pedido->forma_pagamento == 'Pix'){
            $tipoPagamento = '17';
        }elseif($pedido->forma_pagamento == 'Boleto'){
            $tipoPagamento = '15';
        }else{
            $tipoPagamento = '03';
        }

        $dataVenda = [
            'cliente_id' => $cliente->id,
            'usuario_id' => get_id_user(),
            'frete_id' => $frete->id,
            'valor_total' => $pedido->valor_total,
            'forma_pagamento' => 'a_vista',
            'NfNumero' => 0,
            'natureza_id' => $natureza,
            'chave' => '',
            'path_xml' => '',
            'estado' => 'DISPONIVEL',
            'observacao' => '',
            'desconto' => 0,
            'transportadora_id' => $transportadora,
            'sequencia_cce' => 0,
            'tipo_pagamento' => $tipoPagamento,
            'empresa_id' => $this->empresa_id,
            'pedido_ecommerce_id' => $pedido->id
        ];

        $venda = Venda::create($dataVenda);

        foreach($pedido->itens as $i){
            $dataItem = [
                'produto_id' => $i->produto->produto->id,
                'venda_id' => $venda->id,
                'quantidade' => $i->quantidade,
                'valor' => $i->produto->valor
            ];

            $item = ItemVenda::create($dataItem);
        }

        session()->flash("mensagem_sucesso", "Venda de pedido gerada com sucesso!");
        return redirect('/vendas');
    }

    private function salvarCliente($pedido){

        $cliente = $pedido->cliente;
        $endereco = $pedido->endereco;

        $clienteExist = Cliente::
        where('cpf_cnpj', $cliente->cpf)
        ->first();

        $cidade = Cidade::
        where('nome', $endereco->cidade)
        ->first();

        if($clienteExist == null){
            //criar novo

            $dataCliente = [
                'razao_social' => "$cliente->nome $cliente->sobre_nome",
                'nome_fantasia' => "$cliente->nome $cliente->sobre_nome",
                'bairro' => $endereco->bairro,
                'numero' => $endereco->numero,
                'rua' => $endereco->rua,
                'cpf_cnpj' => $cliente->cpf,
                'telefone' => $cliente->telefone,
                'celular' => $cliente->telefone,
                'email' => $cliente->email,
                'cep' => $endereco->cep,
                'ie_rg' => $cliente->ie,
                'consumidor_final' => 1,
                'limite_venda' => 0,
                'cidade_id' => $cidade->id, 
                'contribuinte' => 1,
                'rua_cobranca' => '',
                'numero_cobranca' => '',
                'bairro_cobranca' => '',
                'cep_cobranca' => '',
                'cidade_cobranca_id' => NULL,
                'empresa_id' => $this->empresa_id,
                'cod_pais' => 1058,
                'id_estrangeiro' => '',
                'grupo_id' => 0
            ];

            print_r($dataCliente);

            return Cliente::create($dataCliente);

        }else{
            //atualiza endereço

            $clienteExist->rua = $endereco->rua;
            $clienteExist->numero = $endereco->numero;
            $clienteExist->bairro = $endereco->bairro;
            $clienteExist->cep = $endereco->cep;
            $clienteExist->cidade_id = $cidade->id;

            $clienteExist->save();
            return $clienteExist;
        }

    }

    private function criaFrete($request){
        $frete = null;

        if($request->frete != '9'){
            $frete = Frete::create([
                'placa' => $request->placa ?? '',
                'valor' => $request->valor_frete,
                'tipo' => $request->frete,
                'qtdVolumes' => $request->qtd_volumes ?? 0,
                'uf' => $request->uf_placa ?? '',
                'numeracaoVolumes' => $request->numeracao_volumes ?? 0,
                'especie' => $request->especie ?? '',
                'peso_liquido' => $request->peso_liquido ? 
                __replace($request->peso_liquido) : 0,
                'peso_bruto' => $request->peso_bruto ? 
                __replace($request->peso_bruto) : 0
            ]);
        }

        return $frete;
    }

    public function imprimir($id){
        $pedido = PedidoEcommerce::find($id);

        if(valida_objeto($pedido)){
            $config = ConfigNota::
            where('empresa_id', $this->empresa_id)
            ->first();
            $p = view('pedidoEcommerce/print')
            ->with('config', $config)
            ->with('pedido', $pedido);
            // return $p;

            $domPdf = new Dompdf(["enable_remote" => true]);
            $domPdf->loadHtml($p);

            $pdf = ob_get_clean();

            $domPdf->setPaper("A4");
            $domPdf->render();
            $domPdf->stream("pedido ecommerce $id.pdf");
        }else{
            return redirect('/403');
        }
    }

    public function delete($id){
        $pedido = PedidoEcommerce::find($id);
        if(valida_objeto($pedido)){
            try{
                $pedido->delete();
                session()->flash("mensagem_sucesso", "Pedido removido com sucesso!");

            }catch(\Exception $e){
                session()->flash("mensagem_erro", "Erro: " . $e->getMessage());
            }

            return redirect('/pedidosEcommerce');
        }else{
            return redirect('/403');
        }
    }

    public function alterarStatus(Request $request){
        try{

            $pedido = PedidoEcommerce::find($request->id);

            $pedido->status_preparacao = $request->status_preparacao;
            $pedido->observacao = $request->observacao ?? '';
            $pedido->codigo_rastreio = $request->codigo_rastreio ?? '';

            $pedido->save();
            session()->flash("mensagem_sucesso", "Status alterado!");

        }catch(\Exception $e){
            session()->flash("mensagem_erro", "Erro: " . $e->getMessage());
        }

        return redirect()->back();
    }

}
