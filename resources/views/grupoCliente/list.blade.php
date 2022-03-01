@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">
	<div class="card-body">
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-sm-12 col-lg-4 col-md-6 col-xl-4">

				<a href="/gruposCliente/new" class="btn btn-lg btn-success">
					<i class="fa fa-plus"></i>Novo Grupo de Cliente
				</a>
			</div>
		</div>
		<br>
		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">
			<br>

			<div class="row @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				@foreach($grupos as $g)

				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-4">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{$g->nome}}
							</h3>
							<div class="card-toolbar">

								<a href="/gruposCliente/edit/{{$g->id}}" class="btn btn-icon btn-circle btn-sm btn-light-primary mr-1"><i class="la la-pencil"></i></a>
								<a href="/gruposCliente/delete/{{$g->id}}" class="btn btn-icon btn-circle btn-sm btn-light-danger mr-1"><i class="la la-trash"></i></a>
								<a href="/gruposCliente/list/{{$g->id}}" class="btn btn-icon btn-circle btn-sm btn-light-info mr-1"><i class="la la-list"></i></a>

							</div>
						</div>

					</div>

				</div>

				@endforeach

			</div>
		</div>
	</div>
</div>

@endsection