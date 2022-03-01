@extends('relatorios.cabecalho')
@section('content')

<div class="row">
	<div class="col s12">
		<h3 >Relátorio de Evento</h3>
	</div>

	@if(isset($data_inicial) && isset($data_final))
	<h3>Periodo: {{$data_inicial}} - {{$data_final}}</h3>
	@endif

	@if(isset($funcionario))
	<h3>Funcionário: {{$funcionario}}</h3>
	@endif

	@if(isset($evento))
	<h3>Evento: {{$evento}}</h3>
	@endif

	@if(isset($status))
	<h3>Estado: 
		@if($status == 1)
		CONCLUIDO
		@elseif($status == 0)
		PENDENTE
		@else
		TODOS
		@endif
	</h3>
	@endif

	<table class="pure-table">
		<thead>
			<tr>


				<th width="100">RESPONSÁVEL</th>
				<th width="80">TELEFONE</th>
				<th width="100">CRIANÇA</th>
				<th width="80">DATA</th>
				<th width="80">INICIO/FIM</th>
				<th width="80">VALOR</th>
				<th width="80">FORMA DE PAGAMENTO</th>
			</tr>
		</thead>

		<tbody>
			<?php $soma = 0; ?>
			@foreach($atividades as $key => $e)
			<tr class="@if($key%2 == 0) pure-table-odd @endif">

				<td>{{$e->responsavel_nome}}</td>
				<td>{{$e->responsavel_telefone}}</td>
				<td>{{$e->crianca_nome}}</td>
				<td>{{ \Carbon\Carbon::parse($e->created_at)->format('d/m/Y')}}</td>
				<td>{{ \Carbon\Carbon::parse($e->inicio)->format('H:i')}}/{{ \Carbon\Carbon::parse($e->fim)->format('H:i')}}</td>
				<td>{{ number_format($e->total, 2, ',', '.')}}</td>
				<td>{{$e->forma_pagamento}}</td>

				@php
				$soma += $e->total;
				@endphp
			</tr>

			@endforeach
		</tbody>
	</table>
	<hr>

	<div class="row">
		<h2>Total: <strong style="color: green">R$ {{number_format($soma, 2, ',', '.')}}</strong></h2>

		@foreach($somaEmGrupo as $s)
						<h4>{{$s->forma_pagamento ?? 'Outros'}} - <strong class="text-info">R$ {{number_format($s->total, 2, ',', '.')}}</strong></h4>
						@endforeach
	</div>
</div>
@endsection

