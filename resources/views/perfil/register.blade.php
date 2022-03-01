@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="{{{ isset($perfil) ? '/perfilAcesso/update': '/perfilAcesso/save' }}}" enctype="multipart/form-data">


					<input type="hidden" name="id" value="{{{ isset($perfil) ? $perfil->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">
							<h3 class="card-title">{{isset($perfil) ? 'Editar' : 'Novo'}} Perfil</h3>
						</div>
					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">

									<div class="row">
										<div class="form-group validated col-sm-6 col-lg-6">
											<label class="col-form-label">Nome</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('nome')) is-invalid @endif" name="nome" value="{{{ isset($perfil) ? $perfil->nome : old('nome') }}}">
												@if($errors->has('nome'))
												<div class="invalid-feedback">
													{{ $errors->first('nome') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-12">

											<label class="col-3 col-form-label">Permissão de Acesso:</label>
											<input type="hidden" id="menus" value="{{json_encode($menu)}}" name="">
											@foreach($menu as $m)
											<div class="col-12 col-form-label">
												<span>
													<label class="checkbox checkbox-info">
														<input id="todos_{{$m['titulo']}}" onclick="marcarTudo('{{$m['titulo']}}')" type="checkbox" >
														<span></span><strong class="text-info" style="margin-left: 5px; font-size: 16px;">{{strtoupper($m['titulo'])}} </strong>
													</label>
												</span>
												<div class="checkbox-inline" style="margin-top: 10px;">
													@foreach($m['subs'] as $s)

													@if($s['nome'] != 'NFS-e')

													@php
													$link = str_replace('/', '', $s['rota']);
													$link = str_replace('.', '_', $link);
													$link = str_replace(':', '_', $link);
													@endphp
													<!-- <label class="checkbox checkbox-info check-sub">
														<input id="sub_{{str_replace('/', 	'', $s['rota'])}}" @if(in_array($s['rota'], $permissoesAtivas)) checked @endif type="checkbox" name="{{$s['rota']}}">
														<span></span>{{$s['nome']}}
													</label> -->
													<label class="checkbox checkbox-info check-sub">
														<input id="sub_{{$link}}" @if(\App\Models\Empresa::validaLink($s['rota'], $permissoesAtivas)) checked @endif type="checkbox" name="{{$s['rota']}}">
														<span></span>{{$s['nome']}}
													</label>
													@endif
													@endforeach
												</div>

											</div>
											@endforeach
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
								<a style="width: 100%" class="btn btn-danger" href="/perfilAcesso">
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

@section('javascript')
<script type="text/javascript" src="/js/perfil.js"></script>
@endsection

@endsection