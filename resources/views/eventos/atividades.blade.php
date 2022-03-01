@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">


	<div class="card-body">
		<div class="">
			<div class="col-sm-12 col-lg-4 col-md-6 col-xl-4">

				<a href="/eventos/novaAtividade/{{$evento->id}}" class="btn btn-lg btn-success">
					<i class="fa fa-plus"></i>Nova Atividade
				</a>

				<a href="/eventos/registros/{{$evento->id}}" class="btn btn-lg btn-info">
					<i class="fa fa-money"></i> Registros
				</a>
			</div>
		</div>
		<br>

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<form method="get" action="/eventos/filtroAtividade">
				<div class="row align-items-center">

					<input type="hidden" name="evento_id" value="{{$evento->id}}">

					<div class="form-group col-lg-3 col-md-4 col-sm-6">
						<label class="col-form-label">Reponsável</label>
						<div class="">
							<div class="input-group">
								<input type="text" name="responsavel" class="form-control" value="{{{isset($responsavel) ? $responsavel : ''}}}" />
							</div>
						</div>
					</div>

					<div class="form-group col-lg-3 col-md-4 col-sm-6">
						<label class="col-form-label">Criança</label>
						<div class="">
							<div class="input-group">
								<input type="text" name="crianca" class="form-control" value="{{{isset($crianca) ? $crianca : ''}}}" />
							</div>
						</div>
					</div>

					<div class="form-group col-lg-2 col-md-4 col-sm-6">
						<label class="col-form-label">Estado</label>
						<div class="">
							<div class="input-group date">
								<select class="custom-select form-control" id="estado" name="estado">
									<option @if(isset($estado) && $estado == 'TODOS') selected @endif value="TODOS">TODOS</option>
									<option @if(isset($estado) && $estado == 'CONCLUIDO') selected @endif value="1">CONCLUIDO</option>
									<option @if(isset($estado) && $estado == 'OPERANDO') selected @else selected @endif value="0">OPERANDO</option>
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
			<h4>Lista de Atividades</h4>

			<div class="row">

				<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">

					<div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="between" data-wizard-clickable="true">
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

						<div class="pb-5" data-wizard-type="step-content">
							<div class="row">

								<div class="col-xl-12">

									<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

										<table class="datatable-table" style="max-width: 100%; overflow: scroll">
											<thead class="datatable-head">
												<tr class="datatable-row" style="left: 0px;">
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">RESPONSÁVEL</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">CRIANÇA</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">TELEFONE</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">STATUS</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">ATIVIDADES</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">INICIO/FIM</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">VALOR</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">AÇÕES</span></th>
												</tr>
											</thead>

											<tbody id="body" class="datatable-body">
												@foreach($atividades as $e)
												<tr class="datatable-row">
													<td class="datatable-cell"><span class="codigo" style="width: 200px;" id="id">{{$e->responsavel_nome}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 200px;" id="id">{{$e->crianca_nome}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 200px;" id="id">{{$e->responsavel_telefone}}</span>
													</td>
													<td class="datatable-cell">
														<span class="codigo" style="width: 100px;" id="id">
															@if($e->status)
															<span class="label label-xl label-inline label-light-info">CONCLUIDO</span>
															@else
															<span class="label label-xl label-inline label-light-success">OPERANDO</span>
															@endif
														</span>
													</td>

													<td class="datatable-cell">
														<span class="codigo" style="width: 100px;" id="id">
															@foreach($e->servicos as $key => $s)
															<span>{{$s->servico->nome}}
																@if($key < sizeof($e->servicos)-1) |
																@endif
															</span>
															@endforeach
														</span>
													</td>

													<td class="datatable-cell">
														<span class="codigo" style="width: 100px;" id="id">
															{{ \Carbon\Carbon::parse($e->inicio)->format('H:i')}}/{{ \Carbon\Carbon::parse($e->fim)->format('H:i')}}
														</span>
													</td>
													<td class="datatable-cell">
														<span class="codigo" style="width: 100px;" id="id">
															{{ number_format($e->total, 2, ',', '.')}} - {{$e->forma_pagamento}}
														</span>
													</td>

													<td class="datatable-cell">
														<span class="codigo" style="width: 200px;" id="id">
															@if($e->status == 0)
															<a class="btn btn-danger" href="/eventos/finalizarAtividade/{{$e->id}}">
																<i class="la la-close"></i>	
															</a>
															@else
															<a class="btn btn-info" href="/eventos/finalizarAtividade/{{$e->id}}">
																<i class="la la-list"></i>	
															</a>
															@endif

															<a target="_blank" href="/eventos/imprimirComprovante/{{$e->id}}" class="btn btn-primary">
																<i class="la la-print"></i>
															</a>

															@php
															$whats = str_replace(" ", "", $e->responsavel_telefone);
															$whats = str_replace("-", "", $whats);
															@endphp
															<a href="http://wa.me/55{{$whats}}" class="btn btn-success">
																<i class="lab la-whatsapp"></i>
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


						<div class="pb-5" data-wizard-type="step-content">
							<div class="row">
								@foreach($atividades as $e)

								<div class="col-sm-12 col-lg-6 col-md-6 col-xl-6">
									<div class="card card-custom gutter-b example example-compact" style="height: 380px;">
										<div class="card-header">
											<div class="card-title">
												<h3 style="width: 230px; font-size: 12px; height: 10px;" class="card-title">
													Responsável: <strong style="margin-left: 5px;" class="text-info"> {{$e->responsavel_nome}}</strong> 
												</h3>
												<h3 style="width: 230px; font-size: 12px; height: 10px;" class="card-title">
													Criança: <strong style="margin-left: 5px;" class="text-info">{{$e->crianca_nome}}</strong>
												</h3>
											</div>


										</div>

										<div class="card-body">

											<div class="kt-widget__info">
												<span class="kt-widget__label">Atividades:</span>
												<a target="_blank" class="kt-widget__data text-success">
													@foreach($e->servicos as $key => $s)
													<span>{{$s->servico->nome}}
														@if($key < sizeof($e->servicos)-1) |
														@endif
													</span>
													@endforeach
												</a>
											</div>
											<div class="kt-widget__info">
												<span class="kt-widget__label">Status:</span>
												<a target="_blank" class="kt-widget__data text-success">
													@if($e->status)
													<span class="label label-xl label-inline label-light-info">CONCLUIDO</span>
													@else
													<span class="label label-xl label-inline label-light-success">OPERANDO</span>
													@endif
												</a>
											</div>
											<div class="kt-widget__info">
												<span class="kt-widget__label">Telefone:</span>
												<a target="_blank" class="kt-widget__data text-success">
													{{ $e->responsavel_telefone }}
												</a>
											</div>
											<div class="kt-widget__info">
												<span class="kt-widget__label">Início:</span>
												<a target="_blank" class="kt-widget__data text-success">
													{{ \Carbon\Carbon::parse($e->inicio)->format('H:i')}}
												</a>
											</div>
											<div class="kt-widget__info">
												<span class="kt-widget__label">Fim:</span>
												<a target="_blank" class="kt-widget__data text-danger">
													{{ \Carbon\Carbon::parse($e->fim)->format('H:i')}}
												</a>
											</div>

											@if($e->status == 1)
											<div class="kt-widget__info">
												<span class="kt-widget__label">Valor:</span>
												<a target="_blank" class="kt-widget__data text-danger">
													{{ number_format($e->total, 2, ',', '.')}} - {{$e->forma_pagamento}}
												</a>
											</div>
											@endif


										</div>

										<div class="card-footer">
											@if($e->status == 0)
											<a style="width: 100%;" href="/eventos/finalizarAtividade/{{$e->id}}" class="btn btn-light-danger">
												<i class="la la-close"></i>
												Finalizar
											</a>
											@else
											<a style="width: 100%;" href="/eventos/finalizarAtividade/{{$e->id}}" class="btn btn-light-info">
												<i class="la la-list"></i>
												Detalhes
											</a>
											@endif

											<a style="width: 100%;" target="_blank" href="/eventos/imprimirComprovante/{{$e->id}}" class="btn btn-light-primary">
												<i class="la la-print"></i>
												Imprimir
											</a>

											@php
											$whats = str_replace(" ", "", $e->responsavel_telefone);
											$whats = str_replace("-", "", $whats);
											@endphp
											<a target="_blank" style="width: 100%;"href="http://wa.me/55{{$whats}}" class="btn btn-light-success">
												<i class="lab la-whatsapp"></i>
												WhatsApp
											</a>
										</div>
									</div>
								</div>

								@endforeach

							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="d-flex justify-content-between align-items-center flex-wrap">
				<div class="d-flex flex-wrap py-2 mr-3">
					@if(isset($links))
					{{$eventos->links()}}
					@endif
				</div>
			</div>
		</div>

	</div>
</div>

@endsection