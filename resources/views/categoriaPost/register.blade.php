@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="{{{ isset($categoria) ? '/categoriaPosts/update': '/categoriaPosts/save' }}}" enctype="multipart/form-data">


					<input type="hidden" name="id" value="{{{ isset($categoria) ? $categoria->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">
							<h3 class="card-title">{{isset($categoria) ? 'Editar' : 'Novo'}} Categoria</h3>
						</div>
					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">

									<div class="row">
										<div class="form-group validated col-sm-6 col-lg-6">
											<label class="col-form-label">Nome</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('nome')) is-invalid @endif" name="nome" value="{{{ isset($categoria) ? $categoria->nome : old('nome') }}}">
												@if($errors->has('nome'))
												<div class="invalid-feedback">
													{{ $errors->first('nome') }}
												</div>
												@endif
											</div>
										</div>
									</div>
									@if(!isset($categoria))
									@if(getenv('DELIVERY') == 1)
									<div class="form-group row">
										<label class="col-3 col-form-label">Atribuir ao Delivery</label>
										<div class="col-3">
											<span class="switch switch-outline switch-success">
												<label>
													<input value="true" @if(old('atribuir_delivery')) checked @endif type="checkbox" name="atribuir_delivery" id="atribuir_delivery">
													<span></span>
												</label>
											</span>

										</div>
									</div>
									@endif
									<div id="imagem" style="display: none">

										<div class="row">
											<div class="form-group validated col-sm-12 col-lg-12">
												<label class="col-form-label">Descrição</label>
												<div class="">
													<input type="text" class="form-control @if($errors->has('descricao')) is-invalid @endif" name="descricao" value="{{{ isset($categoria) ? $categoria->descricao : old('descricao') }}}">
													@if($errors->has('descricao'))
													<div class="invalid-feedback">
														{{ $errors->first('descricao') }}
													</div>
													@endif
												</div>
											</div>
										</div>

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
								<a style="width: 100%" class="btn btn-danger" href="/categoriaPosts">
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