@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-12">
				<h1>Lista de operações de caixa</h1>
			</div>
		</div>
		<br>
		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<form class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft" method="get" action="/caixa/filtro">
				<div class="row align-items-center">


					<div class="form-group col-lg-2 col-md-4 col-sm-6">
						<label class="col-form-label">Data Inicial</label>
						<div class="">
							<div class="input-group date">
								<input type="text" name="data_inicial" class="form-control" readonly value="{{{isset($dataInicial) ? $dataInicial : ''}}}" id="kt_datepicker_3" />
								<div class="input-group-append">
									<span class="input-group-text">
										<i class="la la-calendar"></i>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group col-lg-2 col-md-4 col-sm-6">
						<label class="col-form-label">Data Final</label>
						<div class="">
							<div class="input-group date">
								<input type="text" name="data_final" class="form-control" readonly value="{{{isset($dataFinal) ? $dataFinal : ''}}}" id="kt_datepicker_3" />
								<div class="input-group-append">
									<span class="input-group-text">
										<i class="la la-calendar"></i>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-2 col-xl-2 mt-2 mt-lg-0">
						<button style="margin-top: 15px;" class="btn btn-light-primary px-6 font-weight-bold">Pesquisa</button>
					</div>
					
				</div>
			</form>
			<br>
			<div class="row @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				@foreach($aberturas as $a)

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-4">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">
								{{ \Carbon\Carbon::parse($a->created_at)->format('d/m/Y H:i:s')}}
							</h3>
							<div class="card-toolbar">
								<a href="/caixa/detalhes/{{$a->id}}" class="btn btn-icon btn-circle btn-sm btn-light-primary mr-1"><i class="la la-list"></i></a>
							</div>
						</div>
						<div class="card-body">
							<h3>Valor de abertura: <strong class="text-info">R$ {{number_format($a->valor, 2, ',', '.')}}</strong></h3>
							<h3>Usuário: <strong class="text-info">{{$a->usuario->nome}}</strong></h3>
						</div>
					</div>

				</div>

				@endforeach

			</div>
		</div>
	</div>
</div>

@endsection