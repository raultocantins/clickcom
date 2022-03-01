@extends('relatorios.cabecalho')
@section('content')
<div class="row">
	<div class="col s12">
		<h3 class="center-align">Relátorio de Comissão</h3>
		@if($data_inicial && $data_final)
		<h4>Periodo: {{$data_inicial}} - {{$data_final}}</h4>
		@endif

		@if($funcionario != 'null')
		<h4>Funcionario: <strong>{{$funcionario}}</strong></h4>
		@endif

		@if($produto != 'null')
		<h4>Produto: <strong>{{$produto}}</strong></h4>
		@endif
	</div>


	<table class="pure-table">
		<thead>
			<tr>
				<th width="150">DATA</th>
				<th width="150">VALOR DA COMISSÃO</th>
				<th width="150">VALOR DA VENDA</th>
				@if($funcionario == 'null')
				<th width="150">VENDEDOR</th>
				@endif
			</tr>
		</thead>
		@php
		$somaComissao = 0;
		$somaVendas = 0;
		@endphp
		<tbody>
			@foreach($comissoes as $key => $c)
			<tr class="@if($key%2 == 0) pure-table-odd @endif">
				<td><center>{{ \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i:s')}}</center></td>
				<td><center>{{number_format($c->valor, 2, ',', '.')}}</center></td>
				<td><center>{{number_format($c->valor_total_venda, 2, ',', '.')}}</center></td>
				@if($funcionario == 'null')
				<td><center>{{$c->funcionario}}</center></td>
				@endif
			</tr>

			@php
			$somaComissao += $c->valor;
			$somaVendas += $c->valor_total_venda;
			@endphp
			@endforeach
		</tbody>
	</table>

	<h4>Soma comissão: <strong style="color: green">{{number_format($somaComissao, 2, ',', '.')}}</strong></h4>
	<h4>Soma Vendas: <strong style="color: purple">{{number_format($somaVendas, 2, ',', '.')}}</strong></h4>
</div>
@endsection
