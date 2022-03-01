@extends('relatorios.cabecalho')
@section('content')
<div class="row">
	<div class="col s12">
		<h3 class="center-align">Relátorio de Tipos de Pagamento</h3>
		@if($data_inicial && $data_final)
		<h4>Periodo: {{$data_inicial}} - {{$data_final}}</h4>
		@endif
	</div>

	<table class="pure-table">
		<thead>
			<tr>
				<th width="200">TIPO</th>
				<th width="200">TOTAL</th>
				<!-- <th width="150">ITENS VENDIDOS</th> -->
			</tr>
		</thead>

		

		<tbody>
			@foreach($somaTiposPagamento as $key => $v)
			<tr class="@if($key%2 == 0) pure-table-odd @endif">
				<td><center>{{App\Models\VendaCaixa::getTipoPagamento($key)}}</center></td>
				<td><center>{{number_format($v, 2, ',', '.')}}</center></td>

			</tr>
			@endforeach
		</tbody>
	</table>


</div>

@endsection
