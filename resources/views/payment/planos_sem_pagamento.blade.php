@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<h4 class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Lista de planos sem pagamento</h4>

			<label class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Registros: <strong class="text-success">{{sizeof($planosEmpresa)}}</strong></label>
			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
				<div class="row">

					

				</div>
			</div>

			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

					<table class="datatable-table" style="max-width: 100%; overflow: scroll">
						<thead class="datatable-head">
							<tr class="datatable-row" style="left: 0px;">

								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">Empresa</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Data</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Plano</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Valor</span></th>
								
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">Ações</span></th>
							</tr>
						</thead>

						@php
						$soma = 0;
						@endphp
						<tbody class="datatable-body">
							@foreach($planosEmpresa as $p)
							<tr class="datatable-row">
								<td class="datatable-cell">
									<span class="codigo" style="width:250px;">
										{{$p->empresa->nome}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 150px;">
										{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i:s')}}
									</span>
								</td>
								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">
										{{$p->plano->nome}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">
										{{number_format($p->plano->valor, 2, ',', '.')}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 200px;">
										<a href="/financeiro/pay/{{$p->id}}" class="btn btn-sm btn-primary">
											Atribuir
										</a>

										<a onclick='swal("Atenção!", "Deseja remover este pagamento?", "warning").then((sim) => {if(sim){ location.href="/financeiro/removerPlano/{{ $p->id }}" }else{return false} })' href="#!"  class="btn btn-sm btn-danger">
											Remover
										</a>
									</span>
								</td>
							
							@php
							$soma += $p->plano->valor;
							@endphp
							@endforeach
						</tbody>
					</table>
				</div>
			
			</div>
		</div>
	</div>
</div>

@endsection	
