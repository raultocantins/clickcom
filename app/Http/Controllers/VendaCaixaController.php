<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendaCaixa;
use App\Helpers\StockMove;
use App\Models\ItemVendaCaixa;
use App\Models\CreditoVenda;
use App\Models\ConfigNota;
use App\Models\PedidoDelivery;
use App\Models\Produto;
use App\Models\ProdutoPizza;
use App\Models\Pedido;
use App\Models\AberturaCaixa;
use App\Models\ComissaoVenda;
use App\Models\ContaReceber;
use App\Models\Usuario;
use App\Models\Agendamento;
use App\Models\CategoriaConta;
use App\Models\NaturezaOperacao;

class VendaCaixaController extends Controller
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
  
  public function save(Request $request){

    $venda = $request->venda;
    $agendamento_id = $venda['agendamento_id'];
    
    $config = ConfigNota::
    where('empresa_id', $this->empresa_id)
    ->first();

    $totalVenda = str_replace(",", ".", $venda['valor_total']) + str_replace(",", ".", $venda['acrescimo']) - str_replace(",", ".", $venda['desconto']);

    $result = VendaCaixa::create([
      'cliente_id' => $venda['cliente'],
      'usuario_id' => get_id_user(),
      'natureza_id' => $config->nat_op_padrao,
      'valor_total' => $totalVenda,
      'acrescimo' => str_replace(",", ".", $venda['acrescimo']),
      'troco' => str_replace(",", ".", $venda['troco']),
      'dinheiro_recebido' => str_replace(",", ".", $venda['dinheiro_recebido']),
      'forma_pagamento' => $venda['acao'] == 'credito' ? 'credito' : " ",
      'tipo_pagamento' => $venda['tipo_pagamento_1'] ? '99' : $venda['tipo_pagamento'],
      'estado' => 'DISPONIVEL',
      'NFcNumero' => 0,
      'chave' => '',
      'path_xml' => '',
      'nome' => $venda['nome'] ?? '',
      'cpf' => $venda['cpf'] ?? '',
      'observacao' => $venda['observacao'] ?? '',
      'desconto' => $venda['desconto'],
      'pedido_delivery_id' => isset($venda['delivery_id']) ? $venda['delivery_id'] : 0,
      'tipo_pagamento_1' => $venda['tipo_pagamento_1'] ?? '', 
      'valor_pagamento_1' => $venda['valor_pagamento_1'] ? __replace($venda['valor_pagamento_1']) :  0,
      'tipo_pagamento_2' => $venda['tipo_pagamento_2'] ?? '',
      'valor_pagamento_2' => $venda['valor_pagamento_2'] ? __replace($venda['valor_pagamento_2']) : 0,
      'tipo_pagamento_3' => $venda['tipo_pagamento_3'] ?? '',
      'valor_pagamento_3' => $venda['valor_pagamento_3'] ? __replace($venda['valor_pagamento_3']) : 0,
      'empresa_id' => $this->empresa_id,
      'bandeira_cartao' => $venda['bandeira_cartao'],
      'cAut_cartao' => $venda['cAut_cartao'] ?? '',
      'cnpj_cartao' => $venda['cnpj_cartao'] ?? '',
      'descricao_pag_outros' => $venda['descricao_pag_outros'] ?? '',
    ]);

    if($venda['tipo_pagamento'] == '06'){

      $dataVenc = date('Y-m-d', strtotime("+30 days",
        strtotime(date('Y-m-d'))));
      $categoria = $this->categoriaCrediario();
      $resultConta = ContaReceber::create([
        'venda_caixa_id' => $result->id,
        'venda_id' => NULL,
        'data_vencimento' => $dataVenc,
        'data_recebimento' => $dataVenc,
        'valor_integral' => $totalVenda,
        'valor_recebido' => 0,
        'status' => false,
        'referencia' => "Venda PDV " . $result->id,
        'categoria_id' => $categoria,
        'empresa_id' => $this->empresa_id,
        'cliente_id' => $venda['cliente']
      ]);
    }
    $contCredito = 1;
    if($venda['tipo_pagamento_1'] == '06'){
      $this->salvaCredito($result->id, $venda['valor_pagamento_1'], $contCredito, 
        $venda['cliente']);
      $contCredito++;
    }

    if($venda['tipo_pagamento_2'] == '06'){
      $this->salvaCredito($result->id, $venda['valor_pagamento_2'], $contCredito, 
        $venda['cliente']);
      $contCredito++;
    }

    if($venda['tipo_pagamento_3'] == '06'){
      $this->salvaCredito($result->id, $venda['valor_pagamento_3'], $contCredito, 
        $venda['cliente']);
    }

    if($venda['codigo_comanda'] > 0){
      $pedido = Pedido::
      where('comanda', $venda['codigo_comanda'])
      ->where('status', 0)
      ->where('desativado', 0)
      ->first();

      $pedido->status = 1;
      $pedido->desativado = 1;
      $pedido->save();
    }

    $itens = $venda['itens'];
    $stockMove = new StockMove();

    $natureza = NaturezaOperacao::find($config->nat_op_padrao);
    foreach ($itens as $i) {

      $produto = Produto::find($i['id']);
      $cfop = 0;

      if($natureza->sobrescreve_cfop){
        $cfop = $natureza->CFOP_saida_estadual;
      }else{
        $cfop = $produto->CFOP_saida_estadual;
      }

      ItemVendaCaixa::create([
        'venda_caixa_id' => $result->id,
        'produto_id' => (int) $i['id'],
        'quantidade' => (float) str_replace(",", ".", $i['quantidade']),
        'valor' => (float) str_replace(",", ".", $i['valor']),
        'item_pedido_id' => isset($i['itemPedido']) ? $i['itemPedido'] : NULL,
        'observacao' => $i['obs'] ?? '',
        'cfop' => $cfop
      ]);

      if(!isset($venda['delivery_id']) || $venda['delivery_id'] == 0){
     // nao delivery
        $prod = Produto
        ::where('id', $i['id'])
        ->first();

        if(isset($venda['pizza']) && $i['pizza'] == 1){
          $sabores = explode(" | ", $i['nome']);
          $totalSabores = count($sabores);
          foreach($sabores as $sb){

            $produto = Produto::
            where('nome', $sb)
            ->first();

            $produtoPizza = ProdutoPizza::
            where('produto_id', $i['id'])
            ->where('valor', $i['valor'])
            ->first();

            if(!empty($produto->receita)){
              $receita = $produto->receita;
              foreach($receita->itens as $rec){

                $stockMove->downStock(
                  $rec->produto_id, 
                  (float) str_replace(",", ".", $i['quantidade']) 
                      * 
                  ((($rec->quantidade/$totalSabores)/$receita->pedacos)*$produtoPizza->tamanho->pedacos)/$receita->rendimento
                );
              }
            }


          }

        }else if(!empty($prod->receita)){

          $receita = $prod->receita; 

          foreach($receita->itens as $rec){

            if(!empty($rec->produto->receita)){ 

              $receita2 = $rec->produto->receita; 

              foreach($receita2->itens as $rec2){
                $stockMove->downStock(
                  $rec2->produto_id, 
                  (float) str_replace(",", ".", $i['quantidade']) * 
                  ($rec2->quantidade/$receita2->rendimento)
                );
              }
            }else{

              $stockMove->downStock(
                $rec->produto_id, 
                (float) str_replace(",", ".", $i['quantidade']) * 
                ($rec->quantidade/$receita->rendimento)
              );
            }
          }

        }else{
          $stockMove->downStock(
            (int) $i['id'], 
            (float) str_replace(",", ".", $i['quantidade'])
          );
        }
      }

    }

        //DELIVERY
    if(isset($venda['delivery_id']) && $venda['delivery_id'] > 0){
      $pedidoDelivery = PedidoDelivery
      ::where('id', $venda['delivery_id'])
      ->first();

      foreach($pedidoDelivery->itens as $i){

        if(count($i->sabores) > 0){

          $totalSabores = count($i->sabores);
          foreach($i->sabores as $sb){
            if(!empty($sb->produto->produto->receita)){
              $receita = $sb->produto->produto->receita;
              foreach($receita->itens as $rec){

                $stockMove->downStock(
                  $rec->produto_id, 
                  (float) str_replace(",", ".", $i['quantidade']) 
                      * 
                  ((($rec->quantidade/$totalSabores)/$receita->pedacos)*$i->tamanho->pedacos)/$receita->rendimento
                );
              }
            }
          }
        }else{

          if(!empty($i->produto->produto->receita)){
            $receita = $i->produto->produto->receita; 
            foreach($receita->itens as $rec){

              if(!empty($rec->produto->receita)){ 

                $receita2 = $rec->produto->receita; 

                foreach($receita2->itens as $rec2){
                  $stockMove->downStock(
                    $rec2->produto_id, 
                    (float) str_replace(",", ".", $i['quantidade']) * 
                    ($rec2->quantidade/$receita2->rendimento)
                  );
                }
              }else{


                $stockMove->downStock(
                  $rec->produto_id, 
                  (float) str_replace(",", ".", $i['quantidade']) * 
                  ($rec->quantidade/$receita->rendimento)
                );
              }
            }



          }else{

            $stockMove->downStock(
              $i->produto->produto->id, 
              (float) str_replace(",", ".", $i['quantidade'])
            );
          }
        }

      }
    }

    $usuario = Usuario::find(get_id_user());
    if(isset($usuario->funcionario)){
      $percentual_comissao = $usuario->funcionario->percentual_comissao;
      $valorComissao = ($totalVenda * $percentual_comissao) / 100;
      ComissaoVenda::create(
        [
          'funcionario_id' => $usuario->funcionario->id,
          'venda_id' => $result->id,
          'tabela' => 'venda_caixas',
          'valor' => $valorComissao,
          'status' => 0,
          'empresa_id' => $this->empresa_id
        ]
      );
    }

    if($agendamento_id > 0){

      $agendamento = Agendamento::find($agendamento_id);
      $valorComissao = $this->calculaComissao($agendamento);
      $agendamento->valor_comissao = $valorComissao;
      $agendamento->status = 1;
      $agendamento->save();
    }
    echo json_encode($result);
  }

  private function categoriaCrediario(){
    $cat = CategoriaConta::
    where('empresa_id', $this->empresa_id)
    ->where('nome', 'Credi??rio')
    ->first();
    if($cat != null) return $cat->id;
    $cat = CategoriaConta::create([
      'nome' => 'Credi??rio',
      'empresa_id' => $this->empresa_id,
      'tipo'=> 'receber'
    ]);
    return $cat->id;
  }

  private function salvaCredito($vendaId, $totalVenda, $contCredito, $clienteId){
    if($contCredito == 1) $dias = 30;
    if($contCredito == 2) $dias = 60;
    if($contCredito == 3) $dias = 90;
    $dataVenc = date('Y-m-d', strtotime("+$dias days",
      strtotime(date('Y-m-d'))));
    $categoria = $this->categoriaCrediario();

    $resultConta = ContaReceber::create([
      'venda_caixa_id' => $vendaId,
      'venda_id' => NULL,
      'data_vencimento' => $dataVenc,
      'data_recebimento' => $dataVenc,
      'valor_integral' => $totalVenda,
      'valor_recebido' => 0,
      'status' => false,
      'referencia' => "Venda PDV " . $vendaId,
      'categoria_id' => $categoria,
      'empresa_id' => $this->empresa_id,
      'cliente_id' => $clienteId
    ]);
  }

  private function calculaComissao($agendamento){
    $soma = 0;
    $somaDesconto = 0;
    $total = $agendamento->total + $agendamento->desconto;
    foreach($agendamento->itens as $key => $i){
      $tempDesc = 0;
      $valorServico = $i->servico->valor;
      
      if($key < sizeof($agendamento->itens)-1){

        $media = (((($valorServico - $total)/$total))*100);
        
        $media = 100 - ($media * -1);
        $tempDesc = ($agendamento->desconto*$media)/100;

        $somaDesconto += $tempDesc;

      }else{
        $tempDesc = $agendamento->desconto - $somaDesconto;
      }

      $comissao = $i->servico->comissao;

      $valorComissao = ($valorServico - $tempDesc) * ($comissao/100);
      $soma += $valorComissao;
    }

    return number_format($soma,2);
  }

  public function diaria(){
    $ab = AberturaCaixa::where('ultima_venda_nfe', 0)
    ->where('ultima_venda_nfce', 0)
    ->where('empresa_id', $this->empresa_id)
    ->orderBy('id', 'desc')->first();

    date_default_timezone_set('America/Sao_Paulo');
    $hoje = date("Y-m-d") . " 00:00:00";
    $amanha = date('Y-m-d', strtotime('+1 days')). " 00:00:00";
    $vendas = VendaCaixa::
    whereBetween('created_at', [$ab->created_at, 
     $amanha])
    ->where('empresa_id', $this->empresa_id)
    ->get();
    echo json_encode($vendas);
  }
  
public function calcComissao(){

    ComissaoVenda::
    where('empresa_id', $this->empresa_id)
    ->delete();

    $comissao = ComissaoVenda::
    where('empresa_id', $this->empresa_id)
    ->get();

    // echo $comissao;
    // die;

    $vendas = VendaCaixa::
    where('empresa_id', $this->empresa_id)
    ->get();

    // echo $vendas;
    // die;

    foreach($vendas as $v){
      $comissao = ComissaoVenda::
      where('empresa_id', $this->empresa_id)
      ->where('tabela', 'venda_caixas')
      ->where('venda_id', $v->id)
      ->first();
      if($comissao == null){
        try{
          $usuario = Usuario::find($v->usuario_id);
          if(isset($usuario->funcionario)){

            $percentual_comissao = __replace($usuario->funcionario->percentual_comissao);
            $valorComissao = ($v->valor_total * $percentual_comissao) / 100;
            ComissaoVenda::create(
              [
                'funcionario_id' => $usuario->funcionario->id,
                'venda_id' => $v->id,
                'tabela' => 'venda_caixas',
                'valor' => $valorComissao,
                'status' => 0,
                'empresa_id' => $this->empresa_id,
                'created_at' => $v->created_at,
              ]
            );
          }else{
            echo $v->usuario->nome . ' - '. $v->created_at . "<br>";
          }
        }catch(\Exception $e){
          echo "Erro: ". $e->getMessage();
        }
      }

    }
  }
}
