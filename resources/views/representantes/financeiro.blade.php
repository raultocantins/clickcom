@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">

		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			<input type="hidden" id="_token" value="{{ csrf_token() }}">
			<form class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft" method="get" action="/representantes/filtroFinanceiro">
				<div class="row align-items-center">

					<input type="hidden" value="{{$representante->id}}" name="rep_id">
					<div class="form-group col-lg-4 col-md-4 col-sm-4">
						<label class="col-form-label">Nome</label>
						<div class="">
							<div class="input-group date">
								<input type="text" name="nome" class="form-control" value="{{{isset($nome) ? $nome : ''}}}" />
								
							</div>
						</div>
					</div>

					<div class="form-group col-lg-2 col-md-3 col-sm-3">
						<label class="col-form-label">Data inicial</label>
						<div class="">
							<div class="input-group date">
								<input type="text" name="data_inicial" class="form-control date-input" value="{{{isset($data_inicial) ? $data_inicial : ''}}}" />
								
							</div>
						</div>
					</div>
					<div class="form-group col-lg-2 col-md-3 col-sm-3">
						<label class="col-form-label">Data final</label>
						<div class="">
							<div class="input-group date">
								<input type="text" name="data_final" class="form-control date-input" value="{{{isset($data_final) ? $data_final : ''}}}" />
								
							</div>
						</div>
					</div>

					<div class="form-group col-lg-2 col-md-3 col-sm-3">
						<label class="col-form-label">Estado Comissão</label>
						<div class="">
							<select name="status" class="custom-select">
								<option @isset($status) @if($status == 'TODOS') selected @endif @endisset value="TODOS">TODOS</option>
								<option @isset($status) @if($status == 1) selected @endif @endisset value="1">PAGO</option>
								<option @isset($status) @if($status == 0) selected @endif @endisset value="0">PENDENTE</option>
							</select>
						</div>
					</div>
					<div class="col-lg-2 col-xl-2 mt-2 mt-lg-0">
						<button style="margin-top: 15px;" class="btn btn-light-primary px-6 font-weight-bold">Pesquisa</button>
					</div>
				</div>
			</form>

			<h4 class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Financeiro {{$representante->nome}}</h4>

			<label class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">Registros: <strong class="text-success">{{sizeof($pagamentos)}}</strong></label>
			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
				
			</div>

			<div class="col-xl-12 @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

					<table class="datatable-table" style="max-width: 100%; overflow: scroll">
						<thead class="datatable-head">
							<tr class="datatable-row" style="left: 0px;">
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Data</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 250px;">Empresa</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Valor recebido</span></th>
								
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Comissão</span></th>
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 120px;">Estado</span></th>
								
								<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">Ações</span></th>
							</tr>
						</thead>

						@php 
						$somaValor = 0;
						$somaComissao = 0;
						@endphp

						<tbody class="datatable-body">
							@foreach($pagamentos as $p)

							<tr class="datatable-row">
								<td class="datatable-cell">
									<span class="codigo" style="width: 120px;">
										{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/y H:i')}}
									</span>
								</td>
								<td class="datatable-cell">
									<span class="codigo" style="width: 250px;">
										{{$p->rep->empresa->nome}}
									</span>
								</td>
								
								<td class="datatable-cell">
									<span class="codigo" style="width: 120px;">
										{{ number_format($p->valor, 2, ',', '.') }}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 120px;">
										{{ number_format($p->valor*($representante->comissao/100), 2, ',', '.') }}
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 120px;">
										@if($p->pagamento_comissao)
										<span class="label label-xl label-inline label-light-success">
											PAGO
										</span>

										@else
										<span class="label label-xl label-inline label-light-danger">
											PENDENTE
										</span>
										@endif
									</span>
								</td>

								<td class="datatable-cell">
									<span class="codigo" style="width: 200px;">
										@if(!$p->pagamento_comissao)
										<a class="btn btn-sm btn-danger" onclick='swal("Atenção!", "Deseja pagar esta comissão?", "warning").then((sim) => {if(sim){ location.href="/representantes/pagarComissao/{{$p->id}}" }else{return false} })' href="#!">
											Pagar comissão
										</a>
										@endif

									</span>
								</td>
							</tr>

							@php 
							$somaValor += $p->valor;
							$somaComissao += $p->valor*($representante->comissao/100);
							@endphp
							@endforeach
						</tbody>
					</table>
				</div>

				<h4>Valor total: <strong class="text-success">R$ {{number_format($somaValor, 2, ',', '.')}}</strong></h4>
				<h4>Valor comissão: <strong class="text-danger">R$ {{number_format($somaComissao, 2, ',', '.')}}</strong></h4>
			</div>
		</div>
	</div>
</div>

@endsection	
