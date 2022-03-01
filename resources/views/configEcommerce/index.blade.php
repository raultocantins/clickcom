@extends('default.layout')
@section('content')

<style type="text/css">
	.img-template img{
		width: 300px;
		border: 1px solid #999;
		border-radius: 10px;
	}

	.img-template-active img{
		width: 300px;
		border: 3px solid green;
		border-radius: 10px;
	}

	.template:hover{
		cursor: pointer;
	}

	#btn_token:hover{
		cursor: pointer;
	}
</style>

<div class="content d-flex flex-column flex-column-fluid @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft" id="kt_content">

	<div class="container">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="/configEcommerce/save" enctype="multipart/form-data">
					<input type="hidden" name="id" value="{{{ isset($config->id) ? $config->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{{ isset($config) ? "Editar": "Cadastrar" }}} Configuração de Ecommerce</h3>
						</div>

					</div>
					@csrf
					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">

									<div class="row">
										<div class="form-group validated col-sm-6 col-lg-6 col-12">
											<label class="col-form-label">Nome exibição</label>
											<div class="">
												<input id="nome" type="text" class="form-control @if($errors->has('nome')) is-invalid @endif" name="nome" value="{{{ isset($config) ? $config->nome : old('nome') }}}">
												@if($errors->has('nome'))
												<div class="invalid-feedback">
													{{ $errors->first('nome') }}
												</div>
												@endif
												<p class="text-danger">Exemplo Loja Slym</p>
											</div>
										</div>

										<div class="form-group validated col-sm-6 col-lg-6 col-12">
											<label class="col-form-label">Link</label>
											<div class="">
												<input id="link" type="text" class="form-control @if($errors->has('link')) is-invalid @endif" name="link" value="{{{ isset($config) ? $config->link : old('link') }}}">
												@if($errors->has('link'))
												<div class="invalid-feedback">
													{{ $errors->first('link') }}
												</div>
												@endif
												<p class="text-danger">Exemplo slym</p>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-4 col-lg-4 col-12">
											<label class="col-form-label">Link do Facebook</label>
											<div class="">
												<input id="link_facebook" type="text" class="form-control @if($errors->has('link_facebook')) is-invalid @endif" name="link_facebook" value="{{{ isset($config) ? $config->link_facebook : old('link_facebook') }}}">
												@if($errors->has('link_facebook'))
												<div class="invalid-feedback">
													{{ $errors->first('link_facebook') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4 col-12">
											<label class="col-form-label">Link do Twiter</label>
											<div class="">
												<input id="link_twiter" type="text" class="form-control @if($errors->has('link_twiter')) is-invalid @endif" name="link_twiter" value="{{{ isset($config) ? $config->link_twiter : old('link_twiter') }}}">
												@if($errors->has('link_twiter'))
												<div class="invalid-feedback">
													{{ $errors->first('link_twiter') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4 col-12">
											<label class="col-form-label">Link do Instagram</label>
											<div class="">
												<input id="link_instagram" type="text" class="form-control @if($errors->has('link_instagram')) is-invalid @endif" name="link_instagram" value="{{{ isset($config) ? $config->link_instagram : old('link_instagram') }}}">
												@if($errors->has('link_instagram'))
												<div class="invalid-feedback">
													{{ $errors->first('link_instagram') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-8 col-lg-8 col-12">
											<label class="col-form-label">Rua</label>
											<div class="">
												<input id="rua" type="text" class="form-control @if($errors->has('rua')) is-invalid @endif" name="rua" value="{{{ isset($config) ? $config->rua : old('rua') }}}">
												@if($errors->has('rua'))
												<div class="invalid-feedback">
													{{ $errors->first('rua') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-2 col-lg-2 col-12">
											<label class="col-form-label">Número</label>
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

										<div class="form-group validated col-sm-4 col-lg-4 col-12">
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

										<div class="form-group validated col-sm-4 col-lg-4 col-12">
											<label class="col-form-label">Cidade</label>
											<div class="">
												<input id="cidade" type="text" class="form-control @if($errors->has('cidade')) is-invalid @endif" name="cidade" value="{{{ isset($config) ? $config->cidade : old('cidade') }}}">
												@if($errors->has('cidade'))
												<div class="invalid-feedback">
													{{ $errors->first('cidade') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-2 col-6">
											<label class="col-form-label">UF</label>
											<div class="">
												<input data-mask="AA" id="uf" type="text" class="form-control @if($errors->has('uf')) is-invalid @endif" name="uf" value="{{{ isset($config) ? $config->uf : old('uf') }}}">
												@if($errors->has('uf'))
												<div class="invalid-feedback">
													{{ $errors->first('uf') }}
												</div>
												@endif
											</div>
										</div>

									</div>

									<div class="row">

										<div class="form-group validated col-sm-4 col-lg-4 col-12">
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


										<div class="form-group validated col-sm-4 col-lg-4 col-12">
											<label class="col-form-label">Email</label>
											<div class="">
												<input id="email" type="text" class="form-control @if($errors->has('email')) is-invalid @endif" name="email" value="{{{ isset($config) ? $config->email : old('email') }}}">
												@if($errors->has('email'))
												<div class="invalid-feedback">
													{{ $errors->first('email') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4 col-12">
											<label class="col-form-label">Telefone</label>
											<div class="">
												<input data-mask="00 00000-0000" data-mask-reverse="true" type="text" class="form-control @if($errors->has('telefone')) is-invalid @endif" name="telefone" value="{{{ isset($config) ? $config->telefone : old('telefone') }}}">
												@if($errors->has('telefone'))
												<div class="invalid-feedback">
													{{ $errors->first('telefone') }}
												</div>
												@endif
											</div>
										</div>
									</div>


									<div class="row">

										<div class="form-group validated col-sm-4 col-lg-4 col-6">
											<label class="col-form-label">Latitude</label>
											<div class="">
												<input id="latitude" type="text" class="form-control @if($errors->has('latitude')) is-invalid @endif" name="latitude" value="{{{ isset($config) ? $config->latitude : old('latitude') }}}">
												@if($errors->has('latitude'))
												<div class="invalid-feedback">
													{{ $errors->first('latitude') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4 col-6">
											<label class="col-form-label">Longitude</label>
											<div class="">
												<input id="longitude" type="text" class="form-control @if($errors->has('longitude')) is-invalid @endif" name="longitude" value="{{{ isset($config) ? $config->longitude : old('longitude') }}}">
												@if($errors->has('longitude'))
												<div class="invalid-feedback">
													{{ $errors->first('longitude') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4 col-6">
											<label class="col-form-label">Frete gratis a partir de:</label>
											<div class="">
												<input id="frete_gratis_valor" type="text" class="form-control @if($errors->has('frete_gratis_valor')) is-invalid @endif money" name="frete_gratis_valor" value="{{{ isset($config) ? $config->frete_gratis_valor : old('frete_gratis_valor') }}}">
												@if($errors->has('frete_gratis_valor'))
												<div class="invalid-feedback">
													{{ $errors->first('frete_gratis_valor') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">

										<div class="form-group validated col-sm-6 col-lg-6 col-12">
											<label class="col-form-label">Mercado pago public key</label>
											<div class="">
												<input id="mercadopago_public_key" type="text" class="form-control @if($errors->has('mercadopago_public_key')) is-invalid @endif" name="mercadopago_public_key" value="{{{ isset($config) ? $config->mercadopago_public_key : old('mercadopago_public_key') }}}">
												@if($errors->has('mercadopago_public_key'))
												<div class="invalid-feedback">
													{{ $errors->first('mercadopago_public_key') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-6 col-lg-6 col-12">
											<label class="col-form-label">Mercado pago access token</label>
											<div class="">
												<input id="mercadopago_access_token" type="text" class="form-control @if($errors->has('mercadopago_access_token')) is-invalid @endif" name="mercadopago_access_token" value="{{{ isset($config) ? $config->mercadopago_access_token : old('mercadopago_access_token') }}}">
												@if($errors->has('mercadopago_access_token'))
												<div class="invalid-feedback">
													{{ $errors->first('mercadopago_access_token') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">

										<div class="form-group validated col-sm-6 col-lg-6 col-12">
											<label class="col-form-label">Google maps Api</label>
											<div class="">
												<input id="google_api" type="text" class="form-control @if($errors->has('google_api')) is-invalid @endif" name="google_api" value="{{{ isset($config) ? $config->google_api : old('google_api') }}}">
												@if($errors->has('google_api'))
												<div class="invalid-feedback">
													{{ $errors->first('google_api') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-6 col-lg-6 col-12">
											<label class="col-form-label">Habilitar retira na loja</label>
											<div class="">
												<span class="switch switch-outline switch-info">
													<label>
														<input value="true" @if(isset($config) && $config->habilitar_retirada) checked @endif type="checkbox" name="habilitar_retirada" id="habilitar_retirada">
														<span></span>
													</label>
												</span>
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4 col-6">
											<label class="col-form-label">% Desconto padrão boleto</label>
											<div class="">
												<input id="desconto_padrao_boleto" type="text" class="form-control @if($errors->has('desconto_padrao_boleto')) is-invalid @endif money" name="desconto_padrao_boleto" value="{{{ isset($config) ? $config->desconto_padrao_boleto : old('desconto_padrao_boleto') }}}">
												@if($errors->has('desconto_padrao_boleto'))
												<div class="invalid-feedback">
													{{ $errors->first('desconto_padrao_boleto') }}
												</div>
												@endif
											</div>
										</div>
										<div class="form-group validated col-sm-4 col-lg-4 col-6">
											<label class="col-form-label">% Desconto padrão PIX</label>
											<div class="">
												<input id="desconto_padrao_pix" type="text" class="form-control @if($errors->has('desconto_padrao_pix')) is-invalid @endif money" name="desconto_padrao_pix" value="{{{ isset($config) ? $config->desconto_padrao_pix : old('desconto_padrao_pix') }}}">
												@if($errors->has('desconto_padrao_pix'))
												<div class="invalid-feedback">
													{{ $errors->first('desconto_padrao_pix') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4 col-6">
											<label class="col-form-label">% Desconto padrão cartão</label>
											<div class="">
												<input id="desconto_padrao_cartao" type="text" class="form-control @if($errors->has('desconto_padrao_cartao')) is-invalid @endif money" name="desconto_padrao_cartao" value="{{{ isset($config) ? $config->desconto_padrao_cartao : old('desconto_padrao_cartao') }}}">
												@if($errors->has('desconto_padrao_cartao'))
												<div class="invalid-feedback">
													{{ $errors->first('desconto_padrao_cartao') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">

										<div class="form-group validated col-sm-12 col-lg-12 col-12">
											<label class="col-form-label">Descreva o funcionamento</label>
											<div class="">
												<input id="funcionamento" type="text" class="form-control @if($errors->has('funcionamento')) is-invalid @endif" name="funcionamento" value="{{{ isset($config) ? $config->funcionamento : old('funcionamento') }}}">
												@if($errors->has('funcionamento'))
												<div class="invalid-feedback">
													{{ $errors->first('funcionamento') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-12 col-lg-12 col-12">
											<label class="col-form-label">Politica de privacidade</label>
											<div class="">
												<textarea class="form-control" name="politica_privacidade" placeholder="Politica de privacidade" rows="3">{{{ isset($config->politica_privacidade) ? $config->politica_privacidade : old('politica_privacidade') }}}</textarea>
												@if($errors->has('politica_privacidade'))
												<div class="invalid-feedback">
													{{ $errors->first('politica_privacidade') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-4 col-lg-4 col-6">
											<label class="col-xl-12 col-lg-12 col-form-label text-left">Imagem</label>
											<div class="col-lg-10 col-xl-6">

												<div class="image-input image-input-outline" id="kt_image_1">
													<div class="image-input-wrapper"
													@if($config == null || $config->logo == "") 
													style="background-image: url(/ecommerce/logo.png)" @else style="background-image: url(/ecommerce/logos/{{$config->logo}})" @endif ></div>
													<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
														<i class="fa fa-pencil icon-sm text-muted"></i>
														<input type="file" name="file" accept=".png">
														<input type="hidden" name="profile_avatar_remove">
													</label>
													<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Cancel avatar">
														<i class="fa fa-close icon-xs text-muted"></i>
													</span>
												</div>


												<span class="form-text text-muted">.png</span>
												@if($errors->has('file'))
												<div class="invalid-feedback">
													{{ $errors->first('file') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-12 col-lg-12 col-12">
											<label class="col-form-label">Iframe maps</label>
											<div class="">
												<textarea class="form-control" name="src_mapa" placeholder="Iframe maps" rows="3">{{{ isset($config->src_mapa) ? $config->src_mapa : old('src_mapa') }}}</textarea>
												@if($errors->has('src_mapa'))
												<div class="invalid-feedback">
													{{ $errors->first('src_mapa') }}
												</div>
												@endif
											</div>
											<p class="text-danger">* Somente o src do iframe</p>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-4 col-lg-4 col-12">
											<label class="col-form-label">Usar ecommerce Api</label>
											<div class="">
												<span class="switch switch-outline switch-success">
													<label>
														<input value="true" @if(isset($config) && $config->usar_api) checked @endif type="checkbox" name="usar_api" id="usar_api">
														<span></span>
													</label>
												</span>
											</div>
										</div>

										<div class="form-group validated col-sm-5 col-lg-5 col-6 use-api @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft" style="display: none">
											<label class="col-form-label">Api token</label>
											<div class="input-group">
												
												<input readonly id="api_token" type="text" class="form-control @if($errors->has('api_token')) is-invalid @endif" name="api_token" value="{{{ isset($config) ? $config->api_token : old('api_token') }}}">
												<div class="input-group-prepend">
													<span class="input-group-text" id="btn_token">
														<li class="la la-refresh"></li>
													</span>
												</div>
												@if($errors->has('api_token'))
												<div class="invalid-feedback">
													{{ $errors->first('api_token') }}
												</div>
												@endif

											</div>
										</div>


										<div class="form-group validated col-sm-12 col-lg-12 col-12 use-api @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft" style="visibility: hidden">
											<label class="col-form-label">Mensagem de agradecimento</label>
											<div class="">
												<textarea class="form-control mensagem_agradecimento" name="mensagem_agradecimento" placeholder="Mensagem de agradecimento" id="mensagem_agradecimento" rows="5">{{{ isset($config) ? $config->mensagem_agradecimento : old('mensagem_agradecimento') }}}</textarea>
												@if($errors->has('mensagem_agradecimento'))
												<div class="invalid-mensagem_agradecimento">
													{{ $errors->first('mensagem_agradecimento') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group col-sm-3 col-lg-3 col-6 use-api @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
											<label for="example-color-input" class=" col-form-label">Cor do fundo</label>
											<div class="">
												<input name="cor_fundo" class="form-control" type="color" value="{{{ isset($config) ? $config->cor_fundo : old('cor_fundo') }}}" id="example-color-input"/>
											</div>
										</div>

										<div class="form-group col-sm-3 col-lg-3 col-6 use-api @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
											<label for="example-color-input" class=" col-form-label">Cor do botão</label>
											<div class="">
												<input name="cor_btn" class="form-control" type="color" value="{{{ isset($config) ? $config->cor_btn : old('cor_btn') }}}" id="example-color-input"/>
											</div>
										</div>

										<div class="form-group @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft validated col-sm-4 col-lg-4 col-6 use-api" style="display: none">
											<label class="col-form-label">Timer do carrossel</label>
											<div class="">
												<input id="timer_carrossel" type="text" class="form-control @if($errors->has('timer_carrossel')) is-invalid @endif" name="timer_carrossel" value="{{{ isset($config) ? $config->timer_carrossel : old('timer_carrossel') }}}">
												@if($errors->has('timer_carrossel'))
												<div class="invalid-feedback">
													{{ $errors->first('timer_carrossel') }}
												</div>
												@endif
											</div>
										</div>


										<div class="form-group validated col-sm-4 col-lg-4 col-6 use-api @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft" style="display: none">
											<label class="col-xl-12 col-lg-12 col-form-label text-left">Imagem tela contato</label>
											<div class="col-lg-10 col-xl-6">

												<div class="image-input image-input-outline" id="kt_image_2">
													<div class="image-input-wrapper"
													@if($config == null || $config->img_contato == "") 
													style="background-image: url(/imgs/default.png)" @else style="background-image: url(/ecommerce/assets/img/contato/{{$config->img_contato}})" @endif ></div>
													<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
														<i class="fa fa-pencil icon-sm text-muted"></i>
														<input type="file" name="img_contato" accept=".png">
														<input type="hidden" name="profile_avatar_remove">
													</label>
													<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Cancel avatar">
														<i class="fa fa-close icon-xs text-muted"></i>
													</span>
												</div>


												<span class="form-text text-muted">.png</span>
												@if($errors->has('img_contato'))
												<div class="invalid-feedback">
													{{ $errors->first('img_contato') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4 col-6 use-api @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft" style="display: none">
											<label class="col-xl-12 col-lg-12 col-form-label text-left">Favicon</label>
											<div class="col-lg-10 col-xl-6">

												<div class="image-input image-input-outline" id="kt_image_3">
													<div class="image-input-wrapper"
													@if($config == null || $config->fav_icon == "") 
													style="background-image: url(/imgs/default.png)" @else style="background-image: url(/ecommerce/assets/img/favicon/{{$config->fav_icon}})" @endif ></div>
													<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
														<i class="fa fa-pencil icon-sm text-muted"></i>
														<input type="file" name="fav_icon" accept=".png">
														<input type="hidden" name="profile_avatar_remove">
													</label>
													<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Cancel avatar">
														<i class="fa fa-close icon-xs text-muted"></i>
													</span>
												</div>


												<span class="form-text text-muted">.png</span>
												@if($errors->has('img_contato'))
												<div class="invalid-feedback">
													{{ $errors->first('img_contato') }}
												</div>
												@endif
											</div>
										</div>

									</div>

									<div class="row">
										<div class="col-12 no-api">
											<p>Selecione o tema:</p>
										</div>
										<div class="form-group validated col-sm-6 col-lg-6 col-12 template no-api">

											<div id="template1" onclick="selectTemplate(1)" @if(isset($config) && $config->tema_ecommerce == 'ecommerce') class="img-template-active" @else class="img-template" @endif>
												<img src="/ecommerce/template1.png">
											</div>
										</div>

										<div class="form-group validated col-sm-6 col-lg-6 col-12 template no-api">

											<div id="template2" onclick="selectTemplate(2)" @if(isset($config) && $config->tema_ecommerce == 'ecommerce_one_tech') class="img-template-active" @else class="img-template" @endif>
												<img src="/ecommerce/template2.png">
											</div>
										</div>
									</div>

									<input type="hidden" value="{{{ isset($config) ? $config->tema_ecommerce : old('tema_ecommerce') }}}" name="tema_ecommerce" id="tema_ecommerce">


									<div @if(isset($config) && $config->tema_ecommerce == 'ecommerce_one_tech') style="visibility: hidden" @endif class="form-group row cor">
										<label for="example-color-input" class="col-2 col-form-label">Cor principal</label>
										<div class="col-10">
											<input name="cor_principal" class="form-control" type="color" value="{{{ isset($config) ? $config->cor_principal : old('cor_principal') }}}" id="example-color-input"/>
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
				</form>
			</div>
		</div>
	</div>
</div>

@section('javascript')
<script type="text/javascript">
	$(function () {
		usarApi()
	})
	function selectTemplate(id){
		if(id == 1){
			$('.cor').css('visibility', 'visible')

			$('#template1').addClass('img-template-active')
			$('#template2').removeClass('img-template-active')
			$('#template2').addClass('img-template')

			$('#tema_ecommerce').val('ecommerce')
		}else if(id == 2){
			$('.cor').css('visibility', 'hidden')
			$('#template1').removeClass('img-template-active')
			$('#template1').addClass('img-template')

			$('#template2').addClass('img-template-active')
			$('#tema_ecommerce').val('ecommerce_one_tech')

		}
	}

	$('#btn_token').click(() => {
		let token = generate_token(25);

		swal({
			title: "Atenção",
			text: "Esse token é o responsavel pela comunicação com o ecommerce, tenha atenção!!",
			icon: "warning",
			buttons: true,
			dangerMode: true,
		}).then((confirmed) => {
			if (confirmed) {
				$('#api_token').val(token)
			}
		});

	})

	function generate_token(length){

		var a = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890".split("");
		var b = [];  
		for (var i=0; i<length; i++) {
			var j = (Math.random() * (a.length-1)).toFixed(0);
			b[i] = a[j];
		}
		return b.join("");
	}

	$('#usar_api').change(() => {
		usarApi()
	})

	function usarApi(){
		let usarApi = $('#usar_api').is(":checked");
		if(usarApi){
			$('.use-api').css('display', 'inline-block')
			$('.use-api').css('visibility', 'visible')
			$('.no-api').css('display', 'none')
		}else{
			$('.use-api').css('display', 'none')
			$('.no-api').css('display', 'inline-block')
		}
	}
</script>
@endsection
@endsection