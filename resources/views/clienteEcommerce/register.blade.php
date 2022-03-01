@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="{{{ isset($cliente) ? '/clienteEcommerce/update': '/clienteEcommerce/save' }}}">


					<input type="hidden" name="id" value="{{{ isset($cliente) ? $cliente->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{isset($cliente) ? 'Editar' : 'Novo'}} Cliente</h3>
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
												<input type="text" class="form-control @if($errors->has('nome')) is-invalid @endif" name="nome" value="{{{ isset($cliente) ? $cliente->nome : old('nome') }}}">
												@if($errors->has('nome'))
												<div class="invalid-feedback">
													{{ $errors->first('nome') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-6 col-lg-6">
											<label class="col-form-label">Sobre Nome</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('sobre_nome')) is-invalid @endif" name="sobre_nome" value="{{{ isset($cliente) ? $cliente->sobre_nome : old('sobre_nome') }}}">
												@if($errors->has('sobre_nome'))
												<div class="invalid-feedback">
													{{ $errors->first('sobre_nome') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-2 col-lg-2">
											<label class="col-form-label">Tipo Doc.</label>
											<div class="">
												<select id="tp_doc" class="custom-select">
													<option value="cpf">CPF</option>
													<option value="cnpj">CNPJ</option>
												</select>
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label">Documento</label>
											<div class="">
												<input data-mask="000.000.000-00" data-mask-reverse="true" type="text" class="form-control @if($errors->has('cpf')) is-invalid @endif" id="doc" name="cpf" value="{{{ isset($cliente) ? $cliente->cpf : old('cpf') }}}">
												@if($errors->has('cpf'))
												<div class="invalid-feedback">
													{{ $errors->first('cpf') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-6 col-lg-6">
											<label class="col-form-label">Email</label>
											<div class="">
												<input type="email" class="form-control @if($errors->has('email')) is-invalid @endif" name="email" value="{{{ isset($cliente) ? $cliente->email : old('email') }}}">
												@if($errors->has('email'))
												<div class="invalid-feedback">
													{{ $errors->first('email') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-6 col-lg-6">
											<label class="col-form-label">Telefone</label>
											<div class="">
												<input data-mask="(00) 00000-0000" type="text" class="form-control @if($errors->has('telefone')) is-invalid @endif" name="telefone" value="{{{ isset($cliente) ? $cliente->telefone : old('telefone') }}}">
												@if($errors->has('telefone'))
												<div class="invalid-feedback">
													{{ $errors->first('telefone') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-6 col-lg-6">
											<label class="col-form-label">Senha</label>
											<div class="">
												<input type="password" class="form-control @if($errors->has('senha')) is-invalid @endif" name="senha" value="">
												@if($errors->has('senha'))
												<div class="invalid-feedback">
													{{ $errors->first('senha') }}
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
@section('javascript')
<script type="text/javascript">
    $('#tp_doc').change((target) => {
        let v = target.target.value
        if(v == 'cpf'){
            $('#doc').mask('000.000.000-00', {reverse: true});
        }else{
            $('#doc').mask('00.000.000/0000-00', {reverse: true});
        }
    })
</script>
@endsection 

@endsection