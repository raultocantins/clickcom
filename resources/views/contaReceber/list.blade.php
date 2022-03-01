
@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">


	<div class="card-body">
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-12">
				<div class="row">

					<a href="/contasReceber/new" class="btn btn-lg btn-success">
						<i class="fa fa-plus"></i>Novo Conta a Receber
					</a>
					@isset($paraImprimir)
					<form method="post" action="/contasReceber/relatorio">
						@csrf
						<input type="hidden" name="cliente" value="{{{ isset($cliente) ? $cliente : '' }}}">
						<input type="hidden" name="tipo_filtro_data" value="{{{ isset($tipo_filtro_data) ? $tipo_filtro_data : '' }}}">
						<input type="hidden" name="data_inicial" value="{{{ isset($dataInicial) ? $dataInicial : '' }}}">
						<input type="hidden" name="data_final" value="{{{ isset($dataFinal) ? $dataFinal : '' }}}">
						<input type="hidden" name="status" value="{{{ isset($status) ? $status : '' }}}">
						<input type="hidden" name="categoria" value="{{{ isset($categoria) ? $categoria : '' }}}">
						<button style="margin-left: 5px;" href="/contasPagar/new" class="btn btn-lg btn-info">
							<i class="fa fa-print"></i>Imprimir relatório
						</button>
					</form>
					@endisset

				</div>
			</div>
		</div>
		<br>

		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<form method="get" action="/contasReceber/filtro">
				<div class="row align-items-center">

					<div class="form-group col-lg-4 col-xl-4">
						<div class="row align-items-center">

							<div class="col-md-12 my-2 my-md-0">
								<label class="col-form-label">Cliente</label>

								<div class="input-icon">
									<input type="text" name="cliente" value="{{{ isset($cliente) ? $cliente : '' }}}" class="form-control" placeholder="Cliente" id="kt_datatable_search_query">
									<span>
										<i class="fa fa-search"></i>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group col-lg-2 col-md-4 col-sm-6">
						<label class="col-form-label">Filtro de data</label>

						<select class="custom-select form-control" id="tipo_filtro_data" name="tipo_filtro_data">
							<option @if(isset($tipo_filtro_data) && $tipo_filtro_data == 1) selected @endif value="1">Vencimento</option>
							<option @if(isset($tipo_filtro_data) && $tipo_filtro_data == 2) selected @endif value="2">Data de registro</option>
						</select>
					</div>

					<div class="form-group col-lg-2 col-md-4 col-sm-6">
						<label class="col-form-label">Data inicial</label>
						<div class="">
							<div class="input-group date">
								<input type="text" name="data_inicial" class="form-control" readonly value="{{{ isset($dataInicial) ? $dataInicial : '' }}}" id="kt_datepicker_3" />
								<div class="input-group-append">
									<span class="input-group-text">
										<i class="la la-calendar"></i>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group col-lg-2 col-md-4 col-sm-6">
						<label class="col-form-label">Data final</label>
						<div class="">
							<div class="input-group date">
								<input type="text" name="data_final" class="form-control" readonly value="{{{ isset($dataFinal) ? $dataFinal : '' }}}" id="kt_datepicker_3" />
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

						<select class="custom-select form-control" id="status" name="status">
							<option @if(isset($status) && $status == 'todos') selected @endif value="todos">TODOS</option>
							<option @if(isset($status) && $status == 'pago') selected @endif value="pago">PAGO</option>
							<option @if(isset($status) && $status == 'pendente') selected @endif value="pendente">PENDENTE</option>
						</select>
					</div>

					<div class="form-group col-lg-2 col-md-4 col-sm-6">
						<label class="col-form-label">Categoria</label>

						<select class="custom-select form-control" id="categoria" name="categoria">
							<option @if(isset($categoria) && $categoria == 'todos') selected @endif value="todos">TODOS</option>
							@foreach($categorias as $c)
							<option @if(isset($categoria) && $categoria == $c->id) selected @endif value="{{$c->id}}">{{$c->nome}}</option>
							@endforeach
						</select>

					</div>

					<div class="col-lg-2 col-xl-2 mt-2 mt-lg-0">
						<button style="margin-top: 10px;" class="btn btn-light-primary px-6 font-weight-bold">Pesquisa</button>
					</div>
				</div>

			</form>
			<br>
			<h4>Lista de Contas a Receber</h4>
			<h6 style="color: red">*{{$infoDados}}</h6>
			<label>Total de registros: {{sizeof($contas)}}</label>

			<div class="row">
				<div class="col-12">
					<button id="btn_seleciona_varios" class="btn btn-light">
						<i class="la la-list"></i>
						Selecionar varios
					</button>

					<button style="display: none" id="btn_gerar" class="btn btn-success">
						<i class="la la-list"></i>
						Gerar Boletos
					</button>

					<a href="/remessasBoleto" class="btn btn-danger">
						<i class="la la-list"></i>
						Remessas de boleto
					</a>

					<br><br>
				</div>
			</div>

			<div class="row">

				<?php 
				$somaValor = 0;
				$somaRecebido = 0;
				$somaPendente = 0;
				?>

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
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">CLIENTE</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">CATEGORIA</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">VALOR INTEGRAL</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">VALOR RECEBIDO</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">DATA REGISTRO</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">DATA VENCIMENTO</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">ESTADO</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 300px;">AÇÕES</span></th>
												</tr>
											</thead>
											<tbody id="body" class="datatable-body">
												@foreach($contas as $c)
												<tr class="datatable-row">
													<td class="datatable-cell">
														<span class="codigo" style="width: 250px;" id="id">
															@if($c->venda_id != null || $c->venda_caixa_id != null)
															@if($c->venda_id != null)
															{{ $c->venda->cliente->razao_social }}
															@else
															{{ $c->vendaCaixa->cliente->razao_social }}
															@endif
															@else
															@if($c->cliente_id != null)
															{{ $c->cliente->razao_social }}
															@else
															--
															@endif
															@endif
														</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$c->categoria->nome}}</span>
													</td>

													<td class="datatable-cell">
														<span class="codigo" style="width: 120px;" id="id">
															R$ {{number_format($c->valor_integral, $casasDecimais, ',', '.')}}
														</span>
													</td>

													<td class="datatable-cell">
														<span class="codigo" style="width: 120px;" id="id">
															R$ {{number_format($c->valor_recebido, $casasDecimais, ',', '.')}}
														</span>
													</td>

													<td class="datatable-cell">
														<span class="codigo" style="width: 120px;" id="id">
															{{ \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i:s')}}
														</span>
													</td>

													<td class="datatable-cell">
														<span class="codigo" style="width: 120px;" id="id">
															{{ \Carbon\Carbon::parse($c->data_vencimento)->format('d/m/Y')}}
														</span>
													</td>

													<td class="datatable-cell">
														<span class="codigo" style="width: 120px;" id="id">
															@if($c->status == true)
															<span class="label label-xl label-inline label-light-success">Recebido</span>
															@else
															<span class="label label-xl label-inline label-light-danger">Pendente</span>
															@endif
														</span>
													</td>

													<td class="datatable-cell">
														<span class="codigo" style="width: 300px;" id="id">

															@if(!$c->boleto)


															<a href="/contasReceber/edit/{{$c->id}}" class="btn btn-warning">
																<i class="la la-edit"></i>
															</a>
															<a onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/contasReceber/delete/{{ $c->id }}" }else{return false} })' href="#!" class="btn btn-danger">
																<i class="la la-trash"></i>
															</a>

															@endif

															@if($c->status == false)

															<a href="/contasReceber/receber/{{$c->id}}" class="btn btn-success">
																<i class="la la-money"></i>
															</a>

															@endif

															@if(!$c->boleto)

															<a href="/boleto/gerar/{{$c->id}}" class="btn btn-info">
																<i class="la la-file"></i>
															</a>

															@else

															<a target="_blank" href="/boleto/imprimir/{{$c->boleto->id}}" class="btn btn-primary">
																<i class="la la-print"></i>
															</a>


															<a target="_blank" href="/boleto/gerarRemessa/{{$c->boleto->id}}" class="btn btn-info">
																<i class="la-file-alt"></i>
															</a>

															@endif

															@if($c->venda_id != null || $c->venda_caixa_id != null)
															<a href="/contasReceber/detalhes_venda/{{$c->id}}" class="btn btn-warning" title="Detalhes da venda">
																<i class="la la-list"></i>
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

						<input type="hidden" id="contas" value="{{json_encode($contas)}}" name="">
						<div class="pb-5" data-wizard-type="step-content">
							<div class="row">
								@foreach($contas as $c)

								<div class="col-sm-12 col-lg-6 col-md-6 col-xl-4">
									<div class="card card-custom gutter-b example example-compact">
										<div class="card-header">
											<div class="card-title">

												@if(!$c->boleto)
												<label style="display: none" class="checkbox checkbox-success" for="sel_{{$c->id}}">
													<input id="sel_{{$c->id}}" class="select" type="checkbox" name="Checkboxes5"/>
													<span></span>
												</label>
												@endif
												<h3 style="width: 230px; font-size: 20px; height: 10px;" class="card-title">
													R$ <strong id="valor_{{$c->id}}">{{number_format($c->valor_integral, $casasDecimais, ',', '.')}}</strong>
												</h3>

											</div>

											<div class="card-toolbar">
												<div class="dropdown dropdown-inline" data-toggle="tooltip" title="" data-placement="left" data-original-title="Ações">
													<a href="#" class="btn btn-hover-light-primary btn-sm btn-icon btn-action" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
														<i class="fa fa-ellipsis-h"></i>
													</a>
													<div class="dropdown-menu p-0 m-0 dropdown-menu-md dropdown-menu-left">
														<!--begin::Navigation-->
														<ul class="navi navi-hover">
															<li class="navi-header font-weight-bold py-4">
																<span class="font-size-lg">Ações:</span>
															</li>


															<li class="navi-separator mb-3 opacity-70"></li>

															@if(!$c->boleto)

															<li class="navi-item">
																<a href="/contasReceber/edit/{{$c->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-primary">Editar</span>
																	</span>
																</a>
															</li>
															<li class="navi-item">
																<a onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/contasReceber/delete/{{ $c->id }}" }else{return false} })' href="#!" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-danger">Excluir</span>
																	</span>
																</a>
															</li>
															@endif

															@if($c->status == false)

															<li class="navi-item">
																<a href="/contasReceber/receber/{{$c->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-success">Receber</span>
																	</span>
																</a>
															</li>

															@endif

															@if(!$c->boleto)
															<li class="navi-item">
																<a href="/boleto/gerar/{{$c->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-info">Gerar Boleto</span>
																	</span>
																</a>
															</li>

															@else

															<li class="navi-item">
																<a target="_blank" href="/boleto/imprimir/{{$c->boleto->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-info">Imprimir</span>
																	</span>
																</a>
															</li>

															<li class="navi-item">
																<a target="_blank" href="/boleto/gerarRemessa/{{$c->boleto->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-warning">Gerar remessa</span>
																	</span>
																</a>
															</li>

															@endif

															@if($c->venda_id != null || $c->venda_caixa_id != null)
															<li class="navi-item">
																<a href="/contasReceber/detalhes_venda/{{$c->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-warning">Detalhes da Venda</span>
																	</span>
																</a>
															</li>
															@endif


														</ul>
														<!--end::Navigation-->
													</div>
												</div>

											</div>

											<div class="card-body">

												<div class="kt-widget__info">
													<span class="kt-widget__label">Cliente:</span>
													<a target="_blank" class="kt-widget__data text-success">
														@if($c->venda_id != null || $c->venda_caixa_id != null)
														@if($c->venda_id != null)
														<th>{{ $c->venda->cliente->razao_social }}</th>
														@else
														<th>{{ $c->vendaCaixa->cliente->razao_social }}</th>
														@endif
														@else
														@if($c->cliente_id != null)
														<th>{{ $c->cliente->razao_social }}</th>
														@else
														<th> -- </th>
														@endif
														@endif
													</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Categoria:</span>
													<a class="kt-widget__data text-success">
														{{$c->categoria->nome}} 
													</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Valor recebido:</span>
													<a class="kt-widget__data text-success">
														{{ number_format($c->valor_recebido, $casasDecimais, ',', '.') }}
													</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Data de registro:</span>
													<a class="kt-widget__data text-success">
														{{ \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i:s')}}
													</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Data de vencimento:</span>
													<a class="kt-widget__data text-success">
														{{ \Carbon\Carbon::parse($c->data_vencimento)->format('d/m/Y')}}
													</a>
												</div>

												<div class="kt-widget__info">
													<span class="kt-widget__label">Estado:</span>
													@if($c->status == true)
													<span class="label label-xl label-inline label-light-success">Recebido</span>
													@else
													<span class="label label-xl label-inline label-light-danger">Pendente</span>
													@endif
												</div>

												@if($c->boleto)
												<div class="kt-widget__info" style="margin-top: 3px;">
													<span class="kt-widget__label">Boleto:</span>

													<span class="label label-xl label-inline label-light-success">{{$c->boleto->banco->banco}}</span>

												</div>
												@else
												<div class="kt-widget__info" style="margin-top: 3px;">
													<span class="kt-widget__label">Boleto:</span>
													<span class="label label-xl label-inline label-light-info">--</span>

												</div>
												@endif


											</div>

										</div>

									</div>

								</div>

								<?php
								$somaValor += $c->valor_integral;
								$somaRecebido += $c->valor_recebido;

								if($c->status == false)
									$somaPendente += $c->valor_integral;
								?>

								@endforeach

							</div>
						</div>
					</div>
				</div>


				<div class="d-flex justify-content-between align-items-center flex-wrap">
					<div class="d-flex flex-wrap py-2 mr-3">
						@if(isset($links))
						{{$contas->links()}}
						@endif
					</div>
				</div>

				<div class="card-body">
					<div class="row">
						<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
							<div class="card card-custom gutter-b example example-compact">
								<div class="card-header">

									<div class="card-body">
										<div class="row">
											<div class="col-6">

												<h3 class="card-title">Valor a Receber: <strong style="margin-left: 5px;"> R$ {{number_format($somaPendente, 2, ',', '.') }}</strong></h3>
											</div>
											<div style="display: none" class="col-6 div-valor-selecionado">

												<h3 class="card-title">Valor Selecionado: <strong style="margin-left: 5px;" id="valor-selecionado">R$ 0,00</strong></h3>
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	@section('javascript')
	<script type="text/javascript">
		var CONTAS = [];
		var ADICIONADAS = [];
		var SOMA = 0;
		var BTNSELECIONA = false;
		$(function () {
			CONTAS = JSON.parse($('#contas').val());
		});
		$('.select').click(() => {
			ADICIONADAS = []
			CONTAS.map((c) => {
				let s = $('#sel_'+c.id).is(':checked');
				if(s){
					ADICIONADAS.push(c)
				}
			})
			somaArray();
			verificaBotaoGerar();
		})

		function somaArray(){
			SOMA = 0;
			ADICIONADAS.map((a) => {
				SOMA += parseFloat(a.valor_integral.replace(',', '.'))
			})
			console.log(SOMA)
			$('#valor-selecionado').html(formatReal(SOMA))
		}

		$('#btn_seleciona_varios').click(() => {
			BTNSELECIONA = !BTNSELECIONA

			$('#grade').trigger('click')

			if(BTNSELECIONA){
				$('#btn_seleciona_varios').removeClass('btn-light')
				$('#btn_seleciona_varios').addClass('btn-info')
				$('.checkbox').css('display', 'block')
				$('.checkbox').css('margin-right', '5px')
				$('.btn-action').css('display', 'none')
				$('.div-valor-selecionado').css('display', 'block')
			}else{
				$('#btn_seleciona_varios').removeClass('btn-info')
				$('#btn_seleciona_varios').addClass('btn-light')

				$('.checkbox').css('display', 'none')
				$('.div-valor-selecionado').css('display', 'none')
				$('.btn-action').css('display', 'block')
			}

			ADICIONADAS.map((a) => {
				$('#sel_'+a.id).prop('checked', false)
			})
		})	

		function verificaBotaoGerar(){
			if(ADICIONADAS.length > 1){
				$('#btn_gerar').css('display', 'inline-block')
			}else{
				$('#btn_gerar').css('display', 'none')
			}
		}

		$('#btn_gerar').click(() => {
			let temp = [];
			ADICIONADAS.map((a) => {
				temp.push(a.id)
			})
			console.log(temp)
			location.href = path + 'boleto/gerarMultiplos/'+temp
		})

		function formatReal(v){
			return v.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
		}

	</script>
	@endsection

	@endsection
