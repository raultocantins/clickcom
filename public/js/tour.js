$(function () {
	// console.clear()
	setTimeout(() => {
		$('head').append('<link href="/css/bootstrap-tour-standalone.css" rel="stylesheet">');
		var tour = getTour()
		setTimeout(() => {

			let toutVar = window.localStorage.getItem('tour-slym');
			if(!toutVar){
				localStorage.removeItem('tour_current_step');
				localStorage.removeItem('tour_end');
				tour.init();
				tour.start();
				window.localStorage.setItem('tour-slym', true);
			}
		}, 200);

	}, 100);
});

function getTour(){
	var tour = new Tour({
		steps: [
		{
			element: "",
			title: "Bem vindo!",
			content: "Esse é o seu primeiro acesso, obrigado :)"
		},
		{
			element: ".top-tour",
			title: "Dados da empresa",
			content: "Aqui temos o nome da sua empresa, hora e tema, fique a vontade para escolher um tema."
		},
		{
			element: "#ambiente-tour",
			title: "Configuração da empresa",
			content: "Primeiro passo é cadastrar os dados da sua empresa, e fazer upload do certificado digital, clicando aqui!"
		},
		{
			element: ".menu-tour",
			title: "Menu lateral",
			content: "Navegue pelas telas do sistema utilizando o menu lateral"
		},
		{
			element: ".recolhe-tour",
			title: "Menu lateral",
			content: "É possivel reduzir a largura do menu clicando aqui."
		},
		{
			element: "#Configurações-tour",
			title: "Configurações",
			content: "Inicie cadastrando a tributação e pelo menus uma natureza de operação :)",
		},
		{
			element: "#Cadastros-tour",
			title: "Cadastros",
			content: "Então pode cadastre uma categoria, um produto e um cliente.",
		},
		{
			element: "#Vendas-tour",
			title: "Vendas",
			content: "Após isso fique a vontade para explorar o sistema, realizar vendas, orçamentos e etc.",

		},
		{
			element: ".pdv-tour",
			title: "PDV",
			content: "Aqui temos um atalho para o PDV.",
			placement: 'left'
		},
		{
			element: ".notifica-tour",
			title: "Notificações",
			content: "Alertas do sistema, contas a receber/pagar, produtos com estoque mínimo e outros.",
			placement: 'left'
		},
		{
			element: ".user-tour",
			title: "Usuário",
			content: "Nome do usuário logado e botão de saída do sistema.",
			placement: 'left'
		},
		{
			element: "",
			title: "Fim do tour!",
			content: "Obrigado por se cadastrar, boa experiência com o sistema"
		},
		],
		orphan: true,
		backdrop: true,
		template:
		"<div class='popover tour'><div class='arrow'></div><h3 class='popover-title text-bold'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-info btn-sm' data-role='prev'>« " +
		'voltar' +
		"</button>&nbsp;<button class='btn btn-success btn-sm' data-role='next'>" +
		'próximo' +
		" »</button><button class='btn btn-danger btn-sm' data-role='end'>" +
		'fim do tour' +
		'</button></div></div>',
		onEnd: function(){
			$('link[rel=stylesheet][href*="/css/bootstrap-tour-standalone.css"]').remove();
		}
	});
	return tour;
}
$('#clickTour').click(() => {
	alert('oo')
	$('head').append('<link href="/css/bootstrap-tour-standalone.css" rel="stylesheet">');
	setTimeout(() => {
		var tour = getTour()
		localStorage.removeItem('tour_current_step');
		localStorage.removeItem('tour_end');
		tour.init();
		tour.start();
	}, 100);
})
