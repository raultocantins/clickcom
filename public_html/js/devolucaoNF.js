$(function () {
	validaBtns();

	var w = window.innerWidth
	if(w < 900){
		$('#grade').trigger('click')
	}
})

$('#checkbox input').click(() => {
	validaBtns();
})
function enviar(){
	let id = 0
	let cont = 0;
	$('#body tr').each(function(){
		if($(this).find('#checkbox input').is(':checked')){
			id = $(this).find('#id').html();
			cont++;
		}
	})

	if(cont > 1){
		alert('Por favor selecione apenas um item da tabela!')
	}else{
		if(cont > 0){

			$('#btn-enviar').addClass('spinner')
			$('#btn-enviar').addClass('disabled')

			let token = $('#_token').val();

			$.ajax
			({
				type: 'POST',
				data: {
					devolucao_id: id,
					_token: token
				},
				url: path + 'devolucao/enviarSefaz',
				dataType: 'json',
				success: function(e){

					console.log(e)
					let recibo = e;
					let retorno = recibo.substring(0,4);
					console.log(retorno)
					let mensagem = recibo.substring(5,recibo.length);
					if(retorno == 'Erro'){
						let m = JSON.parse(mensagem);
						try{
							swal("Erro", "[" + m.protNFe.infProt.cStat + "] : " + m.protNFe.infProt.xMotivo, "error")
						}catch{
							swal("Erro", "Erro desconhecido", "error")
						}
					}
					else if(e == 'false'){
						swal("Atenção", "Esta NF já esta aprovada, não é possível enviar novamente!", "warning")
					}
					else{

						swal("Sucesso", "Devolução emitida com sucesso RECIBO: "+recibo, "success")
						.then(() => {
							window.open(path+"devolucao/imprimir/"+id, "_blank");
							location.reload()
						})
					}

					$('#btn-enviar').removeClass('spinner')
					$('#btn-enviar').removeClass('disabled')
				}, error: function(e){
					console.log(e)
					try{
						swal("Erro", e.responseJSON.message, "error")
					}catch{
						swal("Erro", e.responseJSON, "error")
					}
					$('#btn-enviar').removeClass('spinner')
					$('#btn-enviar').removeClass('disabled')
				}
			});
		}else{
			alert('Selecione um documento para envio!')
		}
	}
}

function consultar(){
	let id = 0;
	let cont = 0;
	$('#body tr').each(function(){
		if($(this).find('#checkbox input').is(':checked')){
			id = $(this).find('#id').html();
			cont++;
		}
	})

	if(cont > 1){

		swal("Atenção", "Selecione um documento para consultar", "warning")

	}else{
		$('#btn-consultar').addClass('spinner')
		let token = $('#_token').val();
		$.ajax
		({
			type: 'POST',
			data: {
				id: id,
				_token: token
			},
			url: path + 'devolucao/consultar',
			dataType: 'json',
			success: function(e){
				let js = JSON.parse(e)
				console.log(js)

				try{
					if(js.cStat != '656'){
						$('#motivo').html(js.xMotivo);
						$('#chave').html(js.chNFe);
						$('#protocolo').html(js.protNFe.infProt.nProt);
						$('#modal2').modal('show');
						$('#preloader1').css('display', 'none');

					swal("Sucesso", "["+js.cStat+"] - " + js.xMotivo + " >> " + js.chNFe, "success")

					}else{
						alert('Consumo indevido!')
					}
				}catch{
					console.log(js)
					swal("Erro", js.xMotivo, "error")
				}
				$('#btn-consultar').removeClass('spinner')

			}, error: function(e){
				console.log(e)
				swal("Erro", "Erro de comunicação contate o desenvolvedor", "error")

				// $('#preloader1').css('display', 'none');
				$('#btn-consultar').removeClass('spinner')

			}
		});
	}
}

function cancelarNFe(id, nf){
	$('#modal1_aux').modal('show')
	$('#numero_cancelamento2').html(nf)
	$('#id_cancela').val(id)
}

function corrigirNFe(id, nf){
	$('#modal4_aux').modal('show')
	$('#numero_correcao_aux').html(nf)
	$('#id_correcao').val(id)
}

function transmitir(id){
	$('#btn_transmitir_grid_'+id).addClass('spinner')
	$('#btn_transmitir_grid_'+id).addClass('disabled')

	let token = $('#_token').val();

	$.ajax
	({
		type: 'POST',
		data: {
			devolucao_id: id,
			_token: token
		},
		url: path + 'devolucao/enviarSefaz',
		dataType: 'json',
		success: function(e){

			console.log(e)
			let recibo = e;
			let retorno = recibo.substring(0,4);
			console.log(retorno)
			let mensagem = recibo.substring(5,recibo.length);
			if(retorno == 'Erro'){
				let m = JSON.parse(mensagem);

				try{
					$('#evento-erro').html("[" + m.protNFe.infProt.cStat + "] : " + m.protNFe.infProt.xMotivo)
					swal("Erro", "[" + m.protNFe.infProt.cStat + "] : " + m.protNFe.infProt.xMotivo, "error")
				}catch{
					swal("Erro", "Erro desconhecido", "error")
				}

			}
			else if(e == 'false'){
				swal("Atenção", "Esta NF já esta aprovada, não é possível enviar novamente!", "warning")
			}
			else{
				$('#modal-alert').modal('open');
				$('#evento').html("Devolução emitida com sucesso RECIBO: "+recibo)
				swal("Sucesso", "Devolução emitida com sucesso RECIBO: "+recibo, "success")
				.then(() => {
					location.reload()
					window.open(path+"devolucao/imprimir/"+id, "_blank");
				})
			}

			$('#btn_transmitir_grid_'+id).removeClass('spinner')
			$('#btn_transmitir_grid_'+id).removeClass('disabled')
		}, error: function(e){
			swal("Erro", "Erro de comunicação contate o desenvolvedor!", "error")
			console.log(e)
			$('#btn_transmitir_grid_'+id).removeClass('spinner')
			$('#btn_transmitir_grid_'+id).removeClass('disabled')
		}
	});

}

function imprimir(){
	let id = 0;
	let cont = 0;
	$('#body tr').each(function(){
		if($(this).find('#checkbox input').is(':checked')){
			id = $(this).find('#id').html();
			cont++;
		}
	})
	if(cont > 1){
		alert('Selecione apenas um documento da lista!');
	}else{
		if(id > 0){
			window.open(path+"devolucao/imprimir/"+id, "_blank");
		}else{
			swal("Erro", "Selecione um documento para imprimir!", "error")
		}
	}

}

function validaBtns(){
	let cont = 0;
	let estado = "";
	$('#body tr').each(function(){
		if($(this).find('#checkbox input').is(':checked')){

			id = $(this).find('#id').html();
			estado = $(this).find('#estado_'+id).html();
			cont++;
		}
	})

	if(cont > 1 || cont == 0){
		desabilitaBotoes();
	}else{
		habilitaBotoes();

		if(estado == 0){
			$('#btn-enviar').removeClass("disabled");
			$('#btn-imprimir').addClass("disabled");

			$('#btn-cancelar').addClass("disabled");
			$('#btn-correcao').addClass("disabled");
			$('#btn-imprimir-cce').addClass("disabled");
			$('#btn-imprimir-cancela').addClass("disabled");

		} else if(estado == 2){
			$('#btn-enviar').removeClass("disabled");
			$('#btn-imprimir').addClass("disabled");

			$('#btn-cancelar').addClass("disabled");
			$('#btn-correcao').removeClass("disabled");
			$('#btn-imprimir-cce').addClass("disabled");
			$('#btn-imprimir-cancela').addClass("disabled");


		}  else if(estado == 1){
			$('#btn-enviar').addClass("disabled");
			$('#btn-imprimir').removeClass("disabled");

			$('#btn-cancelar').removeClass("disabled");
			$('#btn-correcao').removeClass("disabled");
			$('#btn-imprimir-cce').removeClass("disabled");

		} else if(estado == 3){
			$('#btn-enviar').addClass("disabled");
			$('#btn-imprimir').removeClass("disabled");

			$('#btn-cancelar').addClass("disabled");
			$('#btn-correcao').addClass("disabled");
			$('#btn-imprimir-cce').addClass("disabled");
			$('#btn-imprimir-cancela').removeClass("disabled");
		}

	}
}

function desabilitaBotoes(){
	$('#btn-enviar').addClass("disabled");
	$('#btn-imprimir').addClass("disabled");
	$('#btn-cancelar').addClass("disabled");
	$('#btn-correcao').addClass("disabled");
	$('#btn-imprimir-cce').addClass("disabled");
	$('#btn-imprimir-cancela').addClass("disabled");

}

function habilitaBotoes(){
	$('#btn-enviar').removeClass("disabled");
	$('#btn-imprimir').removeClass("disabled");
	$('#btn-cancelar').removeClass("disabled");
	$('#btn-correcao').removeClass("disabled");
	$('#btn-imprimir-cce').removeClass("disabled");
	$('#btn-imprimir-cancela').removeClass("disabled");


}

function cancelar(){
	$('#btn-cancelar-2').addClass('spinner')
	$('#btn-cancelar-2').addClass('disabled')
	let id = 0;
	let cont = 0;
	let justificativa = $('#justificativa').val();
	$('#body tr').each(function(){
		if($(this).find('#checkbox input').is(':checked')){
			id = $(this).find('#id').html();
			cont++;
		}
	})

	if(cont > 1){
		swal("Erro", "Selecione apenas um documento para cancelar!", "warning")
	}else{
		let token = $('#_token').val();
		$.ajax
		({
			type: 'POST',
			data: {
				devolucao_id: id,
				justificativa: justificativa,
				_token: token
			},
			url: path + 'devolucao/cancelar',
			dataType: 'json',
			success: function(e){
				$('#modal1').modal('hide');
				console.log(e)
				$('#btn-cancelar-2').removeClass('spinner')
				$('#btn-cancelar-2').removeClass('disabled')

				let js = JSON.parse(e);
				// console.log(js)
				// alert(js.retEvento.infEvento.xMotivo)
				swal("Sucesso", "[" + js.retEvento.infEvento.cStat + "] : " + js.retEvento.infEvento.xMotivo, "success")
				.then(() => {
					window.open(path+"devolucao/imprimirCancela/"+id, "_blank");
					location.reload()
				})
				// $('#preloader5').css('display', 'none');
			}, error: function(e){
				console.log(e)
				try{
					swal("Erro", e.responseJSON, "error")
				}catch{
					swal("Erro", "Verifique o console do navegador!", "error")
				}
				$('#btn-cancelar-2').removeClass('spinner')
				$('#btn-cancelar-2').removeClass('disabled')
			}
		});
	}
}

function cancelar2(){
	// $('#preloader5').css('display', 'block');
	$('#btn-cancelar-3').addClass('spinner')

	let id = $('#id_cancela').val();
	
	let justificativa = $('#justificativa2').val();
	
	let token = $('#_token').val();
	$.ajax
	({
		type: 'POST',
		data: {
			devolucao_id: id,
			justificativa: justificativa,
			_token: token
		},
		url: path + 'devolucao/cancelar',
		dataType: 'json',
		success: function(e){
			console.log(e)
			let js = JSON.parse(e);
			console.log(js)
			$('#btn-cancelar-3').removeClass('spinner')
				// alert(js.retEvento.infEvento.xMotivo)
				swal("Sucesso", js.retEvento.infEvento.xMotivo, "success")
				.then(() => {
					window.open(path+"devolucao/imprimirCancela/"+id, "_blank");
					location.reload();
				})

				// $('#preloader5').css('display', 'none');

			}, error: function(e){
				console.log(e)
				try{
					swal("Erro", e.responseJSON, "error")
				}catch{
					swal("Erro", "Verifique o console do navegador!", "error")
				}

				$('#btn-cancelar-3').removeClass('spinner')

			}
		});
}

function redireciona(){
	location.reload();
}

function xmlTemp(){
	let id = 0;
	let cont = 0;
	$('#body tr').each(function(){
		if($(this).find('#checkbox input').is(':checked')){
			id = $(this).find('#id').html();
			cont++;
		}
	})
	if(cont > 1){
		alert('Selecione apenas um documento da lista!');
	}else{
		if(id > 0){
			window.open(path+"devolucao/xmltemp/"+id, "_blank");
		}else{
			swal("Erro", "Selecione um documento!", "error")
		}
	}
}

function danfeTemp(){
	let id = 0;
	let cont = 0;
	$('#body tr').each(function(){
		if($(this).find('#checkbox input').is(':checked')){
			id = $(this).find('#id').html();
			cont++;
		}
	})
	if(cont > 1){
		alert('Selecione apenas um documento da lista!');
	}else{
		if(id > 0){
			window.open(path+"devolucao/danfeTemp/"+id, "_blank");
		}else{
			swal("Erro", "Selecione um documento!", "error")
		}
	}
}

function setarNumero(buscarCliente = false){

	let id = 0;
	let nf = 0;
	let cont = 0;
	$('#body tr').each(function(){
		if($(this).find('#checkbox input').is(':checked')){
			nf = $(this).find('#numeroNf').html();

			$('#numero_correcao').html(nf)
			$('#numero_correcao_aux').html(nf)
			$('#numero_cancelamento').html(nf)
			$('#numero_cancelamento2').html(nf)

			cont++;
		}
	})
	
	if(cont > 1){
		swal('Alerta', 'Selecione apenas um documento para continuar!', 'warning')
		swal('Alerta', 'Selecione um documento para continuar', 'warning')
		.then(() => {
			$('#modal4').modal('hide');
		});
	}
	if(cont == 0){
		swal('Alerta', 'Selecione um documento para continuar', 'warning')
		.then(() => {
			$('#modal4').modal('hide');
		});
	}
}

function cartaCorrecao(){
	// $('#preloader4').css('display', 'block');
	$('#btn-corrigir-2').addClass('spinner');
	let id = 0;
	let cont = 0;
	let correcao = $('#correcao').val();
	$('#body tr').each(function(){
		if($(this).find('#checkbox input').is(':checked')){
			id = $(this).find('#id').html();
			cont++;
		}
	})

	if(cont > 1){
		swal("Atenção", "Selecione apenas um documento para continuar!", "warning")

	}else{
		let token = $('#_token').val();
		$.ajax
		({
			type: 'POST',
			data: {
				id: id,
				correcao: correcao,
				_token: token
			},
			url: path + 'devolucao/cartaCorrecao',
			dataType: 'json',
			success: function(e){
				$('#btn-corrigir-2').removeClass('spinner');
				console.log(e)
				try{
					let js = JSON.parse(e);
					console.log(js)


					swal("Sucesso", js.retEvento.infEvento.xMotivo, "success")
					.then(() => {
						window.open(path+"devolucao/imprimirCce/"+id, "_blank");
						location.reload()
					})
				}catch{
					swal("Erro", e.data.retEvento.infEvento.xMotivo, "error")
					.then(() => {
						location.reload()
					})
				}
				// $('#preloader4').css('display', 'none');

			}, error: function(e){
				$('#btn-corrigir-2').removeClass('spinner');
				console.log(e)
				swal("Erro", "Erro de comunicação contate o desenvolvedor!", "error")
				$('#btn-corrigir-2').removeClass('spinner');

			}
		});
	}
}

function cartaCorrecaoAux(){
	// $('#preloader4').css('display', 'block');
	$('#btn-corrigir-2-aux').addClass('spinner');
	
	let correcao = $('#correcao_aux').val();
	let id = $('#id_correcao').val();
	let token = $('#_token').val();
	$.ajax
	({
		type: 'POST',
		data: {
			id: id,
			correcao: correcao,
			_token: token
		},
		url: path + 'devolucao/cartaCorrecao',
		dataType: 'json',
		success: function(e){
			$('#btn-corrigir-2-aux').removeClass('spinner');
			console.log(e)
			try{
				let js = JSON.parse(e);
				console.log(js)


				swal("Sucesso", js.retEvento.infEvento.xMotivo, "success")
				.then(() => {
					window.open(path+"devolucao/imprimirCce/"+id, "_blank");
					location.reload()
				})
			}catch{
				swal("Erro", e, "error")
				.then(() => {
					location.reload()
				})
			}

		}, error: function(e){
			$('#btn-corrigir-2-aux').removeClass('spinner');
			console.log(e)
			swal("Erro", "Erro de comunicação contate o desenvolvedor!", "error")
		}
	});

}

function imprimirCCe(){
	let id = 0;
	let cont = 0;
	$('#body tr').each(function(){
		if($(this).find('#checkbox input').is(':checked')){
			id = $(this).find('#id').html();
			cont++
		}
	})

	if(cont > 1){
		swal("Atenção", "Selecione um documento para impressão", "warning")
	}else{
		window.open(path+"devolucao/imprimirCce/"+id, "_blank");
	}
}

function imprimirCancela(){
	let id = 0;
	let cont = 0;
	$('#body tr').each(function(){
		if($(this).find('#checkbox input').is(':checked')){
			id = $(this).find('#id').html();
			cont++
		}
	})

	if(cont > 1){
		swal("Atenção", "Selecione um documento para impressão", "warning")
	}else{
		window.open(path+"devolucao/imprimirCancela/"+id, "_blank");
	}
}
