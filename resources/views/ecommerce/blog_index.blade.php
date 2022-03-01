@extends('ecommerce.default')
@section('content')
<style type="text/css">
	.owl-nav{
		display: none;
	}
</style>

<section class="blog">
	<div class="container">
		<div class="row">
			<div class="col-lg-4 col-md-5">
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

			<div class="col-lg-8 col-md-7">
				<div class="row">
					
					@foreach($posts as $p)
					<div class="col-lg-6 col-md-6 col-sm-6">
						<div class="blog__item">
							<div class="blog__item__pic">
								<img height="300" src="/ecommerce/posts/{{$p->img}}" alt="">
							</div>
							<div class="blog__item__text">
								<ul>
									<li><i class="fa fa-calendar-o"></i> {{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y')}}</li>
									<!-- <li><i class="fa fa-comment-o"></i> 5</li> -->
								</ul>
								<h5><a href="#">{{$p->titulo}}</a></h5>
								<div class="text-truncate" style="height: 85px;">
									{!! $p->texto !!}
								</div>
								<br>
								<a href="{{$rota}}/{{$p->id}}/verPost" class="blog__btn">VER MAIS <span class="arrow_right"></span></a>
							</div>
						</div>
					</div>
					@endforeach

				</div>
			</div>
		</div>
	</div>
</section>
<!-- Blog Section End -->

@endsection	
