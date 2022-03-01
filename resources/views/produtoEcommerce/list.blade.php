@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">
	<div class="card-body">
		<div class="">

			<div class="col-sm-12 col-lg-4 col-md-6 col-xl-4">

				<a href="/produtoEcommerce/new" class="btn btn-lg btn-success">
					<i class="fa fa-plus"></i>Nova Produto de Ecommerce
				</a>
			</div>
		</div>
		<br>
		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">
			<br>
			<h4>Lista de Produtos de Ecommerce</h4>
			<label>Numero de registros: <strong class="text-info">{{sizeof($produtos)}}</strong></label>					

			<form method="get" action="/produtoEcommerce/pesquisa">
				<div class="row align-items-center">
					<div class="col-lg-5 col-xl-5">
						<div class="row align-items-center">
							<div class="col-md-12 my-2 my-md-0">
								<label>Produto</label>

								<div class="input-icon">

									<input type="text" name="pesquisa" class="form-control" value="{{{isset($pesquisa) ? $pesquisa : ''}}}"
									placeholder="Produto..." id="kt_datatable_search_query">
									<span>
										<i class="fa fa-search"></i>
									</span>
								</div>
							</div>

						</div>
					</div>

					<div class="col-lg-3 col-xl-3">
						<div class="row align-items-center">
							<div class="col-md-12 my-2 my-md-0">
								<label>Categoria</label>
								<div class="input-icon">
									<select name="categoria_id" class="custom-select">
										<option value="">--</option>
										@foreach($categorias as $c)
										<option @if(isset($categoria_id)) @if($c->id == $categoria_id)
										selected
										@endif
										@endif value="{{$c->id}}">{{$c->nome}}</option>
										@endforeach
									</select>
									
								</div>
							</div>

						</div>
					</div>
					<div class="col-lg-2 col-xl-2 mt-2 mt-lg-0">
						<button style="margin-top: 23px;" type="submit" class="btn btn-light-primary px-6 font-weight-bold">Buscar</button>
					</div>
				</div>
				<br>
			</form>

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

				<div class="pb-5" data-wizard-type="step-content">
					<div class="row">
						<div class="col-xl-12">

							<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

								<table class="datatable-table" style="max-width: 100%; overflow: scroll">
									<thead class="datatable-head">
										<tr class="datatable-row" style="left: 0px;">
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">NOME</span></th>
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">CATEGORIA</span></th>
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">VALOR</span></th>
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">ATIVO</span></th>
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">CONTROLE ESTOQUE</span></th>
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">DESTAQUE</span></th>
											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">GRADE</span></th>

											<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">AÇÕES</span></th>
										</tr>
									</thead>

									<tbody id="body" class="datatable-body">
										@foreach($produtos as $p)
										<tr class="datatable-row" @if($p->inativo) style="background: #ffcdd2;" @endif>
											<td class="datatable-cell"><span class="codigo" style="width: 250px;" id="id">
												{{$p->produto->nome}}</span>
											</td>
											<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">
												{{$p->categoria->nome}}</span>
											</td>
											<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">
												{{number_format($p->valor, 2, ',', '.')}}</span>
											</td>
											<td class="datatable-cell">
												<span class="codigo" style="width: 100px;" id="id">
													@if($p->status)
													<span class="label label-xl label-inline label-light-success">Ativo</span>
													@else
													<span class="label label-xl label-inline label-light-danger">Desativado</span>
													@endif
												</span>
											</td>

											<td class="datatable-cell">
												<span class="codigo" style="width: 100px;" id="id">
													@if($p->controlar_estoque)
													<span class="label label-xl label-inline label-light-success">Ativo</span>
													@else
													<span class="label label-xl label-inline label-light-danger">Desativado</span>
													@endif
												</span>
											</td>

											<td class="datatable-cell">
												<span class="codigo" style="width: 100px;" id="id">
													@if($p->destaque)
													<span class="label label-xl label-inline label-light-success">Sim</span>
													@else
													<span class="label label-xl label-inline label-light-danger">Não</span>
													@endif
												</span>
											</td>

											<td class="datatable-cell">
												<span class="codigo" style="width: 100px;" id="id">
													@if($p->produto->grade)
													<span class="label label-xl label-inline label-light-success">Sim</span>
													@else
													<span class="label label-xl label-inline label-light-danger">Não</span>
													@endif
												</span>
											</td>

											<td class="datatable-cell">
												<span class="codigo" style="width: 200px;" id="id">
													<a class="btn btn-warning" onclick='swal("Atenção!", "Deseja editar este registro?", "warning").then((sim) => {if(sim){ location.href="/produtoEcommerce/edit/{{ $p->id }}" }else{return false} })' href="#!">
														<i class="la la-edit"></i>	

													</a>
													@if(!$p->produto->grade)
													<a class="btn btn-danger" onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/produtoEcommerce/delete/{{ $p->id }}" }else{return false} })' href="#!">
														<i class="la la-trash"></i>	
													</a>

													<a class="btn btn-success"  href="/produtoEcommerce/galeria/{{$p->id}}">
														<i class="la la-photo"></i>	
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

				<div class="pb-5" data-wizard-type="step-content">

					<div class="row">

						@foreach($produtos as $p)


						<div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
							<!--begin::Card-->
							<div class="card card-custom gutter-b card-stretch">
								<!--begin::Body-->
								<div class="card-body pt-4">
									<!--begin::Toolbar-->
									<div class="d-flex justify-content-end">
										<div class="dropdown dropdown-inline" data-toggle="tooltip" title="" data-placement="left" >
											<a href="#" class="btn btn-clean btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="fa fa-ellipsis-h"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
												<!--begin::Navigation-->
												<ul class="navi navi-hover">
													<li class="navi-header font-weight-bold py-4">
														<span class="font-size-lg">Ações:</span>

													</li>
													<li class="navi-separator mb-3 opacity-70"></li>

													<li class="navi-item">
														<a href="/produtoEcommerce/edit/{{ $p->id }}" class="navi-link">
															<span class="navi-text">
																<span class="label label-xl label-inline label-light-primary">Editar</span>
															</span>
														</a>
													</li>

													@if(!$p->produto->grade)

													<li class="navi-item">
														<a onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/produtoEcommerce/delete/{{ $p->id }}" }else{return false} })' href="#!" class="navi-link">
															<span class="navi-text">
																<span class="label label-xl label-inline label-light-danger">Remover</span>
															</span>
														</a>
													</li>

													<li class="navi-item">
														<a href="/produtoEcommerce/galeria/{{ $p->id }}" class="navi-link">
															<span class="navi-text">
																<span class="label label-xl label-inline label-light-success">Galeria</span>
															</span>
														</a>
													</li>
													@endif


												</ul>
												<!--end::Navigation-->
											</div>
										</div>
									</div>
									<!--end::Toolbar-->
									<!--begin::User-->
									<div class="d-flex align-items-end mb-7">
										<!--begin::Pic-->
										<div class="d-flex align-items-center">
											<!--begin::Pic-->
											<div class="flex-shrink-0 mr-4 mt-lg-0 mt-3">
												<div class="symbol symbol-circle symbol-lg-75">
													@if(sizeof($p->galeria) > 0)
													<img src="/ecommerce/produtos/{{$p->galeria[0]->img}}" alt="image">
													@else
													<img src="imgs/no_image.png" alt="image">
													@endif
												</div>
												<div class="symbol symbol-lg-75 symbol-circle symbol-primary d-none">
													<span class="font-size-h3 font-weight-boldest">JM</span>
												</div>
											</div>
											<!--end::Pic-->
											<!--begin::Title-->
											<div class="d-flex flex-column">
												<a class="text-dark font-weight-bold text-hover-primary font-size-h4 mb-0">{{$p->produto->nome}}</a>

											</div>
											<!--end::Title-->
										</div>
										<!--end::Title-->
									</div>
									<!--end::User-->
									<!--begin::Desc-->

									<div class="mb-7">
										<div class="d-flex justify-content-between align-items-center">
											<span class="text-dark-75 font-weight-bolder mr-2">Categoria:</span>
											<a href="#" class="text-danger">{{$p->categoria->nome}}</a>
										</div>
										<div class="d-flex justify-content-between align-items-cente my-1">
											<span class="text-dark-75 font-weight-bolder mr-2">Valor:</span>
											<a href="#" class="text-danger">

												<label>R$ {{ number_format($p->valor, 2, ',', '.') }}</label>
											</a>
										</div>

										<div class="d-flex justify-content-between align-items-center">
											<span class="text-dark-75 font-weight-bolder mr-2">Total de imagens:</span>
											<span class="text-danger">{{sizeof($p->galeria)}}</span>
										</div>

										<div class="d-flex justify-content-between align-items-center">
											<span class="text-dark-75 font-weight-bolder mr-2">Grade:</span>
											
											@if($p->produto->grade)
											<span class="text-success">
												Sim
											</span>
											@else
											<span class="text-danger">
												Não
											</span>
											@endif

										</div>

										<div class="d-flex justify-content-between align-items-center">
											<span class="text-dark-75 font-weight-bolder mr-2">Ativo:</span>
											<span class="text-danger">
												<div class="switch switch-outline switch-info">
													<label class="">
														<input 
														onclick="alterarStatus({{$p->id}})" @if($p->status) checked @endif value="true" name="status" class="red-text" type="checkbox">
														<span class="lever"></span>
													</label>
												</div>
											</span>
										</div>

										<div class="d-flex justify-content-between align-items-center">
											<span class="text-dark-75 font-weight-bolder mr-2">Controle estoque:</span>
											<span class="text-danger">
												<div class="switch switch-outline switch-danger">
													<label class="">
														<input onclick="alterarControlarEstoque({{$p->id}})" @if($p->controlar_estoque) checked @endif value="true" name="controlar_estoque" class="red-text" type="checkbox">
														<span class="lever"></span>
													</label>
												</div>
											</span>
										</div>

										<div class="d-flex justify-content-between align-items-center">
											<span class="text-dark-75 font-weight-bolder mr-2">Destaque:</span>
											<span class="text-danger">
												<div class="switch switch-outline switch-success">
													<label class="">
														<input onclick="alterarDestaque({{$p->id}})" @if($p->destaque) checked @endif value="true" name="destaque" class="red-text" type="checkbox">
														<span class="lever"></span>
													</label>
												</div>
											</span>
										</div>
									</div>


								</div>
								<!--end::Body-->
							</div>
							<!--end::Card-->
						</div>

						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@section('javascript')
<script type="text/javascript">

	function alterarStatus(id){
		$.ajax
		({
			type: 'GET',
			url: path + 'produtoEcommerce/alterarStatus/'+id,
			dataType: 'json',
			success: function(e){

				console.log(e)

			}, error: function(e){
				console.log(e)
			}

		});
	}

	function alterarControlarEstoque(id){
		$.ajax
		({
			type: 'GET',
			url: path + 'produtoEcommerce/alterarControlarEstoque/'+id,
			dataType: 'json',
			success: function(e){

				console.log(e)

			}, error: function(e){
				console.log(e)
			}

		});
	}

	function alterarDestaque(id){
		$.ajax
		({
			type: 'GET',
			url: path + 'produtoEcommerce/alterarDestaque/'+id,
			dataType: 'json',
			success: function(e){

				console.log(e)

			}, error: function(e){
				console.log(e)
			}

		});
	}
</script>
@endsection
@endsection	