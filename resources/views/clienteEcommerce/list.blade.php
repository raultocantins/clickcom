@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<input type="hidden" id="_token" value="{{ csrf_token() }}">
			<form class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft" method="get" action="/clienteEcommerce/filtro">
				<div class="row align-items-center">

					<div class="form-group col-lg-3 col-md-4 col-sm-6">
						<label class="col-form-label">Cliente</label>
						<div class="">
							<div class="input-group">
								<input type="text" name="cliente" class="form-control" value="{{{isset($cliente) ? $cliente : ''}}}" />
							</div>
						</div>
					</div>					

					<div class="col-lg-2 col-xl-2 mt-2 mt-lg-0">
						<button style="margin-top: 15px;" class="btn btn-light-primary px-6 font-weight-bold">Pesquisa</button>
					</div>
				</div>
			</form>
			<br>
			<h4 class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Lista de Clientes de Ecommerce</h4> 

			<label class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Registros: <strong class="text-success">{{sizeof($clientes)}}</strong></label>
			<div class="row @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">
				<div class="form-group col-lg-3 col-md-4 col-sm-6">
					<a href="/clienteEcommerce/new" class="btn btn-success">
						<i class="la la-plus"></i>
						Novo Cliente
					</a>
				</div>
			</div>


		</div>

		<div class="row @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">
			<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">

				<div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="between" data-wizard-clickable="true">
					<!--begin: Wizard Nav-->


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
															<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">Nome</span></th>

															<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Documento</span></th>

															<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">Email</span></th>

															<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Telefone</span></th>
															
															<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Ações</span></th>
														</tr>
													</thead>

													<tbody id="body" class="datatable-body">
														<?php $total = 0; ?>
														@foreach($clientes as $c)
														<tr class="datatable-row">

															<td class="datatable-cell"><span class="codigo" style="width: 200px;" id="id">{{$c->nome}} {{$c->sobre_nome}}</span>
															</td>

															<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$c->cpf}}</span>
															</td>

															<td class="datatable-cell"><span class="codigo" style="width: 250px;" id="id">{{$c->email}}</span>
															</td>

															<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$c->telefone}}</span>
															</td>

															<td>
																<div class="row">
																	<span style="width: 150px;">
																		<a class="btn btn-warning" onclick='swal("Atenção!", "Deseja editar este registro?", "warning").then((sim) => {if(sim){ location.href="/clienteEcommerce/edit/{{ $c->id }}" }else{return false} })' href="#!">
																			<i class="la la-edit"></i>
																		</a>

																		<a class="btn btn-info" href="/enderecosEcommerce/{{ $c->id }}">
																			<i class="la la-map"></i>
																		</a>
																	</span>
																</div>
															</td>

														</tr>
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
							
							<!--end: Wizard Step 2-->
							<div class="d-flex justify-content-between align-items-center flex-wrap">
								<div class="d-flex flex-wrap py-2 mr-3">
									@if(isset($links))
									{{$clientes->links()}}
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