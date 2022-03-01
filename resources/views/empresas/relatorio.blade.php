@extends('relatorios.cabecalho')
@section('content')

<div class="row">
	<div class="col s12">
		<h3 >Relátorio de Empresas</h3>
	</div>

	<table class="pure-table">
		<thead>
			<tr>

				<th width="80">ID</th>
				<th width="140">NOME</th>
				<th width="180">ENDEREÇO</th>
				<th width="100">CIDADE</th>
				<th width="130">PLANO</th>
				<th width="100">STATUS</th>
			</tr>
		</thead>

		<tbody>
			<?php $soma = 0; ?>
			@foreach($empresas as $key => $e)
			<tr class="@if($key%2 == 0) pure-table-odd @endif">

				<td><center>{{ $e->id}}</td>
					<td><center>{{ $e->nome}}</center></td>
					<td>
						<center>{{$e->rua}}, {{$e->numero}} - {{$e->bairro}}</center>
					</td>
					<td><center>{{$e->cidade}}</center></td>
					@if($e->planoEmpresa)
					<td><center>{{$e->planoEmpresa->plano->nome}} R$ {{number_format($e->planoEmpresa->plano->valor, 2, ',', '.')}}</center>
					@php
					$soma += $e->planoEmpresa->plano->valor;
					@endphp
					</td>
					@else
					<td><center>--</center></td>
					@endif
					<td>
						<center>
							@if($e->status() == -1)
							MASTER
							@elseif($e->status())
							ATIVO
							@else
							DESATIVADO
							@endif
						</center>
					</td>
				</tr>

				@endforeach
			</tbody>
		</table>

		<h4>Soma: <strong style="color: green">{{number_format($soma, 2, ',', '.')}}</strong></h4>
	</div>
	@endsection

