var codigo = "";
var nome = "";
var ncm = "";
var cfop = "";
var unidade = "";
var valor = "";
var quantidade = "";
var codBarras = "";
var cfopEntrda = "";
var TOTAL = 0;
var fatura = [];
var semRegitro;

$(function () {

	fatura = JSON.parse($('#fatura').val());
	console.log(fatura)
	TOTAL = parseFloat($('#total').val())
	semRegitro = $('#prodSemRegistro').val();
	if(semRegitro == 0){
		$('#salvarNF').removeAttr("disabled");
		$('.sem-registro').css('display', 'none');
	}
	verificaProdutoSemRegistro();

	montaHtmlFatura((html) => {
		$('#fatura-html').html(html)
	})
});

function linkProduto(){
	$('#kt_select2_1').val('null').change()
	$('#valor_venda2').val('')
	$('#valor_compra2').val('')
	$('#modal1').modal('hide');
	$('#modal-link').modal('show');
	$('#estoque').val(this.quantidade)
}

$('#kt_select2_1').change(() => {
	let produto = $('#kt_select2_1').val()
	if(produto != 'null'){

		produto = JSON.parse(produto);
		console.log(produto)
		$('#valor_venda2').val(parseFloat(produto.valor_venda).toFixed(casas_decimais))
		$('#valor_compra2').val(parseFloat(produto.valor_compra).toFixed(casas_decimais))
	}else{
		$('#valor_venda2').val('')
	}
})

$('#salvarLink').click(() => {
	let id = this.codigo;
	let prod = $('#kt_select2_1').val()

	let estoque = $('#estoque').val()
	let valor = $('#valor_venda2').val()
	let valorCompra = $('#valor_compra2').val()
	if(prod != 'null'){
		let produto = $('#kt_select2_1').val()
		produto = JSON.parse(produto);

		$('#n_'+id).html(produto.nome)
		$('#n_'+id).removeClass('text-danger')
		$('#th_prod_id_'+id).html(produto.id)
		$('#th_prod_valor_venda_'+id).html(valor)
		$('#th_prod_valor_compra_'+id).html(valorCompra)
		$('#qtd_aux_'+id).html(estoque)

		semRegitro--;
		verificaProdutoSemRegistro();
		$('#modal-link').modal('hide');

	}else{
		swal("Erro", "Selecione o produto", "error");
	}
})

$('#salvarEdit').click(() => {
	let id = $('#idEdit').val();
	$('#n_'+id).html($('#nomeEdit').val());
	$('#th_prod_conv_unit_'+id).html($('#conv_estoqueEdit').val());


	$('#th_prod_valor_venda_'+id).html($('#valorVendaEdit').val());
	$('#th_prod_valor_compra_'+id).html($('#valorCompraEdit').val());

	$('#modal2').modal('hide');
})

function verificaProdutoSemRegistro(){
	if(semRegitro == 0){
		$('#salvarNF').removeAttr("disabled");
		$('.sem-registro').css('display', 'none');
	}else{
		$('.prodSemRegistro').html(semRegitro);
	}
}

function _construct(codigo, nome, codBarras, ncm, cfop, unidade, valor, quantidade, cfop_entrada){

	this.codigo = codigo;
	this.nome = nome;
	this.ncm = ncm;
	this.cfop = cfop;
	this.unidade = unidade;
	this.valor = valor;
	this.quantidade = quantidade;
	this.codBarras = codBarras;
	this.cfopEntrda = cfop_entrada;
}

function cadProd(codigo, nome, codBarras, ncm, cfop, unidade, valor, quantidade, cfop_entrada){

	_construct(codigo, nome, codBarras, ncm, cfop, unidade, valor, quantidade, cfop_entrada);
	$('#nome').val(nome);
	$("#nome").focus();

	getUnidadeMedida((data) => {
		let achouUnidade = false;
		data.map((v) => {
			if(v == unidade){
				achouUnidade = true;
			}
		})

		// if(!achouUnidade){

		// 	swal('', "Unidade de compra deste produto não corresponde a nenhuma pré-determinada\n"+
		// 		"Unidade: " + unidade, 'warning')
		// 	.then(s => {


		// 		if(unidade == 'M3C'){
		// 			unidade = 'M3';
		// 			swal('', 'M3C alterado para ' + unidade, 'warning')

		// 		}
		// 		else if(unidade == 'M2C'){
		// 			unidade = 'M2';
		// 			swal('', 'M2C alterado para ' + unidade, 'warning')

		// 		}
		// 		else if(unidade == 'MC'){
		// 			unidade = 'M';
		// 			swal('', 'MC alterado para ' + unidade, 'warning')
		// 		}
		// 		else if(unidade == 'UN'){
		// 			unidade = 'UNID';
		// 			swal('', 'UN alterado para ' + unidade, 'warning')

		// 		}else{
		// 			unidade = 'UNID';
		// 			swal('', 'UN alterado para ' + unidade, 'warning')

		// 		}
		// 	})

		// }

		$('#ncm').val(ncm);
		$("#ncm").trigger("click");

		let dig2Cfop = cfop.substring(1,2);

		if(dig2Cfop == 4){
			cfop = '5405';
		}

		if(cfop == 5405){
			$('#CST_CSOSN').val(500).change()
		}

		$('#cfop').val(cfop);


		$('#un_compra').val(unidade);
		$('#unidade_venda option[value="'+unidade+'"]').prop("selected", true);

		$('#valor').val(valor);
		let percentualLucro = $('#percentual_lucro').val()
		percentualLucro = percentualLucro.replace(",", ".");
		// percentualLucro = parseFloat(percentualLucro)

		let valorVenda = parseFloat(valor) + (parseFloat(valor) * (percentualLucro/100));
		valorVenda = formatReal(valorVenda);
		valorVenda = valorVenda.replace('.', '')
		valorVenda = valorVenda.substring(3, valorVenda.length)

		$('#valor_venda').val(valorVenda)

		$('#quantidade').val(quantidade);
		$('#conv_estoque').val('1');

		$('#cfop_entrada').val(cfop_entrada);
		$('#codBarras').val(codBarras);
		$("#quantidade").trigger("click");

		$('#modal1').modal('toggle');

	})

}

function deleteProd(item){
	if (confirm('Deseja excluir este item, se confirmar sua NF ficará informal?')) { 
		var tr = $(item).closest('tr');	
		console.log(tr)
		tr.fadeOut(500, function() {	      
			tr.remove();  
			verificaTabelaVazia();	
			verificaProdutoSemRegistro();
		});	

		return false;
	}
}

function editProd(id){

	let produtoId = $('#th_prod_id_'+id).html();
	$('#idEdit').val(id)
	$.ajax
	({
		type: 'GET',
		url: path + 'produtos/getProduto/'+produtoId,
		dataType: 'json',
		success: function(e){
			console.log(e)
			$("#nomeEdit").val(e.nome)
			$("#conv_estoqueEdit").val(e.conversao_unitaria)
			$("#valorVendaEdit").val(e.valor_venda)
			$("#valorCompraEdit").val(e.valor_compra)
			$('#modal2').modal('show');
		}, error: function(e){
			console.log(e);
		}
	});
}

function verificaTabelaVazia(){
	if($('table tbody tr').length == 0){
		$('#salvarNF').addClass("disabled");
	}
}


var saveProduto = false;
$('#salvar').click(() => {

	if(saveProduto == false){
		saveProduto = true;
		$('#preloader').css('display', 'block');
		$("#th_"+this.codigo).removeClass("red-text");
		$("#th_"+this.codigo).html($('#nome').val());
		let valorVenda = $('#valor_venda').val();
		let valor_compra = $('#valor_compra').val();
		let unidadeVenda = $('#unidade_venda').val();
		let conversaoEstoque = $('#conv_estoque').val();
		let categoria_id = $('#categoria_id').val();
		let cor = $('#cor').val();
		let cfop = $('#cfop').val();
		let referencia = $('#referencia').val();
		let percentual_lucro = $('#percentual_lucro').val();

		let CST_CSOSN = $('#CST_CSOSN').val();
		let CST_PIS = $('#CST_PIS').val();
		let CST_COFINS =$('#CST_COFINS').val();
		let CST_IPI = $('#CST_IPI').val();
		let perc_icms = $('#perc_icms').val();
		let perc_pis = $('#perc_pis').val();
		let perc_cofins = $('#perc_cofins').val();
		let perc_ipi = $('#perc_ipi').val();
		let codBarras = $('#codBarras').val();

		let prod = {
			valorVenda: valorVenda,
			unidadeVenda: unidadeVenda,
			conversao_unitaria: conversaoEstoque,
			categoria_id: categoria_id,
			cor: cor,
			valorCompra: valor_compra,
			nome: $('#nome').val(),
			ncm: this.ncm,
			cfop: cfop,
			percentual_lucro: percentual_lucro,
			referencia: referencia,
			referencia: this.codigo,
			unidadeCompra: this.unidade,
			valor: this.valor,
			quantidade: this.quantidade,
			codBarras: codBarras,
			CST_CSOSN: CST_CSOSN,
			CST_PIS: CST_PIS,
			CST_COFINS: CST_COFINS,
			CST_IPI: CST_IPI,
			valorCompra: this.valor,
			perc_icms: perc_icms,
			perc_pis: perc_pis,
			perc_cofins: perc_cofins,
			perc_ipi: perc_cofins,
		}
		console.log(prod)
		semRegitro--;
		verificaProdutoSemRegistro();

		let token = $('#_token').val();

		$.ajax
		({
			type: 'POST',
			data: {
				produto: prod,
				_token: token
			},
			url: path + 'produtos/salvarProdutoDaNota',
			dataType: 'json',
			success: function(e){
				let cfop_entrada = $('#cfop_entrada').val()
				$("#th_prod_id_"+codigo).html(e.id);
				$("#cfop_entrada_"+codigo).html(cfop_entrada);
				$("#th_acao1_"+codigo).css('display', 'none');
				$("#th_acao2_"+codigo).css('display', 'block');
				$("#n_"+codigo).removeClass('text-danger');
				$('#preloader').css('display', 'none');
				$('#modal1').modal('hide');

				swal('Sucesso', 'Item salvo', 'success')
				saveProduto = false;

			}, error: function(e){
				console.log(e)
				$('#preloader').css('display', 'none');
				saveProduto = false;
			}
		});
	}
})


var salvando = false;
$('#salvarNF').click(() => {
	if(salvando == false){
		salvando = true;
		$('#preloader2').css('display', 'block');

		salvarNF((data) => {
			if(data.id){
				salvarItens(data.id, (v) => { //data.id codigo da compra

					if(v){
						salvarFatura(data.id, (f) => {
							$('#modal1').modal('hide');
							$('#preloader2').css('display', 'none');
							sucesso();

						})
					}
				})
			}
		})
	}
})

function salvarFatura(compra_id, call){
	
	retorno = [];
	let token = $('#_token').val();
	let cont = 0; 

	if(fatura.length > 0){
		fatura.map((item) => {
			cont++;
			item.numero = item.numero;
			item.referencia = "Parcela "+cont+", da NF " + $('#nNf').val();
			item.compra_id = compra_id;

			console.log(item)
			$.ajax
			({
				type: 'POST',
				data: {
					parcela: item,
					_token: token
				},
				url: path + 'contasPagar/salvarParcela',
				dataType: 'json',
				success: function(e){
					console.log(e)
					call(e)

				}, error: function(e){
					console.log(e)
					$('#preloader2').css('display', 'none');
				}

			});
		})
	}else{
		sucesso();
		$('#preloader2').css('display', 'none');
	}
}


function sucesso(){
	console.log("sucesso")
	$('#content').css('display', 'none');
	$('#anime').css('display', 'block');
	setTimeout(() => {
		location.href = path+'compras';
	}, 4000)
}

function salvarNF(call){
	
	let js = {
		fornecedor_id: $('#idFornecedor').val(),
		nNf: $('#nNf').val(),
		valor_nf: $('#valorDaNF').html(),
		observacao: '*',
		desconto: $('#vDesc').val(),
		xml_path: $('#pathXml').val(),
		chave: $('#chave').val(),
	}
	let token = $('#_token').val();

	$.ajax
	({
		type: 'POST',
		data: {
			nf: js,
			_token: token
		},
		url: path + 'compraFiscal/salvarNfFiscal',
		dataType: 'json',
		success: function(e){
			call(e)

		}, error: function(e){
			console.log(e)
			$('#preloader2').css('display', 'none');
		}

	});
}

function getUnidadeMedida(call){
	$.ajax
	({
		type: 'GET',
		url: path + 'produtos/getUnidadesMedida',
		dataType: 'json',
		success: function(e){
			console.log(e)
			call(e)

		}, error: function(e){
			console.log(e)
		}

	});
}

function salvarItens(id, call){

	let token = $('#_token').val();
	$('table tbody tr').each(function(){
		let js = {
			cod_barras : $(this).find('.codBarras').html(),
			nome : $(this).find('.nome').html(),
			produto_id : parseInt($(this).find('.cod').html()),
			compra_id : id,
			unidade : $(this).find('.unidade').html(),
			quantidade : $(this).find('.quantidade').html(),
			valor : $(this).find('.valor').html(),
			valor_venda : $(this).find('.valor_venda').html(),
			valor_compra : $(this).find('.valor_compra').html(),
			cfop_entrada : $(this).find('#cfop_entrada_input').val(),
			conversao_unitaria : $(this).find('.conv_estoque').html(),
			said : $(this).find('#codigo_siad_input').val(),

		}

		console.log(js)
		$.ajax
		({
			type: 'POST',
			data: {
				produto: js,
				_token: token
			},
			url: path + 'compraFiscal/salvarItem',
			dataType: 'json',
			success: function(e){

			}, error: function(e){
				console.log(e)
				$('#preloader2').css('display', 'none');
			}

		});
	});
	call(true)
}

$('#add-pag').click(() => {
	let vencimento = $('#kt_datepicker_3').val();
	let valor_parcela = $('#valor_parcela').val();
	if(vencimento.length<10 || valor_parcela < 0){
		swal("Erro", "Informe o valor da parcela e vencimento", "error")
	}else{
		somaFatura((res) => {
			valor_parcela = valor_parcela.replace(",", ".")
			let soma = res + parseFloat(valor_parcela)

			if(soma <= TOTAL){
				let js = {
					numero: fatura.length+1,
					vencimento: vencimento,
					valor_parcela: parseFloat(valor_parcela),
					rand: Math.floor(Math.random() * 10000)
				}
				console.log(js)
				fatura.push(js)
				montaHtmlFatura((html) => {
					$('#fatura-html').html(html)
				})
			}else{
				swal({
					title: "Alerta", 
					text: "Valor total de parcelas ultrapassado, deseja continuar?", 
					icon : "warning",
					buttons: [
					'Cancelar',
					'Confirmar'
					],
				})
				.then(
					(Confirmar) => {
						let js = {
							numero: fatura.length+1,
							vencimento: vencimento,
							valor_parcela: parseFloat(valor_parcela),
							rand: Math.floor(Math.random() * 10000)
						}
						console.log(js)
						fatura.push(js)
						montaHtmlFatura((html) => {
							$('#fatura-html').html(html)
						})
					},
					(Cancelar) => {}
					)
			}
		})
	}
})

function somaFatura(call){
	let soma = 0;
	fatura.map((rs) => {
		console.log(rs)
		let v = 0;
		try{
			v = parseFloat(rs.valor_parcela.replace(",", "."))
		}catch{
			v = parseFloat(rs.valor_parcela)
		}
		soma += v
	})
	call(soma)
}

function montaHtmlFatura(call){
	let html = '';
	fatura.map((f) => {
		html += '<div class="col-sm-12 col-lg-6 col-md-6 col-xl-4">'
		html += '<div class="card card-custom gutter-b example example-compact">'
		html += '<div class="card-header">'
		html += '<div class="card-title">'
		html += '<h3 style="width: 230px; font-size: 20px; height: 10px;" class="card-title"> R$ '
		html += maskMoney(f.valor_parcela)
		html += '</h3> <a onclick="deleteParcela('+f.rand+')"><i class="la la-trash text-danger"></i></a></div>'
		html += '<div class="card-body">'
		html += '<div class="kt-widget__info">'
		html += '<span class="kt-widget__label">Número:</span>'
		html += '<a target="_blank" class="kt-widget__data text-success">'
		html += f.numero
		html += '</a></div>'
		html += '<div class="kt-widget__info">'
		html += '<span class="kt-widget__label">Vencimento:</span>'
		html += '<a target="_blank" class="kt-widget__data text-success">'
		html += f.vencimento
		html += '</a></div>'
		html += '</div></div></div></div>'
	});
	call(html)
}

function deleteParcela(rand){
	let arr = [];
	fatura.map((rs) => {
		if(rs.rand != rand){
			arr.push(rs)
		}
	})
	fatura = arr;
	montaHtmlFatura((html) => {
		$('#fatura-html').html(html)
	})

}

function maskMoney(v){
	try{
		v = v.replace(",", ".");
		v = parseFloat(v);
	}catch{

	}
	return v.toFixed(2);
}

$('#percentual_lucro').keyup(() => {

	let valorCompra = parseFloat($('#valor').val().replace(',', '.'));
	let percentualLucro = parseFloat($('#percentual_lucro').val().replace(',', '.'));
	console.log(percentualLucro)
	if(valorCompra > 0 && percentualLucro > 0){
		let valorVenda = valorCompra + (valorCompra * (percentualLucro/100));
		valorVenda = formatReal(valorVenda);
		valorVenda = valorVenda.replace('.', '')
		valorVenda = valorVenda.substring(3, valorVenda.length)

		$('#valor_venda').val(valorVenda)
	}else{
		$('#valor_venda').val('0')
	}
})

$('#valor_venda').keyup(() => {
	let valorCompra = parseFloat($('#valor').val().replace(',', '.'));
	let valorVenda = parseFloat($('#valor_venda').val().replace(',', '.'));

	if(valorCompra > 0 && valorVenda > 0){
		let dif = (valorVenda - valorCompra)/valorCompra*100;

		$('#percentual_lucro').val(dif)
	}else{
		$('#percentual_lucro').val('0')
	}
})

function formatReal(v){
  return v.toLocaleString('pt-br', {style: 'currency', currency: 'BRL', minimumFractionDigits: casas_decimais});
}

