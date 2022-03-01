@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<input type="hidden" id="_token" value="{{ csrf_token() }}">
			<form class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft" method="get" action="/pedidosEcommerce/filtro">
				<div class="row align-items-center">

					<div class="form-group col-lg-3 col-md-4 col-sm-6">
						<label class="col-form-label">Cliente</label>
						<div class="">
							<div class="input-group">
								<input type="text" name="cliente" class="form-control" value="{{{isset($cliente) ? $cliente : ''}}}" />
							</div>
						</div>
					</div>

					<div class="form-group col-lg-2 col-md-4 col-sm-6">
						<label class="col-form-label">Data Inicial</label>
						<div class="">
							<div class="input-group date">
								<input type="text" name="data_inicial" class="form-control date-out" readonly value="{{{isset($dataInicial) ? $dataInicial : ''}}}" id="kt_datepicker_3" />
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
								<input type="text" name="data_final" class="form-control" readonly value="{{{isset($dataFinal) ? $dataFinal : ''}}}" id="kt_datepicker_3" />
								<div class="input-group-append">
									<span class="input-group-text">
										<i class="la la-calendar"></i>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group col-lg-2 col-md-4 col-sm-6">
						<label class="col-form-label">Estado</label>
						<div class="">
							<div class="input-group date">
								<select class="custom-select form-control" id="estado" name="estado">
									<option @if(isset($estado) && $estado == 'TODOS') selected @endif value="TODOS">TODOS</option>
									<option @if(isset($estado) && $estado == '0') selected @endif value="0">NOVO</option>
									<option @if(isset($estado) && $estado == '1') selected @endif value="1">APROVADO</option>
									<option @if(isset($estado) && $estado == '2') selected @endif value="2">CANCELADO</option>
									<option @if(isset($estado) && $estado == '3') selected @endif value="3">AGUARDANDO ENVIO</option>
									<option @if(isset($estado) && $estado == '4') selected @endif value="4">ENVIADO</option>
									<option @if(isset($estado) && $estado == '5') selected @endif value="5">ENTREGUE</option>
									
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-2 col-xl-2 mt-2 mt-lg-0">
						<button style="margin-top: 15px;" class="btn btn-light-primary px-6 font-weight-bold">Pesquisa</button>
					</div>
				</div>
			</form>
			<br>
			<h4 class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Lista de Pedidos</h4>

			<label class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Registros: <strong class="text-success">{{sizeof($pedidos)}}</strong></label>
			
			<a style="margin-left: 10px;" class="btn btn-info @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight" href="/pedidosEcommerce/verificaPagamentos">
				<i class="la la-refresh"></i>
				Consultar/Atualizar pagamentos
			</a>

		</div>

		<div class="row @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">
			<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">

				<div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="between" data-wizard-clickable="true">
					<!--begin: Wizard Nav-->

					<div class="wizard-nav">

						<div class="wizard-steps px-8 py-8 px-lg-15 py-lg-3">
							<!--begin::Wizard Step 1 Nav-->
							<div class="wizard-step" data-wizard-type="step" data-wizard-state="done">
								<div class="wizard-label">
									<h3 class="wizard-title">
										<span>
											<i style="font-size: 40px" class="la la-table"></i>
											Tabela
										</span>
									</h3>
									<div class="wizard-bar"></div>
								</div>
							</div>
							<!--end::Wizard Step 1 Nav-->
							<!--begin::Wizard Step 2 Nav-->
							<div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
								<div class="wizard-label" id="grade">
									<h3 class="wizard-title">
										<span>
											<i style="font-size: 40px" class="la la-tablet"></i>
											Grade
										</span>
									</h3>
									<div class="wizard-bar"></div>
								</div>
							</div>

						</div>
					</div>


					<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">

						<!--begin: Wizard Form-->
						<form class="form fv-plugins-bootstrap fv-plugins-framework" id="kt_form">
							<!--begin: Wizard Step 1-->
							<div class="pb-5" data-wizard-type="step-content">

								<!-- Inicio da tabela -->

								<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
									<div class="row">
										<div class="col-xl-12">

											<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

												<table class="datatable-table" style="max-width: 100%; overflow: scroll">
													<thead class="datatable-head">
														<tr class="datatable-row" style="left: 0px;">
															
															<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 70px;">ID</span></th>
															<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">Cliente</span></th>
															<th data-field="Country" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Data</span></th>
															<th data-field="ShipDate" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Forma de pagamento</span></th>

															<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Estado de Pagamento</span></th>

															<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Estado de Envio</span></th>

															<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">NFe</span></th>
															
															<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Valor</span></th>
															<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Frete</span></th>
															<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Valor Total</span></th>
															<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 320px;">Ações</span></th>
														</tr>
													</thead>

													<tbody id="body" class="datatable-body">
														<?php $total = 0; ?>
														@foreach($pedidos as $p)

														<tr class="datatable-row">
															
															<td class="datatable-cell"><span class="codigo" style="width: 70px;" id="id">{{$p->id}}</span>
															</td>

															<td class="datatable-cell"><span class="codigo" style="width: 200px;" id="id">{{$p->cliente->nome}} {{$p->cliente->sobre_nome}}</span>
															</td>

															<td class="datatable-cell">
																<span class="codigo" style="width: 100px;" id="id">
																	{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i:s')}}
																</span>
															</td>
															<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{$p->forma_pagamento}}</span>
															</td>

															<td class="datatable-cell">
																<span class="codigo" style="width: 100px;" id="id">
																	@if($p->status_pagamento == 'pending')
																	<span class="label label-xl label-inline label-light-warning">Pendente</span>

																	@elseif($p->status_pagamento == 'approved')
																	<span class="label label-xl label-inline label-light-success">Aprovado</span>

																	@else
																	<span class="label label-xl label-inline label-light-danger">Rejeitado</span>
																	@endif
																</span>
															</td>

															<td class="datatable-cell">
																<span class="codigo" style="width: 100px;" id="id">
																	@if($p->status_preparacao == 0)

																	<span class="label label-xl label-inline label-light-info">Novo</span>
																	@elseif($p->status_preparacao == 1)
																	<span class="label label-xl label-inline label-light-primary">Aprovado</span>
																	@elseif($p->status_preparacao == 2)
																	<span class="label label-xl label-inline label-light-danger">Cancelado</span>
																	@elseif($p->status_preparacao == 3)
																	<span class="label label-xl label-inline label-light-warning">Aguardando Envio</span>
																	@elseif($p->status_preparacao == 4)
																	<span class="label label-xl label-inline label-light-dark">Enviado</span>
																	@else
																	<span class="label label-xl label-inline label-light-success">Entregue</span>
																	@endif
																</span>
															</td>

															<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{$p->numero_nfe > 0 ? $p->numero_nfe : '--'}}</span>
															</td>
															
															<td class="datatable-cell">
																<span class="codigo" style="width: 100px;" id="id">
																	{{number_format($p->valor_total - $p->valor_frete, 2, ',', '.')}}
																</span>
															</td>

															<td class="datatable-cell">
																<span class="codigo" style="width: 100px;" id="id">
																	{{number_format($p->valor_frete, 2, ',', '.')}}
																</span>
															</td>

															<td class="datatable-cell">
																<span class="codigo" style="width: 100px;" id="id">
																	{{number_format($p->valor_total, 2, ',', '.')}}
																</span>
															</td>

															<td>
																<div class="row">
																	<span style="width: 320px;">

																		@if($p->status == 1)
																		<a class="btn btn-danger" onclick='swal("Atenção!", "Deseja excluir este registro?", "warning").then((sim) => {if(sim){ location.href="/pedidosEcommerce/delete/{{ $p->id }}" }else{return false} })' href="#!">
																			<i class="la la-trash"></i>				
																		</a>
																		@endif

																		<a class="btn btn-info" href="/pedidosEcommerce/detalhar/{{ $p->id }}">
																			<i class="la la-file"></i>
																		</a>
																	</span>
																</div>
															</td>
														</tr>
														<?php 
														$total += $p->valor_total;
														?>
														@endforeach

													</tbody>
												</table>
											</div>
										</div>

									</div>
								</div>
								<!-- Fim da tabela -->
							</div>

							<!--end: Wizard Step 1-->
							<!--begin: Wizard Step 2-->
							<div class="pb-5" data-wizard-type="step-content">

								<!-- Inicio do card -->

								<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
									<div class="row">

										@foreach($pedidos as $p)
										<div class="col-sm-6 col-lg-6 col-md-6 col-xl-6">

											<div class="card card-custom gutter-b example example-compact">
												<div class="card-header">
													<div class="card-title">
														<h3 style="width: 230px; font-size: 15px; height: 10px;" class="card-title">
															<strong class="text-success"> </strong>

															{{$p->cliente->nome}}

														</h3>

													</div>
													<div class="card-toolbar">
														<div class="dropdown dropdown-inline" data-toggle="tooltip" title="" data-placement="left" data-original-title="Ações">
															<a href="#" class="btn btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<i class="fa fa-ellipsis-h"></i>
															</a>
															<div class="dropdown-menu p-0 m-0 dropdown-menu-md dropdown-menu-right">
																<!--begin::Navigation-->
																<ul class="navi navi-hover">
																	<li class="navi-header font-weight-bold py-4">
																		<span class="font-size-lg">Ações:</span>
																	</li>
																	<li class="navi-separator mb-3 opacity-70"></li>

																	<li class="navi-item">
																		<a href="/pedidosEcommerce/detalhar/{{$p->id}}" class="navi-link">
																			<span class="navi-text">
																				<span class="label label-xl label-inline label-light-info">Detalhar</span>
																			</span>
																		</a>
																	</li>
																	<li class="navi-item">
																		<a onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/pedidosEcommerce/delete/{{ $p->id }}" }else{return false} })' href="#!" class="navi-link">
																			<span class="navi-text">
																				<span class="label label-xl label-inline label-light-danger">Excluir</span>
																			</span>
																		</a>
																	</li>


																</ul>
																<!--end::Navigation-->
															</div>
														</div>

													</div>
												</div>

												<div class="card-body">

													<div class="kt-widget__info">
														<span class="kt-widget__label">Cliente:</span>
														<a target="_blank" class="kt-widget__data text-success">
															{{$p->cliente->nome}} {{$p->cliente->sobre_nome}}
														</a>
													</div>

													<div class="kt-widget__info">
														<span class="kt-widget__label">Valor Total:</span>
														<a target="_blank" class="kt-widget__data text-success">
															R$ {{ number_format($p->valor_total, 2, ',', '.') }}
														</a>
													</div>

													<div class="kt-widget__info">
														<span class="kt-widget__label">Valor Frete:</span>
														<a target="_blank" class="kt-widget__data text-success">
															R$ {{ number_format($p->valor_frete, 2, ',', '.') }}
														</a>
													</div>

													<div class="kt-widget__info">
														<span class="kt-widget__label">Data:</span>
														<a target="_blank" class="kt-widget__data text-success">
															{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i:s')}}
														</a>
													</div>

													<div class="kt-widget__info">
														<span class="kt-widget__label">Estado de pagamento:</span>
														<a target="_blank" class="kt-widget__data text-success">

															@if($p->status_pagamento == 'pending')
															<span class="label label-xl label-inline label-light-warning">Pedente</span>

															@elseif($p->status_pagamento == 'approved')
															<span class="label label-xl label-inline label-light-success">Aprovado</span>
															@else
															<span class="label label-xl label-inline label-light-danger">Cancelado/Rejeitado</span>
															@endif
														</a>
													</div>

													
													<div class="kt-widget__info">
														<span class="kt-widget__label">Estado de envio:</span>
														<a target="_blank" class="kt-widget__data text-success">

															@if($p->status_preparacao == 0)
															<span class="label label-xl label-inline label-light-info">Novo</span>

															@elseif($p->status_preparacao == 1)
															<span class="label label-xl label-inline label-light-primary">Aprovado</span>
															@elseif($p->status_preparacao == 2)
															<span class="label label-xl label-inline label-light-danger">Cancelado</span>

															@elseif($p->status_preparacao == 3)
															<span class="label label-xl label-inline label-light-warning">Aguardando Envio</span>

															@elseif($p->status_preparacao == 4)
															<span class="label label-xl label-inline label-light-dark">Enviado</span>

															@else
															<span class="label label-xl label-inline label-light-success">Entregue</span>
															@endif
														</a>
													</div>

													<div class="kt-widget__info">
														<span class="kt-widget__label">Forma de pagamento:</span>
														<a target="_blank" class="kt-widget__data text-success">
															{{ $p->forma_pagamento }}
														</a>
													</div>

													<div class="kt-widget__info">
														<span class="kt-widget__label">NFe:</span>
														<a target="_blank" class="kt-widget__data text-success">
															{{$p->numero_nfe > 0 ? $p->numero_nfe : '--'}}
														</a>
													</div>

													
												</div>
											</div>

										</div>
										@endforeach

									</div>
								</div>
							</div>
							<!--end: Wizard Step 2-->
							<div class="d-flex justify-content-between align-items-center flex-wrap">
								<div class="d-flex flex-wrap py-2 mr-3">
									@if(isset($links))
									{{$vendas->links()}}
									@endif
								</div>
							</div>
						</form>

					</div>
				</div>
			</div>
		</div>
	</div>

</div>



@endsection	