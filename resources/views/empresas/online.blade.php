@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<input type="hidden" id="_token" value="{{ csrf_token() }}">
			

			<h4 class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Empresas Online</h4>

			<p class=" @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft text-info"> Atualização ultimos {{getenv("MINUTOS_ONLINE")}} minutos</p>

			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

					<table class="datatable-table" style="max-width: 100%; overflow: scroll">
						<thead class="datatable-head">
							<tr class="datatable-row" style="left: 0px;">
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 70px;">#</span></th>
								
								
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Nome</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Telefone</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Cidade</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Plano</span></th>
								
								
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Ultimo login</span></th>
								
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">Ações</span></th>
							</tr>
						</thead>

						<tbody class="datatable-body">
							@foreach($empresas as $e)

							<tr class="datatable-row">
								<td class="datatable-cell">
									<span class="codigo" style="width: 70px;">
										{{$e->id}}
									</span>
								</td>
								
								<td class="datatable-cell">
									<span class="codigo" style="width: 150px;">
										{{$e->nome}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 120px;">
										{{$e->telefone}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">
										{{$e->cidade}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">
										@if($e->planoEmpresa)
										{{$e->planoEmpresa->plano->nome}}
										@else
										--
										@endif
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 120px;">

										@if($e->ultimoLogin($e->id))
										{{ 
											\Carbon\Carbon::parse(
											$e->ultimoLogin2($e->id)->created_at)->format('d/m/Y H:i')
										}}
										@else
										--
										@endif
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 200px;">
										@if($e->status)
										<a href="/empresas/alterarStatus/{{$e->id}}" class="btn btn-sm btn-danger">
											Bloquear
										</a>
										@else
										<a href="/empresas/alterarStatus/{{$e->id}}" class="btn btn-sm btn-success">
											Desbloquear
										</a>
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
