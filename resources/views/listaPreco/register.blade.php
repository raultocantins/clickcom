@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="{{{ isset($lista) ? '/listaDePrecos/update': '/listaDePrecos/save' }}}" >
					<input type="hidden" name="id" value="{{{ isset($lista->id) ? $lista->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{isset($lista) ? 'Editar' : 'Novo'}} Lista de Preço</h3>
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
												<input type="text" class="form-control @if($errors->has('nome')) is-invalid @endif" name="nome" value="{{{ isset($lista) ? $lista->nome : old('nome') }}}">
												@if($errors->has('nome'))
												<div class="invalid-feedback">
													{{ $errors->first('nome') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-2 col-lg-2">
											<label class="col-form-label">% Alteração</label>
											<div class="">
												<input type="text" id="percentual_alteracao" class="form-control @if($errors->has('percentual_alteracao')) is-invalid @endif" name="percentual_alteracao" value="{{{ isset($lista) ? $lista->percentual_alteracao : old('percentual_alteracao') }}}">
												@if($errors->has('percentual_alteracao'))
												<div class="invalid-feedback">
													{{ $errors->first('percentual_alteracao') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Definir valor por</label>
											<div class="">
												<select id="tipo" name="tipo" class="custom-select">
													<option 
													@if(isset($lista))
													@if($lista->tipo == 1)
													selected
													@endif
													@else
													@if(old('tipo') == 1)
													selected
													@endif
													@endif
													value="1">Valor de compra</option>
													<option 
													@if(isset($lista))
													@if($lista->tipo == 2)
													selected
													@endif
													@else
													@if(old('tipo') == 2)
													selected
													@endif
													@endif
													value="2">Valor de venda</option>
												</select>
												@if($errors->has('tipo'))
												<div class="invalid-feedback">
													{{ $errors->first('tipo') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Tipo</label>
											<div class="">
												<select id="tipo_inc_red" name="tipo_inc_red" class="custom-select">
													<option 
													@if(isset($lista))
													@if($lista->tipo_inc_red == 1)
													selected
													@endif
													@else
													@if(old('tipo_inc_red') == 1)
													selected
													@endif
													@endif
													value="1">Incremento</option>
													<option 
													@if(isset($lista))
													@if($lista->tipo_inc_red == 2)
													selected
													@endif
													@else
													disabled
													@if(old('tipo_inc_red') == 2)
													selected
													@endif
													@endif
													value="2" id="reduc">Redução</option>
												</select>
												@if($errors->has('tipo_inc_red'))
												<div class="invalid-feedback">
													{{ $errors->first('tipo_inc_red') }}
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
								<a style="width: 100%" class="btn btn-danger" href="/listaDePrecos">
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

@section('javascript')
<script type="text/javascript">
	$('#tipo').change(() => {
		if($('#tipo').val() == 1){
			$('#tipo_inc_red').val('1').change()
			$('#reduc').attr("disabled", "disabled")
		}else{
			$('#reduc').removeAttr("disabled")
		}
	})
</script>
@endsection
@endsection