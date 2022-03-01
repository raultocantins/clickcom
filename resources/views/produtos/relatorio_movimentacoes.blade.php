@extends('relatorios.cabecalho')
@section('content')
<div class="row">
	<div class="col s12">
		<h3 class="center-align">Relátorio de Movimentações Produto: <strong class="text-danger">{{$produto->nome}}</strong></h3>
	</div>

	<table class="pure-table">
		<thead>
			<tr>
				<th width="200">TIPO</th>
				<th width="100">QUANTIDADE</th>
				<th width="100">VALOR</th>
				<th width="100">DATA</th>
				<!-- <th width="150">ITENS VENDIDOS</th> -->
			</tr>
		</thead>

		

		<tbody>
			@foreach($movimentacoes as $key => $m)
			<tr class="@if($key%2 == 0) pure-table-odd @endif">
				<td>{{$m['tipo']}}</td>
				<td>{{number_format($m['quantidade'], 2, ',', '.')}}</td>
				<td>{{number_format($m['valor'], 2, ',', '.')}}</td>
				<td>
					{{ \Carbon\Carbon::parse($m['data'])->format('d/m/Y H:i:s') }}
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>


</div>

@endsection
