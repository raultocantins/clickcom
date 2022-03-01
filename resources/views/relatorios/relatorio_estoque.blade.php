@extends('relatorios.cabecalho')
@section('content')

<div class="row">
	<div class="col s12">
		<h3 class="center-align">Relátorio de Estoque</h3>
		<h3 class="center-align">Categoria: {{$categoria}}</h3>

	</div>


	<table class="pure-table">
		<thead>
			<tr>

				<th width="200">PRODUTO</th>
				<th width="80">ESTOQUE ATUAL</th>
				<th width="80">CUSTO</th>
				<th width="80">MARGEM LUCRO</th>
				<th width="80">VALOR DE VENDA</th>
				<th width="120">VALOR TOTAL DE ESTOQUE</th>
				<th width="80">DATA ULT. COMPRA</th>
			</tr>
		</thead>


		<tbody>
			@php 
			$somaEstoque = 0;
			$somaValorEstoque = 0;
			@endphp
			@foreach($produtos as $key => $p)
			<tr class="@if($key%2 == 0) pure-table-odd @endif">

				<td>{{$p->nome}} {{$p->str_grade}}</td>
				@if($p->unidade_venda == 'UNID' || $p->unidade_venda == 'UN')
				<td>{{number_format($p->quantidade)}} {{$p->unidade_venda}}</td>
				@else
				<td>{{number_format($p->quantidade, 3, ',', '.')}} {{$p->unidade_venda}}</td>
				@endif
				<td>R$ {{number_format($p->valor_compra, 2, ',', '.')}}</td>
				<td>{{number_format($p->percentual_lucro, 2)}}%</td>
				<td>R$ {{number_format($p->valor_venda, 2, ',', '.')}}</td>
				<td>R$ {{number_format($p->valor_venda*$p->quantidade, 2, ',', '.')}}</td>
				<td>{{$p->data_ultima_compra}}</td>
				@php 
				$somaEstoque += $p->quantidade;
				$somaValorEstoque += $p->valor_venda*$p->quantidade;
				@endphp
			</tr>
			@endforeach
		</tbody>
	</table>
	<h4>Quantidade estoque: <strong style="color: red">{{number_format($somaEstoque, 2)}}</strong></h4>

	<h4>Soma valor de estoque: <strong style="color: green">R${{number_format($somaValorEstoque, 2, ',', '.')}}</strong></h4>

</div>
@endsection
