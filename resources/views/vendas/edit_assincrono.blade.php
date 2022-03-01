@extends('default.layout')
@section('content')
<style type="text/css">
	#focus-codigo:hover{
		cursor: pointer
	}
	.search-prod{
		position: absolute;
		top: 0;
		margin-top: 40px;
		left: 10;
		width: 100%;
		max-height: 200px;
		overflow: auto;
		z-index: 9999;
		border: 1px solid #eeeeee;
		border-radius: 4px;
		background-color: #fff;
		box-shadow: 0px 1px 6px 1px rgba(0, 0, 0, 0.4);
	}

	.search-prod label:hover{
		cursor: pointer;
	}

	.search-prod label{
		margin-left: 10px;
		width: 100%;
		margin-top: 7px;
		font-size: 14px;
	}
</style>
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

				<h3 class="card-title">DADOS INICIAIS</h3>

				<input type="hidden" value="{{json_encode($venda)}}" id="venda_edit" name="">

				<input type="hidden" id="_token" value="{{csrf_token()}}" name="">
				<div class="row">
					<div class="col-xl-12">

						<div class="kt-section kt-section--first">
							<div class="kt-section__body">

								<div class="row">
									<div class="col-lg-4 col-md-4 col-sm-6">

										<h6>Ultima NF-e: <strong>{{$lastNF}}</strong></h6>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-6">

										@if($config->ambiente == 2)
										<h6>Ambiente: <strong class="text-primary">Homologação</strong></h6>
										@else
										<h6>Ambiente: <strong class="text-success">Produção</strong></h6>
										@endif
									</div>
								</div>

								<div class="row">
									<div class="form-group col-lg-4 col-md-4 col-sm-6">
										<label class="col-form-label">Natureza de Operação</label>
										<div class="">
											<div class="input-group date">
												<select class="custom-select form-control" id="natureza" name="natureza">
													@foreach($naturezas as $n)
													<option 
													@if($venda->natureza_id == $n->id)
													selected
													@endif
													value="{{$n->id}}">{{$n->natureza}}</option>
													@endforeach
												</select>
											</div>
										</div>
									</div>
									@if(isset($listaPreco))
									<div class="form-group col-lg-4 col-md-4 col-sm-6">
										<label class="col-form-label">Lista de Preço</label>
										<div class="">
											<div class="input-group date">
												<select class="custom-select form-control" id="lista_id" name="lista_id">
													<option value="0">Padrão</option>
													@foreach($listaPreco as $l)
													<option value="{{$l->id}}">{{$l->nome}} - {{$l->percentual_alteracao}}%</option>
													@endforeach
												</select>
											</div>
										</div>
									</div>
									@endif
								</div>


								<div class="row">
									<div class="col-xl-12">

										<div class="card card-custom gutter-b">
											<div class="card-body">

												<h4 class="center-align">CLIENTE</h4>
												<div class="row">

													<div class="col-sm-6 col-lg-6 col-12">
														<h5>Razão Social: <strong id="razao_social" class="text-success">
															{{$venda->cliente->razao_social}}
														</strong></h5>
														<h5>Nome Fantasia: <strong id="nome_fantasia" class="text-success">
															{{$venda->cliente->nome_fantasia}}
														</strong></h5>
														<h5>Logradouro: <strong id="logradouro" class="text-success">{{$venda->cliente->rua}}</strong></h5>
														<h5>Numero: <strong id="numero" class="text-success">{{$venda->cliente->numero}}</strong></h5>
													</div>
													<div class="col-sm-6 col-lg-6 col-12">
														<h5>CPF/CNPJ: <strong id="cnpj" class="text-success">
															{{$venda->cliente->cpf_cnpj}}
														</strong></h5>
														<h5>RG/IE: <strong id="ie" class="text-success">
															{{$venda->cliente->ie_rg}}
														</strong></h5>
														<h5>Fone: <strong id="fone" class="text-success">
															{{$venda->cliente->fone}}
														</strong></h5>
														<h5>Cidade: <strong id="cidade" class="text-success">
															{{$venda->cliente->cidade->nome}} ({{$venda->cliente->cidade->uf}})
														</strong></h5>

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


				<!-- Wizzard -->
				<div class="card card-custom gutter-b">


					<div class="card-body">

						<div class="row">
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
															ITENS
														</span>
													</h3>
													<div class="wizard-bar"></div>
												</div>
											</div>
											<!--end::Wizard Step 1 Nav-->
											<!--begin::Wizard Step 2 Nav-->
											<div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
												<div class="wizard-label">
													<h3 class="wizard-title">
														<span>
															TRANSPORTE
														</span>
													</h3>
													<div class="wizard-bar"></div>
												</div>
											</div>

											<div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
												<div class="wizard-label">
													<h3 class="wizard-title">
														<span>
															PAGAMENTO
														</span>
													</h3>
													<div class="wizard-bar"></div>
												</div>
											</div>


										</div>
									</div>

									<input class="mousetrap" type="" autofocus style="border: none; width: 0px; height: 0px;" id="codBarras">

									<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">

										<!--begin: Wizard Form-->
										<form class="form fv-plugins-bootstrap fv-plugins-framework" id="kt_form">
											<!--begin: Wizard Step 1-->
											<div class="pb-5" data-wizard-type="step-content">

												<!-- Inicio da tabela -->

												<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
													<div class="row">
														<div class="col-xl-12">
															<div class="row align-items-center">
																<div class="form-group validated col-sm-6 col-lg-5 col-12">
																	<label class="col-form-label" id="">Produto</label>
																	<div class="input-group">
																		<div class="input-group-prepend">
																			<span class="input-group-text" id="focus-codigo">
																				<li class="la la-barcode"></li>
																			</span>
																		</div>


																		<input placeholder="Digite para buscar o produto" type="search" id="produto-search" class="form-control">
																		<div class="search-prod" style="display: none">
																		</div>
																		
																		<button type="button" onclick="novoProduto()" class="btn btn-info btn-sm">
																			<i class="la la-plus-circle icon-add"></i>
																		</button>
																	</div>
																</div>

																<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
																	<label class="col-form-label">Quantidade</label>
																	<div class="">
																		<div class="input-group">
																			<input type="text" name="quantidade" class="form-control qtd-p" value="0" id="quantidade"/>
																		</div>
																	</div>
																</div>
																<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
																	<label class="col-form-label">Valor Unitário</label>
																	<div class="">
																		<div class="input-group">
																			<input type="text" name="valor" class="form-control money-p" value="0" id="valor"/>
																		</div>
																	</div>
																</div>

																<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
																	<label class="col-form-label">Subtotal</label>
																	<div class="">
																		<div class="input-group">
																			<input type="text" name="subtotal" class="form-control" value="0" id="subtotal"/>
																		</div>
																	</div>
																</div>
																<div class="col-lg-1 col-md-4 col-sm-6 col-6">
																	<a href="#!" style="margin-top: 10px;" id="addProd" class="btn btn-light-success px-6 font-weight-bold">
																		<i class="la la-plus"></i>
																	</a>
																	
																</div>

															</div>
														</div>
													</div>


													<!-- Inicio tabela -->

													<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

														<table class="datatable-table" style="max-width: 100%; overflow: scroll;" id="prod">
															<thead class="datatable-head">
																<tr class="datatable-row" style="left: 0px;">
																	<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 70px;">Item</span></th>
																	<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 70px;">Cód Prod</span></th>
																	<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 300px;">Nome</span></th>
																	<th data-field="Country" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Quantidade</span></th>
																	<th data-field="ShipDate" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Valor</span></th>

																	<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Subtotal</span></th>

																	<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Ações</span></th>
																</tr>
															</thead>

															<tbody id="body" class="datatable-body">
																<tr class="datatable-row">

																</tr>
															</tbody>
														</table>
														<!-- Fim da tabela -->
													</div>
												</div>
											</div>

											<!--end: Wizard Step 1-->
											<!--begin: Wizard Step 2-->
											<div class="pb-5" data-wizard-type="step-content">

												<!-- Inicio do card -->

												<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
													<div class="row">
														<div class="col-xl-12">
															<h3>Transportadora</h3>

															<div class="row align-items-center">
																<div class="form-group validated col-sm-6 col-lg-5 col-12">
																	<select class="form-control select2" style="width: 100%" id="kt_select2_2" name="transportadora">
																		<option value="null">Selecione a transportadora (opcional)</option>
																		@foreach($transportadoras as $t)
																		<option 
																		@if($venda->transportadora_id == $t->id)
																		selected
																		@endif
																		value="{{$t->id}}">{{$t->id}} - {{$t->razao_social}}</option>
																		@endforeach
																	</select>
																</div>
															</div>
														</div>
													</div>
													<hr>

													<div class="row">
														<div class="col-xl-12">
															<h3>Frete</h3>

															<div class="row align-items-center">
																<div class="form-group validated col-sm-4 col-lg-4 col-8">
																	<label class="col-form-label" id="">Transportadora</label>
																	<select class="custom-select form-control" id="frete" name="frete">
																		<option @if($config->frete_padrao == '0') selected @endif value="0">0 - Emitente</option>
																		<option @if($config->frete_padrao == '1') selected @endif  value="1">1 - Destinatário</option>
																		<option @if($config->frete_padrao == '2') selected @endif  value="2">2 - Terceiros</option>
																		<option @if($config->frete_padrao == '9') selected @endif  value="9">9 - Sem Frete</option>
																	</select>
																</div>

																<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
																	<label class="col-form-label">Placa Veiculo</label>
																	<div class="">
																		<div class="input-group">
																			<input type="text" name="placa" class="form-control" value="@if($venda->frete != null) {{$venda->frete->placa}} @endif" id="placa"/>
																		</div>
																	</div>
																</div>

																<div class="form-group validated col-sm-2 col-lg-2 col-6">
																	<label class="col-form-label" id="">UF</label>
																	<select class="custom-select form-control" id="uf_placa" name="uf_placa">
																		<option value="--">--</option>
																		@foreach(App\Models\Venda::estados() as $e)
																		<option @if($venda->frete != null) @if($venda->frete->uf == $e) selected @endif @endif value="{{$e}}">{{$e}}</option>
																		@endforeach
																	</select>
																</div>

																<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
																	<label class="col-form-label">Valor</label>
																	<div class="">
																		<div class="input-group">
																			<input type="text" name="valor_frete" class="form-control" value="@if($venda->frete != null) {{$venda->frete->valor}} @endif" id="valor_frete"/>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<hr>
													<div class="row">
														<div class="col-xl-12">
															<h3>Volume</h3>

															<div class="row align-items-center">
																
																<div class="form-group col-lg-3 col-md-4 col-sm-6 col-6">
																	<label class="col-form-label">Espécie</label>
																	<div class="">
																		<div class="input-group">
																			<input type="text" name="especie" class="form-control" value="@if($venda->frete != null) {{$venda->frete->especie}} @endif" id="especie"/>
																		</div>
																	</div>
																</div>

																<div class="form-group col-lg-3 col-md-4 col-sm-6 col-6">
																	<label class="col-form-label">Numeração de Volumes</label>
																	<div class="">
																		<div class="input-group">
																			<input type="text" name="numeracaoVol" class="form-control" value="@if($venda->frete != null) {{$venda->frete->numeracaoVolumes}} @endif" id="numeracaoVol"/>
																		</div>
																	</div>
																</div>

																<div class="form-group col-lg-3 col-md-4 col-sm-6 col-6">
																	<label class="col-form-label">Quantidade de Volumes</label>
																	<div class="">
																		<div class="input-group">
																			<input type="text" name="qtdVol" class="form-control" value="@if($venda->frete != null) {{$venda->frete->qtdVolumes}} @endif" id="qtdVol"/>
																		</div>
																	</div>
																</div>

																<div class="form-group col-lg-3 col-md-4 col-sm-6 col-6">
																	<label class="col-form-label">Peso Liquido</label>
																	<div class="">
																		<div class="input-group">
																			<input type="text" name="pesoL" class="form-control" value="@if($venda->frete != null) {{$venda->frete->peso_liquido}} @endif" id="pesoL"/>
																		</div>
																	</div>
																</div>

																<div class="form-group col-lg-3 col-md-4 col-sm-6 col-6">
																	<label class="col-form-label">Peso Bruto</label>
																	<div class="">
																		<div class="input-group">
																			<input type="text" name="pesoB" class="form-control" value="@if($venda->frete != null) {{$venda->frete->peso_bruto}} @endif" id="pesoB"/>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>

												</div>

											</div>
											<!--end: Wizard Step 2-->

											<div class="pb-5" data-wizard-type="step-content">

												<!-- Inicio do card -->

												<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
													<div class="row">
														<div class="col-xl-12">

															<div class="row">
																
																<div class="col-lg-4 col-md-4 col-sm-5 col-12">
																	<h3>Pagamento</h3>
																	

																	<div class="row">
																		
																		<div class="row">
																			<div class="form-group validated col-sm-12 col-lg-12 col-12">
																				<label class="col-form-label" id="">Tipo de Pagamento</label>
																				<select class="custom-select form-control" id="tipoPagamento" name="tipoPagamento">
																					<option value="--">Selecione o Tipo de pagamento</option>
																					@foreach($tiposPagamento as $key => $t)
																					<option 
																					@if($venda->tipo_pagamento == $key)
																					selected
																					@endif
																					value="{{$key}}">{{$key}} - {{$t}}</option>
																					@endforeach
																				</select>
																			</div>
																		</div>
																		<div class="row">

																			<div class="form-group validated col-sm-12 col-lg-12 col-12">
																				<label class="col-form-label" id="">Forma de Pagamento</label>
																				<select class="custom-select form-control" id="formaPagamento" name="formaPagamento">
																					<option value="--">Selecione a forma de pagamento</option>
																					<option value="a_vista">A vista</option>
																					<option value="30_dias">30 Dias</option>
																					<option value="personalizado">Personalizado</option>
																					<option value="conta_crediario">Conta crediario</option>
																				</select>
																			</div>
																		</div>
																		<div class="row">

																			<div class="form-group col-lg-8 col-md-8 col-sm-8 col-12">
																				<label class="col-form-label">Quantidade de Parcelas</label>
																				<div class="">
																					<div class="input-group">
																						<input type="text" name="qtdParcelas" class="form-control" value="" id="qtdParcelas"/>
																					</div>
																				</div>
																			</div>
																			<div class="form-group col-lg-4 col-md-4 col-sm-4 col-12">
																				<br>
																				<a data-toggle="modal" onclick="renderizarPagamento()" id="btn-modal-pagamentos"data-target="#modal-pagamentos" type="button" style="margin-top: 20px;" class="btn btn-light-info font-weight-bold disabled">
																					<i class="la la-list"></i>
																				</a>
																			</div>
																		</div>

																		<div class="row">

																			<div class="form-group col-lg-6 col-md-6 col-sm-6 col-12">
																				<label class="col-form-label">Data Vencimento</label>
																				<div class="">
																					<div class="input-group date">
																						<input type="text" name="data" class="form-control" id="kt_datepicker_3" />
																						<div class="input-group-append">
																							<span class="input-group-text">
																								<i class="la la-calendar"></i>
																							</span>
																						</div>
																					</div>
																				</div>
																			</div>

																			<div class="form-group col-lg-6 col-md-6 col-sm-6 col-12">
																				<label class="col-form-label">Valor Parcela</label>
																				<div class="">
																					<div class="input-group">
																						<input type="text" name="valor_parcela" class="form-control" value="" id="valor_parcela"/>
																					</div>
																				</div>
																			</div>
																		</div>
																		<div class="row">
																			<div class="col-lg-12 col-md-12 col-sm-12 col-12">
																				<a id="add-pag" href="#!" style="width: 100%;" class="btn btn-light-success">
																					<i class="la la-check"></i>
																					Adicionar Pagamento
																				</a>
																			</div>
																		</div>

																	</div>
																</div>

																<div class="offset-lg-1 col-lg-7 col-md-7 col-sm-6 col-12">
																	<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
																		<div class="row">
																			<div class="col-xl-12">


																				<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

																					<table id="fatura" class="datatable-table" style="max-width: 100%; overflow: scroll;">
																						<thead class="datatable-head">
																							<tr class="datatable-row" style="left: 0px;">
																								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Parcela</span></th>
																								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Data</span></th>
																								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Valor</span></th>
																							</tr>
																						</thead>

																						<tbody class="datatable-body">
																							
																						</tbody>
																					</table>
																				</div>

																			</div>
																		</div>
																		<div class="row">
																			<button type="button" style="margin-top: 10px;" id="delete-parcelas" class="btn btn-light-danger">
																				<i class="la la-close"></i>
																				Excluir parcelas
																			</button>
																		</div>
																	</div>
																</div>
															</div>
														</div>


													</div>
												</div>
											</div>


										</form>

									</div>
								</div>
							</div>
						</div>

						<!-- Fim wizzard -->

					</div>
				</div>

				<div class="card card-custom gutter-b">


					<div class="card-body">
						<div class="row">
							<div class="col-12">
								<button data-toggle="modal" data-target="#modal-referencia-nfe" class="btn btn-warning">
									<i class="la la-list"></i>
									Referênciar NF-e
								</button>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3 col-lg-3 col-md-6 col-xl-3">
								<h5 style="margin-top: 15px;">Valor Total: R$ <strong id="totalNF">0,00</strong></h5>
								<h5>Soma de quantidade: <strong id="soma-quantidade">0</strong></h5>
							</div>

							<div class="col-sm-2 col-lg-4 col-md-6 col-xl-2">
								<div class="form-group col-lg-12 col-md-12 col-sm-12 col-12">
									<label class="col-form-label">Desconto</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="">
												<div class="input-group">
													<input type="text" name="desconto" class="form-control" value="" id="desconto"/>
												</div>
											</div>
											<button onclick="percDesconto()" type="button" class="btn btn-warning btn-sm">
												<i class="la la-percent"></i>
											</button>
										</div>

									</div>

								</div>
							</div>

							<div class="col-sm-2 col-lg-4 col-md-6 col-xl-2">
								<div class="form-group col-lg-12 col-md-12 col-sm-12 col-12">
									<label class="col-form-label">Acréscimo</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="">
												<input type="text" name="acrescimo" class="form-control money" value="" id="acrescimo"/>
											</div>
											<button onclick="setaAcresicmo()" type="button" class="btn btn-success btn-sm">
												<i class="la la-percent"></i>
											</button>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-8 col-lg-8 col-md-12 col-xl-5">
								<div class="form-group col-lg-12 col-md-12 col-sm-12 col-12">
									<label class="col-form-label">Informação Adicional</label>
									<div class="">
										<div class="input-group">
											<input type="text" name="obs" class="form-control" value="{{$venda->observacao}}" id="obs"/>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6 col-lg-6 col-md-6 col-xl-6 col-12">
								<a id="salvar-orcamento" style="width: 100%;" href="#" onclick="swal('Erro', 'Não é possível salvar como orçamento em editar!', 'error')" class="btn btn-primary disabled">Salvar como Orçamento</a>
							</div>

							<div class="col-sm-6 col-lg-6 col-md-6 col-xl-6 col-12">
								<a id="salvar-venda" style="width: 100%;" href="#" @if(isset($venda)) onclick="atualizarVenda('nfe')" @else onclick="salvarVenda('nfe')" @endif class="btn btn-success disabled">Atualizar Venda</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-cartao" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">INFORME OS DADOS DO CARTÃO</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="form-group validated col-sm-3 col-lg-3 col-6">
						<label class="col-form-label">Bandeira</label>
						<select class="custom-select" id="bandeira_cartao">
							<option value="">--</option>
							@foreach(App\Models\Venda::bandeiras() as $key => $b)
							<option
							@if($venda->bandeira_cartao == $key)
							selected
							@endif
							value="{{$key}}">{{$b}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group validated col-sm-4 col-lg-4 col-6">
						<label class="col-form-label">Código autorização(opcional)</label>
						<input value="{{$venda->cAut_cartao}}" type="text" placeholder="Código autorização" id="cAut_cartao" class="form-control" value="">
					</div>

					<div class="form-group validated col-sm-4 col-lg-5 col-12">
						<label class="col-form-label">CNPJ(opcional)</label>
						<input value="{{$venda->cnpj_cartao}}" type="text" placeholder="CNPJ" id="cnpj_cartao" data-mask="00.000.000/0000-00" name="cnpj_cartao" class="form-control" value="">
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button style="width: 100%" type="button" data-dismiss="modal" class="btn btn-success font-weight-bold">Salvar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-pag-outros" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">INFORME A DESCRIÇAO DO TIPO DE PAGAMENTO OUTROS</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					

					<div class="form-group validated col-12">
						<label class="col-form-label">Descrição</label>
						<input type="text" placeholder="Descrição" id="descricao_pag_outros" name="descricao_pag_outros" class="form-control" value="">
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button style="width: 100%" type="button" data-dismiss="modal" class="btn btn-success font-weight-bold">Salvar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-produto" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Novo Produto</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<div class="modal-body">

				<div class="wizard wizard-3" id="kt_wizard_v4" data-wizard-state="between" data-wizard-clickable="true">
					<!--begin: Wizard Nav-->

					<div class="wizard-nav">

						<div class="wizard-steps px-8 py-8 px-lg-15 py-lg-3">
							<!--begin::Wizard Step 1 Nav-->
							<div class="wizard-step" data-wizard-type="step" data-wizard-state="done">
								<div class="wizard-label">
									<h3 class="wizard-title">
										<span>
											IDENTIFICAÇÃO
										</span>
									</h3>
									<div class="wizard-bar"></div>
								</div>
							</div>
							<!--end::Wizard Step 1 Nav-->
							<!--begin::Wizard Step 2 Nav-->
							<div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
								<div class="wizard-label">
									<h3 class="wizard-title">
										<span>
											ALÍQUOTAS
										</span>
									</h3>
									<div class="wizard-bar"></div>
								</div>
							</div>
						</div>
					</div>

					<div class="card-body">
						<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">

							<!--begin: Wizard Form-->
							<form class="form fv-plugins-bootstrap fv-plugins-framework form-prod" id="kt_form">
								<!--begin: Wizard Step 1-->
								<p class="kt-widget__data text-danger">Campos com (*) obrigatório</p>

								<div class="pb-5" data-wizard-type="step-content">
									<div class="row">

										<div class="col-xl-2"></div>
										<div class="col-xl-8">
											<div class="row">

												<div class="form-group validated col-sm-9 col-lg-9">
													<label class="col-form-label">Nome*</label>
													<div class="">
														<input type="text" class="form-control @if($errors->has('nome')) is-invalid @endif" id="nome">
														
													</div>
												</div>

												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">Referência</label>
													<div class="">
														<input type="text" class="form-control @if($errors->has('referencia')) is-invalid @endif" id="referencia">
													</div>
												</div>

												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">Valor de Compra*</label>
													<div class="">
														<input type="text" id="valor_compra" class="form-control @if($errors->has('valor_compra')) is-invalid @endif money">
													</div>
												</div>

												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">Valor de Venda*</label>
													<div class="">
														<input type="text" id="valor_venda" class="form-control @if($errors->has('valor_venda')) is-invalid @endif money">
														
													</div>
												</div>

												
												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">Iniciar com Estoque</label>
													<div class="">
														<input type="text" id="estoque" class="form-control @if($errors->has('estoque')) is-invalid @endif money">
														
													</div>
												</div>

												<div class="form-group validated col-sm-4 col-lg-4">
													<label class="col-form-label">Código de Barras EAN13</label>
													<div class="">
														<input type="text" class="form-control @if($errors->has('codBarras')) is-invalid @endif" id="codBarras">
													</div>
												</div>


												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">Estoque minimo</label>
													<div class="">
														<input type="text" id="estoque_minimo" class="form-control @if($errors->has('estoque_minimo')) is-invalid @endif">
													</div>
												</div>


												<div class="form-group validated col-sm-6 col-lg-4">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Gerenciar estoque</label>
													<div class="col-6">
														<span class="switch switch-outline switch-primary">
															<label>
																<input value="true" type="checkbox" id="gerenciar_estoque">
																<span></span>
															</label>
														</span>
													</div>
												</div>

												<div class="form-group validated col-sm-6 col-lg-2">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Inativo</label>
													<div class="col-6">
														<span class="switch switch-outline switch-danger">
															<label>
																<input value="true" type="checkbox" id="inativo">
																<span></span>
															</label>
														</span>
													</div>
												</div>

												<div class="form-group validated col-lg-5 col-md-5 col-sm-10">
													<label class="col-form-label text-left col-12">Categoria</label>
													<div class="input-group">

														<select id="categoria_id" class="form-control custom-select">
															@foreach($categorias as $cat)
															<option value="{{$cat->id}}">{{$cat->nome}}
															</option>
															@endforeach
														</select>
														
													</div>
												</div>

												<div class="form-group validated col-sm-4 col-lg-4">
													<label class="col-form-label">Limite maximo desconto %</label>
													<div class="">
														<input type="text" id="limite_maximo_desconto" class="form-control @if($errors->has('limite_maximo_desconto')) is-invalid @endif">
													</div>
												</div>



												<div class="form-group validated col-sm-3 col-lg-4">
													<label class="col-form-label">Alerta de Venc. (Dias)</label>
													<div class="">
														<input type="text" id="alerta_vencimento" class="form-control @if($errors->has('alerta_vencimento')) is-invalid @endif">
													</div>
												</div>


												<div class="form-group validated col-lg-4 col-md-6 col-sm-10">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Unidade de compra *</label>

													<select class="custom-select form-control" id="unidade_compra" id="unidade_compra">
														@foreach($unidadesDeMedida as $u)
														<option value="{{$u}}">{{$u}}
														</option>
														@endforeach
													</select>
												</div>


												<div class="form-group validated col-sm-3 col-lg-3" id="conversao" style="display: none">
													<label class="col-form-label">Conversão Unitária</label>
													<div class="">
														<input type="text" id="conversao_unitaria" class="form-control @if($errors->has('conversao_unitaria')) is-invalid @endif">
													</div>
												</div>
												<div class="form-group validated col-lg-4 col-md-6 col-sm-10">
													<label class="col-form-label text-left col-lg-12 col-sm-12">Unidade de venda *</label>

													<select class="custom-select form-control" id="unidade_venda">
														@foreach($unidadesDeMedida as $u)
														<option  value="{{$u}}">{{$u}}
														</option>
														@endforeach
													</select>

												</div>

												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">NCM *</label>
													<div class="">
														<input type="text" id="NCM" class="form-control @if($errors->has('NCM')) is-invalid @endif" value="{{$tributacao->ncm_padrao}}">
													</div>
												</div>

												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">CEST</label>
													<div class="">
														<input type="text" id="CEST" class="form-control @if($errors->has('CEST')) is-invalid @endif">
													</div>
												</div>
												<hr>

												<div class="form-group validated col-12">
													<h3>Derivado Petróleo</h3>
												</div>

												<div class="form-group validated col-lg-6 col-md-10 col-sm-10">
													<label class="col-form-label">ANP</label>

													<select class="custom-select form-control" id="anp">
														<option value="">--</option>
														@foreach($anps as $key => $a)
														<option value="{{$key}}">[{{$key}}] - {{$a}}
														</option>
														@endforeach
													</select>
												</div>

												<div class="form-group validated col-lg-3 col-md-4 col-sm-4">
													<label class="col-form-label">%GLP</label>

													<input type="text" id="perc_glp" class="form-control @if($errors->has('perc_glp')) is-invalid @endif trib">
												</div>

												<div class="form-group validated col-lg-3 col-md-4 col-sm-4">
													<label class="col-form-label">%GNn</label>

													<input type="text" id="perc_gnn" class="form-control @if($errors->has('perc_gnn')) is-invalid @endif trib">
												</div>

												<div class="form-group validated col-lg-3 col-md-4 col-sm-4">
													<label class="col-form-label">%GNi</label>

													<input type="text" id="perc_gni" class="form-control @if($errors->has('perc_gni')) is-invalid @endif trib">
												</div>

												<div class="form-group validated col-lg-3 col-md-4 col-sm-4">
													<label class="col-form-label">Valor de partida</label>

													<input type="text" id="valor_partida" class="form-control @if($errors->has('valor_partida')) is-invalid @endif money">
												</div>

												<div class="form-group validated col-lg-3 col-md-4 col-sm-4">
													<label class="col-form-label">Un. tributável</label>

													<input type="text" id="unidade_tributavel" class="form-control @if($errors->has('unidade_tributavel')) is-invalid @endif" data-mask="AAAA">
												</div>

												<div class="form-group validated col-lg-3 col-md-4 col-sm-4">
													<label class="col-form-label">Qtd. tributável</label>

													<input type="text" id="quantidade_tributavel" class="form-control @if($errors->has('quantidade_tributavel')) is-invalid @endif" data-mask="00000,00" data-mask-reverse="true">
												</div>


												<hr>
												<div class="form-group validated col-12">
													<h3>Dados de dimensão e peso do produto (Opcional)</h3>
												</div>


												<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
													<label class="col-form-label">Largura (cm)</label>

													<input type="text" id="largura" class="form-control @if($errors->has('largura')) is-invalid @endif">

												</div>

												<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
													<label class="col-form-label">Altura (cm)</label>

													<input type="text" id="altura" class="form-control @if($errors->has('altura')) is-invalid @endif">
												</div>

												<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
													<label class="col-form-label">Comprimento (cm)</label>

													<input type="text" id="comprimento" class="form-control @if($errors->has('comprimento')) is-invalid @endif">
												</div>

												<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
													<label class="col-form-label">Peso liquido</label>

													<input type="text" id="peso_liquido" class="form-control @if($errors->has('peso_liquido')) is-invalid @endif">
												</div>

												<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
													<label class="col-form-label">Peso bruto</label>

													<input type="text" id="peso_bruto" class="form-control @if($errors->has('peso_bruto')) is-invalid @endif">
												</div>


												<div class="col-lg-12 col-xl-12">
													<p class="text-danger">*Se atente a preencher todos os dados para utilizar a Api dos correios.</p>
												</div>

											</div>

										</div>
									</div>

								</div>
							</div>
							<div class="pb-5" data-wizard-type="step-content">

								<div class="row">

									<div class="col-xl-2"></div>
									<div class="col-xl-8">

										<div class="row">

											<div class="form-group validated col-lg-10 col-md-10 col-sm-10">
												<label class="col-form-label text-left col-lg-12 col-sm-12">
													@if($tributacao->regime == 1)
													CST
													@else
													CSOSN
													@endif
												*</label>

												<select class="custom-select form-control" id="CST_CSOSN">
													@foreach($listaCSTCSOSN as $key => $c)
													<option value="{{$key}}" @if($config !=null) @if(isset($produto)) @if($key==$produto->CST_CSOSN)
														selected
														@endif
														@else
														@if($key == $config->CST_CSOSN_padrao)
														selected
														@endif
														@endif

														@endif
														>{{$key}} - {{$c}}
													</option>
													@endforeach
												</select>

											</div>

											<div class="form-group validated col-lg-5 col-md-10 col-sm-10">
												<label class="col-form-label text-left col-lg-12 col-sm-12">CST PIS *</label>

												<select class="custom-select form-control" id="CST_PIS">
													@foreach($listaCST_PIS_COFINS as $key => $c)
													<option value="{{$key}}" @if($config !=null) @if(isset($produto)) @if($key==$produto->CST_PIS)
														selected
														@endif
														@else
														@if($key == $config->CST_PIS_padrao)
														selected
														@endif
														@endif

														@endif
														>{{$key}} - {{$c}}
													</option>
													@endforeach
												</select>

											</div>

											<div class="form-group validated col-lg-5 col-md-10 col-sm-10">
												<label class="col-form-label text-left col-lg-12 col-sm-12">CST COFINS *</label>

												<select class="custom-select form-control" id="CST_COFINS">
													@foreach($listaCST_PIS_COFINS as $key => $c)
													<option value="{{$key}}" @if($config !=null) @if(isset($produto)) @if($key==$produto->CST_COFINS)
														selected
														@endif
														@else
														@if($key == $config->CST_COFINS_padrao)
														selected
														@endif
														@endif

														@endif
														>{{$key}} - {{$c}}
													</option>
													@endforeach
												</select>

											</div>

											<div class="form-group validated col-lg-10 col-md-10 col-sm-10">
												<label class="col-form-label text-left col-lg-12 col-sm-12">CST IPI *</label>

												<select class="custom-select form-control" id="CST_IPI">
													@foreach($listaCST_IPI as $key => $c)
													<option value="{{$key}}" @if($config !=null) @if(isset($produto)) @if($key==$produto->CST_IPI)
														selected
														@endif
														@else
														@if($key == $config->CST_IPI_padrao)
														selected
														@endif
														@endif

														@endif
														>{{$key}} - {{$c}}
													</option>
													@endforeach
												</select>
											</div>

											<div class="form-group validated col-lg-10 col-md-10 col-sm-10">
												<label class="col-form-label text-left col-lg-12 col-sm-12">
													@if($tributacao->regime == 1)
													CST Exportação
													@else
													CSOSN Exportação
													@endif
												*</label>

												<select class="custom-select form-control" id="CST_CSOSN_EXP">
													<option value="">--</option>
													@foreach($listaCSTCSOSN as $key => $c)
													<option value="{{$key}}" @if(isset($produto)) @if($key==$produto->CST_CSOSN_EXP)
														selected
														@endif
														@endif

														>{{$key}} - {{$c}}
													</option>
													@endforeach
												</select>

											</div>

											<div class="form-group validated col-sm-4 col-lg-4">
												<label class="col-form-label">CFOP saida interno *</label>
												<div class="">
													<input type="text" id="CFOP_saida_estadual" class="form-control @if($errors->has('CFOP_saida_estadual')) is-invalid @endif" value="{{{ isset($produto->CFOP_saida_estadual) ? $produto->CFOP_saida_estadual : $natureza->CFOP_saida_estadual }}}">
												</div>
											</div>
											<div class="form-group validated col-sm-4 col-lg-4">
												<label class="col-form-label">CFOP saida externo *</label>
												<div class="">
													<input type="text" id="CFOP_saida_inter_estadual" class="form-control @if($errors->has('CFOP_saida_inter_estadual')) is-invalid @endif" value="{{{ isset($produto->CFOP_saida_inter_estadual) ? $produto->CFOP_saida_inter_estadual : $natureza->CFOP_saida_inter_estadual }}}">
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">%ICMS *</label>
												<div class="">
													<input type="text" id="perc_icms" class="form-control trib @if($errors->has('perc_icms')) is-invalid @endif" value="{{{ isset($produto->perc_icms) ? $produto->perc_icms : $tributacao->icms }}}">
												</div>
											</div>
											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">%PIS *</label>
												<div class="">
													<input type="text" id="perc_pis" class="form-control trib @if($errors->has('perc_pis')) is-invalid @endif" value="{{{ isset($produto->perc_pis) ? $produto->perc_pis : $tributacao->pis }}}">
												</div>
											</div>
											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">%COFINS *</label>
												<div class="">
													<input type="text" id="perc_cofins" class="form-control trib @if($errors->has('perc_cofins')) is-invalid @endif" value="{{{ isset($produto->perc_cofins) ? $produto->perc_cofins : $tributacao->cofins }}}">
												</div>
											</div>
											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">%IPI *</label>
												<div class="">
													<input type="text" id="perc_ipi" class="form-control trib @if($errors->has('perc_ipi')) is-invalid @endif" value="{{{ isset($produto->perc_ipi) ? $produto->perc_ipi : $tributacao->ipi }}}">
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">%ISS*</label>
												<div class="">
													<input type="text" id="perc_iss" class="form-control trib @if($errors->has('perc_iss')) is-invalid @endif" value="{{{ isset($produto->perc_iss) ? $produto->perc_iss : 0.00 }}}">
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">%Redução BC *</label>
												<div class="">
													<input type="text" id="pRedBC" class="form-control @if($errors->has('pRedBC')) is-invalid @endif" value="{{{ isset($produto->pRedBC) ? $produto->pRedBC : 0.00 }}}">
													
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">Cod benefício</label>
												<div class="">
													<input type="text" id="cBenef" class="form-control @if($errors->has('cBenef')) is-invalid @endif" value="{{{ isset($produto->cBenef) ? $produto->cBenef : old('cBenef') }}}">
													
												</div>
											</div>
											<div class="col-xl-12"></div>
											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">%ICMS interestadual</label>
												<div class="">
													<input type="text" id="perc_icms_interestadual" class="form-control @if($errors->has('perc_icms_interestadual')) is-invalid @endif trib" value="{{{ isset($produto->perc_icms_interestadual) ? $produto->perc_icms_interestadual : old('perc_icms_interestadual') }}}">

												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">%ICMS interno</label>
												<div class="">
													<input type="text" id="perc_icms_interno" class="form-control @if($errors->has('perc_icms_interno')) is-invalid @endif trib" value="{{{ isset($produto->perc_icms_interno) ? $produto->perc_icms_interno : old('perc_icms_interno') }}}">
													
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">%FCP interestadual</label>
												<div class="">
													<input type="text" id="perc_fcp_interestadual" class="form-control @if($errors->has('perc_fcp_interestadual')) is-invalid @endif trib" value="{{{ isset($produto->perc_fcp_interestadual) ? $produto->perc_fcp_interestadual : old('perc_fcp_interestadual') }}}">
													
												</div>
											</div>

										</div>
									</div>
								</div>
							</div>

						</div>

					</form>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn-frete" class="btn btn-danger font-weight-bold spinner-white spinner-right" data-dismiss="modal" aria-label="Close">Fechar</button>
				<button type="button" onclick="salvarProduto()" class="btn btn-success font-weight-bold spinner-white spinner-right">Salvar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-cod-barras" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">INFORME O CÓDIGO MANUAL</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="form-group validated col-sm-12 col-lg-12 col-12">
						<label class="col-form-label" id="">Código de barras</label>
						<input type="text" placeholder="Código de barras" id="cod-barras2" name="cod-barras2" class="form-control pula" value="">
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button style="width: 100%" type="button" onclick="apontarCodigoDeBarras()" class="btn btn-success font-weight-bold pula">OK</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-referencia-nfe" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Referência NF-e</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="form-group validated col-12 col-lg-10">

						<div class="">
							<input placeholder="Chave" type="text" id="chave" class="form-control">
						</div>
					</div>

					<div class="form-group validated col-12 col-lg-2">
						<button onclick="addChave()" class="btn btn-success">
							<i class="la la-plus"></i>
						</button>
					</div>
				</div>

				<div class="row">
					<div class="col-12">

						<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">
							<table class="datatable-table" id="chaves">
								<thead class="datatable-head">
									<tr class="datatable-row">
										<th class="datatable-cell datatable-cell-sort">
											Chave
										</th>
									</tr>
								</thead>
								<tbody class="datatable-body" id="chaves"></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button data-dismiss="modal" type="button" data-dismiss="modal" class="btn btn-success font-weight-bold">OK</button>
			</div>
		</div>
	</div>
</div>

@endsection