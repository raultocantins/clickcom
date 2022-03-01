@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="/locacao/salvar">

					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">
								@isset($locacao) Editar @else Nova @endif Locação
							</h3>
						</div>

					</div>
					<input type="hidden" id="_token" name="_token" value="{{csrf_token()}}">

					<input type="hidden" name="id" value="{{{ isset($locacao) ? $locacao->id : 0 }}}">


					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">

									<div class="row">
										<div class="form-group validated col-lg-8 col-md-8 col-sm-10">
											<label class="col-form-label">Cliente</label>
											<div class="input-group">

												<select class="form-control select2 @if($errors->has('cliente_id')) is-invalid @endif" id="kt_select2_1" name="cliente_id">
													<option value="0">Selecione o cliente</option>
													@foreach($clientes as $c)
													<option 
													@if(isset($locacao))
													@if($locacao->cliente_id == $c->id)
													selected
													@endif
													@else
													@if(old('cliente_id') == $c->id)
													selected
													@endif
													@endif

													value="{{$c->id}}">{{$c->razao_social}}</option>
													@endforeach
												</select>

												<div class="input-group-prepend">
													<span class="input-group-text btn-info btn" onclick="novoCliente()">
														<i class="la la-plus"></i>
													</span>
												</div>
											</div>
											@if($errors->has('cliente_id'))
											<div class="invalid-feedback">
												{{ $errors->first('cliente_id') }}
											</div>
											@endif
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-6 col-lg-4">
											<label class="col-form-label">Data início</label>
											<div class="">
												<input type="text" data-mask="00/00/0000" id="kt_datepicker_1" class="form-control @if($errors->has('inicio')) is-invalid @endif" name="inicio" value="{{{ isset($locacao) ? \Carbon\Carbon::parse($locacao->inicio)->format('d/m/Y') : old('inicio') }}}">
												@if($errors->has('inicio'))
												<div class="invalid-feedback">
													{{ $errors->first('inicio') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-6 col-lg-4">
											<label class="col-form-label">Data fim</label>
											<div class="">
												<input type="text" data-mask="00/00/0000" id="kt_datepicker_2" class="form-control @if($errors->has('fim')) is-invalid @endif" name="fim" value="{{{ isset($locacao) ? \Carbon\Carbon::parse($locacao->fim)->format('d/m/Y') : old('fim') }}}">
												@if($errors->has('fim'))
												<div class="invalid-feedback">
													{{ $errors->first('fim') }}
												</div>
												@endif
											</div>
										</div>

									</div>

									<div class="row">
										<div class="form-group validated col-lg-8 col-md-8 col-sm-10">
											<label class="col-form-label text-left col-lg-4 col-sm-12">Observação</label>

											<input type="text" id="observacao" class="form-control @if($errors->has('observacao')) is-invalid @endif" name="observacao" value="{{{ isset($locacao) ? $locacao->observacao : old('observacao') }}}">
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
					<div class="card-footer">

						<div class="row">
							<div class="col-xl-2">

							</div>
							<div class="col-lg-3 col-sm-6 col-md-4">
								<a style="width: 100%" class="btn btn-danger" href="">
									<i class="la la-close"></i>
									<span class="">Cancelar</span>
								</a>
							</div>
							<div class="col-lg-3 col-sm-6 col-md-4">
								<button style="width: 100%" type="submit" class="btn btn-success">
									<i class="la la-check"></i>
									<span class="">Salvar</span>
								</button>
							</div>

						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>



<div class="modal fade" id="modal-cliente" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Novo Cliente</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<div class="modal-body">

				<div class="row">
					<div class="col-xl-12">

						<div class="row">
							<div class="form-group col-sm-12 col-lg-12">
								<label>Pessoa:</label>
								<div class="radio-inline">
									<label class="radio radio-success">
										<input name="group1" type="radio" id="pessoaFisica"/>
										<span></span>
										FISICA
									</label>
									<label class="radio radio-success">
										<input name="group1" type="radio" id="pessoaJuridica"/>
										<span></span>
										JURIDICA
									</label>

								</div>

							</div>
						</div>
						<div class="row">

							<div class="form-group validated col-sm-3 col-lg-4">
								<label class="col-form-label" id="lbl_cpf_cnpj">CPF</label>
								<div class="">
									<input type="text" id="cpf_cnpj" class="form-control @if($errors->has('cpf_cnpj')) is-invalid @endif" name="cpf_cnpj">
									
								</div>
							</div>
							<div class="form-group validated col-lg-2 col-md-2 col-sm-6">
								<label class="col-form-label text-left col-lg-12 col-sm-12">UF</label>

								<select class="custom-select form-control" id="sigla_uf" name="sigla_uf">
									@foreach(App\Models\Cidade::estados() as $c)
									<option value="{{$c}}">{{$c}}
									</option>
									@endforeach
								</select>

							</div>
							<div class="form-group validated col-lg-2 col-md-2 col-sm-6">
								<br><br>
								<a type="button" id="btn-consulta-cadastro" onclick="consultaCadastro()" class="btn btn-success spinner-white spinner-right">
									<span>
										<i class="fa fa-search"></i>
									</span>
								</a>
							</div>

						</div>

						<div class="row">
							<div class="form-group validated col-sm-10 col-lg-10">
								<label class="col-form-label">Razao Social/Nome</label>
								<div class="">
									<input id="razao_social" type="text" class="form-control @if($errors->has('razao_social')) is-invalid @endif">
									
								</div>
							</div>

							<div class="form-group validated col-sm-10 col-lg-10">
								<label class="col-form-label">Nome Fantasia</label>
								<div class="">
									<input id="nome_fantasia" type="text" class="form-control @if($errors->has('nome_fantasia')) is-invalid @endif">
								</div>
							</div>

							<div class="form-group validated col-sm-3 col-lg-4">
								<label class="col-form-label" id="lbl_ie_rg">RG</label>
								<div class="">
									<input type="text" id="ie_rg" class="form-control @if($errors->has('ie_rg')) is-invalid @endif">
								</div>
							</div>
							<div class="form-group validated col-lg-3 col-md-3 col-sm-10">
								<label class="col-form-label text-left col-lg-12 col-sm-12">Consumidor Final</label>

								<select class="custom-select form-control" id="consumidor_final">
									<option value="1">SIM</option>
									<option value="0">NAO</option>
								</select>

							</div>

							<div class="form-group validated col-lg-3 col-md-3 col-sm-10">
								<label class="col-form-label text-left col-lg-12 col-sm-12">Contribuinte</label>

								<select class="custom-select form-control" id="contribuinte">

									<option value="1">SIM</option>
									<option value="0">NAO</option>
								</select>
							</div>

							<div class="form-group validated col-sm-3 col-lg-4">
								<label class="col-form-label" id="lbl_ie_rg">Limite de Venda</label>
								<div class="">
									<input type="text" id="limite_venda" class="form-control @if($errors->has('limite_venda')) is-invalid @endif money"  value="0">
									
								</div>
							</div>

						</div>
						<hr>
						<h5>Endereço de Faturamento</h5>
						<div class="row">
							<div class="form-group validated col-sm-8 col-lg-8">
								<label class="col-form-label">Rua</label>
								<div class="">
									<input id="rua" type="text" class="form-control @if($errors->has('rua')) is-invalid @endif">
									
								</div>
							</div>

							<div class="form-group validated col-sm-2 col-lg-2">
								<label class="col-form-label">Número</label>
								<div class="">
									<input id="numero2" type="text" class="form-control @if($errors->has('numero')) is-invalid @endif">
									
								</div>
							</div>

							<div class="form-group validated col-sm-8 col-lg-5">
								<label class="col-form-label">Bairro</label>
								<div class="">
									<input id="bairro" type="text" class="form-control @if($errors->has('bairro')) is-invalid @endif">
									
								</div>
							</div>

							<div class="form-group validated col-sm-8 col-lg-3">
								<label class="col-form-label">CEP</label>
								<div class="">
									<input id="cep" type="text" class="form-control @if($errors->has('cep')) is-invalid @endif">
									
								</div>
							</div>

							<div class="form-group validated col-sm-8 col-lg-4">
								<label class="col-form-label">Email</label>
								<div class="">
									<input id="email" type="text" class="form-control @if($errors->has('email')) is-invalid @endif">
									
								</div>
							</div>

							@php
							$cidade = App\Models\Cidade::getCidadeCod($config->codMun);
							@endphp
							<div class="form-group validated col-lg-6 col-md-6 col-sm-10">
								<label class="col-form-label text-left col-lg-4 col-sm-12">Cidade</label><br>
								<select style="width: 100%" class="form-control select2" id="kt_select2_4">
									@foreach(App\Models\Cidade::all() as $c)
									<option @if($cidade->id == $c->id) selected @endif value="{{$c->id}}">
										{{$c->nome}} ({{$c->uf}})
									</option>
									@endforeach
								</select>
								
							</div>

							<div class="form-group validated col-sm-8 col-lg-3">
								<label class="col-form-label">Telefone (Opcional)</label>
								<div class="">
									<input id="telefone" type="text" class="form-control @if($errors->has('telefone')) is-invalid @endif">
								</div>
							</div>

							<div class="form-group validated col-sm-8 col-lg-3">
								<label class="col-form-label">Celular (Opcional)</label>
								<div class="">
									<input id="celular" type="text" class="form-control @if($errors->has('celular')) is-invalid @endif">
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" id="btn-frete" class="btn btn-danger font-weight-bold spinner-white spinner-right" data-dismiss="modal" aria-label="Close">Fechar</button>
				<button type="button" onclick="salvarCliente()" class="btn btn-success font-weight-bold spinner-white spinner-right">Salvar</button>
			</div>
		</div>
	</div>
</div>

@section('javascript')
<script type="text/javascript">
	function novoCliente(){
		$('#modal-cliente').modal('show')
	}

	function salvarCliente(){
		let js = {
			razao_social: $('#razao_social').val(),
			nome_fantasia: $('#nome_fantasia').val() ? $('#nome_fantasia2').val() : '',
			rua: $('#rua').val() ? $('#rua').val() : '',
			cpf_cnpj: $('#cpf_cnpj').val() ? $('#cpf_cnpj').val() : '',
			ie_rg: $('#ie_rg').val() ? $('#ie_rg').val() : '',
			bairro: $('#bairro').val() ? $('#bairro').val() : '',
			cep: $('#cep').val() ? $('#cep').val() : '',
			consumidor_final: $('#consumidor_final').val() ? $('#consumidor_final').val() : '',
			contribuinte: $('#contribuinte').val() ? $('#contribuinte').val() : '',
			limite_venda: $('#limite_venda').val() ? $('#limite_venda').val() : '',
			cidade_id: $('#kt_select2_4').val() ? $('#kt_select2_4').val() : NULL,
			telefone: $('#telefone').val() ? $('#telefone').val() : '',
			celular: $('#celular').val() ? $('#celular').val() : '',
		}

		if(js.razao_social == ''){
			swal("Erro", "Informe a razão social", "warning")
		}

		if(js.razao_social == ''){
			swal("Erro", "Informe a razão social", "warning")
		}else{

			let token = $('#_token').val();
			$.post(path + 'clientes/quickSave',
			{
				_token: token,
				data: js
			})
			.done((res) =>{
				CLIENTE = res;
				console.log(res)
				$('#kt_select2_1').append('<option value="'+res.id+'">'+ 
					res.razao_social+'</option>').change();
				$('#kt_select2_1').val(res.id).change();
				swal("Sucesso", "Cliente adicionado!!", 'success')
				.then(() => {
					$('#modal-cliente').modal('hide')
				})
			})
			.fail((err) => {
				console.log(err)
			})
		}

		console.log(js)
	}
</script>
@endsection
@endsection

