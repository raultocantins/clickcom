@extends('default.layout')
@section('content')


<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

	<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInLeft">
		<div class="card card-custom gutter-b example example-compact">
			<div class="col-lg-12">
				<!--begin::Portlet-->

			</div>

		</div>
	</div>
</div>




<div class="container @if(getenv('ANIMACAO')) animate__animated @endif animate__backInRight" style="margin-top: -30px;">
	<div class="card card-custom gutter-b example example-compact">
		<div class="col-lg-12">
			<div class="card card-custom gutter-b example example-compact">

			</div>
			<div class="row">

				<div class="col-xl-12">
					<h4 class="text-danger"><i class="la la-warning text-danger"></i>Cuidado ao remover os dados não poderam ser recuperados!</h4>
					<div class="row">

						<div class="col-sm-4 col-lg-4 col-md-4 col-12">

							<div class="card card-custom gutter-b">
								<div class="card-header">
									<h3 class="card-title">
										Total de Cadastros
									</h3>
								</div>
								<div class="card-body" style="height: 200px;">

									<h4>Clientes: <strong class="text-info">{{sizeof($empresa->clientes)}}</strong></h4>
									<h4>Fornecedores: <strong class="text-info">{{sizeof($empresa->fornecedores)}}</strong></h4>
									<h4>Produtos: <strong class="text-info">{{sizeof($empresa->produtos)}}</strong></h4>
									<h4>Usuários: <strong class="text-info">{{sizeof($empresa->usuarios)}}</strong></h4>
									<h4>Veiculos: <strong class="text-info">{{sizeof($empresa->veiculos)}}</strong></h4>
								</div>
							</div>
						</div>

						<div class="col-sm-4 col-lg-4 col-md-4 col-12">

							<div class="card card-custom gutter-b">
								<div class="card-header">
									<h3 class="card-title">
										Total de Documentos
									</h3>
								</div>
								<div class="card-body" style="height: 200px;">

									<h4>NF-e: <strong class="text-info">{{$empresa->nfes()}}</strong></h4>
									<h4>NFC-e: <strong class="text-info">{{$empresa->nfces()}}</strong></h4>
									<h4>CT-e: <strong class="text-info">{{$empresa->ctes()}}</strong></h4>
									<h4>MDF-e: <strong class="text-info">{{$empresa->mdfes()}}</strong></h4>
								</div>
							</div>
						</div>

						<div class="col-sm-4 col-lg-4 col-md-4 col-12">

							<div class="card card-custom gutter-b">
								<div class="card-header">
									<h3 class="card-title">
										Registros
									</h3>
								</div>
								<div class="card-body" style="height: 200px;">

									<h4>Vendas: <strong class="text-info">{{sizeof($empresa->vendas)}}</strong></h4>
									<h4>Vendas PDV: <strong class="text-info">{{sizeof($empresa->vendasCaixa)}}</strong></h4>

								</div>
							</div>
						</div>

					</div>
					<div class="row">
						<div class="form-group validated col-sm-6 col-lg-6">
							<div class="">

								<a href="/empresas/delete/{{$empresa->id}}" class="btn btn-danger">
									<i class="la la-close"></i> Estou ciente quero remover
								</a>
							</div>
						</div>

						<div class="col-sm-6 col-lg-6">
							<h3 class="text-success">Data de cadastro: {{ \Carbon\Carbon::parse($empresa->created_at)->format('d/m/Y H:i:s')}}</h3>
						</div>
					</div>
					
					<br>

				</div>
			</div>
		</div>
	</div>
</div>

@endsection