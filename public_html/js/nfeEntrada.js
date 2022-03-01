
function enviar(id){

	swal("Atenção", "Deseja gerar entrada fiscal desta Compra?", "warning")
	.then((v) => {
		$('#btn-enviar-nfe').addClass('spinner')
		

		let token = $('#_token').val();
		let js = {
			compra_id: id,
			natureza: $('#natureza').val(),
			tipo_pagamento: $('#tipo_pagamento').val(),
			_token: token
		}
		console.log(js)
		$.ajax
		({
			type: 'POST',
			data: js,
			url: path + 'compras/gerarEntrada',
			dataType: 'json',
			success: function(e){
				$('#btn-enviar-nfe').removeClass('spinner')

				console.log(e)

				swal("Sucesso", "NF-e de Entrada emitida com sucesso RECIBO: "+e, "success")
				.then(() => {
					window.open(path+"compras/imprimir/"+id, "_blank");
					location.reload()
				})

			}, error: function(e){
				console.log(e)
				$('#btn-enviar-nfe').removeClass('spinner')

				let js = e.responseJSON;

				try{
					let mensagem = js.substring(5,js.length);
					js = JSON.parse(mensagem)
					console.log(js)

					swal("Erro", "[" + js.protNFe.infProt.cStat + "] : " + js.protNFe.infProt.xMotivo, "warning")
				}catch{
					swal("Erro", e.responseJSON.message, "error")
				}
			}
		});
	})

}

function xmlTemporaria(id){

	let natureza = $('#natureza').val()
	let tipo_pagamento = $('#tipo_pagamento').val()

	window.open(path + "compras/xmlTemporaria?id="+id+"&natureza="+natureza+"&tipo_pagamento="+tipo_pagamento)
}

function danfeTemporaria(id){

	let natureza = $('#natureza').val()
	let tipo_pagamento = $('#tipo_pagamento').val()

	window.open(path + "compras/danfeTemporaria?id="+id+"&natureza="+natureza+"&tipo_pagamento="+tipo_pagamento)
}

function redireciona(){
	location.reload();
}

function cancelar(){
	$('#preloader5').css('display', 'block')
	let token = $('#_token').val();

	let js = {
		justificativa: $('#justificativa').val(),
		compra_id: $('#compra_id').val(),
		_token: token
	}
	console.log(js)

	$('#btn-cancelar').addClass('spinner')
	$.ajax
	({
		type: 'POST',
		data: js,
		url: path + 'compras/cancelarEntrada',
		dataType: 'json',
		success: function(e){
			$('#btn-cancelar').removeClass('spinner')

			console.log(e)
			let js = JSON.parse(e);
			console.log(js)

			swal("Sucesso", js.retEvento.infEvento.xMotivo, "success")
			.then(() => {

				location.reload();
			})
		}, error: function(e){
			console.log(e)
			$('#btn-cancelar').removeClass('spinner')
			swal("Erro", "Algo deu errado", "warning")

			// Materialize.toast('Erro de comunicação contate o desenvolvedor', 5000)

		}
	});
}

$('#btn-consulta').click(() => {
	$('#btn-consulta').addClass('spinner')
	$('#btn-consulta').addClass('disabled')
	let token = $('#_token').val();


	let js = {
		compra_id: $('#compra_id').val(),
		_token: token
	}
	console.log(js)


	$.ajax
	({
		type: 'POST',
		data: js,
		url: path + 'compras/consultar',
		dataType: 'json',
		success: function(e){
			$('#btn-consulta').removeClass('spinner')
			$('#btn-consulta').removeClass('disabled')

			console.log(e)
			let js = JSON.parse(e)
			if(js.cStat != '656'){
				swal("Sucesso", "Status: " + js.xMotivo + " - chave: " + js.chNFe + ", protocolo: " + js.protNFe.infProt.nProt, "success")
			}else{

				swal("Erro", "Consumo indevido!", "error")
			}
			
		}, error: function(e){
			console.log(e)
			$('#btn-consulta').removeClass('spinner')
			$('#btn-consulta').removeClass('disabled')
			swal("Erro", "Algo deu errado", "warning")

			// Materialize.toast('Erro de comunicação contate o desenvolvedor', 5000)

		}
	});
});

