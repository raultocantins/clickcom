@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="/representantes/alterarSenha">

					<input type="hidden" name="id" value="{{$representante->id}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">Alterar senha dos representante: <strong style="margin-left: 2px;">{{$representante->nome}}</strong></h3>
						</div>

					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">

									<div class="row">
										<div class="form-group validated col-12">
											<h4>Usuário:
												<div class="col-sm-6">

													<div class="card card-custom gutter-b">
														<div class="card-header">
															<h3 class="card-title">
																{{$representante->usuario->nome}}
															</h3>
														</div>
														<div class="card-body" style="height: 90px;">

															<h4>Login: <strong class="text-info">{{$representante->usuario->login}}</strong></h4>
															
														</div>
													</div>
												</div>

											</h4>
										</div>
									</div>
									
									<div class="row">
										<div class="form-group validated col-sm-5 col-lg-5">
											<label class="col-form-label">Nova senha</label>
											<div class="">
												<input required id="nome" type="password" class="form-control @if($errors->has('nome')) is-invalid @endif" name="senha" value="">
												
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
								<a style="width: 100%" class="btn btn-danger" href="/funcionarios">
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