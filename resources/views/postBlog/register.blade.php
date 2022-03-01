@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="{{{ isset($post) ? '/postBlog/update': '/postBlog/save' }}}" enctype="multipart/form-data">


					<input type="hidden" name="id" value="{{{ isset($post) ? $post->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{isset($post) ? 'Editar' : 'Novo'}} Post</h3>
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
											<label class="col-form-label">Título</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('titulo')) is-invalid @endif" name="titulo" value="{{{ isset($post) ? $post->titulo : old('titulo') }}}">
												@if($errors->has('titulo'))
												<div class="invalid-feedback">
													{{ $errors->first('titulo') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label">Categoria</label>
											<div class="">
												<select name="categoria_id" class="custom-select @if($errors->has('categoria_id')) is-invalid @endif">
													@foreach($categorias as $c)
													<option 
													@isset($post)
													@if($post->categoria_id == $c->id)
													selected
													@endif

													@else
													@if(old('categoria_id') == $c->id)
													selected
													@endif
													@endif
													value="{{$c->id}}">
													{{$c->nome}}</option>
													@endforeach
												</select>
												@if($errors->has('categoria_id'))
												<div class="invalid-feedback">
													{{ $errors->first('categoria_id') }}
												</div>
												@endif


											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-4 col-lg-4">
											<label class="col-form-label">Autor</label>
											<div class="">
												<select name="autor_id" class="custom-select @if($errors->has('autor_id')) is-invalid @endif">
													@foreach($autores as $a)
													<option 
													@isset($post)
													@if($post->autor_id == $a->id)
													selected
													@endif
													@else
													
													@if(old('autor_id') == $a->id)
													selected
													@endif
													@endif
													value="{{$a->id}}">{{$a->nome}}</option>
													@endforeach
												</select>
												@if($errors->has('autor_id'))
												<div class="invalid-feedback">
													{{ $errors->first('autor_id') }}
												</div>
												@endif
											</div>
										</div>

										<div class="form-group validated col-sm-6 col-lg-6">
											<label class="col-form-label">Tags</label>
											<div class="">
												<input type="text" class="form-control @if($errors->has('tags')) is-invalid @endif" name="tags" value="{{{ isset($post) ? $post->tags : old('tags') }}}">
												@if($errors->has('tags'))
												<div class="invalid-feedback">
													{{ $errors->first('tags') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group validated col-sm-12 col-lg-12">
											<label class="col-form-label">Texto</label>
											<div class="">

												<div class="row">
													<div class="col-12">
														<textarea name="texto" id="descricao" style="width:100%;height:500px;">{{isset($post) ? $post->texto : old('texto')}}</textarea>
													</div>
												</div>

												@if($errors->has('texto'))
												<div class="invalid-feedback">
													{{ $errors->first('texto') }}
												</div>
												@endif
											</div>
										</div>
									</div>

									<div>
										<div style="background-image: url(/imgs/no_image.png)">

										</div>

										<div class="form-group row">
											<label class="col-xl-12 col-lg-12 col-form-label text-left">Imagem</label>
											<div class="col-lg-10 col-xl-6">

												<div class="image-input image-input-outline" id="kt_image_1">
													<div class="image-input-wrapper" @if(isset($post) && file_exists(public_path('ecommerce/posts/').$post->img)) style="background-image: url(/ecommerce/posts/{{$post->img}})" @else style="background-image: url(/imgs/no_image.png)" @endif></div>
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
												<div class="text-danger">
													{{ $errors->first('file') }}
												</div>
												@endif
												<span class="text-danger">*Recomendado 800x400</span>
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
								<a style="width: 100%" class="btn btn-danger" href="/categoriaEcommerce">
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