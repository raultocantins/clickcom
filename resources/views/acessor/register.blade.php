@extends('default.layout')
@section('content')


<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="/acessores/{{{ isset($acessor) ? 'update' : 'save' }}}">

					<input type="hidden" name="id" value="{{{ isset($acessor) ? $acessor->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{isset($acessor) ? 'Editar' : 'Novo'}} Assessor</h3>
						</div>

					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">


									<div class="row">
										<div class="form-group col-sm-12 col-lg-12">
											<label>Pessoa:</label>
											<div class="radio-inline">


												<label class="radio radio-success">
													<input value="p_fisica" name="group1" type="radio" id="pessoaFisica" @if(isset($acessor)) @if(strlen($acessor->cpf_cnpj)
													< 15) checked @endif @endif @if(old('group1') == 'p_fisica') checked @endif/>
													<span></span>
													FISICA
												</label>
												<label class="radio radio-success">
													<input value="p_juridica" name="group1" type="radio" id="pessoaJuridica" @if(isset($acessor)) @if(strlen($acessor->cpf_cnpj) > 15) checked @endif @endif @if(old('group1') == 'p_juridica') checked @endif/>
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
												<input type="text" id="cpf_cnpj" class="form-control @if($errors->has('cpf_cnpj')) is-invalid @endif" name="cpf_cnpj" value="{{{ isset($acessor) ? $acessor->cpf_cnpj : old('cpf_cnpj') }}}">
												@if($errors->has('cpf_cnpj'))
												<div class="invalid-feedback">
													{{ $errors->first('cpf_cnpj') }}
												</div>
												@endif
											</div>
										</div>
										<div class="form-group validated col-lg-2 col-md-2 col-sm-6">
											<label class="col-form-label text-left col-lg-12 col-sm-12">UF</label>

											<select class="custom-select form-control" id="sigla_uf" name="sigla_uf">
												@foreach($estados as $c)
												<option @if(isset($acessor)) @if($acessor->cidade->uf == $c) selected @endif @endif value="{{$c}}" 
													@if(old('sigla_uf') == $c)
													selected
													@endif>
													{{$c}}
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
											<label class="col-form-label">Nome</label>
											<div class="">
												<input id="razao_social" type="text" class="form-control @if($errors->has('razao_social')) is-invalid @endif" name="razao_social" value="{{{ isset($acessor) ? $acessor->razao_social : old('razao_social') }}}">
												@if($errors->has('razao_social'))
												<div class="invalid-feedback">
													{{ $errors->first('razao_social') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<hr>

									<div class="row">
										<div class="form-group validated col-sm-8 col-lg-8">
											<label class="col-form-label">Rua</label>
											<div class="">
												<input id="rua" type="text" class="form-control @if($errors->has('rua')) is-invalid @endif" name="rua" value="{{{ isset($acessor) ? $acessor->rua : old('rua') }}}">
												@if($errors->has('rua'))
												<div class="invalid-feedback">
													{{ $errors->first('rua') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-2 col-lg-2">
											<label class="col-form-label">Número</label>
											<div class="">
												<input id="numero" type="text" class="form-control @if($errors->has('numero')) is-invalid @endif" name="numero" value="{{{ isset($acessor) ? $acessor->numero : old('numero') }}}">
												@if($errors->has('numero'))
												<div class="invalid-feedback">
													{{ $errors->first('numero') }}
												</div>
												@endif
											</div>
										</div>

									</div>
									<div class="row">
										<div class="form-group validated col-sm-8 col-lg-4">
											<label class="col-form-label">Bairro</label>
											<div class="">
												<input id="bairro" type="text" class="form-control @if($errors->has('bairro')) is-invalid @endif" name="bairro" value="{{{ isset($acessor) ? $acessor->bairro : old('bairro') }}}">
												@if($errors->has('bairro'))
												<div class="invalid-feedback">
													{{ $errors->first('bairro') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-lg-4 col-md-5 col-sm-10">
											<label class="col-form-label text-left col-12 col-sm-12">Cidade</label>
											<select class="form-control select2" id="kt_select2_1" name="cidade_id">
												@foreach($cidades as $c)
												<option value="{{$c->id}}" @isset($cliente) @if($c->id == $acessor->cidade_id) selected @endif @endisset 
													@if(old('cidade') == $c->id)
													selected
													@endif
													>
													{{$c->nome}} ({{$c->uf}})
												</option>
												@endforeach
											</select>
											@if($errors->has('cidade'))
											<div class="invalid-feedback">
												{{ $errors->first('cidade') }}
											</div>
											@endif
										</div>
										<div class="form-group validated col-sm-8 col-lg-4">
											<label class="col-form-label">Email</label>
											<div class="">
												<input id="email" type="text" class="form-control @if($errors->has('email')) is-invalid @endif" name="email" value="{{{ isset($acessor) ? $acessor->email : old('email') }}}">
												@if($errors->has('email'))
												<div class="invalid-feedback">
													{{ $errors->first('email') }}
												</div>
												@endif
											</div>
										</div>

									</div>



									<div class="row">
										<div class="form-group validated col-sm-8 col-lg-3">
											<label class="col-form-label">Telefone</label>
											<div class="">
												<input id="telefone" type="text" class="form-control @if($errors->has('telefone')) is-invalid @endif" name="telefone" value="{{{ isset($acessor) ? $acessor->telefone : old('telefone') }}}">
												@if($errors->has('telefone'))
												<div class="invalid-feedback">
													{{ $errors->first('telefone') }}
												</div>
												@endif
											</div>
										</div>


										<div class="form-group col-lg-4 col-md-9 col-sm-12">
											<label class="col-form-label">Data de Registro</label>
											<div class="">
												<div class="input-group date">
													<input type="text" name="data_registro" class="form-control @if($errors->has('data_registro')) is-invalid @endif" readonly value="{{{ isset($acessor->data_registro) ? \Carbon\Carbon::parse($acessor->data_registro)->format('d/m/Y') : old('data_registro') }}}" id="kt_datepicker_3" />
													<div class="input-group-append">
														<span class="input-group-text">
															<i class="la la-calendar"></i>
														</span>
													</div>
												</div>
												@if($errors->has('data_registro'))
												<div class="invalid-feedback">
													{{ $errors->first('data_registro') }}
												</div>
												@endif

											</div>
										</div>

										<div class="form-group validated col-sm-8 col-lg-3">
											<label class="col-form-label">CEP</label>
											<div class="">
												<input id="cep" type="text" class="form-control @if($errors->has('cep_cobranca')) is-invalid @endif" name="cep" value="{{{ isset($acessor) ? $acessor->cep : old('cep') }}}">
												@if($errors->has('cep'))
												<div class="invalid-feedback">
													{{ $errors->first('cep') }}
												</div>
												@endif
											</div>
										</div>

									</div>



									<div class="row">
										@if(!isset($acessor) || $acessor->funcionario_id == NULL)
										<div class="form-group validated col-sm-8 col-lg-5">
											<label class="col-form-label">Funcionário (opcional)</label>
											<div class="">
												<select class="form-control custom-select" name="funcionario_id">
													<option value="0">--</option>

													@foreach($funcionarios as $u)
													<option 
													@if(isset($acessor))
													@if($acessor->funcionario_id == $u->id)
													selected
													@endif
													@endif
													value="{{$u->id}}">{{$u->nome}}</option>
													@endforeach
												</select>
											</div>
										</div>
										@else
										<div class="form-group validated col-sm-8 col-lg-5">
											<label class="col-form-label">Usuario: 
												<strong class="text-info">{{$acessor->funcionario->nome}}</strong>
											</label>
										</div>
										@endif
										

										<div class="form-group validated col-sm-8 col-lg-3">
											<label class="col-form-label">Percentual de comissão</label>
											<div class="">
												<input id="percentual_comissao" type="text" class="form-control @if($errors->has('percentual_comissao')) is-invalid @endif money" name="percentual_comissao" value="{{{ isset($acessor) ? $acessor->percentual_comissao : old('percentual_comissao') }}}">
												@if($errors->has('percentual_comissao'))
												<div class="invalid-feedback">
													{{ $errors->first('percentual_comissao') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-6 col-lg-2">
											<label class="col-form-label text-left col-lg-12 col-sm-12">Ativo</label>
											<div class="col-6">
												<span class="switch switch-outline switch-success">
													<label>
														<input value="true" @if(isset($acessor) && $acessor->ativo) checked @else checked @endif type="checkbox" name="ativo" id="ativo">
														<span></span>
													</label>
												</span>
											</div>
										</div>

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
							<a style="width: 100%" class="btn btn-danger" href="/acessores">
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

@endsection