@extends('default.layout')
@section('content')


<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="/dre/save">

					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">Nova DRE
								<a style="margin-left: 10px;" href="/dre/list" class="btn btn-light-info">
									<i class="la la-list"></i>
									Lista
								</a>
							</h3>
							

						</div>

					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">

									<div class="row">

										<div class="form-group col-lg-4 col-md-6 col-sm-12">
											<label class="col-form-label">Data de Inicio</label>
											<div class="">
												<div class="input-group date">
													<input type="text" name="data_inicio" class="form-control @if($errors->has('data_inicio')) is-invalid @endif" readonly id="kt_datepicker_3" />
													<div class="input-group-append">
														<span class="input-group-text">
															<i class="la la-calendar"></i>
														</span>
													</div>
												</div>
												@if($errors->has('data_inicio'))
												<div class="invalid-feedback">
													{{ $errors->first('data_inicio') }}
												</div>
												@endif

											</div>
										</div>

										<div class="form-group col-lg-4 col-md-6 col-sm-12">
											<label class="col-form-label">Data de Término</label>
											<div class="">
												<div class="input-group date">
													<input type="text" name="data_fim" class="form-control @if($errors->has('data_fim')) is-invalid @endif" readonly id="kt_datepicker_3" />
													<div class="input-group-append">
														<span class="input-group-text">
															<i class="la la-calendar"></i>
														</span>
													</div>
												</div>
												@if($errors->has('data_fim'))
												<div class="invalid-feedback">
													{{ $errors->first('data_fim') }}
												</div>
												@endif

											</div>
										</div>

										@if($tributacao->regime != 1)
										<div class="form-group col-lg-4 col-md-6 col-sm-12">
											<label class="col-form-label">% Imposto</label>
											<div class="">
												<div class="input-group date">
													<input type="text" name="perc_imposto" class="form-control @if($errors->has('perc_imposto')) is-invalid @endif" id="perc_imposto" value="0" />
													<div class="input-group-append">
														<span class="input-group-text">
															<i class="la la-percent"></i>
														</span>
													</div>
												</div>
												@if($errors->has('perc_imposto'))
												<div class="invalid-feedback">
													{{ $errors->first('perc_imposto') }}
												</div>
												@endif

											</div>
										</div>
										@endif

										<div class="form-group col-12">
											<label class="col-form-label">Observação</label>
											<div class="">
												<div class="input-group date">
													<input type="text" name="observacao" class="form-control @if($errors->has('observacao')) is-invalid @endif"/>

												</div>
												@if($errors->has('observacao'))
												<div class="invalid-feedback">
													{{ $errors->first('observacao') }}
												</div>
												@endif

											</div>
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
			</form>
		</div>
	</div>
</div>
</div>


@endsection