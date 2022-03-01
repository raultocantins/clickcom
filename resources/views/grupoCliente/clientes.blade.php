@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-sm-12 col-lg-4 col-md-6 col-xl-4">

				<h2>Lista de clientes</h2>
				<h3>Grupo: <strong class="text-info">{{$grupo->nome}}</strong></h3>
			</div>
		</div>
		<br>
		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">
			<br>

			<div class="row @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				<div class="col-xl-12">

					<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

						<table class="datatable-table" style="max-width: 100%; overflow: scroll">
							<thead class="datatable-head">
								<tr class="datatable-row" style="left: 0px;">

									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">CLIENTE</span></th>

									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">CPF/CNPJ</span></th>

									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 550px;">ENDEREÇO</span></th>

									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">CIDADE</span></th>

								</tr>
							</thead>

							<tbody id="body" class="datatable-body">
								@foreach($grupo->clientes as $c)
								<tr class="datatable-row" >
									<td class="datatable-cell"><span class="codigo" style="width: 250px;" id="id">{{$c->razao_social}}</span>
									</td>
									<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{$c->cpf_cnpj}}</span>
									</td>
									<td class="datatable-cell">
										<span class="codigo" style="width: 550px;" id="id">{{$c->rua}}, {{$c->numero}} - {{$c->bairro}}
										</span>
									</td>
									<td class="datatable-cell">
										<span class="codigo" style="width: 150px;" id="id">
											{{$c->cidade->nome}} ({{$c->cidade->uf}})
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

@endsection