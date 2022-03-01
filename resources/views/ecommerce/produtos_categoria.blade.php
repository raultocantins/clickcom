@extends('ecommerce.default')
@section('content')

<section class="from-blog spad">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="section-title from-blog__title">
					@if($categoria != null)
					<h2>Produtos da categoria: <strong style="color: var(--color-default);">{{$categoria->nome}}</strong></h2>
					@else
					<h2>Pesquisa produto: <strong style="color: var(--color-default);">{{$pesquisa}}</strong></h2>
					@endif
				</div>
			</div>
		</div>
		@if(sizeof($produtos) > 0)
		<div class="row">
			@foreach($produtos as $p)
			@if(sizeof($p->galeria) > 0)

			<div class="col-lg-4 col-md-4 col-sm-6">
				<div class="blog__item">
					<div class="blog__item__pic">
						<a href="{{$rota}}/{{$p->id}}/verProduto">
							<img style="height: 300px; width: 60%;" src="/ecommerce/produtos/{{$p->galeria[0]->img}}" alt="">
						</a>
					</div>
					<div class="blog__item__text">

						<h5><a href="{{$rota}}/{{$p->id}}/verProduto">{{$p->produto->nome}}</a></h5>
						<div class="text-truncate" style="height: 85px;">
							{!! $p->descricao !!}
						</div>
						<h4 class="text-danger">
							R$ {{ number_format($p->valor, 2, ',', '.')}}
						</h4>
						@if($p->valor_pix > 0)
						<span style="display: block; font-size: 14px; font-weight: bold;" class="text-info"> R$ {{number_format($p->valor_pix,2,',', '.')}} -{{number_format($default['config']->desconto_padrao_pix)}}% no PIX</span>
						@endif

						@if($p->valor_cartao > 0)
						<span style="display: block; font-size: 14px; font-weight: bold;" class="text-primary"> R$ {{number_format($p->valor_cartao,2,',', '.')}} -{{number_format($default['config']->desconto_padrao_cartao)}}% no Cartão</span>
						@endif

						@if($p->valor_boleto > 0)
						<span style="display: block; font-size: 14px; font-weight: bold;" class="text-dark"> R$ {{number_format($p->valor_boleto,2,',', '.')}} -{{number_format($default['config']->desconto_padrao_boleto)}}% no Boleto</span>
						@endif

						<br>

						<a href="{{$rota}}/{{$p->id}}/verProduto" class="blog__btn">VER MAIS <span class="arrow_right"></span></a>
					</div>
				</div>
			</div>
			@else
			@php 
			$semImagem = 1;
			@endphp
			@endif

			@endforeach

		</div>

		@else
		<h2>Categoria sem produtos cadastrados <strong class="text-danger">:(</strong></h2>
		@endif

		@if(isset($semImagem))
		<h2>Categoria sem produtos cadastrados <strong class="text-danger">:(</strong></h2>
		@endif
	</div>
</section>

@endsection	
