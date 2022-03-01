@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<h4 class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Lista de Pagamentos com Alteração de status</h4>

			<label class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Registros: <strong class="text-success">{{sizeof($payments)}}</strong></label>
			
			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

					<table class="datatable-table" style="max-width: 100%; overflow: scroll">
						<thead class="datatable-head">
							<tr class="datatable-row" style="left: 0px;">
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">#</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">Empresa</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Data</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Plano</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Valor</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Status</span></th>
								
							</tr>
						</thead>

						
						<tbody class="datatable-body">
							@foreach($payments as $p)

							<tr class="datatable-row">
								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">
										{{$p->transacao_id}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 250px;">
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
										{{$p->plano->plano->nome}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">
										{{number_format($p->valor, 2, ',', '.')}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">

										@if($p->status == 'approved')
										<span class="label label-xl label-inline label-light-success">Aprovado</span>
										@elseif($p->status == 'pending')
										<span class="label label-xl label-inline label-light-warning">Pendente</span>
										@elseif($p->status == 'rejected')
										<span class="label label-xl label-inline label-light-danger">Rejeitado</span>
										@else
										<span class="label label-xl label-inline label-light-dark">Não identificado</span>
										@endif
									</span>
								</td>

							</tr>

						
							@endforeach
						</tbody>
					</table>
				</div>
				
			</div>
		</div>
	</div>
</div>

@endsection	
