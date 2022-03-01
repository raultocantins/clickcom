@extends('default.layout')
@section('content')
<style type="text/css">
	.btn-file {
		position: relative;
		overflow: hidden;
	}

	.btn-file input[type=file] {
		position: absolute;
		top: 0;
		right: 0;
		min-width: 100%;
		min-height: 100%;
		font-size: 100px;
		text-align: right;
		filter: alpha(opacity=0);
		opacity: 0;
		outline: none;
		background: white;
		cursor: inherit;
		display: block;
	}
</style>

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<form method="post" enctype="multipart/form-data" action="/clientes/importacao">

		<div class="container">
			<div class="card card-custom gutter-b example example-compact">
				<div class="col-lg-12">
					<!--begin::Portlet-->

					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">Importação de Clientes

								<a style="margin-left: 5px;" class="btn btn-info" href="/clientes/downloadModelo">
									<i class="las la-download"></i>Baixar modelo
								</a>
							</h3>


						</div>

					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">


							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<div class="form-group validated col-sm-10 col-lg-10">
								<label class="col-form-label">.xls/.xlsx</label>
								<div class="">
									<span class="btn btn-primary btn-file">
										Procurar arquivo<input accept=".xls, .xlsx" name="file" type="file">
									</span>
									<label class="text-info" id="filename"></label>
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
							<button style="width: 100%" type="submit" class="btn btn-success">
								<i class="la la-check"></i>
								<span class="">Importar Cliente</span>
							</button>
						</div>

					</div>
				</div>
			</div>
		</div>
	</form>
</div>

@endsection