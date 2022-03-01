var TOTAL = 0;
var ITENS = [];
var caixaAberto = false;
var PRODUTO = null;
var PRODUTOS = [];
var CLIENTE = null;
var TOTALEMABERTOCLIENTE = null;
var COMANDA = 0;
var VALORBAIRRO = 0;
var VALORACRESCIMO = 0;
var OBSERVACAO = "";
var OBSERVACAOITEM = "";
var DESCONTO = 0;
var LISTAID = 0;
var PDV_VALOR_RECEBIDO = 0;
var PRODUTOGRADE = null;
var VALORPAG1 = 0
var VALORPAG2 = 0
var VALORPAG3 = 0
var TIPOPAG1 = ''
var TIPOPAG2 = ''
var TIPOPAG3 = ''
var CATEGORIAS = [];
var CLIENTES = [];
var ATALHOS = null;
var DIGITOBALANCA = 5;
var TIPOUNIDADEBALANCA = 1;
var QUANTIDADE = 1;

document.addEventListener("DOMContentLoaded", function(event) {
	// console.log("DOM completamente carregado e analisado");
	$('#prods').css('visibility', 'visible')
});



$(function () {

	try{
		ATALHOS = JSON.parse($('#ATALHOS').val())

		CATEGORIAS = JSON.parse($('#categorias').val())
		CLIENTES = JSON.parse($('#clientes').val())
	}catch{

	}
	montaAtalhos()

	$('#finalizar-venda').attr('disabled', true)

	var w = window.innerWidth
	if(w < 900){
		$('#grade').trigger('click')
	}

	novaHora();
	novaData();
	$('#codBarras').val('')
	
	let semCertificado = $('#semCertificado').val()
	if(semCertificado){
		swal("Aviso", "Para habilitar o cupom fiscal, realize o upload do certificado digital!!", "warning")
	}

	PDV_VALOR_RECEBIDO = $('#PDV_VALOR_RECEBIDO').val()

	let valor_entrega = $('#valor_entrega').val();

	VALORACRESCIMO = parseFloat(valor_entrega);
	let obs = $('#obs').val();
	if(obs) OBSERVACAO = obs;

	verificaCaixa((v) => {
		// console.log(v)
		caixaAberto = v >= 0 ? true : false;
		if(v < 0){
			$('#modal1').modal('show');
		}
	})

	let itensPedido = $('#itens_pedido').val();

	//Verifica se os dados estao vindo da comanda
	//Controller Pedido
	if(itensPedido){

		itensPedido = JSON.parse(itensPedido);

		if($('#bairro').val() != 0){
			// console.log($('#bairro').val())
			let bairro = JSON.parse($('#bairro').val());

			VALORBAIRRO = parseFloat(bairro.valor_entrega);
		}
		let cont = 1;
		itensPedido.map((v) => {
			// console.log(v)
			let nome = '';
			let valorUnit = 0;
			if(v.sabores && v.sabores.length > 0){

				let cont = 0;
				v.sabores.map((sb) => {
					cont++;
					valorUnit = v.valor;
					nome += sb.produto.produto.nome + 
					(cont == v.sabores.length ? '' : ' | ')
				})
				valorUnit = v.maiorValor

			}else{
				if (typeof v.produto !== 'undefined') {
					nome = v.produto.nome;
					valorUnit = v.produto.valor_venda
				}else{
					nome = v.nome;
					valorUnit = v.valor_venda
				}
			}

			let item = null
			if (typeof v.produto !== 'undefined') {

				item = {
					cont: cont++,
					id: v.produto_id,
					nome: nome,
					quantidade: v.quantidade,
					valor: parseFloat(valorUnit) + parseFloat(v.valorAdicional),
					pizza: v.maiorValor ? true : false,
					itemPedido: v.item_pedido
				}
			}else{
				item = {
					cont: cont++,
					id: v.id,
					nome: nome,
					quantidade: 1 + "",
					valor: (valorUnit),
					pizza: false,
					itemPedido: null
				}
			}


			ITENS.push(item)


			TOTAL += parseFloat((item.valor * item.quantidade));

		});
		let t = montaTabela();

		let valor_total = $('#valor_total').val();
		if(valor_total > TOTAL){ 
			TOTAL = valor_total
			VALORACRESCIMO = 0;
		}


		atualizaTotal();
		$('#body').html(t);
		let codigo_comanda = $('#codigo_comanda_hidden').val();

		COMANDA = codigo_comanda;

	}

});

$('#desconto').keyup(() => {
	$('#acrescimo').val('0')
	let desconto = $('#desconto').val();
	// if(!desconto){ $('#desconto').val('0'); desconto = 0}

	if(desconto){
		desconto = parseFloat(desconto.replace(",", "."))
		DESCONTO = 0;
		if(desconto > TOTAL && $('#desconto').val().length > 2){
			// Materialize.toast('ERRO, Valor desconto maior que o valor total', 4000)
			$('#desconto').val("");
		}else{
			DESCONTO = desconto;

			atualizaTotal();
		}
	}
})

function pad(s) {
	return (s < 10) ? '0' + s : s;
}

function categoria(cat){

	desmarcarCategorias(() => {
		$('#cat_' + cat).addClass('ativo')
	})
	
	produtosDaCategoria(cat, (res) => {
		// console.log(res)
		montaProdutosPorCategoria(res, (html) => {
			$('#prods').html(html)
		})
	})
}

function desmarcarCategorias(call){
	CATEGORIAS.map((v) => {
		$('#cat_' + v.id).removeClass('ativo')
		$('#cat_' + v.id).removeClass('desativo')
	})
	$('#cat_todos').removeClass('desativo')
	$('#cat_todos').removeClass('ativo')

	call(true)
}

// function produtosDaCategoria(cat, call){
// 	let lista_id = $('#lista_id').val();
// 	// $('#codBarras').focus()
// 	temp = [];
// 	if(cat != 'todos'){
// 		PRODUTOS.map((v) => {
// 			if(v.categoria_id == cat){
// 				temp.push(v)
// 			}
// 		})
// 	}else{
// 		temp = PRODUTOS
// 	}
// 	call(temp)
// }

function montaProdutosPorCategoria(produtos, call){
	console.clear()
	$('#prods').html('')
	let lista_id = $('#lista_id').val();

	let html = '';
	produtos.map((p) => {
		// console.log(p)
		html += '<div class="col-sm-12 col-lg-6 col-md-6 col-xl-4" id="atalho_add" '
		html += 'onclick="adicionarProdutoRapido2(\''+ p.id +'\')">'
		html += '<div class="card card-custom gutter-b example example-compact">'
		html += '<div class="card-header" style="height: 200px;">'
		html += '<div class="symbol symbol-circle symbol-lg-100">'
		if(p.imagem == ''){
			html += '<img class="img-prod" src="/imgs/no_image.png">'
		}else{
			html += '<img class="img-prod" src="/imgs_produtos/'+p.imagem+'">'
		}
		html += '</div>'
		html += '<h6 style="font-size: 12px; width: 100%" class="kt-widget__label">'
		html += p.nome.substr(0, 40) + '</h6>'
		html += '<h6 style="font-size: 12px;" class="text-danger" class="kt-widget__label">R$ '
		if(lista_id == 0){
			html += formatReal(parseFloat(p.valor_venda).toFixed(casas_decimais).replace('.', ',')) + '</h6>'
		}else{
			let v = 0;
			let temNaLista = 0;
			p.lista_preco.map((l) => {
				if(lista_id == l.lista_id){
					temNaLista = 1;
					if(l.valor){
						html += formatReal(l.valor) + '</h6>'
					}else{
						html += formatReal(parseFloat(p.valor_venda).toFixed(casas_decimais).replace('.', ',')) + '</h6>'
					}
				}
			})
			if(temNaLista == 0){
				html += formatReal(parseFloat(p.valor_venda).toFixed(casas_decimais).replace('.', ',')) + '</h6>'
			}
		}
		if(p.gerenciar_estoque == 1){
			html += '<h6 style="font-size: 10px; margin-right: -15px;" class="text-info" class="kt-widget__label">';
			html += 'Estoque: '
			html += p.estoque_atual
			html += '</h6>'
		}

		html += '</div></div></div>'
	})

	call(html)
}

function adicionarProdutoRapido(produto){

	let lista_id = $('#lista_id').val();

	// console.log(produto)
	// console.log(produto.nome)
	produto = JSON.parse(produto)
	PRODUTO = produto

	if(lista_id == 0){

		$('#valor_item').val(parseFloat(produto.valor_venda).toFixed(casas_decimais))
	}else{
		produto.lista_preco.map((l) => {
			if(lista_id == l.lista_id){
				$('#valor_item').val(parseFloat(l.valor).toFixed(casas_decimais))
			}
		})
	}
	$('#quantidade').val(1)
	addItem()
}

function novaHora() {
	var date = new Date();
	let v = [date.getHours(), date.getMinutes()].map(pad).join(':');
	$('#horas').html(v);
}

function novaData() {
	var date = new Date();
	let v = [date.getDate(), date.getMonth()+1, date.getFullYear()].map(pad).join('/');
	$('#data').html(v);
}

function apontarObs(){
	let obs = $('#obs').val();
	OBSERVACAO = obs;

	$('#modal-obs').modal('hide')
}

function setarObservacaoItem(){
	let obs = $('#obs-item').val();
	OBSERVACAOITEM = obs;

	$('#modal-obs-item').modal('hide')
}

$('#autocomplete-cliente').on('keyup', () => {
	$('#cliente-nao').css('display', 'block');
	CLIENTE = null;
})

function formatReal(v){
	return v.toLocaleString('pt-br',{style: 'currency', currency: 'BRL', minimumFractionDigits: casas_decimais});
}

function getProdutos(data){
	$.ajax
	({
		type: 'GET',
		url: path + 'produtos/all',
		dataType: 'json',
		success: function(e){
			data(e)

		}, error: function(e){
			console.log(e)
		}

	});
}

function getClientes(data){
	$.ajax
	({
		type: 'GET',
		url: path + 'clientes/all',
		dataType: 'json',
		success: function(e){
			data(e)
		}, error: function(e){
			console.log(e)
		}

	});
}

function getCliente(id, data){
	$.ajax
	({
		type: 'GET',
		url: path + 'clientes/find/'+id,
		dataType: 'json',
		success: function(e){
			data(e)
		}, error: function(e){
			console.log(e)
		}

	});
}

function getVendasEmAbertoContaCredito(id, data){
	$.ajax
	({
		type: 'GET',
		url: path + 'vendasEmCredito/somaVendas/'+id,
		dataType: 'json',
		success: function(e){
			data(e)
		}, error: function(e){
			console.log(e)
		}

	});
}

$('#codBarras').keyup((v) => {
	setTimeout(() => {
		let cod = v.target.value
		if(cod.length > 10){
			$('#codBarras').val('')
			getProdutoCodBarras(cod, (data) => {
				if(data){
					setTimeout(() => {
						addItem();
					}, 400)
				}else{
					
				}
			})

		}
	}, 500)
})

$('#focus-codigo').click(() => {
	$('#codBarras').focus()
})

$('#focus-codigo').dblclick(() => {
	$('#modal-cod-barras').modal('show')
	$('#cod-barras2').focus()
})

$('#lista_id').change(() => {
	let lista = $('#lista_id').val();
	$('#produto-search').val('')
	$('#valor_item').val('0,00')
	$('#quantidade').val('1')
})

$('#select-doc').change(() => {
	let tipo = $('#select-doc').val()
	if(tipo == 'CPF'){
		$('#tipo-doc').html('CPF')
		$('#cpf').attr("placeholder", "CPF")
		$('#cpf').mask('000.000.000-00', {reverse: true});
	}else{
		$('#tipo-doc').html('CNPJ')
		$('#cpf').attr("placeholder", "CNPJ")
		$('#cpf').mask('00.000.000/0000-00', {reverse: true});
	}
})

function getProduto(id, data){
	console.log(LISTAID)
	$.ajax
	({
		type: 'GET',
		url: path + 'produtos/getProdutoVenda/' + id + '/' + LISTAID,
		dataType: 'json',
		success: function(e){
			data(e)
		}, error: function(e){
			console.log(e)
		}
	});
}

$('#produto-search').keyup(() => {
	console.clear()
	let pesquisa = $('#produto-search').val();

	if(pesquisa.length > 1){
		montaAutocomplete(pesquisa, (res) => {
			if(res){
				if(res.length > 0){
					montaHtmlAutoComplete(res, (html) => {
						$('.search-prod').html(html)
						$('.search-prod').css('display', 'block')
					})

				}else{
					$('.search-prod').css('display', 'none')
				}
			}else{
				$('.search-prod').css('display', 'none')
			}
		})
	}else{
		$('.search-prod').css('display', 'none')
	}
})

function montaAutocomplete(pesquisa, call){
	$.get(path + 'produtos/autocomplete', {pesquisa: pesquisa})
	.done((res) => {
		console.log(res)
		call(res)
	})
	.fail((err) => {
		console.log(err)
		call([])
	})
}

function montaHtmlAutoComplete(arr, call){
	let html = ''
	arr.map((rs) => {
		let p = rs.nome
		if(rs.grade){
			p += ' ' + rs.str_grade
		}

		if(rs.referencia != ""){
			p += ' | REF: ' + rs.referencia
		}

		if(parseFloat(rs.estoqueAtual) > 0){
			p += ' | Estoque: ' + rs.estoqueAtual
		}
		html += '<label onclick="selectProd('+rs.id+')">'+p+'</label>'
	})
	call(html)
}


function selectProd(id){
	let lista_id = $('#lista_id').val();
	$.get(path + 'produtos/autocompleteProduto', {id: id, lista_id: lista_id})
	.done((res) => {
		PRODUTO = res
		console.log(PRODUTO)

		let p = PRODUTO.nome
		if(PRODUTO.referencia != ""){
			p += ' | REF: ' + PRODUTO.referencia
		}
		if(parseFloat(PRODUTO.estoqueAtual) > 0){
			p += ' | Estoque: ' + PRODUTO.estoqueAtual
		}

		$('#valor_item').val(parseFloat(PRODUTO.valor_venda).toFixed(casas_decimais))
		$('#quantidade').val(1)
		$('#produto-search').val(p)
	})
	.fail((err) => {
		console.log(err)
		swal("Erro", "Erro ao encontrar produto", "error")
	})
	$('.search-prod').css('display', 'none')
}

function adicionarProdutoRapido2(id){
	// let lista_id = $('#lista_id').val();

	// PRODUTOS.map((p) => {
	// 	if(p.id == id){
	// 		PRODUTO = p

	// 		if(lista_id == 0){
	// 			$('#valor_item').val(parseFloat(p.valor_venda).toFixed(casas_decimais))
	// 		}else{
	// 			p.lista_preco.map((l) => {
	// 				if(lista_id == l.lista_id){
	// 					$('#valor_item').val(parseFloat(l.valor).toFixed(casas_decimais))
	// 				}
	// 			})
	// 		}
	// 		// $('#valor_item').val(p.valor_venda)
	// 		$('#quantidade').val(1)
	// 		addItem()
	// 	}
	// })

	let lista_id = $('#lista_id').val();
	$.get(path + 'produtos/autocompleteProduto', {id: id, lista_id: lista_id})
	.done((res) => {
		PRODUTO = res
		console.log(PRODUTO)

		let p = PRODUTO.nome
		if(PRODUTO.referencia != ""){
			p += ' | REF: ' + PRODUTO.referencia
		}
		if(parseFloat(PRODUTO.estoqueAtual) > 0){
			p += ' | Estoque: ' + PRODUTO.estoqueAtual
		}

		$('#valor_item').val(parseFloat(PRODUTO.valor_venda).toFixed(casas_decimais))
		$('#quantidade').val(1)
		addItem()

		$('#produto-search').val(p)
	})
	.fail((err) => {
		console.log(err)
		swal("Erro", "Erro ao encontrar produto", "error")
	})
	$('.search-prod').css('display', 'none')
}

$('#pesquisa-produto-lateral').keyup(() => {
	let pesquisa = $('#pesquisa-produto-lateral').val();

	if(pesquisa.length > 1){

		$.get(path + 'produtos/autocomplete', {pesquisa: pesquisa})
		.done((res) => {
			console.log(res)
			montaProdutosPorCategoria(res, (html) => {
				$('#prods').html(html)
			})
		})
		.fail((err) => {
			console.log(err)
		})

	}
})

$('#kt_select2_1').change(() => {
	let id = $('#kt_select2_1').val()
	let lista_id = $('#lista_id').val()
	PRODUTOS.map((p) => {
		if(p.id == id){
			if(p.grade == 0){

				PRODUTO = p
				if(lista_id == 0){

					$('#valor_item').val(parseFloat(p.valor_venda).toFixed(casas_decimais))
				}else{
					p.lista_preco.map((l) => {
						if(lista_id == l.lista_id){
							$('#valor_item').val(l.valor)
						}
					})
				}

				$('#quantidade').val(1)
			}else{
				montaGrade(p.referencia_grade)
				$('#modal-grade').modal('show')
			}
		}
	})
})

function montaGrade(referencia){
	let prods = PRODUTOS.filter((x) => {
		if(referencia == x.referencia_grade) return x
	})
	console.log(prods)
	let html = ''
	prods.map((p) => {
		html += '<div class="row" style="height: 40px">'
		html += '<div class="col-sm-8 col-lg-8 col-10">'
		html += '<h4>'+ p.str_grade +'</h4>'
		html += '</div>'
		html += '<div class="col-sm-2 col-lg-2 col-2">'
		html += '<button onclick="selectGrade('+p.id+')" class="btn btn-success btn-sm btn-block">'
		html += '<i class="la la-check"></i></button>'
		html += '</div></div>'
	})
	$('.grade-prod').html(html)
}

function selectGrade(id){
	let p = PRODUTOS.filter((x) => { return x.id == id })
	p = p[0]
	PRODUTOGRADE = p
	LIMITEDESCONTO = parseFloat(p.limite_maximo_desconto);
	VALORDOPRODUTO = parseFloat(p.valor_venda);
	console.log(VALORDOPRODUTO)
	let lista_id = $('#lista_id').val()

	$('#quantidade').val('1')
	if(lista_id == 0){
		$('#valor_item').val(p.valor_venda)
	}else{
		p.lista_preco.map((l) => {
			if(lista_id == l.lista_id){
				$('#valor_item').val(l.valor)
			}
		})
	}
	$('#modal-grade').modal('hide')
}

$('#finalizar-venda').click(() => {
	$('#modal-venda').modal('show')
})

function somaQuantidadeProdutoAdicionado(produto, quantidadeAdicionar, call){
	console.clear()
	
	console.log(produto)
	let quantidade = 0;
	ITENS.map((p) => {
		if(p.codigo == produto.id){
			quantidade += parseFloat(p.quantidade)
		}
	})

	quantidade += parseFloat(quantidadeAdicionar);

	console.log("qtd", quantidade)

	if(produto.gerenciar_estoque == 1 && (!produto.estoque || produto.estoque.quantidade < quantidade)){
		call(false)
	}else{
		call(true)
	}
}

function addItem(){
	if(caixaAberto){
		// $('#codBarras').focus();

		if(PRODUTOGRADE != null){
			PRODUTO = PRODUTOGRADE
		}

		let valorItem = $('#valor_item').val().replace(",", ".");
		if(PRODUTO != null && valorItem > 0){
			verificaProdutoIncluso((call) => {

				console.log("cal", call)
				$('#codBarras').val('')
				if(call >= 0){
					let quantidade = $('#quantidade').val() ? $('#quantidade').val() :  '1.00';
					quantidade = quantidade.replace(",", ".");
					let valor = $('#valor_item').val();
					console.log("teste", (parseFloat(quantidade) + parseFloat(call)));

					PRODUTOS.push(PRODUTO)
					if(PRODUTO.gerenciar_estoque == 1 && (parseFloat(quantidade) + parseFloat(call)) > PRODUTO.estoque_atual){
						swal("Erro", 'O estoque atual deste produto é de ' + PRODUTO.estoque_atual, "warning")
						$('#quantidade').val('1')

					}else{

						if(quantidade.length > 0 && parseFloat(quantidade.replace(",", ".")) > 0 && valor.length > 0 && parseFloat(valor.replace(",", ".")) > 0 && PRODUTO != null){
							TOTAL += parseFloat(valor.replace(',','.'))*(quantidade.replace(',','.'));

							let nomeProduto = PRODUTO.nome
							let item = {
								cont: (ITENS.length+1),
								obs: OBSERVACAOITEM,
								id: PRODUTO.id,
								nome: nomeProduto,
								quantidade: $('#quantidade').val(),
								valor: $('#valor_item').val()
							}

							console.log(item)

							$('#body').html("");
							ITENS.push(item);

							console.log(ITENS)

							limparCamposFormProd();
							atualizaTotal();

							let v = $('#valor_recebido').val();
							v = v.replace(",", ".");

							if(PDV_VALOR_RECEBIDO == 1){
								$('#valor_recebido').val(TOTAL)
								// Materialize.updateTextFields();
							}

							if(ITENS.length > 0 && ((parseFloat(v) >= TOTAL))){
								$('#finalizar-venda').removeAttr('disabled');
							}else{
								$('#finalizar-venda').attr('disabled', true);
							}

							let t = montaTabela();

							$('#body').html(t);


							PRODUTO = null;
							$('#obs-item').val('');
							OBSERVACAOITEM = "";
							$('#kt_select2_1').val('-1').change()
							$('#produto-search').val('')
						}
					}
				}else{
					swal('Cuidado', 'Informe corretamente para continuar', 'warning')
				}
			});
		}else{
			swal('Cuidado', 'Informe corretamente para continuar', 'warning')
		}
	}else{
		swal("Erro", "Abra o caixa para vender!!", "error")
	}
	QUANTIDADE = 1
}

function setaObservacao(){
	$('#modal-obs').modal('show')
}

function setaDesconto(){
	if(TOTAL == 0){
		swal("Erro", "Total da venda é igual a zero", "warning")
	}else{
		swal({
			title: 'Valor desconto?',
			text: 'Ultiliza ponto(.) ao invés de virgula!',
			content: "input",
			button: {
				text: "Ok",
				closeModal: false,
				type: 'error'
			}
		}).then(v => {
			if(v) {

				let desconto = v;
				if(desconto.substring(0, 1) == "%"){
					let perc = desconto.substring(1, desconto.length);
					DESCONTO = TOTAL * (perc/100);
				}else{
					desconto = desconto.replace(",", ".")
					DESCONTO = parseFloat(desconto)
				}
				console.log(DESCONTO)
				if(desconto.length == 0) DESCONTO = 0;

				$('#valor_desconto').html(formatReal(DESCONTO))
				atualizaTotal()

			}
			swal.close()
			$('#codBarras').focus()

		});
	}
}

function setaQuantidade(){
	
	swal({
		title: 'Quantidade do próximo item',
		text: 'Ultiliza ponto(.) ao invés de virgula!',
		content: "input",
		button: {
			text: "Ok",
			closeModal: false,
			type: 'error'
		}
	}).then(v => {
		if(v) {
			if(v.length == 0){
				QUANTIDADE = 1;
			}else{
				QUANTIDADE = v
			}
		}

		swal.close()
		$('#codBarras').focus()

	});
	
}

function setaDesconto(){
	if(TOTAL == 0){
		swal("Erro", "Total da venda é igual a zero", "warning")
	}else{
		swal({
			title: 'Valor desconto?',
			text: 'Ultiliza ponto(.) ao invés de virgula!',
			content: "input",
			button: {
				text: "Ok",
				closeModal: false,
				type: 'error'
			}
		}).then(v => {
			if(v) {

				let desconto = v;
				if(desconto.substring(0, 1) == "%"){
					let perc = desconto.substring(1, desconto.length);
					DESCONTO = TOTAL * (perc/100);
				}else{
					desconto = desconto.replace(",", ".")
					DESCONTO = parseFloat(desconto)
				}
				console.log(DESCONTO)
				if(desconto.length == 0) DESCONTO = 0;

				$('#valor_desconto').html(formatReal(DESCONTO))
				atualizaTotal()

			}
			swal.close()
			$('#codBarras').focus()

		});
	}
}

function setaAcresicmo(){
	if(TOTAL == 0){
		swal("Erro", "Total da venda é igual a zero", "warning")
	}else{
		swal({
			title: 'Valor acrescimo?',
			text: 'Ultiliza ponto(.) ao invés de virgula!',
			content: "input",
			button: {
				text: "Ok",
				closeModal: false,
				type: 'error'
			}
		}).then(v => {
			if(v) {

				let acrescimo = v;
				if(acrescimo > 0){
					DESCONTO = 0;
					$('#valor_desconto').html(formatReal(DESCONTO))
				}

				let total = TOTAL+VALORBAIRRO;

				if(acrescimo.substring(0, 1) == "%"){
					let perc = acrescimo.substring(1, acrescimo.length);
					VALORACRESCIMO = total * (perc/100);
				}else{
					acrescimo = acrescimo.replace(",", ".")
					VALORACRESCIMO = parseFloat(acrescimo)
				}

				if(acrescimo.length == 0) VALORACRESCIMO = 0;
				atualizaTotal();
				VALORACRESCIMO = parseFloat(VALORACRESCIMO)
				$('#valor_acrescimo').html(formatReal(VALORACRESCIMO))

				atualizaTotal()
				$('#codBarras').focus()
			}
			swal.close()

		});
	}
}

$('#adicionar-item').click(() => {
	addItem();
})

function atualizaTotal(){

	let valor_recebido = $('#valor_recebido').val();
	if(!valor_recebido) valor_recebido = 0;
	if(valor_recebido > 0){
		valor_recebido = valor_recebido.replace(",", ".");
		valor_recebido = parseFloat(valor_recebido)
	}

	console.log(TOTAL)
	console.log(TOTAL + VALORBAIRRO + VALORACRESCIMO - DESCONTO)
	if($('#tipo-pagamento').val() == '01'){
		if((TOTAL + VALORBAIRRO + VALORACRESCIMO - DESCONTO) > valor_recebido){
			$('#finalizar-venda').attr('disabled', true)
		}else{
			$('#finalizar-venda').removeAttr('disabled')
		}
	}else{
		$('#finalizar-venda').removeAttr('disabled')
	}
	
	console.log(valor_recebido)
	if(!$('#valor_recebido').val()){
		$('#finalizar-venda').attr('disabled', true)
	}
	// $('#total-venda').html(formatReal(TOTAL + VALORBAIRRO + VALORACRESCIMO - DESCONTO));
	console.log(VALORACRESCIMO)
	$('#total-venda').html(formatReal(TOTAL + VALORBAIRRO + VALORACRESCIMO - DESCONTO));
}

function montaTabela(){
	let t = ""; 
	let quantidades = 0;


	ITENS.map((v) => {
		console.log(v)

		t += '<tr class="datatable-row" style="left: 0px;">'
		t += '<td class="datatable-cell">'
		t += '<span class="codigo" style="width: 50px;">'
		t += v.cont + '</span>'
		t += '</td>'

		t += '<td class="datatable-cell">'
		t += '<span class="codigo" style="width: 50px;">'
		t += v.id
		t += '</span></td>'

		t += '<td class="datatable-cell">'
		t += '<span class="codigo" style="width: 200px;">'
		t += v.nome + (v.obs ? " [OBS: "+v.obs+"]" : "")
		t += '</span></td>'

		t += '<td class="datatable-cell">'
		t += '<span class="codigo" style="width: 120px;">'
		t += '<div class="form-group mb-2">'
		t += '<div class="input-group">'
		t += '<div class="input-group-prepend">'
		t += '<button onclick="subtraiItem('+v.cont+')" class="btn btn-danger" type="button">-</button>'
		t += '</div>'
		t += '<input type="text" readonly class="form-control" value="'+v.quantidade+'">'
		t += '<div class="input-group-append">'
		t += '<button onclick="incrementaItem('+v.cont+')" class="btn btn-success" type="button">+</button>'
		t += '</div></div></div></span></td>'

		t += '<td class="datatable-cell">'
		t += '<span class="codigo" style="width: 120px;">'
		t += formatReal(v.valor)
		t += '</span></td>'

		t += '<td class="datatable-cell">'
		t += '<span class="codigo" style="width: 120px;">'
		try{
			t += formatReal((v.valor.replace(",", ".")) * (v.quantidade.replace(",", ".")))
		}catch{
			t += formatReal((v.valor) * (v.quantidade))

		}
		t += '</span></td>'
		t += '</tr>'

		quantidades += parseInt(v.quantidade);
	});

	$('#qtd-itens').html(ITENS.length);
	$('#_qtd').html(quantidades);
	return t
}

function subtraiItem(id){
	let temp = [];
	let soma = 0
	ITENS.map((v) => {
		if(v.cont != id){
			temp.push(v)
			soma += parseFloat(v.valor.replace(',','.'))*(v.quantidade.replace(',','.'));
		}else{
			if(v.quantidade > 1){
				v.quantidade = (parseFloat(v.quantidade) - 1) + "";
				soma += parseFloat(v.valor.replace(',','.')*v.quantidade.replace(',','.'));
				temp.push(v)
			}
		}
	});
	TOTAL = soma
	ITENS = temp
	let t = montaTabela();
	atualizaTotal();
	$('#body').html(t);
	if(PDV_VALOR_RECEBIDO){
		$('#valor_recebido').val(TOTAL)
	}
}

$('#click-client').click(() => {
	$('#modal-cliente').modal('show')
})

function selecionarCliente(){
	let cliente = $('#kt_select2_3').val();
	CLIENTES.map((c) => {
		if(c.id == cliente){
			CLIENTE = c
		}
	})
	$('#conta_credito-btn').removeClass('disabled')
	$('#modal-cliente').modal('hide')
}

function verificaProdutoInclusoAtalho(id, call){
	let cont = 0;
	ITENS.map((rs) => {
		if(id == rs.cont){
			cont += parseFloat(rs.quantidade);
		}
	})
	call(cont);
}

function incrementaItem(id){
	let temp = [];
	let soma = 0
	console.clear()
	ITENS.map((v) => {
		console.log(v)
		if(v.cont != id){
			temp.push(v)
			soma += parseFloat(v.valor.replace(',','.'))*(v.quantidade);
		}else{
			let prod = PRODUTOS.filter((p) => { return p.id == v.id})
			prod = prod[0]
			quantidade = (parseFloat(v.quantidade))

			verificaProdutoInclusoAtalho(id, (call) => {
				console.log(call)
				console.log(quantidade + 1)
				if(prod.gerenciar_estoque == 1 && (quantidade + 1) > parseFloat(prod.estoque_atual)){
					swal("Erro", 'O estoque atual deste produto é de ' + prod.estoque_atual, "warning")
					temp.push(v)
					soma += parseFloat(v.valor.replace(',','.'))*(v.quantidade);
				}else{
					v.quantidade = (parseFloat(call)+1) + "";
					soma += parseFloat(v.valor.replace(',','.')*v.quantidade);
					temp.push(v)
				}
			})
		}
	});
	TOTAL = soma
	ITENS = temp
	let t = montaTabela();
	atualizaTotal();
	$('#body').html(t);
	if(PDV_VALOR_RECEBIDO){
		$('#valor_recebido').val(formatReal(TOTAL))
	}
}

function limparCamposFormProd(){
	$('#autocomplete-produto').val('');
	$('#quantidade').val('1');
	$('#valor_item').val(parseFloat(0).toFixed(casas_decimais));
}

function verificaProdutoIncluso(call){
	let cont = 0;
	ITENS.map((rs) => {
		if(PRODUTO.id == rs.id){
			cont += parseFloat(rs.quantidade);
		}
	})
	call(cont);
}

function getProdutoCodBarras(cod, data){
	let tamanho = ITENS.length;
	console.log(tamanho)
	$.ajax
	({
		type: 'GET',
		url: path + 'produtos/getProdutoCodBarras/'+cod,
		dataType: 'json',
		success: function(e){
			data(e)
			if(e){
				PRODUTO = e;
				$('#nome-produto').html(e.nome);
				$('#valor_item').val(e.valor_venda);
				$('#quantidade').val(QUANTIDADE);
			}else{
				if(cod.length > 10){
					//validar pelo cod balança

					let id = parseInt(cod.substring(1, DIGITOBALANCA));

					console.log(id)

					$.get(path+'produtos/getProdutoCodigoReferencia/'+id)
					.done((res) => {

						let valor = cod.substring(7,12);

						let temp = valor.substring(0,3) + '.' +valor.substring(3,5);
						valor = parseFloat(temp)
						console.log(valor)

						PRODUTO = res;

						$('#nome-produto').html(PRODUTO.nome);
						let quantidade = QUANTIDADE;
						if(PRODUTO.unidade_venda == 'KG'){
							if(TIPOUNIDADEBALANCA == 1){
								let valor_venda = PRODUTO.valor_venda;
								quantidade = valor/valor_venda;
								quantidade = quantidade.toFixed(3);
								valor = valor_venda;
							}else{

								quantidade = valor
								valor = PRODUTO.valor_venda;
							}
						}
						$('#valor_item').val(valor);
						$('#quantidade').val(quantidade);
						let tamanho2 = ITENS.length;
						if(tamanho2 == tamanho){
							console.log("inserindo");
							$('#adicionar-item').trigger('click');
						}

					})
					.fail((err) => {
						// alert('Produto nao encontrado!')
						swal("Erro", 'Produto nao encontrado!!', "warning")

						$('#autocomplete-produto').val('')

					})

				}
			}

		}, error: function(e){
			console.log(e)
		}

	});
}

function verificaCaixa(data){
	$.ajax
	({
		type: 'GET',
		url: path + 'aberturaCaixa/verificaHoje',
		dataType: 'json',
		success: function(e){
			console.log(e)
			data(e)

		}, error: function(e){
			console.log(e)
		}

	});
}

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
				caixaAberto = true;
				$('#modal1').modal('hide');
				swal("Sucesso", "Caixa aberto", "success")


			}, error: function(e){
				$('#modal1').modal('hide');
				swal("Erro", "Erro ao abrir caixa", "error")
				console.log(e)
			}

		});
	}else{
		// alert('Insira um valor válido')
		swal("Erro", 'Insira um valor válido', "warning")

	}
	
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
			$('#modal2').modal('hide');
			$('#valor_sangria').val('');
			swal("Sucesso", "Sangria realizada!", "success")


		}, error: function(e){
			console.log(e)
			// swal("Erro", "Erro ao realizar sangria!", "error")
			try{
				swal("Erro", e.responseJSON, "error")
				.then(() => {
					$('#modal2').modal('hide');
				})
			}catch{
				swal("Erro", "Erro ao realizar sangria!", "error")
				.then(() => {
					$('#modal2').modal('hide');
				})
			}

		}

	});
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

		}, error: function(e){
			console.log(e)
			swal("Erro", "Erro ao realizar suprimento de caixa!", "error")

		}

	});
}

function getSangriaDiaria(data){
	$.ajax
	({
		type: 'GET',
		url: path + 'sangriaCaixa/diaria',
		dataType: 'json',
		success: function(e){
			data(e)

		}, error: function(e){
			console.log(e)
		}

	});
}

function getSuprimentoDiario(data){
	$.ajax
	({
		type: 'GET',
		url: path + 'suprimentoCaixa/diaria',
		dataType: 'json',
		success: function(e){
			data(e)

		}, error: function(e){
			console.log(e)
		}

	});
}

function getAberturaDiaria(data){

	$.ajax
	({
		type: 'GET',
		url: path + 'aberturaCaixa/verificaHoje',
		dataType: 'json',
		success: function(e){
			console.log(e)
			data(e)

		}, error: function(e){
			console.log(e)
		}

	});
}

function getVendaDiaria(data){
	$.ajax
	({
		type: 'GET',
		url: path + 'vendasCaixa/diaria',
		dataType: 'json',
		success: function(e){
			data(e)

		}, error: function(e){
			console.log(e)
		}

	});
}

function fluxoDiario(){
	$('#preloader1').css('display', 'block');
	getSangriaDiaria((sangrias) => {
		getSuprimentoDiario((suprimentos) => {

			let elem = "";
			let totalSangria = 0;
			let totalSuprimento = 0;
			sangrias.map((v) => {

				elem += "<p> Horario: "
				elem += "<strong>" + v.data_registro.substring(10, 16) + "</strong>, Valor: "
				elem += "<strong> R$ " + formatReal(v.valor) + "</strong>, Usuario: "
				elem += "<strong class='text-info'>" + v.nome_usuario + "</strong>, Obs: "
				elem += "<strong class='text-info'>" + v.observacao + "</strong>"

				elem += "</p>";
				totalSangria += parseFloat(v.valor);
			})

			elem += "<h6>Total: <strong class='text-danger'>" + formatReal(totalSangria) + "</strong></h6>";
			elem += "<hr>"
			$('#fluxo_sangrias').html(elem)
			elem = ""
			suprimentos.map((v) => {

				elem += "<p> Horario: "
				elem += "<strong>" + v.created_at.substring(10, 16) + "</strong>, Valor: "
				elem += "<strong> R$ " + formatReal(v.valor) + "</strong>, Usuario: "
				elem += "<strong class='text-info'>" + v.nome_usuario + "</strong>, Obs: "
				elem += "<strong class='text-info'>" + v.observacao + "</strong>"
				elem += "</p>";
				totalSuprimento += parseFloat(v.valor);
			})
			elem += "<h6>Total: <strong class='text-danger'>" + formatReal(totalSuprimento) + "</strong></h6>";
			elem += "<hr>"
			
			$('#fluxo_suprimentos').html(elem)

			getAberturaDiaria((abertura) => {
				abertura = abertura.replace(",", ".")
				elem = "<p> Valor: ";
				elem += "<strong class='text-danger'>R$ "+formatReal(abertura)+"</strong>";
				elem += "</p>";
				elem += "<hr>"

				$('#fluxo_abertura_caixa').html(elem);
				getVendaDiaria((vendas) => {

					elem = "";
					let totalVendas = 0;
					vendas.map((v) => {
						console.log(v)
						elem += "<p> Horario: "
						elem += "<strong>" + v.data_registro.substring(10, 16) + "</strong>, Valor: "
						elem += "<strong> R$ " + formatReal(parseFloat(v.valor_total)) + "</strong>, Tipo Pagamento: "
						elem += "<strong>" + v.tipo_pagamento + "</strong>"
						elem += "</p>";
						totalVendas += parseFloat(parseFloat(v.valor_total));
					})
					elem += "<h6>Total: <strong class='text-primary'>" + formatReal(totalVendas) + "</strong></h6>";
					elem += "<hr>";
					$('#fluxo_vendas').html(elem);
					$('#total_caixa').html(formatReal((totalVendas+parseFloat(abertura)) - totalSangria + totalSuprimento));

					$('#preloader1').css('display', 'none');
				});
			})
		})
	})
	if(caixaAberto){
		$('#modal3').modal('open');
	}else{

		// var $toastContent = $('<span>Por favor abra o caixa!</span>').add($('<button class="btn-flat toast-action">OK</button>'));
		// Materialize.toast($toastContent, 5000);
		swal('Erro', 'Por favor abra o caixa!', 'error')
		.then(() => {
			location.reload();
		})
	}
}

function esconderTodasMoedas(){
	$('.50_reais').css('display', 'none');
	$('.20_reais').css('display', 'none');
	$('.10_reais').css('display', 'none');
	$('.5_reais').css('display', 'none');
	$('.2_reais').css('display', 'none');
	$('.1_real').css('display', 'none');
	$('.50_centavo').css('display', 'none');
	$('.25_centavo').css('display', 'none');
	$('.50_centavo').css('display', 'none');
	$('.5_centavo').css('display', 'none');
}

$('#valor_recebido').on('keyup', (event) => {
	esconderTodasMoedas();
	let t = TOTAL;
	let v = $('#valor_recebido').val();
	v = v.replace(",", ".");

	let troco = v - (TOTAL - DESCONTO);
	if(troco > 0){

		$('#valor-troco').html(formatReal(troco))
	}else{
		$('#valor-troco').html('R$ 0,00')
	}
	
	if(ITENS.length > 0 && (parseFloat(v) >= (TOTAL + VALORBAIRRO + VALORACRESCIMO - DESCONTO))){
		$('#finalizar-venda').removeAttr('disabled');
	}else{
		$('#finalizar-venda').attr('disabled', true);
	}

	console.log(TOTAL)

	if(v.length > 0 && parseFloat(v) > TOTAL && TOTAL > 0){
		v = parseFloat(v);

		if (event.keyCode === 13) {

			let troco = v - (t - DESCONTO + VALORACRESCIMO);
			$("#valor_troco").html(formatReal(troco))
			$('#modal4').modal('show');

			let resto = troco;
			notas = [];

			if(parseInt(troco / 50) > 0 && resto > 0){

				resto = troco % 50;
				$('#qtd_50_reais').html(' X'+1);
				$('.50_reais').css('display', 'block');

			}
			if(parseInt(resto / 20) > 0){
				numeroNotas = parseInt(resto/20);
				$('#qtd_20_reais').html(' X'+numeroNotas);
				resto = resto%(20*numeroNotas);
				$('.20_reais').css('display', 'block');

			}
			if(parseInt(resto / 10) > 0){
				numeroNotas = parseInt(resto/10);
				$('#qtd_10_reais').html(' X'+numeroNotas);
				resto = resto%(10*numeroNotas);
				$('.10_reais').css('display', 'block');

			}
			if(parseInt(resto / 5) > 0){
				numeroNotas = parseInt(resto/5);
				$('#qtd_5_reais').html(' X'+numeroNotas);
				resto = duasCasas(resto%(5*numeroNotas));
				$('.5_reais').css('display', 'block');

			}
			if(parseInt(resto / 2) > 0){
				numeroNotas = parseInt(resto/2);
				$('#qtd_2_reais').html(' X'+numeroNotas);
				resto = duasCasas(resto%(2*numeroNotas));
				$('.2_reais').css('display', 'block');

			}

			if(parseInt(resto / 1) > 0){
				numeroNotas = parseInt(resto/1);
				$('#qtd_1_real').html(' X'+numeroNotas);
				resto = duasCasas(resto%(1*numeroNotas));
				$('.1_real').css('display', 'block');

			}

			if(parseInt(resto / 0.5) > 0){
				numeroNotas = parseInt(resto/0.5);
				$('#qtd_50_centavos').html(' X'+numeroNotas);
				resto = duasCasas(resto%(0.5*numeroNotas));
				$('.50_centavo').css('display', 'block');

			}

			if(parseInt(resto / 0.25) > 0){
				numeroNotas = parseInt(resto/0.25);
				$('#qtd_25_centavos').html(' X'+numeroNotas);
				resto = duasCasas(resto%(0.25*numeroNotas));
				$('.25_centavo').css('display', 'block');

			}

			if(parseInt(resto / 0.10) > 0){
				numeroNotas = parseInt(resto/0.10);
				$('#qtd_10_centavos').html(' X'+numeroNotas);
				resto = duasCasas(resto%(0.10*numeroNotas));
				$('.10_centavo').css('display', 'block');

			}


			if(parseInt(resto / 0.05) > 0){
				numeroNotas = parseInt(resto/0.05);
				$('#qtd_5_centavos').html(' X'+numeroNotas);
				resto = resto%(0.05*numeroNotas);
				$('.5_centavo').css('display', 'block');

			}

		}
	}
})

function duasCasas(valor){
	return parseFloat(valor.toFixed(2));
}

$('#autocomplete-produto').on('keyup', () => {
	let val = $('#autocomplete-produto').val();
	if($.isNumeric(val) && val.length > 6){
		getProdutoCodBarras(val, (data) => {
			setTimeout(() => {
				addItem();
				
			}, 400)
		})
	}
})

function verificaCliente(){

	if(CLIENTE == null){

		$('#modal-venda').modal('hide');
		$('#modal-cpf-nota').modal('show');
		$('#modal-cpf-nota').on('shown.bs.modal', function () {
			$('#cpf').focus()
		})
	} 
	else{ 
		finalizarVenda('fiscal')
	}
}

function validaCpf(){

	if(CLIENTE != null) return true;

	let strCPF = $('#cpf').val();
	let nome = $('#nome').val();
	if(strCPF.length == 0) return true;

	// if(nome == '' || nome == null || nome.length == 0) return false;
	
	strCPF = strCPF.replace(".", "");
	strCPF = strCPF.replace(".", "");
	strCPF = strCPF.replace("-", "");
	if(strCPF.length == 11){
		var Soma;
		var Resto;
		Soma = 0;
		if (strCPF == "00000000000") return false;

		for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
			Resto = (Soma * 10) % 11;

		if ((Resto == 10) || (Resto == 11))  Resto = 0;
		if (Resto != parseInt(strCPF.substring(9, 10)) ) return false;;

		Soma = 0;
		for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
			Resto = (Soma * 10) % 11;

		if ((Resto == 10) || (Resto == 11))  Resto = 0;
		if (Resto != parseInt(strCPF.substring(10, 11) ) ) return false;;

		return true;
	}else{
		let cnpj = strCPF
		cnpj = cnpj.replace(/[^\d]+/g,'');

		if (cnpj.length != 14)
			return false;

		if (cnpj == "00000000000000" || 
			cnpj == "11111111111111" || 
			cnpj == "22222222222222" || 
			cnpj == "33333333333333" || 
			cnpj == "44444444444444" || 
			cnpj == "55555555555555" || 
			cnpj == "66666666666666" || 
			cnpj == "77777777777777" || 
			cnpj == "88888888888888" || 
			cnpj == "99999999999999")
			return false;


		tamanho = cnpj.length - 2
		numeros = cnpj.substring(0,tamanho);
		digitos = cnpj.substring(tamanho);
		soma = 0;
		pos = tamanho - 7;
		for (i = tamanho; i >= 1; i--) {
			soma += numeros.charAt(tamanho - i) * pos--;
			if (pos < 2)
				pos = 9;
		}
		resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
		if (resultado != digitos.charAt(0))
			return false;

		tamanho = tamanho + 1;
		numeros = cnpj.substring(0,tamanho);
		soma = 0;
		pos = tamanho - 7;
		for (i = tamanho; i >= 1; i--) {
			soma += numeros.charAt(tamanho - i) * pos--;
			if (pos < 2)
				pos = 9;
		}
		resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
		if (resultado != digitos.charAt(1))
			return false;

		return true;
	}
}

$('#tipo-pagamento').change(() => {
	$('#valor_recebido').val('');

	let tipo = $('#tipo-pagamento').val();

	if(tipo == '06'){
		if(CLIENTE == null){
			swal("Alerta", "Informe o cliente!", "warning")
			$('#tipo-pagamento').val('--').change()
		}
	}

	if(tipo == '03' || tipo == '04'){
		$('#modal-cartao').modal('show')
	}

	if(tipo == '99'){
		$('#modal-pag-outros').modal('show')
	}

	if(tipo == '01'){
		$('#valor_recebido').removeAttr('disabled');
		$('#finalizar-venda').attr('disabled', true);

	}else{
		$('#valor_recebido').attr('disabled', 'true');
		$('#finalizar-venda').removeAttr('disabled');
	}
})

var ENVIANDO = false
function finalizarVenda(acao) {
	$('#btn_nao_fiscal').attr('disabled')

	if(ENVIANDO == false){
		ENVIANDO = true
		let validCpf = validaCpf();
		if(validCpf == true || acao != 'fiscal'){

			let valorRecebido = $('#valor_recebido').val();
			let troco = 0;
			if(valorRecebido.length > 0 && parseFloat(valorRecebido) > (TOTAL + VALORACRESCIMO + VALORBAIRRO - DESCONTO)){
				troco = parseFloat(valorRecebido) - (TOTAL + VALORACRESCIMO + VALORBAIRRO - DESCONTO);
			}

			let desconto = DESCONTO;

			let obs = $('#obs').val();

			let js = { 
				itens: ITENS,
				cliente: CLIENTE != null ? CLIENTE.id : null,
				valor_total: TOTAL,
				acrescimo: VALORBAIRRO + VALORACRESCIMO,
				troco: troco,
				tipo_pagamento: $('#tipo-pagamento').val(),
				forma_pagamento: '',
				dinheiro_recebido: valorRecebido ? valorRecebido : TOTAL,
				acao: acao,
				nome: $('#nome-cpf').val() ? $('#nome-cpf').val() : "",
				cpf: $('#cpf').val(),
				delivery_id: $('#delivery_id').val(),
				pedido_local: $('#pedidoLocal').val() ? true : false,
				codigo_comanda: COMANDA,
				desconto: desconto ? desconto : 0,
				observacao: obs,
				tipo_pagamento_1: TIPOPAG1,
				tipo_pagamento_2: TIPOPAG2,
				tipo_pagamento_3: TIPOPAG3,
				valor_pagamento_1: VALORPAG1,
				valor_pagamento_2: VALORPAG2,
				valor_pagamento_3: VALORPAG3,
				agendamento_id: $('#agendamento_id').val(),
				bandeira_cartao: $('#bandeira_cartao').val() ? $('#bandeira_cartao').val() : '99',
				cAut_cartao: $('#cAut_cartao').val() ? $('#cAut_cartao').val() : '',
				cnpj_cartao: $('#cnpj_cartao').val() ? $('#cnpj_cartao').val() : '',
				descricao_pag_outros: $('#descricao_pag_outros').val() ? $('#descricao_pag_outros').val() : '',
			}

			console.log(js)
			let token = $('#_token').val();

			if(acao != 'credito'){

				$('#btn_nao_fiscal').addClass('disabled')
				$.ajax
				({
					type: 'POST',
					url: path + 'vendasCaixa/save',
					dataType: 'json',
					data: {
						venda: js,
						_token: token
					},
					success: function(e){
						if(acao == 'fiscal'){
							$('#preloader2').css('display', 'block');
							$('#preloader9').css('display', 'block');
							emitirNFCe(e.id);	
						} else{
							swal({
								title: "Sucesso",
								text: "Deseja imprimir comprovante?",
								icon: "success",
								buttons: ["Não", 'Imprimir'],
								dangerMode: true,
							})
							.then((v) => {
								if (v) {
									window.open(path + 'nfce/imprimirNaoFiscal/'+e.id, '_blank');
									location.href=path+'frenteCaixa';
								} else {
									location.href=path+'frenteCaixa';
								}
							});

						}

					}, error: function(e){
						console.log(e)
						$('#preloader2').css('display', 'none');
						$('#preloader9').css('display', 'none');
						$('#modal-venda').modal('hide')
					}

				});
			}else{
				
				if(CLIENTE == null){
					swal("Alerta", "Informe um cliente para conta crédito", "warning")
				}else{

					if(CLIENTE.limite_venda < parseFloat(CLIENTE.totalEmAberto) + TOTAL){
						swal({
							text: "Valor do limite de conta crédito ultrapassado, confirma a venda?!",
							title: 'Cuidado',
							icon: 'warning',
							buttons: ["Não", "Vender"],
						}).then(sim => {
							if (sim) {
								salvarCredito(js, token)
							}else{
								$('#preloader2').css('display', 'none');
								$('#preloader9').css('display', 'none');
								$('#modal-venda').modal('hide')
							}
						});

					}else{
						salvarCredito(js, token)
					}
				}
				
			}

			$('#kt_select2_3').val('null').change();
		}else{

			swal('Erro', 'CPF/CNPJ Inválido!', 'error')
		}

	}
}

function salvarCredito(js, token){
	$.ajax
	({
		type: 'POST',
		url: path + 'vendas/salvarCrediario',
		dataType: 'json',
		data: {
			venda: js,
			_token: token
		},
		success: function(e){
			$('#modal-venda').modal('hide')

			window.open(path + 'nfce/imprimirNaoFiscalCredito/'+e.id, '_blank');
			// $('#modal-credito').modal('open');
			// $('#evento-conta-credito').html('Venda salva na conta crédito do cliente ' +
			// 	CLIENTE.razao_social)
			swal("Sucesso", "Venda salva na conta crédito do cliente " + CLIENTE.razao_social, "success")
			.then(() => {
				location.href = path + 'frenteCaixa'
			})

		}, error: function(e){
			console.log(e)
			$('#preloader2').css('display', 'none');
			$('#preloader9').css('display', 'none');
			$('#modal-venda').modal('hide')
		}

	});
}

$('#btn-cpf').keypress(function(event) {
	if (event.key === "Enter") {
		finalizarVenda('fiscal')
	}
});

function emitirNFCe(vendaId){
	// $('#modal-venda').modal('close')
	// $('#preloader_'+vendaId).css('display', 'inline-block');

	console.log('emitindo...')
	$('#btn-cpf').addClass('spinner')
	$('#btn-cpf').attr('disabled', true)
	$('#btn_envia_'+vendaId).addClass('spinner')
	$('#btn_envia_'+vendaId).addClass('disabled')
	$('#btn_envia_grid_'+vendaId).addClass('spinner')
	$('#btn_envia_grid_'+vendaId).addClass('disabled')

	let token = $('#_token').val();
	$.ajax
	({
		type: 'POST',
		url: path + 'nfce/gerar',
		dataType: 'json',
		data: {
			vendaId: vendaId,
			_token: token
		},
		success: function(e){

			$('#modal-cpf-nota').modal('hide')
			// $('#preloader_'+vendaId).css('display', 'none');
			$('#btn-cpf').removeClass('spinner')
			$('#btn-cpf').removeAttr('disabled')
			$('#btn_envia_'+vendaId).removeClass('spinner')
			$('#btn_envia_'+vendaId).removeClass('disabled')
			$('#btn_envia_grid_'+vendaId).removeClass('spinner')
			$('#btn_envia_grid_'+vendaId).removeClass('disabled')


			let recibo = e;
			let retorno = recibo.substring(0,4);
			let mensagem = recibo.substring(5,recibo.length);
			if(retorno == 'Erro'){
				try{
					console.log(mensagem)
					let m = JSON.parse(mensagem);
					swal("Algo deu errado!", "[" + m.protNFe.infProt.cStat + "] : " + m.protNFe.infProt.xMotivo, "error")
					.then(() => {
						location.reload()
					})
				}catch{
					console.log(e);
					swal("Algo deu errado!", mensagem, "error").then(() => {
						location.reload()
					})
				}
			}
			
			else if(retorno == 'erro'){
				// $('#modal-alert-erro').modal('show');
				// $('#evento-erro').html("WebService sefaz em manutenção, falha de comunicação SOAP")
				swal("Algo deu errado!", "WebService sefaz em manutenção, falha de comunicação SOAP", "error").then(() => {
					location.reload()
				})


			}
			else if(e == 'Apro'){
				swal("Cuidado", "Esta NF já esta aprovada, não é possível enviar novamente!", "warning").then(() => {
					location.reload()
				})
				// var $toastContent = $('<span>Esta NF já esta aprovada, não é possível enviar novamente!</span>').add($('<button class="btn-flat toast-action">OK</button>'));
				// Materialize.toast($toastContent, 5000);
			}
			else{
				$('#modal-venda').modal('hide')
				swal("Sucesso", "NFCe gerada com sucesso RECIBO: " +recibo, "success")
				.then(() => {
					window.open(path + 'nfce/imprimir/'+vendaId, '_blank');
					location.reload()
				})
				// $('#evento').html("NFCe gerada com sucesso RECIBO: " +recibo)
				
			}
			$('#btn_envia_'+vendaId).removeClass('spinner')
			$('#btn_envia_grid_'+vendaId).removeClass('spinner')
			// $('#preloader2').css('display', 'none');
			// $('#preloader9').css('display', 'none');
			// $('#preloader1').css('display', 'none');
		}, error: function(err){
			console.log(err)
			// $('#preloader_'+vendaId).css('display', 'none');
			$('#btn-cpf').removeAttr('spinner')
			$('#btn-cpf').removeClass('disabled')
			$('#btn_envia_'+vendaId).removeClass('spinner')
			$('#btn_envia_'+vendaId).removeClass('disabled')
			$('#btn_envia_grid_'+vendaId).removeClass('spinner')
			$('#btn_envia_grid_'+vendaId).removeClass('disabled')

			let js = err.responseJSON;
			// deletarVenda(vendaId)
			// swal("Algo errado", js, "error").then(() => {
			// 	location.reload()
			// })
			// var $toastContent = $('<span>Erro ao enviar NFC-e</span>').add($('<button class="btn-flat toast-action">OK</button>'));
			// Materialize.toast($toastContent, 5000);
			// $('#preloader2').css('display', 'none');
			// $('#preloader9').css('display', 'none');

			
			console.log(js)
			if(js.message){
				swal("Algo errado", js.message, "error")

			}else{
				let err = "";
				js.map((v) => {
					err += v + "\n";
				});
				// alert(err);
				swal("Erro", err, "warning")

			}
			$('#btn-cpf').removeClass('spinner')
			

			// $('#preloader1').css('display', 'none');
			
		}
	})

}

function deletarVenda(id){
	$.get(path + 'nfce/deleteVenda/'+id)
	.done((data) => {
		console.log(data)
	})
	.fail((err) => {
		console.log(err)
	})
	
}

function removerVenda(id){
	let senha = $('#pass').val()
	if(senha != ""){

		swal({
			title: 'Cancelamento de venda',
			text: 'Informe a senha!',
			content: {
				element: "input",
				attributes: {
					placeholder: "Digite a senha",
					type: "password",
				},
			},
			button: {
				text: "Cancelar!",
				closeModal: false,
				type: 'error'
			},
			confirmButtonColor: "#DD6B55",
		}).then(v => {
			if(v.length > 0){
				$.get(path+'configNF/verificaSenha', {senha: v})
				.then(
					res => {
						location.href="/frenteCaixa/deleteVenda/"+id;
					},
					err => {
						swal("Erro", "Senha invorreta", "error")
						.then(() => {
							location.reload()
						});
					}
					)
			}else{
				location.reload()
			}
		})
	}else{
		location.href="/frenteCaixa/deleteVenda/"+id;
	}
}

function redireciona(){
	location.href=path+'frenteCaixa';
}

function modalCancelar(id){
	$('#modal').modal('show');
	$('#venda_id').val(id)
}


function cancelar(){

	$('#btn_cancelar_nfce').addClass('spinner');

	let justificativa = $('#justificativa').val();
	let id = $('#venda_id').val();
	let token = $('#_token').val();
	$.ajax
	({
		type: 'POST',
		data: {
			id: id,
			justificativa: justificativa,
			_token: token
		},
		url: path + 'nfce/cancelar',
		dataType: 'json',
		success: function(e){
			$('#btn_cancelar_nfce').removeClass('spinner');
			
			// alert(e.retEvento.infEvento.xMotivo)
			swal("Sucesso", e.retEvento.infEvento.xMotivo, "success")
			.then((v) => {
				location.reload()
			})

		}, error: function(e){
			$('#btn_cancelar_nfce').removeClass('spinner');

			console.log(e)
			let js = e.responseJSON;
			if(e.status == 404){
				// alert(js.mensagem)
				swal("Erro", js.mensagem, "warning")

			}else{
				// alert(js.retEvento.infEvento.xMotivo)
				swal("Erro", js.retEvento.infEvento.xMotivo, "warning")

				// Materialize.toast('Erro de comunicação contate o desenvolvedor', 5000)
				
			}
		}
	});
}

function verItens(){
	$('#modal-itens').modal('open');
	let t = montaTabela();
	$('#body-modal').html(t);

}

function modalWhatsApp(){
	$('#modal-whatsApp').modal('show')
}

function enviarWhatsApp(){
	let celular = $('#celular').val();
	let texto = $('#texto').val();

	let mensagem = texto.split(" ").join("%20");

	let celularEnvia = '55'+celular.replace(' ', '');
	celularEnvia = celularEnvia.replace('-', '');
	let api = 'https://api.whatsapp.com/send?phone='+celularEnvia
	+'&text='+mensagem;
	window.open(api)
}

function apontarComanda(){
	let cod = $('#cod-comanda').val()
	$.get(path+'pedidos/itensParaFrenteCaixa', {cod: cod})
	.done((success) => {
		montarComanda(success, (rs) => {
			if(rs){
				COMANDA = cod;
				$('#modal-comanda').modal('hide')
				swal("", "Comanda setada!!!", "success")


			}
		})
	})
	.fail((err) => {
		if(err.status == 401){
			swal("", "Nada encontrado!!!", "error")
		}
		console.log(err)
	})
}

function montarComanda(itens, call){
	let cont = 0;
	itens.map((v) => {
		let nome = '';
		let valorUnit = 0;
		if(v.sabores.length > 0){

			let cont = 0;
			v.sabores.map((sb) => {
				cont++;
				valorUnit = v.maiorValor;
				nome += sb.produto.produto.nome + 
				(cont == v.sabores.length ? '' : ' | ')
			})


		}else{
			nome = v.produto.nome;
			valorUnit = v.produto.valor_venda
		}

		let item = {
			cont: cont+1,
			id: v.produto_id,
			nome: nome,
			quantidade: v.quantidade,
			valor: parseFloat(valorUnit) + parseFloat(v.valorAdicional),
			pizza: v.maiorValor ? true : false,
			itemPedido: v.item_pedido
		}

		ITENS.push(item)
		TOTAL += parseFloat(item.valor)*(item.quantidade);
	});
	let t = montaTabela();

	atualizaTotal();
	$('#body').html(t);
	call(true)
}

$('#acrescimo').keyup(() => {
	let acrescimo = $('#acrescimo').val();
	if(acrescimo > 0){ 
		$('#desconto').val('0')
	}

	let total = TOTAL+VALORBAIRRO;
	
	if(acrescimo.substring(0, 1) == "%"){

		let perc = acrescimo.substring(1, acrescimo.length);

		VALORACRESCIMO = total * (perc/100);

	}else{
		acrescimo = acrescimo.replace(",", ".")
		VALORACRESCIMO = parseFloat(acrescimo)
	}

	if(acrescimo.length == 0) VALORACRESCIMO = 0;
	atualizaTotal();


})

function consultarNFCe(id){
	$('#btn_consulta_' + id).addClass('spinner')
	$('#btn_consulta_grid_' + id).addClass('spinner')
	$.get(path + 'nfce/consultar/'+id)
	.done((data) => {
		$('#btn_consulta_' + id).removeClass('spinner')
		$('#btn_consulta_grid_' + id).removeClass('spinner')

		console.log(data)
		let js = JSON.parse(data)
		console.log(js)
		swal("Consulta", "[" + js.protNFe.infProt.cStat + "] " + js.protNFe.infProt.xMotivo ,"success");
	})
	.fail((err) => {
		$('#btn_consulta_' + id).removeClass('spinner')
		$('#btn_consulta_grid_' + id).removeClass('spinner')
		console.log(err)
	})
}

$('#btn-plus').click((target) => {
	let quantidade = parseInt($('#quantidade').val());
	$('#quantidade').val(quantidade+1)
})

$('#click-multi').click(() => {
	// if(CLIENTE != null){
	// 	swal("Atenção", "Para pagamento multiplo não é permitido conta crédito", "warning")
	// 	CLIENTE = null;
	// 	$('#conta_credito-btn').attr('disabled', true)
	// 	$('#conta_credito-btn').addClass('disabled')
	// 	$('#kt_select2_3').val('null').change()
	// }
	$('#modal-pag-mult').modal('show')
	$('#v-multi').html(formatReal(TOTAL+VALORACRESCIMO - DESCONTO))

	if(TOTAL <= 0){
		swal("Erro", "Valor da venda deve ser maior que Zero!!", "error")
		.then(() => {
			$('#modal-pag-mult').modal('hide')
		})
	}
	$('#vl_restante').html(formatReal(TOTAL+VALORACRESCIMO - DESCONTO))
	$('#total-multi').html(formatReal(TOTAL+VALORACRESCIMO - DESCONTO))
})

$('#btn-ok-multi').click(() => {

	VALORPAG1 = $('#valor_pagamento_1').val() ? parseFloat($('#valor_pagamento_1').val().replace(",", ".")) : 0;
	VALORPAG2 = $('#valor_pagamento_2').val() ? parseFloat($('#valor_pagamento_2').val().replace(",", ".")) : 0;
	VALORPAG3 = $('#valor_pagamento_3').val() ? parseFloat($('#valor_pagamento_3').val().replace(",", ".")) : 0;

	TIPOPAG1 = $('#tipo_pagamento_1').val()
	TIPOPAG2 = $('#tipo_pagamento_2').val()
	TIPOPAG3 = $('#tipo_pagamento_3').val()

	if((TIPOPAG1 == '06' || TIPOPAG2 == '06' || TIPOPAG3 == '06') && CLIENTE == null){
		swal("Alerta", "Informe um cliente!", "warning")
	}else{
		$('#modal-pag-mult').modal('hide')
		console.log(VALORPAG1, VALORPAG2, VALORPAG3)
		console.log(TIPOPAG1, TIPOPAG2, TIPOPAG3)
		$('#modal-venda').modal('show')
	}
})

$('#valor_pagamento_1').keyup((target) => {
	somaMultiplo();
})
$('#valor_pagamento_2').keyup((target) => {
	somaMultiplo();
})
$('#valor_pagamento_3').keyup((target) => {
	somaMultiplo();
})

function somaMultiplo(){
	let v1 = $('#valor_pagamento_1').val() ? parseFloat($('#valor_pagamento_1').val().replace(",", ".")) : 0;
	let v2 = $('#valor_pagamento_2').val() ? parseFloat($('#valor_pagamento_2').val().replace(",", ".")) : 0;
	let v3 = $('#valor_pagamento_3').val() ? parseFloat($('#valor_pagamento_3').val().replace(",", ".")) : 0;

	let soma = v1 + v2 + v3;
	let somaAux = parseFloat((TOTAL+VALORACRESCIMO - DESCONTO).toFixed(2))
	console.log("somaAux", somaAux)
	$('#vl_restante').html(formatReal((somaAux) - soma))
	if(soma == somaAux){
		$('#btn-ok-multi').removeAttr('disabled')
	}else if(soma > somaAux){
		// swal("Alerta", "Valor de pagamentos ultrapassou o valor da venda", "warning")
		$('#btn-ok-multi').attr('disabled')
	}else{
		$('#btn-ok-multi').attr('disabled')
	}
}

$('#close-multi').click(() => {
	$('#modal-pag-mult').modal('hide')
	VALORPAG1 = 0
	VALORPAG2 = 0
	VALORPAG3 = 0
	TIPOPAG1 = ''
	TIPOPAG2 = ''
	TIPOPAG3 = ''
})
//modal-venda

function montaAtalhos(){
	if(ATALHOS != null){
		if(ATALHOS.finalizar != ""){
			Mousetrap.bind(ATALHOS.finalizar, function(e) {
				e.preventDefault();
				let v = $('#valor_recebido').val();
				let tp = $('#tipo-pagamento').val()
				v = v.replace(",", ".");
				if(ITENS.length > 0 && ((parseFloat(v) >= TOTAL) || tp != '01' )){
					$('#finalizar-venda').trigger('click');
				}else{
					swal("Cuidado", "Venda sem itens, ou valor recebido inferior ao total da venda", "warning")
				}
			});
		}
		if(ATALHOS.reiniciar != ""){
			Mousetrap.bind(ATALHOS.reiniciar, function(e) {
				e.preventDefault();
				location.href = '/frenteCaixa'
			});
		}
		if(ATALHOS.editar_desconto != ""){
			Mousetrap.bind(ATALHOS.editar_desconto, function(e) {
				e.preventDefault();
				setaDesconto()
			});
		}
		if(ATALHOS.editar_acrescimo != ""){
			Mousetrap.bind(ATALHOS.editar_acrescimo, function(e) {
				e.preventDefault();
				setaAcresicmo()
			});
		}
		if(ATALHOS.editar_observacao != ""){
			Mousetrap.bind(ATALHOS.editar_observacao, function(e) {
				e.preventDefault();
				setaObservacao()
			});
		}
		if(ATALHOS.setar_valor_recebido != ""){
			Mousetrap.bind(ATALHOS.setar_valor_recebido, function(e) {
				e.preventDefault();
				
				$('#valor_recebido').val(TOTAL)	
				$('#finalizar-venda').removeAttr('disabled');

			});
		}

		if(ATALHOS.forma_pagamento_dinheiro != ""){
			Mousetrap.bind(ATALHOS.forma_pagamento_dinheiro, function(e) {
				e.preventDefault();
				$('#tipo-pagamento').val('01').change()
			});
		}
		if(ATALHOS.forma_pagamento_debito != ""){
			Mousetrap.bind(ATALHOS.forma_pagamento_debito, function(e) {
				e.preventDefault();
				$('#tipo-pagamento').val('04').change()
			});
		}
		if(ATALHOS.forma_pagamento_credito != ""){
			Mousetrap.bind(ATALHOS.forma_pagamento_credito, function(e) {
				e.preventDefault();
				$('#tipo-pagamento').val('03').change()
			});
		}

		if(ATALHOS.forma_pagamento_pix != ""){
			Mousetrap.bind(ATALHOS.forma_pagamento_pix, function(e) {
				e.preventDefault();
				$('#tipo-pagamento').val('17').change()
			});
		}

		if(ATALHOS.setar_leitor != ""){
			Mousetrap.bind(ATALHOS.setar_leitor, function(e) {
				e.preventDefault();
				$('#codBarras').focus()
			});
		}

		if(ATALHOS.setar_quantidade != ""){
			Mousetrap.bind(ATALHOS.setar_quantidade, function(e) {
				e.preventDefault();
				setaQuantidade()
			});
		}

		if(ATALHOS.balanca_digito_verificador){
			DIGITOBALANCA = ATALHOS.balanca_digito_verificador
		}

		if(ATALHOS != null){
			TIPOUNIDADEBALANCA = ATALHOS.balanca_valor_peso
		}

		if(ATALHOS.finalizar_fiscal != ""){
			Mousetrap.bind(ATALHOS.finalizar_fiscal, function(e) {
				e.preventDefault();
				if($('#modal-venda').hasClass('show')){
					verificaCliente()
				}
			});
		}

		if(ATALHOS.finalizar_nao_fiscal != ""){
			Mousetrap.bind(ATALHOS.finalizar_nao_fiscal, function(e) {
				e.preventDefault();
				if($('#modal-venda').hasClass('show')){
					$('#btn_nao_fiscal').trigger('click')
				}
			});
		}
	}

}

function apontarCodigoDeBarras(){
	let codBarras = $('#cod-barras2').val()
	getProdutoCodBarras(codBarras, (data) => {
		if(data){
			setTimeout(() => {
				addItem();
			}, 400)

		}else{
		}
		$('#cod-barras2').val('')
		$('#modal-cod-barras').modal('hide')
	})
}

$('.pula').keypress(function(e){

	var tecla = (e.keyCode?e.keyCode:e.which);
	// console.log(tecla)
	if(tecla == 13){

		let campo = $('.pula');
		indice = campo.index(this);
		if(campo[indice+1] != null){
			let proximo = campo[indice + 1];
			proximo.focus();
		}
	}
	// e.preventDefault(e);
	// return false;
})

function inutilizar(){

	let justificativa = $('#justificativa_inut').val();
	let nInicio = $('#nInicio').val();
	let nFinal = $('#nFinal').val();


	if(!justificativa){
		swal("Erro", "Informe a justificativa", "error")
		return;
	}

	if(!nInicio || !nFinal){
		swal("Erro", "Informe a Número inicial e final", "error")
		return;
	}
	
	// $('#preloader3').css('display', 'block');
	$('#btn-inut-2').addClass('spinner')

	let token = $('#_token').val();
	$.ajax
	({
		type: 'POST',
		data: {
			justificativa: justificativa,
			nInicio: nInicio,
			nFinal: nFinal,
			_token: token
		},
		url: path + 'nfce/inutilizar',
		dataType: 'json',
		success: function(e){
			console.log(e)
			if(e.infInut.cStat == '102'){
				$('#nInicio').val('');
				$('#justificativa_inut').val('');
				$('#nFinal').val('');
				// alert("cStat:" + e.infInut.cStat + "\n" + e.infInut.xMotivo);
				swal("Sucesso", "["+e.infInut.cStat + "] " + e.infInut.xMotivo, "success")
				.then(() => {
					location.reload()
				})
			}else{
				swal("Erro", "["+e.infInut.cStat + "] " + e.infInut.xMotivo, "error")
				.then(() => {
					location.reload()
				})
			}


			// $('#preloader3').css('display', 'none');
			$('#btn-inut-2').removeClass('spinner')

		}, error: function(e){
			console.log(e)
			swal("Erro", "Erro de comunicação contate o desenvolvedor!", "error")
			$('#preloader1').css('display', 'none');
		}
	});
	
}



