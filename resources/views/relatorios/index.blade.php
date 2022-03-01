@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<div class="row">

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-6 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
					<div class="accordion" id="accordionExample1">

						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne1a">
									<h3 class="card-title">Relatório de Vendas<i class="la la-angle-double-down"></i>
									</h3>
								</div>
							</div>

							<div id="collapseOne1a" class="collapse" data-parent="#accordionExample1">
								<div class="card-content">
									<div class="col-xl-12">
										<form method="get" action="/relatorios/filtroVendas2">
											<div class="row">

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Inicial</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_inicial" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Final</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_final" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group validated col-lg-12 col-xl-12 mt-12 mt-lg-0">
													<button style="width: 100%" class="btn btn-light-primary px-6 font-weight-bold">Gerar Relatório</button>
												</div>

											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-6 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
					<div class="accordion" id="accordionExample1">

						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne1">
									<h3 class="card-title">Relatório de Somatório de Vendas<i class="la la-angle-double-down"></i>
									</h3>
								</div>
							</div>

							<div id="collapseOne1" class="collapse" data-parent="#accordionExample1">
								<div class="card-content">
									<div class="col-xl-12">
										<form method="get" action="/relatorios/filtroVendas">
											<div class="row">

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Inicial</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_inicial" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Final</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_final" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Nro. Resultados</label>
													<div class="">
														<input id="razao_social" type="text" class="form-control" name="total_resultados" value="">
													</div>
												</div>

												<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Ordem</label>

													<select class="custom-select form-control" id="" name="ordem">
														<option value="desc">Maior Valor</option>
														<option value="asc">Menor Valor</option>
														<option value="data">Data</option>
													</select>

												</div>

												<div class="form-group validated col-lg-12 col-xl-12 mt-12 mt-lg-0">
													<button style="width: 100%" class="btn btn-light-primary px-6 font-weight-bold">Gerar Relatório</button>
												</div>



											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-6 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">
					<div class="accordion" id="accordionExample1">
						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne2">
									<h3 class="card-title">Relatório de Somatório de Compras <i class="la la-angle-double-down"></i>
									</h3>
								</div>
							</div>

							<div id="collapseOne2" class="collapse" data-parent="#accordionExample1">
								<div class="card-content">
									<div class="col-xl-12">
										<form method="get" action="/relatorios/filtroCompras">
											<div class="row">

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Inicial</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_inicial" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Final</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_final" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Nro. Resultados</label>
													<div class="">
														<input id="razao_social" type="text" class="form-control" name="total_resultados" value="">
													</div>
												</div>

												<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Ordem</label>

													<select class="custom-select form-control" id="" name="ordem">
														<option value="desc">Maior Valor</option>
														<option value="asc">Menor Valor</option>
														<option value="data">Data</option>
													</select>

												</div>

												<div class="form-group validated col-lg-12 col-xl-12 mt-12 mt-lg-0">
													<button style="width: 100%" class="btn btn-light-success px-6 font-weight-bold">Gerar Relatório</button>
												</div>

											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-6 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">
					<div class="accordion" id="accordionExample1">

						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne3">
									<h3 class="card-title">Relatório de Vendas para Clientes <i class="la la-angle-double-down"></i>
									</h3>
								</div>
							</div>

							<div id="collapseOne3" class="collapse" data-parent="#accordionExample1">
								<div class="card-content">
									<div class="col-xl-12">
										<form method="get" action="/relatorios/filtroVendaClientes">
											<div class="row">

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Inicial</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_inicial" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Final</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_final" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Nro. Resultados</label>
													<div class="">
														<input id="razao_social" type="text" class="form-control" name="total_resultados" value="">
													</div>
												</div>

												<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Ordem</label>

													<select class="custom-select form-control" id="" name="ordem">
														<option value="desc">Mais Vendas</option>
														<option value="asc">Menos Vendas</option>
													</select>

												</div>

												<div class="form-group validated col-lg-12 col-xl-12 mt-12 mt-lg-0">
													<button style="width: 100%" class="btn btn-light-info px-6 font-weight-bold">Gerar Relatório</button>
												</div>



											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-6 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
					<div class="accordion" id="accordionExample1">

						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne4">
									<h3 class="card-title">Relatório de Lucro <i class="la la-angle-double-down"></i>
									</h3>
								</div>
							</div>

							<div id="collapseOne4" class="collapse" data-parent="#accordionExample1">
								<div class="card-content">
									<div class="col-xl-12">
										<form method="get" action="/relatorios/filtroLucro">
											<div class="row">

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label dt">Data Inicial</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_inicial" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6" id="lucro_col">
													<label class="col-form-label">Data Final</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_final" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Ordem</label>

													<select class="custom-select form-control" id="tipo_lucro" name="tipo">
														<option value="grupo">Agrupado</option>
														<option value="detalhado">Detalhado</option>
													</select>

												</div>

												<div class="form-group validated col-lg-12 col-xl-12 mt-12 mt-lg-0">
													<button style="width: 100%" class="btn btn-light-danger px-6 font-weight-bold">Gerar Relatório</button>
												</div>



											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-6 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">
					<div class="accordion" id="accordionExample1">

						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne5">
									<h3 class="card-title">Relatório de estoque de produtos <i class="la la-angle-double-down"></i>
									</h3>
								</div>
							</div>

							<div id="collapseOne5" class="collapse" data-parent="#accordionExample1">
								<div class="card-content">
									<div class="col-xl-12">
										<form method="get" action="/relatorios/estoqueProduto">
											<div class="row">

												<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Ordem</label>

													<select class="custom-select form-control" id="usuario" name="ordem">
														<option value="nome">Nome</option>
														<option value="qtd">Quantidade</option>
														<!-- <option value="ultima_movimentacao">Ultima movimentação</option> -->
													</select>

												</div>
												<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Categoria</label>

													<select class="custom-select form-control" id="categoria" name="categoria">
														<option value="todos">todos</option>
														@foreach($categorias as $c)
														<option value="{{$c->id}}">{{$c->nome}}</option>
														@endforeach
													</select>

												</div>
												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Nro. Resultados</label>
													<div class="">
														<input id="razao_social" type="text" class="form-control" name="total_resultados" value="">
													</div>
												</div>

												<div style="height: 100px;">
													<br>
												</div>
												<div class="form-group validated col-lg-12 col-xl-12 mt-12 mt-lg-0">
													<button style="width: 100%" class="btn btn-light-info px-6 font-weight-bold">Gerar Relatório</button>
												</div>

											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-6 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
					<div class="accordion" id="accordionExample1">

						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne6">

									<h3 class="card-title">Relatório de Comissão de Vendas <i class="la la-angle-double-down"></i>
									</h3>
								</div>
							</div>

							<div id="collapseOne6" class="collapse" data-parent="#accordionExample1">

								<div class="card-content">
									<div class="col-xl-12">
										<form method="get" action="/relatorios/comissaoVendas">
											<div class="row">

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Inicial</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_inicial" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Final</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_final" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Produto</label>
													<div class="">
														<select class="form-control select2" style="width: 100%" id="kt_select2_1" name="produto">
															<option value="null">Selecione o produto</option>
															@foreach($produtos as $p)
															<option value="{{$p->id}}">{{$p->id}} - {{$p->nome}}</option>
															@endforeach
														</select>
													</div>
												</div>

												<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Vendedor</label>

													<select class="form-control select2" style="width: 100%" id="kt_select2_2" name="funcionario">
														<option value="null">Selecione o vendedor</option>
														@foreach($funcionarios as $p)
														<option value="{{$p->id}}">{{$p->id}} - {{$p->nome}}</option>
														@endforeach
													</select>

												</div>

												<div class="form-group validated col-lg-12 col-xl-12 mt-12 mt-lg-0">
													<button style="width: 100%" class="btn btn-light-success px-6 font-weight-bold">Gerar Relatório</button>
												</div>

											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-6 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
					<div class="accordion" id="accordionExample1">

						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne7">
									<h3 class="card-title">Relatório de Estoque Mínimo <i class="la la-angle-double-down"></i>
									</h3>
								</div>
							</div>

							<div id="collapseOne7" class="collapse" data-parent="#accordionExample1">

								<div class="card-content">
									<div class="col-xl-12">
										<form method="get" action="/relatorios/filtroEstoqueMinimo">
											<div class="row">

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Nro. Resultados</label>
													<div class="">
														<input id="razao_social" type="text" class="form-control" name="total_resultados" value="">
													</div>
												</div>

												<div class="form-group validated col-lg-12 col-xl-12 mt-12 mt-lg-0">
													<button style="width: 100%" class="btn btn-light-warning px-6 font-weight-bold">Gerar Relatório</button>
												</div>

											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-6 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">
					<div class="accordion" id="accordionExample1">

						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne8">
									<h3 class="card-title">Relatório de Vendas Diária(s) Detalhado <i class="la la-angle-double-down"></i>
									</h3>
								</div>
							</div>

							<div id="collapseOne8" class="collapse" data-parent="#accordionExample1">

								<div class="card-content">
									<div class="col-xl-12">
										<form method="get" action="/relatorios/filtroVendaDiaria">
											<div class="row">

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_inicial" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Nro. Resultados</label>
													<div class="">
														<input id="razao_social" type="text" class="form-control" name="total_resultados" value="">
													</div>
												</div>


												<div class="form-group validated col-lg-12 col-xl-12 mt-12 mt-lg-0">
													<button style="width: 100%" class="btn btn-light-dark px-6 font-weight-bold">Gerar Relatório</button>
												</div>


											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-6 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
					<div class="accordion" id="accordionExample1">

						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne9">

									<h3 class="card-title">Relatório Custo/Venda <i class="la la-angle-double-down"></i>
									</h3>
								</div>
							</div>

							<input type="hidden" id="subs" value="{{json_encode($subs)}}">

							<div id="collapseOne9" class="collapse" data-parent="#accordionExample1">

								<div class="card-content">
									<div class="col-xl-12">
										<form method="get" action="/relatorios/filtroVendaProdutos">
											<div class="row">

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Inicial</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_inicial" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Final</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_final" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Nro. Resultados</label>
													<div class="">
														<input id="razao_social" type="text" class="form-control" name="total_resultados" value="">
													</div>
												</div>

												<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Ordem</label>

													<select class="custom-select form-control" id="" name="ordem">
														<option value="desc">Mais Vendidos</option>
														<option value="asc">Menos Vendidos</option>
														<option value="alfa">Alfabética</option>
													</select>

												</div>

												<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Marca</label>

													<select class="custom-select form-control" id="" name="marca_id">
														<option value="">--</option>
														@foreach($marcas as $m)
														<option value="{{$m->id}}">
															{{$m->nome}}
														</option>
														@endforeach
													</select>
												</div>

												<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Categoria</label>

													<select class="custom-select form-control" id="categoria" name="categoria_id">
														<option value="">--</option>
														@foreach($categorias as $c)
														<option value="{{$c->id}}">
															{{$c->nome}}
														</option>
														@endforeach
													</select>
												</div>

												<div class="form-group validated col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Sub Categoria</label>

													<select class="custom-select form-control" id="sub_categoria_id" name="sub_categoria_id">
														<option value="">--</option>
														
													</select>
												</div>

												<div class="form-group validated col-lg-12 col-xl-12 mt-12 mt-lg-0">
													<button style="width: 100%" class="btn btn-light-danger px-6 font-weight-bold">Gerar Relatório</button>
												</div>

											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-6 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
					<div class="accordion" id="accordionExample1">

						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne10">

									<h3 class="card-title">Relatório Tipos de Pagamento <i class="la la-angle-double-down"></i>
									</h3>
								</div>
							</div>

							<div id="collapseOne10" class="collapse" data-parent="#accordionExample1">

								<div class="card-content">
									<div class="col-xl-12">
										<form method="get" action="/relatorios/tiposPagamento">
											<div class="row">

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Inicial</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_inicial" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Final</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_final" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>


												<div class="form-group validated col-lg-12 col-xl-12 mt-12 mt-lg-0">
													<button style="width: 100%" class="btn btn-light-danger px-6 font-weight-bold">Gerar Relatório</button>
												</div>

											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-6 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
					<div class="accordion" id="accordionExample1">

						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header">
								<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne12">

									<h3 class="card-title">Relatório Cadastro de produtos <i class="la la-angle-double-down"></i>
									</h3>
								</div>
							</div>

							<div id="collapseOne12" class="collapse" data-parent="#accordionExample1">

								<div class="card-content">
									<div class="col-xl-12">
										<form method="get" action="/relatorios/cadastroProdutos">
											<div class="row">

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Inicial</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_inicial" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group col-lg-6 col-md-6 col-sm-6">
													<label class="col-form-label">Data Final</label>
													<div class="">
														<div class="input-group date">
															<input type="text" name="data_final" class="form-control" readonly value="" id="kt_datepicker_3" />
															<div class="input-group-append">
																<span class="input-group-text">
																	<i class="la la-calendar"></i>
																</span>
															</div>
														</div>
													</div>
												</div>


												<div class="form-group validated col-lg-12 col-xl-12 mt-12 mt-lg-0">
													<button style="width: 100%" class="btn btn-light-info px-6 font-weight-bold">Gerar Relatório</button>
												</div>

											</div>
										</form>
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
	var SUBCATEGORIAS = [];
	$(function () {

		SUBCATEGORIAS = JSON.parse($('#subs').val())
		console.log(SUBCATEGORIAS)
	})

	$('#categoria').change(() => {
		montaSubs()
	})

	function montaSubs(){
		let categoria_id = $('#categoria').val()
		let subs = SUBCATEGORIAS.filter((x) => {
			return x.categoria_id == categoria_id
		})

		let options = ''
		subs.map((s) => {
			options += '<option value="'+s.id+'">'
			options += s.nome
			options += '</option>'
		})
		$('#sub_categoria_id').html('<option value="">--</option>')
		$('#sub_categoria_id').append(options)
	}
</script>
@endsection	

@endsection	