@extends('default.layout')
@section('content')

<div class="card card-custom gutter-b">

	<div class="card-body">
		<div class="" id="kt_user_profile_aside" style="margin-left: 10px; margin-right: 10px;">
			<div class="col-lg-12" id="content">
				<!--begin::Portlet-->
				<br>
				<div class="row">
					<div class="col-lg-4">
						<h2 class="card-title">Inicio: 
							<strong class="text-success">{{\Carbon\Carbon::parse($dre->inicio)
								->format('d/m/Y')}}
							</strong>
						</h2>
					</div>

					<div class="col-lg-4">
						<h2 class="card-title">Fim: 
							<strong class="text-danger">{{\Carbon\Carbon::parse($dre->fim)
								->format('d/m/Y')}}
							</strong>
						</h2>
					</div>

					@if($tributacao->regime != 1)
					<div class="col-lg-4">
						<h2 class="card-title">% Imposto: 
							<strong class="text-primary">{{number_format($dre->percentual_imposto, 2, ',', '.')}}
							</strong>
						</h2>
					</div>
					@endif
				</div>

				<div class="row">
					<div class="col-lg-12">
						<h2 class="card-title">Observação: 
							<strong class="text-info">
								{{ $dre->observacao != "" ? $dre->observacao : "--" }}
							</strong>
						</h2>
					</div>
				</div>

				
				<div class="card card-custom gutter-b">

					<div class="card-body">

						@foreach($dre->categorias as $key => $c)
						<div class="card card-custom gutter-b example example-compact">
							<div class="card-header bg-info">
								<h2 class="card-title text-white">{{$c->nome}} 
									<button style="margin-left: 5px;" class="btn btn-sm btn-success" onclick="addLancamento({{$c}})">
										<i class="la la-plus"></i>
									</button>
								</h2>
								
							</div>

							<div class="card-body">

								
								@foreach($c->lancamentos as $l)
								<div class="row" style="height: 45px;">
									<div class="col-6">
										<h4 class="text-left">{{$l->nome}}</h4>
									</div>

									<div class="col-3">
										<h4 class="text-left">
											<strong>
												R$ {{ number_format($l->valor, 2, ',', '.') }}
											</strong>
											@if($key > 0)
											<strong class="text-danger">
												- {{ number_format($l->percentual, 2, ',', '.') }}%
											</strong>
											@endif
										</h4>
									</div>

									<div class="col-2">
										<button onclick="editLacamento({{$l}})" style="margin-top: -5px;" class="btn btn-sm btn-outline-info" href="">
											<i class="la la-edit"></i>
										</button>

										<a  style="margin-top: -5px;" class="btn btn-sm btn-outline-danger" onclick='swal("Atenção!", "Deseja remover este registro?", "warning").then((sim) => {if(sim){ location.href="/dre/deleteLancamento/{{ $l->id }}" }else{return false} })' href="#!">
											<i class="la la-trash"></i>
										</a>
									</div>
								</div>
								@endforeach

								@if($key > 2)

								<div class="row" style="height: 45px;">
									<div class="col-6">
										<h3 class="text-left text-info">{{$c->nome}}</h3>
									</div>

									<div class="col-3">
										<h4 class="text-left text-info">
											<strong>
												R$ {{ number_format($c->soma(), 2, ',', '.') }}
											</strong>
											@if($key > 0)
											<strong class="text-primary">
												- {{ number_format($c->percentual(), 2, ',', '.') }}%
											</strong>
											@endif
										</h4> 
									</div>
								</div>
								@endif
							</div>
						</div>
						@endforeach

						<div class="card card-custom gutter-b">

							<div class="card-body @if($dre->lucro_prejuizo >= 0) bg-success @else bg-danger @endif">
								<div class="row" style="height: 45px;">
									<div class="col-6">
										<h3 class="text-left text-white">Lucro (Prejuizo) no Período</h3>
									</div>

									<div class="col-3">
										<h4 class="text-left text-white">
											<strong>
												R$ {{ number_format($dre->lucro_prejuizo, 2, ',', '.') }}
											</strong>

										</h4> 
									</div>
								</div>
							</div>
						</div>

					</div>

				</div>
				<a class="btn btn-info btn-lg" href="/dre/imprimir/{{$dre->id}}">
					<i class="la la-print"></i>
					Imprimir
				</a>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-edit" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<form method="post" action="/dre/updatelancamento">
			@csrf
			<div class="modal-content">
				<div class="modal-header">

					<h6 id="titulo" class="modal-title"></h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						x
					</button>
				</div>
				<div class="modal-body">

					<div class="row">
						<div class="form-group validated col-sm-12 col-lg-12 col-12">
							<label class="col-form-label" id="">Nome</label>
							<input required="" type="text" placeholder="Nome" id="nome-edit" name="nome" class="form-control" value="">
						</div>
					</div>

					<div class="row">
						<div class="form-group validated col-sm-6 col-lg-6 col-12">
							<label class="col-form-label" id="">Valor</label>
							<input type="text" placeholder="Valor" id="valor" name="valor" class="form-control" value="">
						</div>
					</div>
					<input type="hidden" id="lancamento_id" name="lancamento_id">
				</div>
				<div class="modal-footer">
					<button style="width: 100%" type="submit" class="btn btn-success font-weight-bold">
						<i class="la la-edit"></i>
						ALTERAR
					</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="modal-new" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<form method="post" action="/dre/novolancamento">
			@csrf
			<div class="modal-content">
				<div class="modal-header">

					<h6 id="titulo-new" class="modal-title"></h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						x
					</button>
				</div>
				<div class="modal-body">

					<div class="row">
						<div class="form-group validated col-sm-12 col-lg-12 col-12">
							<label class="col-form-label" id="">Nome</label>
							<input required="" type="text" placeholder="Nome" id="nome" name="nome" class="form-control" value="">
						</div>
					</div>

					<div class="row">
						<div class="form-group validated col-sm-6 col-lg-6 col-12">
							<label class="col-form-label" id="">Valor</label>
							<input required type="text" placeholder="Valor" id="valor" name="valor" class="form-control money" value="">
						</div>
					</div>
					<input type="hidden" id="categoria_id" name="categoria_id">
				</div>
				<div class="modal-footer">
					<button style="width: 100%" type="submit" class="btn btn-success font-weight-bold">
						<i class="la la-check"></i>
						SALVAR
					</button>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection	