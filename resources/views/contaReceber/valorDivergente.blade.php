@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->


				<div class="card card-custom gutter-b example example-compact">
					<div class="card-header">

						<h3 class="card-title">Receber Conta</h3>
					</div>

				</div>
				@csrf

				<input type="hidden" value="{{$valor - $conta->valor_integral}}" id="diferenca" name="">
				<input type="hidden" value="{{$conta->valor_integral}}" id="valor_padrao" name="">

				<div class="row">
					<div class="col-xl-2"></div>
					<div class="col-xl-8">

						<div class="row">
							<div class="col s12">
								@if($conta->venda_id != null)
								<h5>Fornecedor: <strong>{{$conta->venda->cliente->razao_social}}</strong></h5>
								@endif

								<h5>Data de registro: <strong>{{ \Carbon\Carbon::parse($conta->data_registro)->format('d/m/Y')}}</strong></h5>
								<h5>Data de vencimento: <strong>{{ \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y')}}</strong></h5>
								<h5>Valor Integral: <strong class="text-primary">{{ number_format($conta->valor_integral, 2, ',', '.') }}</strong></h5>
								<h5>Valor informado do cliente: <strong class="text-info">{{ number_format($valor, 2, ',', '.') }}</strong></h5>

								@if($conta->valor_integral > $valor)
								<h5>Diferença: <strong class="text-danger">{{ number_format($conta->valor_integral - $valor, 2, ',', '.') }}</strong></h5>
								@else
								<h5>Diferença: <strong class="text-success">{{ number_format($valor - $conta->valor_integral, 2, ',', '.') }}</strong></h5>
								@endif
								<h5>Categoria: <strong>{{$conta->categoria->nome}}</strong></h5>
								<h5>Referencia: <strong>{{$conta->referencia}}</strong></h5>
							</div>
						</div>

						@if(sizeof($contasParaReceber) > 0)
						<p class="text-danger">* Cliente sobre as contas para selecionar e agrupar o recebimento</p>

						<h3>Somatório: <strong id="somatorio">{{$conta->valor_integral}}</strong></h3>
						@endif

						<div class="row">

							@foreach($contasParaReceber as $c)
							<div class="col-lg-6 col-xl-6 col-sm-6">
								<div onclick="adicionarConta('{{$c->id}}', '{{$c->valor_integral}}')" id="div_{{$c->id}}" class="card card-custom gutter-b example example-compact">
									<div class="card-body">

										<h3 class="card-title">Vencimento: <strong>{{ \Carbon\Carbon::parse($c->data_vencimento)->format('d/m/Y')}}</strong></h3><br>
										<h3 class="card-title">Valor: <strong>{{ number_format($c->valor_integral, 2, ',', '.') }}</strong></h3>
									</div>
								</div>
							</div>
							@endforeach
						</div>

						<div class="kt-section kt-section--first">
							<div class="kt-section__body">


							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">

					<div class="row">
						<div class="col-xl-2">

						</div>
						<div class="col-lg-3 col-sm-6 col-md-4">
							<a style="width: 100%" class="btn btn-danger" href="/contasReceber">
								<i class="la la-close"></i>
								<span class="">Cancelar</span>
							</a>
						</div>

						@if($conta->valor_integral > $valor)
						<form method="post" action="/contasReceber/receberComDivergencia">
							@csrf
							<input type="hidden" name="id" value="{{$conta->id}}">
							<input type="hidden" name="valor" value="{{$valor}}">

							<button style="width: 100%;" type="submit" class="btn btn-success">
								<i class="la la-check"></i>
								<span class="">Receber incluindo uma nova conta</span>
							</button>

						</form>
						@endif

						@if($conta->valor_integral > $valor)
						<form method="post" action="/contasReceber/receberSomente">
							@csrf
							<input type="hidden" name="id" value="{{$conta->id}}">
							<input type="hidden" name="valor" value="{{$valor}}">

							<button style="width: 100%;margin-left: 10px;" type="submit" class="btn btn-info">
								<i class="la la-check"></i>
								<span class="">Somente receber</span>
							</button>

						</form>

						@else

						<form method="post" action="/contasReceber/receberComOutros">
							@csrf
							<input type="hidden" name="id" value="{{$conta->id}}">
							<input type="hidden" name="valor" value="{{$valor}}">
							<input type="hidden" id="contas" name="contas" value="">

							<button style="width: 100%;margin-left: 10px;" type="submit" class="btn btn-success">
								<i class="la la-check"></i>
								<span class="">Receber conta(s)</span>
							</button>

						</form>

						@endif


					</div>
				</div>

			</div>
		</div>
	</div>
</div>

@endsection
