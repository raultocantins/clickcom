@extends('relatorios.cabecalho')
@section('content')

<div class="row">
	<div class="col s12">
		<h3 >Relátorio de Contas a pagar</h3>
		@if($data_inicial && $data_final)
		<h4>Periodo: {{$data_inicial}} - {{$data_final}}</h4>
		@endif
	</div>

	<table class="pure-table">
		<thead>
			<tr>

				<th width="110">VALOR INTEGRAL</th>
				<th width="110">VALOR PAGO</th>
				<th width="110">FORNECEDOR</th>
				<th width="110">REFERÊNCIA</th>
				<th width="60">DATA DE CADASTRO</th>
				<th width="60">DATA DE VENCIMENTO</th>
				<th width="60">ESTADO</th>
			</tr>
		</thead>

		<tbody>
			<?php $somaContasIntegral = 0; ?>
			<?php $somaContasPagas = 0; ?>
			@foreach($contas as $key => $c)
			<tr class="@if($key%2 == 0) pure-table-odd @endif">

			
				<td>{{ number_format($c->valor_integral,2,",",".") }}</td>
				<td>{{ number_format($c->valor_pago,2,",",".")}}</td>
				<td>{{ $c->fornecedor }}</td>
				<td>{{ $c->referencia }}</td>
				<td>{{ \Carbon\Carbon::parse($c->created_at)->format('d/m/Y')}}</td>
				<td>{{ \Carbon\Carbon::parse($c->data_vencimento)->format('d/m/Y')}}</td>
				<td>
					@if($c->status)
					<span class="text-success">Pago</span>
					@else
					<span class="text-danger">Pendente</span>
					@endif
				</td>


			</tr>

			<?php $somaContasIntegral += $c->valor_integral; ?>
									<?php $somaContasPagas += $c->valor_pago; ?>
			@endforeach
		</tbody>
	</table>
<h4 class="soma">Soma valor integral: <strong class="text-success">R$ {{number_format($somaContasIntegral, 2, ',', '.')}}</strong></h4>

	<h4 class="soma">Soma valor pagor: <strong class="text-success">R$ {{number_format($somaContasPagas, 2, ',', '.')}}</strong></h4>



</div>
@endsection

