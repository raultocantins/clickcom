@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">


	<div class="card-body">
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-12">
				<div class="row">

					<a style="margin-left: 5px; margin-top: 5px;" href="/clientes/new" class="btn btn-lg btn-success">
						<i class="fa fa-plus"></i>Novo Cliente
					</a>

					@isset($paraImprimir)
					<form method="post" action="/clientes/relatorio">
						@csrf
						<input type="hidden" name="pesquisa" value="{{{ isset($pesquisa) ? $pesquisa : '' }}}">

						<input type="hidden" name="aniversariante" value="{{ $aniversariante }}">
						<button style="margin-left: 5px; margin-top: 5px;" class="btn btn-lg btn-info">
							<i class="fa fa-print"></i>Imprimir relatório
						</button>
					</form>
					@endisset

					<a style="margin-left: 5px; margin-top: 5px;" href="/clientes/importacao" class="btn btn-lg btn-danger">
						<i class="fa fa-arrow-up"></i>Importação
					</a>

				</div>
			</div>
		</div>
		<br>


		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<form method="get" action="/clientes/pesquisa">
				<div class="row align-items-center">
					<div class="col-lg-5 col-xl-5">
						<div class="row align-items-center">
							<div class="col-md-12 my-2 my-md-0">
								<div class="input-group">
									<input type="text" name="pesquisa" class="form-control" placeholder="Pesquisa cliente" id="kt_datatable_search_query" value="{{{ isset($pesquisa) ? $pesquisa : ''}}}">
									
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="la la-birthday-cake"></i>
										</span>
									</div>
									<div class="input-group-append">
										<span class="input-group-text">
											<label class="checkbox checkbox-inline checkbox-info">
												<input type="checkbox" name="aniversariante" @isset($aniversariante) @if($aniversariante == true) checked @endif @endif/>
												<span></span>
											</label>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>

					
					<div class="col-lg-2 col-xl-2 mt-2 mt-lg-0">
						<button class="btn btn-light-primary px-6 font-weight-bold">Pesquisa</button>
					</div>
				</div>

			</form>
			<br>
			<h4>Lista de Clientes</h4>
			@if(isset($totalGeralClientes))
			<label>Total de clientes cadastrados: <strong class="text-info">{{$totalGeralClientes}}</strong></label>
			@endif
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
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">RAZÃO SOCIAL</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">CPF/CNPJ</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">IE/RG</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">CIDADE</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">TELEFONE</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">EMAIL</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">DATA DE CADASTRO</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">DATA DE ANIVERSÁRIO</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">AÇÕES</span></th>
												</tr>
											</thead>
											<tbody id="body" class="datatable-body">
												@foreach($clientes as $c)
												<tr class="datatable-row">
													<td class="datatable-cell"><span class="codigo" style="width: 250px;" id="id">{{$c->razao_social}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$c->cpf_cnpj}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{$c->ie_rg}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 200px;" id="id">{{$c->cidade->nome}} ({{$c->cidade->uf}})</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 120px;" id="id">{{$c->telefone}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{$c->email}}</span>
													</td>

													<td class="datatable-cell">
														<span class="codigo" style="width: 100px;" id="id">
															{{\carbon\carbon::parse($c->created_at)->format('d/m/Y')}}
														</span>
													</td>

													@if($c->data_aniversario != "")
													<td class="datatable-cell">
														<span class="codigo" style="width: 100px;" id="id">
															{{$c->data_aniversario}}
														</span>
													</td>
													@else
													<td class="datatable-cell">
														<span class="codigo" style="width: 100px;" id="id">
															--
														</span>
													</td>
													@endif

													<td class="datatable-cell">
														<span class="codigo" style="width: 200px;" id="id">
															<a class="btn btn-warning" onclick='swal("Atenção!", "Deseja editar este registro?", "warning").then((sim) => {if(sim){ location.href="/clientes/edit/{{ $c->id }}" }else{return false} })' href="#!">
																<i class="la la-edit"></i>	
															</a>
															<a class="btn btn-danger" onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/clientes/delete/{{ $c->id }}" }else{return false} })' href="#!">
																<i class="la la-trash"></i>	
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

								@foreach($clientes as $c)

								<div class="col-sm-12 col-lg-6 col-md-6 col-xl-4">
									<div class="card card-custom gutter-b example example-compact">
										<div class="card-header">
											<div class="card-title">
												<h3 style="width: 230px; font-size: 12px; height: 10px;" class="card-title">{{substr($c->razao_social, 0, 30)}}
												</h3>
											</div>

											<div class="card-toolbar">
												<div class="dropdown dropdown-inline" data-toggle="tooltip" title="" data-placement="left" data-original-title="Ações">
													<a href="#" class="btn btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
														<i class="fa fa-ellipsis-h"></i>
													</a>
													<div class="dropdown-menu p-0 m-0 dropdown-menu-md dropdown-menu-left">
														<!--begin::Navigation-->
														<ul class="navi navi-hover">
															<li class="navi-header font-weight-bold py-4">
																<span class="font-size-lg">Ações:</span>
															</li>
															<li class="navi-separator mb-3 opacity-70"></li>
															<li class="navi-item">
																<a href="/clientes/edit/{{$c->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-primary">Editar</span>
																	</span>
																</a>
															</li>
															<li class="navi-item">
																<a onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/clientes/delete/{{ $c->id }}" }else{return false} })' href="#!" class="navi-link">
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

											<div class="card-body">

												<div class="kt-widget__info">
													<span class="kt-widget__label">Nome fantasia:</span>
													<a target="_blank" class="kt-widget__data text-success">{{ $c->nome_fantasia }}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">CNPJ/CPF:</span>
													<a target="_blank" class="kt-widget__data text-success">{{ $c->cpf_cnpj }}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">IE/RG:</span>
													<a class="kt-widget__data text-success">{{$c->ie_rg}}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Cidade:</span>
													<a class="kt-widget__data text-success">{{$c->cidade->nome}}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Data de cadastro:</span>
													<a class="kt-widget__data text-success">
														{{\carbon\carbon::parse($c->created_at)->format('d/m')}}
													</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Data de aniversário:</span>
													<a class="kt-widget__data text-success">
														@if($c->data_aniversario != "")
														{{$c->data_aniversario}}
														@else
														--
														@endif
													</a>
												</div>

												<div class="kt-widget__info">
													<span class="kt-widget__label">UF:</span>
													<a class="kt-widget__data text-success">{{$c->cidade->uf}}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Telefone:</span>
													<a class="kt-widget__data text-success">{{$c->telefone}}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Email:</span>
													<a class="kt-widget__data text-success">{{$c->email}}</a>
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
					{{$clientes->links()}}
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@endsection