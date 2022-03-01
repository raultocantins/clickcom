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
                                <th>Adicionado em</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(sizeof($curtidas) > 0)
                            @foreach($curtidas as $i)
                            <tr>
                                <td class="shoping__cart__item">
                                    <a href="{{$rota}}/{{$i->produto->id}}/verProduto">
                                        <img height="40" src="/ecommerce/produtos/{{$i->produto->galeria[0]->img}}" alt="">
                                    </a>
                                    <h5>
                                        <a style="color: #000;" href="{{$rota}}/{{$i->produto->id}}/verProduto">
                                            {{$i->produto->produto->nome}}
                                        </a>
                                    </h5>
                                </td>
                                <td class="shoping__cart__quantity">
                                    R$ {{number_format($i->produto->valor, 2, ',', '.')}}
                                </td>
                                
                                <td class="shoping__cart__quantity">
                                    {{ \Carbon\Carbon::parse($i->created_at)->format('d/m/Y H:i:s')}}
                                </td>

                                <td class="shoping__cart__quantity">
                                    <a class="btn primary-btn" href="{{$rota}}/{{$i->produto->id}}/verProduto">
                                        <i class="fa fa-shopping-cart"></i> Adicionar
                                    </a>
                                </td>
                                
                            </tr>
                            @endforeach

                            @else


                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>

@endsection 
