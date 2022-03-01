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
				console.log(rt)
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
			console.log('#todos_'+m.titulo)
			$('#todos_'+m.titulo).prop('checked',false)
		}
	});
}

$('.check-sub').click(() => {
	console.log("teste")
	validaCategoriaCompleta()
})