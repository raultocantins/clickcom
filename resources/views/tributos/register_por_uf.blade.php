@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->
				<form @isset($tributacao) action="/percentualuf/update" @else action="/percentualuf/save" @endif method="post">
					@csrf
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">Tributação para <strong class="text-info" style="margin-left: 3px;">{{$uf}}</strong>
								<a style="margin-left: 10px;" href="/percentualuf/verProdutos/{{$uf}}" class="btn btn-info">
									<i class="la la-boxes"></i>
									Ver Produtos
								</a>
							</h3>

						</div>

					</div>

					<input type="hidden" value="{{$uf}}" name="uf">

					<div class="row">
						<div class="col-12">
							<p class="text-danger">>> O percentual será registrado para todos os seus produtos</p>

							<div class="form-group validated col-sm-3 col-lg-3">
								<label class="col-form-label">%ICMS</label>
								<div class="">
									<input id="percentual_icms" type="text" class="form-control @if($errors->has('percentual_icms')) is-invalid @endif" name="percentual_icms" value="{{{ isset($tributacao) ? $tributacao->percentual_icms : old('percentual_icms') }}}" data-mask="00,00" data-mask-reverse="true">
									@if($errors->has('percentual_icms'))
									<div class="invalid-feedback">
										{{ $errors->first('percentual_icms') }}
									</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer">

						<div class="row">
							<div class="col-xl-2">

							</div>
							<div class="col-lg-3 col-sm-6 col-md-4">
								<a style="width: 100%" class="btn btn-danger" href="/percentualuf">
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
				</form>
			</div>
		</div>
	</div>
</div>

@endsection