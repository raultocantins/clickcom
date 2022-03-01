$(function () {
	let v = $('#cnpj2').val();
	if(v.length == 18){
		$('#tipo-doc').html('CNPJ');
		$('#tipo').val('j').change();
		$('#cnpj2').mask('00.000.000/0000-00', { reverse: true });

	}else{
		$('#tipo-doc').html('CPF');
		$('#cnpj2').mask('000.000.000-00', { reverse: true });
	}
});

$('#tipo').change(() => {
	let tipo = $('#tipo').val();
	if(tipo == 'j'){
		$('#tipo-doc').html('CNPJ');
		$('#cnpj2').mask('00.000.000/0000-00', { reverse: true });
	}else{
		$('#tipo-doc').html('CPF');
		$('#cnpj2').mask('000.000.000-00', { reverse: true });
	}
})