@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__bounce" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<h4>Total de Arquivos importados: <strong class="text-info">
				{{sizeof($data)}}
			</strong></h4>

			<form method="post" action="/vendas/importStore">
				@csrf
				<div class="row">
					<div class="col-lg-12 col-xl-12">
						<div class="form-group col-lg-3 col-md-4 col-sm-6">
							<label class="col-form-label">Local</label>
							<div class="">
								<div class="input-group date">
									<select class="custom-select form-control" name="tabela">
										<option value="vendas">VENDAS</option>
										<option @if(!$data[0]['cliente']) selected @endif value="venda_caixas">PDV</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>

				<input type="hidden" value="{{json_encode($data)}}" name="data">

				<div class="col-lg-12 col-xl-12">
					<div class="accordion accordion-toggle-arrow" id="accordionExample1">

						@foreach($data as $key => $d)
						<div class="card">
							<div class="card-header">
								<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne{{$key}}">
									<label class="checkbox checkbox-info check-sub">
										<input checked type="checkbox" name="ch_{{$d['chave']}}">
										<span></span>
									</label>
									<strong style="margin-left: 5px;" class="text-info">{{$d['chave']}}</strong> 
									<strong style="margin-left: 5px;" class="text-danger">{{ \Carbon\Carbon::parse($d['data'])->format('d/m/Y H:i:s')}}</strong>
									<i style="margin-left: 5px;" class="la la-angle-double-down"></i>
								</div>
							</div>
							<div id="collapseOne{{$key}}" class="collapse" data-parent="#accordionExample1">
								<div class="card-body">
									@if($d['cliente'])
									<div class="card card-custom gutter-b">
										<div class="card-body">
											<h3 class="card-title">Cliente</h3>

											<h5>Razão social: <strong>{{$d['cliente']['razao_social']}}</strong></h5>
											<h5>Nome fantasia: <strong>{{$d['cliente']['nome_fantasia']}}</strong></h5>
											<h5>CNPJ/CPF: <strong>{{$d['cliente']['cpf_cnpj']}}</strong></h5>
											<h5>IE/RG: <strong>{{$d['cliente']['ie_rg']}}</strong></h5>
											<h5>Endereço: <strong>{{$d['cliente']['rua']}}, {{$d['cliente']['numero']}} - {{$d['cliente']['bairro']}}</strong></h5>
										</div>
									</div>
									@endif

									<div class="card card-custom gutter-b">
										<div class="card-body">
											<h3 class="card-title">Produtos</h3>

											@foreach($d['produtos'] as $p)
											<h5>Código: <strong>{{$p['codigo']}}</strong></h5>
											<h5>Nome: <strong>{{$p['xProd']}}</strong></h5>
											<h5>CFOP: <strong>{{$p['CFOP']}}</strong></h5>
											<h5>Unidade: <strong>{{$p['uCom']}}</strong></h5>
											<h5>Valor unitário: <strong>{{number_format((float)$p['vUnCom'], 2, ',', '.')}}</strong></h5>
											<h5>Quantidade: <strong>{{$p['qCom']}}</strong></h5>
											<h5>NCM: <strong>{{$p['NCM']}}</strong></h5>
											<h5>Código de barras: <strong>{{$p['codBarras']}}</strong></h5>

											<hr>
											@endforeach
										</div>
									</div>

									<div class="card card-custom gutter-b">
										<div class="card-body">
											<h3 class="card-title">Fatura</h3>

											@foreach($d['fatura'] as $f)
											<h5>Vencimento: <strong>{{ \Carbon\Carbon::parse($f['vencimento'])->format('d/m/Y')}}</strong></h5>
											<h5>Valor: <strong>{{number_format((float)$f['valor_parcela'], 2, ',', '.')}}</strong></h5>
											<hr>
											@endforeach
										</div>
									</div>

								</div>
							</div>
						</div>
						@endforeach

					</div>
				</div><br>
				<div class="row">
					<div class="col-xl-12">

						<div class="col-lg-3">
							<button style="width: 100%;" id="salvar-venda" type="submit" class="btn btn-lg btn-success">
								<i class="la la-check"></i>
								Salvar Importação
							</button>
						</div>
					</div>
				</div>
			</form>

		</div>
	</div>
</div>

@endsection	