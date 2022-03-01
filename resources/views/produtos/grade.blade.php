@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">


	<div class="card-body">
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-sm-12 col-lg-4 col-md-6 col-xl-12">

				<div class="row">
					
				</div>
			</div>
		</div>
		<br>

		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<br>
			<h4>Lista de Produtos Grade: <strong class="text-info">{{$produtos[0]->nome}}</strong></h4>
			<label>Total de registros: <strong>{{count($produtos)}}</strong></label>
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
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">VALOR</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">UN. COMPRA</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">UN. SAÍDA</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">GERENCIAR ESTOQUE</span></th>
													
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">ESTOQUE</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">AÇÕES</span></th>
												</tr>
											</thead>

											<tbody id="body" class="datatable-body">
												@foreach($produtos as $p)
												<tr class="datatable-row">
													<td class="datatable-cell"><span class="codigo" style="width: 250px;" id="id">{{$p->nome}} {{$p->str_grade}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{number_format($p->valor_venda, 2, ',', '.')}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{$p->unidade_compra}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{$p->unidade_venda}}</span>
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
															<a class="btn btn-warning" onclick='swal("Atenção!", "Deseja editar este registro?", "warning").then((sim) => {if(sim){ location.href="/produtos/editGrade/{{ $p->id }}" }else{return false} })' href="#!">
																<i class="la la-edit"></i>	
															</a>
															<a class="btn btn-danger" onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/produtos/delete/{{ $p->id }}" }else{return false} })' href="#!">
																<i class="la la-trash"></i>	
															</a>

															<a class="btn btn-info" href="/produtos/movimentacao/{{ $p->id }}">
																<i class="las la-tasks"></i>
															</a>

															<a class="btn btn-primary" onclick='swal("Atenção!", "Deseja duplicar este registro?", "warning").then((sim) => {if(sim){ location.href="/produtos/duplicar/{{ $p->id }}" }else{return false} })' href="#!">
																<i class="la la-copy"></i>	
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
													<span class="kt-widget__label">Valor:</span>
													<a class="kt-widget__data text-success">{{ number_format($p->valor_venda, 2, ',', '.') }}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Unidade:</span>
													<a class="kt-widget__data text-success">{{$p->unidade_compra}}/{{$p->unidade_venda}}</a>
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