@extends('multi_delivery.default')
@section('content')

<div class="testimonial">
	<div class="container">
		<div class="owl-carousel testimonials-carousel">
			@foreach($categorias as $c)
			<div class="testimonial-item" style="height: 180px;">
				<div class="testimonial-img">
					<img src="/categorias_delivery/{{$c->img}}" alt="Image">
				</div>
				
				<h2>{{$c->nome}}</h2>
			</div>
			@endforeach
		</div>
	</div>
</div>
<div class="menu">
	<div class="container">
		<div class="section-header text-center">
			<p>Menu</p>
			<h2>Produtos em Destaque</h2>
		</div>
		<div class="menu-tab">
			<ul class="nav nav-pills justify-content-center">
				@foreach($categoriasMaster as $key => $c)
				<li class="nav-item">
					<a class="nav-link @if($key == 0) active @endif" data-toggle="pill" href="#{{$c->nome}}">{{$c->nome}}</a>
				</li>
				@endforeach
			</ul>
			<div class="tab-content">
				@foreach($categoriasMaster as $key => $c)

				<div id="{{$c->nome}}" class="container tab-pane @if($key == 0) active @endif">
					<div class="row">
						<div class="col-lg-7 col-md-12">

							@foreach($c->produtos as $p)

							<div class="menu-item">
								<div class="menu-img">
									@if(sizeof($p->produto->galeria) > 0)
									<img src="/imagens_produtos/{{$p->galeria[0]->path}}" alt="image">
									@else
									<img src="imgs/no_image.png" alt="image">
									@endif

								</div>
								<div class="menu-text">
									<h3><span>{{$p->produto->produto->nome}}</span> <strong>R$ {{number_format($p->produto->valor, 2, ',', '.')}}</strong></h3>
									<p>
										{{$p->produto->descricao}}
									</p>
								</div>
							</div>
							@endforeach
							
						</div>
						<div class="col-lg-5 d-none d-lg-block">
							<img src="/categorias_delivery/{{$c->img}}" alt="Image">
						</div>
					</div>
				</div>
				@endforeach
				
			</div>
		</div>
	</div>
</div>

@endsection 
