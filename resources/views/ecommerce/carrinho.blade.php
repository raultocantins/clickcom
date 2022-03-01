@extends('ecommerce.default')
@section('content')

<section class="shoping-cart">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
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
                            @if($default['carrinho'] != null)
                            @foreach($default['carrinho']->itens as $i)
                            <tr>
                                <td class="shoping__cart__item">
                                    <img height="40" src="/ecommerce/produtos/{{$i->produto->galeria[0]->img}}" alt="">
                                    <h5>
                                        {{$i->produto->produto->nome}}

                                        @if($i->produto->produto->grade)
                                        | {{$i->produto->produto->str_grade}}
                                        @endif
                                    </h5>
                                </td>
                                <td class="shoping__cart__price">
                                    R$ {{number_format($i->produto->valor, 2, ',', '.')}}
                                </td>
                                <td class="shoping__cart__quantity">
                                    <div class="quantity">
                                        <div class="pro-qty">
                                            <input id="{{$i->id}}" class="qtd" type="number" value="{{$i->quantidade}}">
                                        </div>
                                    </div>
                                </td>
                                <td class="shoping__cart__total">
                                    R$ {{number_format($i->quantidade*$i->produto->valor, 2, ',', '.')}}
                                </td>
                                <td class="shoping__cart__item__close">
                                    <a href="{{$rota}}/{{$i->id}}/deleteItemCarrinho"><span class="icon_close"></span></a>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="shoping__cart__btns">
                    <a href="{{$rota}}" class="primary-btn cart-btn">CONTINUAR COMPRANDO</a>
                    <a href="{{$rota}}/carrinho" class="primary-btn cart-btn cart-btn-right"><span class="icon_loading"></span>
                    ATUALIZAR CARRINHO</a>
                </div>
            </div>
            <input type="hidden" value="{{csrf_token()}}" id="token">
            <div class="col-lg-6">
                <div class="shoping__continue">
                    <div class="shoping__discount">
                        <h5>Frete</h5>
                        <form action="#">
                            @if($default['carrinho'] != null)
                            <input type="hidden" id="pedido_id" name="" value="{{$default['carrinho']->id}}">
                            @endif
                            <input id="cep" data-mask="00000-000" data-mask-reverse="true" type="text" placeholder="CEP">
                            <button id="btn-calcular-frete" type="button" class="site-btn">CALCULAR</button>
                            <div style="display: none" class="spinner-border text-secondary" role="status">
                                <span class="sr-only"></span>
                            </div>
                        </form>
                        <br>
                        <div class="frete">

                        </div>

                    </div>

                </div>
            </div>

            <input type="hidden" value="{{ $default['carrinho'] != null ? $default['carrinho']->somaItens() : 0}}" id="soma_hidden" name="">
            <div class="col-lg-6">
                <div class="shoping__checkout">
                    <h5>CARRINHO TOTAL</h5>
                    <ul>
                        <li>Subtotal 
                            <span>
                                R$ {{ $default['carrinho'] != null ? number_format($default['carrinho']->somaItens(), 2, ',', '.') : '0,00'}}
                            </span>
                        </li>
                        <li>Total 
                            <span id="total">
                                R$ {{ $default['carrinho'] != null ? number_format($default['carrinho']->somaItens(), 2, ',', '.') : '0,00'}}
                            </span>
                        </li>
                    </ul>

                    <form method="get" action="{{$rota}}/checkout">
                        <input type="hidden" id="tp_frete" value="" name="tp_frete">
                        <button class="btn primary-btn">CONTINUAR PARA PAGAMENTO</button>
                    </form>
                    <!-- <a href="{{$rota}}/checkout" class="primary-btn">CONTINUAR PARA PAGAMENTO</a> -->
                </div>
            </div>
        </div>
    </div>
</section>

@endsection 
