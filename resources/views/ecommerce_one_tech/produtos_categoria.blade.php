@extends('ecommerce_one_tech.default')
@section('content')

<style type="text/css">
	.img-prod{
		width: 100px;
	}
</style>
<div class="super_container">

	<div class="shop">
		<div class="container">
			<div class="row">
				<div class="col-lg-3">

					<!-- Shop Sidebar -->
					<div class="shop_sidebar">
						<div class="sidebar_section">
							<div class="sidebar_title">Categorias</div>
							<ul class="sidebar_categories">

								@foreach($default['categorias'] as $c)
								<li><a href="{{$rota}}/{{$c->id}}/categorias">{{$c->nome}}</a></li>
								@endforeach
							</ul>
						</div>
						<div class="sidebar_section filter_by_section">
							<div class="sidebar_title">Filtrar por</div>
							<div class="sidebar_subtitle">Preço</div>
							<div class="filter_price">
								<div id="slider-range" class="slider_range"></div>
								<p>Deslize: </p>
								<p><input type="text" id="amount" class="amount" readonly style="border:0; font-weight:bold;"></p>
							</div>
						</div>

						
					</div>

				</div>

				<div class="col-lg-9">

					<!-- Shop Content -->

					<div class="shop_content">
						<div class="shop_bar clearfix">
							@if($categoria)
							<div class="shop_product_count"><span>{{sizeof($produtos)}}</span> produtos encontrados</div>
							@endif
							<div class="shop_sorting">
								<span>Ordenar por:</span>
								<ul>
									<li>
										<span class="sorting_text">Nome<i class="fas fa-chevron-down"></span></i>
										<ul>
											<li class="shop_sorting_button" data-isotope-option='{ "sortBy": "name" }'>Nome</li>
											<li class="shop_sorting_button"data-isotope-option='{ "sortBy": "price" }'>Preço</li>
										</ul>
									</li>
								</ul>
							</div>
						</div>

						<div class="product_grid">
							<div class="product_grid_border"></div>

							<!-- Product Item -->
							@php
							$max = 0;

							@endphp
							@foreach($produtos as $p)
							@if(sizeof($p->galeria) > 0)
							<div class="product_item is_new">
								<div class="product_border"></div>
								<a href="{{$rota}}/{{$p->id}}/verProduto"><div class="product_image d-flex flex-column align-items-center justify-content-center"><img class="img-prod" src="/ecommerce/produtos/{{$p->galeria[0]->img}}" alt=""></div>
								</a>
								<div class="product_content">
									<div class="product_price">{{number_format($p->valor, 2, ',', '.')}}</div>
									<div class="product_name"><div><a href="{{$rota}}/{{$p->id}}/verProduto" tabindex="0">
										{{$p->produto->nome}}
									</a></div></div>
								</div>
								
								<a href="{{$rota}}/{{$p->id}}/curtirProduto"><div class="product_fav">
									<i class="fas fa-heart"></i>
								</div></a>
								<ul class="product_marks">
									<!-- <li class="product_mark product_discount">-25%</li> -->
									@if($p->isNovo())
									<li class="product_mark product_new">novo</li>
									@endif
								</ul>

								@php
								if($p->valor > $max) $max = $p->valor;
								@endphp

							</div>
							@endif
							@endforeach

						</div>

						<input type="hidden" id="max" value="{{$max+200}}" name="">

					</div>

				</div>
			</div>
		</div>
	</div>
</div>

@endsection