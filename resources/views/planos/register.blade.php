@extends('default.layout')
@section('content')


<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="{{{ isset($plano) ? '/planos/update': '/planos/save' }}}" enctype="multipart/form-data">

					<input type="hidden" name="id" value="{{{ isset($plano) ? $plano->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{isset($plano) ? 'Editar' : 'Novo'}} Plano</h3>
						</div>

					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">


									<div class="row">
										<div class="form-group validated col-sm-5 col-lg-5">
											<label class="col-form-label">Nome</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('nome')) is-invalid @endif" name="nome" value="{{{ isset($plano) ? $plano->nome : old('nome') }}}">
												@if($errors->has('nome'))
												<div class="invalid-feedback">
													{{ $errors->first('nome') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Valor</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('valor')) is-invalid @endif money" name="valor" value="{{{ isset($plano) ? $plano->valor : old('valor') }}}">
												@if($errors->has('valor'))
												<div class="invalid-feedback">
													{{ $errors->first('valor') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Intervalo (dias)</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('intervalo_dias')) is-invalid @endif" name="intervalo_dias" value="{{{ isset($plano) ? $plano->intervalo_dias : old('intervalo_dias') }}}">
												@if($errors->has('intervalo_dias'))
												<div class="invalid-feedback">
													{{ $errors->first('intervalo_dias') }}
												</div>
												@endif
											</div>
										</div>
									</div>
									<p class="text-danger">-1 = Infinito</p>

									<div class="row">
										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Max. Clientes</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('maximo_clientes')) is-invalid @endif" name="maximo_clientes" value="{{{ isset($plano) ? $plano->maximo_clientes : old('maximo_clientes') }}}">
												@if($errors->has('maximo_clientes'))
												<div class="invalid-feedback">
													{{ $errors->first('maximo_clientes') }}
												</div>
												@endif
											</div>
										</div>
										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Max. Produtos</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('maximo_produtos')) is-invalid @endif" name="maximo_produtos" value="{{{ isset($plano) ? $plano->maximo_produtos : old('maximo_produtos') }}}">
												@if($errors->has('maximo_produtos'))
												<div class="invalid-feedback">
													{{ $errors->first('maximo_produtos') }}
												</div>
												@endif
											</div>
										</div>
										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Max. Fornecedores</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('maximo_fornecedores')) is-invalid @endif" name="maximo_fornecedores" value="{{{ isset($plano) ? $plano->maximo_fornecedores : old('maximo_fornecedores') }}}">
												@if($errors->has('maximo_fornecedores'))
												<div class="invalid-feedback">
													{{ $errors->first('maximo_fornecedores') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Max. NFe</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('maximo_nfes')) is-invalid @endif" name="maximo_nfes" value="{{{ isset($plano) ? $plano->maximo_nfes : old('maximo_nfes') }}}">
												@if($errors->has('maximo_nfes'))
												<div class="invalid-feedback">
													{{ $errors->first('maximo_nfes') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Max. NFCe</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('maximo_nfces')) is-invalid @endif" name="maximo_nfces" value="{{{ isset($plano) ? $plano->maximo_nfces : old('maximo_nfces') }}}">
												@if($errors->has('maximo_nfces'))
												<div class="invalid-feedback">
													{{ $errors->first('maximo_nfces') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Max. CTe</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('maximo_cte')) is-invalid @endif" name="maximo_cte" value="{{{ isset($plano) ? $plano->maximo_cte : old('maximo_cte') }}}">
												@if($errors->has('maximo_cte'))
												<div class="invalid-feedback">
													{{ $errors->first('maximo_cte') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Max. MDFe</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('maximo_mdfe')) is-invalid @endif" name="maximo_mdfe" value="{{{ isset($plano) ? $plano->maximo_mdfe : old('maximo_mdfe') }}}">
												@if($errors->has('maximo_mdfe'))
												<div class="invalid-feedback">
													{{ $errors->first('maximo_mdfe') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Max. Usuários</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('maximo_usuario')) is-invalid @endif" name="maximo_usuario" value="{{{ isset($plano) ? $plano->maximo_usuario : old('maximo_usuario') }}}">
												@if($errors->has('maximo_usuario'))
												<div class="invalid-feedback">
													{{ $errors->first('maximo_usuario') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Max. Usuários Logados</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('maximo_usuario_simultaneo')) is-invalid @endif" name="maximo_usuario_simultaneo" value="{{{ isset($plano) ? $plano->maximo_usuario_simultaneo : old('maximo_usuario_simultaneo') }}}">
												@if($errors->has('maximo_usuario_simultaneo'))
												<div class="invalid-feedback">
													{{ $errors->first('maximo_usuario_simultaneo') }}
												</div>
												@endif
											</div>
										</div>

										@if(getenv("EVENTO") == 1)
										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Max. Evento</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('maximo_evento')) is-invalid @endif" name="maximo_evento" value="{{{ isset($plano) ? $plano->maximo_evento : old('maximo_evento') }}}">
												@if($errors->has('maximo_evento'))
												<div class="invalid-feedback">
													{{ $errors->first('maximo_evento') }}
												</div>
												@endif
											</div>
										</div>
										@endif

										@if(getenv("DELIVERY") == 1)
										<div class="form-group validated col-sm-6 col-lg-3">
											<label class="col-form-label text-left col-lg-12 col-sm-12">Delivery</label>
											<div class="col-6">
												<span class="switch switch-outline switch-primary">
													<label>
														<input id="adm" @if(isset($plano->delivery) && $plano->delivery) checked @endisset
														name="delivery" type="checkbox" >
														<span></span>
													</label>
												</span>

											</div>
										</div>
										@endif

										<div class="form-group validated col-sm-3 col-lg-3">
											<label class="col-form-label">Perfil</label>
											<div class="">
												<select name="perfil_id" class="form-control custom-select">
													@foreach($perfis as $p)
													<option @isset($plano) @if($p->id == $plano->perfil_id) selected @endif @endisset value="{{$p->id}}">
														{{$p->nome}}
													</option>
													@endforeach
												</select>
											</div>
										</div>

										<div class="form-group validated col-sm-2 col-lg-2">
											<label class="col-form-label">Visivel</label>

											<div class="switch switch-outline switch-info">
												<label class="">
													<input @if(isset($plano) && $plano->visivel) checked @endisset value="true" name="visivel" class="red-text" type="checkbox">
													<span class="lever"></span>
												</label>
											</div>
										</div>

										<div class="form-group validated col-sm-3 col-lg-4">
											<label class="col-form-label">Armazenamento (Mb)</label>
											<button type="button" class="btn btn-light-info btn-sm btn-icon col-lg-6 col-sm-6" data-toggle="popover" data-trigger="click" data-content="Se diferente de zero o controle será definido por armazenamento"><i class="la la-info"></i></button>
											<div class="">
												<input type="text" class="form-control @if($errors->has('armazenamento')) is-invalid @endif" name="armazenamento" value="{{{ isset($plano) ? $plano->armazenamento : old('armazenamento') }}}">
												@if($errors->has('armazenamento'))
												<div class="invalid-feedback">
													{{ $errors->first('armazenamento') }}
												</div>
												@endif
											</div>
										</div>

									</div>

									<div class="row">
										<div class="col-12">
											<textarea name="descricao" id="area" style="width:100%;height:500px;">@isset($plano) {{$plano->descricao}} @endisset</textarea>

											@if($errors->has('descricao'))
											<div class="invalid-feedback">
												{{ $errors->first('descricao') }}
											</div>
											@endif
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-lg-12 col-md-12 col-sm-12">
											<label class="col-xl-12 col-lg-12 col-form-label text-left">Imagem</label>
											<div class="col-lg-12 col-xl-12">

												<div class="image-input image-input-outline" id="kt_image_1">
													<div class="image-input-wrapper" @if(!isset($plano) || $plano->img == '') style="background-image: url(/imgs_planos/sem_imagem.png)" @else
														style="background-image: url(/imgs_planos/{{$plano->img}})"
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
								<a style="width: 100%" class="btn btn-danger" href="/planos">
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