@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">


	<div class="card-body">
		<div class="@if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-sm-12 col-lg-4 col-md-6 col-xl-4">

				<a href="/divisaoGrade/new" class="btn btn-lg btn-success">
					<i class="fa fa-plus"></i>Nova Divisão Grade
				</a>
			</div>
		</div>
		<br>
		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">
			<br>

			<div class="row @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight">

				@foreach($divisoes as $c)


				<div class="col-sm-12 col-lg-6 col-md-6 col-xl-4">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{$c->nome}} 
								@if($c->sub_divisao)
								<span style="margin-left: 5px;" class="label label-xl label-inline label-light-info">Subdivisão</span>
								@else
								<span style="margin-left: 5px;" class="label label-xl label-inline label-light-success">Divisão</span>
								@endif
							</h3>
							<div class="card-toolbar">

								<a href="/divisaoGrade/edit/{{$c->id}}" class="btn btn-icon btn-circle btn-sm btn-light-primary mr-1"><i class="la la-pencil"></i></a>
								<a onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/divisaoGrade/delete/{{$c->id}}" }else{return false} })' href="#!" class="btn btn-icon btn-circle btn-sm btn-light-danger mr-1"><i class="la la-trash"></i></a>

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