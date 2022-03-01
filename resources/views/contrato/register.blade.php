@extends('default.layout')
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

				<form method="post" action="{{{ $contract != null ? '/contrato/update': '/contrato/save' }}}" enctype="multipart/form-data">


					<input type="hidden" name="id" value="{{{ $contract != null ? $contract->id : 0 }}}">
					<div class="card card-custom gutter-b example example-compact">
						<div class="card-header">

							<h3 class="card-title">{{$contract != null ? 'Editar' : 'Novo'}} Contrato</h3>
						</div>

					</div>
					@csrf
					<div class="row">
						<div class="col-12">
							<textarea name="texto" id="area" style="width:100%;height:500px;">{{$contract != null ? $contract->texto : ''}}</textarea>
						</div>
					</div>

					<div class="row" style="margin-top: 10px; margin-bottom: 10px;">

						<div class="col-lg-3 col-sm-6 col-md-4">
							<button style="width: 100%" type="submit" class="btn btn-success">
								<i class="la la-check"></i>
								<span class="">Salvar</span>
							</button>
						</div>
						<div class="col-lg-3 col-sm-6 col-md-4">

							@if($contract != null)
							<a target="_blank" href="/contrato/impressao" style="width: 100%" class="btn btn-info">
								<i class="la la-print"></i>
								Impressão
							</a>
							@endif
						</div>

					</div>

				</form>
			</div>
		</div>

	</div>
</div>

@endsection