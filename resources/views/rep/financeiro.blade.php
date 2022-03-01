@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="/rep/salvarPagamento">

					<input type="hidden" name="rep_id" value="{{$rep_id}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">Pagamento empresa: <strong style="margin-left: 2px;">{{$empresa->nome}}</strong>
								<a style="margin-left: 5px;" class="btn btn-info" href="/rep/verPagamentos/{{$rep_id}}">Ver pagamentos</a>
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
										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Valor</label>
											<div class="">
												<input required id="nome" type="text" class="form-control @if($errors->has('valor')) is-invalid @endif money" name="valor" value="">
												
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label">Forma de pagamento</label>
											<div class="">
												<select name="forma_pagamento" class="custom-select">
													<option value="Dinheiro">Dinheiro</option>
													<option value="Cartão de débito">Cartão de débito</option>
													<option value="Cartão de crédito">Cartão de crédito</option>
													<option value="Pix">Pix</option>
													<option value="Transferência">Transferência</option>
												</select>
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
								<a style="width: 100%" class="btn btn-danger" href="/rep">
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