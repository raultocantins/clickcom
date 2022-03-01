@extends('relatorios.cabecalho')
@section('content')
<div class="row">
	<div class="col s12">
		<h3 class="center-align">Relátorio de Produtos com Filtro</h3>
	</div>

	<table class="pure-table">
		<thead>
			<tr>
				<th width="200">PRODUTO</th>
				<th width="80">ESTOQUE</th>
				<th width="80">CATEGORIA</th>
				<th width="120">TOTAL CUSTO</th>
				<th width="120">TOTAL VENDA</th>
				<!-- <th width="150">ITENS VENDIDOS</th> -->
			</tr>
		</thead>

		

		<tbody>
			@foreach($produtos as $key => $p)
			<tr class="@if($key%2 == 0) pure-table-odd @endif">
				<td>{{$p->nome}}</td>
				@if($p->estoque)
				<td>{{number_format($p->estoque->quantidade, 2, ',', '.')}}</td>
				@else
				<td>--</td>
				@endif

				<td>{{$p->categoria->nome}}</td>
				@if($p->estoque)
				<td>{{number_format($p->valor_venda*$p->estoque->quantidade, 2, ',', '.')}}</td>
				<td>{{number_format($p->valor_compra*$p->estoque->quantidade, 2, ',', '.')}}</td>
				@else
				<td>--</td>
				<td>--</td>
				@endif
			</tr>
			@endforeach
		</tbody>
	</table>


</div>

@endsection
