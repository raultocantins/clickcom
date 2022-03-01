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
									<div class="col-lg-6 col-md-6 col-sm-6 col-12">
										<h4>Cliente: <strong class="text-success">{{$venda->cliente->razao_social}}</strong></h4>
										<h5>CNPJ: <strong class="text-success">{{$venda->cliente->cpf_cnpj}}</strong></h5>
										<h5>Data: <strong class="text-success">{{ \Carbon\Carbon::parse($venda->data_registro)->format('d/m/Y H:i:s')}}</strong></h5>
										<h5>Valor Total: <strong class="text-success">{{ number_format($venda->valor_total, $casasDecimais, ',', '.') }}</strong></h5>
										<h5>Cidade: <strong class="text-success">{{ $venda->cliente->cidade->nome }} ({{ $venda->cliente->cidade->uf }})</strong></h5>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-6 col-12">
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

										<h4>Chave NFe: <strong class="text-info">{{$venda->chave != "" ? $venda->chave : '--'}}</strong></h4>
										
										@if($adm)
										<a href="/vendas/estadoFiscal/{{$venda->id}}" class="btn btn-danger">
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
											@if($i->produto->lote != "")
											| Lote: {{$i->produto->lote}}, 
											Vencimento: {{$i->produto->vencimento}}
											@endif
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
						<h4>Soma dos itens: <strong class="text-info">R$ {{number_format($somaItens, $casasDecimais, ',', '.')}}</strong></h4>
						<h4>Desconto: <strong class="text-danger">R$ {{number_format($venda->desconto, $casasDecimais, ',', '.')}}</strong></h4>
						<h4>Acréscimo: <strong class="text-primary">R$ {{number_format($venda->acrescimo, $casasDecimais, ',', '.')}}</strong></h4>
						<h4>Total: <strong class="text-success">R$ {{number_format($venda->valor_total+$venda->acrescimo-$venda->desconto, $casasDecimais, ',', '.')}}</strong></h4>
					</div>
					
				</div>



				<hr>
				<div class="row">


					<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">
						
						<p>Forma de pagamento: <strong class="text-danger">{{$venda->forma_pagamento == 'a_vista' ? 'A vista' : $venda->forma_pagamento}}</strong></p>

						<p>Tipo de pagamento:
							<strong class="text-danger">
								{{$venda->getTipo($venda->tipo_pagamento)}}
							</strong>
						</p>

						<hr>
						<h3>Fatura</h3>

						<table class="datatable-table" style="max-width: 100%; overflow: scroll;" id="prod">
							<thead class="datatable-head">
								<tr class="datatable-row" style="left: 0px;">
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">Vencimento</span></th>
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">Valor</span></th>
								</tr>
							</thead>

							@if(sizeof($venda->duplicatas) > 0)
							<tbody class="datatable-body">
								@foreach($venda->duplicatas as $dp)
								<tr class="datatable-row" style="left: 0px;">

									<td class="datatable-cell"><span class="codigo" style="width: 200px;">
										{{ \Carbon\Carbon::parse($dp->data_vencimento)->format('d/m/Y')}}
									</span></td>
									<td class="datatable-cell"><span class="codigo" style="width: 200px;">
										{{number_format($dp->valor_integral, $casasDecimais, ',', '.')}}
									</span></td>
								</tr>
								@endforeach
							</tbody>
							@else

							<tbody class="datatable-body">
								<tr class="datatable-row" style="left: 0px;">

									<td class="datatable-cell"><span class="codigo" style="width: 200px;">
										{{ \Carbon\Carbon::parse($venda->created_at)->format('d/m/Y')}}
									</span></td>
									<td class="datatable-cell"><span class="codigo" style="width: 200px;">
										{{number_format($venda->valor_total+$venda->acrescimo-$venda->desconto, 2, ',', '.')}}
									</span></td>
								</tr>
							</tbody>
							@endif
						</table>
					</div>
				</div>

				<div class="row">
					<a target="_blank" href="/vendas/imprimirPedido/{{$venda->id}}" class="btn btn-lg btn-light-success">
						<i class="la la-print"></i>
						Imprimir
					</a>

					@if($venda->estado == 'DISPONIVEL' || $venda->estado == 'REJEITADO')
					<a style="margin-left: 3px;" href="/vendas/edit/{{$venda->id}}" class="btn btn-lg btn-light-warning">
						<i class="la la-edit"></i>
						Editar
					</a>
					@endif

					@if($venda->estado == 'APROVADO')
					<a style="margin-left: 3px;" href="/nf/imprimir/{{$venda->id}}" class="btn btn-lg btn-light-info">
						<i class="la la-print"></i>
						Imprimir Danfe
					</a>
					<a style="margin-left: 3px;" href="/nf/imprimirSimples/{{$venda->id}}" class="btn btn-lg btn-light-primary">
						<i class="la la-print"></i>
						Imprimir Danfe Simples
					</a>
					@endif
					
				</div>
				<hr>
				<div class="row">

					<div class="col-12">

						@if($venda->tipo_pagamento == '14')
						<form action="/vendas/carne" method="get" class="row" target="_blank">

							<input type="hidden" value="{{$venda->id}}" name="id">
							<div class="col-xl-2 col-4">
								<div class="form-group">
									<label>Juros</label>
									<input required type="" class="form-control money" name="juros">
								</div>
							</div>

							<div class="col-xl-2 col-4">
								<div class="form-group">
									<label>Multa</label>
									<input required type="" class="form-control money" name="multa">
								</div>
							</div>

							<div class="col-xl-3 col-6">

								<button type="submit" style="margin-left: 3px; margin-top: 22px;" class="btn btn-lg btn-light-info">
									<i class="la la-list"></i>
									Gerar Carnê
								</button>
							</div>
						</form>
						@endif
					</div>
				</div>

			</div>
		</div>
	</div>
</div>




@endsection	