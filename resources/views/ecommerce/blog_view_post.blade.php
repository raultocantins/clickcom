@extends('ecommerce.default')
@section('content')
<style type="text/css">
	.owl-nav{
		display: none;
	}
</style>

<section class="blog-details">
	<div class="container">
		<div class="row">
			<div class="col-lg-4 col-md-5 order-md-1 order-2">
				<div class="blog__sidebar">
					
					<div class="blog__sidebar__item">
						<h4>Categorias</h4>
						<ul>
							@foreach($categoriasPost as $c)
							<li><a href="{{$rota}}/{{$c->id}}/posts">{{$c->nome}} ({{sizeof($c->posts)}})</a></li>
							@endforeach
						</ul>
					</div>
					<div class="blog__sidebar__item">
						<h4>Posts recentes</h4>
						<div class="blog__sidebar__recent">
							@foreach($postsRecentes as $p)
							<a href="{{$rota}}/{{$p->id}}/verPost" class="blog__sidebar__recent__item">
								<div class="blog__sidebar__recent__item__pic">
									<img height="50" width="50" src="/ecommerce/posts/{{$p->img}}" alt="">
								</div>
								<div class="blog__sidebar__recent__item__text">
									<h6>{{$p->titulo}}</h6>
									<span>{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i')}}</span>
								</div>
							</a>
							@endforeach
						</div>
					</div>

				</div>
			</div>
			<div class="col-lg-8 col-md-7 order-md-1 order-1">
				<div class="blog__details__text">
					<img height="500" src="/ecommerce/posts/{{$post->img}}" alt="">
					{!! $post->texto !!}
				</div>
				<div class="blog__details__content">
					<div class="row">
						<div class="col-lg-6">
							<div class="blog__details__author">
								<div class="blog__details__author__pic">
									<img src="/ecommerce/autores/{{$post->autor->img}}" alt="">
								</div>
								<div class="blog__details__author__text">
									<h6>{{$post->autor->nome}}</h6>
									<span>{{$post->autor->tipo}}</span>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="blog__details__widget">
								<ul>
									<li><span>Categoria:</span> {{$post->categoria->nome}}</li>
									@if($post->tags != "")
									<li><span>Tags:</span> {{$post->tags}}</li>
									@endif
								</ul>
								<div class="blog__details__social">
									@if($default['config']['link_facebook'] != "")
									<a target="_blank" href="{{$default['config']['link_facebook']}}"><i class="fa fa-facebook"></i></a>
									@endif
									@if($default['config']['link_twitter'] != "")
									<a target="_blank" href="{{$default['config']['link_twitter']}}"><i class="fa fa-twitter"></i></a>
									@endif
									@if($default['config']['link_instagram'] != "")
									<a target="_blank" href="{{$default['config']['link_instagram']}}"><i class="fa fa-instagram"></i></a>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Blog Section End -->

@endsection	
