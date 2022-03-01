@extends('default.layout')
@section('content')


<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="/empresas/save">

					<input type="hidden" name="id" value="{{{ isset($funcionario) ? $funcionario->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">Cadastrar Empresa</h3>
						</div>

					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">

									<div class="radio-inline">

										<label class="radio radio-outline radio-success">
											<input type="radio" name="tipo_pessoa" value="j" checked/>
											<span></span>
											Juridica
										</label>
										<label class="radio radio-outline radio-success">
											<input type="radio" name="tipo_pessoa" value="f" />
											<span></span>
											Fisica
										</label>
									</div>
									<div class="row">

										<div class="form-group validated col-sm-3 col-lg-4">
											<label class="col-form-label" id="lbl_cpf_cnpj">CNPJ</label>
											<div class="">
												<input type="text" id="cnpj" class="form-control @if($errors->has('cnpj')) is-invalid @endif" name="cnpj" value="{{ old('cnpj') }}">
												@if($errors->has('cpf_cnpj'))
												<div class="invalid-feedback">
													{{ $errors->first('cpf_cnpj') }}
												</div>
												@endif
											</div>
										</div>
										
										<div class="form-group validated col-lg-2 col-md-2 col-sm-6">
											<br><br>
											<a type="button" id="consulta" class="btn btn-success spinner-white spinner-right">
												<span>
													<i class="fa fa-search"></i>
												</span>
											</a>
										</div>

									</div>
									<div class="row">
										<div class="form-group validated col-sm-10 col-lg-10">
											<label class="col-form-label">Nome</label>
											<div class="">
												<input id="nome" type="text" class="form-control @if($errors->has('nome')) is-invalid @endif" name="nome" value="{{ old('nome') }}">
												@if($errors->has('nome'))
												<div class="invalid-feedback">
													{{ $errors->first('nome') }}
												</div>
												@endif
											</div>
										</div>
									</div>




									<div class="row">

										<div class="form-group validated col-sm-6 col-lg-6">
											<label class="col-form-label" id="lbl_ie_rg">Rua</label>
											<div class="">
												<input type="text" id="rua" class="form-control @if($errors->has('rua')) is-invalid @endif" name="rua" value="{{ old('rua') }}">
												@if($errors->has('rua'))
												<div class="invalid-feedback">
													{{ $errors->first('rua') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-2">
											<label class="col-form-label" id="lbl_ie_rg">Nº</label>
											<div class="">
												<input type="text" id="numero" class="form-control @if($errors->has('numero')) is-invalid @endif" name="numero" value="{{ old('numero') }}">
												@if($errors->has('numero'))
												<div class="invalid-feedback">
													{{ $errors->first('numero') }}
												</div>
												@endif
											</div>
										</div>

									</div>

									<div class="row">
										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label" id="lbl_ie_rg">Bairro</label>
											<div class="">
												<input type="text" id="bairro" class="form-control @if($errors->has('bairro')) is-invalid @endif" name="bairro" value="{{ old('bairro') }}">
												@if($errors->has('bairro'))
												<div class="invalid-feedback">
													{{ $errors->first('bairro') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label" id="lbl_ie_rg">Cidade</label>
											<div class="">
												<input type="text" id="cidade" class="form-control @if($errors->has('cidade')) is-invalid @endif" name="cidade" value="{{ old('cidade') }}">
												@if($errors->has('cidade'))
												<div class="invalid-feedback">
													{{ $errors->first('cidade') }}
												</div>
												@endif
											</div>
										</div>

									</div>

									<div class="row">
										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label" id="lbl_ie_rg">Email</label>
											<div class="">
												<input type="text" id="email" class="form-control @if($errors->has('email')) is-invalid @endif" name="email" value="{{ old('email') }}">
												@if($errors->has('email'))
												<div class="invalid-feedback">
													{{ $errors->first('email') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label" id="lbl_ie_rg">Telefone</label>
											<div class="">
												<input type="text" id="telefone" class="form-control @if($errors->has('telefone')) is-invalid @endif telefone" name="telefone" value="{{ old('telefone') }}">
												@if($errors->has('telefone'))
												<div class="invalid-feedback">
													{{ $errors->first('telefone') }}
												</div>
												@endif
											</div>
										</div>

									</div>

									<hr>

									<div class="row">
										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label" id="lbl_ie_rg">Login</label>
											<div class="">
												<input type="text" id="login" class="form-control @if($errors->has('login')) is-invalid @endif" name="login" value="{{ old('login') }}">
												@if($errors->has('login'))
												<div class="invalid-feedback">
													{{ $errors->first('login') }}
												</div>
												@endif
											</div>
										</div>


										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label" id="lbl_ie_rg">Senha</label>
											<div class="">
												<input type="password" id="senha" class="form-control @if($errors->has('senha')) is-invalid @endif" name="senha" value="{{old('senha')}}">
												@if($errors->has('senha'))
												<div class="invalid-feedback">
													{{ $errors->first('senha') }}
												</div>
												@endif
											</div>
										</div>



										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label" id="lbl_ie_rg">Nome usuário</label>
											<div class="">
												<input type="text" id="nome_usuario" class="form-control @if($errors->has('nome_usuario')) is-invalid @endif" name="nome_usuario" value="{{old('nome_usuario')}}">
												@if($errors->has('nome_usuario'))
												<div class="invalid-feedback">
													{{ $errors->first('nome_usuario') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-6 col-lg-4">
											<label class="col-form-label text-left col-lg-9 col-sm-9">Tipo representante</label>
											<button type="button" class="btn btn-light-info btn-sm btn-icon col-lg-6 col-sm-6" data-toggle="popover" data-trigger="click" data-content="Se selecionado a empresa será listada no cadastro de representantes"><i class="la la-info"></i></button>
											<div class="col-6">
												<span class="switch switch-outline switch-primary">
													<label>
														<input id="tipo_representante" @if(old('tipo_representante')) checked @endif
														name="tipo_representante" type="checkbox" >
														<span></span>
													</label>
												</span>
											</div>
										</div>

										<div class="comissao col-12" style="display: none;">
											<div class="row">
												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">Comissão %</label>
													<div class="">
														<input type="text" id="comissao" class="form-control @if($errors->has('comissao')) is-invalid @endif money" name="comissao" value="{{old('comissao')}}">
														@if($errors->has('comissao'))
														<div class="invalid-feedback">
															{{ $errors->first('comissao') }}
														</div>
														@endif
													</div>
												</div>

												<div class="col-3">
													<label class="col-form-label">Acesso a XML</label>

													<span class="switch switch-outline switch-primary">
														<label>
															<input id="acesso_xml" @if(old('acesso_xml')) checked @endif
															name="acesso_xml" type="checkbox" >
															<span></span>
														</label>
													</span>
												</div>

												<div class="form-group validated col-sm-4 col-lg-4">
													<label class="col-form-label">Limite de cadastro de empresas</label>
													<div class="">
														<input type="text" id="limite_cadastros" class="form-control @if($errors->has('limite_cadastros')) is-invalid @endif" name="limite_cadastros" value="{{old('limite_cadastros')}}">
														@if($errors->has('limite_cadastros'))
														<div class="invalid-feedback">
															{{ $errors->first('limite_cadastros') }}
														</div>
														@endif
													</div>
												</div>
											</div>

										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-12">

											<label class="col-3 col-form-label">Permissão de Acesso:</label>

											@if(sizeof($perfis) > 0)
											<div class="form-group validated col-sm-4 col-lg-4">
												<label class="col-form-label" id="lbl_ie_rg">Perfil</label>
												<div class="">
													<select id="perfil-select" class="custom-select" name="perfil_id">
														<option value="0">--</option>
														@foreach($perfis as $p)
														<option value="{{$p}}">
															{{$p->nome}}
														</option>
														@endforeach
													</select>
												</div>
											</div>
											@endif

											<input type="hidden" id="menus" value="{{json_encode($menu)}}" name="">
											@foreach($menu as $m)
											
											<div class="col-12 col-form-label">
												<span>
													<label class="checkbox checkbox-info">
														<input id="todos_{{$m['titulo']}}" onclick="marcarTudo('{{$m['titulo']}}')" type="checkbox" >
														<span></span><strong class="text-info" style="margin-left: 5px; font-size: 16px;">{{strtoupper($m['titulo'])}} </strong>
													</label>
												</span>
												<div class="checkbox-inline" style="margin-top: 10px;">
													@foreach($m['subs'] as $s)

													@if($s['nome'] != 'NFS-e')

													@php
													$link = str_replace('/', '', $s['rota']);
													$link = str_replace('.', '_', $link);
													$link = str_replace(':', '_', $link);

													@endphp
													<label class="checkbox checkbox-info check-sub">
														<input id="sub_{{$link}}" type="checkbox" name="{{$s['rota']}}">
														<span></span>{{$s['nome']}}
													</label>
													
													@endif
													@endforeach
												</div>

											</div>

											@endforeach
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
								<a style="width: 100%" class="btn btn-danger" href="/empresas">
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
	$('[data-toggle="popover"]').popover()
	$("input[name='tipo_pessoa']").change(function(target){
		console.log(target.target.value)
		if(target.target.value == 'j'){
			$('#lbl_cpf_cnpj').html('CNPJ')
			$('#cnpj').mask('00.000.000/0000-00')
			$('#consulta').removeClass('disabled')
			$('#consulta').removeAttr('disabled')

		}else{
			$('#lbl_cpf_cnpj').html('CPF')
			$('#cnpj').mask('000.000.000-00')
			$('#consulta').addClass('disabled')
			$('#consulta').attr('disabled', true)

		}	
	});

	$(function () {
		tipoRepresentante()
	})

	$('#tipo_representante').change(() => {
		tipoRepresentante()
	})

	function tipoRepresentante(){
		if($('#tipo_representante').is(':checked')){
			$('.comissao').css('display', 'block')
		}else{
			$('.comissao').css('display', 'none')
		}
	}
</script>

@endsection
@endsection