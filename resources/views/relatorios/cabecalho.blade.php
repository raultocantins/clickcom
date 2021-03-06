<!DOCTYPE html>
<html>
<head>
	<title></title>
	<!-- <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.1/build/pure-min.css" integrity="sha384-oAOxQR6DkCoMliIh8yFnu25d7Eq/PHS21PClpwjOTeU2jRSq11vu66rf90/cZr47" crossorigin="anonymous"> -->
	<style type="text/css">
		.soma{
			font-size: 30px;
		}

		.center{
			text-align: center;
			line-height: 0.5;
		}

		.text-success{
			color: #00e676;
		}

		.text-info{
			color: #8D56FC;
		}

		.text-danger{
			color: #e53935;
		}

		thead{
			border: 1px solid #000;
		}

		tbody{
			border: 1px solid #000;
		}

		td{
			border-bottom: 1px solid #000;
		}

		.pure-table-odd{
			background: #F3F6F9;
		}

		h1{
			font-size: 22px;
		}

		h3{
			font-size: 16px;
		}
	</style>
</head>
<body>
	<?php $config = App\Models\ConfigNota::configStatic(); ?>

	<div class="center">
		<h1>{{$config->razao_social}}</h1>
		<h1>CNPJ: <strong>{{str_replace(" ", "", $config->cnpj)}}</strong></h1>
		<h1>IE: <strong>{{str_replace(" ", "", $config->ie)}}</strong></h1>

		<h3>{{$config->logradouro}}, {{$config->numero}} - {{$config->bairro}}</h3>
		<h3>{{$config->municipio}} ({{$config->UF}}) - {{$config->cep}}</h3>
	</div>
	@yield('content')
</body>
</html>