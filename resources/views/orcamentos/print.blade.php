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
		@if($config->logo != "")
		<table>
			<tr>
				<td class="" style="width: 150px;">
					<img src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('logos/').$config->logo))}}" width="100px;">
				</td>

				<td class="" style="width: 550px;">
					<center><label class="titulo">DOCUMENTO AUXILIAR DE ORÇAMENTO</label></center>
					<center><label class="titulo">NÃO É DOCUMENTO FISCAL</label></center>
					<center><label class="titulo">NÃO COMPROVA PAGAMENTO</label></center>
				</td>
			</tr>
		</table>
		@else
		<center><label class="titulo">DOCUMENTO AUXILIAR DE ORÇAMENTO</label></center>
		<center><label class="titulo">NÃO É DOCUMENTO FISCAL</label></center>
		<center><label class="titulo">NÃO COMPROVA PAGAMENTO</label></center>
		@endif
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
				CNPJ: <strong>{{$config->cnpj}}</strong>
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
			<td class="b-top b-bottom" style="width: 700px;">
				Telefone: <strong>{{$config->fone}}</strong>
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
				Nome: <strong>{{$orcamento->cliente->razao_social}}</strong>
			</td>
			<td class="b-top" style="width: 247px;">
				CPF/CNPJ: <strong>{{$orcamento->cliente->cpf_cnpj}}</strong>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td class="b-top" style="width: 500px;">
				Endereço: <strong>{{$orcamento->cliente->rua}}, {{$orcamento->cliente->numero}} - {{$orcamento->cliente->bairro}} - {{$orcamento->cliente->cidade->nome}} ({{$orcamento->cliente->cidade->uf}})</strong>
			</td>

			<td class="b-top" style="width: 200px;">
				Telefone: <strong>{{$orcamento->cliente->telefone}}</strong>
			</td>
		</tr>
	</table>

	<table>
		<tr>
			<td class="b-top" style="width: 350px;">
				Nº Doc: <strong>{{$orcamento->id}}</strong>
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
			@foreach($orcamento->itens as $i)
			<tr>
				<th class="b-top">{{$i->produto->id}}</th>
				<th class="b-top">
					{{$i->produto->nome}}
					{{$i->produto->grade ? " (" . $i->produto->str_grade . ")" : ""}}

					@if($i->produto->lote != "")
					| Lote: {{$i->produto->lote}}, 
					Vencimento: {{$i->produto->vencimento}}
					@endif

				</th class="b-top">
				<th class="b-top">{{number_format($i->quantidade, 2, ',', '.')}}</th>
				<th class="b-top">{{number_format($i->valor, $casasDecimais, ',', '.')}}</th>
				<th class="b-top">{{number_format($i->quantidade * $i->valor, $casasDecimais, ',', '.')}}</th>

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
				<center><strong>Quantidade Total: {{$somaItens}}</strong></center>
			</td>

			<td class="b-top b-bottom" style="width: 350px;">
				<center><strong>Valor Total dos Itens: 
					{{number_format($somaTotalItens, 2, ',', '.')}}
				</strong></center>
			</td>
		</tr>
	</table>

	@if($orcamento->duplicatas()->exists())
	<table>
		<tr>
			<td class="b-bottom" style="width: 700px; height: 50px;">
				<strong>FATURA:</strong>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td class="b-bottom" style="width: 150px;">
				Vencimento
			</td>
			<td class="b-bottom" style="width: 150px;">
				Valor
			</td>
		</tr>
		@foreach($orcamento->duplicatas as$key => $d)
		<tr>

			<td class="b-bottom">
				<strong>{{ \Carbon\Carbon::parse($d->vencimento)->format('d/m/Y')}}</strong>
			</td>
			<td class="b-bottom">
				<strong>{{number_format($d->valor, 2, ',', '.')}}</strong>
			</td>

		</tr>
		@endforeach
	</table>
	@endif

	<br>
	<table>
		<tr>
			<td class="" style="width: 350px;">
				<strong>Forma de pagamento: 
					{{$orcamento->forma_pagamento == 'a_vista' ? 'À vista' : $orcamento->forma_pagamento}}
				</strong>
			</td>

			<td class="" style="width: 350px;">
				<strong>Vendedor: 
					{{$orcamento->usuario->nome}}
				</strong>
			</td>
		</tr>
	</table>

	<table>
		<tr>
			<td class="" style="width: 350px;">
				Data do orçamento: <strong>{{\Carbon\Carbon::parse($orcamento->created_at)->format('d/m/Y H:i')}}</strong>
			</td>
			<td class="" style="width: 347px;">

			</td>
		</tr>
	</table>

	@if($orcamento->observacao != "")
	<table>
		<tr>
			<td class="" style="width: 700px;">
				<strong>Observação: 
					{{$orcamento->observacao}}
				</strong>
			</td>
		</tr>
	</table>
	@endif

	<table>
		<tr>
			<td class="" style="width: 170px;">
				Desconto (-):
				<strong> 
					{{number_format($orcamento->desconto, 2, ',', '.')}}
				</strong>
			</td>

			<td class="" style="width: 170px;">
				Acréscimo (+):
				<strong> 
					{{number_format($orcamento->acrescimo, 2, ',', '.')}}
				</strong>
			</td>

			<td class="" style="width: 170px;">
				Frete (+):
				<strong> 
					@if($orcamento->frete)
					{{number_format($orcamento->frete->valor, 2, ',', '.')}}
					@else
					0,00
					@endif
				</strong>
			</td>

			<td class="" style="width: 200px;">
				Valor Líquido:
				<strong> 
					{{number_format($orcamento->valor_total - $orcamento->desconto + $orcamento->acrescimo, $casasDecimais, ',', '.')}}
				</strong>
			</td>
			
		</tr>
	</table>

	<br><br><br>
	<table>
		<tr>
			<td class="" style="width: 350px;">
				<strong>
					________________________________________
				</strong><br>
				<span style="font-size: 11px;">{{$config->razao_social}}</span>

			</td>

			<td class="" style="width: 350px;">
				<strong>
					________________________________________
				</strong><br>
				<span style="font-size: 11px;">{{$orcamento->cliente->razao_social}}</span>
			</td>
		</tr>
	</table>


</body>
</html>