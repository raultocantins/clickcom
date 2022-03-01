@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">

	<div class="card-body">
		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">
			<div class="col-lg-12" id="content">
				<!--begin::Portlet-->
				<br>
				<h2 class="card-title">Total de vendas: <strong class="text-info">{{sizeof($vendas)}}</strong></h2>
				<h2 class="card-title text-success">Valor de abertura: <strong class="">{{number_format($abertura->valor, 2, ',', '.')}}</strong></h2>

				<div class="row">
					<div class="col-xl-12">
						<h3 class="text-info">Total por tipo de pagamento:</h3>
						<div class="kt-section kt-section--first">
							<div class="kt-section__body">
								<div class="row">

									@foreach($somaTiposPagamento as $key => $tp)
									@if($tp > 0)
									<div class="col-sm-4 col-lg-4 col-md-6">
										<div class="card card-custom gutter-b">
											<div class="card-header">
												<h3 class="card-title">
													{{App\Models\VendaCaixa::getTipoPagamento($key)}}
												</h3>
											</div>
											<div class="card-body">
												<h4 class="text-success">R$ {{number_format($tp, 2, ',', '.')}}</h4>
											</div>

										</div>
									</div>
									@endif
									@endforeach

								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xl-12">

						<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

							<table class="datatable-table" style="max-width: 100%; overflow: scroll">
								<thead class="datatable-head">
									<tr class="datatable-row" style="left: 0px;">
										
										<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Cliente</span></th>
										<th data-field="Country" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Data</span></th>
										<th data-field="ShipDate" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Tipo de pagamento</span></th>

										<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Estado</span></th>

										<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">NFCe/NFe</span></th>

										<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Tipo</span></th>

										<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Valor</span></th>
									</tr>
								</thead>

								<tbody class="datatable-body">
									@php
									$soma = 0;
									@endphp


									@foreach($vendas as $v)

									<tr class="datatable-row" >
										
										<td class="datatable-cell"><span class="codigo" style="width: 150px;">{{ $v->cliente->razao_social ?? 'NAO IDENTIFCADO' }}</span>
										</td>
										<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{ \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i:s')}}</span>
										</td>
										<td class="datatable-cell">
											<span class="codigo" style="width: 100px;">

												@if($v->tipo_pagamento == '99')

												Outros
												@else
												{{$v->getTipoPagamento($v->tipo_pagamento)}}
												@endif

											</span>
										</td>
										<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{ $v->estado }}</span>
										</td>
										@if($v->tipo == 'PDV')
										<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{ $v->NFcNumero > 0 ? $v->NFcNumero : '--' }}</span>
										</td>
										@else
										<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{ $v->NfNumero > 0 ? $v->NfNumero : '--' }}</span>
										</td>
										@endif

										<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{ $v->tipo }}</span>
										</td>

										<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{ number_format($v->valor_total, 2, ',', '.') }}</span>
										</td>

									</tr>

									@php
									$soma += $v->valor_total;
									@endphp

									@endforeach

								</tbody>
							</table>
						</div>
					</div>
				</div>
				<br>

				<h2 class="text-info">Soma de vendas: 
					<strong>{{number_format($soma, 2, ',', '.')}}</strong>
				</h2>	

				@php
				$somaSuprimento = 0;
				$somaSangria = 0;
				@endphp

				<div class="row">
					<div class="col-12 col-xl-6">
						<div class="card card-custom gutter-b bg-light-info">

							<div class="card-body">
								<h2 class="card-title">Suprimentos:</h2>

								@if(sizeof($suprimentos) > 0)
								@foreach($suprimentos as $s)
								<h4>Valor: R$ {{number_format($s->valor, 2, ',', '.')}}</h4>
								@php
								$somaSuprimento += $s->valor;
								@endphp
								@endforeach
								@else
								<h4>R$ 0,00</h4>
								@endif
							</div>			
						</div>			
					</div>	

					<div class="col-12 col-xl-6">
						<div class="card card-custom gutter-b bg-light-danger">

							<div class="card-body">
								<h2 class="card-title">Sangrias:</h2>

								@if(sizeof($sangrias) > 0)
								@foreach($sangrias as $s)
								<h4>Valor: R$ {{number_format($s->valor, 2, ',', '.')}}</h4>
								@php
								$somaSangria += $s->valor;
								@endphp
								@endforeach
								@else
								<h4>R$ 0,00</h4>
								@endif
							</div>			
						</div>			
					</div>		
				</div>

				<div class="row">
					<div class="col-12">
						<div class="card card-custom gutter-b">
							<div class="card-body">
								<div class="row">

									<div class="col-6">
										<h4 class="text-primary">Soma da vendas: <strong>{{number_format($soma, 2, ',', '.')}}</strong></h4>

									</div>

									<div class="col-6">
										<h4 class="text-danger">Soma de sangria: <strong>{{number_format($somaSangria, 2, ',', '.')}}</strong></h4>

									</div>

									<div class="col-6">
										<h4 class="text-success">Soma de suprimento: <strong>{{number_format($somaSuprimento, 2, ',', '.')}}</strong></h4>
									</div>

									<div class="col-6">
										<h4 class="text-info">Resultado caixa: <strong>{{number_format($abertura->valor + $somaSuprimento + $soma - $somaSangria, 2, ',', '.')}}</strong></h4>
									</div>
								</div>
							</div>
						</div>			
					</div>			
				</div>	

				<div class="row">
					<div class="col-12">
						<a href="/caixa/imprimir/{{$abertura->id}}" class="btn btn-info">
							<i class="la la-print"></i>
							Imprimir
						</a>
					</div>
				</div>		

			</div>
		</div>
	</div>
</div>

@endsection	
