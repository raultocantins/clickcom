var ITENS = [];
var SOMAITENS = 0;

$(function () {

	ITENS = JSON.parse($('#itens_nf').val());
	prepara((res) => {
		let t = montaTabela();
		$('#tbl tbody').html(t)
	});
	console.log(ITENS)
	
});

function prepara(call){
	let temp = [];
	ITENS.map((v) => {
		console.log(v)
		let js = {
			CFOP: v.CFOP[0],
			NCM: v.NCM[0],
			codBarras: v.codBarras[0],
			codigo: v.codigo[0],
			qCom: v.qCom[0],
			uCom: v.uCom[0],
			vUnCom: v.vUnCom[0],
			vFrete: v.vFrete[0],
			xProd: v.xProd[0],
			parcial: 0,
			cst_csosn: v.cst_csosn,
			cst_pis: v.cst_pis,
			cst_cofins: v.cst_cofins,
			cst_ipi: v.cst_ipi,
			perc_icms: v.perc_icms,
			perc_pis: v.perc_pis,
			perc_cofins: v.perc_cofins,
			perc_ipi: v.perc_ipi,
			modBCST: v.modBCST,
			vBCST: v.vBCST,
			pICMSST: v.pICMSST,
			vICMSST: v.vICMSST,
			pMVAST: v.pMVAST,
			pRedBC: v.pRedBC,

		}
		temp.push(js)
	})
	ITENS = temp;
	call(true)
}

function montaTabela(){
	SOMAITENS = 0;
	let t = ""; 

	ITENS.map((v) => {

		t += '<tr class="datatable-row" style="left: 0px;">'
		t += '<td class="datatable-cell">'
		t += '<span style="width: 80px;">'
		t += v.codigo + '</span>'
		t += '</td>'

		t += '<td class="datatable-cell">'
		t += '<span style="width: 200px;" class="cod">'
		t += v.xProd + '</span>'
		t += '</td>'

		t += '<td class="datatable-cell">'
		t += '<span style="width: 80px;">'
		t += v.NCM + '</span>'
		t += '</td>'

		t += '<td class="datatable-cell">'
		t += '<span style="width: 80px;">'
		t += v.CFOP + '</span>'
		t += '</td>'

		t += '<td class="datatable-cell">'
		t += '<span style="width: 80px;">'
		t += v.codBarras + '</span>'
		t += '</td>'

		t += '<td class="datatable-cell">'
		t += '<span style="width: 80px;">'
		t += v.uCom + '</span>'
		t += '</td>'

		t += '<td class="datatable-cell">'
		t += '<span style="width: 80px;">'
		t += formatReal(v.vUnCom) + '</span>'
		t += '</td>'

		t += '<td class="datatable-cell">'
		t += '<span style="width: 80px;">'
		t += v.qCom + '</span>'
		t += '</td>'

		t += '<td class="datatable-cell">'
		t += '<span style="width: 80px;">'
		t += formatReal(v.vUnCom*v.qCom) + '</span>'
		t += '</td>'

		t += '<td class="datatable-cell">'
		t += '<span style="width: 120px;">'
		t += "<a href='#tbl tbody' class='btn btn-danger' onclick='deleteItem(\""+v.codigo+"\")'>"
		t += '<i class="la la-trash"></i></a>'

		t += "<a href='#tbl tbody' class='btn btn-warning' onclick='editItem(\""+v.codigo+"\")'>"
		t += '<i class="la la-edit"></i></a>'

		t += '</span>'
		t += '</td>'


		// t += "<td><a href='#tbl tbody' onclick='deleteItem("+v.codigo+")'>"
		// t += "<i class=' material-icons red-text'>delete</i></a></td>";
		// t += "<td><a href='#tbl tbody' onclick='editItem("+v.codigo+")'>"
		// t += "<i class=' material-icons blue-text'>edit</i></a></td>";

		t+= "</tr>";

		SOMAITENS += v.vUnCom*v.qCom;
	});
	$('#soma-itens').html(formatReal(SOMAITENS))
	return t;
}

function formatReal(v)
{	
	return v.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
}

function deleteItem(item){
	swal("Atenção", "Deseja excluir este item, se confirmar sua NF ficará informal?", "warning")
	.then(() => {
		percorreDelete(item, (res) => {
			console.log(res)
			let vf = parseFloat(res.vFrete)
			let vfTotal = parseFloat($('#vFrete').val())

			let freteV = vfTotal - vf;

			$('#vFrete').val(freteV.toFixed(2))
			$('#valor_frete').val(freteV.toFixed(2))
			let t = montaTabela();
			$('#tbl tbody').html(t);

		})
		return false;
	})
}

function percorreDelete(id, call){
	let temp = [];
	let itemR = null;
	ITENS.map((v) => {
		if(v.codigo != id){
			temp.push(v);
		}else{
			itemR = v
		}

	});
	ITENS = temp;
	call(itemR);
}

function editItem(item){
	console.log(item)
	getItem(item, (res) => {
		console.log(res)
		$("#nomeEdit").val(res.xProd)
		$("#quantidadeEdit").val(res.qCom)
		$("#valorEdit").val(res.vUnCom)
		$("#valorFreteEdit").val(res.vFrete)
		$("#idEdit").val(res.codigo)

		$('#CST_CSOSN').val(res.cst_csosn).change();
		$('#CST_PIS').val(res.cst_pis).change();
		$('#CST_COFINS').val(res.cst_cofins).change();
		$('#CST_IPI').val(res.cst_ipi).change();

		$('#icms').val(res.perc_icms);
		$('#pis').val(res.perc_pis);
		$('#cofins').val(res.perc_cofins);
		$('#ipi').val(res.perc_ipi);

		$('#modal2').modal('show');
	})	
}

$('#salvarEdit').click(() => {
	let id = $('#idEdit').val();
	let nome = $('#nomeEdit').val();
	let quantidade = $('#quantidadeEdit').val();
	let valorFreteEdit = $('#valorFreteEdit').val();
	let valor = $('#valorEdit').val();
	percorreEdit(id, nome, quantidade, valor, valorFreteEdit, (res) => {
		console.log(ITENS)
		let t = montaTabela();
		$('#tbl tbody').html(t)
		$('#modal2').modal('hide');
		swal("Sucesso", "Item editado!!", "success")
	})

})

function percorreEdit(id, nome, quantidade, valor, valorFreteEdit, call){

	let cst_csosn = $('#CST_CSOSN').val();
	let cst_pis = $('#CST_PIS').val();
	let cst_cofins = $('#CST_COFINS').val();
	let cst_ipi = $('#CST_IPI').val();

	let icms = $('#icms').val();
	let pis = $('#pis').val();
	let cofins = $('#cofins').val();
	let ipi = $('#ipi').val();

	valor = valor.replace(",", ".")
	quantidade = quantidade.replace(",", ".")
	valorFreteEdit = valorFreteEdit.replace(",", ".")

	let temp = [];
	console.log(quantidade)
	ITENS.map((v) => {
		if(v.codigo == id){

			v.xProd = nome;
			v.parcial = quantidade != v.qCom ? 1 : 0;
			v.qCom = quantidade;
			v.vUnCom = valor;
			v.vFrete = valorFreteEdit;

			v.cst_csosn = cst_csosn;
			v.cst_pis = cst_pis;
			v.cst_cofins = cst_cofins;
			v.cst_ipi = cst_ipi;

			v.perc_icms = icms;
			v.perc_pis = pis;
			v.perc_cofins = cofins;
			v.perc_ipi = ipi;

		}
		temp.push(v);
	});
	ITENS = temp;

	call(true);
}

function getItem(id, call){
	let obj = null;
	ITENS.map((v) => {
		if(v.codigo == id){
			obj = v;
		}
	})
	call(obj)
}


$('#salvar-devolucao').click(() => {
	$('#preloader2').css('display', 'block');
	let natureza = $('#natureza').val();
	let xmlEntrada = $('#xmlEntrada').val();
	let fornecedorId = $('#idFornecedor').val();
	let nNf = $('#nNf').val();
	let vDesc = $('#vDesc').val();
	let vFrete = $('#valor_frete').val();
	let totalNF = $('#totalNF').val();
	let obs = $('#obs').val();
	let motivo = $('#motivo').val();
	let tipo = $('#tipo').val();
	let transportadora_id = $('#transportadora_id').val();
	let transportadora = JSON.parse($('#transportadora').val());

	let tipoFrete = $('#tipo_frete').val();
	let ufPlaca = $('#uf_placa').val();
	let placa = $('#placa').val();
	let qtd = $('#qtd').val();
	let especie = $('#especie').val();
	let pBruto = $('#peso_bruto').val();
	let pLiquido = $('#peso_liquido').val();
	let vOutros = $('#valor_outros').val();

	let data = {
		natureza: natureza,
		xmlEntrada: xmlEntrada.substring(0, 44),
		fornecedorId: fornecedorId,
		nNf: nNf,
		vDesc: vDesc,
		vFrete: vFrete,
		itens: ITENS,
		devolucao_parcial: SOMAITENS != totalNF,
		valor_integral: totalNF,
		valor_devolvido: SOMAITENS,
		motivo: motivo,
		obs: obs,
		transportadora_id: transportadora_id,
		transportadora: transportadora,
		tipo: tipo,
		tipoFrete: tipoFrete,
		ufPlaca: ufPlaca,
		placa: placa,
		qtd : qtd,
		especie : especie,
		pBruto : pBruto,
		pLiquido : pLiquido,
		vOutros: vOutros
	};

	console.log(data)
	// console.log(path+'devolucao/salvar')
	let token = $('#_token').val();

	$.post(path+'devolucao/salvar', {_token: token, data: data})
	.done((success) => {
		console.log(success)
		$('#preloader2').css('display', 'none');
		sucesso();
	})
	.fail((err) => {
		console.log(err)
		$('#preloader2').css('display', 'none');
		
	})

})

function sucesso(){
	$('#content').css('display', 'none');
	$('#anime').css('display', 'block');
	setTimeout(() => {
		location.href = path+'devolucao';
	}, 4000)
}




