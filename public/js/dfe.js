var array = [];
var codigo = "";
var nome = "";
var ncm = "";
var cfop = "";
var unidade = "";
var valor = "";
var valorCompra = "";
var quantidade = "";
var codBarras = "";
var nNf = 0;
var semRegitro;

$(function () {

	let uri = window.location.pathname;
	if(uri.split('/')[2] == 'novaConsulta'){
		filtrar();
	}else {
		try{
			array = JSON.parse($('#docs').val());
		}catch{
			array = [];
		}
	}
});

$('#tipo_evento').change(() => {
	let tipo = $('#tipo_evento').val();
	if(tipo == 3 || tipo == 4){
		$('#div-just').css('display', 'block')
	}else{
		$('#div-just').css('display', 'none')
	}
})

function filtrar(){
	$.get(path + 'dfe/getDocumentosNovos')
	.done(value => {
		console.log(value)
		$('#preloader1').css('display', 'none')
		$('#aguarde').css('display', 'none')

		if(value.length > 0){
			montaTabela(value, (html) => {
				console.log(html)
				$('table tbody').html(html)
				$('#table').css('display', 'block')
			})
			swal("Sucesso", "Foram encontrados " + value.length + " novos registros!", "success")
		}else{
			swal("Sucesso", "A requisição obteve sucesso, porém sem novos registros!!", "success")
			$('#sem-resultado').css('display', 'block')

		}

	})
	.fail(err => {
		console.log(err)
		$('#preloader1').css('display', 'none')
		$('#aguarde').css('display', 'none')
		try{
			swal("Erro", err.responseJSON.message, "warning")
		}catch{
			swal("Erro", "Erro inesperado!!", "warning")
		}
	})
}

function montaTabela(array, call){
	let html = '';
	array.map(v => {
		console.log(v)
		html += '<tr class="datatable-row">';
		html += '<td class="datatable-cell"><span class="codigo" style="width: 300px;" id="id">'
		+ v.nome[0] + '</span></td>'
		html += '<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">'
		+ v.documento[0] + '</span></td>'
		html += '<td class="datatable-cell"><span class="codigo" style="width: 100px;" id="id">'
		+ v.valor[0] + '</span></td>'
		html += '<td class="datatable-cell"><span class="codigo" style="width: 200px;" id="id">'
		+ v.chave[0] + '</span></td>'
		html += '</tr>';
	})

	call(html)
}

function setarEvento(chave){
	console.log(array)
	array.map((element) => {
		if(element.chave == chave){
			console.log(element)
			$('#nome').val(element.nome)
			$('#cnpj').val(element.documento)
			$('#valor').val(element.valor)
			$('#data_emissao').val(element.data_emissao)
			$('#num_prot').val(element.num_prot)
			$('#chave').val(element.chave)
		}

	})

}

function _construct(codigo, nome, codBarras, ncm, cfop, unidade, valor, quantidade, valorCompra, nNf){
	this.codigo = codigo;
	this.nome = nome;
	this.ncm = ncm;
	this.cfop = cfop;
	this.unidade = unidade;
	this.valor = valor;
	this.valorCompra = valorCompra;
	this.quantidade = quantidade;
	this.nNf = nNf;
	this.codBarras = codBarras.substring(0, 13);
}

function cadProd(codigo, nome, codBarras, ncm, cfop, unidade, valor, quantidade, valorCompra, nNf){
	_construct(codigo, nome, codBarras, ncm, cfop, unidade, valor, quantidade, valorCompra, nNf);

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

		// CST_CSOSN

		$('#cfop').val(cfop);
		console.log(unidade)

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
		$('#codBarras').val(codBarras);
		$('#conv_estoque').val('1');
		// $('#valor_venda').val('0');
		$("#quantidade").trigger("click");

		$('#modal1').modal('show');
	})

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

$('#salvar').click(() => {
	$('#preloader').css('display', 'block');
	$("#th_"+this.codigo).removeClass("red-text");
	$("#th_"+this.codigo).html($('#nome').val());
	let valorVenda = $('#valor_venda').val();

	if(valorVenda <= 0){
		swal("Erro", "Informe um valor de venda", "warning")
	}else{
		let valorCompra = $('#valor').val();
		let unidadeVenda = $('#unidade_venda').val();
		let conversaoEstoque =$('#conv_estoque').val();
		let categoria_id =$('#categoria_id').val();
		let cor = $('#cor').val();

		let CST_CSOSN =$('#CST_CSOSN').val();
		let CST_PIS =$('#CST_PIS').val();
		let CST_COFINS =$('#CST_COFINS').val();
		let CST_IPI =$('#CST_IPI').val();
		let cfop = $('#cfop').val();
		let percentual_lucro = $('#percentual_lucro').val();
		let codBarras = $('#codBarras').val();


		let prod = {
			valorVenda: valorVenda,
			valorCompra: valorCompra,
			percentual_lucro: percentual_lucro,
			unidadeVenda: unidadeVenda,
			conversao_unitaria: conversaoEstoque,
			categoria_id: categoria_id,
			cor: cor,
			nome: $('#nome').val(),
			ncm: this.ncm,
			cfop: cfop,
			unidadeCompra: this.unidade,
			valor: this.valor,
			quantidade: this.quantidade,
			codBarras: codBarras,
			numero_nfe: this.nNf,
			CST_CSOSN: CST_CSOSN,
			CST_PIS: CST_PIS,
			CST_COFINS: CST_COFINS,
			CST_IPI: CST_IPI,
			referencia: this.codigo,

		}

		console.log(prod)

		let token = $('#_token').val();

		$.ajax
		({
			type: 'POST',
			data: {
				produto: prod,
				_token: token
			},
			url: path + 'produtos/salvarProdutoDaNotaComEstoque',
			dataType: 'json',
			success: function(e){
				$("#th_prod_id_"+codigo).html(e.id);
				$("#th_acao1_"+codigo).css('display', 'none');
				$("#th_acao2_"+codigo).css('display', 'block');
				$("#th_estoque_"+codigo).addClass('disabled');

				$('#preloader').css('display', 'none');
				$('#modal1').modal('hide');

				swal("Sucesso", "Produto Salvo, e inserido o estoque quantidade: " + prod.quantidade, "success")
				.then(sim => {
					location.reload();

				});

			}, error: function(e){
				console.log(e)
				$('#preloader').css('display', 'none');
			}
		});
	}
})

function salvarEstoque(id, valor, quantidade, numero_nfe){
	swal("Alerta", "Deseja atribuir estoque a este produto?", "warning")
	.then(sim => {
		if(sim){
			let token = $('#_token').val();
			$.ajax
			({
				type: 'POST',
				data: {
					produto: id,
					quantidade: quantidade,
					valor: valor,
					numero_nfe: numero_nfe,
					_token: token
				},
				url: path + 'produtos/setEstoque',
				dataType: 'json',
				success: function(e){
					$("#th_estoque_"+id).addClass('disabled');

					swal("Sucesso", "Inserido o estoque quantidade: " + quantidade, "success")
					.then(() => {
						location.reload()
					})


				}, error: function(e){
					console.log(e)
					$('#preloader').css('display', 'none');
				}
			});
		}
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

