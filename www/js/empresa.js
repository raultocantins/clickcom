var menu = [];
$(function () {
	menu = JSON.parse($('#menus').val())
	validaCategoriaCompleta()

});
function marcarTudo(titulo){
	let marked = $('#todos_'+titulo).is(':checked')
	if(!marked){
		acaoCheck(false, titulo)
	}else{
		acaoCheck(true, titulo)
	}
}

function acaoCheck(acao, titulo){
	menu.map((m) => {
		if(titulo == m.titulo){
			m.subs.map((sub) => {
				let rt = sub.rota.replaceAll("/", "")
				rt = rt.replaceAll(".", "_")
				rt = rt.replaceAll(":", "_")
				if(acao){
					$('#sub_'+rt).attr('checked', true);
				}else{
					$('#sub_'+rt).removeAttr('checked');
				}
			})
		}
	})
}

function validaCategoriaCompleta(){
	let temp = true;

	menu.map((m) => {
		temp = true;
		m.subs.map((sub) => {
			let rt = sub.rota.replaceAll("/", "")
			rt = rt.replaceAll(".", "_")
			rt = rt.replaceAll(":", "_")

			let marked = $('#sub_'+rt).is(':checked')
			if(!marked && sub.nome != "NFS-e") temp = false;
		})

		if(temp){
			$('#todos_'+m.titulo).prop('checked', true);
		}else{
			$('#todos_'+m.titulo).prop('checked',false)
		}
	});
}

$('.check-sub').click(() => {
	validaCategoriaCompleta()
})

$('#perfil-select').change(() => {
	desmarcarTudo((cl) => {
		let perfil = $('#perfil-select').val();
		if(perfil != '0'){
			perfil = JSON.parse(perfil)
			let permissao = JSON.parse(perfil.permissao)
			permissao.map((p) => {
				menu.map((m) => {
					m.subs.map((sub) => {
						// console.log(p)
						if(sub.rota == p){

							let rt = sub.rota.replaceAll("/", "")
							rt = rt.replaceAll(".", "_")
							rt = rt.replaceAll(":", "_")
							$('#sub_'+rt).prop('checked', true);
						}

						if(p.length > 60){
							let tr = sub.rota.replaceAll(".", "_")
							console.log(p)
							console.log(tr)

							if(tr == p){

								let rt = sub.rota.replaceAll("/", "")
								rt = rt.replaceAll(".", "_")
								rt = rt.replaceAll(":", "_")
								$('#sub_'+rt).prop('checked', true);
							}

						}
					})
				})
			})

			validaCategoriaCompleta();
		}
	})

})

function desmarcarTudo(call){
	console.clear();
	menu.map((m) => {
		m.subs.map((sub) => {
			let rt = sub.rota.replaceAll("/", "")
			rt = rt.replaceAll(".", "_")
			rt = rt.replaceAll(":", "_")
			$('#sub_'+rt).prop('checked',false);
		})
	})
	call(true)
}

$('#consulta').click(() => {
	$('#consulta').addClass('spinner');
	let cnpj = $('#cnpj').val();

	cnpj = cnpj.replace('.', '');
	cnpj = cnpj.replace('.', '');
	cnpj = cnpj.replace('-', '');
	cnpj = cnpj.replace('/', '');

	if(cnpj.length == 14){

		$.ajax({

			url: 'https://www.receitaws.com.br/v1/cnpj/'+cnpj, 
			type: 'GET', 
			crossDomain: true, 
			dataType: 'jsonp', 
			success: function(data) 
			{ 
				$('#consulta').removeClass('spinner');

				if(data.status == "ERROR"){
					swal(data.message, "", "error")
				}else{
					$('#nome').val(data.nome)
					$('#rua').val(data.logradouro)
					$('#numero').val(data.numero)
					$('#bairro').val(data.bairro)
					$('#email').val(data.email)
					$('#telefone').val(data.telefone.replace("(", "").replace(")", ""))
					$('#cidade').val(data.municipio)
					$('#email').val(data.email)

				}

			}, 
			error: function(e) { 
				$('#consulta').removeClass('spinner');
				console.log(e)
				swal("Alerta", "Nenhum retorno encontrado para este CNPJ, informe manualmente por gentileza", "warning")

			},
		});
	}else{
		swal("Alerta", "Informe corretamente o CNPJ", "warning")
	}
})