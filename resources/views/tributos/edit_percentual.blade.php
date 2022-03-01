@extends('default.layout')
@section('content')
<div class="card card-custom gutter-b">

	<div class="card-body">
		<div class="col-sm-12 col-lg-12 col-md-12 col-xl-12">
			<h4>Atualizar Percentual</h4>

			<h5>Produto: <strong class="text-danger">{{$tributacao->produto->nome}}</strong></h5>

			<form method="post" action="/percentualuf/updatePercentualSingle">
				<input type="hidden" name="id" value="{{$tributacao->id}}">
				@csrf
				<div class="row">
					<div class="form-group validated col-sm-3 col-lg-3">
						<label class="col-form-label">%ICMS</label>
						<div class="">
							<input type="text" id="percentual_icms" class="form-control @if($errors->has('percentual_icms')) is-invalid @endif money" name="percentual_icms" value="{{{ isset($tributacao->percentual_icms) ? $tributacao->percentual_icms : old('percentual_icms') }}}">
							@if($errors->has('percentual_icms'))
							<div class="invalid-feedback">
								{{ $errors->first('percentual_icms') }}
							</div>
							@endif
						</div>
					</div>
				</div>

				<div class="row col-12">
					<a class="btn btn-light-danger" href="/percentualuf/verProdutos/{{$tributacao->uf}}">Cancelar</a>
					<input style="margin-left: 5px;" type="submit" value="Salvar" class="btn btn-light-success">
				</div>
			</form>
		</div>
	</div>
</div>

@endsection