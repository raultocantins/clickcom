
let adicionais = [];
var maximo = 1;
$(function () {
	maximo = $('#maximo_adicionais_pizza').val();
})

function selet_add(adicional){
	console.log(adicional)
	controlaMaximo(adicional.id, (cl)=> {
		if(cl == false){
			verificaAdicionado(adicional.id, (res) => {

				if(res == true){
					$('#adicional_'+adicional.id).css('background', '#fff')
					removeElemento(adicional.id)
				}else{
					$('#adicional_'+adicional.id).css('background', '#81c784')
					adicionais.push({
						'id': adicional.id,
						'nome': adicional.nome,
						'valor': adicional.valor
					})
				}

				somaTotal();
			})
		}
	})

}

function controlaMaximo(id, call){
	let ret = false;
	console.log(adicionais.length)
	if(adicionais.length >= maximo){
		ret = true
	}

	adicionais.map((rs) => {
		if(rs.id == id)
			ret = false;
	})

	if(ret == true){
		swal("Atenção!", 'Maximo de '+maximo+' adicionais!!', "warning")
	}
	
	call(ret)
}

function removeElemento(elem_id){
	let temp = [];
	adicionais.map((v) => {
		if(v.id != elem_id){
			temp.push(v)
		}
	});

	adicionais = temp;
}

function verificaAdicionado(elem_id, call){
	let b = false;
	adicionais.map((v) => {
		if(v.id == elem_id){
			b = true;
		}
	});
	call(b);
}

function somaTotal(){
	let quantidade = $('#quantidade').val() ? $('#quantidade').val() : 1
	let valorProduto = $('#valor_produto').html();
	valorProduto = parseFloat(valorProduto)
	adicionais.map((v) => {
		valorProduto += parseFloat(v.valor);
	})
	valorProduto = valorProduto * quantidade;
	$('#valor_total').html(convertMoney(valorProduto))
}

function convertMoney(v){
	return v.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

$('#quantidade').keyup((value) => {
	let qtd = value.target.value
	if(!qtd || qtd == 0){
		$('#quantidade').val('1')
	}
	somaTotal();
})

$('#quantidade').click((value) => {
	let qtd = value.target.value
	if(!qtd || qtd == 0){
		$('#quantidade').val('1')
	}
	somaTotal();
})

function adicionar(){
	let tk = $('#_token').val();
	let sabores = JSON.parse($('#sabores').val());
	let quantidade = $('#quantidade').val();
	let observacao = $('#observacao').val();
	let tamanho = $('#tamanho').val();

	let js = {
		_token: tk, 
		sabores: sabores,
		tamanho: tamanho,
		adicionais: adicionais,
		quantidade: quantidade,
		observacao: observacao
	};

	$.post(path + "carrinho/addPizza", js
		)
	.done(function(data) {
		if(data == '401'){
			swal("", "Você precisa estar logado", "error")
		}
		else if(data == 'false'){
			swal("", "Você está com um pedido pendente, aguarde o processamento", "warning")

		}else{
			sucesso();
		}
	})
	.fail( function(err) {
		console.log(err)

	});

}

function sucesso(){
	$('#content').css('display', 'none');
	$('#anime').css('display', 'block');
	setTimeout(() => {
		location.href = path + 'carrinho';
	}, 3000)
}
