<!DOCTYPE html>
<html>
<head>
	<title></title>
	<!--  -->

	<style type="text/css">

		.content{
			margin-top: -30px;
		}
		.titulo{
			font-size: 20px;
			margin-bottom: 0px;
			font-weight: bold;
		}

		.b-top{
			border-top: 1px solid #000; 
		}
		.b-bottom{
			border-bottom: 1px solid #000; 
		}

	</style>

</head>
<body>
	<div class="content">

		<center><label class="titulo">DOCUMENTO AUXILIAR DE LOCAÇÃO</label></center>
		<center><label class="titulo">NÃO É DOCUMENTO FISCAL</label></center>
		<center><label class="titulo">NÃO COMPROVA PAGAMENTO</label></center>

	</div>
	<br>
	<table>
		<tr>
			<td class="" style="width: 700px;">
				<strong>Identificação do Estabelecimento Emitente</strong>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td class="b-top" style="width: 500px;">
				Razão social: <strong>{{$config->razao_social}}</strong>
			</td>
			<td class="b-top" style="width: 197px;">
				CNPJ: <strong>{{str_replace(" ", "", $config->cnpj)}}</strong>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td class="b-top b-bottom" style="width: 700px;">
				Endereço: <strong>{{$config->logradouro}}, {{$config->numero}} - {{$config->bairro}} - {{$config->municipio}} ({{$config->UF}})</strong>
			</td>
		</tr>
	</table>
	<br>
	
	<table>
		<tr>
			<td class="b-top b-bottom" style="width: 700px; height: 50px;">
				<strong>Registros:</strong>
			</td>
		</tr>
	</table>	


	<table>
		<thead>
			<tr>
				<td class="" style="width: 449px;">
					Cliente
				</td>
				<td class="" style="width: 80px;">
					Data Inicio
				</td>
				<td class="" style="width: 80px;">
					Data Fim
				</td>
				<td class="" style="width: 80px;">
					Total
				</td>
				
			</tr>
		</thead>
		@php
		$soma = 0;
		@endphp
		<tbody>
			@foreach($locacoes as $l)
			<tr>
				<th class="b-top">{{$l->cliente->razao_social}}</th>
				<th class="b-top">{{ \Carbon\Carbon::parse($l->inicio)->format('d/m/Y')}}</th>
				<th class="b-top">{{ \Carbon\Carbon::parse($l->fim)->format('d/m/Y')}}</th>
				<th class="b-top">{{ number_format($l->total,2, ',', '.')}}</th>
				
			</tr>

			@if($l->observacao)
			<tr>
				<th colspan="4" class="b-top">Observação: <strong style="color: blue">{{$l->observacao}}</strong></th>

			</tr>
			@endif

			@if(sizeof($l->itens) > 0)
			<tr>
				<th colspan="" style="color: red">ITENS/PRODUTOS</th>
			</tr>

			@foreach($l->itens as $i)
			<tr>
				<th class="b-top">{{$i->produto->nome}} {{$i->observacao}}</th>
				<th class="b-top">{{$i->valor}}</th>

			</tr>
			@endforeach
			@endif
			@php
			$soma += $l->total;
			@endphp

			@endforeach
		</tbody>
	</table>
	<br>

	<table>
		<tr>

			<td class="b-top b-bottom" style="width: 350px;">
				<center><strong>Somatório: 
					{{number_format($soma, 2, ',', '.')}}
				</strong></center>
			</td>
		</tr>
	</table>




</body>
</html>