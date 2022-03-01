@extends('ecommerce.default')
@section('content')

<style type="text/css">
	.box-desconto{
		margin-top: 3px;
		padding: 10px;
		background: #F5F5F5;
	}
	.box-desconto span{
		font-size: 20px;
	}
	.box-desconto img{
		height: 30px;
		margin-right: 10px;
	}
</style>

<section class="product-details spad">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 col-md-6">
				<div class="product__details__pic">
					<div class="product__details__pic__item">
						<img class="product__details__pic__item--large"
						src="/ecommerce/produtos/{{$produto->galeria[0]->img}}" alt="">
					</div>
					<div class="product__details__pic__slider owl-carousel">
						@foreach($produto->galeria as $g)
						<img data-imgbigurl="/ecommerce/produtos/{{$g->img}}" alt="" src="/ecommerce/produtos/{{$g->img}}">
						@endforeach
					</div>
				</div>
			</div>
			<div class="col-lg-6 col-md-6">
				<div class="product__details__text">
					<h3>{{$produto->produto->nome}}</h3>
					
					<div class="product__details__price">
						R$ {{number_format($produto->valor, 2, ',', '.')}}
					</div>
					
					<div class="text-truncate" style="height: 85px;">
						{!! $produto->descricao !!}
					</div>
					<form method="post" action="{{$rota}}/addProduto">
						@csrf
						<input type="hidden" value="{{$default['config']->empresa_id}}" name="empresa_id">
						<input type="hidden" value="{{$produto->id}}" name="produto_id">
						<div class="product__details__quantity">
							<div class="quantity">
								<div class="pro-qty">
									<input name="quantidade" type="text" value="1">
								</div>
							</div>
						</div>
						<button class="btn primary-btn">ADICIONAR AO CARRINHO</button>
						<a href="{{$rota}}/{{$produto->id}}/curtirProduto" class="heart-icon @if($curtida) text-danger @endif">
							<span class="icon_heart_alt"></span>
						</a>
					</form>

					@if($produto->valor_pix > 0)
					<div class="box-desconto">
						<span><img src="/imgs/pix.png" />R$ {{number_format($produto->valor_pix,2,',', '.')}} -{{number_format($default['config']->desconto_padrao_pix)}}% no PIX</span>
					</div>
					@endif

					@if($produto->valor_boleto > 0)
					<div class="box-desconto">
						<span><img src="/imgs/boleto.png" />R$ {{number_format($produto->valor_boleto,2,',', '.')}} -{{number_format($default['config']->desconto_padrao_boleto)}}% no Boleto</span>
					</div>
					@endif

					@if($produto->valor_cartao > 0)
					<div class="box-desconto">
						<span><img src="/imgs/cartao.png" />R$ {{number_format($produto->valor_cartao,2,',', '.')}} -{{number_format($default['config']->desconto_padrao_cartao)}}% no Cartão</span>
					</div>
					@endif

					<ul>
						<li><b>Disponibilidade</b> <span>Em estoque</span></li>
						<li><b>Entrega</b> <span>Imediata</span></li>
					</ul>

				</div>
			</div>
			<div class="col-lg-12">
				<div class="product__details__tab">
					
					<div class="tab-pane active" id="tabs-1" role="tabpanel">
						<div class="product__details__tab__desc">
							<h6>Informação do produto</h6>
							{!! $produto->descricao !!}
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>
</section>

@endsection	
