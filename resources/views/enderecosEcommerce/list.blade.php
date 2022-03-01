@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<input type="hidden" id="_token" value="{{ csrf_token() }}">
			
			<br>
			<h4 class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Endereços de <strong class="text-info">{{$cliente->nome}} {{$cliente->sobre_nome}}</strong></h4> 

			<label class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Registros: <strong class="text-success">{{sizeof($cliente->enderecos)}}</strong></label>

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
															<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">Rua</span></th>

															<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Número</span></th>

															<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Bairro</span></th>

															<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">CEP</span></th>

															<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Cidade</span></th>
															
															<th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Ações</span></th>
														</tr>
													</thead>

													<tbody id="body" class="datatable-body">
														<?php $total = 0; ?>
														@foreach($cliente->enderecos as $e)
														<tr class="datatable-row">

															<td class="datatable-cell"><span class="codigo" style="width: 200px;" id="id">{{$e->rua}}</span>
															</td>

															<td class="datatable-cell"><span class="codigo" style="width: 90px;" id="id">{{$e->numero}}</span>
															</td>

															<td class="datatable-cell"><span class="codigo" style="width: 140px;" id="id">{{$e->bairro}}</span>
															</td>

															<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$e->cep}}</span>
															</td>

															<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$e->cidade}} ({{$e->uf}})</span>
															</td>

															<td>
																<div class="row">
																	<span style="width: 150px;">
																		<a class="btn btn-warning" onclick='swal("Atenção!", "Deseja editar este registro?", "warning").then((sim) => {if(sim){ location.href="/enderecosEcommerce/edit/{{ $e->id }}" }else{return false} })' href="#!">
																			<i class="la la-edit"></i>
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
							
							
						</form>

					</div>
				</div>
			</div>
		</div>
	</div>

</div>


@endsection	