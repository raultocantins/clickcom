@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">


	<div class="card-body">
		<div class="row">
			<h2 class="center-align">Selecione a tela</h2>

			<div class="col-sm-6 col-lg-6 col-md-6 col-xl-6">

				<div class="progresso" style="display: none">
					<div class="spinner spinner-track spinner-primary spinner-lg mr-15"></div>
				</div>
			</div>
		</div>

		<div class="row" id="itens">
			<div class="col-sm-4 col-lg-4 col-md-12">
				<a href="/controleCozinha/controle"><button style="height: 90px; width: 100%; margin-top: 5px;" class="btn btn-info">
					Todos
				</button></a>
			</div>
			@foreach($telas as $t)
			<div class="col-sm-4 col-lg-4 col-md-12">
				<a href="/controleCozinha/controle/{{$t->id}}"><button style="height: 90px; width: 100%; margin-top: 5px;" class="btn btn-info">
					{{$t->nome}}
				</button></a>
			</div>
			@endforeach
		</div>
	</div>
</div>

@endsection	