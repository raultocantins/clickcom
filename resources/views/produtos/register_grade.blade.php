@extends('default.layout')
@section('content')

<style type="text/css">
	.modal-body-grade{
		height: 70vh;
		overflow-y: auto;
	}
</style>

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="/produtos/atualizarGradeCompleta" enctype="multipart/form-data">


					<input type="hidden" name="id" value="{{{ isset($produto) ? $produto->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{isset($produto) ? 'Editar' : 'Novo'}} Produto</h3>
						</div>

					</div>
					<input type="hidden" value="{{csrf_token()}}" id="_token" name="_token">

					<div class="wizard wizard-3" id="kt_wizard_v3" data-wizard-state="between" data-wizard-clickable="true">
						<!--begin: Wizard Nav-->

						<div class="wizard-nav">

							<div class="wizard-steps px-8 py-8 px-lg-15 py-lg-3">
								<!--begin::Wizard Step 1 Nav-->
								<div class="wizard-step" data-wizard-type="step" data-wizard-state="done">
									<div class="wizard-label">
										<h3 class="wizard-title">
											<span>
												IDENTIFICAÇÃO
											</span>
										</h3>
										<div class="wizard-bar"></div>
									</div>
								</div>
								<!--end::Wizard Step 1 Nav-->
								<!--begin::Wizard Step 2 Nav-->
								<div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
									<div class="wizard-label">
										<h3 class="wizard-title">
											<span>
												ALÍQUOTAS
											</span>
										</h3>
										<div class="wizard-bar"></div>
									</div>
								</div>
							</div>
						</div>

						<div class="card-body">
							<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">

								<!--begin: Wizard Form-->
								<form class="form fv-plugins-bootstrap fv-plugins-framework" id="kt_form">
									<!--begin: Wizard Step 1-->
									<p class="kt-widget__data text-danger">Campos com (*) obrigatório</p>
									<p class="kt-widget__data text-danger">>>Os campos com caixa de seleção se marcados serão alterados na grade completa</p>

									<div class="pb-5" data-wizard-type="step-content">
										<div class="row">

											<div class="col-xl-2"></div>
											<div class="col-xl-8">
												<div class="row">

													<div class="form-group validated col-sm-9 col-lg-9">
														<label class="col-form-label">Nome*</label>
														<div class="">
															<input type="text" class="form-control @if($errors->has('nome')) is-invalid @endif" name="nome" value="{{{ isset($produto) ? $produto->nome : old('nome') }}}">
															@if($errors->has('nome'))
															<div class="invalid-feedback">
																{{ $errors->first('nome') }}
															</div>
															@endif
														</div>
													</div>

													<div class="form-group validated col-sm-3 col-lg-3">
														<label class="col-form-label">Referência</label>
														<div class="">
															<input type="text" class="form-control @if($errors->has('referencia')) is-invalid @endif" name="referencia" value="{{{ isset($produto) ? $produto->referencia : old('referencia') }}}">
															@if($errors->has('referencia'))
															<div class="invalid-feedback">
																{{ $errors->first('referencia') }}
															</div>
															@endif
														</div>
													</div>

													<div class="form-group validated col-sm-3 col-lg-3">
														<label class="col-form-label">Valor de Compra*</label>
														<div class="input-group">
															<div class="input-group-prepend">
																<span class="input-group-text">
																	<label class="checkbox checkbox-inline checkbox-info">
																		<input type="checkbox" name="check_valor_compra" />
																		<span></span>
																	</label>
																</span>
															</div>
															<input type="text" id="valor_compra" class="form-control @if($errors->has('valor_compra')) is-invalid @endif money" name="valor_compra" value="{{{ isset($produto) ? number_format($produto->valor_compra, $casasDecimais) : old('valor_compra') }}}">
															@if($errors->has('valor_compra'))
															<div class="invalid-feedback">
																{{ $errors->first('valor_compra') }}
															</div>
															@endif
														</div>
													</div>
													

													<div class="form-group validated col-sm-3 col-lg-3">
														<label class="col-form-label">% lucro*</label>
														<div class="">
															<input type="text" id="percentual_lucro" class="form-control @if($errors->has('percentual_lucro')) is-invalid @endif money" name="percentual_lucro" value="{{{ isset($produto) ? $produto->percentual_lucro : old('percentual_lucro') }}}">
															@if($errors->has('percentual_lucro'))
															<div class="invalid-feedback">
																{{ $errors->first('percentual_lucro') }}
															</div>
															@endif
														</div>
													</div>

													<div class="form-group validated col-sm-3 col-lg-3">
														<label class="col-form-label">Valor de Venda*</label>
														<div class="input-group">
															<div class="input-group-prepend">
																<span class="input-group-text">
																	<label class="checkbox checkbox-inline checkbox-info">
																		<input type="checkbox" name="check_valor_venda" />
																		<span></span>
																	</label>
																</span>
															</div>
															<input type="text" id="valor_venda" class="form-control @if($errors->has('valor_venda')) is-invalid @endif money" name="valor_venda" value="{{{ isset($produto) ? number_format($produto->valor_venda, $casasDecimais) : old('valor_venda') }}}">
															@if($errors->has('valor_venda'))
															<div class="invalid-feedback">
																{{ $errors->first('valor_venda') }}
															</div>
															@endif
														</div>
													</div>


													<div class="form-group validated col-sm-3 col-lg-3">
														<label class="col-form-label">Estoque Atual</label>
														<div class="input-group">
															<div class="input-group-prepend">
																<span class="input-group-text">
																	<label class="checkbox checkbox-inline checkbox-info">
																		<input type="checkbox" name="check_estoque" />
																		<span></span>
																	</label>
																</span>
															</div>
															<input type="text" id="estoque" class="form-control @if($errors->has('estoque')) is-invalid @endif" name="estoque" 
															value="@if($produto->estoque) {{$produto->estoque->quantidade}} @else 0 @endif">
															@if($errors->has('estoque'))
															<div class="invalid-feedback">
																{{ $errors->first('estoque') }}
															</div>
															@endif
														</div>
													</div>
													

													<div class="form-group validated col-sm-6 col-lg-4">
														<label class="col-form-label text-left col-lg-12 col-sm-12">Reajuste valor automatico</label>
														<div class="col-6">
															<span class="switch switch-outline switch-danger">
																<label>
																	<input value="true" @if(isset($produto) && $produto->reajuste_automatico) checked @endif type="checkbox" name="reajuste_automatico" id="reajuste_automatico">
																	<span></span>
																</label>
															</span>
														</div>
													</div>

													<div class="form-group validated col-sm-6 col-lg-3">
														<label class="col-form-label text-left col-lg-12 col-sm-12">Gerenciar estoque</label>
														<div class="col-6">
															<span class="switch switch-outline switch-primary">
																<label>
																	<input value="true" @if(isset($produto) && $produto->gerenciar_estoque) checked @elseif(getenv("PRODUTO_GERENCIAR_ESTOQUE") == 1 && !isset($produto)) checked @endif type="checkbox" name="gerenciar_estoque" id="gerenciar_estoque">
																	<span></span>
																</label>
															</span>
														</div>
													</div>

													<div class="form-group validated col-sm-6 col-lg-2">
														<label class="col-form-label text-left col-lg-12 col-sm-12">Inativo</label>
														<div class="col-6">
															<span class="switch switch-outline switch-danger">
																<label>
																	<input value="true" @if(isset($produto) && $produto->inativo) checked @endif type="checkbox" name="inativo" id="inativo">
																	<span></span>
																</label>
															</span>
														</div>
													</div>


													<div class="form-group validated col-lg-4 col-md-6 col-sm-10">
														<label class="col-form-label">Categoria</label>
														<div class="input-group">

															<select id="categoria" class="form-control custom-select" name="categoria_id">
																@foreach($categorias as $cat)
																<option value="{{$cat->id}}" @isset($produto) @if($cat->id == $produto->categoria_id)
																	selected=""
																	@endif
																	@endisset >{{$cat->nome}}
																</option>
																@endforeach
															</select>
															<div class="input-group-prepend">
																<span class="input-group-text btn-info btn" onclick="novaCategoria()">
																	<i class="la la-plus"></i>
																</span>
															</div>
															@if($errors->has('categoria_id'))
															<div class="invalid-feedback">
																{{ $errors->first('categoria_id') }}
															</div>
															@endif
														</div>
													</div>

													<div class="form-group validated col-lg-3 col-md-6 col-sm-10">
														<label class="col-form-label">Sub Categoria</label>
														<div class="input-group">

															<select id="sub_categoria_id" class="form-control custom-select" name="sub_categoria_id">
																<option value="">--</option>
															</select>
															
															@if($errors->has('categoria_id'))
															<div class="invalid-feedback">
																{{ $errors->first('categoria_id') }}
															</div>
															@endif
														</div>
													</div>

													<div class="form-group validated col-lg-4 col-md-6 col-sm-10">
														<label class="col-form-label">Marca</label>
														<div class="input-group">

															<select id="marca" class="form-control custom-select" name="marca_id">
																<option value="">--</option>
																@foreach($marcas as $cat)
																<option value="{{$cat->id}}" @isset($produto) @if($cat->id == $produto->marca_id)
																	selected=""
																	@endif
																	@endisset >{{$cat->nome}}
																</option>
																@endforeach
															</select>
															<div class="input-group-prepend">
																<span class="input-group-text btn-danger btn" onclick="novaMarca()">
																	<i class="la la-plus"></i>
																</span>
															</div>
															@if($errors->has('marca_id'))
															<div class="invalid-feedback">
																{{ $errors->first('marca_id') }}
															</div>
															@endif
														</div>
													</div>

													<div class="form-group validated col-sm-3 col-lg-3">
														<label class="col-form-label">Estoque minimo</label>
														<div class="">
															<input type="text" id="estoque_minimo" class="form-control @if($errors->has('estoque_minimo')) is-invalid @endif" name="estoque_minimo" value="{{{ isset($produto) ? $produto->estoque_minimo : old('estoque_minimo') }}}">
															@if($errors->has('estoque_minimo'))
															<div class="invalid-feedback">
																{{ $errors->first('estoque_minimo') }}
															</div>
															@endif
														</div>
													</div>

													<div class="form-group validated col-sm-4 col-lg-4">
														<label class="col-form-label">Limite maximo desconto %</label>
														<div class="">
															<input type="text" id="limite_maximo_desconto" class="form-control @if($errors->has('limite_maximo_desconto')) is-invalid @endif" name="limite_maximo_desconto" value="{{{ isset($produto) ? $produto->limite_maximo_desconto : old('limite_maximo_desconto') }}}">
															@if($errors->has('limite_maximo_desconto'))
															<div class="invalid-feedback">
																{{ $errors->first('limite_maximo_desconto') }}
															</div>
															@endif
														</div>
													</div>



													<div class="form-group validated col-sm-3 col-lg-4">
														<label class="col-form-label">Alerta de Venc. (Dias)</label>
														<div class="">
															<input type="text" id="alerta_vencimento" class="form-control @if($errors->has('alerta_vencimento')) is-invalid @endif" name="alerta_vencimento" value="{{{ isset($produto) ? $produto->alerta_vencimento : old('alerta_vencimento') }}}">
															@if($errors->has('alerta_vencimento'))
															<div class="invalid-feedback">
																{{ $errors->first('alerta_vencimento') }}
															</div>
															@endif
														</div>
													</div>


													<div class="form-group validated col-lg-4 col-md-6 col-sm-10">
														<label class="col-form-label">Unidade de compra *</label>

														<select class="custom-select form-control" id="unidade_compra" name="unidade_compra">
															@foreach($unidadesDeMedida as $u)
															<option @if(isset($produto)) @if($u==$produto->unidade_compra)
																selected
																@endif
																@else
																@if($u == 'UN')
																selected
																@endif
																@endif value="{{$u}}">{{$u}}
															</option>
															@endforeach
														</select>
													</div>


													<div class="form-group validated col-sm-3 col-lg-3" id="conversao" style="display: none">
														<label class="col-form-label">Conversão Unitária</label>
														<div class="">
															<input type="text" id="alerta_vencimento" class="form-control @if($errors->has('conversao_unitaria')) is-invalid @endif" name="conversao_unitaria" value="{{{ isset($produto->conversao_unitaria) ? $produto->conversao_unitaria : old('conversao_unitaria') }}}">
															@if($errors->has('conversao_unitaria'))
															<div class="invalid-feedback">
																{{ $errors->first('conversao_unitaria') }}
															</div>
															@endif
														</div>
													</div>
													<div class="form-group validated col-lg-4 col-md-6 col-sm-10">
														<label class="col-form-label">Unidade de venda *</label>

														<select class="custom-select form-control" id="unidade_venda" name="unidade_venda">
															@foreach($unidadesDeMedida as $u)
															<option @if(isset($produto)) @if($u==$produto->unidade_venda)
																selected
																@endif
																@else
																@if($u == 'UN')
																selected
																@endif
																@endif value="{{$u}}">{{$u}}
															</option>
															@endforeach
														</select>

													</div>

													<div class="form-group validated col-sm-3 col-lg-3">
														<label class="col-form-label">NCM *</label>
														<div class="">
															<input type="text" id="ncm" class="form-control @if($errors->has('NCM')) is-invalid @endif" name="NCM" value="{{{ isset($produto->NCM) ? $produto->NCM : $tributacao->ncm_padrao }}}">
															@if($errors->has('NCM'))
															<div class="invalid-feedback">
																{{ $errors->first('NCM') }}
															</div>
															@endif
														</div>
													</div>

													<div class="form-group validated col-sm-3 col-lg-3">
														<label class="col-form-label">CEST</label>
														<div class="">
															<input type="text" id="cest" class="form-control @if($errors->has('CEST')) is-invalid @endif" name="CEST" value="{{{ isset($produto->CEST) ? $produto->CEST : old('CEST') }}}">
															@if($errors->has('CEST'))
															<div class="invalid-feedback">
																{{ $errors->first('CEST') }}
															</div>
															@endif
														</div>
													</div>

													<div class="form-group validated col-sm-3 col-lg-3">
														<label class="col-form-label">Ref. balança</label>
														<div class="">
															<input type="text" id="referencia_balanca" class="form-control @if($errors->has('referencia_balanca')) is-invalid @endif" name="referencia_balanca" value="{{{ isset($produto->referencia_balanca) ? $produto->referencia_balanca : old('referencia_balanca') }}}">
															@if($errors->has('referencia_balanca'))
															<div class="invalid-feedback">
																{{ $errors->first('referencia_balanca') }}
															</div>
															@endif
														</div>
													</div>


													<div class="form-group validated col-lg-12 col-md-12 col-sm-12">
														<label class="col-xl-12 col-lg-12 col-form-label text-left">Imagem</label>
														<div class="col-lg-12 col-xl-12">

															<div class="image-input image-input-outline" id="kt_image_1">
																<div class="image-input-wrapper" @if(!isset($produto) || $produto->imagem == '') style="background-image: url(/imgs/no_image.png)" @else
																	style="background-image: url(/imgs_produtos/{{$produto->imagem}})"
																	@endif>

																</div>
																<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
																	<i class="fa fa-pencil icon-sm text-muted"></i>
																	<input type="file" name="file" accept=".png, .jpg, .jpeg">
																	<input type="hidden" name="profile_avatar_remove">
																</label>
																<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Cancel avatar">
																	<i class="fa fa-close icon-xs text-muted"></i>
																</span>
															</div>

															<span class="form-text text-muted">.png, .jpg, .jpeg</span>
															@if($errors->has('file'))
															<div class="invalid-feedback">
																{{ $errors->first('file') }}
															</div>
															@endif
														</div>
													</div>

													<div class="row">
														<div class="form-group validated col-lg-12 col-md-12 col-sm-12">
															<label class="col-xl-12 col-lg-12 col-form-label text-left">Composto</label>
															<div class="col-lg-12 col-xl-12">
																<span class="switch switch-outline switch-success">
																	<label>
																		<input @if(isset($produto->composto) && $produto->composto) checked @endisset value="true" name="composto" class="red-text" type="checkbox">
																		<span></span>
																	</label>
																</span>

																<p class="text-danger">*Produzido no estabelecimento composto de outros produtos já cadastrados, deverá ser criado uma receita para redução de estoque. </p>

															</div>
														</div>
													</div>


													<hr>

													<div class="form-group validated col-12">
														<h3>Derivado Petróleo</h3>
													</div>

													<div class="form-group validated col-lg-6 col-md-10 col-sm-10">
														<label class="col-form-label">ANP</label>

														<select class="custom-select form-control" id="anp" name="anp">
															<option value="">--</option>
															@foreach($anps as $key => $a)
															<option value="{{$key}}" @isset($produto->codigo_anp)
																@if($key == $produto->codigo_anp)
																selected=""
																@endif
																@endisset >[{{$key}}] - {{$a}}
															</option>

															@endforeach
														</select>
													</div>

													<div class="form-group validated col-lg-3 col-md-4 col-sm-4">
														<label class="col-form-label">%GLP</label>

														<input type="text" id="perc_glp" class="form-control @if($errors->has('perc_glp')) is-invalid @endif trib" name="perc_glp" 
														value="{{{ isset($produto->perc_glp) ? $produto->perc_glp : old('perc_glp') }}}">
													</div>

													<div class="form-group validated col-lg-3 col-md-4 col-sm-4">
														<label class="col-form-label">%GNn</label>

														<input type="text" id="perc_gnn" class="form-control @if($errors->has('perc_gnn')) is-invalid @endif trib" name="perc_gnn" 
														value="{{{ isset($produto->perc_gnn) ? $produto->perc_gnn : old('perc_gnn') }}}">
													</div>

													<div class="form-group validated col-lg-3 col-md-4 col-sm-4">
														<label class="col-form-label">%GNi</label>

														<input type="text" id="perc_gni" class="form-control @if($errors->has('perc_gni')) is-invalid @endif trib" name="perc_gni" 
														value="{{{ isset($produto->perc_gni) ? $produto->perc_gni : old('perc_gni') }}}">
													</div>

													<div class="form-group validated col-lg-3 col-md-4 col-sm-4">
														<label class="col-form-label">Valor de partida</label>

														<input type="text" id="valor_partida" class="form-control @if($errors->has('valor_partida')) is-invalid @endif money" name="valor_partida" 
														value="{{{ isset($produto->valor_partida) ? $produto->valor_partida : old('valor_partida') }}}">
													</div>

													<div class="form-group validated col-lg-3 col-md-4 col-sm-4">
														<label class="col-form-label">Un. tributável</label>

														<input type="text" id="unidade_tributavel" class="form-control @if($errors->has('unidade_tributavel')) is-invalid @endif" data-mask="AAAA" name="unidade_tributavel"
														value="{{{ isset($produto->unidade_tributavel) ? $produto->unidade_tributavel : old('unidade_tributavel') }}}">
													</div>

													<div class="form-group validated col-lg-3 col-md-4 col-sm-4">
														<label class="col-form-label">Qtd. tributável</label>

														<input type="text" id="quantidade_tributavel" class="form-control @if($errors->has('quantidade_tributavel')) is-invalid @endif" data-mask="00000,00" data-mask-reverse="true" name="quantidade_tributavel"
														value="{{{ isset($produto->quantidade_tributavel) ? $produto->quantidade_tributavel : old('quantidade_tributavel') }}}">
													</div>


													<hr>
													<div class="form-group validated col-12">
														<h3>Dados de dimensão e peso do produto (Opcional)</h3>
													</div>


													<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
														<label class="col-form-label">Largura (cm)</label>

														<input type="text" id="largura" class="form-control @if($errors->has('largura')) is-invalid @endif" name="largura" 
														value="{{{ isset($produto->largura) ? $produto->largura : old('largura') }}}">

														@if($errors->has('largura'))
														<div class="invalid-feedback">
															{{ $errors->first('largura') }}
														</div>
														@endif
													</div>

													<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
														<label class="col-form-label">Altura (cm)</label>

														<input type="text" id="altura" class="form-control @if($errors->has('altura')) is-invalid @endif" name="altura" 
														value="{{{ isset($produto->altura) ? $produto->altura : old('altura') }}}">
														@if($errors->has('altura'))
														<div class="invalid-feedback">
															{{ $errors->first('altura') }}
														</div>
														@endif
													</div>

													<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
														<label class="col-form-label">Comprimento (cm)</label>

														<input type="text" id="comprimento" class="form-control @if($errors->has('comprimento')) is-invalid @endif" name="comprimento" value="{{{ isset($produto->comprimento) ? $produto->comprimento : old('comprimento') }}}">
														@if($errors->has('comprimento'))
														<div class="invalid-feedback">
															{{ $errors->first('comprimento') }}
														</div>
														@endif
													</div>


													<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
														<label class="col-form-label">Peso liquido</label>

														<input type="text" id="peso_liquido" class="form-control @if($errors->has('peso_liquido')) is-invalid @endif" name="peso_liquido" 
														value="{{{ isset($produto->peso_liquido) ? $produto->peso_liquido : old('peso_liquido') }}}">
														@if($errors->has('peso_liquido'))
														<div class="invalid-feedback">
															{{ $errors->first('peso_liquido') }}
														</div>
														@endif
													</div>

													<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
														<label class="col-form-label">Peso bruto</label>

														<input type="text" id="peso_bruto" class="form-control @if($errors->has('peso_bruto')) is-invalid @endif" name="peso_bruto" 
														value="{{{ isset($produto->peso_bruto) ? $produto->peso_bruto : old('peso_bruto') }}}">
														@if($errors->has('peso_bruto'))
														<div class="invalid-feedback">
															{{ $errors->first('peso_bruto') }}
														</div>
														@endif
													</div>


													<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
														<label class="col-form-label">Largura (cm)</label>

														<input type="text" id="largura" class="form-control @if($errors->has('largura')) is-invalid @endif" name="largura" 
														value="{{{ isset($produto->largura) ? $produto->largura : old('largura') }}}">

														@if($errors->has('largura'))
														<div class="invalid-feedback">
															{{ $errors->first('largura') }}
														</div>
														@endif
													</div>

													@if(!isset($produto) && getenv("ECOMMERCE") == 1)
													<div class="form-group validated col-sm-6 col-lg-3">
														<label class="col-form-label text-left col-lg-12 col-sm-12">Ecommerce</label>
														<div class="col-6">
															<span class="switch switch-outline switch-danger">
																<label>
																	<input @if(old('ecommerce')) checked @endif class="ecommerce" type="checkbox" name="ecommerce">
																	<span></span>
																</label>
															</span>
														</div>
													</div>
													@endif

													<div class="col-lg-12 col-xl-12">
														<p class="text-danger">*Se atente a preencher todos os dados para utilizar a Api dos correios.</p>
													</div>

													@if(!isset($produto))
													<div class="col-12 div-ecommmerce" style="display: none;">
														<div class="row">
															<div class="form-group validated col-12">
																<h3>Dados de Ecommerce</h3>
															</div>
															<div class="form-group validated col-lg-4 col-md-4 col-sm-10">
																<label class="col-form-label ">Categoria</label>

																<select id="categoria-select" class="custom-select form-control @if($errors->has('categoria_ecommerce_id')) is-invalid @endif" name="categoria_ecommerce_id">
																	@foreach($categoriasEcommerce as $c)
																	<option 
																	@if(old('categoria_ecommerce_id') == $c->id) selected @endif
																	value="{{$c->id}}">{{$c->nome}}</option>
																	@endforeach
																</select>

																@if($errors->has('categoria_ecommerce_id'))
																<div class="invalid-feedback">
																	{{ $errors->first('categoria_ecommerce_id') }}
																</div>
																@endif
															</div>

															<div class="form-group validated col-sm-4 col-lg-3">
																<label class="col-form-label">Valor</label>
																<div class="">
																	<input type="text" class="form-control @if($errors->has('valor_ecommerce')) is-invalid @endif money" name="valor_ecommerce" id="valor_ecommerce" value="{{old('valor_ecommerce')}}">
																	@if($errors->has('valor_ecommerce'))
																	<div class="invalid-feedback">
																		{{ $errors->first('valor_ecommerce') }}
																	</div>
																	@endif
																</div>
															</div>

															<div class="col col-sm-3 col-lg-3">
																<br>
																<label>Controlar estoque:</label>

																<div class="switch switch-outline switch-success">
																	<label class="">
																		<input value="true" name="controlar_estoque" class="red-text" type="checkbox">
																		<span class="lever"></span>
																	</label>
																</div>
															</div>

															<div class="col col-sm-3 col-lg-3">
																<br>
																<label>Ativo:</label>

																<div class="switch switch-outline switch-info">
																	<label class="">
																		<input value="true" name="status" class="red-text" type="checkbox">
																		<span class="lever"></span>
																	</label>
																</div>
															</div>

															<div class="col col-sm-3 col-lg-3">
																<br>
																<label>Destaque:</label>

																<div class="switch switch-outline switch-primary">
																	<label class="">
																		<input value="true" name="destaque" class="red-text" type="checkbox">
																		<span class="lever"></span>
																	</label>
																</div>
															</div>

															<div class="form-group validated col-sm-12 col-lg-12">
																<label class="col-form-label">Descrição</label>
																<div class="">

																	<div class="row">
																		<div class="col-12">
																			<textarea name="descricao" id="descricao" style="width: 800px;height:500px;">{{old('descricao')}}</textarea>
																		</div>
																	</div>

																	@if($errors->has('descricao'))
																	<div class="invalid-feedback">
																		{{ $errors->first('descricao') }}
																	</div>
																	@endif
																</div>
															</div>


														</div>
													</div>
													@endif

													<hr>
													<div class="form-group validated col-12">
														<h3>Dados Veiculo (Opcional)</h3>

														<span class="switch switch-outline switch-info">
															<label>
																<input @isset($produto) @if($produto->renavam != '') checked @endif @endisset id="tp_veiculo" value="true" class="red-text" type="checkbox">
																<span></span>
															</label>
														</span>
													</div>

													<div class="div_veiculo col-12" style="display: none">
														<div class="row">
															<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
																<label class="col-form-label">Renavam</label>

																<input type="text" id="renavam" class="form-control @if($errors->has('renavam')) is-invalid @endif" name="renavam" 
																value="{{{ isset($produto->renavam) ? $produto->renavam : old('renavam') }}}">
																@if($errors->has('renavam'))
																<div class="invalid-feedback">
																	{{ $errors->first('renavam') }}
																</div>
																@endif
															</div>

															<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
																<label class="col-form-label">Placa</label>

																<input type="text" id="placa" class="form-control @if($errors->has('placa')) is-invalid @endif" name="placa" 
																value="{{{ isset($produto->placa) ? $produto->placa : old('placa') }}}">
																@if($errors->has('placa'))
																<div class="invalid-feedback">
																	{{ $errors->first('placa') }}
																</div>
																@endif
															</div>

															<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
																<label class="col-form-label">Chassi</label>

																<input type="text" id="chassi" class="form-control @if($errors->has('chassi')) is-invalid @endif" name="chassi" 
																value="{{{ isset($produto->chassi) ? $produto->chassi : old('chassi') }}}">
																@if($errors->has('chassi'))
																<div class="invalid-feedback">
																	{{ $errors->first('chassi') }}
																</div>
																@endif
															</div>

															<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
																<label class="col-form-label">Combustível</label>

																<input type="text" id="combustivel" class="form-control @if($errors->has('combustivel')) is-invalid @endif" name="combustivel" 
																value="{{{ isset($produto->combustivel) ? $produto->combustivel : old('combustivel') }}}">
																@if($errors->has('combustivel'))
																<div class="invalid-feedback">
																	{{ $errors->first('combustivel') }}
																</div>
																@endif
															</div>

															<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
																<label class="col-form-label">Ano/Modelo</label>

																<input type="text" id="ano_modelo" class="form-control @if($errors->has('ano_modelo')) is-invalid @endif" name="ano_modelo" 
																value="{{{ isset($produto->ano_modelo) ? $produto->ano_modelo : old('ano_modelo') }}}">
																@if($errors->has('ano_modelo'))
																<div class="invalid-feedback">
																	{{ $errors->first('ano_modelo') }}
																</div>
																@endif
															</div>

															<div class="form-group validated col-lg-4 col-md-4 col-sm-4">
																<label class="col-form-label">cor</label>

																<input type="text" id="cor_veiculo" class="form-control @if($errors->has('cor_veiculo')) is-invalid @endif" name="cor_veiculo" 
																value="{{{ isset($produto->cor_veiculo) ? $produto->cor_veiculo : old('cor_veiculo') }}}">
																@if($errors->has('cor_veiculo'))
																<div class="invalid-feedback">
																	{{ $errors->first('cor_veiculo') }}
																</div>
																@endif
															</div>

														</div>

													</div>

												</div>

											</div>
										</div>

									</div>
								</div>
								<div class="pb-5" data-wizard-type="step-content">

									<div class="row">

										<div class="col-xl-2"></div>
										<div class="col-xl-8">

											<div class="row">
												
												<div class="form-group validated col-lg-10 col-md-10 col-sm-10">
													<label class="col-form-label text-left col-lg-12 col-sm-12">
														@if($tributacao->regime == 1)
														CST
														@else
														CSOSN
														@endif
													*</label>

													<select class="custom-select form-control" id="CST_CSOSN" name="CST_CSOSN">
														@foreach($listaCSTCSOSN as $key => $c)
														<option value="{{$key}}" @if($config !=null) @if(isset($produto)) @if($key==$produto->CST_CSOSN)
															selected
															@endif
															@else
															@if($key == $config->CST_CSOSN_padrao)
															selected
															@endif
															@endif

															@endif
															>{{$key}} - {{$c}}
														</option>
														@endforeach
													</select>

												</div>

												<div class="form-group validated col-lg-5 col-md-10 col-sm-10">
													<label class="col-form-label text-left col-lg-12 col-sm-12">CST PIS *</label>

													<select class="custom-select form-control" id="CST_CSOSN" name="CST_PIS">
														@foreach($listaCST_PIS_COFINS as $key => $c)
														<option value="{{$key}}" @if($config !=null) @if(isset($produto)) @if($key==$produto->CST_PIS)
															selected
															@endif
															@else
															@if($key == $config->CST_PIS_padrao)
															selected
															@endif
															@endif

															@endif
															>{{$key}} - {{$c}}
														</option>
														@endforeach
													</select>

												</div>

												<div class="form-group validated col-lg-5 col-md-10 col-sm-10">
													<label class="col-form-label text-left col-lg-12 col-sm-12">CST COFINS *</label>

													<select class="custom-select form-control" id="CST_CSOSN" name="CST_COFINS">
														@foreach($listaCST_PIS_COFINS as $key => $c)
														<option value="{{$key}}" @if($config !=null) @if(isset($produto)) @if($key==$produto->CST_COFINS)
															selected
															@endif
															@else
															@if($key == $config->CST_COFINS_padrao)
															selected
															@endif
															@endif

															@endif
															>{{$key}} - {{$c}}
														</option>
														@endforeach
													</select>

												</div>

												<div class="form-group validated col-lg-10 col-md-10 col-sm-10">
													<label class="col-form-label text-left col-lg-12 col-sm-12">CST IPI *</label>

													<select class="custom-select form-control" id="CST_IPI" name="CST_IPI">
														@foreach($listaCST_IPI as $key => $c)
														<option value="{{$key}}" @if($config !=null) @if(isset($produto)) @if($key==$produto->CST_IPI)
															selected
															@endif
															@else
															@if($key == $config->CST_IPI_padrao)
															selected
															@endif
															@endif

															@endif
															>{{$key}} - {{$c}}
														</option>
														@endforeach
													</select>

												</div>



												<div class="form-group validated col-lg-10 col-md-10 col-sm-10">
													<label class="col-form-label text-left col-lg-12 col-sm-12">
														@if($tributacao->regime == 1)
														CST Exportação
														@else
														CSOSN Exportação
														@endif
													*</label>

													<select class="custom-select form-control" id="CST_CSOSN_EXP" name="CST_CSOSN_EXP">
														<option value="">--</option>
														@foreach($listaCSTCSOSN as $key => $c)
														<option value="{{$key}}" @if(isset($produto)) @if($key==$produto->CST_CSOSN_EXP)
															selected
															@endif
															@endif

															>{{$key}} - {{$c}}
														</option>
														@endforeach
													</select>

												</div>

												<div class="form-group validated col-sm-4 col-lg-4">
													<label class="col-form-label">CFOP saida interno *</label>
													<div class="">
														<input type="text" id="CFOP_saida_estadual" class="form-control @if($errors->has('CFOP_saida_estadual')) is-invalid @endif" name="CFOP_saida_estadual" 
														value="{{{ isset($produto->CFOP_saida_estadual) ? $produto->CFOP_saida_estadual : $natureza->CFOP_saida_estadual }}}">
														@if($errors->has('CFOP_saida_estadual'))
														<div class="invalid-feedback">
															{{ $errors->first('CFOP_saida_estadual') }}
														</div>
														@endif
													</div>
												</div>
												<div class="form-group validated col-sm-4 col-lg-4">
													<label class="col-form-label">CFOP saida externo *</label>
													<div class="">
														<input type="text" id="CFOP_saida_inter_estadual" class="form-control @if($errors->has('CFOP_saida_inter_estadual')) is-invalid @endif" name="CFOP_saida_inter_estadual" 
														value="{{{ isset($produto->CFOP_saida_inter_estadual) ? $produto->CFOP_saida_inter_estadual : $natureza->CFOP_saida_inter_estadual }}}">
														@if($errors->has('CFOP_saida_inter_estadual'))
														<div class="invalid-feedback">
															{{ $errors->first('CFOP_saida_inter_estadual') }}
														</div>
														@endif
													</div>
												</div>

												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">%ICMS *</label>
													<div class="">
														<input type="text" id="perc_icms" class="form-control trib @if($errors->has('perc_icms')) is-invalid @endif" name="perc_icms" 
														value="{{{ isset($produto->perc_icms) ? $produto->perc_icms : $tributacao->icms }}}">
														@if($errors->has('perc_icms'))
														<div class="invalid-feedback">
															{{ $errors->first('perc_icms') }}
														</div>
														@endif
													</div>
												</div>
												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">%PIS *</label>
													<div class="">
														<input type="text" id="perc_pis" class="form-control trib @if($errors->has('perc_pis')) is-invalid @endif" name="perc_pis" 
														value="{{{ isset($produto->perc_pis) ? $produto->perc_pis : $tributacao->pis }}}">
														@if($errors->has('perc_pis'))
														<div class="invalid-feedback">
															{{ $errors->first('perc_pis') }}
														</div>
														@endif
													</div>
												</div>
												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">%COFINS *</label>
													<div class="">
														<input type="text" id="perc_cofins" class="form-control trib @if($errors->has('perc_cofins')) is-invalid @endif" name="perc_cofins" 
														value="{{{ isset($produto->perc_cofins) ? $produto->perc_cofins : $tributacao->cofins }}}">
														@if($errors->has('perc_cofins'))
														<div class="invalid-feedback">
															{{ $errors->first('perc_cofins') }}
														</div>
														@endif
													</div>
												</div>
												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">%IPI *</label>
													<div class="">
														<input type="text" id="perc_ipi" class="form-control trib @if($errors->has('perc_ipi')) is-invalid @endif" name="perc_ipi" 
														value="{{{ isset($produto->perc_ipi) ? $produto->perc_ipi : $tributacao->ipi }}}">
														@if($errors->has('perc_ipi'))
														<div class="invalid-feedback">
															{{ $errors->first('perc_ipi') }}
														</div>
														@endif
													</div>
												</div>

												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">%ISS*</label>
													<div class="">
														<input type="text" id="perc_iss" class="form-control trib @if($errors->has('perc_iss')) is-invalid @endif" name="perc_iss" 
														value="{{{ isset($produto->perc_iss) ? $produto->perc_iss : 0.00 }}}">
														@if($errors->has('perc_iss'))
														<div class="invalid-feedback">
															{{ $errors->first('perc_iss') }}
														</div>
														@endif
													</div>
												</div>

												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">%Redução BC *</label>
													<div class="">
														<input type="text" id="pRedBC" class="form-control @if($errors->has('pRedBC')) is-invalid @endif" name="pRedBC" 
														value="{{{ isset($produto->pRedBC) ? $produto->pRedBC : 0.00 }}}">
														@if($errors->has('pRedBC'))
														<div class="invalid-feedback">
															{{ $errors->first('pRedBC') }}
														</div>
														@endif
													</div>
												</div>

												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">Cod benefício</label>
													<div class="">
														<input type="text" id="cBenef" class="form-control @if($errors->has('cBenef')) is-invalid @endif" name="cBenef" 
														value="{{{ isset($produto->cBenef) ? $produto->cBenef : old('cBenef') }}}">
														@if($errors->has('cBenef'))
														<div class="invalid-feedback">
															{{ $errors->first('cBenef') }}
														</div>
														@endif
													</div>
												</div>
												<div class="col-xl-12"></div>
												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">%ICMS interestadual</label>
													<div class="">
														<input type="text" id="perc_icms_interestadual" class="form-control @if($errors->has('perc_icms_interestadual')) is-invalid @endif trib" name="perc_icms_interestadual" 
														value="{{{ isset($produto->perc_icms_interestadual) ? $produto->perc_icms_interestadual : old('perc_icms_interestadual') }}}">
														@if($errors->has('perc_icms_interestadual'))
														<div class="invalid-feedback">
															{{ $errors->first('perc_icms_interestadual') }}
														</div>
														@endif
													</div>
												</div>

												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">%ICMS interno</label>
													<div class="">
														<input type="text" id="perc_icms_interno" class="form-control @if($errors->has('perc_icms_interno')) is-invalid @endif trib" name="perc_icms_interno" 
														value="{{{ isset($produto->perc_icms_interno) ? $produto->perc_icms_interno : old('perc_icms_interno') }}}">
														@if($errors->has('perc_icms_interno'))
														<div class="invalid-feedback">
															{{ $errors->first('perc_icms_interno') }}
														</div>
														@endif
													</div>
												</div>

												<div class="form-group validated col-sm-3 col-lg-3">
													<label class="col-form-label">%FCP interestadual</label>
													<div class="">
														<input type="text" id="perc_fcp_interestadual" class="form-control @if($errors->has('perc_fcp_interestadual')) is-invalid @endif trib" name="perc_fcp_interestadual" 
														value="{{{ isset($produto->perc_fcp_interestadual) ? $produto->perc_fcp_interestadual : old('perc_fcp_interestadual') }}}">
														@if($errors->has('perc_fcp_interestadual'))
														<div class="invalid-feedback">
															{{ $errors->first('perc_fcp_interestadual') }}
														</div>
														@endif
													</div>
												</div>

											</div>
										</div>
									</div>
								</div>

								<input type="hidden" id="subs" value="{{json_encode($subs)}}">
								<input type="hidden" id="divisoes" value="{{json_encode($divisoes)}}" name="">
								<input type="hidden" id="subDivisoes" value="{{json_encode($subDivisoes)}}" name="">

								<input type="hidden" id="combinacoes" value="{{old('combinacoes')}}" name="combinacoes">


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
							</div>

						</form>
					</div>
				</div>
				<!-- end nav -->


			</form>
		</div>
	</div>
</div>
</div>

<div class="modal fade" id="modal-grade1" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Escolha as combinações</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div style="margin-top: 15px;">
						<h3>Divisões</h3>
						<div class="divisoes">
							
						</div>
					</div>
				</div>

				<hr>

				<div class="row">
					<div style="margin-top: 5px;">
						<h3>Subdivisões</h3>
						<div class="subDivisoes">
							
						</div>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button style="width: 100%" type="button" onclick="escolhaDivisao()" class="btn btn-success font-weight-bold">OK</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-grade2" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Preencha os campos das combinações</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<div class="modal-body modal-body-grade">
				<div class="row">
					<div style="margin-top: 15px;">
						<div class="combinacoes">

						</div>
					</div>
				</div>


			</div>

			<div class="modal-footer">
				<button style="width: 100%" type="button" onclick="finalizarGrade()" class="btn btn-success font-weight-bold">OK</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-categoria" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Nova Categoria</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<div class="modal-body">

				<div class="row">
					<div class="col-xl-12">

						
						<div class="row">

							<div class="form-group validated col-12">
								<label class="col-form-label" id="lbl_cpf_cnpj">Nome</label>
								<div class="">
									<input type="text" id="nome_categoria" class="form-control @if($errors->has('nome')) is-invalid @endif" name="nome">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" id="btn-frete" class="btn btn-danger font-weight-bold spinner-white spinner-right" data-dismiss="modal" aria-label="Close">Fechar</button>
				<button type="button" onclick="salvarCategoria()" class="btn btn-success font-weight-bold spinner-white spinner-right">Salvar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-marca" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Nova Marca</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					x
				</button>
			</div>
			<div class="modal-body">

				<div class="row">
					<div class="col-xl-12">

						
						<div class="row">

							<div class="form-group validated col-12">
								<label class="col-form-label" id="lbl_cpf_cnpj">Nome</label>
								<div class="">
									<input type="text" id="nome_marca" class="form-control">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" id="btn-frete" class="btn btn-danger font-weight-bold spinner-white spinner-right" data-dismiss="modal" aria-label="Close">Fechar</button>
				<button type="button" onclick="salvarMarca()" class="btn btn-success font-weight-bold spinner-white spinner-right">Salvar</button>
			</div>
		</div>
	</div>
</div>
@section('javascript')
<script type="text/javascript">

	$(function () {
		let is = $('.ecommerce').is(':checked');
		if(is){
			$('.div-ecommmerce').css('display', 'block')
		}else{
			$('.div-ecommmerce').css('display', 'none')
		}
		tpVeiculo();

	});
	$('.ecommerce').change((target) => {
		let is = $('.ecommerce').is(':checked');
		if(is){
			$('.div-ecommmerce').css('display', 'block')
		}else{
			$('.div-ecommmerce').css('display', 'none')

		}
	})

	function novaCategoria(){
		$('#modal-categoria').modal('show')

	}

	function novaMarca(){
		$('#modal-marca').modal('show')

	}

	function salvarCategoria(){
		let nome = $('#nome_categoria').val()
		if(!nome){
			swal("Erro", "Informe nome", "warning")
		}else{
			let token = $('#_token').val();
			$.post(path + 'categorias/quickSave',
			{
				_token: token,
				nome: nome
			})
			.done((res) =>{

				console.log(res)
				$('#categoria').append('<option value="'+res.id+'">'+ 
					res.nome+'</option>').change();
				$('#categoria').val(res.id).change();
				swal("Sucesso", "Categoria adicionada!!", 'success')
				.then(() => {
					$('#modal-categoria').modal('hide')
				})
			})
			.fail((err) => {
				console.log(err)
				swal("Erro", "Algo deu errado!!", 'error')

			})
		}
	}

	function salvarMarca(){
		let nome = $('#nome_marca').val()
		if(!nome){
			swal("Erro", "Informe nome", "warning")
		}else{
			let token = $('#_token').val();
			$.post(path + 'marcas/quickSave',
			{
				_token: token,
				nome: nome
			})
			.done((res) =>{

				console.log(res)
				$('#marca').append('<option value="'+res.id+'">'+ 
					res.nome+'</option>').change();
				$('#marca').val(res.id).change();
				swal("Sucesso", "Marca adicionada!!", 'success')
				.then(() => {
					$('#modal-marca').modal('hide')
				})
			})
			.fail((err) => {
				console.log(err)
				swal("Erro", "Algo deu errado!!", 'error')

			})
		}
	}

	$('#tp_veiculo').change(() => {
		tpVeiculo();
	})

	function tpVeiculo(){
		if($('#tp_veiculo').is(':checked')){
			$('.div_veiculo').css('display', 'block')
		}else{
			$('.div_veiculo').css('display', 'none')
		}
	}

</script>
@endsection
@endsection