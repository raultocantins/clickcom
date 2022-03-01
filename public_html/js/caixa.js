function abrirCaixa(){
	let token = $('#_token').val();
	let valor = $('#valor').val();

	valor = valor.length >= 0 ? valor.replace(",", ".") : 0 ;
	if(parseFloat(valor) >= 0){
		$.ajax
		({
			type: 'POST',
			url: path + 'aberturaCaixa/abrir',
			dataType: 'json',
			data: {
				valor: $('#valor').val(),
				_token: token
			},
			success: function(e){

				$('#modal-abrir-caixa').modal('hide');
				swal("Sucesso", "Caixa aberto", "success")
				.then(() => {
					location.reload();
				})


			}, error: function(e){
				$('#modal-abrir-caixa').modal('hide');
				swal("Erro", "Erro ao abrir caixa", "error")
				console.log(e)
			}

		});
	}else{
		// alert('Insira um valor válido')
		swal("Erro", 'Insira um valor válido', "warning")

	}
	
}

function suprimentoCaixa(){
	let token = $('#_token').val();

	$.ajax
	({
		type: 'POST',
		url: path + 'suprimentoCaixa/save',
		dataType: 'json',
		data: {
			valor: $('#valor_suprimento').val(),
			obs: $('#obs_suprimento').val(),
			_token: token
		},
		success: function(e){

			$('#modal-supri').modal('hide');
			$('#valor_suprimento').val('');
			$('#obs_suprimento').val('');
			swal("Sucesso", "suprimento realizado!", "success")
			.then(() => {
				window.open(path+'suprimentoCaixa/imprimir/'+e.id)
				location.reload()
			})

		}, error: function(e){
			console.log(e)
			$('#valor_suprimento').val('')
			$('#obs_suprimento').val('')
			swal("Erro", "Erro ao realizar suprimento de caixa!", "error")

		}

	});
}

function sangriaCaixa(){
	let token = $('#_token').val();
	
	$.ajax
	({
		type: 'POST',
		url: path + 'sangriaCaixa/save',
		dataType: 'json',
		data: {
			valor: $('#valor_sangria').val(),
			observacao: $('#obs_sangria').val(),
			_token: token
		},
		success: function(e){

			caixaAberto = true;
			$('#modal-sangria').modal('hide');
			$('#valor_sangria').val('');
			$('#obs_sangria').val('');
			swal("Sucesso", "Sangria realizada!", "success")
			.then(() => {
				window.open(path+'sangriaCaixa/imprimir/'+e.id)
				location.reload();
			})


		}, error: function(e){
			console.log(e)
			$('#valor_sangria').val('');
			$('#obs_sangria').val('');
			try{
				swal("Erro", e.responseJSON, "error")
				.then(() => {
					$('#modal-sangria').modal('hide');
				})
			}catch{
				swal("Erro", "Erro ao realizar sangria!", "error")
				.then(() => {
					$('#modal-sangria').modal('hide');
				})
			}

		}

	});
}

