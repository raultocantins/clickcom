@extends('default.layout')
@section('content')
<div class="row" id="anime" style="display: none">
	<div class="col s8 offset-s2">
		<lottie-player src="/anime/success.json" background="transparent" speed="0.8" style="width: 100%; height: 300px;" autoplay>
		</lottie-player>
	</div>
</div>
<div id="content" style="display: block">
	<div class="content d-flex flex-column flex-column-fluid" id="kt_content">


		<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__bounce">
			<div class="card card-custom gutter-b example example-compact">
				<div class="col-lg-12">
					<!--begin::Portlet-->


					<input type="hidden" name="id" value="{{{ isset($cliente) ? $cliente->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">Importando XML</h3>
						</div>

					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">

							<h4 class="center-align">Nota Fiscal: <strong class="text-primary">{{$dadosNf['nNf']}}</strong></h4>
							<h4 class="center-align">Chave: <strong class="text-primary">{{$dadosNf['chave']}}</strong></h4>
							@if(count($dadosAtualizados) > 0)
							<div class="row">
								<div class="col-xl-12">
									<h5 class="text-success">Dados atualizados do fornecedor</h5>
									@foreach($dadosAtualizados as $d)
									<p class="red-text">{{$d}}</p>
									@endforeach
								</div>
							</div>
							@endif


							<div class="row">
								<div class="col s8">
									<h5>Fornecedor: <strong>{{$dadosEmitente['razaoSocial']}}</strong></h5>
									<h5>Nome Fantasia: <strong>{{$dadosEmitente['nomeFantasia']}}</strong></h5>
								</div>
								<div class="col s4">
									<h5>CNPJ: <strong>{{$dadosEmitente['cnpj']}}</strong></h5>
									<h5>IE: <strong>{{$dadosEmitente['ie']}}</strong></h5>
								</div>
							</div>
							<div class="row">
								<div class="col s8">
									<h5>Logradouro: <strong>{{$dadosEmitente['logradouro']}}</strong></h5>
									<h5>Numero: <strong>{{$dadosEmitente['numero']}}</strong></h5>
									<h5>Bairro: <strong>{{$dadosEmitente['bairro']}}</strong></h5>
								</div>
								<div class="col s4">
									<h5>CEP: <strong>{{$dadosEmitente['cep']}}</strong></h5>
									<h5>Fone: <strong>{{$dadosEmitente['fone']}}</strong></h5>
								</div>
							</div>

							<input type="hidden" id="pathXml" value="{{$pathXml}}">
							<input type="hidden" id="idFornecedor" value="{{$idFornecedor}}">
							<input type="hidden" id="nNf" value="{{$dadosNf['nNf']}}">
							<input type="hidden" id="vDesc" value="{{$dadosNf['vDesc']}}">
							<input type="hidden" id="prodSemRegistro" value="{{$dadosNf['contSemRegistro']}}">
							<input type="hidden" id="chave" value="{{$dadosNf['chave']}}">

						</div>
						<div class="col-xl-12">
							<div class="row">
								<div class="col-xl-12">

									<p class="text-danger">* Produtos em vermelho ainda não cadastrado no sistma</p>
									<p> Produtos sem registro no sistema: <strong class="prodSemRegistro">{{$dadosNf['contSemRegistro']}}</strong></p>
									<h4>Itens da NF</h4>
									<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">
										<table class="datatable-table" style="max-width: 100%;overflow: scroll">
											<thead class="datatable-head">
												<tr class="datatable-row" style="left: 0px;">
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 70px;">#</span></th>
													<th data-field="Country" class="datatable-cell datatable-cell-sort"><span style="width: 180px;">Produto</span></th>
													<th data-field="ShipDate" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">NCM</span></th>
													<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">CFOP</span></th>
													<th data-field="Status" class="datatable-cell datatable-cell-sort"><span style="width: 90px;">Cod Barra</span></th>
													<th data-field="Type" data-autohide-disabled="false" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">Un. Compra</span></th>
													<th data-field="Type" data-autohide-disabled="false" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">Valor</span></th>
													<th data-field="Type" data-autohide-disabled="false" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">Qtd</span></th>
													<th data-field="Type" data-autohide-disabled="false" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">CFOP Ent.</span></th>
													<th data-field="Type" data-autohide-disabled="false" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">Subtotal</span></th>
													<th data-field="Actions" data-autohide-disabled="false" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">Ações</span></th>
												</tr>
											</thead>

											<tbody class="datatable-body">
												@foreach($itens as $i)

												<tr class="datatable-row" id="tr_{{$i['codigo']}}" style="left: 0px;">
													<td class="datatable-cell"><span class="codigo" style="width: 70px;">{{$i['codigo']}}</span></td>
													<td class="datatable-cell"><span style="width: 180px;" id="n_{{$i['codigo']}}" class="{{$i['produtoNovo'] ? 'text-danger' : ''}} nome">{{$i['xProd']}}</span></td>
													<td class="datatable-cell"><span class="ncm" style="width: 80px;">{{$i['NCM']}}</span></td>
													<td class="datatable-cell"><span class="cfop" style="width: 80px;">{{$i['CFOP']}}</span></td>
													<td class="datatable-cell"><span class="codBarras" style="width: 90px;">{{$i['codBarras']}}</span></td>
													<td class="datatable-cell"><span class="unidade" style="width: 80px;">{{$i['uCom']}}</span></td>
													<td class="datatable-cell"><span class="valor" style="width: 80px;">{{number_format((float)$i['vUnCom'], $casasDecimais, ',', '.')}}</span></td>
													<td class="datatable-cell"><span id="qtd_aux_{{$i['codigo']}}" class="quantidade" style="width: 80px;">{{$i['qCom']}}</span></td>

													<th class="cod" id="th_prod_id_{{$i['codigo']}}" style="visibility: hidden">{{$i['produtoId']}}</th>

													<th class="valor_venda" id="th_prod_valor_venda_{{$i['codigo']}}" style="display: none">-1</th>

													<th style="visibility: hidden" class="conv_estoque" id="th_prod_conv_unit_{{$i['codigo']}}">{{$i['conversao_unitaria']}}</th>

													<th style="display: none" class="valor_compra" id="th_prod_valor_compra_{{$i['codigo']}}">-1</th>

													<th style="display: none" class="link_prod" id="link_prod_{{$i['codigo']}}">

													</th>

													<td class="datatable-cell quantidade">
														<span style="width: 80px;" id="cfop_entrada_{{$i['codigo']}}">
															<input id="cfop_entrada_input" class="cfop form-control" style="width: 60px;" type="text" value="{{$i['CFOP_entrada']}}" name="">
														</span>
													</td>

													<td class="datatable-cell quantidade"><span style="width: 80px;">{{number_format((float) $i['qCom'] * (float) $i['vUnCom'], $casasDecimais, ',', '.')}}</span></td>

													<th class="datatable-cell">
														<span style="width: 80px;">
															<a id="th_acao1_{{$i['codigo']}}" @if($i['produtoNovo']) style="display: block" @else style="display: none" @endif onclick="cadProd('{{$i['codigo']}}','{{$i['xProd']}}','{{$i['codBarras']}}','{{$i['NCM']}}','{{$i['CFOP']}}','{{$i['uCom']}}','{{$i['vUnCom']}}','{{$i['qCom']}}')" href="javascript:;" class="btn btn-sm btn-clean btn-icon mr-2">
																<span class="svg-icon svg-icon-success">
																	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																		<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																			<rect fill="#000000" x="4" y="11" width="16" height="2" rx="1" />
																			<rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1" />
																		</g>
																	</svg>
																</span>
															</a>

															<a id="th_acao2_{{$i['codigo']}}" @if($i['produtoNovo']) style="display: none" @else style="display: block" @endif onclick="editProd('{{$i['codigo']}}')" href="javascript:;" class="btn btn-sm btn-clean btn-icon mr-2">
																<span class="svg-icon svg-icon-danger"> <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																	<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																		<rect x="0" y="0" width="24" height="24" />
																		<path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) " />
																		<rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1" />
																	</g>
																</svg>
															</span>
														</a>
													</span>
												</th>

											</tr>
											@endforeach
										</tbody>
									</table>
									<br><br>

									@if($dadosNf['contSemRegistro'] > 0)
									<div class="row sem-registro">
										<div class="col-xl-12">
											<p class="text-danger">*Esta nota possui produto(s) sem cadastro inclua antes de continuar</p>
										</div>
									</div>
									@endif
								</div>


							</div>
						</div>
					</div>

					<div class="col-xl-12">
						<div class="card card-custom gutter-b example example-compact">

							<div class="row">
								<div class="form-group validated col-sm-2 col-lg-2">
									<label class="col-form-label">Data de Vencimento</label>
									<div class="">
										<div class="input-group date">
											<input type="text" class="form-control data-input" id="kt_datepicker_3">
											<div class="input-group-append">
												<span class="input-group-text">
													<i class="la la-calendar"></i>
												</span>
											</div>
										</div>
									</div>
								</div>

								<div class="form-group validated col-sm-2 col-lg-2">
									<label class="col-form-label">Valor da parcela</label>
									<div class="">
										<input type="text" class="form-control" id="valor_parcela">

									</div>
								</div>

								<div class="form-group validated col-sm-4 col-lg-4">
									<br>
									<a style="margin-top: 13px;" id="add-pag" class="btn btn-primary font-weight-bold text-uppercase px-9 py-4">
										Adicionar Pag.
									</a>
								</div>
							</div>
							<div class="">
								<h2 style="margin-left: 10px;;">Fatura</h2>
								<input type="hidden" id="fatura" value="{{json_encode($fatura)}}">
								<div class="row" id="fatura-html">
									

								</div>
							</div>
						</div>
					</div>

					<input type="hidden" id="total" value="{{$dadosNf['vProd']}}" name="">
					<div class="col-xl-12">
						<div class="row">
							<div class="col-xl-6">
								<h4>Total: <strong id="valorDaNF" class="blue-text">{{$dadosNf['vProd']}}</strong></h4>
							</div>
							<div class="col-xl-3">
							</div>
							<div class="col-xl-3">
								<button id="salvarNF" disabled style="width: 100%" type="submit" class="btn btn-success">
									<i class="la la-check"></i>
									<span class="">Salvar</span>
								</button>
							</div>
						</div>
					</div>

				</div>
				<br>
			</div>
		</div>
	</div>
</div>
</div>



<div class="modal fade" id="modal1" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Adicionar produto</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<div class="modal-body">

				<div class="row">
					<div class="form-group validated col-sm-10 col-lg-10">
						<label class="col-form-label">Nome do Produto</label>
						<div class="input-group">
							<input id="nome" type="text" class="form-control" name="nome" value="">
							<div class="input-group-append">
								<button onclick="linkProduto()" class="btn btn-info" type="button">
									<i class="la la-search"></i>
									
								</button>
							</div>
						</div>
					</div>

				</div>

				<div class="row">
					<div class="form-group validated col-sm-3 col-lg-2">
						<label class="col-form-label">NCM</label>
						<div class="">
							<input id="ncm" type="text" class="form-control" name="ncm" value="">
						</div>
					</div>
					<div class="form-group validated col-sm-3 col-lg-2">
						<label class="col-form-label">CFOP</label>
						<div class="">
							<input id="cfop" type="text" class="form-control" name="cfop" value="">
						</div>
					</div>

					<div class="form-group validated col-sm-3 col-lg-2">
						<label class="col-form-label">Unidade de Compra</label>
						<div class="">
							<input id="un_compra" type="text" class="form-control" name="un_compra" value="">
						</div>
					</div>

					<div class="form-group validated col-sm-3 col-lg-2">
						<label class="col-form-label">Referência</label>
						<div class="">
							<input id="referencia" type="text" class="form-control" name="referencia" value="">
						</div>
					</div>
					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">Conversão unitária para estoque</label>
						<div class="">
							<input id="conv_estoque" type="text" class="form-control" name="conv_estoque" value="">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">Quantidade</label>
						<div class="">
							<input id="quantidade" type="text" class="form-control" name="quantidade" value="">
						</div>
					</div>

					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">Valor de compra</label>
						<div class="">
							<input id="valor" type="text" class="form-control" name="valor" value="">
						</div>
					</div>
					

					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">% lucro*</label>
						<div class="">
							<input type="text" id="percentual_lucro" class="form-control money" name="percentual_lucro" value="{{$config->percentual_lucro_padrao }}">
						</div>
					</div>
					

					<input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">

					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">Valor de Venda</label>
						<div class="">
							<input id="valor_venda" type="text" class="form-control" name="valor_venda" value="">
						</div>
					</div>
					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">Unidade de venda</label>
						<select class="custom-select form-control" id="unidade_venda">
							@foreach($unidadesDeMedida as $u)
							<option value="{{$u}}">{{$u}}</option>
							@endforeach
						</select>
					</div>

				
					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">Cor (Opcional)</label>
						<select class="custom-select form-control" id="cor">
							<option value="--">--</option>
							<option value="Preto">Preto</option>
							<option value="Branco">Branco</option>
							<option value="Dourado">Dourado</option>
							<option value="Vermelho">Vermelho</option>
							<option value="Azul">Azul</option>
							<option value="Rosa">Rosa</option>
						</select>
					</div>
					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">Categoria</label>
						<select class="custom-select form-control" id="categoria_id">
							@foreach($categorias as $cat)
							<option value="{{$cat->id}}">{{$cat->nome}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">Código de barras</label>
						<div class="">
							<input id="codBarras" type="text" class="form-control" name="codBarras" value="">
						</div>
					</div>

					<div class="form-group validated col-sm-6 col-lg-6">
						<label class="col-form-label">CST/CSOSN</label>
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

					<div class="form-group validated col-sm-4 col-lg-4">
						<label class="col-form-label">CST PIS</label>
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

					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">CST COFINS</label>
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

					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">CST IPI</label>
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
				</div>

				<div class="row">
					<div class="form-group validated col-sm-2 col-lg-2">
						<label class="col-form-label">%ICMS</label>
						<div class="">
							<input id="perc_icms" type="text" class="form-control trib" name="perc_icms" value="0">
						</div>
					</div>
					<div class="form-group validated col-sm-2 col-lg-2">
						<label class="col-form-label">%PIS</label>
						<div class="">
							<input id="perc_pis" type="text" class="form-control trib" name="perc_pis" value="0">
						</div>
					</div>
					<div class="form-group validated col-sm-2 col-lg-2">
						<label class="col-form-label">%COFINS</label>
						<div class="">
							<input id="perc_cofins" type="text" class="form-control trib" name="perc_cofins" value="0">
						</div>
					</div>
					<div class="form-group validated col-sm-2 col-lg-2">
						<label class="col-form-label">%IPI</label>
						<div class="">
							<input id="perc_ipi" type="text" class="form-control trib" name="perc_ipi" value="0">
						</div>
					</div>

				</div>


			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light-danger font-weight-bold" data-dismiss="modal">Fechar</button>
				<button type="button" id="salvar" class="btn btn-success font-weight-bold spinner-white spinner-right">Salvar</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modal2" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Editar produto</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<div class="modal-body">

				<input id="idEdit" type="hidden" class="form-control" name="idEdit" value="">

				<div class="row">
					<div class="form-group validated col-sm-12 col-lg-12">
						<label class="col-form-label">Nome do Produto</label>
						<div class="">
							<input id="nomeEdit" type="text" class="form-control" name="nomeEdit" value="">

						</div>
					</div>
				</div>


				<div class="row">

					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">Conv. unit. estoque</label>
						<div class="">
							<input id="conv_estoqueEdit" type="text" class="form-control" name="conv_estoqueEdit" value="">
						</div>
					</div>

					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">Valor de venda</label>
						<div class="">
							<input id="valorVendaEdit" type="text" class="form-control money" name="valorVendaEdit" value="">
						</div>
					</div>

					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">Valor de compra</label>
						<div class="">
							<input id="valorCompraEdit" type="text" class="form-control money" name="valorCompraEdit" value="">
						</div>
					</div>
				</div>


			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light-danger font-weight-bold" data-dismiss="modal">Fechar</button>
				<button type="button" id="salvarEdit" class="btn btn-success font-weight-bold spinner-white spinner-right">Salvar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-link" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Atribuir ao produto</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<div class="modal-body">

				<div class="row">
					<div class="form-group validated col-sm-12 col-lg-12 col-12">
						<label class="col-form-label" id="">Produto</label><br>
						<select class="form-control select2" style="width: 100%" id="kt_select2_1" name="produto_link">
							<option value="null">Selecione o produto</option>
							@foreach($produtos as $p)
							<option value="{{$p}}">{{$p->id}} - {{$p->nome}}</option>
							@endforeach
						</select>
					</div>
				</div>


				<div class="row">

					<div class="form-group validated col-sm-3 col-lg-3 col-12">
						<label class="col-form-label">Quantidade</label>
						<div class="">
							<input id="estoque" type="text" class="form-control" name="estoque" value="">
						</div>
					</div>

					<div class="form-group validated col-sm-3 col-lg-3 col-12">
						<label class="col-form-label">Valor de venda</label>
						<div class="">
							<input id="valor_venda2" type="text" class="form-control money" name="valor_venda2" value="">
						</div>
					</div>

					<div class="form-group validated col-sm-3 col-lg-3 col-12">
						<label class="col-form-label">Valor de compra</label>
						<div class="">
							<input id="valor_compra2" type="text" class="form-control money" name="valor_compra2" value="">
						</div>
					</div>
				</div>


			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light-danger font-weight-bold" data-dismiss="modal">Fechar</button>
				<button type="button" id="salvarLink" class="btn btn-success font-weight-bold spinner-white spinner-right">Salvar</button>
			</div>
		</div>
	</div>
</div>


@endsection