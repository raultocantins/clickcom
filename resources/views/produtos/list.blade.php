@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">


	<div class="card-body">
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-12">

				<div class="row">
					<a style="margin-left: 5px; margin-top: 5px;" href="/produtos/new" class="btn btn-lg btn-success">
						<i class="fa fa-plus"></i>Novo Produto
					</a>

					@isset($paraImprimir)
					<form method="post" action="/produtos/relatorio">
						@csrf
						<input type="hidden" name="produto" value="{{{ isset($produto) ? $produto : '' }}}">
						<input type="hidden" name="categoria" value="{{$categoria}}">
						
						<input type="hidden" name="estoque" value="{{ $estoque }}">
						<button style="margin-left: 5px; margin-top: 5px;" class="btn btn-lg btn-info">
							<i class="fa fa-print"></i>Imprimir relatório
						</button>
					</form>
					@endisset

					<a style="margin-left: 5px; margin-top: 5px;" href="/produtos/importacao" class="btn btn-lg btn-danger">
						<i class="fa fa-arrow-up"></i>Importação
					</a>

					<a style="margin-left: 5px; margin-top: 5px;" href="/divisaoGrade" class="btn btn-lg btn-info">
						<i class="fa fa-th"></i>Divisao de Grade
					</a>

					@if(sizeof($produtos) > 0)
					<a style="margin-left: 5px; margin-top: 5px;" href="/percentualuf" class="btn btn-lg btn-warning">
						<i class="fa fa-percent"></i>Tributação por estado
					</a>
					@endif
				</div>
			</div>
		</div>
		<br>


		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<form method="get" action="/produtos/filtroCategoria">
				<div class="row align-items-center">
					<div class="col-lg-4 col-xl-4">
						<div class="row align-items-center">
							<div class="col-md-12 my-2 my-md-0">
								<label>Produto</label>

								<div class="input-icon">
									<input type="text" name="pesquisa" class="form-control" value="{{{isset($pesquisa) ? $pesquisa : ''}}}"
									placeholder="Produto..." id="kt_datatable_search_query">
									<span>
										<i class="fa fa-search"></i>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-3 col-xl-3">
						<div class="row align-items-center">
							<div class="col-md-12 my-2 my-md-0">
								<label>Categoria</label>
								<select class="form-control select2" id="kt_select2_1" name="categoria">
									<option value="-">Todas</option>
									@foreach($categorias as $c)
									<option @if(isset($categoria)) @if($c->id == $categoria)
										selected
										@endif
										@endif
										value="{{$c->id}}">{{$c->nome}}
									</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-2 col-xl-2">
						<div class="row align-items-center">
							<div class="col-md-12 my-2 my-md-0">
								<label>Estoque</label>
								<select class="form-control custom-select" name="estoque">
									<option value="--">--</option>
									<option @if(isset($estoque) && $estoque == 1) selected @endif value="1">Positivo</option>
									<option @if(isset($estoque) && $estoque == -1) selected @endif value="-1">Negativo</option>
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-2 col-xl-2">
						<button style="margin-top: 25px;" type="submit" class="btn btn-light-primary font-weight-bold">Pesquisa</button>
					</div>
				</div>

			</form>

			<br>
			<h4>Lista de Produtos</h4>
			@if(!isset($categoria))
			<label>Total de produtos cadastrados: <strong class="text-info">{{($totalGeralPrdutos)}}</strong></label>
			@endif
			<p class="text-danger">Produtos em vermelho inativos</p>
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

						<!-- inicio grid -->
						<div class="pb-5" data-wizard-type="step-content">
							<div class="row">
								<div class="col-xl-12">

									<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

										<table class="datatable-table" style="max-width: 100%; overflow: scroll">
											<thead class="datatable-head">
												<tr class="datatable-row" style="left: 0px;">
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">NOME</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">CATEGORIA</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">VALOR DE VENDA</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">VALOR DE COMPRA</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">UN. COMPRA</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">UN. VENDA</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">DATA DE CADASTRO</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">GERENCIAR ESTOQUE</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">TIPO GRADE</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">ESTOQUE</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">AÇÕES</span></th>
												</tr>
											</thead>

											<tbody id="body" class="datatable-body">
												@foreach($produtos as $p)
												<tr class="datatable-row" @if($p->inativo) style="background: #ffcdd2;" @endif>
													<td class="datatable-cell"><span class="codigo" style="width: 250px;" id="id">{{$p->nome}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{$p->categoria->nome}}</span>
													</td>

													@if($p->grade)
													<td class="datatable-cell">
														<span class="codigo" style="width: 100px;" id="id">
														{{$p->valoresGrade()}}	
														</span>
													</td>
													@else
													<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{number_format($p->valor_venda, $casasDecimais, ',', '.')}}</span>
													</td>
													@endif
													<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{number_format($p->valor_compra, $casasDecimais, ',', '.')}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{$p->unidade_compra}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{$p->unidade_venda}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 120px;">{{\Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i')}}</span>
													</td>
													<td class="datatable-cell">
														<span class="codigo" style="width: 120px;" id="id">
															@if($p->gerenciar_estoque)
															<span class="label label-xl label-inline label-light-success">Sim</span>
															@else
															<span class="label label-xl label-inline label-light-warning">Não</span>
															@endif
														</span>
													</td>

													<td class="datatable-cell">
														<span class="codigo" style="width: 120px;" id="id">
															@if($p->grade)
															<span class="label label-xl label-inline label-light-success">Sim</span>
															@else
															<span class="label label-xl label-inline label-light-warning">Não</span>
															@endif
														</span>
													</td>

													<td class="datatable-cell">
														<span class="codigo" style="width: 100px;" id="id">
															@if($p->estoque)
															@if($p->unidade_venda == 'UN' || $p->unidade_venda == 'UNID')
															{{number_format($p->estoque_atual)}}
															@else
															{{$p->estoque_atual}}
															@endif

															@else
															0
															@endif
														</span>
													</td>

													<td class="datatable-cell">
														<span class="codigo" style="width: 300px;" id="id">
															<a title="Editar" class="btn btn-warning" onclick='swal("Atenção!", "Deseja editar este registro?", "warning").then((sim) => {if(sim){ location.href="/produtos/edit/{{ $p->id }}" }else{return false} })' href="#!">
																<i class="la la-edit"></i>	
															</a>
															<a title="Remover" class="btn btn-danger" onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/produtos/delete/{{ $p->id }}" }else{return false} })' href="#!">
																<i class="la la-trash"></i>	
															</a>
															@if($p->composto)
															<a class="btn btn-primary" href="/produtos/receita/{{ $p->id }}">
																<i class="la la-list"></i>	
															</a>
															@endif

															@if($p->grade)
															<a title="Grade" class="btn btn-primary" href="/produtos/grade/{{ $p->id }}">
																<i class="la la-th"></i>	
															</a>
															@endif

															<a title="Movimentação" class="btn btn-info" href="/produtos/movimentacao/{{ $p->id }}">
																<i class="las la-tasks"></i>
															</a>

															<a title="Duplicar" class="btn btn-primary" onclick='swal("Atenção!", "Deseja duplicar este registro?", "warning").then((sim) => {if(sim){ location.href="/produtos/duplicar/{{ $p->id }}" }else{return false} })' href="#!">
																<i class="la la-copy"></i>	
															</a>

															@if($p->ecommerce)
															<a title="Ecommerce" title="Ecommerce" class="btn btn-info" href="/produtoEcommerce/edit/{{ $p->ecommerce->id }}">
																<i class="la la-shopping-cart"></i>
															</a>
															@endif

															<a title="Gerar etiqueta(s)" class="btn btn-dark" href="/produtos/etiqueta/{{ $p->id }}">
																<i class="la la-barcode"></i>
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

								@foreach($produtos as $p)

								<div class="col-sm-12 col-lg-6 col-md-6 col-xl-4">
									<div class="card card-custom gutter-b example example-compact">
										<div class="card-header">
											<div class="card-title">
												<div class="flex-shrink-0 mr-4 mt-lg-0 mt-3">
													<div class="symbol symbol-circle symbol-lg-75">
														@if($p->imagem != '')
														<img src="/imgs_produtos/{{$p->imagem}}" alt="image">
														@else
														<img src="/imgs/no_image.png" alt="image">
														@endif

													</div>
												</div>
												<h3 style="width: 230px; font-size: 12px; height: 10px;" class="card-title">{{substr($p->nome, 0, 30)}}
												</h3>

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
																<a href="/produtos/edit/{{$p->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-primary">Editar</span>
																	</span>
																</a>
															</li>
															<li class="navi-item">
																<a onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/produtos/delete/{{ $p->id }}" }else{return false} })' href="#!" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-danger">Excluir</span>
																	</span>
																</a>
															</li>

															@if($p->composto)
															<li class="navi-item">
																<a href="/produtos/receita/{{$p->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-warning">Receita/Composição</span>
																	</span>
																</a>
															</li>
															@endif

															@if($p->grade)
															<li class="navi-item">
																<a href="/produtos/grade/{{$p->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-warning">Grade</span>
																	</span>
																</a>
															</li>
															@endif

															<li class="navi-item">
																<a href="/produtos/movimentacao/{{$p->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-info">Movimentacao</span>
																	</span>
																</a>
															</li>

															<li class="navi-item">
																<a href="/produtos/etiqueta/{{$p->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-dark">Etiqueta</span>
																	</span>
																</a>
															</li>

															@if($p->ecommerce)

															<li class="navi-item">
																<a href="/produtoEcommerce/edit/{{$p->ecommerce->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-dark">Ecommerce</span>
																	</span>
																</a>
															</li>
															@endif

														</ul>

													</div>
												</div>


											</div>

											<div class="card-body">

												<div class="kt-widget__info">
													<span class="kt-widget__label">Categoria:</span>
													<a target="_blank" href="/categorias/edit/{{ $p->categoria->id }}" class="kt-widget__data text-success">{{ $p->categoria->nome }}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Valor de venda:</span>
													<a class="kt-widget__data text-success">{{ number_format($p->valor_venda, $casasDecimais, ',', '.') }}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Valor de compra:</span>
													<a class="kt-widget__data text-success">{{ number_format($p->valor_compra, $casasDecimais, ',', '.') }}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Unidade:</span>
													<a class="kt-widget__data text-success">{{$p->unidade_compra}}/{{$p->unidade_venda}}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Data de cadastro:</span>
													<a class="kt-widget__data text-success">{{\Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i')}}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Gerenciar estoque:</span>
													@if($p->gerenciar_estoque)
													<span class="label label-xl label-inline label-light-success">Sim</span>
													@else
													<span class="label label-xl label-inline label-light-warning">Não</span>
													@endif
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Tipo grade:</span>
													@if($p->grade)
													<span class="label label-xl label-inline label-light-success">Sim</span>
													@else
													<span class="label label-xl label-inline label-light-warning">Não</span>
													@endif

												</div>

												<div class="kt-widget__info">
													<span class="kt-widget__label">Estoque:</span>
													<a class="kt-widget__data text-success">

														@if($p->estoque)
														@if($p->unidade_venda == 'UN' || $p->unidade_venda == 'UNID')
														{{number_format($p->estoque_atual)}}
														@else
														{{$p->estoque_atual}}
														@endif

														@else
														0
														@endif
													</a>
												</div>

											</div>

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
					{{$produtos->links()}}
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@endsection