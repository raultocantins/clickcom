@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="/locacao/salvarItem">

					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">
								Locação

							</h3>
						</div>

					</div>
					@csrf

					<input type="hidden" id="idLocacao" name="locacao_id" value="{{$locacao->id}}">


					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">


							<div class="kt-section kt-section--first">
								<div class="kt-section__body">

									<div class="row">
										<div class="form-group validated col-lg-8 col-md-8 col-sm-10">
											<label class="col-form-label">Produto/Item</label>
											<div class="input-group">
												
												<select class="form-control select2 @if($errors->has('cliente_id')) is-invalid @endif" id="kt_select2_1" name="produto_id">
													<option value="0">Selecione</option>
													@foreach($produtos as $p)
													<option value="{{$p->id}}">{{$p->nome}}</option>
													@endforeach
												</select>
											</div>
											@if($errors->has('produto_id'))
											<div class="invalid-feedback">
												{{ $errors->first('produto_id') }}
											</div>
											@endif
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-6 col-lg-3">
											<label class="col-form-label">Valor</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('valor')) is-invalid @endif money" id="valor" name="valor" value="{{{ old('valor') }}}">
												@if($errors->has('valor'))
												<div class="invalid-feedback">
													{{ $errors->first('valor') }}
												</div>
												@endif
											</div>
										</div>


										<div class="form-group validated col-lg-9 col-md-9 col-sm-9">
											<label class="col-form-label text-left col-lg-4 col-sm-12">Observação</label>

											<input type="text" id="observacao" class="form-control @if($errors->has('observacao')) is-invalid @endif" name="observacao" value="{{{ old('observacao') }}}">
											@if($errors->has('observacao'))
											<div class="invalid-feedback">
												{{ $errors->first('observacao') }}
											</div>
											@endif
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xl-2">

						</div>

						<div class="col-lg-3 col-sm-6 col-md-4">
							<button style="width: 100%" type="submit" class="btn btn-success">
								<i class="la la-check"></i>
								<span class="">Adicionar</span>
							</button>
						</div>

					</div>


					<br>
				</form>
			</div>

			<div class="col-lg-12">
				<!--begin::Portlet-->

				<div class="card card-custom gutter-b example example-compact">
					<div class="card-header">

						<h3 class="card-title">
							ITENS
						</h3>
					</div>

					<div class="row">
						<div class="col-xl-1"></div>
						<div class="col-xl-10">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">

									<div class="col-xl-12">

										<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

											<table class="datatable-table" style="max-width: 100%; overflow: scroll">

												<thead class="datatable-head">
													<tr class="datatable-row" style="left: 0px;">
														<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 350px;">PRODUTO</span></th>
														<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">VALOR</span></th>
														<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">OBSERVAÇÃO</span></th>
														<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">AÇÕES</span></th>
													</tr>
												</thead>

												<tbody id="body" class="datatable-body">
													@foreach($locacao->itens as $i)
													<tr class="datatable-row">
														<td class="datatable-cell"><span class="codigo" style="width: 350px;" id="id">{{$i->produto->nome}}</span>
														</td>
														<td class="datatable-cell"><span class="codigo" style="width: 150px;" id="id">{{ number_format($i->valor, 2, ',', '.')}}</span>
														</td>
														<td class="datatable-cell"><span class="codigo" style="width: 250px;" id="id">{{$i->observacao}}</span>
														</td>
														<td class="datatable-cell">
															<span class="codigo" style="width: 100px;" id="id">
																<a class="btn btn-danger" href="/locacao/deleteItem/{{$i->id}}">
																	<i class="la la-trash"></i>
																</a>
															</span>
														</td>
													</tr>
													@endforeach
												</tbody>
											</table>
											<br>
										</div>
									</div>
								</div>
								<h4>Data de início: <strong class="text-info">{{\Carbon\Carbon::parse($locacao->inicio)->format('d/m/Y')}}</strong></h4>
								<h4>Data de término: <strong class="text-info">
									@if($locacao->fim != '1969-12-31')
									{{ \Carbon\Carbon::parse($locacao->fim)->format('d/m/Y')}}
									@else
									--
									@endif
								</strong></h4>
								<h4>Cliente: <strong class="text-info">{{$locacao->cliente->razao_social}}</strong></h4>
								<h4>Total: R$ <strong class="text-success">{{number_format($locacao->total, 2, ',', '.')}}</strong></h4>
								<h4>Observação: <strong class="text-success">{{$locacao->observacao}}</strong> <a data-toggle="modal" data-target="#modal1"><i class="la la-edit text-warning"></i></a></h4>

								@if($locacao->status == 0)
								<a href="/locacao/alterarStatus/{{$locacao->id}}" class="btn btn-info">
									<i class="la la-check"></i>
									Alterar para finalizado
								</a>
								@endif

								<a target="_blank" href="/locacao/comprovante/{{$locacao->id}}" class="btn btn-success">
									<i class="la la-print"></i>
									Comprovante
								</a>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal1" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Observação da locação</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<form method="post" action="/locacao/saveObs">
				@csrf
				<input type="hidden" value="{{$locacao->id}}" name="id">
				<div class="modal-body">
					<div class="row">

						<div class="form-group validated col-sm-12 col-lg-12">
							<label class="col-form-label" id="">Observação</label>
							<div class="">
								<textarea name="observacao" class="form-control">{{$locacao->observacao}}</textarea>
							</div>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light-danger font-weight-bold" data-dismiss="modal">Fechar</button>
					<button type="submit" id="btn-cancelar-2" class="btn btn-light-success font-weight-bold spinner-white spinner-right">Salvar</button>
				</div>
			</form>
		</div>
	</div>
</div>

@section('javascript')
<script type="text/javascript">
	$('#kt_select2_1').change(() => {
		let p = $('#kt_select2_1').val()
		if(p){
			let idLocacao = $('#idLocacao').val()

			$.get(path + 'locacao/validaEstoque/'+p+'/'+idLocacao)
			.done((res) => {
				console.log(res)
				if(res.quantidade <= 0){
					swal("Erro", "Produto sem estoque", "error")
					$('#kt_select2_1').val('').change()
					$('#valor').val('')
				}else{
					$('#valor').val(parseFloat(res.valor_locacao).toFixed(casas_decimais))
				}

			})
			.fail((err) => {
				console.log(err)
				swal('Erro', 'Algo deu errado', 'error')
			})
		}
	})
</script>
@endsection
@endsection

