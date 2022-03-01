@extends('default.layout')
@section('content')

<style type="text/css">
	.card-stretch:hover{
		cursor: pointer;
	}
</style>
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
				<h3 class="card-title">PAGAMENTO DE PLANO</h3>

				<form method="post" action="/payment/setPlano">
					@csrf
					<input id="plano" type="hidden" name="plano" value="{{$plano->plano_id}}">

					<div class="row">

						@foreach($planos as $p)
						<div class="col-xl-4 h-t" onclick="selectPlan({{$p}})">
							<!--begin::Nav Panel Widget 2-->
							<div class="card card-custom card-stretch gutter-b @if($p->id == $plano->plano_id) bg-light-danger @endif" id="plan_{{$p->id}}">
								<!--begin::Body-->
								<div class="card-body">
									<!--begin::Wrapper-->
									<div class="d-flex justify-content-between flex-column pt-4 h-100">
										<!--begin::Container-->
										<div class="pb-5">
											<!--begin::Header-->
											<div class="d-flex flex-column flex-center">
												<!--begin::Symbol-->
												<div class="symbol symbol-120 symbol-circle symbol-success overflow-hidden">
													<span class="symbol-label">
														@if($p->img != '')
														<img src="/imgs_planos/{{$p->img}}" class="h-100 align-self-end" alt="">
														@else
														<img src="/imgs_planos/sem_imagem.png" class="h-100 align-self-end" alt="">
														@endif

													</span>
												</div>

												<a class="card-title font-weight-bolder text-dark-75 text-hover-primary font-size-h4 m-0 pt-7 pb-1">{{$p->nome}}</a>

												<h2 class="card-title font-weight-bolder text-info text-hover-primary font-size-h4 m-0 pt-7 pb-1">R$ {{number_format($p->valor, 2, ',', '.')}}</h2>

											</div>
											<div class="pt-1">
												<!--begin::Text-->
												<p class="text-dark-75 font-weight-nirmal font-size-lg m-0 pb-7">
													{!! $p->descricao !!}
												</p>
												<!--end::Text-->

												<!--end::Item-->
											</div>
											<!--end::Body-->
										</div>
										<!--eng::Container-->
										<!--end::Footer-->
									</div>
									<!--end::Wrapper-->
								</div>
								<!--end::Body-->
							</div>
							<!--end::Nav Panel Widget 2-->
						</div>

						@endforeach
					</div>

					<div class="row">

						<button type="submit" style="width: 100%;" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-submit">
							<i class="la la-check"></i>
							ESCOLHER
						</button>
					</div>
				</form>

				<input type="hidden" value="{{$pay}}" id="pay">

				<!--end::Form-->
			</div>
		</div>
	</div>
</div>

@section('javascript')
<script type="text/javascript">

	$(function(){
		let pay = $('#pay').val();
		pay = JSON.parse(pay);

		console.log(pay)

		if(pay){

			swal({
				title: "Alerta",
				text: "Voçe possui uma fatura gerada para o plano",
				icon: "warning",
				buttons: [
				'Gerar nova',
				'ir para fatura'
				],
			}).then((acao) => {
				if(acao){
					location.href = path + 'payment/'+pay.transacao_id
				}else{

				}
			})
		}
	})

	function selectPlan(plano){
		console.log(plano)

		$('.card-stretch').removeClass('bg-light-danger')
		$('#plan_'+plano.id).addClass('bg-light-danger')

		$('#plano').val(plano.id)
	}
</script>

@endsection
@endsection
