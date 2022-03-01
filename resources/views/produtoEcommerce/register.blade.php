@extends('default.layout')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="{{{ isset($produto) ? '/produtoEcommerce/update': '/produtoEcommerce/save' }}}" enctype="multipart/form-data">
					<input type="hidden" name="id" value="{{{ isset($produto->id) ? $produto->id : 0 }}}">


					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{isset($produto) ? 'Editar' : 'Novo'}} Produto</h3>
						</div>

					</div>
					@csrf

					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">
									<p class="text-danger">>> O produto de ecommerce depende do produto principal, isso é necessário para baixa de estoque e emissão fiscal</p>
									<div class="row">
										<div @if(old('produto') != "") style="display: none" @endif id="ref-prod" class="form-group validated col-sm-7 col-lg-7 col-10">
											<label class="col-form-label" id="">Produto</label><br>
											<select  @isset($produto) disabled @endisset class="form-control select2 @if($errors->has('produto_id')) is-invalid @endif" style="width: 100%" id="kt_select2_1" name="produto_id">
												<option value="null">Selecione o produto</option>
												@foreach($produtos as $p)
												<option 
												@if(isset($produto))
												@if($p->id == $produto->produto->id)
												selected
												@endif

												@else

												@if(old('produto_id') == $p->id)
												selected
												@endif

												@endif
												value="{{$p->id}}">{{$p->id}} - {{$p->nome}}</option>
												@endforeach
											</select>
											@if($errors->has('produto_id'))
											<div class="invalid-feedback">
												{{ $errors->first('produto_id') }}
											</div>
											@endif

											
										</div>

										<div @if(old('produto') == "") style="display: none" @endif id="novo-prod" class="form-group validated col-sm-7 col-lg-7 col-10">
											<label class="col-form-label">Nome do Produto</label>
											<div class="">
												<input value="{{old('produto')}}" type="text" class="form-control @if($errors->has('produto')) is-invalid @endif" name="produto" id="produto" >
												@if($errors->has('produto'))
												<div class="invalid-feedback">
													{{ $errors->first('produto') }}
												</div>
												@endif
											</div>
										</div>

										@if(!isset($produto))
										<div class="col-lg-1 col-md-1 col-sm-1 col-2">
											<br>
											<a id="novo-produto" style="margin-top: 18px;" class="btn btn-success">
												<i class="la la-plus"></i>
											</a>
										</div>
										@endif

										<div class="form-group validated col-lg-4 col-md-4 col-sm-10">
											<label class="col-form-label ">Categoria</label>

											<select id="categoria-select" class="custom-select form-control" name="categoria_id">
												@foreach($categorias as $c)
												<option


												@if($c->id == old('categoria_id'))
												selected=""
												@endif

												@isset($produto)
												@if($c->id == $produto->categoria_id)
												selected=""
												@endif
												@endisset
												value="{{$c->id}}">{{$c->nome}}</option>
												@endforeach
											</select>

											@if($errors->has('categoria'))
											<div class="invalid-feedback">
												{{ $errors->first('categoria') }}
											</div>
											@endif
										</div>
									</div>
								</div>



								<div class="row">

									<div class="form-group validated col-sm-4 col-lg-3">
										<label class="col-form-label">Valor</label>
										<div class="">
											<input type="text" class="form-control @if($errors->has('valor')) is-invalid @endif" name="valor" id="valor" value="{{{ isset($produto) ? $produto->valor : old('valor') }}}">
											@if($errors->has('valor'))
											<div class="invalid-feedback">
												{{ $errors->first('valor') }}
											</div>
											@endif
										</div>
									</div>

									<div class="form-group validated col-sm-4 col-lg-3">
										<label class="col-form-label">% desconto exibição</label>
										<div class="">
											<input data-mask="00" type="text" class="form-control @if($errors->has('percentual_desconto_view')) is-invalid @endif" name="percentual_desconto_view" id="percentual_desconto_view" value="{{{ isset($produto) ? $produto->percentual_desconto_view : old('percentual_desconto_view') }}}">
											@if($errors->has('percentual_desconto_view'))
											<div class="invalid-feedback">
												{{ $errors->first('percentual_desconto_view') }}
											</div>
											@endif
										</div>
									</div>

									<div class="col col-sm-3 col-lg-3">
										<br>
										<label>Controlar estoque:</label>

										<div class="switch switch-outline switch-success">
											<label class="">
												<input @if(isset($produto->controlar_estoque) && $produto->controlar_estoque) checked @else
												@if(old('controlar_estoque')) checked @endif @endif value="true" name="controlar_estoque" class="red-text" type="checkbox">
												<span class="lever"></span>
											</label>
										</div>
									</div>

									<div class="col col-sm-3 col-lg-3">
										<br>
										<label>Ativo:</label>

										<div class="switch switch-outline switch-info">
											<label class="">
												<input @if(isset($produto->status) && $produto->status) checked @else
												@if(old('status')) checked @endif @endif value="true" name="status" class="red-text" type="checkbox">
												<span class="lever"></span>
											</label>
										</div>
									</div>

									<div class="col col-sm-3 col-lg-3">
										<br>
										<label>Destaque:</label>

										<div class="switch switch-outline switch-primary">
											<label class="">
												<input @if(isset($produto->destaque) && $produto->destaque) checked @else
												@if(old('destaque')) checked @endif @endif value="true" name="destaque" class="red-text" type="checkbox">
												<span class="lever"></span>
											</label>
										</div>
									</div>

									<div class="form-group validated col-lg-3 col-md-3 col-sm-4">
										<label class="col-form-label">Largura (cm)</label>

										<input type="text" id="largura" class="form-control @if($errors->has('largura')) is-invalid @endif" name="largura" 
										value="{{{ isset($produto) ? $produto->produto->largura : old('largura') }}}">
									</div>

									<div class="form-group validated col-lg-3 col-md-3 col-sm-4">
										<label class="col-form-label">Altura (cm)</label>

										<input type="text" id="altura" class="form-control @if($errors->has('altura')) is-invalid @endif" name="altura" 
										value="{{{ isset($produto) ? $produto->produto->altura : old('altura') }}}">
									</div>

									<div class="form-group validated col-lg-3 col-md-3 col-sm-4">
										<label class="col-form-label">Comprimento (cm)</label>

										<input type="text" id="comprimento" class="form-control @if($errors->has('comprimento')) is-invalid @endif" name="comprimento" value="{{{ isset($produto) ? $produto->produto->comprimento : old('comprimento') }}}">
									</div>


									<div class="form-group validated col-lg-3 col-md-3 col-sm-4">
										<label class="col-form-label">Peso liquido</label>

										<input type="text" id="peso_liquido" class="form-control @if($errors->has('peso_liquido')) is-invalid @endif" name="peso_liquido" 
										value="{{{ isset($produto) ? $produto->produto->peso_liquido : old('peso_liquido') }}}">
									</div>

									<div class="form-group validated col-lg-3 col-md-3 col-sm-4">
										<label class="col-form-label">Peso bruto</label>

										<input type="text" id="peso_bruto" class="form-control @if($errors->has('peso_bruto')) is-invalid @endif" name="peso_bruto" 
										value="{{{ isset($produto) ? $produto->produto->peso_bruto : old('peso_bruto') }}}">
									</div>

									@if(getenv("CEP_PRODUTO_ECOMMERCE") == 1)
									<div class="form-group validated col-lg-3 col-md-3 col-sm-4">
										<label class="col-form-label">CEP</label>

										<input type="text" id="cep" class="form-control @if($errors->has('cep')) is-invalid @endif" name="cep" 
										value="{{{ isset($produto) ? $produto->cep : old('cep') }}}">
									</div>
									@endif

								</div>


								<div class="row">
									<div class="form-group validated col-sm-12 col-lg-12">
										<label class="col-form-label">Descrição</label>
										<div class="">

											<div class="row">
												<div class="col-12">
													<textarea name="descricao" id="descricao" style="width:100%;height:500px;">{{isset($produto) ? $produto->descricao : old('descricao')}}</textarea>
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

								@if(!isset($produto))
								<div class="row">
									<div class="form-group validated col-sm-4 col-lg-4 col-6">
										<label class="col-xl-12 col-lg-12 col-form-label text-left">Imagem </label>
										<div class="col-lg-10 col-xl-6">

											<div class="image-input image-input-outline" id="kt_image_1">
												<div class="image-input-wrapper"
												@if(isset($produto) && file_exists(public_path('ecommerce/produtos/').$produto->galeria[0]->path)) style="background-image: url(/ecommerce/produtos/{{$produto->galeria[0]->img}})" @else style="background-image: url(/imgs/no_image.png)" @endif
												></div>
												<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
													<i class="fa fa-pencil icon-sm text-muted"></i>
													<input type="file" name="file" accept=".png, .jpg">
													<input type="hidden" name="profile_avatar_remove">
												</label>
												<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Cancel avatar">
													<i class="fa fa-close icon-xs text-muted"></i>
												</span>
											</div>


											<span class="form-text text-muted">.png</span>
											@if($errors->has('file'))
											<div class="invalid-feedback">
												{{ $errors->first('file') }}
											</div>
											@endif
											<span class="text-danger">*Recomendado 600x600</span>
										</div>
									</div>
								</div>
								@endif

							</div>

						</div>
					</div>
					<div class="card-footer">

						<div class="row">
							<div class="col-xl-2">

							</div>
							<div class="col-lg-3 col-sm-6 col-md-4">
								<a style="width: 100%" class="btn btn-danger" href="/deliveryCategoria">
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
<script type="text/javascript">
	var PRODUTONOVO = false;
	$('#kt_select2_1').change(() => {
		let uri = window.location.pathname;
		if(uri.split('/')[2] != 'apontamentoManual' && uri.split('/')[2] != 'receita'){
			let id = $('#kt_select2_1').val()
			getProduto(id, (data) => {
				if(!data.ecommerce){
					$('#valor').val(parseFloat(data.valor_venda).toFixed(casas_decimais))
					$('#largura').val(data.largura)
					$('#altura').val(data.altura)
					$('#comprimento').val(data.comprimento)
					$('#peso_liquido').val(data.peso_liquido)
					$('#peso_bruto').val(data.peso_bruto)

					console.log(data)
				}else{
					swal('Erro', 'Este produto já possui cadastro no ecommerce', 'error')
					$('#kt_select2_1').val('null').change();
				}
			})
		}
	})

	function getProduto(id, data){
		$.ajax
		({
			type: 'GET',
			url: path + 'produtos/getProduto/'+id,
			dataType: 'json',
			success: function(e){
				data(e)
			}, error: function(e){
				console.log(e)
			}

		});
	}

	$('#novo-produto').click(() => {
		if(!PRODUTONOVO){
			$('#novo-prod').css('display', 'block')
			$('#ref-prod').css('display', 'none')
		}else{
			$('#novo-prod').css('display', 'none')
			$('#ref-prod').css('display', 'block')
		}

		PRODUTONOVO = !PRODUTONOVO
	})

</script>
@endsection
@endsection