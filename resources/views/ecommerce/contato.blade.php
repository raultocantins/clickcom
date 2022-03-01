@extends('ecommerce.default')
@section('content')
<style type="text/css">
	.text-danger{
		margin-top: -40px;
	}
</style>

<section class="contact spad">
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-6 text-center">
				<div class="contact__widget">
					<span class="icon_phone"></span>
					<h4>Telefone</h4>
					<p>{{$default['config']->telefone}}</p>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-6 text-center">
				<div class="contact__widget">
					<span class="icon_pin_alt"></span>
					<h4>Endereço</h4>
					<p>
						{{$default['config']->rua}}, {{$default['config']->numero}}
						- {{$default['config']->bairro}}
					</p>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-6 text-center">
				<div class="contact__widget">
					<span class="icon_clock_alt"></span>
					<h4>Funcionamento</h4>
					<p>{{$default['config']->funcionamento}}</p>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-6 text-center">
				<div class="contact__widget">
					<span class="icon_mail_alt"></span>
					<h4>Email</h4>
					<p>{{$default['config']->email}}</p>
				</div>
			</div>
		</div>
	</div>
</section>

@if($default['config']->src_mapa != "")
<div class="map">
	<iframe
	src="{{$default['config']->src_mapa}}"
	height="500" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
	<div class="map-inside">
		<i class="icon_pin"></i>
		<div class="inside-widget">
			<h4>{{$default['config']->cidade}}</h4>
			<ul>
				<li>Telefone: {{$default['config']->telefone}}</li>
				<li>Endereço: {{$default['config']->rua}}, {{$default['config']->numero}}
					- {{$default['config']->bairro}}
				</li>
			</ul>
		</div>
	</div>
</div>
@endif

<div class="contact-form spad" id="form-contato">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="contact__form__title">
					<h2>Deixe uma mensagem</h2>
				</div>
			</div>
		</div>

		@if(sizeof($errors) > 0)
		<div class="container">
			<div class="alert alert-custom alert-danger fade show" role="alert" style="margin-top: 10px;">

				<div class="alert-text"><i class="fa fa-check"></i> 
					Erro no forumulário
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<i class="fa fa-close"></i>
					</button>
				</div>

			</div>
		</div>
		@endif
		<form method="post" action="/ecommerceContato">
			@csrf
			<input type="hidden" value="{{$default['config']->empresa_id}}" name="empresa_id">
			<div class="row">
				<div class="col-lg-6 col-md-6">
					
					<input @if(sizeof($errors) > 0) autofocus @endif type="text" name="nome" value="{{old('nome')}}" placeholder="Nome">
					@if($errors->has('nome'))
					<label class="text-danger">{{ $errors->first('nome') }}</label>
					@endif
				</div>
				<div class="col-lg-6 col-md-6">
					
					<input type="email" name="email" value="{{old('email')}}" placeholder="Email">
					@if($errors->has('email'))
					<span class="text-danger">{{ $errors->first('email') }}</span>
					@endif
				</div>
				<div class="col-lg-12">
					
					<textarea name="texto" placeholder="Mensagem">{{old('texto')}}</textarea>
					@if($errors->has('texto'))
					<span class="text-danger">{{ $errors->first('texto') }}</span>
					@endif
					<br>
					<button type="submit" class="site-btn">Enviar</button>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- Blog Section End -->

@endsection	
