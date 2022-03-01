@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<input type="hidden" id="_token" value="{{ csrf_token() }}">
			<form class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft" method="get" action="/cidades/filtro">
				<div class="row align-items-center">

					<div class="form-group col-lg-4 col-md-6 col-sm-6">
						<label class="col-form-label">Nome</label>
						<div class="">
							<div class="input-group date">
								<input type="text" name="nome" class="form-control" value="{{{isset($nome) ? $nome : ''}}}" />
								
							</div>
						</div>
					</div>

					
					<div class="col-lg-2 col-xl-2 mt-2 mt-lg-0">
						<button style="margin-top: 15px;" class="btn btn-light-primary px-6 font-weight-bold">Pesquisa</button>
					</div>
				</div>
			</form>

			<h4 class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Lista de Cidades</h4>

			<label class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Registros: exibindo <strong class="text-success">{{sizeof($cidades)}} de {{$count}}</strong></label>
			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
				<div class="row">

					<a href="/cidades/nova" class="btn btn-success">
						<i class="la la-plus"></i>
						Nova Cidade
					</a>

				</div>
			</div>

			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

					<table class="datatable-table" style="max-width: 100%; overflow: scroll">
						<thead class="datatable-head">
							<tr class="datatable-row" style="left: 0px;">
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 70px;">#</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">Nome</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">UF</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Código IBGE</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">Ações</span></th>
							</tr>
						</thead>

						<tbody class="datatable-body">
							@foreach($cidades as $c)

							<tr class="datatable-row">
								<td class="datatable-cell">
									<span class="codigo" style="width: 70px;">
										{{$c->id}}
									</span>
								</td>
								<td class="datatable-cell">
									<span class="codigo" style="width: 250px;">
										{{$c->nome}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">
										{{$c->uf}}
									</span>
								</td>
								<td class="datatable-cell">
									<span class="codigo" style="width: 150px;">
										{{$c->codigo}}
									</span>
								</td>


								<td class="datatable-cell">
									<span class="codigo" style="width: 200px;">
										<a href="/cidades/editar/{{$c->id}}" class="btn btn-sm btn-warning">
											Editar
										</a>
										<a onclick='swal("Atenção!", "Deseja remover esta cidade?", "warning").then((sim) => {if(sim){ location.href="/cidades/delete/{{ $c->id }}" }else{return false} })' href="#!"  class="btn btn-sm btn-danger">
											Remover
										</a>
									</span>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<div class="d-flex justify-content-between align-items-center flex-wrap">
				<div class="d-flex flex-wrap py-2 mr-3">
					@if(isset($links))
					{{$cidades->links()}}
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@endsection	
