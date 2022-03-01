@extends('relatorios.cabecalho')
@section('content')
<div class="row">
	<div class="col s12">
		<h3 class="center-align">Relátorio de Vendas</h3>
		@if($data_inicial && $data_final)
		<h4>Periodo: {{$data_inicial}} - {{$data_final}}</h4>
		@endif

	</div>


	<table class="pure-table">
		<thead>
			<tr>
				<th width="100">DATA</th>
				<th width="70">ID</th>
				<th width="120">VENDEDOR</th>
				<th width="200">CLIENTE</th>
				<th width="130">FORMA DE PAGAMENTO</th>
				<th width="130">VALOR TOTAL</th>
			</tr>
		</thead>
		@php
		$somaPedido = 0;
		$somaPdv = 0;
		@endphp
		<tbody>
			@foreach($vendas as $key => $v)
			<tr class="@if($key%2 == 0) pure-table-odd @endif">
				<td><center>{{\Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i')}}</center></td>
				<td><center>{{$v->id}}</center></td>
				<td><center>{{$v->vendedor()}}</center></td>
				<td><center>{{$v->cliente ? $v->cliente->razao_social : 'Consumidor final'}}</center></td>
				@if(isset($v->cpf))
				<td><center>{{$v->getTipoPagamento2()}}</center></td>
				@else
				<td><center>{{$v->getTipoPagamento()}}</center></td>
				@endif
				<td><center>{{number_format($v->valor_total, 2, ',', '.')}}</center></td>

			</tr>

			@php
			if(!isset($v->cpf))
			$somaPedido += $v->valor_total;
			else
			$somaPdv += $v->valor_total;
			@endphp
			@endforeach
		</tbody>
	</table>

	<h4>Soma Pedido: <strong style="color: blue">{{number_format($somaPedido, 2, ',', '.')}}</strong></h4>
	<h4>Soma PDV: <strong style="color: purple">{{number_format($somaPdv, 2, ',', '.')}}</strong></h4>

	<h3>Total: <strong style="color: green">{{number_format($somaPedido + $somaPdv, 2, ',', '.')}}</strong></h3>
</div>
@endsection
