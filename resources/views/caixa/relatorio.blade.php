@extends('relatorios.cabecalho')
@section('content')

<div class="row">
	<div class="col s12">
		<h3 class="center-align">Relátorio de Caixa</h3>

		<h2 class="card-title">Total de vendas: <strong class="text-info">{{sizeof($vendas)}}</strong></h2>
		<h2 class="card-title text-success">Valor de abertura: <strong class="">{{number_format($abertura->valor, 2, ',', '.')}}</strong></h2>
	</div>

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
										{{App\Models\VendaCaixa::getTipoPagamento($key)}}: 
										<strong class="text-success"> R$ {{number_format($tp, 2, ',', '.')}}</strong>
									</h3>
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

	<table class="pure-table">
		<thead>
			<tr>
				<th width="110">CLIENTE</th>
				<th width="110">DATA</th>
				<th width="110">TIPO DE PAGAMENTO</th>
				<th width="110">ESTADO</th>
				<th width="110">NFCE/NFE</th>
				<th width="110">TIPO</th>
				<th width="110">VALOR</th>
			</tr>
		</thead>

		<tbody>
			@php
			$soma = 0;
			@endphp

			@foreach($vendas as $v)
			<tr>
				<td>{{ $v->cliente->razao_social ?? 'NAO IDENTIFCADO' }}</td>
				<td>{{ \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i:s')}}</td>
				<td>
					@if($v->tipo_pagamento == '99')

					<a href="#!" onclick='swal("", "{{$v->multiplo()}}", "info")' class="btn btn-light-info">
						Ver
					</a>
					@else
					{{$v->getTipoPagamento($v->tipo_pagamento)}}
					@endif
				</td>
				<td>{{ $v->estado }}</td>

				@if($v->tipo == 'PDV')
				<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{ $v->NFcNumero > 0 ? $v->NFcNumero : '--' }}</span>
				</td>
				@else
				<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{ $v->NfNumero > 0 ? $v->NfNumero : '--' }}</span>
				</td>
				@endif

				<td>{{ $v->tipo }}</td>
				<td>{{ number_format($v->valor_total, 2, ',', '.') }}</td>
			</tr>

			@php
			$soma += $v->valor_total;
			@endphp

			@endforeach
		</tbody>
	</table>
	

	<h2 class="text-info">Soma de vendas: 
		<strong>{{number_format($soma, 2, ',', '.')}}</strong>
	</h2>	

	@php
	$somaSuprimento = 0;
	$somaSangria = 0;
	@endphp

	<div class="row">
		<div class="col-12 col-xl-6" style="width: 50%">
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

		<div class="col-12 col-xl-6" style="width: 50%">
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
			<h4 class="text-info">Valor em caixa: <strong>{{number_format($somaSuprimento + $soma - $somaSangria, 2, ',', '.')}}</strong></h4>
		</div>
	</div>


</div>
@endsection
