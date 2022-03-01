@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="/enderecosEcommerce/update">


					<input type="hidden" name="id" value="{{{ isset($endereco) ? $endereco->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{isset($endereco) ? 'Editar' : 'Novo'}} Endereço</h3>
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
											<label class="col-form-label">Rua</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('rua')) is-invalid @endif" name="rua" value="{{{ isset($endereco) ? $endereco->rua : old('rua') }}}">
												@if($errors->has('rua'))
												<div class="invalid-feedback">
													{{ $errors->first('rua') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-2 col-lg-2">
											<label class="col-form-label">Número</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('numero')) is-invalid @endif" name="numero" value="{{{ isset($endereco) ? $endereco->numero : old('numero') }}}">
												@if($errors->has('numero'))
												<div class="invalid-feedback">
													{{ $errors->first('numero') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-6 col-lg-6">
											<label class="col-form-label">Bairro</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('bairro')) is-invalid @endif" name="bairro" value="{{{ isset($endereco) ? $endereco->bairro : old('bairro') }}}">
												@if($errors->has('bairro'))
												<div class="invalid-feedback">
													{{ $errors->first('bairro') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label">CEP</label>
											<div class="">
												<input data-mask="00000-000" type="text" class="form-control @if($errors->has('cep')) is-invalid @endif" name="cep" value="{{{ isset($endereco) ? $endereco->cep : old('cep') }}}">
												@if($errors->has('cep'))
												<div class="invalid-feedback">
													{{ $errors->first('cep') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-7 col-lg-7">
											<label class="col-form-label">Complemento</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('complemento')) is-invalid @endif" name="complemento" value="{{{ isset($endereco) ? $endereco->complemento : old('complemento') }}}">
												@if($errors->has('complemento'))
												<div class="invalid-feedback">
													{{ $errors->first('complemento') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-lg-5 col-md-5 col-sm-10">
											<label class="col-form-label">Cidade</label>
											<select class="form-control select2" id="kt_select2_1" name="cidade">
												@foreach($cidades as $c)
												<option 
												@if(strtolower($c->nome) == strtolower($endereco->cidade))
												selected
												@endif
												value="{{$c->nome}}"
												>
												{{$c->nome}} ({{$c->uf}})
											</option>
											@endforeach
										</select>
										@if($errors->has('cidade'))
										<div class="invalid-feedback">
											{{ $errors->first('cidade') }}
										</div>
										@endif
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
							<a style="width: 100%" class="btn btn-danger" href="/categoriaEcommerce">
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