@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">


	<div class="card-body">
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-sm-12 col-lg-4 col-md-6 col-xl-4">

				
			</div>
		</div>
		<br>


		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">
			<form class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft" method="get" action="/enviarXml/filtroCfopGet">
				<div class="row align-items-center">

					
					<div class="form-group col-lg-2 col-md-4 col-sm-6">
						<label class="col-form-label">Data Inicial</label>
						<div class="">
							<div class="input-group date">
								<input required type="text" name="data_inicial" class="form-control" readonly value="{{{isset($dataInicial) ? $dataInicial : ''}}}" id="kt_datepicker_3" />
								<div class="input-group-append">
									<span class="input-group-text">
										<i class="la la-calendar"></i>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group col-lg-2 col-md-4 col-sm-6">
						<label class="col-form-label">Data Final</label>
						<div class="">
							<div class="input-group date">
								<input required type="text" name="data_final" class="form-control" readonly value="{{{isset($dataFinal) ? $dataFinal : ''}}}" id="kt_datepicker_3" />
								<div class="input-group-append">
									<span class="input-group-text">
										<i class="la la-calendar"></i>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group col-lg-2 col-md-4 col-sm-6">
						<label class="col-form-label">CFOP</label>
						<div class="">
							<div class="input-group date">
								<input value="{{isset($cfop) ? $cfop : ''}}" data-mask="0000" type="" class="form-control" name="cfop">
							</div>
						</div>
					</div>

					<div class="col-lg-2 col-xl-2 mt-2 mt-lg-0">
						<button style="margin-top: 15px;" class="btn btn-light-primary px-6 font-weight-bold">Pesquisa</button>
					</div>
				</div>
			</form>
			
			<br>
			@isset($itens)
			<label>Total de registros: {{sizeof($itens)}}</label>
			<div class="row">
				<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">

					
					
					<div class="row">
						<div class="col-xl-12">

							<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

								<table class="datatable-table" style="max-width: 100%; overflow: scroll">
									<thead class="datatable-head">
										<tr class="datatable-row" style="left: 0px;">
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">Produto</span></th>
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Quantidade</span></th>

											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Unidade</span></th>
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">R$ Total</span></th>

										</tr>
									</thead>

									@php
									$somaValor = 0;
									$somaQuantidade = 0;
									@endphp
									<tbody id="body" class="datatable-body">
										@foreach($itens as $i)
										<tr class="datatable-row">
											<td class="datatable-cell"><span class="codigo" style="width: 200px;" id="id">{{$i->produto->nome}}</span>
											</td>
											<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$i->produto->unidade_venda}}</span>
											</td>
											<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{number_format($i->quantidade, 2, ',', '.')}}</span>
											</td>
											<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{ number_format($i->total, 2, ',', '.') }}</span>
											</td>

											@php
											$somaValor += $i->total;
											$somaQuantidade += $i->quantidade;
											@endphp

										</tr>
										@endforeach
									</tbody>
								</table>
							</div>

							<h4>Total quantidade: <strong class="text-info">{{$somaQuantidade}}</strong></h4>
							<h4>Total valor: <strong class="text-info">{{ number_format($somaValor, 2, ',', '.') }}</strong></h4>

							@php
							$percentual = $somaTotalVendas > 0 ? (100 - ((($somaValor-$somaTotalVendas)/$somaTotalVendas*100)*-1)) : 0;

							@endphp
							<h4>Percentual: <strong class="text-info">{{ number_format($percentual, 2, ',', '.') }}%</strong></h4>

							<form method="get" action="/enviarXml/filtroCfopImprimir">
								<!-- <input type="hidden" value="{{json_encode($itens)}}" name="objeto"> -->
								<input type="hidden" value="{{$dataInicial}}" name="dataInicial">
								<input type="hidden" value="{{$percentual}}" name="percentual">
								<input type="hidden" value="{{$dataFinal}}" name="dataFinal">
								<input type="hidden" value="{{$cfop}}" name="cfop">
								<input type="hidden" value="{{$somaTotalVendas}}" name="somaTotalVendas">

								<button type="submit" class="btn btn-light-primary">
									<i class="la la-print"></i>
									Imprimir
								</button>
							</form>
						</div>
					</div>
				</div>

			</div>
			@endisset

		</div>

	</div>

</div>

@endsection