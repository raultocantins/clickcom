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
			<td class="" style="width: 700px;">
				<strong>Identificação do Destinatário</strong>
			</td>
		</tr>
	</table>

	<table>
		<tr>
			<td class="b-top" style="width: 450px;">
				Nome: <strong>{{$locacao->cliente->razao_social}}</strong>
			</td>
			<td class="b-top" style="width: 247px;">
				CPF/CNPJ: <strong>{{$locacao->cliente->cpf_cnpj}}</strong>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td class="b-top" style="width: 700px;">
				Endereço: <strong>{{$locacao->cliente->rua}}, {{$locacao->cliente->numero}} - {{$locacao->cliente->bairro}} - {{$locacao->cliente->cidade->nome}} ({{$locacao->cliente->cidade->uf}})</strong>
			</td>
		</tr>
	</table>
	
	<table>
		<tr>
			<td class="b-top b-bottom" style="width: 700px; height: 50px;">
				<strong>Itens/Produtos:</strong>
			</td>
		</tr>
	</table>	


	<table>
		<thead>
			<tr>
				<td class="" style="width: 300px;">
					Nome
				</td>
				<td class="" style="width: 292px;">
					Observação
				</td>
				<td class="" style="width: 100px;">
					Valor
				</td>
				
			</tr>
		</thead>

		<tbody>
			@foreach($locacao->itens as $i)
			<tr>
				<th class="b-top">{{ $i->produto->nome }}</th>
				<th class="b-top">{{ $i->observação != "" ? $i->observação : '--' }}</th>
				<th class="b-top">{{ number_format($i->valor, 2, ',', '.') }}</th>
				
			</tr>

			@endforeach
		</tbody>
	</table>
	<br>



	<table>
		<tr>

			<td class="b-top b-bottom" style="width: 300px;">
				<center><strong>Total: 
					{{number_format($locacao->total, 2, ',', '.')}}
				</strong></center>
			</td>

			<td class="b-top b-bottom" style="width: 200px;">
				<center><strong>Inicio: 
					{{ \Carbon\Carbon::parse($locacao->inicio)->format('d/m/y') }}
				</strong></center>
			</td>

			<td class="b-top b-bottom" style="width: 200px;">
				<center><strong>Fim: 

					@if($locacao->fim != '1969-12-31')
					{{ \Carbon\Carbon::parse($locacao->fim)->format('d/m/Y')}}
					@else
					--
					@endif
				</strong></center>
			</td>
		</tr>
	</table>


	<table>
		<tr>

			<td class="b-top b-bottom" style="width: 700px;">
				<center><strong>Observação: 
					{{ $locacao->observacao != "" ? $locacao->observacao : '--' }}
				</strong></center>
			</td>

			
		</td>
	</tr>
</table>

</body>
</html>