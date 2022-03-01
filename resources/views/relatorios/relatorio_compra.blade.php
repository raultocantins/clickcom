@extends('relatorios.cabecalho')
@section('content')

<div class="row">
	<div class="col s12">
		<h3 class="center-align">Relátorio de Compras</h3>
		@if($data_inicial && $data_final)
		<h4>Periodo: {{$data_inicial}} - {{$data_final}}</h4>
		@endif
	</div>


	<table class="pure-table">
		<thead>
			<tr>
				<th width="150">DATA</th>
				<th width="150">QTD COMPRAS</th>
				<th width="150">TOTAL</th>
			</tr>
		</thead>

		@php
		$soma = 0;
		@endphp
		<tbody>
			@foreach($compras as $key => $c)
			<tr class="@if($key%2 == 0) pure-table-odd @endif">
				<td><center>{{$c->data}}</center></td>
				<td><center>{{number_format($c->compras_diarias)}}</center></td>
				<td><center>R$ {{number_format($c->total, 2, ',', '.')}}</center></td>
			</tr>

			@php
			$soma += $c->total;
			@endphp
			@endforeach
		</tbody>
	</table>

	<h3>Soma: <strong style="color: purple">{{number_format($soma, 2, ',', '.')}}</strong></h3>
</div>

@endsection
