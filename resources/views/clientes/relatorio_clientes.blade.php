@extends('relatorios.cabecalho')
@section('content')
<div class="row">
	<div class="col s12">
		<h3 class="center-align">Relátorio de Clientes</h3>
	</div>

	<table class="pure-table">
		<thead>
			<tr>
				<th width="200">CLIENTE</th>
				<th width="100">CPF/CNPJ</th>
				<th width="100">IE/RG</th>
				<th width="120">CIDADE</th>
				<th width="120">DATA DE CADASTRO</th>
				<th width="120">DATA DE ANIVERSÁRIO</th>
				<!-- <th width="150">ITENS VENDIDOS</th> -->
			</tr>
		</thead>

		

		<tbody>
			@foreach($clientes as $key => $c)
			<tr class="@if($key%2 == 0) pure-table-odd @endif">
				<td>{{$c->razao_social}}</td>
				<td>{{$c->cpf_cnpj}}</td>
				<td>{{$c->ie_rg}}</td>
				<td>{{$c->cidade->nome}} ({{$c->cidade->uf}})</td>
				<td>{{\Carbon\Carbon::parse($c->created_at)->format('d/m/Y')}}</td>
				<td>{{$c->data_aniversario}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>


</div>

@endsection
