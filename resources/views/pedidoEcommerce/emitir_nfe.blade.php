@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">
			<form method="post" action="/pedidosEcommerce/salvarVenda">
				@csrf
				<input type="hidden" value="{{$pedido->id}}" name="id">
				<div class="row justify-content-center py-8 px-8 py-md-27 px-md-0">
					<div class="col-md-10">
						<div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
							<h1 class="display-4 font-weight-boldest mb-10">EMITIR NFe</h1>
							<div class="d-flex flex-column align-items-md-end px-0">
								<!--begin::Logo-->
								<a href="#" class="mb-5">
									<img src="/metronic/theme/html/demo1/dist/assets/media/logos/logo-dark.png" alt="">
								</a>

							</div>
						</div>
						<div class="border-bottom w-100">
							<h2>Cliente</h2>
						</div>
						<div class="d-flex justify-content-between pt-6">
							<div class="d-flex flex-column flex-root">
								<span class="font-weight-bolder mb-2">Nome</span>
								<span class="opacity-70">
									{{ $pedido->cliente->nome }} 
									{{ $pedido->cliente->sobre_nome }}
								</span>
							</div>
							<div class="d-flex flex-column flex-root">
								<span class="font-weight-bolder mb-2">Doc.</span>
								<span class="opacity-70">
									{{$pedido->cliente->cpf}}
								</span>
							</div>

							@if($pedido->cliente->ie != "")
							<div class="d-flex flex-column flex-root">
								<span class="font-weight-bolder mb-2">IE</span>
								<span class="opacity-70">
									{{$pedido->cliente->ie}}
								</span>
							</div>
							@endif

							<div class="d-flex flex-column flex-root">
								<span class="font-weight-bolder mb-2">Telefone</span>
								<span class="opacity-70">
									{{$pedido->cliente->telefone}}
								</span>
							</div>

							<div class="d-flex flex-column flex-root">
								<span class="font-weight-bolder mb-2">Email</span>
								<span class="opacity-70">
									{{$pedido->cliente->email}}
								</span>
							</div>

							<div class="d-flex flex-column flex-root">
								<span class="font-weight-bolder mb-2">-</span>
								<span class="opacity-70">
									<a style="margin-left: 10px;" href="/clienteEcommerce/edit/{{$pedido->cliente->id}}">
										<i class="la la-edit text-info"></i>
									</a>
								</span>
							</div>
						</div>

						<br>

						<div class="border-bottom w-100">
							<h2>Endereço</h2>
						</div>
						<div class="d-flex justify-content-between pt-6">
							<div class="d-flex flex-column flex-root">
								<span class="font-weight-bolder mb-2">Rua</span>
								<span class="opacity-70">
									{{ $pedido->endereco->rua }} 
								</span>
							</div>
							<div class="d-flex flex-column flex-root">
								<span class="font-weight-bolder mb-2">Número</span>
								<span class="opacity-70">
									{{$pedido->endereco->numero}}
								</span>
							</div>

							<div class="d-flex flex-column flex-root">
								<span class="font-weight-bolder mb-2">Bairro</span>
								<span class="opacity-70">
									{{$pedido->endereco->bairro}}
								</span>
							</div>

							<div class="d-flex flex-column flex-root">
								<span class="font-weight-bolder mb-2">Cidade</span>
								<span class="opacity-70">
									{{$pedido->endereco->cidade}}
								</span>
							</div>

							<div class="d-flex flex-column flex-root">
								<span class="font-weight-bolder mb-2">UF</span>
								<span class="opacity-70">
									{{$pedido->endereco->uf}}
								</span>
							</div>

							<div class="d-flex flex-column flex-root">
								<span class="font-weight-bolder mb-2">CEP</span>
								<span class="opacity-70">
									{{$pedido->endereco->cep}}
								</span>
							</div>

							<div class="d-flex flex-column flex-root">
								<span class="font-weight-bolder mb-2">-</span>
								<span class="opacity-70">
									<a style="margin-left: 10px;" href="/enderecosEcommerce/edit/{{$pedido->endereco->id}}">
										<i class="la la-edit text-info"></i>
									</a>
									<a style="margin-left: 5px;" onclick="buscaCep('{{$pedido->endereco->cep}}')">
										<i class="la la-search text-danger"></i>
									</a>
								</span>
							</div>
						</div>

					</div>
				</div>

				<div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0 mx-0">
					<div class="col-md-10">

						<div class="row">
							<div class="form-group col-lg-6 col-md-6 col-sm-6">
								<label class="col-form-label">Natureza de Operação</label>
								<div class="">
									<div class="input-group date">
										<select class="custom-select form-control" id="natureza" name="natureza">
											@foreach($naturezas as $n)
											<option 
											value="{{$n->id}}">{{$n->natureza}}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group col-lg-6 col-md-6 col-sm-6">
								<label class="col-form-label">Transportadora</label>
								<div class="">
									<div class="input-group date">
										<select class="custom-select form-control" id="natureza" name="transportadora">
											<option value="">--</option>
											@foreach($transportadoras as $t)
											<option 
											value="{{$t->id}}">{{$t->razao_social}} - {{$t->cnpj_cpf}}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>

							<div class="form-group validated col-sm-4 col-lg-4 col-8">
								<label class="col-form-label" id="">Tipo</label>
								<select class="custom-select form-control" id="frete" name="frete">
									<option value="0">0 - Emitente</option>
									<option value="1">1 - Destinatário</option>
									<option value="2">2 - Terceiros</option>
									<option value="9">9 - Sem Frete</option>
								</select>
							</div>
							<div class="form-group col-lg-2 col-md-4 col-sm-6 col-6">
								<label class="col-form-label">Valor do frete</label>
								<div class="">
									<div class="input-group">
										<input type="text" value="{{$pedido->valor_frete}}" name="valor_frete" class="form-control" value="" id="valor_frete"/>
									</div>
								</div>
							</div>
							<div class="form-group col-lg-2 col-md-2 col-sm-6 col-6">
								<label class="col-form-label">Placa Veiculo</label>
								<div class="">
									<div class="input-group">
										<input type="text" name="placa" class="form-control" value="" id="placa"/>
									</div>
								</div>
							</div>

							<div class="form-group validated col-sm-2 col-lg-2 col-6">
								<label class="col-form-label" id="">UF</label>
								<select class="custom-select form-control" id="uf_placa" name="uf_placa">
									<option value="">--</option>
									<option value="AC">AC</option>
									<option value="AL">AL</option>
									<option value="AM">AM</option>
									<option value="AP">AP</option>
									<option value="BA">BA</option>
									<option value="CE">CE</option>
									<option value="DF">DF</option>
									<option value="ES">ES</option>
									<option value="GO">GO</option>
									<option value="MA">MA</option>
									<option value="MG">MG</option>
									<option value="MS">MS</option>
									<option value="MT">MT</option>
									<option value="PA">PA</option>
									<option value="PB">PB</option>
									<option value="PE">PE</option>
									<option value="PI">PI</option>
									<option value="PR">PR</option>
									<option value="RJ">RJ</option>
									<option value="RN">RN</option>
									<option value="RS">RS</option>
									<option value="RO">RO</option>
									<option value="RR">RR</option>
									<option value="SC">SC</option>
									<option value="SE">SE</option>
									<option value="SP">SP</option>
									<option value="TO">TO</option>
								</select>
							</div>

							<div class="form-group col-lg-2 col-md-2 col-sm-3 col-6">
								<label class="col-form-label">Qtd Volumes</label>
								<div class="">
									<div class="input-group">
										<input type="text" name="qtd_volumes" class="form-control" value="1" id="qtd_volumes"/>
									</div>
								</div>
							</div>

							<div class="form-group col-lg-2 col-md-2 col-sm-3 col-6">
								<label class="col-form-label">Num. Volumes</label>
								<div class="">
									<div class="input-group">
										<input type="text" name="numeracao_volumes" class="form-control" value="1" id="numeracao_volumes"/>
									</div>
								</div>
							</div>

							<div class="form-group col-lg-3 col-md-3 col-sm-3 col-6">
								<label class="col-form-label">Espécie</label>
								<div class="">
									<div class="input-group">
										<input type="text" name="especie" class="form-control" value="" id="especie"/>
									</div>
								</div>
							</div>

							<div class="form-group col-lg-2 col-md-2 col-sm-3 col-6">
								<label class="col-form-label">Peso liquído</label>
								<div class="">
									<div class="input-group">
										<input data-mask="00000,0000" data-mask-reverse="true" type="text" name="peso_liquido" class="form-control" value="{{number_format($pedido->somaPeso(), 3)}}" id="peso_liquido"/>
									</div>
								</div>
							</div>

							<div class="form-group col-lg-2 col-md-2 col-sm-3 col-6">
								<label class="col-form-label">Peso bruto</label>
								<div class="">
									<div class="input-group">
										<input data-mask="00000,0000" data-mask-reverse="true" type="text" name="peso_bruto" class="form-control" value="{{number_format($pedido->somaPeso(), 3)}}" id="peso_bruto"/>
									</div>
								</div>
							</div>

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
										<th class="font-weight-bold text-muted text-uppercase">FRETE</th>
										<th class="font-weight-bold text-muted text-uppercase text-right">TOTAL</th>
									</tr>
								</thead>
								<tbody>
									<tr class="font-weight-bolder">
										<td>{{$pedido->forma_pagamento}}</td>
										<td>
											@if($pedido->status == 1)
											<span class="text-warning">PENDENTE</span>
											@elseif($pedido->status == 2)
											<span class="text-success">APROVADO</span>
											@elseif($pedido->status == 3)
											<span class="text-danger">CANCELANDO</span>
											@endif
										</td>

										<td>R$ {{ number_format($pedido->valor_frete, 2, ',', '.')}}</td>

										<td class="text-primary font-size-h3 font-weight-boldest text-right">R$ {{ number_format($pedido->valor_total, 2, ',', '.')}}</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<br>

				@if(sizeof($erros) == 0)
				<button class="btn btn-success">
					<i class="la la-check"></i>
					Salvar
				</button>

				@else
				@foreach($erros as $e)
				<p>
					<span class="label label-xl label-inline label-light-danger">
						{{$e}}
					</span>
				</p>
				@endforeach
				@endif
			</form>
		</div>
	</div>
</div>

@section('javascript')
<script type="text/javascript">
	function buscaCep(cep){
		cep = cep.replace("-", "")

		$.get('https://viacep.com.br/ws/'+cep+'/json')
		.done((res) => {
			console.log(res)
			let html = "Cidade: "+ res.localidade +" ("+res.uf+")\n" 
			html += "Rua: "+ res.logradouro +"\n" 
			html += "Bairro: "+ res.bairro +"\n" 
			html += "DDD: "+ res.ddd +"\n" 
			html += "Ibge: "+ res.ibge +"\n" 

			swal("Sucesso", html, "success")
		})
		.fail((err) => {
			console.log(err)
			swal("Erro", "Erro ao encontrar CEP", "error")

		})
	}
</script>
@endsection
@endsection