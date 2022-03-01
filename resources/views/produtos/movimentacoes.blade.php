

@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">
	
	<div class="card-body">

		<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
			<div class="card card-custom gutter-b example example-compact">
				<div class="card-header">

					<div class="col-xl-12">
						<div class="row">
							<div class="col-xl-12">
								<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">
									<br>
									<h4>Movimentações do Produto: <strong>{{$produto->nome}}</strong></h4>

									<a class="btn btn-info" href="/produtos/movimentacaoImprimir/{{$produto->id}}">
										<i class="la la-print"></i>
										Imprimir
									</a>

									<table class="datatable-table" style="max-width: 100%; overflow: scroll">
										<thead class="datatable-head">
											<tr class="datatable-row" style="left: 0px;">	
												<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">Tipo</span></th>
												<th data-field="Country" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Quanitdade</span></th>
												<th data-field="Country" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Valor</span></th>
												<th data-field="ShipDate" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Data</span></th>
											</tr>
										</thead>
										<tbody class="datatable-body">
											<?php 
											$subtotal = 0;
											?>
											@foreach($movimentacoes as $e)
											<tr class="datatable-row" style="left: 0px;">
												<td class="datatable-cell"><span class="codigo" style="width: 250px;">
													{{$e['tipo']}}
												</span></td>
												<td class="datatable-cell"><span class="codigo" style="width: 150px;">
													{{number_format($e['quantidade'], 2, ',', '.')}}
												</span></td>

												<td class="datatable-cell"><span class="codigo" style="width: 150px;">
													{{number_format($e['valor'], 2, ',', '.')}}
												</span></td>

												<td class="datatable-cell"><span class="codigo" style="width: 150px;">
													{{ \Carbon\Carbon::parse($e['data'])->format('d/m/Y H:i:s')}}
												</span></td>

											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="d-flex justify-content-between align-items-center flex-wrap">
							<div class="d-flex flex-wrap py-2 mr-3">
								@if(isset($links))
								{{$movimentacoes->links()}}
								@endif
							</div>
						</div>


					</div>
				</div>

			</div>
		</div>
	</div>

</div>

@endsection
