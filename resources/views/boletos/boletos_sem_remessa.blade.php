@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">


	<div class="card-body">


		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">

			
			<br>
			<h4>Lista de boletos</h4>
			<label>Total de registros: {{count($boletos)}}</label>
			<div class="row">


				<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">

					<div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="between" data-wizard-clickable="true">


						<div class="row">
							<div class="col-xl-12">

								<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

									<table class="datatable-table" style="max-width: 100%; overflow: scroll">
										<thead class="datatable-head">
											<tr class="datatable-row" style="left: 0px;">
												<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 70px;">#</span></th>
												<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 200px;">CLIENTE</span></th>
												<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">VALOR</span></th>
												<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">BANCO</span></th>
												<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">VENCIMENTO</span></th>
												<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Nº DO BOLETO</span></th>
												<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 100px;">Nº DO DOCUMENTO</span></th>
												<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">JUROS</span></th>
												<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">MULTA</span></th>
											</tr>
										</thead>
										<input type="hidden" id="boletos" value="{{json_encode($boletos)}}" name="">
										<tbody id="body" class="datatable-body">
											@foreach($boletos as $b)
											<tr class="datatable-row">

												<td id="checkbox">

													<label style="width: 80px;" class="checkbox checkbox-success" for="sel_{{$b->id}}">
														<input id="sel_{{$b->id}}" class="select" type="checkbox" name="Checkboxes5"/>
														<span></span>
													</label>
												</td>
												<td class="datatable-cell"><span class="codigo" style="width: 200px;" id="id">{{$b->conta->getCliente()->razao_social}}</span>
												</td>

												<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{number_format($b->conta->valor_integral, 2, ',', '.')}}</span>
												</td>

												<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{$b->banco->banco}}</span>
												</td>

												<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">{{ \Carbon\Carbon::parse($b->data_vencimento)->format('d/m/Y')}}</span>
												</td>

												<td class="datatable-cell">
													<span class="codigo" style="width: 100px;" id="id">
														{{ $b->numero }}
													</span>
												</td>

												<td class="datatable-cell">
													<span class="codigo" style="width: 100px;" id="id">
														{{ $b->numero_documento }}
													</span>
												</td>

												<td class="datatable-cell"><span class="codigo" style="width: 80px;" id="id">{{number_format($b->juros, 2, ',', '.')}}</span>
												</td>

												<td class="datatable-cell"><span class="codigo" style="width: 80px;" id="id">{{number_format($b->multa, 2, ',', '.')}}</span>
												</td>

											</tr>
											@endforeach
										</tbody>
									</table>
								</div>

							</div>
							<button id="btn_gerar" style="display: none" class="btn btn-info">
								<i class="la la-file"></i>
								Gerar
							</button>
						</div>

					</div>
				</div>

			</div>

		</div>
	</div>
</div>

@section('javascript')
<script type="text/javascript">
	var ADICIONADAS = [];
	var BOLETOS = [];

	$(function () {
		BOLETOS = JSON.parse($('#boletos').val());
	});

	$('.select').click(() => {
		ADICIONADAS = []
		BOLETOS.map((b) => {
			let s = $('#sel_'+b.id).is(':checked');
			console.log(s)
			if(s){
				ADICIONADAS.push(b)
			}
		})
		verificaBotaoGerar();
	})

	function verificaBotaoGerar(){
		if(ADICIONADAS.length > 0){
			$('#btn_gerar').css('display', 'inline-block')
			$('#btn_gerar').css('width', '150px')
		}else{
			$('#btn_gerar').css('display', 'none')
		}
	}

	$('#btn_gerar').click(() => {
		let temp = [];
		ADICIONADAS.map((a) => {
			temp.push(a.id)
		})
		console.log(temp)
		location.href = path + 'remessasBoleto/gerarRemessaMulti/'+temp
	})
</script>
@endsection

@endsection