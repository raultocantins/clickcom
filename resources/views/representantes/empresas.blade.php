@extends('default.layout')
@section('content')


<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="/representantes/saveEmpresa">

					<input type="hidden" name="id" value="{{$representante->id}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">
							<h3 class="card-title">Atribuir empresa ao representante <strong style="margin-left: 3px;" class="text-danger">{{$representante->nome}}</strong></h3>
						</div>
					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">


									<div class="row">
										<div class="form-group validated col-xl-8">
											<label class="col-form-label" id="">Empresa</label><br>
											<select class="form-control select2" style="width: 100%" id="kt_select2_1" name="empresa">
												<option value="null">Selecione a empresa</option>
												@foreach($empresas as $e)
												<option value="{{$e->id}}"> 
													{{$e->nome}} 
													[{{$e->cnpj}}]
												</option>
												@endforeach
											</select>
											@if($errors->has('empresa'))
											<div class="invalid-feedback">
												{{ $errors->first('empresa') }}
											</div>
											@endif
										</div>

										<div class="col-lg-3 col-sm-6 col-md-4">
											<button style="width: 100%; margin-top: 37px;" type="submit" class="btn btn-success">
												<i class="la la-check"></i>
												<span class="">Adicionar</span>
											</button>
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

	<div style="margin-top: -20px;" class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">

		<div class="card card-custom gutter-b example example-compact">

			<div class="col-lg-12">
				<div class="row">
					<div class="col-xl-1"></div>
					<div class="col-xl-10">

						<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

							<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

								<table class="datatable-table" style="max-width: 100%; overflow: scroll">
									<thead class="datatable-head">
										<tr class="datatable-row" style="left: 0px;">
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 70px;">#</span></th>
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Nome</span></th>
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Data atribuição</span></th>

											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Ação</span></th>
										</tr>
									</thead>

									<tbody class="datatable-body">
										@foreach($representante->empresas as $e)
										<tr class="datatable-row">
											<td class="datatable-cell">
												<span class="codigo" style="width: 70px;">
													{{$e->empresa->id}}
												</span>
											</td>
											<td class="datatable-cell">
												<span class="codigo" style="width: 150px;">
													{{$e->empresa->nome}}
												</span>
											</td>
											<td class="datatable-cell">
												<span class="codigo" style="width: 120px;">
													{{ \Carbon\Carbon::parse($e->created_at)->format('d/m/Y H:i') }}
												</span>
											</td>

											<td class="datatable-cell">
												<span class="codigo" style="width: 120px;">
													<a href="/representantes/deleteAttr/{{$e->id}}" class="btn btn-danger">
														<i class="la la-trash"></i>
													</a>
												</span>
											</td>
										</tr>
										@endforeach

										@if(sizeof($representante->empresas) == 0)
										<tr class="datatable-row">

											<td colspan="4" class="text-danger" style="text-align: center;">
												Nenhuma empresa atribuída até o momento!!
											</td>
										</tr>
										@endif
									</tbody>
								</table>
								<br>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection