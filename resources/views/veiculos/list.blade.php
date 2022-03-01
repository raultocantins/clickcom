@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">


	<div class="card-body">
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-sm-12 col-lg-4 col-md-6 col-xl-4">

				<a href="/veiculos/new" class="btn btn-lg btn-success">
					<i class="fa fa-plus"></i>Novo Veiculo
				</a>
			</div>
		</div>
		<br>


		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">
			
			<br>
			<h4>Lista de Veiculos</h4>
			<label>Total de registros: {{count($veiculos)}}</label>
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
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">MARCA</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">MODELO</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">COR</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">RNTRC</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">TARA/CAPACIDADE</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">CNPJ PROPRIETÁRIO</span></th>
													
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">AÇÕES</span></th>
												</tr>
											</thead>
											<tbody id="body" class="datatable-body">
												@foreach($veiculos as $v)
												<tr class="datatable-row">
													<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$v->marca}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$v->modelo}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$v->cor}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$v->rntrc}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$v->tara}}/{{$v->capacidade}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$v->proprietario_documento}}</span>
													</td>
													<td class="datatable-cell">
														<span class="codigo" style="width: 200px;" id="id">
															<a class="btn btn-warning" onclick='swal("Atenção!", "Deseja editar este registro?", "warning").then((sim) => {if(sim){ location.href="/veiculos/edit/{{ $v->id }}" }else{return false} })' href="#!">
																<i class="la la-edit"></i>	
															</a>
															<a class="btn btn-danger" onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/veiculos/delete/{{ $v->id }}" }else{return false} })' href="#!">
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
								@foreach($veiculos as $c)


								<div class="col-sm-12 col-lg-6 col-md-6 col-xl-4">
									<div class="card card-custom gutter-b example example-compact">
										<div class="card-header">
											<div class="card-title">
												<h3 style="width: 230px; font-size: 12px; height: 10px;" class="card-title">{{$c->placa}}
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
																<a href="/veiculos/edit/{{$c->id}}" class="navi-link">
																	<span class="navi-text">
																		<span class="label label-xl label-inline label-light-primary">Editar</span>
																	</span>
																</a>
															</li>
															<li class="navi-item">
																<a onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/veiculos/delete/{{$c->id}}" }else{return false} })' href="#!" class="navi-link">
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
													<span class="kt-widget__label">Marca:</span>
													<a target="_blank" class="kt-widget__data text-success">{{ $c->marca }}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Modelo:</span>
													<a class="kt-widget__data text-success">{{$c->modelo}}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">RNTRC:</span>
													<a class="kt-widget__data text-success">{{$c->rntrc}}</a>
												</div>
												<div class="kt-widget__info">
													<span class="kt-widget__label">Cor:</span>
													<a class="kt-widget__data text-success">{{$c->cor}}</a>
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
					{{$veiculos->links()}}
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@endsection