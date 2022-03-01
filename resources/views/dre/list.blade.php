@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">


	<div class="card-body">
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-sm-12 col-lg-4 col-md-6 col-xl-4">

				DRE
			</div>
		</div>
		<br>
		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">
			<br>

			<div class="row @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				@foreach($docs as $d)


				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-4">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">
							<div class="card-title">

								<h3 style="width: 230px; font-size: 12px; height: 10px;" class="card-title">{{\Carbon\Carbon::parse($d->inicio)
									->format('d/m/Y')}} - {{\Carbon\Carbon::parse($d->fim)
									->format('d/m/Y')}}
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
												<a href="/dre/ver/{{$d->id}}" class="navi-link">
													<span class="navi-text">
														<span class="label label-xl label-inline label-light-primary">Ver</span>
													</span>
												</a>
											</li>
											<li class="navi-item">
												<a onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/dre/delete/{{ $d->id }}" }else{return false} })' href="#!" class="navi-link">
													<span class="navi-text">
														<span class="label label-xl label-inline label-light-danger">Excluir</span>
													</span>
												</a>
											</li>

											

										</ul>

									</div>
								</div>


							</div>

							<div class="card-body">

								
								<div class="kt-widget__info">
									<span class="kt-widget__label">Observação:</span>
									<a onclick='swal("", "{{$d->observacao != '' ? $d->observacao : 'Nenhuma observação'}}", "info")' class="btn btn-sm btn-light-primary font-weight-bolder text-uppercase py-4">Ver</a>
								</div>

								<div class="kt-widget__info">
									<span class="kt-widget__label">Lucro/prejuízo:</span>
									<a class="kt-widget__data text-info">{{number_format($d->lucro_prejuizo, 2, ',', '.')}}</a>
								</div>

								<div class="kt-widget__info">
									<span class="kt-widget__label">Data de criação:</span>
									<a class="kt-widget__data text-info">
										{{\Carbon\Carbon::parse($d->created_at)->format('d/m/Y H:i')}}
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