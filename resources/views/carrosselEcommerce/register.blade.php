@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="{{{ isset($carrossel) ? '/carrosselEcommerce/update': '/carrosselEcommerce/save' }}}" enctype="multipart/form-data">


					<input type="hidden" name="id" value="{{{ isset($carrossel) ? $carrossel->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{isset($carrossel) ? 'Editar' : 'Novo'}} Carrossel</h3>
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
											<label class="col-form-label">Título</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('titulo')) is-invalid @endif" name="titulo" value="{{{ isset($carrossel) ? $carrossel->titulo : old('titulo') }}}">
												@if($errors->has('titulo'))
												<div class="invalid-feedback">
													{{ $errors->first('titulo') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-2 col-lg-2">
											<label class="col-form-label">Cor</label>
											<div class="">
												<input type="color" class="form-control @if($errors->has('cor_titulo')) is-invalid @endif" name="cor_titulo" value="{{{ isset($carrossel) ? $carrossel->cor_titulo : old('cor_titulo') }}}">
												@if($errors->has('cor_titulo'))
												<div class="invalid-feedback">
													{{ $errors->first('cor_titulo') }}
												</div>
												@endif
											</div>
										</div>
									</div>
									<div class="row">

										<div class="form-group validated col-sm-6 col-lg-6">
											<label class="col-form-label">Link ação(opcional)</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('link_acao')) is-invalid @endif" name="link_acao" value="{{{ isset($carrossel) ? $carrossel->link_acao : old('link_acao') }}}">
												@if($errors->has('link_acao'))
												<div class="invalid-feedback">
													{{ $errors->first('link_acao') }}
												</div>
												@endif
											</div>
										</div>
										<div class="form-group validated col-sm-6 col-lg-6">
											<label class="col-form-label">Nome do botão(opcional)</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('nome_botao')) is-invalid @endif" name="nome_botao" value="{{{ isset($carrossel) ? $carrossel->nome_botao : old('nome_botao') }}}">
												@if($errors->has('nome_botao'))
												<div class="invalid-feedback">
													{{ $errors->first('nome_botao') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-10 col-lg-10">
											<label class="col-form-label">Descrição</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('descricao')) is-invalid @endif" name="descricao" value="{{{ isset($carrossel) ? $carrossel->descricao : old('descricao') }}}">
												@if($errors->has('descricao'))
												<div class="invalid-feedback">
													{{ $errors->first('descricao') }}
												</div>
												@endif
											</div>
										</div>
										<div class="form-group validated col-sm-2 col-lg-2">
											<label class="col-form-label">Cor</label>
											<div class="">
												<input type="color" class="form-control @if($errors->has('cor_descricao')) is-invalid @endif" name="cor_descricao" value="{{{ isset($carrossel) ? $carrossel->cor_descricao : old('cor_descricao') }}}">
												@if($errors->has('cor_descricao'))
												<div class="invalid-feedback">
													{{ $errors->first('cor_descricao') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div>
										<div style="background-image: url(/imgs/no_image.png)">
										</div>

										<div class="form-group row">
											<label class="col-xl-12 col-lg-12 col-form-label text-left">Imagem</label>
											<div class="col-lg-10 col-xl-6">

												<div class="image-input image-input-outline" id="kt_image_1">
													<div class="image-input-wrapper" @if(isset($carrossel) && file_exists(public_path('ecommerce/carrossel/').$carrossel->img)) style="background-image: url(/ecommerce/carrossel/{{$carrossel->img}})" @else style="background-image: url(/imgs/no_image.png)" @endif></div>
													<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
														<i class="fa fa-pencil icon-sm text-muted"></i>
														<input type="file" name="file" accept=".png, .jpg, .jpeg">
														<input type="hidden" name="profile_avatar_remove">
													</label>
													<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Cancel avatar">
														<i class="fa fa-close icon-xs text-muted"></i>
													</span>
												</div>


												<span class="form-text text-muted">.png, .jpg, .jpeg</span>
												@if($errors->has('file'))
												<div class="text-danger">
													{{ $errors->first('file') }}
												</div>
												@endif
												<span class="text-danger">*Recomendado 1280x600</span>
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
								<a style="width: 100%" class="btn btn-danger" href="/carrosselEcommerce">
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