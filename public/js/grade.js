var DIVISOES = [];
var SUBDIVISOES = [];
var DIVISOESSELECIONADAS = [];
var SUBDIVISOESSELECIONADAS = [];
var COMBINACOES = [];
function prepara(){
	let oldComb = $('#combinacoes').val()
	if(oldComb){
		oldComb = JSON.parse(oldComb)
	}

	console.log(oldComb)
	console.log(DIVISOES)
	for(let i = 0; i < DIVISOES.length; i++){
		if(oldComb){
			let marca = false
			oldComb.map((o) => {

				if(o.combinacao == DIVISOES[i].id){
					marca = true
				}
			})
			DIVISOES[i].selecionado = marca;

		}else{
			DIVISOES[i].selecionado = false;
		}
	}

	for(let i = 0; i < SUBDIVISOES.length; i++){
		SUBDIVISOES[i].selecionado = false;
	}
	console.log(DIVISOES)
	console.log(SUBDIVISOES)
	montaDivisoes()
	montaSubDivisoes()

}

function montaDivisoes(){
	let html = '';
	DIVISOES.map((rs) => {
		let cor = rs.selecionado ? 'success' : 'light'
		html += '<a style="margin-left: 3px;" class="btn btn-'+cor+'" onclick="selectDivisao('+rs.id+')">';
		html += rs.nome
		html += '</a>'
	})
	$('.divisoes').html(html)
}

function montaSubDivisoes(){
	let html = '';
	SUBDIVISOES.map((rs) => {
		let cor = rs.selecionado ? 'info' : 'light'
		html += '<a style="margin-left: 3px;" class="btn btn-'+cor+'" onclick="selectSubDivisao('+rs.id+')">';
		html += rs.nome
		html += '</a>'
	})
	$('.subDivisoes').html(html)
}

$(function () {

	// $('#combinacoes').val('');
	try{
		DIVISOES = JSON.parse($('#divisoes').val());
		SUBDIVISOES = JSON.parse($('#subDivisoes').val());
		prepara()
	}catch{}
})

$('.grade').change(() => {
	let g = $('.grade').is(':checked')
	if(g){
		// $('#modal-grade2').modal('show')
		$('#modal-grade1').modal('show')
	}else{
		$('#combinacoes').val('')
	}
})

function selectDivisao(id){
	for(let i = 0; i < DIVISOES.length; i++){
		if(DIVISOES[i].id == id){
			DIVISOES[i].selecionado = !DIVISOES[i].selecionado;
		}
	}
	setTimeout(() => {
		montaDivisoes();
	}, 100)
}

function selectSubDivisao(id){
	for(let i = 0; i < SUBDIVISOES.length; i++){
		if(SUBDIVISOES[i].id == id){
			SUBDIVISOES[i].selecionado = !SUBDIVISOES[i].selecionado;
		}
	}
	setTimeout(() => {
		montaSubDivisoes();
	}, 100)
}

function escolhaDivisao(){
	DIVISOESSELECIONADAS = DIVISOES.filter((x) => {
		if(x.selecionado) return x;
	})
	SUBDIVISOESSELECIONADAS = SUBDIVISOES.filter((x) => {
		if(x.selecionado) return x;
	})

	if(DIVISOESSELECIONADAS.length > 0 || SUBDIVISOESSELECIONADAS.length > 0){
		$('#modal-grade1').modal('hide')
		montaCombinacoes();
	}else{
		swal("Erro", "Selecione ao menos uma divisão ou subdivisão", "error")
	}
}

function montaCombinacoes(){
	let titulo = ''
	let html = ''
	let comb = '';

	COMBINACOES = []
	if(DIVISOESSELECIONADAS.length > 0){
		DIVISOESSELECIONADAS.map((d) => {
			if(SUBDIVISOESSELECIONADAS.length > 0){
				SUBDIVISOESSELECIONADAS.map((s) => {
					titulo = d.nome + ' ' + s.nome
					comb = d.id+"-"+s.id
					html += htmlCombinacao(titulo, comb)
					let js = {
						cod_barras: '',
						quantidade: 0,
						valor: 0,
						combinacao: comb,
						titulo: titulo
					}
					COMBINACOES.push(js)
				});
			}else{
				comb = d.id
				titulo = d.nome
				html += htmlCombinacao(titulo, comb)

				let js = {
					cod_barras: '',
					quantidade: 0,
					valor: 0,
					combinacao: comb,
					titulo: titulo
				}
				COMBINACOES.push(js)
			}
		})
	}else{
		SUBDIVISOESSELECIONADAS.map((s) => {
			titulo = s.nome
			comb = s.id
			html += htmlCombinacao(titulo, comb)
			let js = {
				cod_barras: '',
				quantidade: 0,
				valor: 0,
				combinacao: comb,
				titulo: titulo
			}
			COMBINACOES.push(js)
		});
	}

	$('.combinacoes').html(html)
	$('#modal-grade2').modal('show')
}

function htmlCombinacao(titulo, comb){
	let html = '';

	let valorVenda = $('#valor_venda').val()

	html += '<div class="row">'
	html += '<div class="form-group validated col-sm-3 col-lg-3">'
	html += '<br><br>'
	html += '<h2>'+titulo+'</h2>'
	html += '</div>'
	html += '<div class="form-group validated col-sm-3 col-lg-3">'
	html += '<label class="col-form-label">Código de Barras</label>'
	html += '<div class="">'
	html += '<input type="text" class="form-control" id="cod_barras_'+comb+'" value="">'
	html += '</div></div>'
	html += '<div class="form-group validated col-sm-3 col-lg-3">'
	html += '<label class="col-form-label">Quantidade</label>'
	html += '<div class="">'
	html += '<input type="text" class="form-control money" id="quantidade_'+comb+'" value="0">'
	html += '</div></div>'
	html += '<div class="form-group validated col-sm-3 col-lg-3">'
	html += '<label class="col-form-label">Valor unit.</label>'
	html += '<div class="">'
	html += '<input type="text" value="'+valorVenda+'" class="form-control" id="valor_'+comb+'">'
	html += '</div></div>'
	html += '</div>'
	return html;
}

function finalizarGrade(){
	validaCamposCombinacoes((res) => {
		if(res == ""){
			for(let i = 0; i < COMBINACOES.length; i++){

				let comb = COMBINACOES[i].combinacao
				let codBarras = $('#cod_barras_'+comb).val()
				let quantidade = $('#quantidade_'+comb).val()
				let valor = $('#valor_'+comb).val()

				COMBINACOES[i].cod_barras = codBarras;
				COMBINACOES[i].quantidade = quantidade;
				COMBINACOES[i].valor = valor;

				$('#combinacoes').val(JSON.stringify(COMBINACOES))
				$('#modal-grade2').modal('hide')
			}
		}else{
			swal("Alerta", res, "warning")
		}
	})
}

function validaCamposCombinacoes(call){
	let msg = "";
	console.log("info", COMBINACOES.length)

	for(let i = 0; i < COMBINACOES.length; i++){
		let comb = COMBINACOES[i].combinacao
		let codBarras = $('#cod_barras_'+comb).val()
		let quantidade = $('#quantidade_'+comb).val()
		let valor = $('#valor_'+comb).val()

		if(!quantidade){
			msg += "Informe a quantidade da linha: " + (i+1) + "\n"
		}
		if(!valor){
			msg += "Informe a valor da linha: " + (i+1) + "\n"
		}
	}
	call(msg)
}

