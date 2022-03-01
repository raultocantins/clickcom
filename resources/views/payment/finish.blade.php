@extends('default.layout')
@section('content')

<style type="text/css">
	.card-stretch:hover{
		cursor: pointer;
	}
</style>
<div class="card card-custom gutter-b">

	<div class="card-body @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

		<div class="card card-custom gutter-b">


			<div class="card-body">

				<div class="row">
					<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">

						<div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="between" data-wizard-clickable="true">
							<!--begin: Wizard Nav-->

							<div class="wizard-nav">

								<div class="wizard-steps px-8 py-8 px-lg-15 py-lg-3">
									<!--begin::Wizard Step 1 Nav-->
									<div class="wizard-step" data-wizard-type="step" data-wizard-state="done">
										<div class="wizard-label">
											<h3 class="wizard-title">
												<span>
													CARTÃO DE CRÉDITO
												</span>
											</h3>
											<div class="wizard-bar"></div>
										</div>
									</div>
									<!--end::Wizard Step 1 Nav-->
									<!--begin::Wizard Step 2 Nav-->
									<div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
										<div class="wizard-label">
											<h3 class="wizard-title">
												<span>
													BOLETO
												</span>
											</h3>
											<div class="wizard-bar"></div>
										</div>
									</div>

									<div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
										<div class="wizard-label">
											<h3 class="wizard-title">
												<span>
													PIX
												</span>
											</h3>
											<div class="wizard-bar"></div>
										</div>
									</div>


								</div>
							</div>

							<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">

								<!--begin: Wizard Form-->
								
								<!--begin: Wizard Step Cartao-->
								<div class="pb-5" data-wizard-type="step-content">

									<form action="/payment/paymentCard" method="post" id="paymentForm">
										@csrf
										<div class="row">
											<div class="form-group col-lg-6 col-md-8 col-12">
												<label class="col-form-label">Titular do cartão</label>
												<div class="">
													<div class="input-group">
														<input type="text" class="form-control" id="cardholderName" data-checkout="cardholderName"/>
													</div>
												</div>
											</div>

											<div class="form-group col-lg-2 col-md-8 col-12">
												<label class="col-form-label">Tipo de documento</label>
												<div class="">
													<div class="input-group">
														<select class="custom-select" data-checkout="docType" name="docType" id="docType">
														</select>
													</div>
												</div>
											</div>

											<div class="form-group col-lg-3 col-md-8 col-12">
												<label class="col-form-label">Número do documento</label>
												<div class="">
													<div class="input-group">
														<input type="text" class="form-control cpf" data-checkout="docNumber" name="docNumber" id="docNumber"/>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="form-group col-lg-4 col-md-8 col-12">
												<label class="col-form-label">Email</label>
												<div class="">
													<div class="input-group">
														<input type="email" class="form-control" id="email" name="email"/>
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="form-group col-lg-6 col-md-8 col-12">
												<label class="col-form-label">Número do cartão</label>
												<div class="">
													<div class="input-group">
														<input type="text" class="form-control" id="cardNumber" data-checkout="cardNumber"/>

														<div class="input-group-append">
															<span class="input-group-text">
																<img id="band-img" style="width: 20px;" src="">
															</span>
														</div>
													</div>

												</div>
											</div>

											<div class="form-group col-lg-3 col-md-8 col-12">
												<label class="col-form-label">Parcelas</label>
												<div class="">
													<div class="input-group">
														<select class="custom-select" id="installments" name="installments">
														</select>
													</div>
												</div>
											</div>

											<div style="visibility: hidden" class="form-group col-lg-2 col-md-8 col-12">
												<label class="col-form-label">Banco emissor</label>
												<div class="">
													<div class="input-group">
														<select class="custom-select" id="issuer" name="issuer" data-checkout="issuer">
														</select>
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="form-group col-lg-2 col-md-4 col-6">
												<label class="col-form-label">Data de Vencimento</label>
												<div class="row">
													<div class="col-6">
														<div class="input-group">
															<input placeholder="MM" type="text" class="form-control" id="cardExpirationMonth" data-checkout="cardExpirationMonth"/>
														</div>
													</div>

													<div class="col-6">
														<div class="input-group">
															<input placeholder="AA" type="text" class="form-control" id="cardExpirationYear" data-checkout="cardExpirationYear"/>
														</div>
													</div>
												</div>
											</div>

											<div class="form-group col-lg-2 col-md-4 col-6">
												<label class="col-form-label">Código de segurança</label>
												<div class="">
													<div class="input-group">
														<input id="securityCode" data-checkout="securityCode" type="text" class="form-control"/>
													</div>
												</div>
											</div>

										</div>

										<input style="visibility: hidden" type="" name="transactionAmount" id="transactionAmount" value="{{$plano->plano->valor}}" />
										<input style="visibility: hidden" value="{{$plano->plano->nome}}" name="description">
										<input style="visibility: hidden" name="paymentMethodId" id="paymentMethodId" />
										<input type="hidden" value="{{$plano->id}}" name="plano_empresa_id">

										<div class="row">
											<div class="form-group col-lg-2 col-md-4 col-6">
												<div class="input-group">
													<button id="btn-card" style="width: 100%;" class="btn btn-info spinner-white spinner-right" type="submit">Pagar com Cartão</button>
												</div>
											</div>
										</div>

									</form>

								</div>
								<!--end: Wizard Step Cartao-->

								<!--begin: Wizard Step Boleto-->

								<div class="pb-5" data-wizard-type="step-content">
									
									<form action="/payment/paymentBoleto" method="post" id="paymentFormBoleto">
										@csrf
										<div class="row">
											<div class="form-group col-lg-3 col-md-8 col-12">
												<label class="col-form-label">Nome</label>
												<div class="">
													<div class="input-group">
														<input type="text" class="form-control" id="payerFirstName" name="payerFirstName"/>
													</div>
												</div>
											</div>

											<div class="form-group col-lg-3 col-md-8 col-12">
												<label class="col-form-label">Sobrenome</label>
												<div class="">
													<div class="input-group">
														<input type="text" class="form-control" id="payerLastName" name="payerLastName"/>
													</div>
												</div>
											</div>

											
										</div>
										<div class="row">
											<div class="form-group col-lg-4 col-md-8 col-12">
												<label class="col-form-label">Email</label>
												<div class="">
													<div class="input-group">
														<input required type="email" class="form-control" id="payerEmail" name="payerEmail"/>
													</div>
												</div>
											</div>


											<div class="form-group col-lg-2 col-md-8 col-12">
												<label class="col-form-label">Tipo de documento</label>
												<div class="">
													<div class="input-group">
														<select class="custom-select" data-checkout="docType" name="docType" id="docType2">
														</select>
													</div>
												</div>
											</div>

											<div class="form-group col-lg-3 col-md-8 col-12">
												<label class="col-form-label">Número do documento</label>
												<div class="">
													<div class="input-group">
														<input type="text" class="form-control cpf" data-checkout="docNumber" name="docNumber" id="docNumber2"/>
													</div>
												</div>
											</div>

										</div>


										<input type="hidden" value="{{$plano->id}}" name="plano_empresa_id">
										<input style="visibility: hidden" type="" name="transactionAmount" id="transactionAmount" value="{{$plano->plano->valor}}" />
										<input style="visibility: hidden" value="{{$plano->plano->nome}}" name="description">

										<div class="row">
											<div class="form-group col-lg-2 col-md-4 col-6">
												<div class="input-group">
													<button id="btn-boleto" style="width: 100%;" class="btn btn-info spinner-white spinner-right" type="submit">Pagar com Boleto</button>
												</div>
											</div>
										</div>

									</form>

								</div>
								<!--end: Wizard Step Boleto-->


								<!--begin: Wizard Step pix-->

								<div class="pb-5" data-wizard-type="step-content">
									
									<form action="/payment/paymentPix" method="post" id="paymentFormBoleto">
										@csrf
										<div class="row">
											<div class="form-group col-lg-3 col-md-8 col-12">
												<label class="col-form-label">Nome</label>
												<div class="">
													<div class="input-group">
														<input type="text" class="form-control" id="payerFirstName" required name="payerFirstName"/>
													</div>
												</div>
											</div>

											<div class="form-group col-lg-3 col-md-8 col-12">
												<label class="col-form-label">Sobrenome</label>
												<div class="">
													<div class="input-group">
														<input type="text" class="form-control" id="payerLastName" required name="payerLastName"/>
													</div>
												</div>
											</div>

										</div>

										<div class="row">
											<div class="form-group col-lg-4 col-md-8 col-12">
												<label class="col-form-label">Email</label>
												<div class="">
													<div class="input-group">
														<input required type="email" class="form-control" id="payerEmail" name="payerEmail"/>
													</div>
												</div>
											</div>

											<div class="form-group col-lg-2 col-md-8 col-12">
												<label class="col-form-label">Tipo de documento</label>
												<div class="">
													<div class="input-group">
														<select class="custom-select" data-checkout="docType" name="docType" id="docType3">
														</select>
													</div>
												</div>
											</div>

											<div class="form-group col-lg-3 col-md-8 col-12">
												<label class="col-form-label">Número do documento</label>
												<div class="">
													<div class="input-group">
														<input type="text" class="form-control" required data-checkout="docNumber" name="docNumber" id="docNumber3"/>
													</div>
												</div>
											</div>
										</div>

										<input type="hidden" value="{{$plano->id}}" name="plano_empresa_id">
										<input style="visibility: hidden" type="" name="transactionAmount" id="transactionAmount" value="{{$plano->plano->valor}}" />
										<input style="visibility: hidden" value="{{$plano->plano->nome}}" name="description">

										<div class="row">
											<div class="form-group col-lg-2 col-md-4 col-6">
												<div class="input-group">
													<button id="btn-pix" style="width: 100%;" class="btn btn-info spinner-white spinner-right" type="submit">Pagar com PIX</button>
												</div>
											</div>
										</div>

									</form>

								</div>
								<!--end: Wizard Step Boleto-->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

@section('javascript')
<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
<script type="text/javascript">
	$(function () {
		@if(getenv("MERCADOPAGO_AMBIENTE") == 'sandbox')
		window.Mercadopago.setPublishableKey('{{getenv("MERCADOPAGO_PUBLIC_KEY")}}');
		@else
		window.Mercadopago.setPublishableKey('{{getenv("MERCADOPAGO_PUBLIC_KEY_PRODUCAO")}}');
		@endif

		window.Mercadopago.getIdentificationTypes();

		setTimeout(() => {
			let s = $('#docType').html()
			console.log(s)
			$('#docType2').html(s)
			$('#docType3').html(s)
		}, 2000)
	});

	$('#docType').change(() => {
		let tp = $('#docType').val()
		if(tp == 'CNPJ'){
			$('#docNumber').mask('00000000000000', {reverse: true});
		}else{
			$('#docNumber').mask('00000000000', {reverse: true});
		}
	})

	$('#docType2').change(() => {
		let tp = $('#docType2').val()
		if(tp == 'CNPJ'){
			$('#docNumber2').mask('00000000000000', {reverse: true});
		}else{
			$('#docNumber2').mask('00000000000', {reverse: true});
		}
	})

	$('#docType3').change(() => {
		let tp = $('#docType3').val()
		if(tp == 'CNPJ'){
			$('#docNumber3').mask('00000000000000', {reverse: true});
		}else{
			$('#docNumber3').mask('00000000000', {reverse: true});
		}
	})

	$('#cardNumber').keyup(() => {
		let cardnumber = $('#cardNumber').val().replaceAll(" ", "");
		if (cardnumber.length >= 6) {
			let bin = cardnumber.substring(0,6);

			window.Mercadopago.getPaymentMethod({
				"bin": bin
			}, setPaymentMethod);
		}
	})

	function setPaymentMethod(status, response) {
		if (status == 200) {
			let paymentMethod = response[0];
			document.getElementById('paymentMethodId').value = paymentMethod.id;

			$('#band-img').attr("src", paymentMethod.thumbnail);
			getIssuers(paymentMethod.id);
		} else {
			alert(`payment method info error: ${response}`);
		}
	}

	function getIssuers(paymentMethodId) {
		window.Mercadopago.getIssuers(
			paymentMethodId,
			setIssuers
			);
	}

	function setIssuers(status, response) {
		if (status == 200) {
			let issuerSelect = document.getElementById('issuer');
			$('#issuer').html('');
			response.forEach( issuer => {
				let opt = document.createElement('option');
				opt.text = issuer.name;
				opt.value = issuer.id;
				issuerSelect.appendChild(opt);
			});

			getInstallments(
				document.getElementById('paymentMethodId').value,
				document.getElementById('transactionAmount').value,
				issuerSelect.value
				);
		} else {
			alert(`issuers method info error: ${response}`);
		}
	}

	function getInstallments(paymentMethodId, transactionAmount, issuerId){
		window.Mercadopago.getInstallments({
			"payment_method_id": paymentMethodId,
			"amount": parseFloat(transactionAmount),
			"issuer_id": parseInt(issuerId)
		}, setInstallments);
	}

	function setInstallments(status, response){
		if (status == 200) {
			document.getElementById('installments').options.length = 0;
			response[0].payer_costs.forEach( payerCost => {
				console.log(payerCost)
				let opt = document.createElement('option');
				opt.text = payerCost.recommended_message;
				opt.value = payerCost.installments;
				document.getElementById('installments').appendChild(opt);
			});
		} else {
			alert(`installments method info error: ${response}`);
		}
	}

	doSubmit = false;
	document.getElementById('paymentForm').addEventListener('submit', getCardToken);
	function getCardToken(event){
		event.preventDefault();
		if(!doSubmit){
			let $form = document.getElementById('paymentForm');
			window.Mercadopago.createToken($form, setCardTokenAndPay);
			return false;
		}
	};

	function setCardTokenAndPay(status, response) {
		if (status == 200 || status == 201) {
			let form = document.getElementById('paymentForm');
			let card = document.createElement('input');
			card.setAttribute('name', 'token');
			card.setAttribute('type', 'hidden');
			card.setAttribute('value', response.id);
			console.log(card)
			form.appendChild(card);
			doSubmit=true;
			spinnerButtons();

			form.submit();
		} else {
			alert("Verify filled data!\n"+JSON.stringify(response, null, 4));
		}
	};

	$('#cardExpirationMonth').keyup(() => {
		let c = $('#cardExpirationMonth').val();
		if(c.length == 2){
			$('#cardExpirationYear').focus()
		}	
	})

	$('#cardExpirationYear').keyup(() => {
		let c = $('#cardExpirationYear').val();
		if(c.length == 2){
			$('#securityCode').focus()
		}	
	})

	function spinnerButtons(){
		$('#btn-card').attr('disabled', true)
		$('#btn-card').addClass('disabled')
		$('#btn-card').addClass('spinner')

		$('#btn-boleto').attr('disabled', true)
		$('#btn-boleto').addClass('disabled')
		$('#btn-boleto').addClass('spinner')

		$('#btn-pix').attr('disabled', true)
		$('#btn-pix').addClass('disabled')
		$('#btn-pix').addClass('spinner')
	}

	$('#btn-pix').click(() => {
		$('#btn-pix').addClass('disabled')
		$('#btn-pix').addClass('spinner')
	})

	$('#btn-boleto').click(() => {
		$('#btn-boleto').addClass('disabled')
		$('#btn-boleto').addClass('spinner')
	})

</script>

@endsection
@endsection
