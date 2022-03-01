<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\ItemPedidoDelivery;
use App\Models\TelaPedido;

class CozinhaController extends Controller
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
    
    public function index($id = NULL){

        $tela = 'Todos';
        if($id != null){
            $tela = TelaPedido::find($id)->nome;
        }
        return view('controleCozinha/index')
        ->with('cozinhaJs', true)
        ->with('id', $id)
        ->with('tela', $tela)
        ->with('title', 'Controle de Pedidos');
    }

    public function buscar(Request $request){
        $itens = ItemPedido::
        select('item_pedidos.*')
        ->join('pedidos', 'pedidos.id', '=', 'item_pedidos.pedido_id')
        ->where('item_pedidos.status', false)
        ->where('pedidos.empresa_id', $this->empresa_id)
        ->orderBy('item_pedidos.created_at', 'desc')
        ->get();

        // $itensDelivery = ItemPedidoDelivery::
        // where('status', false)
        // ->orderBy('created_at', 'desc')
        // ->get();

        $tela = $request->tela;
        $tipoTela = null;
        if($tela > 0){
            $tipoTela = TelaPedido::find($tela);
        }

        $arr = [];
        foreach($itens as $i){
            $pTemp = $i->produto;
            $i->produto;
            $i->comanda = $i->pedido->comanda;


            $complementos = "";
            $adicionais = "";
            foreach($i->itensAdicionais as $key => $a){
                // if($a->adicional->produto->adicional){
                //     $adicionais .= $a->adicional->nome . " | ";
                // }else{
                //     $complementos .= $a->adicional->nome . " | ";
                // }
                $adicionais .= $a->adicional->nome . " | ";

                
            }
            
            if(strlen($complementos) > 0)
                $complementos = substr($complementos, 0, strlen($complementos)-2);

            if(strlen($adicionais) > 0)
                $adicionais = substr($adicionais, 0, strlen($adicionais)-2);

            $saboresPizza = "";

            foreach($i->sabores as $key => $s){
                $saboresPizza .= $s->produto->produto->nome . ($key < count($i->sabores)-1 ? " | " : "");
            }

            $i->tamanhoPizza = $i->tamanho != null ? $i->tamanho->nome() : false;


            $i->adicionais = $adicionais;
            $i->complementos = $complementos;
            $i->saboresPizza = $saboresPizza;
            $i->data = \Carbon\Carbon::parse($i->created_at)->format('d/m H:i');

            $dataPedido = \Carbon\Carbon::parse($i->created_at)->format('Y-m-d H:i:s');
            $dataAgora = date('Y-m-d H:i:s');

            $date1 = strtotime($dataPedido);
            $date2 = strtotime($dataAgora);

            $dif = (int)($date2 - $date1)/60;
            $i->cor = "white";
            if($tipoTela != null && $dif > $tipoTela->alerta_amarelo){
                $i->cor = 'yellow';
            }

            if($tipoTela != null && $dif > $tipoTela->alerta_vermelho){
                $i->cor = 'red';
            }

            $i->teste = $dif;
            $mesa = "";
            if($i->pedido->mesa_id != null){
                $mesa = $i->pedido->mesa->nome;
            }
            $i->mesa = $mesa;

            if($tela == 0 || $pTemp->tela_id == $tela){
                array_push($arr, $i);
            }
        }

        // foreach($itensDelivery as $i){


        //     if($i->pedido->estado == 'ap'){
        //         $pTemp = $i->produto->produto;

        //         $i->produto->produto;
        //         $i->comanda = null;

        //         $adicionais = "";
        //         foreach($i->itensAdicionais as $key => $a){

        //             $adicionais .= $a->adicional->nome . ($key < count($i->itensAdicionais)-1 ? " | " : "");
        //         }

        //         $saboresPizza = "";

        //         foreach($i->sabores as $key => $s){
        //             $saboresPizza .= $s->produto->produto->nome . ($key < count($i->sabores)-1 ? " | " : "");
        //         }

        //         $i->tamanhoPizza = $i->tamanho != null ? $i->tamanho->nome() : false;


        //         $i->adicionais = $adicionais;
        //         $i->saboresPizza = $saboresPizza;
        //         $i->data = \Carbon\Carbon::parse($i->created_at)->format('d/m H:i');

        //         $dataPedido = \Carbon\Carbon::parse($i->created_at)->format('Y-m-d H:i:s');
        //         $dataAgora = date('Y-m-d H:i:s');

        //         $date1 = strtotime($dataPedido);
        //         $date2 = strtotime($dataAgora);
                
        //         $dif = (int)($date2 - $date1)/60;
        //         $i->cor = "white";
        //         if($tipoTela != null && $dif > $tipoTela->alerta_amarelo){
        //             $i->cor = 'yellow';
        //         }

        //         if($tipoTela != null && $dif > $tipoTela->alerta_vermelho){
        //             $i->cor = 'red';
        //         }

        //         $i->teste = $dif;
        //         $i->mesa = "";

        //         if($tela == 0 || $pTemp->tela_id == $tela){
        //             array_push($arr, $i);
        //         }
        //     }
        // }
        usort($arr, function($a, $b){
            return strcmp($a->created_at, $b->created_at);
        });
        return response()->json($arr, 200);
    }

    public function concluido(Request $request){
        $ehDelivery = $request->ehDelivery;

        if($ehDelivery == 1){
            $item = ItemPedidoDelivery::find($request->id);
            $item->status = true;

            return response()->json($item->save(), 200);
        }else{
            $item = ItemPedido::find($request->id);
            $item->status = true;

            return response()->json($item->save(), 200);
        }

    }

    public function selecionar(){
        $telas = TelaPedido::all();
        if(sizeof($telas) > 0){
            return view('controleCozinha/selecionar')
            ->with('telas', $telas)
            ->with('title', 'Tipo de controle');
        }else{
            return redirect('/controleCozinha/controle');
        }
    }
}
