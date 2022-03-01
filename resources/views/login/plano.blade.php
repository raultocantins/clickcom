<!DOCTYPE html>

<html lang="br">
<!-- begin::Head -->

<head>
	<meta charset="utf-8" />

	<title>Selecione o plano</title>
	<meta name="description" content="Updates and statistics">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!--begin::Fonts -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">

	<link href="/metronic/css/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
	<!-- <link href="/metronic/css/uppy.bundle.css" rel="stylesheet" type="text/css" /> -->
	<link href="/metronic/css/wizard.css" rel="stylesheet" type="text/css" />

	<link href="/css/style.css" rel="stylesheet" type="text/css" />

	<!--end::Page Vendors Styles -->


	<!--begin::Global Theme Styles(used by all pages) -->
	<link href="/metronic/css/plugins.bundle.css" rel="stylesheet" type="text/css" />
	<link href="/metronic/css/prismjs.bundle.css" rel="stylesheet" type="text/css" />
	<link href="/metronic/css/style.bundle.css" rel="stylesheet" type="text/css" />

	<link href="/metronic/css/pricing.css" rel="stylesheet" type="text/css" />
	<!--end::Global Theme Styles -->

	<!--begin::Layout Skins(used by all pages) -->

	<link href="/metronic/css/light.css" rel="stylesheet" type="text/css" />
	<link href="/metronic/css/light-menu.css" rel="stylesheet" type="text/css" />
	<link href="/metronic/css/dark-brand.css" rel="stylesheet" type="text/css" />
	<link href="/metronic/css/dark-aside.css" rel="stylesheet" type="text/css" />

	<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<link rel="shortcut icon" href="/../../imgs/slym.png" />
	
	<script>
		(function(h, o, t, j, a, r) {
			h.hj = h.hj || function() {
				(h.hj.q = h.hj.q || []).push(arguments)
			};
			h._hjSettings = {
				hjid: 1070954,
				hjsv: 6
			};
			a = o.getElementsByTagName('head')[0];
			r = o.createElement('script');
			r.async = 1;
			r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
			a.appendChild(r);
		})(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
	</script>
	<!-- Global site tag (gtag.js) - Google Analytics -->

	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());
		gtag('config', 'UA-37564768-1');
	</script>


	<style type="text/css">
		.select2-selection__arroww:before {
			content: "";
			position: absolute;
			right: 7px;
			top: 42%;
			border-top: 5px solid #888;
			border-left: 4px solid transparent;
			border-right: 4px solid transparent;
		}

		.h-t:hover{
			cursor: pointer;
		}
	</style>
</head>


<!-- end::Head -->

<!-- begin::Body -->

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">


	<div class="d-flex flex-column flex-root">
		<!--begin::Login-->
		<div class="login  flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
			<!--begin::Aside-->
			<div class="login-aside d-flex flex-row-auto position-relative overflow-hidden" >
				<!--begin: Aside Container-->
				<div class="d-flex flex-column-fluid flex-column justify-content-between py-12 col-12 py-lg-12 px-lg-35">
					<!--begin::Logo-->
					
					<!--end::Logo-->
					<!--begin::Aside body-->
					<div class="d-flex flex-column-fluid flex-column" >
						
						<!--begin::Signin-->
						<div class="login-form login-signin py-2">
							<!--begin::Form-->

							<!--begin::Title-->
							<div class="text-center pb-8">
								<h2 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg">{!! getenv("TITUO_PLANO") !!}</h2>

								@if(getenv("MENSAGEM_PLANO"))
								<h4>{!! getenv("MENSAGEM_PLANO") !!}</h4>
								@endif
								@if(getenv("PLANO_AUTOMATICO_DIAS") > 0)
								<p class="text-info">*Faça seu cadastro, e utilize grátis por {{getenv("PLANO_AUTOMATICO_DIAS")}} dia(s)</p>
								@endif



							</div>

							<div class="row">

								@foreach($planos as $p)

								<div class="{{App\Models\Plano::divPlanos()}} h-t">
									<!--begin::Nav Panel Widget 2-->
									<div class="card card-custom card-stretch gutter-b">
										<!--begin::Body-->
										<div class="card-body">
											<!--begin::Wrapper-->
											<div class="d-flex justify-content-between flex-column pt-4 h-100">
												<!--begin::Container-->
												<div class="pb-5">
													<!--begin::Header-->
													<div class="d-flex flex-column flex-center">
														<!--begin::Symbol-->
														<div class="symbol symbol-120 symbol-circle symbol overflow-hidden">
															<span class="symbol-label">
																@if($p->img != '')
																<img src="/imgs_planos/{{$p->img}}" class="h-100 align-self-end" alt="">
																@else
																<img src="/imgs_planos/sem_imagem.png" class="h-100 align-self-end" alt="">
																@endif

															</span>
														</div>
														<!--end::Symbol-->
														<!--begin::Username-->
														<a href="#" class="card-title font-weight-bolder text-dark-75 text-hover-primary font-size-h4 m-0 pt-7 pb-1">{{$p->nome}}</a>

														<h2 class="card-title font-weight-bolder text-info text-hover-primary font-size-h4 m-0 pt-7 pb-1">R$ {{number_format($p->valor, 2, ',', '.')}}</h2>
														<!--end::Username-->
														<!--end::Info-->
													</div>
													<!--end::Header-->
													<!--begin::Body-->
													<div class="pt-1">
														<!--begin::Text-->
														<p class="text-dark-75 font-weight-nirmal font-size-lg m-0 pb-7">
															{!! $p->descricao !!}
														</p>
														<!--end::Text-->

														<!--end::Item-->
													</div>
													<!--end::Body-->
												</div>
												<!--eng::Container-->
												<!--begin::Footer-->
												<div class="d-flex flex-center" id="kt_sticky_toolbar_chat_toggler_1" data-toggle="tooltip" title="">
													<a class="btn btn-primary font-weight-bolder font-size-sm py-3 px-14" href="/cadastro?plano={{$p->id}}">Escolher</a>
												</div>
												<!--end::Footer-->
											</div>
											<!--end::Wrapper-->
										</div>
										<!--end::Body-->
									</div>
									<!--end::Nav Panel Widget 2-->
								</div>

								@endforeach
							</div>

							<!--end::Form-->
						</div>
						
					</div>
					<!--end::Aside body-->

					<!--end: Aside footer for desktop-->
				</div>
				<!--end: Aside Container-->
			</div>
			<!--begin::Aside-->
			<!--begin::Content-->
			
			<!--end::Content-->
		</div>

		<!--end::Login-->
	</div>
	<script>var HOST_URL = "/metronic/theme/html/tools/preview";</script>
	<script>
		var KTAppSettings = {
			"breakpoints": {
				"sm": 576,
				"md": 768,
				"lg": 992,
				"xl": 1200,
				"xxl": 1400
			},
			"colors": {
				"theme": {
					"base": {
						"white": "#ffffff",
						"primary": "#3699FF",
						"secondary": "#E5EAEE",
						"success": "#1BC5BD",
						"info": "#8950FC",
						"warning": "#FFA800",
						"danger": "#F64E60",
						"light": "#E4E6EF",
						"dark": "#181C32"
					},
					"light": {
						"white": "#ffffff",
						"primary": "#E1F0FF",
						"secondary": "#EBEDF3",
						"success": "#C9F7F5",
						"info": "#EEE5FF",
						"warning": "#FFF4DE",
						"danger": "#FFE2E5",
						"light": "#F3F6F9",
						"dark": "#D6D6E0"
					},
					"inverse": {
						"white": "#ffffff",
						"primary": "#ffffff",
						"secondary": "#3F4254",
						"success": "#ffffff",
						"info": "#ffffff",
						"warning": "#ffffff",
						"danger": "#ffffff",
						"light": "#464E5F",
						"dark": "#ffffff"
					}
				},
				"gray": {
					"gray-100": "#F3F6F9",
					"gray-200": "#EBEDF3",
					"gray-300": "#E4E6EF",
					"gray-400": "#D1D3E0",
					"gray-500": "#B5B5C3",
					"gray-600": "#7E8299",
					"gray-700": "#5E6278",
					"gray-800": "#3F4254",
					"gray-900": "#181C32"
				}
			},
			"font-family": "Poppins"
		};
	</script>



	<!-- end::Global Config -->
	<!--begin::Global Theme Bundle(used by all pages) -->

	<script src="/metronic/js/plugins.bundle.js" type="text/javascript"></script>
	<script src="/metronic/js/prismjs.bundle.js" type="text/javascript"></script>
	<script src="/metronic/js/scripts.bundle.js" type="text/javascript"></script>
	<script src="/metronic/js/fullcalendar.bundle.js" type="text/javascript"></script>
	<script src="/metronic/js/file.js" type="text/javascript"></script>

	<script src="/metronic/js/wizard.js" type="text/javascript"></script>
	<script src="/metronic/js/user.js" type="text/javascript"></script>



	<script type="text/javascript" src="/js/jquery.mask.min.js"></script>
	<script type="text/javascript" src="/js/mascaras.js"></script>
	<script src="/metronic/js/select2.js" type="text/javascript"></script>
	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script> -->
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


	<script>

	</script>

	<script type="text/javascript">
		$('#consulta').click(() => {
			$('#consulta').addClass('spinner');
			let cnpj = $('#cnpj').val();

			cnpj = cnpj.replace('.', '');
			cnpj = cnpj.replace('.', '');
			cnpj = cnpj.replace('-', '');
			cnpj = cnpj.replace('/', '');

			if(cnpj.length == 14){

				$.ajax({

					url: 'https://www.receitaws.com.br/v1/cnpj/'+cnpj, 
					type: 'GET', 
					crossDomain: true, 
					dataType: 'jsonp', 
					success: function(data) 
					{ 
						$('#consulta').removeClass('spinner');
						console.log(data);
						if(data.status == "ERROR"){
							swal(data.message, "", "error")
						}else{
							$('#nome_empresa').val(data.nome)
							$('#telefone').val(data.telefone.replace("(", "").replace(")", ""))
							$('#cidade').val(data.municipio)
							$('#email').val(data.email)

						}

					}, 
					error: function(e) { 
						$('#consulta').removeClass('spinner');
						console.log(e)
						swal("Alerta", "Nenhum retorno encontrado para este CNPJ, informe manualmente por gentileza", "warning")

					},
				});
			}else{
				swal("Alerta", "Informe corretamente o CNPJ", "warning")
			}
		})
	</script>

</body>
<!-- end::Body -->

</html>