@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="/rep/saveConfig" enctype="multipart/form-data">

					<input type="hidden" name="id" value="{{{ isset($config->id) ? $config->id : 0 }}}">

					<input type="hidden" name="empresaId" value="{{$empresa->id}}">

					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{{ isset($config) ? "Editar": "Cadastrar" }}} Emitente Fiscal <strong style="margin-left: 3px;" class="text-danger">{{$empresa->nome}}</strong></h3>
						</div>

					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">

									<div class="row">
										@if(empty($certificado))
										<div class="col-lg-12 col-sm-12 col-md-12">
											<p class="text-danger">VOCE AINDA NÃO FEZ UPLOAD DO CERTIFICADO ATÉ O MOMENTO</p>
											
											@if(!isset($config))
											<p class="text-danger">>>Preencha o formulário</p>
											@endif

										</div>
										<div class="col-lg-12 col-sm-12 col-md-12">

											@isset($config)
											<a class="btn btn-lg btn-light-info" href="/rep/uploadCertificado/{{$empresa->id}}">
												Fazer upload agora
											</a>
											@endisset
										</div>

										@else
										<div class="col-lg-12 col-sm-12 col-md-12">
											<a onclick='swal("Atenção!", "Deseja remover este certificado?", "warning").then((sim) => {if(sim){ location.href="/rep/deleteCertificado/{{$empresa->id}}" }else{return false} })' href="#!" class="btn btn-danger">
												Remover certificado
											</a>

										</div>
										
										<div class="card card-custom gutter-b">
											<div class="card-body">
												<div class="card-content">

													<h6>Serial Certificado: <strong class="green-text">{{$infoCertificado['serial']}}</strong></h6>
													<h6>Inicio: <strong class="green-text">{{$infoCertificado['inicio']}}</strong></h6>
													<h6>Expiração: <strong class="green-text">{{$infoCertificado['expiracao']}}</strong></h6>
													<h6>IDCTX: <strong class="green-text">{{$infoCertificado['id']}}</strong></h6>

												</div>
												@if($soapDesativado)
												<div class="alert alert-custom alert-danger fade show" role="alert" style="margin-top: 10px;">
													<div class="alert-icon"><i class="la la-warning"></i></div>
													<div class="alert-text">
														Extensão SOAP está desativada!!
													</div>
												</div>
												@endif
											</div>
										</div>

										@endif
									</div>

									<div class="row">

										<div class="form-group validated col-sm-6 col-lg-6">
											<label class="col-form-label">CNPJ</label>
											<div class="">
												<input id="cnpj" type="text" class="form-control @if($errors->has('cnpj')) is-invalid @endif" name="cnpj" value="{{{ isset($config) ? $config->cnpj : old('cnpj') }}}">
												@if($errors->has('cnpj'))
												<div class="invalid-feedback">
													{{ $errors->first('cnpj') }}
												</div>
												@endif
											</div>
										</div>
										
										<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
											<br><br>
											<a type="button" id="btn-consulta-cadastro" onclick="consultaCNPJ()" class="btn btn-success spinner-white spinner-right">
												<span>
													<i class="fa fa-search"></i>
												</span>
											</a>
										</div>

										
									</div>

									<div class="row">
										<div class="form-group validated col-sm-12 col-lg-12">
											<label class="col-form-label">Razao Social</label>
											<div class="">
												<input id="razao_social" type="text" class="form-control @if($errors->has('razao_social')) is-invalid @endif" name="razao_social" value="{{{ isset($config) ? $config->razao_social : old('razao_social') }}}">
												@if($errors->has('razao_social'))
												<div class="invalid-feedback">
													{{ $errors->first('razao_social') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-12 col-lg-12">
											<label class="col-form-label">Nome Fantasia</label>
											<div class="">
												<input id="nome_fantasia" type="text" class="form-control @if($errors->has('nome_fantasia')) is-invalid @endif" name="nome_fantasia" value="{{{ isset($config) ? $config->nome_fantasia : old('nome_fantasia') }}}">
												@if($errors->has('nome_fantasia'))
												<div class="invalid-feedback">
													{{ $errors->first('nome_fantasia') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">

										<div class="form-group validated col-sm-2 col-lg-2">
											<label class="col-form-label">Tipo</label>
											<div class="">
												<select id="tipo" class="form-control custom-select">
													<option value="f">Fisica</option>
													<option value="j">Juridica</option>
												</select>
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4">
											<label id="tipo-doc" class="col-form-label">CNPJ</label>
											<div class="">
												<input id="cnpj2" type="text" class="form-control @if($errors->has('cnpj')) is-invalid @endif" name="cnpj" value="{{{ isset($config) ? $config->cnpj : old('cnpj') }}}">
												@if($errors->has('cnpj'))
												<div class="invalid-feedback">
													{{ $errors->first('cnpj') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label">Inscrição Estadual</label>
											<div class="">
												<input id="ie" type="text" class="form-control @if($errors->has('ie')) is-invalid @endif" name="ie" value="{{{ isset($config) ? $config->ie : old('ie') }}}">
												@if($errors->has('ie'))
												<div class="invalid-feedback">
													{{ $errors->first('ie') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<hr>
									<h5>Endereço</h5>
									<div class="row">

										<div class="form-group validated col-sm-10 col-lg-10">
											<label class="col-form-label">Logradouro</label>
											<div class="">
												<input id="logradouro" type="text" class="form-control @if($errors->has('logradouro')) is-invalid @endif" name="logradouro" value="{{{ isset($config) ? $config->logradouro : old('logradouro') }}}">
												@if($errors->has('logradouro'))
												<div class="invalid-feedback">
													{{ $errors->first('logradouro') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-2 col-lg-2">
											<label class="col-form-label">Nº</label>
											<div class="">
												<input id="numero" type="text" class="form-control @if($errors->has('numero')) is-invalid @endif" name="numero" value="{{{ isset($config) ? $config->numero : old('numero') }}}">
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
											<label class="col-form-label">Bairro</label>
											<div class="">
												<input id="bairro" type="text" class="form-control @if($errors->has('bairro')) is-invalid @endif" name="bairro" value="{{{ isset($config) ? $config->bairro : old('bairro') }}}">
												@if($errors->has('bairro'))
												<div class="invalid-feedback">
													{{ $errors->first('bairro') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">CEP</label>
											<div class="">
												<input id="cep" type="text" class="form-control @if($errors->has('cep')) is-invalid @endif" name="cep" value="{{{ isset($config) ? $config->cep : old('cep') }}}">
												@if($errors->has('cep'))
												<div class="invalid-feedback">
													{{ $errors->first('cep') }}
												</div>
												@endif
											</div>
										</div>


										<!-- <div class="form-group validated col-sm-5 col-lg-5">
											<label class="col-form-label">Município</label>
											<div class="">
												<input id="municipio" type="text" class="form-control @if($errors->has('municipio')) is-invalid @endif" name="municipio" value="{{{ isset($config) ? $config->municipio : old('municipio') }}}">

												<select>
													
												</select>
												@if($errors->has('municipio'))
												<div class="invalid-feedback">
													{{ $errors->first('municipio') }}
												</div>
												@endif
											</div>
										</div> -->

										<div class="form-group validated col-lg-5 col-md-5 col-sm-10">
											<label class="col-form-label text-left col-12 col-sm-12">Cidade</label>
											<select class="form-control select2" id="kt_select2_1" name="cidade">
												@foreach($cidades as $c)
												<option value="{{$c->id}}" @isset($config) @if($c->codigo == $config->codMun) selected @endif @endisset 
													@if(old('cidade') == $c->id)
													selected
													@endif
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


										<div class="form-group validated col-lg-4 col-md-4 col-sm-10">
											<label class="col-form-label">Telefone</label>
											<div class="">
												<input id="telefone" type="text" class="form-control @if($errors->has('fone')) is-invalid @endif" name="fone" value="{{{ isset($config) ? $config->fone : old('fone') }}}">
												@if($errors->has('fone'))
												<div class="invalid-feedback">
													{{ $errors->first('fone') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-lg-4 col-md-4 col-sm-10">
											<label class="col-form-label">Email</label>
											<div class="">
												<input id="email" class="form-control @if($errors->has('email')) is-invalid @endif" name="email" value="{{{ isset($config) ? $config->email : old('email') }}}">
												@if($errors->has('email'))
												<div class="invalid-feedback">
													{{ $errors->first('email') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-lg-12 col-md-12 col-sm-12">
											<label class="col-form-label">CST/CSOSN Padrão</label>

											<select class="custom-select form-control" name="CST_CSOSN_padrao">
												<option value="null">--</option>
												@foreach($listaCSTCSOSN as $key => $l)
												<option value="{{$key}}"
												@if(isset($config))
												@if($key == $config->CST_CSOSN_padrao)
												selected
												@endif
												@else
												@if(old('CST_CSOSN_padrao') == $key)
												selected
												@endif
												@endif>{{$key}} - {{$l}}</option>
												@endforeach
											</select>

										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
											<label class="col-form-label">CST/PIS Padrão</label>

											<select class="custom-select form-control" name="CST_PIS_padrao">
												<option value="null">--</option>
												@foreach($listaCSTPISCOFINS as $key => $l)
												<option value="{{$key}}"
												@if(isset($config))
												@if($key == $config->CST_PIS_padrao)
												selected
												@endif
												@else
												@if(old('CST_PIS_padrao') == $key)
												selected
												@endif
												@endif
												>{{$key}} - {{$l}}</option>
												@endforeach
											</select>

										</div>

										<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
											<label class="col-form-label">CST/COFINS Padrão</label>

											<select class="custom-select form-control" name="CST_COFINS_padrao">
												<option value="null">--</option>
												@foreach($listaCSTPISCOFINS as $key => $l)
												<option value="{{$key}}"
												@if(isset($config))
												@if($key == $config->CST_COFINS_padrao)
												selected
												@endif
												@else
												@if(old('CST_COFINS_padrao') == $key)
												selected
												@endif
												@endif
												>{{$key}} - {{$l}}</option>
												@endforeach
											</select>

										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-lg-12 col-md-12 col-sm-12">
											<label class="col-form-label">CST/IPI Padrão</label>

											<select class="custom-select form-control" name="CST_IPI_padrao">
												<option value="null">--</option>
												@foreach($listaCSTIPI as $key => $l)
												<option value="{{$key}}"
												@if(isset($config))
												@if($key == $config->CST_IPI_padrao)
												selected
												@endif
												@if(old('CST_IPI_padrao') == $key)
												selected
												@endif
												@endif
												>{{$key}} - {{$l}}</option>
												@endforeach
											</select>

										</div>
									</div>


									<div class="row">
										<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
											<label class="col-form-label">Frete Padrão</label>

											<select class="custom-select form-control" name="frete_padrao">
												@foreach($tiposFrete as $key => $t)
												<option value="{{$key}}"
												@isset($config)
												@if($key == $config->frete_padrao)
												selected
												@endif
												@endisset
												>{{$key}} - {{$t}}</option>
												@endforeach
											</select>

										</div>

										<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
											<label class="col-form-label">Tipo de pagamento Padrão</label>

											<select class="custom-select form-control" name="tipo_pagamento_padrao">
												@foreach($tiposPagamento as $key => $t)
												<option value="{{$key}}"
												@isset($config)
												@if($key == $config->tipo_pagamento_padrao)
												selected
												@endif
												@endisset
												>{{$key}} - {{$t}}</option>
												@endforeach
											</select>

										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-lg-12 col-md-12 col-sm-12">
											<label class="col-form-label">
												Natureza de Operação Padrão Frente de Caixa
											</label>

											<select class="custom-select form-control" name="nat_op_padrao">
												@foreach($naturezas as $n)
												<option value="{{$n->id}}"
													@isset($config)
													@if($n->id == $config->nat_op_padrao)
													selected
													@endif
													@endisset
													>{{$n->natureza}}
												</option>
												@endforeach
											</select>

										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-lg-3 col-md-4 col-sm-4">
											<label class="col-form-label">
												Ambiente
											</label>

											<select class="custom-select form-control" name="ambiente">
												<option @if(isset($config)) @if($config->ambiente == 2) selected @endif @endif value="2">2 - Homologação</option>
												<option @if(isset($config)) @if($config->ambiente == 1) selected @endif @endif value="1">1 - Produção</option>
											</select>

										</div>

										<div class="form-group validated col-lg-3 col-md-4 col-sm-10">
											<label class="col-form-label">Nº Serie NF-e</label>
											<div class="">
												<input id="numero_serie_nfe" type="text" class="form-control @if($errors->has('numero_serie_nfe')) is-invalid @endif" name="numero_serie_nfe" value="{{{ isset($config) ? $config->numero_serie_nfe : old('numero_serie_nfe') }}}">
												@if($errors->has('numero_serie_nfe'))
												<div class="invalid-feedback">
													{{ $errors->first('numero_serie_nfe') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-lg-3 col-md-4 col-sm-10">
											<label class="col-form-label">Nº Serie NFC-e</label>
											<div class="">
												<input id="numero_serie_nfce" type="text" class="form-control @if($errors->has('numero_serie_nfce')) is-invalid @endif" name="numero_serie_nfce" value="{{{ isset($config) ? $config->numero_serie_nfce : old('numero_serie_nfce') }}}">
												@if($errors->has('numero_serie_nfce'))
												<div class="invalid-feedback">
													{{ $errors->first('numero_serie_nfce') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-lg-3 col-md-4 col-sm-10">
											<label class="col-form-label">Nº Serie CT-e</label>
											<div class="">
												<input id="numero_serie_cte" type="text" class="form-control @if($errors->has('numero_serie_cte')) is-invalid @endif" name="numero_serie_cte" value="{{{ isset($config) ? $config->numero_serie_cte : old('numero_serie_cte') }}}">
												@if($errors->has('numero_serie_cte'))
												<div class="invalid-feedback">
													{{ $errors->first('numero_serie_cte') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-lg-3 col-md-3 col-sm-10">
											<label class="col-form-label">Ultimo Nº NF-e</label>
											<div class="">
												<input id="ultimo_numero_nfe" type="text" class="form-control @if($errors->has('ultimo_numero_nfe')) is-invalid @endif" name="ultimo_numero_nfe" value="{{{ isset($config) ? $config->ultimo_numero_nfe : old('ultimo_numero_nfe') }}}">
												@if($errors->has('ultimo_numero_nfe'))
												<div class="invalid-feedback">
													{{ $errors->first('ultimo_numero_nfe') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-lg-3 col-md-3 col-sm-10">
											<label class="col-form-label">Ultimo Nº NFC-e</label>
											<div class="">
												<input id="ultimo_numero_nfce" type="text" class="form-control @if($errors->has('ultimo_numero_nfce')) is-invalid @endif" name="ultimo_numero_nfce" value="{{{ isset($config) ? $config->ultimo_numero_nfce : old('ultimo_numero_nfce') }}}">
												@if($errors->has('ultimo_numero_nfce'))
												<div class="invalid-feedback">
													{{ $errors->first('ultimo_numero_nfce') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-lg-3 col-md-3 col-sm-10">
											<label class="col-form-label">Ultimo Nº CT-e</label>
											<div class="">
												<input id="ultimo_numero_cte" type="text" class="form-control @if($errors->has('ultimo_numero_cte')) is-invalid @endif" name="ultimo_numero_cte" value="{{{ isset($config) ? $config->ultimo_numero_cte : old('ultimo_numero_cte') }}}">
												@if($errors->has('ultimo_numero_cte'))
												<div class="invalid-feedback">
													{{ $errors->first('ultimo_numero_cte') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-lg-3 col-md-3 col-sm-10">
											<label class="col-form-label">Ultimo Nº MDF-e</label>
											<div class="">
												<input id="ultimo_numero_mdfe" type="text" class="form-control @if($errors->has('ultimo_numero_mdfe')) is-invalid @endif" name="ultimo_numero_mdfe" value="{{{ isset($config) ? $config->ultimo_numero_mdfe : old('ultimo_numero_mdfe') }}}">
												@if($errors->has('ultimo_numero_mdfe'))
												<div class="invalid-feedback">
													{{ $errors->first('ultimo_numero_mdfe') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
											<label class="col-form-label">CSC</label>
											<div class="">
												<input id="csc" type="text" class="form-control @if($errors->has('csc')) is-invalid @endif" name="csc" value="{{{ isset($config) ? $config->csc : old('csc') }}}">
												@if($errors->has('csc'))
												<div class="invalid-feedback">
													{{ $errors->first('csc') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-lg-3 col-md-3 col-sm-3">
											<label class="col-form-label">CSCID</label>
											<div class="">
												<input id="csc_id" type="text" class="form-control @if($errors->has('csc_id')) is-invalid @endif" name="csc_id" value="{{{ isset($config) ? $config->csc_id : old('csc_id') }}}">
												@if($errors->has('csc_id'))
												<div class="invalid-feedback">
													{{ $errors->first('csc_id') }}
												</div>
												@endif
											</div>
										</div>

									<!-- 	<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Certificado A3</label>
											<div class="col-6">
												<span class="switch switch-outline switch-primary">
													<label>
														<input id="certificado_a3" @if(isset($config->certificado_a3) && $config->certificado_a3) checked @endisset
														name="certificado_a3" type="checkbox" >
														<span></span>
													</label>
												</span>
											</div>
											<p style="color: red">*Em desenvolvimento</p>

										</div> -->

									</div>

									<div class="row">

										<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
											<label class="col-form-label">Inscrição municipal (opcional)</label>
											<div class="">
												<input id="inscricao_municipal" type="text" class="form-control @if($errors->has('inscricao_municipal')) is-invalid @endif im" name="inscricao_municipal" value="{{{ isset($config) ? $config->inscricao_municipal : old('inscricao_municipal') }}}">
												@if($errors->has('inscricao_municipal'))
												<div class="invalid-feedback">
													{{ $errors->first('inscricao_municipal') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-lg-3 col-md-3 col-sm-3">
											<label class="col-form-label">Casas decimais</label>
											<div class="">
												<select class="custom-select" name="casas_decimais">
													<option @isset($config) @if($config->casas_decimais == 2) selected @endif @endisset value="2">2</option>
													<option @isset($config) @if($config->casas_decimais == 3) selected @endif @endisset value="3">3</option>
													<option @isset($config) @if($config->casas_decimais == 4) selected @endif @endisset value="4">4</option>
												</select>
												@if($errors->has('casas_decimais'))
												<div class="invalid-feedback">
													{{ $errors->first('casas_decimais') }}
												</div>
												@endif
											</div>
										</div>
										<div class="form-group validated col-lg-5 col-md-5 col-sm-5">
											<label class="col-form-label">CNPJ Autorizado (opcional)</label>
											<div class="">
												<input data-mask="00.000.000/0000-00" id="aut_xml" type="text" class="form-control @if($errors->has('aut_xml')) is-invalid @endif cnpj" name="aut_xml" value="{{{ isset($config) ? $config->aut_xml : old('aut_xml') }}}">
												@if($errors->has('aut_xml'))
												<div class="invalid-feedback">
													{{ $errors->first('aut_xml') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-12 col-lg-12">
											<label class="col-form-label">Observação para NFe (opcional)</label>
											<div class="">

												<div class="row">
													<div class="col-12">
														<textarea class="form-control" name="campo_obs_nfe" id="campo_obs_nfe" >{{isset($config) ? $config->campo_obs_nfe : old('campo_obs_nfe')}}</textarea>
													</div>
												</div>

												@if($errors->has('campo_obs_nfe'))
												<div class="invalid-feedback">
													{{ $errors->first('campo_obs_nfe') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-lg-4 col-md-4 col-sm-10">
											<label class="col-form-label">Senha remover venda (opcional)</label>
											<div class="">
												<input id="senha_remover" type="password" class="form-control @if($errors->has('senha_remover')) is-invalid @endif" name="senha_remover" value="{{old('senha_remover')}}">
												@if($errors->has('senha_remover'))
												<div class="invalid-feedback">
													{{ $errors->first('senha_remover') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-4 col-lg-4 col-6">
											<label class="col-xl-12 col-lg-12 col-form-label text-left">Logo</label>
											<div class="col-lg-10 col-xl-6">

												<div class="image-input image-input-outline" id="kt_image_1">
													<div class="image-input-wrapper" @if(isset($config) && $config->logo != '')style="background-image: url(/logos/{{$config->logo}})" @else style="background-image: url(/imgs/logo.png)" @endif ></div>
													<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
														<i class="fa fa-pencil icon-sm text-muted"></i>
														<input type="file" name="file" accept=".jpg">
														<input type="hidden" name="profile_avatar_remove">
													</label>
													<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Cancel avatar">
														<i class="fa fa-close icon-xs text-muted"></i>
													</span>
												</div>

												<span class="form-text text-muted">.jpg</span>
												@if($errors->has('file'))
												<div class="invalid-feedback">
													{{ $errors->first('file') }}
												</div>
												@endif

												@if(isset($config))
												<a href="/rep/removeLogo/{{$empresa->id}}">remover logo</a>
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
								<a style="width: 100%" class="btn btn-danger" href="/clientes">
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
				</div>
			</form>
		</div>
	</div>
</div>

@endsection