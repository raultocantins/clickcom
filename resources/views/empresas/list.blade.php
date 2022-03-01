@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<input type="hidden" id="_token" value="{{ csrf_token() }}">
			<form class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft" method="get" action="/empresas/filtro">
				<div class="row align-items-center">

					<div class="form-group col-lg-4 col-md-6 col-sm-6">
						<label class="col-form-label">Nome</label>
						<div class="">
							<div class="input-group date">
								<input type="text" name="nome" class="form-control" value="{{{isset($nome) ? $nome : ''}}}" />
								
							</div>
						</div>
					</div>

					<div class="form-group col-lg-3 col-md-3 col-sm-3">
						<label class="col-form-label">Estado</label>
						<div class="">
							<select name="status" class="custom-select">
								<option @isset($status) @if($status == 'TODOS') selected @endif @endisset value="TODOS">TODOS</option>
								<option @isset($status) @if($status == 1) selected @endif @endisset value="1">ATIVO</option>
								<option @isset($status) @if($status == 2) selected @endif @endisset value="2">PENDENTE</option>
								<option @isset($status) @if($status == 0) selected @endif @endisset value="0">DESATIVADO</option>
							</select>
						</div>
					</div>
					<div class="col-lg-2 col-xl-2 mt-2 mt-lg-0">
						<button style="margin-top: 15px;" class="btn btn-light-primary px-6 font-weight-bold">Pesquisa</button>
					</div>
				</div>
			</form>

			<h4 class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Lista de Empresas</h4>

			<label class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Registros: <strong class="text-success">{{sizeof($empresas)}}</strong></label>
			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
				<div class="row">

					<a href="/empresas/nova" class="btn btn-success">
						<i class="la la-plus"></i>
						Nova Empresa
					</a>

					@isset($paraImprimir)
					<form method="post" action="/empresas/relatorio">
						@csrf
						<input type="hidden" name="nome" value="{{{ isset($nome) ? $nome : '' }}}">
						<input type="hidden" name="status" value="{{{ isset($status) ? $status : '' }}}">
						<button style="margin-left: 5px;" class="btn btn-lg btn-info">
							<i class="fa fa-print"></i>Imprimir relatório
						</button>
					</form>
					@endisset

				</div>
			</div>

			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

					<table class="datatable-table" style="max-width: 100%; overflow: scroll">
						<thead class="datatable-head">
							<tr class="datatable-row" style="left: 0px;">
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 70px;">#</span></th>
								

								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Data cadastro</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Nome</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Telefone</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Cidade</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Plano</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Status</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Representante</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Ultimo login</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">Dias para expirar</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">Ações</span></th>
							</tr>
						</thead>

						<tbody class="datatable-body">
							@foreach($empresas as $e)

							<tr class="datatable-row">
								<td class="datatable-cell">
									<span class="codigo" style="width: 70px;">
										{{$e->id}}
									</span>
								</td>
								<td class="datatable-cell">
									<span class="codigo" style="width: 120px;">
										{{ \Carbon\Carbon::parse($e->created_at)->format('d/m/Y H:i') }}
									</span>
								</td>
								<td class="datatable-cell">
									<span class="codigo" style="width: 150px;">
										{{$e->nome}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 120px;">
										{{$e->telefone}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">
										{{$e->cidade}}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">
										@if($e->planoEmpresa)
										{{$e->planoEmpresa->plano->nome}}
										@else
										--
										@endif
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 100px;">
										@if($e->status() == -1)
										<span class="label label-xl label-inline label-light-info">
											MASTER
										</span>

										@elseif($e->status() && $e->tempo_expira >= 0)
										<span class="label label-xl label-inline label-light-success">
											ATIVO
										</span>
										@else

										@if(!$e->planoEmpresa)
										<span class="label label-xl label-inline label-light-danger">
											DESATIVADO
										</span>
										@else
										@if($e->planoEmpresa->expiracao == '0000-00-00')
										<span class="label label-xl label-inline label-light-success">
											ATIVO
										</span>
										@else
										<span class="label label-xl label-inline label-light-danger">
											DESATIVADO
										</span>
										@endif
										@endif
										@endif

									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 120px;">
										@if($e->tipo_representante)
										<span class="label label-xl label-inline label-light-success">
											SIM
										</span>

										@else
										<span class="label label-xl label-inline label-light-info">
											NÃO
										</span>
										@endif
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 120px;">

										@if($e->ultimoLogin($e->id))
										{{ 
											\Carbon\Carbon::parse(
											$e->ultimoLogin($e->id)->created_at)->format('d/m/Y H:i')
										}}
										@else
										--
										@endif
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 150px;">
										@if($e->tempo_expira)

										@if($e->planoEmpresa->expiracao == '0000-00-00')
										<span class="text-info">Indeterminado</span>
										@else
										@if($e->tempo_expira < 0)
										<span class="text-danger">Vencido</span>

										@elseif($e->tempo_expira >= 0 && $e->tempo_expira < 5)
										<span class="text-warning">{{$e->tempo_expira}}</span>

										@else
										<span class="text-dark">{{$e->tempo_expira}}</span>
										@endif
										@endif

										@else
										--
										@endif
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 280px;">
										<a href="/empresas/detalhes/{{$e->id}}" class="btn btn-sm btn-primary">
											Detalhes
										</a>

										@if(!$e->isMaster())
										<a onclick='swal("Atenção!", "Deseja remover esta empresa?", "warning").then((sim) => {if(sim){ location.href="/empresas/verDelete/{{ $e->id }}" }else{return false} })' href="#!"  class="btn btn-sm btn-danger">
											Remover
										</a>
										@endif

										@if($e->status)
										<a href="/empresas/alterarStatus/{{$e->id}}" class="btn btn-sm btn-warning">
											Bloquear
										</a>
										@else
										<a href="/empresas/alterarStatus/{{$e->id}}" class="btn btn-sm btn-success">
											Desbloquear
										</a>
										@endif
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

@endsection	
