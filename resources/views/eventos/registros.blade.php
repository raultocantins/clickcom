@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">
	<div class="card-body">
		<div class="card card-custom gutter-b example example-compact">
			<div class="card-header">

				<h3 class="card-title">
					Registros do evento: <strong style="margin-left: 5px;" class="text-info">{{$evento->nome}}</strong>
				</h3>

			</div>

			<div class="card-body">
				<div class="row">

					<div class="col-lg-6">
						<h3 class="card-title">Finalizadas</h3>
						@php $soma = 0; @endphp
						@foreach($atividadesConcluidas as $a)
						<span class="text-success">{{ \Carbon\Carbon::parse($a->created_at)->format('H:i')}} - R$ {{number_format($a->total, 2, ',', '.')}}</span><br>

						@php $soma += $a->total; @endphp
						@endforeach

						<h4>Soma: <strong class="text-success">{{number_format($soma, 2, ',', '.')}}</strong></h4>
					</div>
					<div class="col-lg-6">
						<h3 class="card-title">Pendentes</h3>

						@php $soma = 0; @endphp

						@foreach($atividadesPendentes as $a)
						<span class="text-danger">{{ \Carbon\Carbon::parse($a->created_at)->format('H:i')}} - R$ {{number_format($a->total, 2, ',', '.')}}</span><br>
						@php $soma += $a->total; @endphp

						@endforeach

						<h4>Soma: <strong class="text-danger">{{number_format($soma, 2, ',', '.')}}</strong></h4>
					</div>
				</div>
				<br>
				@if($adm)
				<div class="row">
					<div class="col-lg-12">

						<h2>Somatório total: <strong class="text-primary">R$ {{number_format($somatorio, 2, ',', '.')}}</strong></h2>



						@foreach($somaEmGrupo as $s)
						<h4>{{$s->forma_pagamento ?? 'Outros'}} - <strong class="text-info">R$ {{number_format($s->total, 2, ',', '.')}}</strong></h4>
						@endforeach
					</div>


				</div>
				@endif

			</div>

		</div>
	</div>
</div>

@endsection