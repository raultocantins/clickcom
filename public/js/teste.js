
$('#testar').click(() => {
	$('#testar').addClass('spinner')
	$.ajax
	({
		type: 'GET',
		url: path + 'configNF/teste',
		dataType: 'json',
		success: function(e){
			$('#testar').removeClass('spinner')
			
			console.log(e)
			
			swal("Sucesso", 'Ambiente ok', "success")
			.then((v) => {
				alert(e)
			})


		}, error: function(e){
			if(e.status == 200){
				$('#testar').removeClass('spinner')
				swal("Sucesso", 'Ambiente ok', "success")
				.then((v) => {
					alert(e.responseText)
				})

			}else{
				$('#testar').removeClass('spinner')
				swal("Erro", 'Algo esta errado, verifique o console do navegador!', "warning")
				.then((v) => {
					alert(e.responseText)
				})

				console.log(e)
			}

		}
	});
})

$('#testarEmail').click(() => {

	$('#preloaderEmail').css('display', 'block')

	$.get(path + 'configNF/testeEmail')
	.done((success) => {
		$('#preloaderEmail').css('display', 'none')
		swal("Sucesso", 'Config de email OK', "success")
	}).fail((e) => {
		let err = e.responseJSON
		$('#preloaderEmail').css('display', 'none')
		console.log(err)

		swal("Erro", err, "error")
	})
})

function getUF(uf, call){

	let js = {
		'RO': '11',
		'AC': '12',
		'AM': '13',
		'RR': '14',
		'PA': '15',
		'AP': '16',
		'TO': '17',
		'MA': '21',
		'PI': '22',
		'CE': '23',
		'RN': '24',
		'PB': '25',
		'PE': '26',
		'AL': '27',
		'SE': '28',
		'BA': '29',
		'MG': '31',
		'ES': '32',
		'RJ': '33',
		'SP': '35',
		'PR': '41',
		'SC': '42',
		'RS': '43',
		'MS': '50',
		'MT': '51',
		'GO': '52',
		'DF': '53'
	};

	call(js[uf])
}

function consultaCNPJ(){

	let cnpj = $('#cnpj').val();

	cnpj = cnpj.replace('.', '');
	cnpj = cnpj.replace('.', '');
	cnpj = cnpj.replace('-', '');
	cnpj = cnpj.replace('/', '');
	cnpj = cnpj.replace(' ', '');

	if(cnpj.length != 14){
		swal("Erro", "CNPJ inválido", "error")
	}else{
		$('#btn-consulta-cadastro').addClass('spinner')
		$.ajax({

			url: 'https://www.receitaws.com.br/v1/cnpj/'+cnpj, 
			type: 'GET', 
			crossDomain: true, 
			dataType: 'jsonp', 
			success: function(data) 
			{ 
				console.log(data);
				$('#btn-consulta-cadastro').removeClass('spinner')

				$('#razao_social').val(data.nome)
				$('#nome_fantasia').val(data.fantasia)
				$('#logradouro').val(data.logradouro)
				$('#numero').val(data.numero)
				$('#bairro').val(data.bairro)
				$('#complemento').val(data.complemento)
				$('#cnpj2').val(data.cnpj)
				let cep = data.cep;
				cep = cep.replace( '.', '' );
				cep = cep.replace( '-', '' );				
				$('#cep').val(cep.substring(0, 5) + '-' + cep.substring(5, 9))
				$('#municipio').val(data.municipio)

				// getUF(data.uf, (res) => {
				// 	$('#uf-s').val(res).change()
				// 	$('#pais').val('BRASIL')
				// 	$('#codPais').val('1058')
				// })
				
				findNomeCidade(data.municipio, (res) => {
					console.log(res)
					let jsCidade = JSON.parse(res);
					console.log(jsCidade)
					if (jsCidade) {
						console.log(jsCidade.id + " - " + jsCidade.nome)
						$('#kt_select2_1').val(jsCidade.id).change()
					}
				})
				
				$('#emil').val(data.email)
				

			}, 
			error: function(e) { 
				$('#btn-consulta-cadastro').removeClass('spinner')

				console.error(e); 
				swal("Erro", "Erro na consulta", "error")

			},
		});
	}
}

function findNomeCidade(nomeCidade, call) {
	$.get(path + 'cidades/findNome/' + nomeCidade)
	.done((success) => {
		call(success)
	})
	.fail((err) => {
		call(err)
	})
}