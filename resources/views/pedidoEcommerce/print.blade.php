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

		<center><label class="titulo">DOCUMENTO AUXILIAR DE VENDA - PEDIDO</label></center>
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
				CNPJ: <strong>{{ str_replace(" ", "", $config->cnpj)}}</strong>
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
			<td class="" style="width: 700px;">
				<strong>Identificação do Destinatário</strong>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td class="b-top" style="width: 450px;">
				Nome: <strong>{{$pedido->cliente->nome}} {{$pedido->cliente->sobre_nome}}</strong>
			</td>
			<td class="b-top" style="width: 247px;">
				CPF/CNPJ: <strong>{{$pedido->cliente->cpf}}</strong>
			</td>
		</tr>
	</table>

	<table>
		<tr>
			<td class="b-top" style="width: 250px;">
				Telefone: <strong>{{$pedido->cliente->telefone}}</strong>
			</td>
			<td class="b-top" style="width: 447px;">
				Email: <strong>{{$pedido->cliente->email}}</strong>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td class="b-top" style="width: 400px;">
				Endereço: <strong>{{$pedido->endereco->rua}}, {{$pedido->endereco->numero}} - {{$pedido->endereco->cidade}} ({{$pedido->endereco->uf}})</strong>
			</td>
			<td class="b-top" style="width: 300px;">
				CEP: <strong>{{$pedido->endereco->cep}}</strong>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td class="b-top" style="width: 700px;">
				Complemento: <strong>{{$pedido->endereco->complemento}}</strong>
			</td>
		</tr>
	</table>

	<table>
		<tr>
			<td class="b-top" style="width: 700px;">
				Nº Doc: <strong>{{$pedido->id}}</strong>
			</td>
		
		</tr>
	</table>
	<table>
		<tr>
			<td class="b-top b-bottom" style="width: 700px; height: 50px;">
				<strong>MERCADORIAS:</strong>
			</td>
		</tr>
	</table>	


	<table>
		<thead>
			<tr>
				<td class="" style="width: 95px;">
					Cod
				</td>
				<td class="" style="width: 350px;">
					Descrição
				</td>
				<td class="" style="width: 80px;">
					Quant.
				</td>
				<td class="" style="width: 80px;">
					Vl Uni
				</td>
				<td class="" style="width: 80px;">
					Vl Liq.
				</td>
			</tr>
		</thead>
		@php
		$somaItens = 0;
		$somaTotalItens = 0;
		@endphp
		<tbody>
			@foreach($pedido->itens as $i)
			<tr>
				<th class="b-top" align="left">{{$i->produto->id}}</th>
				<th class="b-top" align="left">
					{{$i->produto->produto->nome}}
					{{$i->produto->produto->grade ? " (" . $i->produto->produto->str_grade . ")" : ""}}
				</th class="b-top" align="left">
				<th class="b-top" align="left">{{number_format($i->quantidade, 2, ',', '.')}}</th>
				<th class="b-top" align="left">{{number_format($i->produto->valor, 2, ',', '.')}}</th>
				<th class="b-top" align="left">{{number_format($i->quantidade * $i->produto->valor, 2, ',', '.')}}</th>

			</tr>
			@php
			$somaItens += $i->quantidade;
			$somaTotalItens += $i->quantidade * $i->produto->valor;
			@endphp

			@endforeach
		</tbody>
	</table>
	<br>

	<table>
		<tr>
			<td class="b-top b-bottom" style="width: 350px;">
				<center><strong>Quantidade Total: {{$somaItens}}</strong></center>
			</td>

			<td class="b-top b-bottom" style="width: 350px;">
				<center><strong>Valor Total dos Itens: 
					{{number_format($somaTotalItens, 2, ',', '.')}}
				</strong></center>
			</td>
		</tr>
	</table>


	<br>
	<table>
		<tr>
			<td class="" style="width: 350px;">
				Forma de pagamento: <strong>{{$pedido->forma_pagamento}}
				</strong>
			</td>

			
		</tr>
	</table>

	<table>
		<tr>
			
			<td class="" style="width: 200px;">
				Frete (+):
				<strong> 
					@if($pedido->valor_frete > 0)
					{{number_format($pedido->valor_frete, 2, ',', '.')}}
					@else
					0,00
					@endif
				</strong>
			</td>

			<td class="" style="width: 200px;">
				Valor Total:
				<strong> 
					{{number_format($pedido->valor_total, 2, ',', '.')}}
				</strong>
			</td>

			<td class="" style="width: 250px;">
				Tipo do frete:
				<strong> 
					{{strtoupper($pedido->tipo_frete)}}
				</strong>
			</td>
			
		</tr>
	</table>


</body>
</html>