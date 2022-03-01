@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<input type="hidden" id="_token" value="{{ csrf_token() }}">
			<form class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft" method="get" action="/financeiro/filtro">
				<div class="row align-items-center">

					<div class="form-group col-lg-3 col-md-6 col-sm-6">
						<label class="col-form-label">Empresa</label>
						<div class="">
							<div class="input-group date">
								<input type="text" name="empresa" class="form-control" value="{{{isset($empresa) ? $empresa : ''}}}" />
							</div>
						</div>
					</div>

					<div class="form-group col-lg-2 col-md-4 col-sm-6">
						<label class="col-form-label">Data Inicial</label>
						<div class="">
							<div class="input-group date">
								<input type="text" name="data_inicial" class="form-control date-out" readonly value="{{{isset($dataInicial) ? $dataInicial : ''}}}" id="kt_datepicker_3" />
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

					<div class="form-group col-lg-2 col-md-3 col-sm-3">
						<label class="col-form-label">Estado</label>
						<div class="">
							<select name="status" class="custom-select">
								<option @isset($status) @if($status == 'TODOS') selected @endif @endisset value="TODOS">TODOS</option>
								<option @isset($status) @if($status ==  'approved') selected @endif @endisset value="approved">PAGO</option>
								<option @isset($status) @if($status == 'pending') selected @endif @endisset value="pending">PENDENTE</option>
								<option @isset($status) @if($status == 'rejected') selected @endif @endisset value="rejected">REJEITADO</option>
							</select>
						</div>
					</div>

					<div class="form-group col-lg-2 col-md-3 col-sm-3">
						<label class="col-form-label">Tipo de pagamento</label>
						<div class="">
							<select name="tipo_pagamento" class="custom-select">
								<option @isset($tipo_pagamento) @if($tipo_pagamento == 'TODOS') selected @endif @endisset value="TODOS">TODOS</option>
								<option @isset($tipo_pagamento) @if($tipo_pagamento == 'Cartão') selected @endif @endisset value="Cartão">CARTÃO</option>
								<option @isset($tipo_pagamento) @if($tipo_pagamento == 'Boleto') selected @endif @endisset value="Boleto">BOLETO</option>
								<option @isset($tipo_pagamento) @if($tipo_pagamento == 'Pix') selected @endif @endisset value="Pix">PIX</option>
							</select>
						</div>
					</div>
					<div class="col-lg-1 col-xl-1 mt-2 mt-lg-0">
						<button style="margin-top: 15px;" class="btn btn-light-primary px-6 font-weight-bold">
							<i class="la la-search"></i>
						</button>
					</div>
				</div>
			</form>

			<h4 class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Lista de Pagamentos</h4>

			<label class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Registros: <strong class="text-success">{{sizeof($payments)}}</strong></label>
			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
				<div class="row">

					<a href="/financeiro/novoPagamento" class="btn btn-success">
						<i class="la la-plus"></i>
						Pagamento Manual
					</a>

					<a id="btn-verifica" style="margin-left: 5px;" href="/financeiro/verificaPagamentos" class="btn btn-info spinner-white spinner-right">
						<i class="la la-refresh"></i>
						verificar pagamentos
					</a>

				</div>
			</div>

			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

					<table class="datatable-table" style="max-width: 100%; overflow: scroll">
						<thead class="datatable-head">
							<tr class="datatable-row" style="left: 0px;">
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">#</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">Empresa</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Data</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Plano</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Valor</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Status</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">Ações</span></th>
							</tr>
						</thead>

						@php
						$soma = 0;
						@endphp
						<tbody class="datatable-body">
							@foreach($payments as $p)

							<tr class="datatable-row">
								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">
										{{$p->transacao_id != '' ? $p->transacao_id : '--'}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 250px;">
										{{$p->empresa->nome}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 150px;">
										{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i:s')}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">
										{{$p->plano->plano->nome}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">
										{{number_format($p->valor, 2, ',', '.')}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">

										@if($p->status == 'approved')
										<span class="label label-xl label-inline label-light-success">Aprovado</span>
										@elseif($p->status == 'pending')
										<span class="label label-xl label-inline label-light-warning">Pendente</span>
										@elseif($p->status == 'rejected')
										<span class="label label-xl label-inline label-light-danger">Rejeitado</span>
										@else
										<span class="label label-xl label-inline label-light-dark">Não identificado</span>
										@endif
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 200px;">
										<a href="/financeiro/detalhes/{{$p->id}}" class="btn btn-sm btn-primary">
											Detalhes
										</a>

										@if($p->status == 'rejected' || $p->status == 'pending')
										<a onclick='swal("Atenção!", "Deseja remover este pagamento?", "warning").then((sim) => {if(sim){ location.href="/financeiro/delete/{{ $p->id }}" }else{return false} })' href="#!"  class="btn btn-sm btn-danger">
											Remover
										</a>
										@endif
									</span>
								</td>

							</tr>

							@php
							$soma += $p->valor;
							@endphp
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
							<div class="card card-custom gutter-b example example-compact">
								<div class="card-header">

									<div class="card-body">
										<h3 class="card-title">Soma: <strong style="margin-left: 5px;" class="text-info"> R$ {{number_format($soma, 2, ',', '.') }}</strong></h3>

									</div>

								</div>
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

	$('#btn-verifica').click(() => {
		$('#btn-verifica').addClass('spinner');
	})
</script>
@endsection	

@endsection	
