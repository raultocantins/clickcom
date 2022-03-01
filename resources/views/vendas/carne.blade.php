<!DOCTYPE html>
<html>
<head>
	<title></title>
	<!--  -->

	<style type="text/css">

		body{
			line-height: 1px;
		}
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
		.b-right{
			border-right: 1px solid #000; 
		}

		.center{
			text-align: center;
		}

		.fright{
			float: right;
		}
		.fleft{
			float: left;
		}

	</style>

</head>
<body>

	@foreach($venda->duplicatas as $key => $d)
	<!-- inicio -->
	<table>
		<tr>
			<td class="b-right b-bottom" style="width: 233px;">
				
				<h6>@if($config->logo != "")
				<img src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@'logos/'.$config->logo))}}" width="20px;">
				@endif{{$config->razao_social}}</h6>
				<p class="">Recibo cliente</p>
			</td>

			<td class="b-right b-bottom" style="width: 469px;">
				<h5 class="center">{{$venda->cliente->razao_social}}</h5>
				
				<p>
					<span class="fright" style="margin-right: 2px;">Recibo caixa</span>
				</p>
			</td>
		</tr>
	</table>
	<table>

		<!-- 2 linha -->
		<tr>
			<td class="b-right b-bottom" style="width: 233px;">
				<p>Vencimento: <strong>
					{{\Carbon\Carbon::parse($d->data_vencimento)->format('d/m/Y')}}
				</strong></p>
				<p>Emissão: <strong>{{date('d/m/Y H:i')}}</strong></p>
			</td>

			<td class="b-right" style="width: 233px;">
				
			</td>
			<td class="b-right b-bottom" style="width: 230px;">
				<p>Vencimento: <strong>
					{{\Carbon\Carbon::parse($d->data_vencimento)->format('d/m/Y')}}
				</strong></p>
				<p>Emissão: <strong>{{date('d/m/Y H:i')}}</strong></p>
			</td>
		</tr>

		<tr>
			<td class="b-right b-bottom" style="width: 233px;">
				<p>Valor do documento: <strong>
					R$ {{number_format($d->valor_integral, 2, ',', '.')}}
				</strong></p>
			</td>

			<td class="b-right" style="width: 233px;">
				
			</td>
			<td class="b-right b-bottom" style="width: 230px;">
				<p>Valor do documento: <strong>
					R$ {{number_format($d->valor_integral, 2, ',', '.')}}
				</strong></p>
				
			</td>
		</tr>

		<tr>
			<td class="b-right b-bottom" style="width: 233px;">
				<p>Documento: <strong>{{$venda->id}}</strong></p>
				<p>Parcela: <strong>{{$key+1}}</strong></p>
			</td>

			<td class="b-right" style="width: 233px;">
				<p class="center">Multa: 
				R$ {{number_format($d->multa, 2, ',', '.')}}</p>
				<p class="center">Juros ao dia: 
					R$ {{number_format($d->juros, 2, ',', '.')}}</p>
			</td>
			<td class="b-right b-bottom" style="width: 230px;">
				<p>Documento: <strong>{{$venda->id}}</strong></p>
				<p>Parcela: <strong>{{$key+1}}</strong></p>
			</td>
		</tr>

		<tr>
			<td class="b-right b-bottom" style="width: 233px;">
				<p class="fleft">__/__/____</p>
				<p style="margin-right: 2px;" class="fright">________________</p>
			</td>

			<td class="b-right" style="width: 233px;">
				
			</td>
			<td class="b-right b-bottom" style="width: 230px;">
				<p style="font-size: 14px;">*Isto não é um documento fiscal</p>
			</td>
		</tr>

	</table>

	<!-- fim -->

	<table style="margin-top: 10px; margin-bottom: 30px;">
		<tr>
			<td class="" style="width: 700px;">
				<p class="fleft">-------------------------------------------------------------------------------------------------------------------------------------</p>
			</td>
		</tr>
	</table>
	@endforeach
	

</body>
</html>