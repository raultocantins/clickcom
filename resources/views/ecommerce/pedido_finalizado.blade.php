@extends('ecommerce.default')
@section('content')

<section class="shoping-cart" style="margin-top: -70px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">

                <input type="hidden" value="{{$pedido->transacao_id}}" id="transacao_id" name="">
                <input type="hidden" value="{{$pedido->status_pagamento}}" id="status" name="">
                @if($pedido->status != 2)
                <div class="">
                    <div class="alert alert-custom alert-success fade show" role="alert">

                        <div class="alert-text"><i class="fa fa-check"></i> 

                            @if($pedido->forma_pagamento == "Boleto")
                            Obrigado por realizar o pedido, aguardamos o pagamento do seu boleto 😊

                            @elseif($pedido->forma_pagamento == "Pix")
                            Obrigado por realizar o pedido, abaixo esta o Qrcode e copia e cola do seu pix 😊
                            @else
                            Obrigado por realizar o pedido 😊
                            @endif

                        </div>
                    </div>
                </div>


                @if($pedido->forma_pagamento == 'Pix')
                <div class="row">
                    <div class="col-lg-12">

                        <div class="col-lg-4 offset-lg-4">
                            <img style="width: 400px; height: 400px;" src="data:image/jpeg;base64,{{$pedido->qr_code_base64}}"/>
                        </div>                  
                    </div>  
                    <div class="col-lg-12">

                        <div class="col-lg-11 offset-lg-1">

                            <div class="input-group">
                                <input type="text" class="form-control" value="{{$pedido->qr_code}}" id="qrcode_input" />

                                <div class="input-group-append">
                                    <span class="input-group-text">

                                        <i onclick="copy()" class="fa fa-copy">
                                        </i>

                                    </span>
                                </div>
                            </div>

                        </div>              
                    </div>              
                </div>

                <div class="row">


                </div>
                @endif

                @elseif($pedido->status == 2)

                <div class="">
                    <div class="alert alert-custom alert-success fade show" role="alert">

                        <div class="alert-text"><i class="fa fa-check"></i> 

                           
                            @if($pedido->forma_pagamento == "Pix")

                            Que ótimo seu pix foi aprovado, obrigado pela confiança 😊

                            @endif

                        </div>
                    </div>
                </div>

                @endif

                <h2>Detalhes do seu pedido</h2>
                <div class="shoping__cart__table">
                    <table>
                        <thead>
                            <tr>
                                <th class="shoping__product">Produto</th>
                                <th>Valor</th>
                                <th>Quantidade</th>
                                <th>SubTotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedido->itens as $i)
                            <tr>
                                <td class="shoping__cart__item">
                                    <img height="40" src="/ecommerce/produtos/{{$i->produto->galeria[0]->img}}" alt="">
                                    <h5>{{$i->produto->produto->nome}}</h5>
                                </td>
                                <td class="shoping__cart__price">
                                    R$ {{number_format($i->produto->valor, 2, ',', '.')}}
                                </td>
                                <td class="shoping__cart__quantity">
                                    <strong>{{$i->quantidade}}</strong>
                                </td>
                                <td class="shoping__cart__total">
                                    R$ {{number_format($i->quantidade*$i->produto->valor, 2, ',', '.')}}
                                </td>
                                <td class="shoping__cart__item__close">

                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="shoping__cart__btns">

                </div>
            </div>
            <input type="hidden" value="{{csrf_token()}}" id="token">


            <div class="col-lg-6">
                <div class="shoping__checkout">
                    <h5>Endereço</h5>
                    <ul>
                        <li>

                            <h6>Rua: 
                                <strong>
                                    {{ $pedido->endereco->rua }}, {{ $pedido->endereco->numero }}
                                </strong>
                            </h6>
                            <h6>Complemento: 
                                <strong>
                                    {{ $pedido->endereco->complemento }}
                                </strong>
                            </h6>
                            <h6>Bairro: 
                                <strong>
                                    {{ $pedido->endereco->bairro }}
                                </strong>
                            </h6>
                            <h6>CEP: 
                                <strong>
                                    {{ $pedido->endereco->cep }}
                                </strong>
                            </h6>

                            <h6>Cidade: 
                                <strong>
                                    {{ $pedido->endereco->cidade }} ({{ $pedido->endereco->uf }})
                                </strong>
                            </h6>
                        </li>

                    </ul>

                </div>
            </div>

            <div class="col-lg-6">
                <div class="shoping__checkout">
                    <h5>TOTAL</h5>
                    <ul>
                        <li>Itens 
                            <span class="text-info">
                                R$ {{ number_format($pedido->somaItens(), 2, ',', '.') }}
                            </span>
                        </li>

                        <li>Frete 
                            <span>
                                R$ {{ number_format($pedido->valor_frete, 2, ',', '.') }}
                            </span>
                        </li>

                        <li>Total 
                            <span id="total" class="text-success">
                                R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}
                            </span>
                        </li>

                        <li>
                            @if($pedido->link_boleto != "")
                            <a target="_blank" class="btn btn-lg btn-info" href="{{$pedido->link_boleto}}">
                                <i class="fa fa-print"></i> Imprimir Boleto
                            </a>
                            @endif
                        </li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
</section>

@section('javascript')
<script type="text/javascript">
    @if($pedido->link_boleto != "")
    window.open('{{$pedido->link_boleto}}')
    @endif


    function copy(){

        const inputTest = document.querySelector("#qrcode_input");

        inputTest.select();
        document.execCommand('copy');

        swal("", "Código pix copado!!", "success")
    }

    var prot = window.location.protocol;
    var host = window.location.host;
    var pathname = window.location.pathname;
    let path = prot + "//" + host;

    if($('#status').val() != "approved"){
        setInterval(() => {
            let transacao_id = $('#transacao_id').val();

            $.get(path+'/ecommercePay/consulta/'+transacao_id)
            .done((success) => {

                if(success == "approved"){
                    location.reload()
                }
            })
            .fail((err) => {
                console.log(err)
            })
        }, 1500)
    }
</script>

@endsection

@endsection 
