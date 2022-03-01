@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">


	<div class="card-body">
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-sm-12">

				<h4>Boletos Remessa: <strong>{{$remessa->nome_arquivo}}</strong></h4>
				<h4>Data: <strong>{{\Carbon\Carbon::parse($remessa->created_at)
									->format('d/m/Y H:i')}} </strong></h4>
			</div>
		</div>
		<br>
		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">
			<br>

			<div class="row @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				@foreach($remessa->boletos as $b)

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-4">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">
							<div class="card-title">

								<h3 style="width: 230px; font-size: 12px; height: 10px;" class="card-title">{{$b->boleto->conta->getCliente()->razao_social}} 
								</h3>

								<div class="dropdown dropdown-inline" data-toggle="tooltip" title="" data-placement="left" data-original-title="Ações">
									<a href="#" class="btn btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="fa fa-ellipsis-h"></i>
									</a>
									<div class="dropdown-menu p-0 m-0 dropdown-menu-md dropdown-menu-right">
										<!--begin::Navigation-->
										<ul class="navi navi-hover">
											<li class="navi-header font-weight-bold py-4">
												<span class="font-size-lg">Ações:</span>
											</li>
											<li class="navi-separator mb-3 opacity-70"></li>
											<li class="navi-item">
												<a target="_blank" href="/boleto/imprimir/{{$b->boleto->id}}" class="navi-link">
													<span class="navi-text">
														<span class="label label-xl label-inline label-light-primary">Imprimir</span>
													</span>
												</a>
											</li>
											
										</ul>

									</div>
								</div>


							</div>

							<div class="card-body">

								<div class="kt-widget__info">
									<span class="kt-widget__label">Valor:</span>
									<a class="kt-widget__data text-info">
										{{number_format($b->boleto->conta->valor_integral,2,',', '.')}}
									</a>
								</div>

								<div class="kt-widget__info">
									<span class="kt-widget__label">Banco:</span>
									<a class="kt-widget__data text-info">
										{{$b->boleto->banco->banco}}
									</a>
								</div>

								<div class="kt-widget__info">
									<span class="kt-widget__label">Vencimento:</span>
									<a class="kt-widget__data text-info">
										{{\Carbon\Carbon::parse($b->boleto->conta->data_vencimento)->format('d/m/Y')}}
									</a>
								</div>


							</div>

						</div>

					</div>

				</div>

				@endforeach

			</div>
		</div>
	</div>
</div>

@endsection