<!DOCTYPE html>
<html>
<head>
	<title></title>
	<!--  -->

	<style type="text/css">

		.content{
			margin-top: -0px;
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

		<center><label class="titulo">{{$config->razao_social}}</label></center>


	</div>
	<br>
	
	<table>
		<tr>
			
			<td class="" style="width: 350px;">
				CNPJ: <strong>{{$config->cnpj}}</strong>
			</td>

			<td class="" style="width: 350px;">
				IE: <strong>{{$config->ie}}</strong>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td class="b-top" style="width: 700px;">
				Endereço: <strong>{{$config->logradouro}}, {{$config->numero}} - {{$config->bairro}} - {{$config->municipio}} ({{$config->UF}})</strong>
			</td>
		</tr>
	</table>

	<table>
		<tr>
			<td class="b-top" style="width: 700px;">
				Saidas autorizadas por CFOP {{$dataInicial}} a {{$dataFinal}}
			</td>
		</tr>
	</table>

	
	
	<table>
		<tr>
			
			<td class="b-top" style="width: 347px;">
				Emissão: <strong>{{date('d/m/Y')}}</strong>
			</td>

			<td class="b-top" style="width: 350px;">
				Valor total de Emissão: <strong class="text-danger">
				{{ number_format($somaTotalVendas, 2, ',', '.')}}</strong>

			</td>
		</tr>
	</table>
	<br>

	@foreach($objeto as $t)

	<table>
		<tr>
			
			<td class="b-bottom" style="width: 347px;">
				CFOP: <strong>{{$t['cfop']}}</strong>
			</td>
		</tr>
	</table>


	<table>
		<thead>
			<tr>
				<td class="" style="width: 95px;">
					Código
				</td>
				<td class="" style="width: 350px;">
					Descrição
				</td>
				<td class="" style="width: 80px;">
					Un.
				</td>
				<td class="" style="width: 80px;">
					Qaunt.
				</td>
				<td class="" style="width: 80px;">
					Total
				</td>
			</tr>
		</thead>
		@php
		$somaItens = 0;
		$somaTotalItens = 0;
		@endphp
		<tbody>
			@foreach($t['itens'] as $i)
			<tr>
				<th align="left" class="b-top">{{$i->produto->id}}</th>
				<th align="left" class="b-top">
					{{$i->produto->nome}}
					{{$i->produto->grade ? " (" . $i->produto->str_grade . ")" : ""}}
				</th>
				<th align="left" class="b-top">{{number_format($i->quantidade, 2, ',', '.')}}</th>
				<th align="left" class="b-top">{{number_format($i->valor, $casasDecimais, ',', '.')}}</th>
				<th align="left" class="b-top">{{number_format($i->quantidade * $i->valor, $casasDecimais, ',', '.')}}</th>

			</tr>
			@php
			$somaItens += $i->quantidade;
			$somaTotalItens += $i->quantidade * $i->valor;
			@endphp

			@endforeach
		</tbody>
	</table>
	<br>

	<table>
		<tr>
			<td class="b-top b-bottom" style="width: 350px;">
				<center><strong>Itens: {{$somaItens}}</strong></center>
			</td>

			<td class="b-top b-bottom" style="width: 350px;">
				<center><strong>Total: R$ 
					{{number_format($somaTotalItens, 2, ',', '.')}} - 
					{{number_format($percentual, 2, '.', '')}}%
				</strong></center>
			</td>


		</tr>
	</table>
	<br>
	@endforeach



	<br>



</body>
</html>