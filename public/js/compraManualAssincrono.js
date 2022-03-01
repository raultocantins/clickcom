

var ITENS = [];
var FATURA = [];
var TOTAL = 0;
var PRODUTOS = [];
var PRODUTO = null;

$(function () {


});

$('.fornecedor').change(() => {
	let fornecedor = $('.fornecedor').val()

	if (fornecedor != '--') {
		getFornecedor(fornecedor, (d) => {
			console.log(d)
			habilitaBtnSalarVenda();
			$('#fornecedor').css('display', 'block');
			$('#razao_social').html(d.razao_social)
			$('#nome_fantasia').html(d.nome_fantasia)
			$('#logradouro').html(d.rua)
			$('#numero').html(d.numero)

			$('#cnpj').html(d.cpf_cnpj)
			$('#ie').html(d.ie_rg)
			$('#fone').html(d.telefone)
			$('#cidade').html(d.nome_cidade)

		})
	}
})

$('#kt_select2_2').change((target) => {

	let prod = $('.produto').val().split('-');
	let codigo = prod[0];
	if(codigo != "null"){
		$('#quantidade').val('1')
		let p = PRODUTOS.filter((x) => { return x.id == codigo })
		p = p[0]
		$('#valor').val(parseFloat(p.valor_compra).toFixed(casas_decimais))
		$('#subtotal').val(parseFloat(p.valor_compra).toFixed(casas_decimais))
	}
})

function getLastPurchase(produto_id, call) {
	$('#preloader-last-purchase').css('display', 'block')
	$.get(path + 'compraManual/ultimaCompra/' + produto_id)
	.done((success) => {
		call(success)
		$('#preloader-last-purchase').css('display', 'none')
	})
	.fail((err) => {
		call(err)
		$('#preloader-last-purchase').css('display', 'none')
	})
}


function getFornecedores(data) {
	$.ajax
	({
		type: 'GET',
		url: path + 'fornecedores/all',
		dataType: 'json',
		success: function (e) {
			data(e)
		}, error: function (e) {
			console.log(e)
		}

	});
}

function getFornecedor(id, data) {
	$.ajax
	({
		type: 'GET',
		url: path + 'fornecedores/find/' + id,
		dataType: 'json',
		success: function (e) {
			data(e)

		}, error: function (e) {
			console.log(e)
		}

	});
}

function getProdutos(data) {
	$.ajax
	({
		type: 'GET',
		url: path + 'produtos/naoComposto',
		dataType: 'json',
		success: function (e) {
			data(e)

		}, error: function (e) {
			console.log(e)
		}

	});
}

function getProduto(id, data) {
	console.log(id)
	$.ajax
	({
		type: 'GET',
		url: path + 'produtos/getProduto/' + id,
		dataType: 'json',
		success: function (e) {
			data(e)

		}, error: function (e) {
			console.log(e)
		}

	});
}

function habilitaBtnSalarVenda() {
	var fornecedor = $('.fornecedor').val().split('-');
	if (ITENS.length > 0 && FATURA.length > 0 && TOTAL > 0 && parseInt(fornecedor[0]) > 0) {
		$('#salvar-venda').removeAttr('disabled', 'false')
	}else{
		$('#salvar-venda').attr('disabled', 'true')
	}
}

$('#valor').on('keyup', () => {
	calcSubtotal()
})

function calcSubtotal() {
	let quantidade = $('#quantidade').val();
	let valor = $('#valor').val();
	let subtotal = parseFloat(valor.replace(',', '.')) * (quantidade.replace(',', '.'));
	let sub = formatReal(subtotal)
	$('#subtotal').val(sub)
}

function maskMoney(v) {
	return v.toFixed(casas_decimais).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

$('#autocomplete-produto').on('keyup', () => {
	$('#last-purchase').css('display', 'none')
})

$('#addProd').click(() => {
	$('#last-purchase').css('display', 'none')
	try{

		let quantidade = $('#quantidade').val();
		let valor = $('#valor').val();

		if (PRODUTO != null && quantidade.length > 0 && parseFloat(quantidade.replace(',', '.')) && valor.length > 0 && parseFloat(valor.replace(',', '.')) > 0) {
			// if (valor.length > 6) valor = valor.replace(".", "");
			valor = valor.replace(",", ".");

			addItemTable(PRODUTO.id, PRODUTO.nome, quantidade, valor);
		} else {
			swal("Erro", "Informe corretamente os campos para continuar!", "error")
		}
	}catch{
		swal("Erro", "Informe corretamente os campos para continuar!!", "error")
	}
	calcTotal()
});

$('#desconto').keyup(() => {
	calcTotal()
	$('.fatura tbody').html("");
	FATURA = []
	limparDadosFatura()
	habilitaBtnSalarVenda()
})

function addItemTable(codigo, nome, quantidade, valor) {
	if (!verificaProdutoIncluso()) {
		limparDadosFatura();
		TOTAL += parseFloat(valor.replace(',', '.')) * parseFloat(quantidade.replace(',', '.'));
		console.log(TOTAL)
		ITENS.push({
			id: (ITENS.length + 1), codigo: codigo, nome: nome,
			quantidade: quantidade, valor: valor
		})
		// apagar linhas tabela
		$('.prod tbody').html("");


		atualizaTotal();
		limparCamposFormProd();
		let t = montaTabela();
		$('.prod tbody').html(t)
	}
}

function verificaProdutoIncluso() {
	if (ITENS.length == 0) return false;
	if ($('#prod tbody tr').length == 0) return false;
	let cod = $('#autocomplete-produto').val().split('-')[0];
	let duplicidade = false;

	ITENS.map((v) => {
		if (v.codigo == cod) {
			duplicidade = true;
		}
	})

	let c;
	if (duplicidade) c = !confirm('Produto já adicionado, deseja incluir novamente?');
	else c = false;
	console.log(c)
	return c;
}

function limparCamposFormProd() {
	$('#autocomplete-produto').val('');
	$('#quantidade').val('0');
	$('#valor').val('0');
}

function limparDadosFatura() {
	$('#fatura tbody').html('')
	$(".data-input").val("");
	$("#valor_parcela").val("");
	$('#add-pag').removeClass("disabled");
	FATURA = [];

}

function atualizaTotal() {
	if(TOTAL < 0){
		$('#total').html(0);
	}else{
		$('#total').html(formatReal(TOTAL));
	}
}

function formatReal(v) {
	return v.toLocaleString('pt-br', { style: 'currency', currency: 'BRL', minimumFractionDigits: casas_decimais });;
}

function montaTabela() {
	let t = "";
	ITENS.map((v) => {
		t += "<tr class='datatable-row' style='left: 0px;'>";
		t += "<td class='datatable-cell'><span class='' style='width: 60px;'>" + v.id + "</span></td>";
		t += "<td class='datatable-cell cod'><span class='codigo' style='width: 60px;'>" + v.codigo + "</span></td>";
		t += "<td class='datatable-cell'><span class='' style='width: 120px;'>" + v.nome + "</span></td>";
		t += "<td class='datatable-cell'><span class='' style='width: 100px;'>" + v.valor + "</span></td>";
		t += "<td class='datatable-cell'><span class='' style='width: 80px;'>" + v.quantidade + "</span></td>";
		t += "<td class='datatable-cell'><span class='' style='width: 80px;'>" + formatReal(v.valor.replace(',', '.') * v.quantidade.replace(',', '.')) + "</span></td>";
		t += "<td class='datatable-cell'><span class='svg-icon svg-icon-danger' style='width: 80px;'><a class='btn btn-danger' href='#prod tbody' onclick='deleteItem(" + v.id + ")'>"
		t += "<i class='la la-trash'></i></a></span></td>";
		t += "</tr>";
	});
	return t
}

function deleteItem(id) {
	let temp = [];
	ITENS.map((v) => {
		if (v.id != id) {
			temp.push(v)
		} else {
			TOTAL -= parseFloat(v.valor.replace(',', '.')) * (v.quantidade.replace(',', '.'));
		}
	});
	ITENS = temp;
	let t = montaTabela(); // para remover
	$('.prod tbody').html(t)
	atualizaTotal();
}

function calcTotal(){
	TOTAL = 0;
	ITENS.map((v) => {
		
		TOTAL += parseFloat(v.valor.replace(',', '.')) * (v.quantidade.replace(',', '.'));

	});

	let desconto = $('#desconto').val().replace(',', '.')
	if(desconto){
		TOTAL -= parseFloat(desconto)
	}
	atualizaTotal()
}

$('#formaPagamento').change(() => {
	calcTotal()
	limparDadosFatura();
	let now = new Date();
	let data = (now.getDate() < 10 ? "0" + now.getDate() : now.getDate()) +
	"/" + ((now.getMonth() + 1) < 10 ? "0" + (now.getMonth() + 1) : (now.getMonth() + 1)) +
	"/" + now.getFullYear();

	var date = new Date(new Date().setDate(new Date().getDate() + 30));
	let data30 = (date.getDate() < 10 ? "0" + date.getDate() : date.getDate()) +
	"/" + ((date.getMonth() + 1) < 10 ? "0" + (date.getMonth() + 1) : (date.getMonth() + 1)) +
	"/" + date.getFullYear();

	$("#qtdParcelas").attr("disabled", true);
	$(".data-input").attr("disabled", true);
	$("#valor_parcela").attr("disabled", true);
	$("#qtdParcelas").val('1');

	if ($('#formaPagamento').val() == 'a_vista') {
		$("#qtdParcelas").val(1)
		$('#valor_parcela').val(formatReal(TOTAL));
		$('.data-input').val(data);
	} else if ($('#formaPagamento').val() == '30_dias') {
		$("#qtdParcelas").val(1)
		$('#valor_parcela').val(formatReal(TOTAL));
		$('.data-input').val(data30);
	} else if ($('#formaPagamento').val() == 'personalizado') {
		$("#qtdParcelas").removeAttr("disabled");
		$(".data-input").removeAttr("disabled");
		$("#valor_parcela").removeAttr("disabled");
		$(".data-input").val("");
		$("#valor_parcela").val(formatReal(TOTAL));
	}
})

$('#qtdParcelas').on('keyup', () => {
	limparDadosFatura();

	if ($("#qtdParcelas").val()) {
		let qtd = $("#qtdParcelas").val();
		console.log(TOTAL)
		$('#valor_parcela').val(formatReal(TOTAL / qtd));
	}
})

$('#add-pag').click(() => {

	if (!verificaValorMaiorQueTotal()) {
		let data = $('.data-input').val();
		let valor = $('#valor_parcela').val();
		let cifrao = valor.substring(0, 2);
		if (cifrao == 'R$') valor = valor.substring(3, valor.length)
			if (data.length > 0 && valor.length > 0 && parseFloat(valor.replace(',', '.')) > 0) {
				addpagamento(data, valor);
			} else {
				swal(
				{
					title: "Erro",
					text: "Informe corretamente os campos para continuar!",
					type: "warning",
				}
				)

			}
		}
	})

function verificaValorMaiorQueTotal(data) {
	let retorno;
	let valorParcela = $('#valor_parcela').val();
	let qtdParcelas = $('#qtdParcelas').val();
	let desconto = $('#desconto').val();

	if (valorParcela <= 0) {

		retorno = true;


		swal(
		{
			title: "Erro",
			text: "Valor deve ser maior que 0",
			type: "warning",
		}
		)
	}

	else if (valorParcela > TOTAL) {

		swal(
		{
			title: "Erro",
			text: "Valor da parcela maior que o total da venda!",
			type: "warning",
		}
		)
		retorno = true;
	}

	else if (qtdParcelas > 1) {
		somaParcelas((v) => {
			console.log(FATURA.length, parseInt(qtdParcelas))

			if (v + parseFloat(valorParcela) > TOTAL) {

				swal(
				{
					title: "Erro",
					text: "Valor ultrapassaou o total!",
					type: "warning",
				}
				)
				retorno = true;
			}
			else if (v + parseFloat(valorParcela) == TOTAL && (FATURA.length + 1) < parseInt(qtdParcelas)) {

				swal(
				{
					title: "Erro",
					text: "Respeite a quantidade de parcelas pré definido!",
					type: "warning",
				}
				)
				retorno = true;

			}
			else if (v + parseFloat(valorParcela) < TOTAL && (FATURA.length + 1) == parseInt(qtdParcelas)) {

				swal(
				{
					title: "Erro",
					text: "Somátoria incorreta!",
					type: "warning",
				}
				)
				let dif = TOTAL - v;
				$('#valor_parcela').val(formatReal(dif))
				retorno = true;

			}
			else {
				retorno = false;

			}
		})
	}
	else {
		retorno = false;
	}

	return retorno;
}

function somaParcelas(call) {
	let soma = 0;
	FATURA.map((v) => {
		console.log(v.valor)
		// if(v.valor.length > 6){
		// 	v = v.valor.replace('.','');
		// 	v = v.replace(',','.');
		// 	soma += parseFloat(v);

		// }else{
		// 	soma += parseFloat(v.valor.replace(',','.'));
		// }
		soma += parseFloat(v.valor.replace(',', '.'));

	})
	call(soma)
}

function addpagamento(data, valor) {
	let result = verificaProdutoIncluso();
	if (!result) {
		FATURA.push({ data: data, valor: valor, numero: (FATURA.length + 1) })

		$('.fatura tbody').html(""); // apagar linhas da tabela
		let t = "";
		FATURA.map((v) => {
			t += "<tr class='datatable-row' style='left: 0px;'>";
			t += "<td class='datatable-cell'><span class='numero' style='width: 160px;'>" + v.numero + "</span></td>";
			t += "<td class='datatable-cell'><span class='' style='width: 160px;'>" + v.data + "</span></td>";
			t += "<td class='datatable-cell'><span class='' style='width: 160px;'>" + v.valor.replace(',', '.') + "</span></td>";
			t += "<td class='datatable-cell'><span class='' style='width: 160px;'><button class='btn btn-danger' onclick='removeParcela("+v.numero+")'>"
			+"<i class='la la-trash'></i></button></span></td>";
			t += "</tr>";
		});

		$('.fatura tbody').html(t)
		verificaValor();
	}
	habilitaBtnSalarVenda();
}

function removeParcela(numero){
	let temp = [];
	FATURA.map((v) => {
		if (v.numero != numero) {
			temp.push(v)
		} 
	});
	FATURA = temp;
	$('.fatura tbody').html(""); // apagar linhas da tabela
	let t = "";
	FATURA.map((v) => {
		t += "<tr class='datatable-row' style='left: 0px;'>";
		t += "<td class='datatable-cell'><span class='numero' style='width: 160px;'>" + v.numero + "</span></td>";
		t += "<td class='datatable-cell'><span class='' style='width: 160px;'>" + v.data + "</span></td>";
		t += "<td class='datatable-cell'><span class='' style='width: 160px;'>" + v.valor.replace(',', '.') + "</span></td>";
		t += "<td class='datatable-cell'><span class='' style='width: 160px;'><a class='btn btn-danger' onclick='removeParcela("+v.numero+")'>"
		+"<i class='la la-trash'></i></a></span></td>";
		t += "</tr>";
	});

	$('.fatura tbody').html(t)
	verificaValor();
}

function verificaValor() {
	let soma = 0;
	FATURA.map((v) => {
		soma += parseFloat(v.valor.replace(',', '.'));
	})
	if (soma >= TOTAL) {
		$('#add-pag').addClass("disabled");
	}
}

var salvando = false
function salvarCompra() {

	if(salvando == false){
		salvando =  true
		$('#preloader2').css('display', 'block');

		var fornecedor = $('.fornecedor').val();
		if (fornecedor == '--') {
			swal(
			{
				title: "Erro",
				text: "Selecione um fornecedor para continuar!",
				type: "warning",
			}
			)
		} else {
			let js = {
				fornecedor: fornecedor,
				formaPagamento: $('#formaPagamento').val(),
				itens: ITENS,
				fatura: FATURA,
				total: TOTAL,
				desconto: $('#desconto').val(),
				observacao: $('#obs').val()
			}

			let token = $('#_token').val();
			console.log(js)
			$.ajax
			({
				type: 'POST',
				data: {
					compra: js,
					_token: token
				},
				url: path + 'compraManual/salvar',
				dataType: 'json',
				success: function (e) {
					$('#preloader2').css('display', 'none');
					sucesso(e)

				}, error: function (e) {
					console.log(e)
					$('#preloader2').css('display', 'none');
				}
			});
		}
	}
	salvando = false
}

function sucesso() {
	$('#content').css('display', 'none');
	$('#anime').css('display', 'block');
	setTimeout(() => {
		location.href = path + 'compras';
	}, 4000)
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
	$.get(path + 'produtos/autocompleteProduto', {id: id, lista_id: 0})
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

		$('#valor').val(parseFloat(PRODUTO.valor_compra).toFixed(casas_decimais))
		$('#quantidade').val(1)
		$('#subtotal').val(parseFloat(PRODUTO.valor_compra).toFixed(casas_decimais))
		$('#produto-search').val(p)
	})
	.fail((err) => {
		console.log(err)
		swal("Erro", "Erro ao encontrar produto", "error")
	})
	$('.search-prod').css('display', 'none')
}

