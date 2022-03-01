@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">

	<div class="card-body">
		<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
			<h4>Lista de Produtos: <strong class="text-primary">{{$uf}}</strong></h4>
			
			<div class="row">
				<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
					<div class="row">
						<div class="col-xl-12">

							<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

								<table class="datatable-table" style="max-width: 100%; overflow: scroll">
									<thead class="datatable-head">
										<tr class="datatable-row" style="left: 0px;">
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 300px;">Produto</span></th>
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Valor venda</span></th>
											
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">%ICMS</span></th>
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Ações</span></th>
										</tr>
									</thead>

									<tbody id="body" class="datatable-body">
										@foreach($tribs as $i)
										<tr class="datatable-row">
											<td class="datatable-cell"><span class="codigo" style="width: 300px;" id="id">{{$i->produto->nome}}</span>
											</td>
											<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{number_format($i->produto->valor_venda, 2)}}</span>
											</td>
											<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{number_format($i->percentual_icms, 2)}}</span>
											</td>
											
											<td class="datatable-cell">
												<span class="codigo" style="width: 120px;" id="id">
													<a class="btn btn-light-primary" href="/percentualuf/editPercentual/{{ $i->id }}">
														<i class="la la-edit"></i>
													</a>
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

			
		</div>

	</div>
</div>


@endsection	