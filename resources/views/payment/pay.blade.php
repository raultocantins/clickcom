@extends('default.layout')
@section('content')

<style type="text/css">
	.card-stretch:hover{
		cursor: pointer;
	}

	.input-group-append:hover{
		cursor: pointer;
	}
</style>
<div class="card card-custom gutter-b">

	<div class="card-body @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">
		<div class="content d-flex flex-column flex-column-fluid" id="kt_content" >
			<form method="post" action="/financeiro/pay">
				@csrf
				<div class="row">
					<div class="col-lg-12">

						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<h3 class="card-title">
									Atribuir pagamento: <strong class="text-info">{{$plano->empresa->nome}}</strong>
								</h3>
							</div>

							<div class="card-body">
								<div class="row">
									<div class="col-lg-6">
										<h4>Data de criação: <strong class="text-info">
											{{ \Carbon\Carbon::parse($plano->created_at)->format('d/m/Y H:i:s')}}
										</strong></h4>

										<h4>Intervalo: <strong class="text-info">{{$plano->plano->intervalo_dias}} dias</strong>
										</h4>
									</div>

									<div class="col-lg-6">
										<h4>Valor plano: <strong class="text-info">{{number_format($plano->plano->valor, 2, ',', '.')}}</strong>
										</h4>


									</div>

								</div>

								<div class="col-lg-12">
									<div class="row">

										<div class="form-group validated col-lg-3">
											<label class="col-form-label">Valor</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('valor')) is-invalid @endif money" name="valor" value="{{$plano->plano->valor}}">
												@if($errors->has('valor'))
												<div class="invalid-feedback">
													{{ $errors->first('valor') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-lg-3">
											<label class="col-form-label">Forma de pagamento</label>
											<div class="">
												<select class="custom-select" name="forma_pagamento">
													<option value="Boleto">Boleto</option>
													<option value="Pix">Pix</option>
													<option value="Dinheiro">Dinheiro</option>
													<option value="Cartão">Cartão</option>
												</select>
											</div>
										</div>

										<input type="hidden" value="{{$plano->id}}" name="plano_id">
									</div>
								</div>
							</div>

							<div class="card-footer">

								<div class="row">
									<div class="col-xl-2">

									</div>
									<div class="col-lg-3 col-sm-6 col-md-4">
										<a style="width: 100%" class="btn btn-danger" href="/clientes">
											<i class="la la-close"></i>
											<span class="">Cancelar</span>
										</a>
									</div>
									<div class="col-lg-3 col-sm-6 col-md-4">
										<button style="width: 100%" type="submit" class="btn btn-success">
											<i class="la la-check"></i>
											<span class="">Salvar</span>
										</button>
									</div>

								</div>
							</div>



						</div>
					</div>

				</div>
			</form>
		</div>
	</div>
</div>



@endsection
