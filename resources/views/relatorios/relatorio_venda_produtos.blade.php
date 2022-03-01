@extends('relatorios.cabecalho')
@section('content')
<div class="row">
	<div class="col s12">
		<h3 class="center-align">Relatório Custo/Venda</h3>
		@if($data_inicial && $data_final)
		<h4>Periodo: {{$data_inicial}} - {{$data_final}}</h4>
		@endif
	</div>

	<table class="pure-table">
		<thead>
			<tr>
				<th width="50">CÓD</th>
				<th width="200">DESCRIÇÃO</th>
				<th width="80">VL CUSTO</th>
				<th width="80">VL VENDA</th>
				<th width="80">QTD</th>
				<th width="80">TOTAL CUSTO/VENDA</th>
			</tr>
		</thead>

		@php
		$somaCusto = 0;
		$somaVenda = 0;
		$somaItens = 0;
		@endphp
		<tbody>
			@foreach($itens as $key => $i)
			<tr class="@if($key%2 == 0) pure-table-odd @endif">
				<td><center>{{$i['id']}}</center></td>
				<td>
					<center>{{$i['nome']}} 
						@if($i['grade'])
						{{$i['str_grade']}}
						@endif
					</center>
				</td>
				<td><center>{{number_format($i['valor_compra'], 2, ',', '.')}}</center></td>
				<td><center>{{number_format($i['valor_venda'], 2, ',', '.')}}</center></td>

				@if($i['unidade'] == 'UN')
				<td><center>{{number_format($i['total'])}} {{$i['unidade']}}</center></td>
				@else
				<td><center>{{number_format($i['total'], 2)}} {{$i['unidade']}}</center></td>

				@endif
				
				<td>
					<center>
						{{number_format($i['valor_compra']*$i['total'], 2, ',', '.')}}/{{number_format($i['valor_venda']*$i['total'], 2, ',', '.')}}
					</center>
				</td>

			</tr>

			@php
			$somaCusto += $i['valor_compra']*$i['total'];
			$somaVenda += $i['valor_venda']*$i['total'];
			$somaItens += $i['total'];
			@endphp

			@endforeach
		</tbody>
	</table>

	<h4>Soma Custo: <strong style="color: red">{{number_format($somaCusto, 2, ',', '.')}}</strong></h4>
	<h4>Soma Venda: <strong style="color: green">{{number_format($somaVenda, 2, ',', '.')}}</strong></h4>
	<h4>Soma Itens: <strong style="color: purple">{{$somaItens}}</strong></h4>
</div>
@endsection
