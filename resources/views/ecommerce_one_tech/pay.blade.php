@extends('ecommerce_one_tech.default')
@section('content')

<style type="text/css">
	.pays:hover{
		cursor: pointer;
	}
	.pays{
		font-size: 20px;
	}
	.active{
		border-bottom: 4px solid red;
	}
	.form-control{
		color: #000;
		margin-top: 5px;
		margin-bottom: 15px;
	}
	@keyframes spinner-border {
		to { transform: rotate(360deg); }
	} 
	.spinner-border{
		display: inline-block;
		width: 2rem;
		height: 2rem;
		vertical-align: text-bottom;
		border: .25em solid currentColor;
		border-right-color: transparent;
		border-radius: 50%;
		-webkit-animation: spinner-border .75s linear infinite;
		animation: spinner-border .75s linear infinite;
		margin-top: 5px;
		/*position: flex;*/
	}

</style>

<div class="cart_section">
	<div class="container">
		<div class="row">
			<div class="col-lg-10 offset-lg-1">
				<div class="cart_container">
					<div class="cart_title"><center>Formas de Pagamento</center></div>
					<div class="cart_title">
						<input type="hidden" id="totais" value="{{json_encode($totais)}}" name="">

						<center class="total-h2">
							R$ {{number_format($total, 2, ',', '.')}}
						</center>
					</div>

					<br>
					<div class="row pays">
						<div class="col-lg-2"></div>
						<div class="col-lg-3 col-4 div-cartao active">
							<center id="click-cartao">Cartão de Crédito</center>
						</div>
						<div class="col-lg-3 col-4 div-pix">
							<center id="click-pix">PIX</center>
						</div>
						<div class="col-lg-3 col-4 div-boleto">
							<center id="click-boleto">Boleto</center>
						</div>
					</div>


					<div class="row">
						<div class="col-12 mix cartao">
							<br>
							<div class="row">
								<div class="col-lg-12">
									<h3>Pagamento com Cartão</h3>
								</div>
							</div>
							<br>


							<form action="/ecommercePay/cartao" method="post" id="paymentForm">
								@csrf
								<div class="row">
									<div class="col-lg-4 col-12">

										<div class="checkout__input">

											<span>Titular do cartão*</span>
											<input class="form-control" value="{{ old('cardholderName') }}" id="cardholderName" data-checkout="cardholderName" type="text">
											@if($errors->has('cardholderName'))
											<label class="text-danger">
												{{ $errors->first('cardholderName') }}
											</label>
											@endif
										</div>
									</div>

									<div class="col-lg-2 col-6">
										<div class="checkout__input">

											<span>Tipo de documento</span>

											<select style="margin-left: 0px;" class="form-control" style="" name="docType" id="docType" data-checkout="docType">
											</select>
										</div>
									</div>

									<div class="col-lg-4 col-12">

										<div class="checkout__input">

											<span>Número do documento*</span>

											<input class="form-control" value="{{ old('docNumber') }}" id="docNumber" data-checkout="docNumber" name="docNumber" type="text">
											@if($errors->has('docNumber'))
											<label class="text-danger">{{ $errors->first('docNumber') }}</label>
											@endif
										</div>
									</div>

									<div class="col-lg-4 col-12">

										<div class="checkout__input">

											<span>Email*</span>

											<input class="form-control" value="{{ $cliente->email }}" id="email" name="email" type="text">
											@if($errors->has('email'))
											<label class="text-danger">{{ $errors->first('email') }}</label>
											@endif
										</div>
									</div>

									<div class="col-lg-6 col-12">

										<div class="checkout__input">

											<span>Número do cartão*</span>

											<input data-mask="0000 0000 0000 0000" class="form-control" style="width: 90%;" data-checkout="cardNumber" value="{{ old('cardNumber') }}" id="cardNumber" type="text" > 
											<img id="band-img" style="width: 20px;" src="">

											@if($errors->has('cardNumber'))
											<label class="text-danger">{{ $errors->first('cardNumber') }}</label>
											@endif

										</div>
									</div>

									<div class="col-lg-3 col-6">
										<div class="checkout__input">
											<span>Parcelas</span>

											<select style="margin-left: 0px;" class="form-control" id="installments" name="installments">
											</select>
										</div>
									</div>


									<div class="col-lg-4 col-12">

										<div class="checkout__input">

											<span>Data de Vencimento*</span>

											<div class="row">
												<div class="col-6">
													<input data-mask="00" class="form-control" placeholder="MM" data-checkout="cardExpirationMonth" value="{{ old('cardExpirationMonth') }}" id="cardExpirationMonth" type="text">
													@if($errors->has('cardExpirationMonth'))
													<label class="text-danger">{{ $errors->first('cardExpirationMonth') }}</label>
													@endif
												</div>
												<div class="col-6">

													<input data-mask="00" class="form-control" placeholder="AA" data-checkout="cardExpirationYear" value="{{ old('cardExpirationYear') }}" id="cardExpirationYear" type="text">
													@if($errors->has('cardExpirationYear'))
													<label class="text-danger">{{ $errors->first('cardExpirationYear') }}</label>
													@endif
												</div>
											</div>
										</div>
									</div>

									<div class="col-lg-3 col-12">

										<div class="checkout__input">

											<span>Código de segurança*</span>

											<input class="form-control" data-checkout="securityCode" value="{{ old('securityCode') }}" id="securityCode" type="text">
											@if($errors->has('securityCode'))
											<label class="text-danger">{{ $errors->first('securityCode') }}</label>
											@endif
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

									<input style="visibility: hidden" type="" name="transactionAmount" id="transactionAmount" value="{{$total}}" />
									<input type="hidden" name="total_pag" value="{{$totais->total_cartao}}">

									<input style="visibility: hidden" value="{{$descricao}}" name="description">
									<input style="visibility: hidden" name="paymentMethodId" id="paymentMethodId" />
									<input type="hidden" value="{{$carrinho->id}}" name="carrinho_id">


								</div>
								<div class="row">
									<div class="col-lg-12">
										<button type="submit" class="button cart_button_checkout">
											<span style="display: none" class="spinner-border" role="status" aria-hidden="true"></span>
											PAGAR COM CARTÃO

										</button>
									</div>
								</div>  
							</form>

						</div>

						<!-- PIX -->

						<div class="col-12 mix pix" style="display: none">
							<br>
							<div class="row">
								<div class="col-lg-12">
									<h3>Pagamento com PIX</h3>
								</div>
							</div>
							<br>


							<form action="/ecommercePay/pix" method="post" id="paymentFormBoleto">
								@csrf
								<div class="row">
									<div class="col-lg-4 col-12">

										<div class="checkout__input">

											<span>Nome*</span>
											<input class="form-control" value="{{$cliente->nome}}" name="payerFirstName" id="payerFirstName" type="text">
											@if($errors->has('payerFirstName'))
											<label class="text-danger">
												{{ $errors->first('payerFirstName') }}
											</label>
											@endif
										</div>
									</div>

									<div class="col-lg-4 col-12">

										<div class="checkout__input">

											<span>Sobrenome*</span>
											<input class="form-control" value="{{$cliente->sobre_nome}}" name="payerLastName" id="payerLastName" type="text">
											@if($errors->has('payerLastName'))
											<label class="text-danger">
												{{ $errors->first('payerLastName') }}
											</label>
											@endif
										</div>
									</div>
									<div class="col-lg-4 col-6"></div>

									<div class="col-lg-2 col-6">
										<div class="checkout__input">

											<span>Tipo de documento*</span>

											<select name="docType" class="form-control" style="margin-left: 0px;" id="docType2" data-checkout="docType">

											</select>
										</div>
									</div>

									<div class="col-lg-3 col-12">

										<div class="checkout__input">

											<span>Número do documento*</span>

											<input class="form-control" value="{{ old('docNumber') ? old('docNumber') : $cliente->cpf  }}" id="docNumber" data-checkout="docNumber" name="docNumber" type="text">
											@if($errors->has('docNumber'))
											<label class="text-danger">{{ $errors->first('docNumber') }}</label>
											@endif
										</div>
									</div>

									<div class="col-lg-3 col-12">

										<div class="checkout__input">

											<span>Email*</span>

											<input class="form-control" value="{{ $cliente->email }}" id="payerEmail" name="payerEmail" type="email">
											@if($errors->has('payerEmail'))
											<label class="text-danger">{{ $errors->first('payerEmail') }}</label>
											@endif
										</div>
									</div>

									<input style="visibility: hidden" type="" name="transactionAmount" id="transactionAmount" value="{{$total}}" />
									<input type="hidden" name="total_pag" value="{{$totais->total_pix}}">

									<input style="visibility: hidden" value="{{$descricao}}" name="description">
									<input style="visibility: hidden" name="paymentMethodId" id="paymentMethodId" />
									<input type="hidden" value="{{$carrinho->id}}" name="carrinho_id">

								</div>

								<div class="row">
									<div class="col-lg-12">
										<button type="submit" class="button cart_button_checkout">
											<span style="display: none" class="spinner-border" role="status" aria-hidden="true"></span>
											PAGAR COM PIX
										</button>
									</div>
								</div> 
							</form>


						</div>

						<!-- Boleto -->

						<div class="col-12 mix boleto" style="display: none">
							<br>
							<div class="row">
								<div class="col-lg-12">
									<h3>Pagamento com Boleto</h3>
								</div>
							</div>
							<br>


							<form action="/ecommercePay/boleto" method="post" id="paymentFormBoleto">
								@csrf
								<div class="row">
									<div class="col-lg-4 col-12">

										<div class="checkout__input">

											<span>Nome*</span>
											<input class="form-control" value="{{ $cliente->nome }}" name="payerFirstName" id="payerFirstName" type="text">
											@if($errors->has('payerFirstName'))
											<label class="text-danger">
												{{ $errors->first('payerFirstName') }}
											</label>
											@endif
										</div>
									</div>

									<div class="col-lg-4 col-12">

										<div class="checkout__input">

											<span>Sobrenome*</span>

											<input class="form-control" value="{{ $cliente->sobre_nome }}" name="payerLastName" id="payerLastName" type="text">
											@if($errors->has('payerLastName'))
											<label class="text-danger">
												{{ $errors->first('payerLastName') }}
											</label>
											@endif
										</div>
									</div>
									<div class="col-lg-4 col-6"></div>

									<div class="col-lg-2 col-6">
										<div class="checkout__input">

											<span>Tipo de documento*</span>

											<select name="docType" class="form-control" style="margin-left: 0px;" id="docType3" data-checkout="docType">

											</select>
										</div>
									</div>

									<div class="col-lg-3 col-12">

										<div class="checkout__input">

											<span>Número do documento*</span>

											<input class="form-control" value="{{ old('docNumber') ? old('docNumber') : $cliente->cpf  }}" id="docNumber" data-checkout="docNumber" name="docNumber" type="text">
											@if($errors->has('docNumber'))
											<label class="text-danger">{{ $errors->first('docNumber') }}</label>
											@endif
										</div>
									</div>

									<div class="col-lg-3 col-12">

										<div class="checkout__input">

											<span>Email*</span>

											<input class="form-control" value="{{ $cliente->email }}" id="payerEmail" name="payerEmail" type="email">
											@if($errors->has('payerEmail'))
											<label class="text-danger">{{ $errors->first('payerEmail') }}</label>
											@endif
										</div>
									</div>


									<input style="visibility: hidden" type="" name="transactionAmount" id="transactionAmount" value="{{$total}}" />
									<input type="hidden" name="total_pag" value="{{$totais->total_boleto}}">

									<input style="visibility: hidden" value="{{$descricao}}" name="description">
									<input style="visibility: hidden" name="paymentMethodId" id="paymentMethodId" />
									<input type="hidden" value="{{$carrinho->id}}" name="carrinho_id">

								</div>

								<div class="row">
									<div class="col-lg-12">
										<button type="submit" class="button cart_button_checkout">
											<span style="display: none" class="spinner-border" role="status" aria-hidden="true"></span>
											PAGAR COM BOLETO
										</button>
									</div>
								</div> 
							</form>


						</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>


@section('javascript')
<script type="text/javascript">

	$('.cart_button_checkout').click(() => {
		$('.cart_button_checkout').prop("disabled", true);
		$('.spinner-border').css('display', 'inline-block')
	})

	$('.div-cartao').click(() => {
		removeClass()
		activeClass('cartao')
	})

	$('.div-pix').click(() => {
		removeClass()
		activeClass('pix')
	})

	$('.div-boleto').click(() => {
		removeClass()
		activeClass('boleto')
	})

	function removeClass(){
		$('.div-cartao').removeClass('active')
		$('.div-pix').removeClass('active')
		$('.div-boleto').removeClass('active')
	}

	function activeClass(classe){

		$('.'+classe).css('display', 'block')

		if(classe == 'cartao'){
			$('.div-cartao').addClass('active')
			$('.pix').css('display', 'none')
			$('.boleto').css('display', 'none')
		}else if(classe == 'boleto'){
			$('.div-boleto').addClass('active')

			$('.cartao').css('display', 'none')
			$('.pix').css('display', 'none')
		}else{
			$('.div-pix').addClass('active')

			$('.cartao').css('display', 'none')
			$('.boleto').css('display', 'none')
		}
		
	}
</script>
@endsection
@endsection