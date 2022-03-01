@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">

	<div class="card-body @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">
		<div class="content d-flex flex-column flex-column-fluid" id="kt_content" >

			<div class="row" id="anime" style="display: none">
				<div class="col s8 offset-s2">
					<lottie-player src="/anime/success.json" background="transparent" speed="0.8" style="width: 100%; height: 300px;" autoplay >
					</lottie-player>
				</div>
			</div>

			<div class="col-lg-12" id="content">


				<input type="hidden" value="{{csrf_token()}}" id="_token">
				<div class="col-xl-12">

					<div class="card card-custom gutter-b">
						<div class="card-body">
							<h4 class="center-align">BOLETOS</h4>

							<div class="row">

								<div class="form-group col-lg-4 col-md-4 col-sm-6 col-6">
									<label class="col-form-label">Banco</label>
									<div class="">
										<div class="input-group">
											<select id="banco" name="banco_id" class="custom-select">
												<option value="">Selecione</option>
												@foreach($contasBancarias as $c)
												<option @if($contaPadrao != null && $contaPadrao->id == $c->id) selected @endif value="{{$c->id}}" @if(old('banco_id') == $c->id) selected @endif >{{$c->banco}} | AG:{{$c->agencia}} - C:{{$c->conta}}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>

								<input type="hidden" value="{{json_encode($contasBancarias)}}" id="contas_json">

								<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
									<label class="col-form-label">Carteira</label>
									<div class="">
										<div class="input-group">
											<input id="carteira" name="carteira" value="@if($contaPadrao != null) {{$contaPadrao->carteira}} @else {{ old('carteira') }} @endif" type="text" class="form-control @if($errors->has('carteira')) is-invalid @endif"/>
											
										</div>
									</div>
								</div>

								<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
									<label class="col-form-label">Convênio</label>
									<div class="">
										<div class="input-group">
											<input id="convenio" name="convenio" value="@if($contaPadrao != null) {{$contaPadrao->convenio}} @else {{ old('convenio') }} @endif" type="text" class="form-control @if($errors->has('convenio')) is-invalid @endif"/>
										</div>
									</div>
								</div>

								<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
									<label class="col-form-label">Tipo</label>
									<div class="">
										<div class="input-group">
											<select id="tipo" class="custom-select">
												<option @if($contaPadrao != null) @if($contaPadrao->tipo == 'Cnab400') selected @endif @endif value="Cnab400">Cnab400</option>
												<option @if($contaPadrao != null) @if($contaPadrao->tipo == 'Cnab240') selected @endif @endif value="Cnab240">Cnab240</option>
											</select>
										</div>
									</div>
								</div>

								<div class="form-group validated col-sm-4 col-lg-2">
									<label class="col-form-label">Usar logo</label>

									<div class="switch switch-outline switch-success">
										<label class="">
											<input value="true" id="usar_logo" name="logo" class="red-text" type="checkbox">
											<span class="lever"></span>
										</label>
									</div>
								</div>

								<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6 div-aux" style="display: none">
									<label class="col-form-label">Posto</label>
									<div class="">
										<div class="input-group">
											<input id="posto" name="posto" value="{{ old('posto') }}" type="text" class="form-control"/>
										</div>
									</div>
								</div>

								<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6 div-aux" style="display: none">
									<label class="col-form-label">Código do cliente</label>
									<div class="">
										<div class="input-group">
											<input id="codigo_cliente" name="codigo_cliente" type="text" class="form-control"/>
										</div>
									</div>
								</div>

							</div>

							<input type="hidden" id="arrJson" value="{{json_encode($arrJson)}}">

							@foreach($contas as $c)
							<div class="row">
								<div class="col-xl-12">

									<div class="card card-custom gutter-b">
										<div class="card-body">
											<div class="row">
												<div class="form-group col-12 col-lg-6">
													<h5>Razão Social: <strong class="text-success">{{$c->getCliente()->razao_social}}</strong></h5>
													<h5>CPF/CNPJ: <strong class="text-success">{{$c->getCliente()->cpf_cnpj}}</strong></h5>
													<h5>Cidade: <strong class="text-success">{{$c->getCliente()->cidade->nome}} ({{$c->getCliente()->cidade->uf}})</strong></h5>
												</div>

												<div class="form-group col-12 col-lg-6">
													<h5>Valor: <strong class="text-info">R$ {{number_format($c->valor_integral, 2, ',', '.')}}</strong></h5>
													<h5>Vencimento: <strong class="text-info">{{ \Carbon\Carbon::parse($c->data_vencimento)->format('d/m/Y')}}</strong></h5>

												</div>
											</div>

											<div class="row">
												<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
													<label class="col-form-label">Nº do boleto</label>
													<div class="">
														<div class="input-group">
															<input id="numero_boleto_{{$c->id}}" type="text" class="form-control"/>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
													<label class="col-form-label">Nº do documento</label>
													<div class="">
														<div class="input-group">
															<input id="numero_documento_{{$c->id}}" type="text" class="form-control"/>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
													<label class="col-form-label">Juros</label>
													<div class="">
														<div class="input-group">
															<input value="@if($contaPadrao != null) {{$contaPadrao->juros}} @else {{ old('juros') }} @endif" id="juros_{{$c->id}}" type="text" class="form-control money"/>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
													<label class="col-form-label">Multa</label>
													<div class="">
														<div class="input-group">
															<input value="@if($contaPadrao != null) {{$contaPadrao->multa}} @else {{ old('multa') }} @endif" id="multa_{{$c->id}}" type="text" class="form-control money"/>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
													<label class="col-form-label">
														Juros após (dias)
													</label>
													<div class="">
														<div class="input-group">
															<input value="@if($contaPadrao != null) {{$contaPadrao->juros_apos}} @else {{ old('juros_apos') }} @endif" id="multa_{{$c->id}}" id="juros_apos_{{$c->id}}" type="text" class="form-control"/>
														</div>
													</div>
												</div>
											</div>

										</div>
									</div>
								</div>
							</div>
							@endforeach
						</div>
					</div>

					<div class="row alerts">

					</div>

					<div class="card-footer">
						<div class="row">
							<div class="col-lg-3 col-sm-6 col-md-4">
								<button disabled id="salvar" style="width: 100%" type="button" class="btn btn-success spinner-white spinner-right">
									<i class="la la-check"></i>
									<span class="">Salvar</span>
								</button>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>

@section('javascript')

<script type="text/javascript">
	var CONTAS = [];
	var ENVIO = [];
	var ISSICREDI = false;

	$(function () {
		ENVIO = JSON.parse($('#arrJson').val());
		CONTAS = JSON.parse($('#contas_json').val());

		console.log(ENVIO)
	})

	$('#banco').change(() => {
		console.log("valida banco")
		verificaBanco();
	})

	function verificaBanco(){
		$('#posto').val('')
		$('#codigo_cliente').val('')
		$('.div-aux').css('display', 'none')
		let banco = $('#banco').val();
		CONTAS.map((c) => {
			if(banco == c.id){
				console.log(c.banco)
				if(c.banco == 'Sicredi' || c.banco == 'Caixa Econônica Federal' || c.banco == 'Santander'){
					ISSICREDI = true;
					$('.div-aux').css('display', 'block')
				}
			}else{
				ISSICREDI = false;
			}
		})
	}

	$('.form-control').keyup(() => {
		validaCampos();
	})

	$('.custom-select').change(() => {
		validaCampos();
	})

	function validaCampos(){
		console.log("validando")
		let valida = [];
		ENVIO.map((c) => {
			let m = validaConta(c)
			if(m != ""){
				valida.push(m)
			}
		})

		let formError = []
		if(!$('#banco').val()){
			formError.push("Informe o banco")
		}
		if(!$('#carteira').val()){
			formError.push("Informe a carteira")
		}
		if(!$('#convenio').val()){
			formError.push("Informe o convênio")
		}
		if(ISSICREDI){
			if(!$('#posto').val()){
				formError.push("Informe o posto para sicredi")
			}
			if(!$('#codigo_cliente').val()){
				formError.push("Informe o código do cliente para sicredi")
			}
		}

		if(valida.length > 0 || formError.length > 0){
			montaAlertas(valida, formError);
		}else{
			$('.alerts').html('')
			$('#salvar').removeAttr('disabled')
		}
	}

	function validaConta(c){

		if(!$('#numero_boleto_'+c.id).val()){ 
			console.log('numero_boleto_')
			return "Valide os campos do boleto "+(c.cont);
		}
		if(!$('#numero_documento_'+c.id).val()){
			console.log('numero_documento_')
			return "Valide os campos do boleto "+(c.cont);
		}
		// if(!$('#juros_'+c.id).val()){
		// 	console.log('juros_')
		// 	return "Valide os campos do item "+(c.id+1);
		// }
		// if(!$('#multa_'+c.id).val()){
		// 	console.log('multa_')
		// 	return "Valide os campos do item "+(c.id+1);
		// }
		// if(!$('#juros_apos_'+c.id).val()){
		// 	console.log('juros_apos_')
		// 	return "Valide os campos do item "+(c.id+1);
		// }
		return "";
	}

	$('#salvar').click(() => {
		$('#salvar').attr('disabled', true);
		console.log("salvando...")
		$('#salvar').addClass('spinner')
		preparaObjeto((res) => {
			let objeto = {
				contas: res,
				banco: $('#banco').val(),
				carteira: $('#carteira').val(),
				convenio: $('#convenio').val(),
				usar_logo: $('#usar_logo').is(':checked'),
				posto: $('#posto').val(),
				codigo_cliente: $('#codigo_cliente').val(),
				tipo: $('#tipo').val()
			}

			console.log(objeto)
			console.log($('#_token').val())

			$.post(path+'boleto/gerarStoreMulti',
			{
				_token: $('#_token').val(),
				objeto: objeto
			}
			).done((success) => {
				console.log(success)
				sucesso()

			}).fail((err) => {
				$('#salvar').removeAttr('disabled');
				console.log(err)
				swal("Erro no formulário", err.responseJSON.mensagem, "error")
				montaAlertasAux(err.responseJSON.mensagem)
				$('#salvar').removeClass('spinner')

			})
		})
	})

	function montaAlertas(valida, formError){
		let html = ''
		$('#salvar').attr('disabled', true)
		valida.map((v) => {
			html += '<div class="alert alert-custom alert-light-danger fade show col-12" role="alert"><div class="alert-icon"><i class="la la-warning"></i></div>'
			+'<div class="alert-text">'+v+'</div>'
			+'<div class="alert-close">'
			+'<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
			+'<span aria-hidden="true"><i class="la la-close"></i></span>'
			+'</button></div></div>';

		});

		formError.map((v) => {
			html += '<div class="alert alert-custom alert-light-danger fade show col-12" role="alert"><div class="alert-icon"><i class="la la-warning"></i></div>'
			+'<div class="alert-text">'+v+'</div>'
			+'<div class="alert-close">'
			+'<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
			+'<span aria-hidden="true"><i class="la la-close"></i></span>'
			+'</button></div></div>';

		});
		$('.alerts').html(html);

	}

	function montaAlertasAux(erro){
		let html = ''
		html += '<div class="alert alert-custom alert-light-danger fade show col-12" role="alert"><div class="alert-icon"><i class="la la-warning"></i></div>'
		+'<div class="alert-text">'+erro+'</div>'
		+'<div class="alert-close">'
		+'<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
		+'<span aria-hidden="true"><i class="la la-close"></i></span>'
		+'</button></div></div>';

		$('.alerts').html(html);

	}

	function preparaObjeto(call){
		let array = [];
		ENVIO.map((c) => {
			let js = addJson(c)
			array.push(js)
		})
		call(array)
	}

	function addJson(c){
		let js = {
			'numero_boleto': $('#numero_boleto_'+c.id).val(),
			'numero_documento': $('#numero_documento_'+c.id).val(),
			'juros': $('#juros_'+c.id).val() ? $('#juros_'+c.id).val() : 0,
			'multa': $('#multa_'+c.id).val() ? $('#multa_'+c.id).val() : 0,
			'juros_apos': $('#juros_apos_'+c.id).val() ? $('#juros_apos_'+c.id).val() : 0,
			'id': c.id
		}
		return js;
	}

	function sucesso(){
		$('#content').css('display', 'none');
		$('#anime').css('display', 'block');
		setTimeout(() => {
			location.href = path+'contasReceber';
		}, 4000)
	}


</script>

@endsection

@endsection
