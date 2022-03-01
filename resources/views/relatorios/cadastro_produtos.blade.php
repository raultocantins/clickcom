@extends('relatorios.cabecalho')
@section('content')
<div class="row">
	<div class="col s12">
		<h3 class="center-align">Relátorio de Cadastro de Produtos</h3>
		@if($data_inicial && $data_final)
		<h4>Periodo: {{$data_inicial}} - {{$data_final}}</h4>
		@endif
	</div>

	<table class="pure-table">
		<thead>
			<tr>
				<th width="300">PRODUTO</th>
				<th width="140">DATA DE CADASTRO</th>
				<th width="100">ESTOQUE</th>
				<!-- <th width="150">ITENS VENDIDOS</th> -->
			</tr>
		</thead>

		

		<tbody>
			@foreach($produtos as $key => $p)
			<tr class="@if($key%2 == 0) pure-table-odd @endif">
				<td><center>{{$p->nome}}</center></td>
				<td><center>{{\Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i')}}</center></td>
				<td><center>{{$p->estoqueAtual()}}</center></td>

			</tr>
			@endforeach
		</tbody>
	</table>


</div>

@endsection
