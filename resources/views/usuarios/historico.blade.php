@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">

	<div class="card-body">
		
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">
			
			<br>
			<h4>Histórico de: <strong class="text-info">{{$usuario->nome}}</strong></h4>
			<label>Total de registros: {{sizeof($acessos)}}</label>
			<div class="row">

				<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">

					<div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="between" data-wizard-clickable="true">
						
						<div class="row">
							<div class="col-xl-12">

								<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">

									<table class="datatable-table" style="max-width: 100%; overflow: scroll">
										<thead class="datatable-head">
											<tr class="datatable-row" style="left: 0px;">
												<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">DATA ACESSO</span></th>

												<th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 150px;">IP</span></th>
											</tr>


										</thead>
										<tbody id="body" class="datatable-body">
											@foreach($acessos as $a)
											<tr class="datatable-row">
												<td class="datatable-cell">
													<span class="codigo" style="width: 150px;" id="id">
														{{ \Carbon\Carbon::parse($a->created_at)->format('d/m/Y H:i:s') }}
													</span>
												</td>

												<td class="datatable-cell">
													<span class="codigo" style="width: 150px;" id="id">
														{{ $a->ip_address }}
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

			<div class="d-flex justify-content-between align-items-center flex-wrap">
				<div class="d-flex flex-wrap py-2 mr-3">
					@if($acessos->links())
					{{$acessos->links()}}
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@endsection