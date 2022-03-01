@extends('ecommerce.default')
@section('content')

<style type="text/css">
    .end{
        font-size: 25px;
    }
</style>

<section class="checkout" style="margin-top: -70px;">
    <div class="container">

        <div class="checkout__form">
            <h4>Selecione o endereço de entrega </h4>

            <form method="post" action="{{$rota}}/pagamento">
                @csrf

                <input type="hidden" value="{{$default['config']->empresa_id}}" name="empresa_id">
                <input type="hidden" value="{{$default['carrinho']->id}}" name="pedido_id">

                <div class="row">
                    <div class="col-lg-8 col-md-6">
                        <div class="row">
                            <div class="col-12" style="margin-bottom: 10px;">
                                <button type="button" data-toggle="modal" data-target="#modal-endereco" class="btn btn-success">
                                    <i class="fa fa-plus"></i> Novo Endereço
                                </button>
                            </div>

                            @foreach($enderecos as $p)
                            @if($p->preco > 0)
                            <div class="col-12" style="border-bottom: 1px solid #000;margin-bottom: 10px; margin-top: 10px;">

                                <label class="end">{{$p->rua}}, {{$p->numero}} - {{$p->bairro}}</label>
                                <p>{{$p->cidade}} ({{$p->uf}}) {{$p->cep}}</p>
                                <p>{{$p->complemento}}</p>
                                
                                @if($p->preco_sedex != '0,00')
                                <input @if($tipoFrete == 'sedex') checked @endif id="sedex" type="radio" value="{{$p}}" name="endereco"> SEDEX R$ {{$p->preco_sedex}} - entrega em {{$p->prazo_sedex}} dias úteis
                                <br>
                                @endif
                                @if($p->preco != '0,00')
                                <input @if($tipoFrete == 'pac') checked @endif id="pac" type="radio" value="{{$p}}" name="endereco"> PAC R$ {{$p->preco}} - entrega em {{$p->prazo}} dias úteis
                                <br>
                                @endif

                                @if($p->habilitar_retirada)
                                <input @if($tipoFrete == 'retirada') checked @endif id="retirada" type="radio" value="{{$p}}" name="endereco"> IREI RETIRAR NA LOJA
                                <br><br>
                                @endif

                                @if($p->frete_gratis)
                                <input id="gratis" type="radio" value="{{$p}}" name="endereco"> Frete grátis - entrega em {{$p->prazo}} dias úteis
                                <br><br>
                                @else
                                <br>
                                @endif

                                
                            </div>
                            @endif
                            @endforeach

                            <input type="hidden" id="tipo" name="tipo" value="">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="checkout__order">
                            <h4>Seu Pedido</h4>
                            <div class="checkout__order__products">Produtos <span>{{number_format($default['carrinho']->somaItens(), 2, ',', '.')}}</span></div>

                            <div class="checkout__order__subtotal">Frete <span id="vFrete">R$ {{number_format($default['carrinho']->valor_frete, 2, ',', '.')}}</span></div>
                            <div class="checkout__order__total">Total <span id="vTotal">R$ {{ $default['carrinho'] != null ? number_format($default['carrinho']->somaItens() + $default['carrinho']->valor_frete, 2, ',', '.') : '0,00'}}</span></div>

                            <input type="hidden" id="total" value="{{$default['carrinho']->somaItens()}}" name="">
                            <button type="submit" class="site-btn">Pronto</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<div class="modal fade" id="modal-endereco" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="/ecommerceSaveEndereco">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo">Cadastrar Endereço</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" value="{{ $cliente->id }}" id="id" name="id">
                        <input type="hidden" value="0" id="endereco_id" name="endereco_id">
                        <div class="col-lg-8 col-12">
                            <div class="checkout__input">
                                <label>Rua</label>
                                <input required id="rua" name="rua" value="" type="text">
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <div class="checkout__input">
                                <label>Nº</label>
                                <input required id="numero" name="numero" value="" type="text">
                            </div>
                        </div>

                        <div class="col-lg-6 col-12">
                            <div class="checkout__input">
                                <label>Bairro</label>
                                <input required id="bairro" name="bairro" value="" type="text">
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="checkout__input">
                                <label>CEP</label>
                                <input id="cep" data-mask="00000-000" data-mask-reverse="true" required name="cep" value="" type="text">
                            </div>
                        </div>

                        <div class="col-lg-8 col-12">
                            <div class="checkout__input">
                                <label>Cidade</label>
                                <input id="cidade" required name="cidade" value="" type="text">
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <div class="checkout__input">
                                <label>UF</label>
                                <select id="uf" required class="custom-select" style="height: 47px;" name="uf">
                                    <option></option>
                                    @foreach(App\Models\EnderecoEcommerce::estados() as $u)
                                    <option value="{{$u}}">{{$u}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="checkout__input">
                                <label>Complemento</label>
                                <input id="complemento" name="complemento" value="" type="text">
                            </div>
                        </div>

                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('javascript')
<script type="text/javascript">
    var TOTAL= 0;
    var FRETE= 0;
    $(function () {
        TOTAL = $('#total').val();
        radioClick();
    });

    function radioClick(){
        let sedex = $('#sedex').is(':checked')
        let pac = $('#pac').is(':checked')
        if(sedex || pac){
            let v = null;
            let id = null;
            if(sedex){
                v = $('#sedex').val()
                id = 'sedex'
            }

            if(pac){
                v = $('#pac').val()
                id = 'pac'
            }
            v = JSON.parse(v)

            $('#tipo').val(id)
            if(id == 'pac'){
                FRETE = v.preco
            }else if(id == 'sedex'){
                FRETE = v.preco_sedex
            }else{
                FRETE = '0';
            }

            $('#vFrete').html('R$ ' + formatReal(FRETE))

            somaTotal();
        }
    }
    $('input:radio').change((target) => {

        let v = target.target.value
        let id = target.target.id

        v = JSON.parse(v)

        $('#tipo').val(id)
        if(id == 'pac'){
            FRETE = v.preco
        }else if(id == 'sedex'){
            FRETE = v.preco_sedex
        }else if(id == 'retirada'){
            FRETE = '0'
        }else{
            FRETE = '0';
        }

        $('#vFrete').html('R$ ' + formatReal(FRETE))

        somaTotal();
    });

    function somaTotal(){
        let f = FRETE.replace(',', '.');

        f = parseFloat(f);
        let t = parseFloat(TOTAL)
        console.log(t + f)

        $('#vTotal').html(formatReal(t + f))

    }

    function formatReal(v){
        return v.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
    }
</script>
@endsection 

@endsection 
