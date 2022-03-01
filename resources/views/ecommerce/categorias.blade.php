@extends('ecommerce.default')
@section('content')

<section class="from-blog spad">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="section-title from-blog__title">
					<h2>Categorias</h2>
				</div>
			</div>
		</div>
		<div class="row">
			@foreach($categorias as $c)
			<div class="col-lg-4 col-md-4 col-sm-6">
				<div class="blog__item">
					<div class="blog__item__pic">
						<a href="{{$rota}}/{{$c->id}}/categorias">
							<img style="height: 300px; width: 60%;" src="/ecommerce/categorias/{{$c->img}}" alt="">
						</a>
					</div>
					<div class="blog__item__text">

						<h5><a href="{{$rota}}/{{$c->id}}/categorias">{{$c->nome}}</a></h5>
					</div>
				</div>
			</div>
			@endforeach

		</div>
	</div>
</section>

@endsection	
