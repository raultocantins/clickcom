@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<input type="hidden" id="_token" value="{{ csrf_token() }}">
			

			<h4 class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Lista de pagamentos</h4>

			<label class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Registros: <strong class="text-success">{{sizeof($representanteEmpresa->pagamentos)}}</strong></label>
			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
				<div class="row">


				</div>
			</div>

			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

					<table class="datatable-table" style="max-width: 100%; overflow: scroll">
						<thead class="datatable-head">
							<tr class="datatable-row" style="left: 0px;">
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Data</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Tipo Pagamento</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Valor</span></th>
								
							</tr>
						</thead>

						<tbody class="datatable-body">
							@foreach($representanteEmpresa->pagamentos as $p)

							<tr class="datatable-row">
								<td class="datatable-cell">
									<span class="codigo" style="width: 150px;">
										{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}
									</span>
								</td>
								<td class="datatable-cell">
									<span class="codigo" style="width: 150px;">
										{{ $p->forma_pagamento}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 150px;">
										{{ number_format($p->valor, 2, ',', '.') }}
									</span>
								</td>
								
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection	
