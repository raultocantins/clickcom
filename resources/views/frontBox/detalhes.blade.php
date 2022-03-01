@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">

	<div class="card-body">
		<div class="content d-flex flex-column flex-column-fluid" id="kt_content" >

			<div class="row" id="anime" style="display: none">
				<div class="col s8 offset-s2">
					<lottie-player src="/anime/success.json" background="transparent" speed="0.8" style="width: 100%; height: 300px;" autoplay >
					</lottie-player>
				</div>
			</div>

			<div class="col-lg-12" id="content">
				<!--begin::Portlet-->

				<h3 class="card-title">Venda código: <strong>{{$venda->id}}</strong></h3>

				<div class="row">
					<div class="col-xl-12">

						<div class="kt-section kt-section--first">
							<div class="kt-section__body">

								<div class="row">
									

									<div class="col-12">
										<h4>Estado: 
											@if($venda->estado == 'DISPONIVEL')
											<span class="label label-xl label-inline label-light-primary">Disponível</span>

											@elseif($venda->estado == 'APROVADO')
											<span class="label label-xl label-inline label-light-success">Aprovado</span>
											@elseif($venda->estado == 'CANCELADO')
											<span class="label label-xl label-inline label-light-danger">Cancelado</span>
											@else
											<span class="label label-xl label-inline label-light-warning">Rejeitado</span>
											@endif
										</h4>

										<h4>Chave NFCe: <strong class="text-info">{{$venda->chave != "" ? $venda->chave : '--'}}</strong></h4>
										
										@if($adm)
										<a href="/nfce/estadoFiscal/{{$venda->id}}" class="btn btn-danger">
											<i class="la la-warning"></i>
											Alterar estado fiscal da venda
										</a>
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<hr>
				<div class="row">
					<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">
						<h3>Itens da Venda</h3>
						<table class="datatable-table" style="max-width: 100%; overflow: scroll;" id="prod">
							<thead class="datatable-head">
								<tr class="datatable-row" style="left: 0px;">
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">ID</span></th>
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 450px;">Produto</span></th>
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Quantidade</span></th>
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Valor</span></th>
									<th data-field="Country" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Subtotal</span></th>
								</tr>
							</thead>

							<tbody class="datatable-body">
								<?php $somaItens = 0; ?>
								@foreach($venda->itens as $i)
								<tr class="datatable-row" style="left: 0px;">

									<td class="datatable-cell"><span class="codigo" style="width: 120px;">{{$i->produto->id}}</span></td>
									<td class="datatable-cell">
										<span class="codigo" style="width: 450px;">{{$i->produto->nome}} 
											{{$i->produto->grade ? " (" . $i->produto->str_grade . ")" : ""}}
										</span>
									</td>

									<td class="datatable-cell"><span class="codigo" style="width: 120px;">{{$i->quantidade}}</span></td>
									<td class="datatable-cell"><span class="codigo" style="width: 120px;">{{number_format($i->valor, $casasDecimais, ',', '.')}}</span></td>

									<td class="datatable-cell"><span class="codigo" style="width: 120px;">{{number_format($i->valor*$i->quantidade, $casasDecimais, ',', '.')}}</span></td>


								</tr>
								<?php $somaItens+=  $i->valor * $i->quantidade?>

								@endforeach
							</tbody>
						</table>
					</div>
					<h4>Soma: <strong class="text-info">R$ {{number_format($somaItens, $casasDecimais, ',', '.')}}</strong></h4>
				</div>

				<hr>

				<div class="row">
					@if($venda->NFcNumero && $venda->estado == 'APROVADO')

					<a target="_blank" href="/nfce/imprimir/{{$venda->id}}" class="btn btn-lg btn-light-success">
						<i class="la la-print"></i>
						Imprimir fiscal
					</a>

					@endif

					<a style="margin-left: 5px;" target="_blank" href="/nfce/imprimirNaoFiscal/{{$venda->id}}" class="btn btn-lg btn-light-info">
						<i class="la la-print"></i>
						Imprimir não fiscal
					</a>
				</div>

			</div>
		</div>
	</div>
</div>




@endsection	