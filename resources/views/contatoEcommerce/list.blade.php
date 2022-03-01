@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">

	<div class="card-body">
		
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<form method="get" action="/contatoEcommerce/pesquisa">
				<div class="row align-items-center">
					<div class="col-lg-5 col-xl-5">
						<div class="row align-items-center">
							<div class="col-md-12 my-2 my-md-0">
								<div class="input-icon">
									<input type="text" name="pesquisa" class="form-control" placeholder="Cliente..." id="kt_datatable_search_query">
									<span>
										<i class="fa fa-search"></i>
									</span>
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
			<h4>Contatos do Ecommerce</h4>
			<label>Total de registros: {{sizeof($contatos)}}</label>
			<div class="row">

				<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">

					<div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="between" data-wizard-clickable="true">

							<div class="row">
								<div class="col-xl-12">

									<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

										<table class="datatable-table" style="max-width: 100%; overflow: scroll">
											<thead class="datatable-head">
												<tr class="datatable-row" style="left: 0px;">
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">NOME</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">EMAIL</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 400px;">TEXTO</span></th>
													<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">AÇÕES</span></th>
												</tr>
											</thead>
											<tbody id="body" class="datatable-body">
												@foreach($contatos as $c)
												<tr class="datatable-row">
													<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$c->nome}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$c->email}}</span>
													</td>
													<td class="datatable-cell"><span class="codigo" style="width: 400px;" id="id">{{$c->texto}}</span>
													</td>
													

													<td class="datatable-cell">
														<span class="codigo" style="width: 100px;" id="id">
															
															<a class="btn btn-danger" onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/contatoEcommerce/delete/{{ $c->id }}" }else{return false} })' href="#!">
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

					</div>
				</div>

			</div>

			<div class="d-flex justify-content-between align-items-center flex-wrap">
				<div class="d-flex flex-wrap py-2 mr-3">
					@if(isset($links))
					{{$contatos->links()}}
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@endsection