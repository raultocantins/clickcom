@extends('default.layout')
@section('content')

<div class="row" id="anime" style="display: none">
	<div class="col s8 offset-s2">
		<lottie-player 
		src="/anime/success.json"  background="transparent"  speed="0.8"  style="width: 100%; height: 300px;" autoplay >
	</lottie-player>
</div>
</div>

<div class="card card-custom gutter-b">
	<div class="card-body">
		<div class="card card-custom gutter-b">
			<div class="card-body">
				<h1 class="center-align">Visualizando Devolução</h1>
				<h4 class="center-align">Nota Fiscal de Entrada <strong class="grey-text">{{$dadosNf['nNf']}}</strong></h4>
				<h4 class="center-align">Chave de Entrada <strong class="grey-text">{{$dadosNf['chave']}}</strong></h4>

				@if($devolucao->chave_gerada)
				<h4 class="center-align">Chave de Devolução <strong class="grey-text">{{$devolucao->chave_gerada}}</strong></h4>
				@endif
			</div>
		</div>
		

		<div class="card card-custom gutter-b">
			<div class="card-body">
				<div class="row">
					<div class="col s8">
						<h5>Fornecedor: <strong>{{$dadosEmitente['razaoSocial']}}</strong></h5>
						<h5>Nome Fantasia: <strong>{{$dadosEmitente['nomeFantasia']}}</strong></h5>
					</div>
					<div class="col s4">
						<h5>CNPJ: <strong>{{$dadosEmitente['cnpj']}}</strong></h5>
						<h5>IE: <strong>{{$dadosEmitente['ie']}}</strong></h5>
					</div>
				</div>
				<div class="row">
					<div class="col s8">
						<h5>Logradouro: <strong>{{$dadosEmitente['logradouro']}}</strong></h5>
						<h5>Numero: <strong>{{$dadosEmitente['numero']}}</strong></h5>
						<h5>Bairro: <strong>{{$dadosEmitente['bairro']}}</strong></h5>
					</div>
					<div class="col s4">
						<h5>CEP: <strong>{{$dadosEmitente['cep']}}</strong></h5>
						<h5>Fone: <strong>{{$dadosEmitente['fone']}}</strong></h5>
					</div>
				</div>
				
			</div>
		</div>
		

		<div class="card card-custom gutter-b">
			<div class="card-body">
				<div class="col s12">
					<h4>Itens da NF</h4>
					<!-- <p class="text-danger">* Produtos em vermelho ainda não cadastrado no sistma</p> -->
					<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">
						<table class="datatable-table" style="max-width: 100%; overflow: scroll">
							<thead class="datatable-head">
								<tr class="datatable-row" style="left: 0px;">

									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Código</span></th>
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">Produto</span></th>
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">NCM</span></th>
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">CFOP</span></th>
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Cod Barra</span></th>
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Un. Compra</span></th>
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Valor</span></th>
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Quantidade</span></th>
									<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Subtotal</span></th>

								</tr>
							</thead>

							<tbody id="body" class="datatable-body">

								@foreach($devolucao->itens as $i)
								<tr class="datatable-row">

									<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{$i->cod}}</span>
									</td>
									<td class="datatable-cell"><span class="codigo" style="width: 200px;">{{$i->nome}}</span>
									</td>
									<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{$i->ncm}}</span>
									</td>
									<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{$i->cfop}}</span>
									</td>
									<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{$i->codBarras}}</span>
									</td>
									<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{$i->unidade_medida}}</span>
									</td>

									<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{$i->valor_unit}}</span>
									</td>
									<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{$i->quantidade}}</span>
									</td>
									<td class="datatable-cell"><span class="codigo" style="width: 100px;">{{number_format(($i->quantidade * $i->valor_unit), 2)}}</span>
									</td>
									
								</tr>
								@endforeach
							</tbody>
						</table>

					</div>
				</div>
				
			</div>
		</div>


		<div class="card card-custom gutter-b">
			<div class="card-body">
				<div class="row">
					<div class="col s6">
						<h4>Valor Integral da NF: <strong id="valorDaNF" class="blue-text">R$ {{$dadosNf['vProd']}}</strong></h4>
					</div>

					<div class="col s6">
						<h4>Valor Devolvido: <strong class="red-text">R$ {{$devolucao->valor_devolvido}}</strong></h4>
					</div>

				</div>

				<div class="row">
					<div class="col s4">
						<a style="width: 100%;" href="/devolucao/downloadXmlEntrada/{{$devolucao->id}}" class="btn btn-danger" target="_blank">
							Downlaod XML de entrada
						</a>
					</div>

					<div class="col s4">
						<a style="width: 100%;" href="/devolucao/downloadXmlDevolucao/{{$devolucao->id}}" class="btn btn-info" target="_blank">
							Downlaod XML de devolução
						</a>
					</div>
				</div>

				<br>

			</div>
		</div>
	</div>

	
</div>
@endsection	