@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="{{{ isset($cidade) ? '/cidades/update': '/cidades/save' }}}">


					<input type="hidden" name="id" value="{{{ isset($cidade) ? $cidade->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">
							<h3 class="card-title">{{isset($cidade) ? 'Editar' : 'Novo'}} Cidade</h3>
						</div>
					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">

									<div class="row">
										<div class="form-group validated col-sm-5 col-lg-5">
											<label class="col-form-label">Nome</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('nome')) is-invalid @endif" name="nome" value="{{{ isset($cidade) ? $cidade->nome : old('nome') }}}">
												@if($errors->has('nome'))
												<div class="invalid-feedback">
													{{ $errors->first('nome') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-2">
											<label class="col-form-label">UF</label>
											<div class="">
												<select name="uf" class="custom-select">
													@foreach(App\Models\Cidade::estados() as $uf)
													<option
													@if(isset($cidade))
													@if($cidade->uf == $uf)
													selected
													@endif
													@else
													@if(old('uf') == $uf)
													selected
													@endif
													@endif
													value="{{$uf}}">{{$uf}}</option>
													@endforeach
												</select>
												@if($errors->has('uf'))
												<div class="invalid-feedback">
													{{ $errors->first('uf') }}
												</div>
												@endif
											</div>
										</div>
										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label">Código IBGE</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('codigo')) is-invalid @endif" name="codigo" value="{{{ isset($cidade) ? $cidade->codigo : old('codigo') }}}">
												@if($errors->has('codigo'))
												<div class="invalid-feedback">
													{{ $errors->first('codigo') }}
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
								<a style="width: 100%" class="btn btn-danger" href="/cidades">
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