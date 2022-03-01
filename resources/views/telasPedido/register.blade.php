@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="{{{ isset($tela) ? '/telasPedido/update': '/telasPedido/save' }}}" enctype="multipart/form-data">


					<input type="hidden" name="id" value="{{{ isset($tela) ? $tela->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{isset($tela) ? 'Editar' : 'Novo'}} Tela de Pedido</h3>
						</div>

					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">

									<div class="row">
										<div class="form-group validated col-sm-8 col-lg-8">
											<label class="col-form-label">Nome</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('nome')) is-invalid @endif" name="nome" value="{{{ isset($tela) ? $tela->nome : old('nome') }}}">
												@if($errors->has('nome'))
												<div class="invalid-feedback">
													{{ $errors->first('nome') }}
												</div>
												@endif
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label">Alerta amarelo(min)</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('alerta_amarelo')) is-invalid @endif" name="alerta_amarelo" value="{{{ isset($tela) ? $tela->alerta_amarelo : old('alerta_amarelo') }}}">
												@if($errors->has('alerta_amarelo'))
												<div class="invalid-feedback">
													{{ $errors->first('alerta_amarelo') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label">Alerta vermelho(min)</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('alerta_vermelho')) is-invalid @endif" name="alerta_vermelho" value="{{{ isset($tela) ? $tela->alerta_vermelho : old('alerta_vermelho') }}}">
												@if($errors->has('alerta_vermelho'))
												<div class="invalid-feedback">
													{{ $errors->first('alerta_vermelho') }}
												</div>
												@endif
											</div>
										</div>

									</div>

								</div>
							</div>
						</div>
					</div>
					<div class="card-footer">

						<div class="row">
							<div class="col-xl-2">

							</div>
							<div class="col-lg-3 col-sm-6 col-md-4">
								<a style="width: 100%" class="btn btn-danger" href="/telasPedido">
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