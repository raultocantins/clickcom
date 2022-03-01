@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<div class="row justify-content-center py-8 px-8 py-md-27 px-md-0">
				<div class="col-md-10">
					<div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
						<h1 class="display-4 font-weight-boldest mb-10">DETALHES DO PEDIDO</h1>
						<div class="d-flex flex-column align-items-md-end px-0">
							<!--begin::Logo-->
							<a href="#" class="mb-5">
								<img src="/metronic/theme/html/demo1/dist/assets/media/logos/logo-dark.png" alt="">
							</a>
							<!--end::Logo-->
							<span class="d-flex flex-column align-items-md-end opacity-70">
								<span>Transação ID Mercado Pago: <strong class="text-info">{{$pedido->transacao_id}}</strong></span>

								@if($pedido->link_boleto != "")
								<a target="_blank" class="btn btn-primary" href="{{$pedido->link_boleto}}">
									<i class="la la-print"></i>
									Imprimir Boleto
								</a>
								@endif

							</span>
						</div>
					</div>
					<div class="border-bottom w-100"></div>
					<div class="d-flex justify-content-between pt-6">
						<div class="d-flex flex-column flex-root">
							<span class="font-weight-bolder mb-2">DATA</span>
							<span class="opacity-70">
								{{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y H:i:s') }}
							</span>
						</div>
						<div class="d-flex flex-column flex-root">
							<span class="font-weight-bolder mb-2">Cliente</span>
							<span class="opacity-70">
								{{$pedido->cliente->nome}} {{$pedido->cliente->sobre_nome}}
							</span>
						</div>
						<div class="d-flex flex-column flex-root">
							<span class="font-weight-bolder mb-2">Endereço</span>
							<span class="opacity-70">
								{{$pedido->endereco->rua}}, {{$pedido->endereco->numero}} - {{$pedido->endereco->bairro}} - {{$pedido->endereco->complemento}}
							</span>
							<span class="opacity-70">
								{{$pedido->endereco->cidade}} ({{$pedido->endereco->uf}}) | {{$pedido->endereco->cep}}
							</span>
						</div>
						@if($pedido->observacao != "")
						<div class="d-flex flex-column flex-root">
							<span class="font-weight-bolder mb-2">Observação</span>
							<span class="opacity-70">
								{{$pedido->observacao}}
							</span>
						</div>
						@endif

						@if($pedido->codigo_rastreio != "")
						<div class="d-flex flex-column flex-root">
							<span class="font-weight-bolder mb-2">Código de reastreio</span>
							<span class="opacity-70">
								{{$pedido->codigo_rastreio}}
							</span>
						</div>
						@endif
					</div>
				</div>
			</div>

			<div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
				<div class="col-md-10">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th class="pl-0 font-weight-bold text-muted text-uppercase">Produto</th>
									<th class="text-right font-weight-bold text-muted text-uppercase">Quantidade</th>
									<th class="text-right font-weight-bold text-muted text-uppercase">Valor unitário</th>
									<th class="text-right pr-0 font-weight-bold text-muted text-uppercase">Total</th>
								</tr>
							</thead>
							<tbody>
								
								@foreach($pedido->itens as $i)

								<tr class="font-weight-boldest border-bottom-0">
									<td class="border-top-0 pl-0 py-4 d-flex align-items-center">
										<!--begin::Symbol-->
										<div class="symbol symbol-40 flex-shrink-0 mr-4 bg-light">
											<div class="symbol-label" style="background-image: url('/ecommerce/produtos/{{$i->produto->galeria[0]->img}}')"></div>
										</div>
										<!--end::Symbol-->

										{{$i->produto->produto->nome}} 
										@if($i->produto->produto->grade)
										({{$i->produto->produto->str_grade}})
										@endif
									</td>
									<td class="border-top-0 text-right py-4 align-middle">
										{{$i->quantidade}}
									</td>
									<td class="border-top-0 text-right py-4 align-middle">R$ {{ number_format($i->produto->valor, 2, ',', '.') }}</td>
									<td class="text-primary border-top-0 pr-0 py-4 text-right align-middle">R$ {{ number_format($i->quantidade*$i->produto->valor, 2, ',', '.') }}</td>
								</tr>

								@endforeach

							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0 mx-0">
				<div class="col-md-10">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th class="font-weight-bold text-muted text-uppercase">FORMA DE PAGAMENTO</th>
									<th class="font-weight-bold text-muted text-uppercase">PAGAMENTO STATUS</th>
									<th class="font-weight-bold text-muted text-uppercase"> STATUS PREPARAÇÃO</th>
									<th class="font-weight-bold text-muted text-uppercase">FRETE</th>
									<th class="font-weight-bold text-muted text-uppercase text-right">TOTAL</th>
								</tr>
							</thead>
							<tbody>
								<tr class="font-weight-bolder">
									<td>{{$pedido->forma_pagamento}}</td>
									<td>
										@if($pedido->status_pagamento ==  'pending')
										<span class="text-warning">PENDENTE</span>
										@elseif($pedido->status_pagamento == 'approved')
										<span class="text-success">APROVADO</span>
										@else
										<span class="text-danger">CANCELANDO/REJEITADO</span>
										@endif
									</td>
									<td class="datatable-cell">
										<span class="codigo" style="width: 100px;" id="id">
											@if($pedido->status_preparacao == 0)

											<span class="label label-xl label-inline label-light-info">Novo</span>
											@elseif($pedido->status_preparacao == 1)
											<span class="label label-xl label-inline label-light-primary">Aprovado</span>
											@elseif($pedido->status_preparacao == 2)
											<span class="label label-xl label-inline label-light-danger">Cancelado</span>
											@elseif($pedido->status_preparacao == 3)
											<span class="label label-xl label-inline label-light-warning">Aguardando Envio</span>
											@elseif($pedido->status_preparacao == 4)
											<span class="label label-xl label-inline label-light-dark">Enviado</span>
											@else
											<span class="label label-xl label-inline label-light-success">Entregue</span>
											@endif

											<a style="margin-left: 10px;" data-toggle="modal" data-target="#modal-status">
												<i class="la la-refresh text-danger"></i>
											</a>
										</span>
									</td>

									<td>R$ {{ number_format($pedido->valor_frete, 2, ',', '.')}} - {{strtoupper($pedido->tipo_frete)}}</td>

									<td class="text-primary font-size-h3 font-weight-boldest text-right">R$ {{ number_format($pedido->valor_total, 2, ',', '.')}}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<br>

			@if(!$pedido->venda)
			<a class="btn btn-info" href="/pedidosEcommerce/gerarNFe/{{$pedido->id}}">
				<i class="la la-file"></i>
				Gerar NF-e
			</a>
			@else

			@if($pedido->numero_nfe > 0)
			<a class="btn btn-light-success" target="_blank" href="/nf/imprimir/{{$pedido->venda->id}}">
				<i class="la la-print"></i>
				Imprimir Danfe
			</a>
			@endif
			<a class="btn btn-light-info" href="/vendas/detalhar/{{$pedido->venda->id}}">
				<i class="la la-file-alt"></i>
				Ver Venda
			</a>

			@endif

			<a target="_blank" class="btn btn-light-primary" href="/pedidosEcommerce/imprimir/{{$pedido->id}}">
				<i class="la la-print"></i>
				Imprimir Pedido
			</a>

		</div>
	</div>
</div>

<div class="modal fade" id="modal-status" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form method="post" action="/pedidosEcommerce/alterarStatus">
				@csrf
				<div class="modal-header">
					<h5 class="modal-title">ALTERAÇÃO DE STATUS PREPARAÇÃO</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						x
					</button>
				</div>
				<div class="modal-body">
					<input type="hidden" value="{{$pedido->id}}" name="id">

					<div class="row">
						<div class="form-group col-lg-4 col-md-4 col-sm-6">
							<label class="col-form-label">Estado</label>
							<div class="">
								<div class="input-group date">
									<select class="custom-select form-control" id="status_preparacao" name="status_preparacao">
										<option @if($pedido->status_preparacao == 0) selected @endif value="0">NOVO</option>
										<option @if($pedido->status_preparacao == 1) selected @endif value="1">APROVADO</option>
										<option @if($pedido->status_preparacao == 2) selected @endif value="2">CANCELADO</option>
										<option @if($pedido->status_preparacao == 3) selected @endif value="3">AGUARDANDO ENVIO</option>
										<option @if($pedido->status_preparacao == 4) selected @endif value="4">ENVIADO</option>
										<option @if($pedido->status_preparacao == 5) selected @endif value="5">ENTREGUE</option>

									</select>
								</div>
							</div>
						</div>
						<div class="form-group col-lg-4 col-md-6 col-sm-6">
							<label class="col-form-label">Código reastreio</label>
							<div class="">
								<div class="input-group date">
									<input class="form-control" placeholder="Código reastreio" type="" name="codigo_rastreio">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group validated col-sm-12 col-lg-12">
							<label class="col-form-label" id="">Observação</label>
							<input type="hidden" id="id_correcao" name="">
							<div class="">
								<input type="text" id="observacao" placeholder="Observação" name="observacao" class="form-control" value="">
							</div>
						</div>
					</div>
					<input type="hidden" id="venda_id">


				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light-danger font-weight-bold" data-dismiss="modal">Fechar</button>
					<button type="submit" id="btn-send" class="btn btn-light-success font-weight-bold spinner-white spinner-right">Salvar</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection