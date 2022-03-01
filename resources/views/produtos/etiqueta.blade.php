@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">
	
	<div class="card-body">

		<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
			<div class="card card-custom gutter-b example example-compact">
				<div class="card-header">

					<div class="col-xl-12">
						<div class="row">
							<div class="col-xl-12">
								<div id="kt_datatable" class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded">
									<br>
									<h4>Gerar etiqueta do produto: 
										<strong>{{$produto->nome}}</strong>
									</h4>

									<form method="post" action="/produtos/etiquetaStore">
										@csrf

										<input type="hidden" value="{{$produto->id}}" name="produto_id">
										<div class="row">
											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">Altura mm*</label>
												<div class="">
													<input type="text" class="form-control @if($errors->has('altura')) is-invalid @endif" name="altura" value="{{ old('altura') }}">
													@if($errors->has('altura'))
													<div class="invalid-feedback">
														{{ $errors->first('altura') }}
													</div>
													@endif
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">Largura mm*</label>
												<div class="">
													<input type="text" class="form-control @if($errors->has('largura')) is-invalid @endif" name="largura" value="{{ old('largura') }}">
													@if($errors->has('largura'))
													<div class="invalid-feedback">
														{{ $errors->first('largura') }}
													</div>
													@endif
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">Num. etiquetas por linha*</label>
												<div class="">
													<input type="text" class="form-control @if($errors->has('qtd_linhas')) is-invalid @endif" name="qtd_linhas" value="{{ old('qtd_linhas') }}">
													@if($errors->has('qtd_linhas'))
													<div class="invalid-feedback">
														{{ $errors->first('qtd_linhas') }}
													</div>
													@endif
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">Dist. entre etiquetas lateral mm*</label>
												<div class="">
													<input type="text" class="form-control @if($errors->has('dist_lateral')) is-invalid @endif" name="dist_lateral" value="{{ old('dist_lateral') }}">
													@if($errors->has('dist_lateral'))
													<div class="invalid-feedback">
														{{ $errors->first('dist_lateral') }}
													</div>
													@endif
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">Dist. entre etiquetas topo mm*</label>
												<div class="">
													<input type="text" class="form-control @if($errors->has('dist_topo')) is-invalid @endif" name="dist_topo" value="{{ old('dist_topo') }}">
													@if($errors->has('dist_topo'))
													<div class="invalid-feedback">
														{{ $errors->first('dist_topo') }}
													</div>
													@endif
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">Qtd de etiquetas*</label>
												<div class="">
													<input type="text" class="form-control @if($errors->has('qtd_etiquetas')) is-invalid @endif" name="qtd_etiquetas" value="{{ old('qtd_etiquetas') }}">
													@if($errors->has('qtd_etiquetas'))
													<div class="invalid-feedback">
														{{ $errors->first('qtd_etiquetas') }}
													</div>
													@endif
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">Tamanho da fonte*</label>
												<div class="">
													<input type="text" class="form-control @if($errors->has('tamanho_fonte')) is-invalid @endif" name="tamanho_fonte" value="{{ old('tamanho_fonte') }}">
													@if($errors->has('tamanho_fonte'))
													<div class="invalid-feedback">
														{{ $errors->first('tamanho_fonte') }}
													</div>
													@endif
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<label class="col-form-label">Tamanho código de barras mm*</label>
												<div class="">
													<input type="text" class="form-control @if($errors->has('tamanho_codigo')) is-invalid @endif" name="tamanho_codigo" value="{{ old('tamanho_codigo') }}">
													@if($errors->has('tamanho_codigo'))
													<div class="invalid-feedback">
														{{ $errors->first('tamanho_codigo') }}
													</div>
													@endif
												</div>
											</div>
										</div>
										<div>
											<p class="text-info">Os campos marcados serão impressos na etiqueta</p>
										</div>
										<div class="row">
											<div class="form-group validated col-sm-3 col-lg-3">
												<div class="">
													<label class="checkbox">
														<input type="checkbox" checked="checked" name="nome_empresa"/>
														<span></span>
														<b style="margin-left: 3px;">Nome da empresa</b>
													</label>
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<div class="">
													<label class="checkbox">
														<input type="checkbox" checked="checked" name="nome_produto"/>
														<span></span>
														<b style="margin-left: 3px;">Nome do produto</b>
													</label>
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<div class="">
													<label class="checkbox">
														<input type="checkbox" checked="checked" name="valor_produto"/>
														<span></span>
														<b style="margin-left: 3px;">Valor do produto</b>
													</label>
												</div>
											</div>

											<div class="form-group validated col-sm-3 col-lg-3">
												<div class="">
													<label class="checkbox">
														<input type="checkbox" checked="checked" name="cod_produto"/>
														<span></span>
														<b style="margin-left: 3px;">Código do produto</b>
													</label>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-12">
												<button class="btn btn-info" type="submit">
													Gerar
												</button>
											</div>
										</div>
									</form>
									<br>
								</div>
							</div>
						</div>
						
					</div>
				</div>

			</div>
		</div>
	</div>

</div>

@endsection
