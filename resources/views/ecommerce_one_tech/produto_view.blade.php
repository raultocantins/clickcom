@extends('ecommerce_one_tech.default')
@section('content')

<div class="single_product">
	<div class="container">
		<div class="row">
			<!-- Images -->
			<div class="col-lg-2 order-lg-1 order-2">
				<ul class="image_list">
					
					@foreach($produto->galeria as $g)
					<li data-image="/ecommerce/produtos/{{$g->img}}"><img src="/ecommerce/produtos/{{$g->img}}" alt=""></li>
					@endforeach
				</ul>
			</div>

			<!-- Selected Image -->
			<div class="col-lg-5 order-lg-2 order-1">
				<div class="image_selected"><img src="/ecommerce/produtos/{{$produto->galeria[0]->img}}" alt=""></div>
			</div>

			<!-- Description -->
			<div class="col-lg-5 order-3">
				<div class="product_description">
					<div class="product_category">{{$produto->categoria->nome}}</div>
					<div class="product_name">{{$produto->produto->nome}}</div>
					<br>
					<div class="text-truncate" style="height: 40px;">
						{!! $produto->descricao !!}
					</div>
					<div class="order_info d-flex flex-row">
						<form method="post" action="{{$rota}}/addProduto">
							@csrf
							<input type="hidden" value="{{$default['config']->empresa_id}}" name="empresa_id">
							<div class="clearfix" style="z-index: 1000;">
								<input type="hidden" value="{{$produto->id}}" name="produto_id">


								<!-- Product Quantity -->
								<div class="product_quantity clearfix">
									<span>Quantidade: </span>
									<input name="quantidade" id="quantity_input" type="text" pattern="[0-9]*" value="1">
									<div class="quantity_buttons">
										<div id="quantity_inc_button" class="quantity_inc quantity_control"><i class="fas fa-chevron-up"></i></div>
										<div id="quantity_dec_button" class="quantity_dec quantity_control"><i class="fas fa-chevron-down"></i></div>
									</div>
								</div>
							</div>

							<div class="product_price">R$ {{number_format($produto->valor, 2, ',', '.')}}</div>
							<div class="button_container">
								<button type="submit" class="button cart_button">
									Adicionar ao carrinho
								</button>
								<div class="product_fav"><i class="fas fa-heart"></i></div>
							</div>

						</form>
					</div>
				</div>
			</div>


		</div>
		<br><br>
		<div class="row"> 
			{!! $produto->descricao !!}
		</div>

	</div>
</div>

<div class="viewed">
	<div class="container">
		<div class="row">
			<div class="col">
				<div class="viewed_title_container">
					<h3 class="viewed_title">Produtos da categoria</h3>
					<div class="viewed_nav_container">
						<div class="viewed_nav viewed_prev"><i class="fas fa-chevron-left"></i></div>
						<div class="viewed_nav viewed_next"><i class="fas fa-chevron-right"></i></div>
					</div>
				</div>

				<div class="viewed_slider_container">
					
					<!-- Recently Viewed Slider -->

					<div class="owl-carousel owl-theme viewed_slider">
						
						<!-- Recently Viewed Item -->

						@foreach($categoria->produtos as $p)

						@if($p->id != $produto->id && sizeof($p->galeria) > 0)
						<div class="owl-item">
							<div class="viewed_item discount d-flex flex-column align-items-center justify-content-center text-center">
								<a href="{{$rota}}/{{$p->id}}/verProduto">
									<div class="viewed_image">
										<img src="/ecommerce/produtos/{{$p->galeria[0]->img}}" alt="">
									</div>
									<div class="viewed_content text-center">
										<div class="viewed_price">R$ 
											{{ number_format($p->valor, 2, ',', '.')}}
											<span>
												{{number_format($p->valor + ($p->valor *($p->percentual_desconto_view/100)), 2, ',', '.')}}
											</span>
										</div>
										<div class="viewed_name">
											<a href="#">
												{{$p->produto->nome}}
											</a>
										</div>
									</div>
									<ul class="item_marks">
										<!-- <li class="item_mark item_discount">-25%</li> -->

										@if($p->isNovo())
										<li class="item_mark item_new">Novo</li>
										@endif

									</ul>
								</a>

							</div>
						</div>
						@endif
						@endforeach

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection