@extends('default.layout')
@section('content')

<style type="text/css">
	.card-stretch:hover{
		cursor: pointer;
	}

	.input-group-append:hover{
		cursor: pointer;
	}
</style>
<div class="card card-custom gutter-b">

	<div class="card-body @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">
		<div class="content d-flex flex-column flex-column-fluid" id="kt_content" >

			<div class="row">
				<div class="col-lg-6">

					<input type="hidden" value="{{$payment->transacao_id}}" id="transacao_id" name="">
					<input type="hidden" value="{{$payment->status}}" id="status" name="">
					<h3>Plano: <strong class="text-info">{{$payment->plano->plano->nome}}</strong></h3>
					<h3>Transação ID: <strong class="text-info">{{$payment->transacao_id}}</strong></h3>

					<h3>Status: 
						@if($payment->status == 'approved')
						<span class="label label-xl label-inline label-light-success">Aprovado</span>
						@elseif($payment->status == 'pending')
						<span class="label label-xl label-inline label-light-warning">Pendente</span>
						@elseif($payment->status == 'rejected')
						<span class="label label-xl label-inline label-light-danger">Rejeitado</span>
						@else
						<span class="label label-xl label-inline label-light-dark">Não identificado</span>
						@endif
					</h3>

					@if($payment->forma_pagamento == 'Boleto')
					<a target="_blank" href="{{$payment->link_boleto}}" class="btn btn-info">
						<i class="la la-print"></i>
						Imprimir Boleto
					</a>
					@endif

				</div>
				<div class="col-lg-6">

					<h3>Valor: <strong class="text-info">{{number_format($payment->valor, 2, ',', '.')}}</strong></h3>
					<h3>Forma de pagamento: <strong class="text-info">{{$payment->forma_pagamento}}</strong></h3>
					<h3>Data de criação: <strong class="text-info">{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y H:i:s')}}</strong></h3>
					<h3>Ultima atualização: <strong class="text-info">{{ \Carbon\Carbon::parse($payment->updated_at)->format('d/m/Y H:i:s')}}</strong></h3>
				</div>

				@if($payment->forma_pagamento == 'Pix')
				<div class="row">
					<div class="col-lg-12">

						<div class="col-lg-4 offset-lg-4">
							<img style="width: 400px; height: 400px;" src="data:image/jpeg;base64,{{$payment->qr_code_base64}}"/>
						</div>					
					</div>	
					<div class="col-lg-12">

						<div class="col-lg-11 offset-lg-1">

							<div class="input-group">
								<input type="text" class="form-control" value="{{$payment->qr_code}}" id="qrcode_input" />

								<div class="input-group-append">
									<span class="input-group-text">

										<i onclick="copy()" class="la la-copy">
										</i>

									</span>
								</div>
							</div>

						</div>				
					</div>				
				</div>

				<div class="row">
					

				</div>
				@endif

			</div>
		</div>
	</div>
</div>

@section('javascript')
<script type="text/javascript">
	@if($payment->link_boleto != "")
    // window.open('{{$payment->link_boleto}}')
    @endif
	function copy(){
		const inputTest = document.querySelector("#qrcode_input");

		inputTest.select();
		document.execCommand('copy');

		swal("", "Código pix copado!!", "success")
	}
	if($('#status').val() != "approved"){
		setInterval(() => {
			let transacao_id = $('#transacao_id').val();
			$.get(path+'payment/consulta/'+transacao_id)
			.done((success) => {
				// console.log(success)
				if(success == "approved"){
					location.reload()
				}
			})
			.fail((err) => {
				console.log(err)
			})
		}, 1000)
	}
</script>

@endsection


@endsection
