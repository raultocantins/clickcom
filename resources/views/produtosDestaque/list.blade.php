@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">


	<div class="card-body">
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-12">

				<a href="/categoriasParaDestaque" class="btn btn-lg btn-success">
					<i class="fa fa-list"></i>Categoria de Destaque
				</a>

				<a href="/produtosDestaque/novoProduto" class="btn btn-lg btn-info">
					<i class="fa fa-plus"></i>Novo Produto em Destaque
				</a>
			</div>
		</div>
		<br>
		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">
			<br>

			<div class="row @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				@foreach($produtos as $p)


				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-4">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{$p->produto->produto->nome}}
							</h3>
							<div class="card-toolbar">

								<a href="/produtosDestaque/delete/{{$p->id}}" class="btn btn-icon btn-circle btn-sm btn-light-danger mr-1"><i class="la la-trash"></i></a>
								
							</div>
						</div>

						<div class="card-body">
							<h5>Data de cadastro: <strong>{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i:s')}}</strong></h5>
						</div>

					</div>

				</div>

				@endforeach

			</div>
		</div>
	</div>
</div>

@endsection