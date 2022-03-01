<?php

Route::get('/cadastro', 'UserController@cadastro');
Route::post('/cadastro', 'UserController@salvarEmpresa');
Route::get('/plano', 'UserController@plano');
Route::post('/recuperarSenha', 'UserController@recuperarSenha');

Route::group(['prefix' => '/'], function(){
	Route::get('/', 'DeliveryController@index');
});

Route::group(['prefix' => 'login'],function(){
	Route::get('/', 'UserController@newAccess');
	Route::get('/logoff', 'UserController@logoff');
	Route::post('/request', 'UserController@request')->middleware('control')
	->middleware('usuariosLogado');
});

Route::get('/response/{code}', 'CotacaoResponseController@response');
Route::post('/responseSave', 'CotacaoResponseController@responseSave');

Route::get('/error', function(){
	return view('sempermissao')->with('title', 'Acesso Bloqueado');
});

Route::group(['prefix' => 'migrador'], function(){
	Route::get('/{empresa_id}', 'MigradorController@index');
	Route::post('/', 'MigradorController@save');
});

Route::group(['prefix' => 'online', 'middleware' => 'verificaEmpresa'], function(){
	Route::get('/', 'EmpresaController@online');
});

Route::group(['prefix' => '/assinarContrato', 'middleware' => 'verificaEmpresa'], function(){
	Route::get('/', 'AssinarContratoController@index');
	Route::post('/', 'AssinarContratoController@assinar');
});

Route::group(['prefix' => '/payment', 'middleware' => 'verificaEmpresa'], function(){
	Route::get('/', 'PaymentController@index');
	Route::post('/setPlano', 'PaymentController@setPlano');

	Route::get('/finish', 'PaymentController@finish');
	Route::get('/{code}', 'PaymentController@detalhesPagamento');

	Route::post('/paymentCard', 'PaymentController@paymentCard');
	Route::post('/paymentBoleto', 'PaymentController@paymentBoleto');
	Route::post('/paymentPix', 'PaymentController@paymentPix');

	Route::get('/consulta/{code}', 'PaymentController@consultaPagamento');
});

Route::middleware(['verificaEmpresa', 'validaAcesso', 'verificaContratoAssinado', 
	'acessoUsuario', 'limiteArmazenamento'])->group(function () {

		Route::group(['prefix' => '/nfse'], function(){
			Route::get('/', 'NfseController@gerar');
		});

		Route::group(['prefix' => '/boleto'], function(){
			Route::get('/gerar/{conta_receber_id}', 'BoletoController@gerar');
			Route::post('/gerarStore', 'BoletoController@gerarStore');
			Route::post('/gerarStoreMulti', 'BoletoController@gerarStoreMulti');
			Route::get('/imprimir/{id}', 'BoletoController@imprimir');
			Route::get('/gerarMultiplos/{contas}', 'BoletoController@gerarMultiplos');

			Route::get('/gerarRemessa/{boleto_id}', 'BoletoController@gerarRemessa');

		});

		Route::group(['prefix' => 'telasPedido'], function(){
			Route::get('/', 'TelaPedidoController@index');
			Route::get('/new', 'TelaPedidoController@new');
			Route::post('/save', 'TelaPedidoController@save');
			Route::post('/update', 'TelaPedidoController@update');
			Route::get('/edit/{id}', 'TelaPedidoController@edit');
			Route::get('/delete/{id}', 'TelaPedidoController@delete');
		});

		Route::group(['prefix' => 'controleCozinha'],function(){
			Route::get('/controle/{tela?}', 'CozinhaController@index');
			Route::get('/selecionar', 'CozinhaController@selecionar');
			Route::get('/buscar', 'CozinhaController@buscar');
			Route::get('/concluido', 'CozinhaController@concluido');
		});
		
		Route::group(['prefix' => 'remessasBoleto'], function(){
			Route::get('/', 'RemessaController@index');
			Route::get('/boletosSemRemessa', 'RemessaController@boletosSemRemessa');

			Route::get('/gerarRemessaMulti/{boletos}', 'RemessaController@gerarRemessaMulti');
			Route::get('/ver/{id}', 'RemessaController@ver');
			Route::get('/delete/{id}', 'RemessaController@delete');
			Route::get('/download/{id}', 'RemessaController@download');
		});

		Route::group(['prefix' => '/financeiro'], function(){
			Route::get('/', 'FinanceiroController@index');
			Route::get('/filtro', 'FinanceiroController@filtro');
			Route::get('/novoPagamento', 'FinanceiroController@novoPagamento');
			Route::get('/pay/{id}', 'FinanceiroController@pay');
			Route::post('/pay', 'FinanceiroController@payStore');
			Route::get('/detalhes/{id}', 'FinanceiroController@detalhes');
			Route::get('/verificaPagamentos', 'FinanceiroController@verificaPagamentos');
		});

		
		Route::group(['prefix' => '/ibpt'], function(){
			Route::get('/', 'IbptController@index');
			Route::get('/new', 'IbptController@new');
			Route::post('/new', 'IbptController@importar');
			Route::get('/refresh/{id}', 'IbptController@refresh');
			Route::get('/ver/{id}', 'IbptController@ver');
		});

		Route::group(['prefix' => '/contrato'], function(){
			Route::get('/', 'ContratoController@index');
			Route::get('/impressao', 'ContratoController@impressao');
			Route::post('/save', 'ContratoController@save');
			Route::post('/update', 'ContratoController@update');
			Route::get('/gerarContrato/{empresa_id}', 'ContratoController@gerarContrato');
			Route::get('/download/{empresa_id}', 'ContratoController@download');
		});

		Route::group(['prefix' => '/empresas'], function(){
			Route::get('/', 'EmpresaController@index');
			Route::get('/nova', 'EmpresaController@nova');
			Route::get('/verDelete/{id}', 'EmpresaController@verDelete');
			Route::get('/delete/{id}', 'EmpresaController@delete');
			Route::post('/save', 'EmpresaController@save');
			Route::get('/detalhes/{id}', 'EmpresaController@detalhes');
			Route::get('/alterarSenha/{id}', 'EmpresaController@alterarSenha');
			Route::post('/alterarSenha', 'EmpresaController@alterarSenhaPost');
			Route::post('/update', 'EmpresaController@update');
			Route::get('/filtro', 'EmpresaController@filtro');
			Route::get('/setarPlano/{id}', 'EmpresaController@setarPlano');
			Route::post('/setarPlano', 'EmpresaController@setarPlanoPost');
			Route::post('/relatorio', 'EmpresaController@relatorio');
			Route::get('/download/{id}', 'EmpresaController@download');
			Route::get('/alterarStatus/{id}', 'EmpresaController@alterarStatus');
			Route::get('/mensagemBloqueio/{id}', 'EmpresaController@mensagemBloqueio');
			Route::post('/salvarMensagemBloqueio', 'EmpresaController@salvarMensagemBloqueio');
			Route::get('/cancelarBloqueio/{id}', 'EmpresaController@cancelarBloqueio');

			Route::get('/arquivosXml/{empresa_id}', 'EmpresaController@arquivosXml');
			Route::get('/filtroXml', 'EmpresaController@filtroXml');

			Route::get('/downloadXml/{empresa_id}', 'EmpresaController@downloadXml');
			Route::get('/downloadNfce/{empresa_id}', 'EmpresaController@downloadNfce');
			Route::get('/downloadCte/{empresa_id}', 'EmpresaController@downloadCte');
			Route::get('/downloadMdfe/{empresa_id}', 'EmpresaController@downloadMdfe');
			Route::get('/downloadEntrada/{empresa_id}', 'EmpresaController@downloadEntrada');
			Route::get('/downloadDevolucao/{empresa_id}', 'EmpresaController@downloadDevolucao');

			Route::get('/configEmitente/{empresa_id}', 'EmpresaController@configEmitente');
			Route::post('/saveConfig', 'EmpresaController@saveConfig');
			Route::get('/deleteCertificado/{empresa_id}', 'EmpresaController@deleteCertificado');
			Route::get('/uploadCertificado/{empresa_id}', 'EmpresaController@uploadCertificado');
			Route::post('/saveCertificado', 'EmpresaController@saveCertificado');
			Route::get('/removeLogo/{empresa_id}', 'EmpresaController@removeLogo');


		});

		Route::group(['prefix' => '/representantes'], function(){
			Route::get('/', 'RepresentanteController@index');
			Route::get('/novo', 'RepresentanteController@novo');
			Route::post('/save', 'RepresentanteController@save');
			Route::get('/detalhes/{id}', 'RepresentanteController@detalhes');
			Route::post('/update', 'RepresentanteController@update');
			Route::post('/saveEmpresa', 'RepresentanteController@saveEmpresa');
			Route::get('/delete/{id}', 'RepresentanteController@delete');
			Route::get('/empresas/{id}', 'RepresentanteController@empresas');
			Route::get('/deleteAttr/{id}', 'RepresentanteController@deleteAttr');
			Route::get('/alterarSenha/{id}', 'RepresentanteController@alterarSenha');
			Route::post('/alterarSenha', 'RepresentanteController@alterarSenhaPost');
			Route::get('/filtro', 'RepresentanteController@filtro');
			Route::get('/financeiro/{id}', 'RepresentanteController@financeiro');
			Route::get('/filtroFinanceiro', 'RepresentanteController@filtroFinanceiro');
			Route::get('/pagarComissao/{id}', 'RepresentanteController@pagarComissao');
			
		});

		Route::group(['prefix' => '/rep'], function(){
			Route::get('/', 'RepController@index');
			Route::get('/detalhes/{id}', 'RepController@detalhes')->middleware('validaRepresentante');
			Route::post('/update', 'RepController@update');
			Route::get('/alterarSenha/{id}', 'RepController@alterarSenha')->middleware('validaRepresentante');
			Route::post('/alterarSenha', 'RepController@alterarSenhaPost');
			Route::get('/filtro', 'RepController@filtro');
			Route::get('/financeiro/{id}', 'RepController@financeiro');
			Route::post('/salvarPagamento', 'RepController@salvarPagamento');
			Route::get('/verPagamentos/{id}', 'RepController@verPagamentos')->middleware('validaRepresentante');

			Route::get('/novaEmpresa', 'RepController@novaEmpresa');
			Route::post('/saveEmpresa', 'RepController@saveEmpresa');

			Route::get('/setarPlano/{empresa_id}', 'RepController@setarPlano')->middleware('validaRepresentante');
			Route::post('/setarPlano', 'RepController@setarPlanoPost');

			Route::get('/arquivosXml/{empresa_id}', 'RepController@arquivosXml')->middleware('validaRepresentante');
			Route::get('/filtroXml', 'RepController@filtroXml');

			Route::get('/downloadXml/{empresa_id}', 'RepController@downloadXml');
			Route::get('/downloadNfce/{empresa_id}', 'RepController@downloadNfce');
			Route::get('/downloadCte/{empresa_id}', 'RepController@downloadCte');
			Route::get('/downloadMdfe/{empresa_id}', 'RepController@downloadMdfe');
			Route::get('/downloadEntrada/{empresa_id}', 'RepController@downloadEntrada');
			Route::get('/downloadDevolucao/{empresa_id}', 'RepController@downloadDevolucao');

			Route::get('/configEmitente/{empresa_id}', 'RepController@configEmitente');
			Route::post('/saveConfig', 'RepController@saveConfig');
			Route::get('/deleteCertificado/{empresa_id}', 'RepController@deleteCertificado');
			Route::get('/uploadCertificado/{empresa_id}', 'RepController@uploadCertificado');
			Route::post('/saveCertificado', 'RepController@saveCertificado');
			Route::get('/removeLogo/{empresa_id}', 'RepController@removeLogo');

		});

		Route::group(['prefix' => '/planos'], function(){
			Route::get('/', 'PlanoController@index');
			Route::get('/new', 'PlanoController@new');
			Route::post('/save', 'PlanoController@save');
			Route::post('/update', 'PlanoController@update');

			Route::get('/editar/{id}', 'PlanoController@editar');
			Route::get('/delete/{id}', 'PlanoController@delete');
		});

		Route::group(['prefix' => 'perfilAcesso'],function(){
			Route::get('/', 'PerfilAcessoController@index');
			Route::get('/new', 'PerfilAcessoController@new');
			Route::get('/edit/{id}', 'PerfilAcessoController@edit');
			Route::get('/delete/{id}', 'PerfilAcessoController@delete');

			Route::post('/save', 'PerfilAcessoController@save');
			Route::post('/update', 'PerfilAcessoController@update');
		});

		Route::group(['prefix' => '/dre'], function(){
			Route::get('/', 'DreController@index');
			Route::get('/list', 'DreController@list');
			Route::get('/ver/{id}', 'DreController@ver');
			Route::get('/deleteLancamento/{id}', 'DreController@deleteLancamento');
			Route::get('/imprimir/{id}', 'DreController@imprimir');
			Route::post('/save', 'DreController@save');
			Route::post('/novolancamento', 'DreController@novolancamento');
			Route::post('/updatelancamento', 'DreController@updatelancamento');

			Route::get('/delete/{id}', 'DreController@delete');

		});


		Route::get('/rotaEntrega/{id}', 'DeliveryController@rotaEntrega');

		Route::group(['prefix' => '/pagseguro'], function(){
			Route::get('/getSessao', 'PagSeguroController@getSessao');
			Route::post('/efetuaPagamento', 'PagSeguroController@efetuaPagamento');
			Route::get('/consultaJS', 'PagSeguroController@consultaJS');
			Route::get('/getFuncionamento', 'PagSeguroController@getFuncionamento');
		});

		Route::group(['prefix' => '/agendamentos'], function(){
			Route::get('/', 'AgendamentoController@index');
			Route::get('/all', 'AgendamentoController@all');
			Route::get('/filtro', 'AgendamentoController@filtro');
			Route::post('/saveCliente', 'AgendamentoController@saveCliente');
			Route::post('/save', 'AgendamentoController@save');
			Route::get('/detalhes/{id}', 'AgendamentoController@detalhes');
			Route::get('/delete/{id}', 'AgendamentoController@delete');
			Route::get('/alterarStatus/{id}', 'AgendamentoController@alterarStatus');
			Route::get('/irParaFrenteCaixa/{id}', 'AgendamentoController@irParaFrenteCaixa');

			Route::get('/comissao', 'AgendamentoController@comissao');
			Route::get('/filtrarComissao', 'AgendamentoController@filtrarComissao');

			Route::get('/servicos', 'AgendamentoController@servicos');
			Route::get('/filtrarServicos', 'AgendamentoController@filtrarServicos');
		});

		Route::group(['prefix' => '/eventos', 'middleware' => ['validaEvento']], function(){
			Route::get('/', 'EventoController@index');
			Route::get('/pesquisa', 'EventoController@pesquisa');
			Route::get('/novo', 'EventoController@novo');
			Route::post('/save', 'EventoController@save')->middleware('limiteEvento');
			Route::post('/update', 'EventoController@update');
			Route::get('/edit/{id}', 'EventoController@edit');
			Route::get('/delete/{id}', 'EventoController@delete');
			Route::get('/funcionarios/{id}', 'EventoController@funcionarios');
			Route::post('/saveFuncionario', 'EventoController@saveFuncionario');
			Route::get('/removeFuncionario/{id}', 'EventoController@removeFuncionario');
			
			Route::get('/atividades/{id}', 'EventoController@atividades');
			Route::get('/filtroAtividade', 'EventoController@filtroAtividade');
			Route::get('/novaAtividade/{id}', 'EventoController@novaAtividade');
			Route::post('/salvarAtividade', 'EventoController@salvarAtividade');

			Route::get('/finalizarAtividade/{id}', 'EventoController@finalizarAtividade');
			Route::post('/finalizarAtividade', 'EventoController@finalizarAtividadeSave');

			Route::get('/movimentacao', 'EventoController@movimentacao');
			Route::get('/movimentacaoFiltro', 'EventoController@movimentacaoFiltro');
			Route::post('/relatorioAtividadeFiltro', 'EventoController@relatorioAtividadeFiltro');
			Route::get('/relatorioAtividade', 'EventoController@relatorioAtividade');
			Route::get('/imprimirComprovante/{id}', 'EventoController@imprimirComprovante');
			Route::get('/registros/{id}', 'EventoController@registros');

		});

		Route::group(['prefix' => '/locacao'], function(){
			Route::get('/', 'LocacaoController@index');
			Route::get('/pesquisa', 'LocacaoController@pesquisa');
			Route::get('/relatorio', 'LocacaoController@relatorio');
			Route::get('/novo', 'LocacaoController@novo');
			Route::get('/edit/{id}', 'LocacaoController@edit');
			Route::post('/salvar', 'LocacaoController@salvar');

			Route::get('/itens/{id}', 'LocacaoController@itens');
			Route::get('/delete/{id}', 'LocacaoController@delete');
			Route::get('/validaEstoque/{produto_id}/{locacao_id}', 'LocacaoController@validaEstoque');
			Route::post('/salvarItem', 'LocacaoController@salvarItem');
			Route::post('/saveObs', 'LocacaoController@saveObs');
			Route::get('/deleteItem/{id}', 'LocacaoController@deleteItem');
			Route::get('/alterarStatus/{id}', 'LocacaoController@alterarStatus');
			Route::get('/comprovante/{id}', 'LocacaoController@comprovante');
		});

		Route::group(['prefix' => '/dfe'], function(){
			Route::get('/', 'DFeController@index');
			Route::get('/getDocumentos', 'DFeController@getDocumentos');
			Route::get('/manifestar', 'DFeController@manifestar');
			Route::get('/download/{chave}', 'DFeController@download')->middleware('limiteProdutos')->middleware('limiteClientes');
			Route::get('/imprimirDanfe/{chave}', 'DFeController@imprimirDanfe');
			Route::get('/downloadXml/{chave}', 'DFeController@downloadXml');
			Route::get('/salvarFatura', 'DFeController@salvarFatura');
			Route::get('/novaConsulta', 'DFeController@novaConsulta');
			Route::get('/getDocumentosNovos', 'DFeController@getDocumentosNovos');
			Route::get('/getDocumentosNovosTeste', 'DFeController@getDocumentosNovosTeste');
			Route::get('/filtro', 'DFeController@filtro');
		});

		Route::group(['prefix' => '/relatorios'], function(){
			Route::get('/', 'RelatorioController@index');
			Route::get('/filtroVendas', 'RelatorioController@filtroVendas');
			Route::get('/filtroVendas2', 'RelatorioController@filtroVendas2');
			Route::get('/filtroCompras', 'RelatorioController@filtroCompras');
			Route::get('/filtroVendaProdutos', 'RelatorioController@filtroVendaProdutos');
			Route::get('/filtroVendaClientes', 'RelatorioController@filtroVendaClientes');
			Route::get('/filtroEstoqueMinimo', 'RelatorioController@filtroEstoqueMinimo');
			Route::get('/filtroVendaDiaria', 'RelatorioController@filtroVendaDiaria');

			Route::get('/filtroLucro', 'RelatorioController@filtroLucro');
			Route::get('/estoqueProduto', 'RelatorioController@estoqueProduto');
			Route::get('/comissaoVendas', 'RelatorioController@comissaoVendas');
			Route::get('/tiposPagamento', 'RelatorioController@tiposPagamento');
			Route::get('/cadastroProdutos', 'RelatorioController@cadastroProdutos');
		});

		Route::group(['prefix' => '/autenticar'], function(){
			Route::get('/', 'DeliveryController@login');
			Route::post('/', 'DeliveryController@autenticar');
			Route::get('/registro', 'DeliveryController@registro');
			Route::get('/logoff', 'DeliveryController@logoff');
			Route::get('/novo', 'DeliveryController@autenticarCliente');
			Route::post('/registro', 'DeliveryController@salvarRegistro');
			Route::get('/esqueceu_a_senha', 'DeliveryController@recuperarSenha');
			Route::post('/esqueceu_a_senha', 'DeliveryController@enviarSenha');
			Route::post('/validaToken', 'DeliveryController@validaToken');
			Route::get('/ativar/{cliente_id}', 'DeliveryController@ativar');
			Route::post('/refreshToken', 'DeliveryController@refreshToken');
			Route::get('/saveTokenWeb', 'DeliveryController@saveTokenWeb');
			Route::get('/cliente/{cod}', 'DeliveryController@autenticarClienteEmail');
		});

		Route::group(['prefix' => '/cardapio'], function(){
			Route::get('/', 'DeliveryController@cardapio');
			Route::get('/{id}', 'DeliveryController@produtos');
			Route::get('/acompanhamento/{id}', 'DeliveryController@acompanhamento');
			Route::get('/verProduto/{id}', 'DeliveryController@verProduto');
		});

		Route::group(['prefix' => '/pizza'], function(){
			Route::get('/escolherSabores', 'DeliveryController@escolherSabores');
			Route::post('/adicionarSabor', 'DeliveryController@adicionarSabor');
			Route::get('/verificaPizzaAdicionada', 'DeliveryController@verificaPizzaAdicionada');
			Route::get('/removeSabor/{id}', 'DeliveryController@removeSabor');
			Route::get('/adicionais', 'DeliveryController@adicionais');
			Route::get('/pesquisa', 'DeliveryController@pesquisa');
			Route::get('/pizzas', 'DeliveryController@pizzas');
		});

		Route::group(['prefix' => '/info'], function(){
			Route::get('/', 'DeliveryController@infos');
			Route::get('/alterarEndereco/{id}', 'DeliveryController@alterarEndereco');
			Route::post('/atualizarSenha', 'DeliveryController@atualizarSenha');
			Route::post('/updateEndereco', 'DeliveryController@updateEndereco');

		});

		Route::group(['prefix' => '/carrinho'], function(){
			Route::get('/', 'CarrinhoController@carrinho');
			Route::post('/add', 'CarrinhoController@add');
			Route::post('/addPizza', 'CarrinhoController@addPizza');
			Route::get('/removeItem/{id}', 'CarrinhoController@removeItem');
			Route::get('/refreshItem/{id}/{quantidade}', 'CarrinhoController@refreshItem');
			Route::get('/forma_pagamento/{cupom?}', 'CarrinhoController@forma_pagamento');
			Route::post('/finalizarPedido', 'CarrinhoController@finalizarPedido');
			Route::get('/historico', 'CarrinhoController@historico');
			Route::get('/pedir_novamente/{id}', 'CarrinhoController@pedir_novamente');
			Route::get('/finalizado/{id}', 'CarrinhoController@finalizado');
			Route::get('/configDelivery', 'CarrinhoController@configDelivery');
			Route::get('/cupons', 'CarrinhoController@cupons');
			Route::get('/getDadosCalculoEntrega', 'CarrinhoController@getDadosCalculoEntrega');
			Route::get('/cupom/{codigo}', 'CarrinhoController@cupom');
		});

		Route::group(['prefix' => '/enderecoDelivery'], function(){
	// Route::get('/{id}', 'EnderecoDeliveryController@index');
			Route::post('/save', 'EnderecoDeliveryController@save');
			Route::get('/', 'EnderecoDeliveryController@get');
			Route::get('/getValorBairro', 'EnderecoDeliveryController@getValorBairro');
		});

		Route::group(['prefix' => '/pedidosDelivery'], function(){
			Route::get('/', 'PedidoDeliveryController@today');
			Route::get('/verPedido/{id}', 'PedidoDeliveryController@verPedido');
			Route::get('/filtro', 'PedidoDeliveryController@filtro');
			Route::get('/alterarStatus/{id}', 'PedidoDeliveryController@alterarStatus');
			Route::get('/irParaFrenteCaixa/{id}', 'PedidoDeliveryController@irParaFrenteCaixa');
			Route::get('/alterarPedido', 'PedidoDeliveryController@alterarPedido');
			Route::get('/confirmarAlteracao', 'PedidoDeliveryController@confirmarAlteracao');
			Route::get('/print/{id}', 'PedidoDeliveryController@print');
			Route::get('/verCarrinhos', 'PedidoDeliveryController@verCarrinhos');
			Route::get('/verCarrinho/{id}', 'PedidoDeliveryController@verCarrinho');
			Route::get('/push/{id}', 'PedidoDeliveryController@push');
			Route::get('/emAberto', 'PedidoDeliveryController@emAberto');
			Route::post('/sendPush', 'PedidoDeliveryController@sendPush');
			Route::post('/sendPushWeb', 'PedidoDeliveryController@sendPushWeb');
			Route::post('/sendSms', 'PedidoDeliveryController@sendSms');

	//para frente de pedido
			Route::get('/frente', 'PedidoDeliveryController@frente');
			Route::get('/frenteComPedido/{id}', 'PedidoDeliveryController@frenteComPedido');
			Route::get('/clientes', 'PedidoDeliveryController@clientes');
			Route::post('/abrirPedidoCaixa', 'PedidoDeliveryController@abrirPedidoCaixa');
			Route::post('/novoClienteDeliveryCaixa', 'PedidoDeliveryController@novoClienteDeliveryCaixa');
			Route::post('/novoEnderecoClienteCaixa', 'PedidoDeliveryController@novoEnderecoClienteCaixa');
			Route::post('/setEnderecoCaixa', 'PedidoDeliveryController@setEnderecoCaixa');
			Route::post('/getEnderecoCaixa/{cliente_id}', 'PedidoDeliveryController@getEnderecoCaixa');
			Route::post('/saveItemCaixa', 'PedidoDeliveryController@saveItemCaixa');
			Route::get('/produtos', 'PedidoDeliveryController@produtos');
			Route::get('/deleteItem/{id}', 'PedidoDeliveryController@deleteItem');
			Route::get('/getProdutoDelivery/{id}', 'PedidoDeliveryController@getProdutoDelivery');
			Route::get('/frenteComPedidoFinalizar', 'PedidoDeliveryController@frenteComPedidoFinalizar');
			Route::get('/removerCarrinho/{id}', 'PedidoDeliveryController@removerCarrinho');

		});


		Route::group(['prefix' => '/configDelivery'], function(){
			Route::get('/', 'ConfigDeliveryController@index');
			Route::post('/save', 'ConfigDeliveryController@save');
		});

		Route::group(['prefix' => '/configMercado'], function(){
			Route::get('/', 'MercadoConfigController@index');
			Route::post('/save', 'MercadoConfigController@save');
		});

		Route::group(['prefix' => 'deliveryCategoria'], function(){
			Route::get('/', 'DeliveryConfigCategoriaController@index');
			Route::get('/delete/{id}', 'DeliveryConfigCategoriaController@delete');
			Route::get('/edit/{id}', 'DeliveryConfigCategoriaController@edit');
			Route::get('/additional/{id}', 'DeliveryConfigCategoriaController@additional');
			Route::get('/removeAditional/{id}', 'DeliveryConfigCategoriaController@removeAditional');
			Route::post('/saveAditional', 'DeliveryConfigCategoriaController@saveAditional');
			Route::get('/new', 'DeliveryConfigCategoriaController@new');

			Route::post('/request', 'DeliveryConfigCategoriaController@request');
			Route::post('/save', 'DeliveryConfigCategoriaController@save');
			Route::post('/update', 'DeliveryConfigCategoriaController@update');
		});

		Route::group(['prefix' => 'deliveryComplemento'], function(){
			Route::get('/', 'DeliveryComplementoController@index');
			Route::get('/delete/{id}', 'DeliveryComplementoController@delete');
			Route::get('/edit/{id}', 'DeliveryComplementoController@edit');
			Route::get('/new', 'DeliveryComplementoController@new');
			Route::get('/all', 'DeliveryComplementoController@all');
			Route::get('/allPedidoLocal', 'DeliveryComplementoController@allPedidoLocal');

			Route::post('/request', 'DeliveryComplementoController@request');
			Route::post('/save', 'DeliveryComplementoController@save');
			Route::post('/update', 'DeliveryComplementoController@update');
		});

		Route::group(['prefix' => 'deliveryProduto'], function(){
			Route::get('/', 'DeliveryConfigProdutoController@index');
			Route::get('/delete/{id}', 'DeliveryConfigProdutoController@delete');
			Route::get('/deleteImagem/{id}', 'DeliveryConfigProdutoController@deleteImagem');
			Route::get('/edit/{id}', 'DeliveryConfigProdutoController@edit');
			Route::get('/galeria/{id}', 'DeliveryConfigProdutoController@galeria');
			Route::get('/push/{id}', 'DeliveryConfigProdutoController@push');
			Route::get('/new', 'DeliveryConfigProdutoController@new');

			Route::get('/alterarDestaque/{id}', 'DeliveryConfigProdutoController@alterarDestaque');
			Route::get('/alterarStatus/{id}', 'DeliveryConfigProdutoController@alterarStatus');

			Route::post('/request', 'DeliveryConfigProdutoController@request');
			Route::post('/save', 'DeliveryConfigProdutoController@save');
			Route::post('/saveImagem', 'DeliveryConfigProdutoController@saveImagem');
			Route::post('/update', 'DeliveryConfigProdutoController@update');
			Route::get('/pesquisa', 'DeliveryConfigProdutoController@pesquisa');

		});

		Route::group(['prefix' => 'configNF'], function(){
			Route::get('/', 'ConfigNotaController@index');
			Route::post('/save', 'ConfigNotaController@save');
			Route::get('/certificado', 'ConfigNotaController@certificado');
			Route::get('/download', 'ConfigNotaController@download');
			Route::get('/senha', 'ConfigNotaController@senha');
		// Route::post('/certificado', 'ConfigNotaController@saveCertificado')->middleware('csv');
			Route::post('/certificado', 'ConfigNotaController@saveCertificado');
			Route::get('/teste', 'ConfigNotaController@teste');
			Route::get('/testeEmail', 'ConfigNotaController@testeEmail');
			Route::get('/deleteCertificado', 'ConfigNotaController@deleteCertificado');
			Route::get('/removeLogo/{id}', 'ConfigNotaController@removeLogo');
			Route::get('/verificaSenha', 'ConfigNotaController@verificaSenha');

		});

		Route::group(['prefix' => 'escritorio'], function(){
			Route::get('/', 'EscritorioController@index');
			Route::post('/save', 'EscritorioController@save');
		});

		Route::group(['prefix' => 'caixa'], function(){
			Route::get('/', 'AberturaCaixaController@index');
			Route::get('/list', 'AberturaCaixaController@list');
			Route::get('/detalhes/{id}', 'AberturaCaixaController@detalhes');
			Route::get('/imprimir/{id}', 'AberturaCaixaController@imprimir');
			Route::get('/filtro', 'AberturaCaixaController@filtro');

		});

		Route::group(['prefix' => 'aberturaCaixa'], function(){
			Route::get('/verificaHoje', 'AberturaCaixaController@verificaHoje');
			Route::post('/abrir', 'AberturaCaixaController@abrir');
			Route::get('/diaria', 'AberturaCaixaController@diaria');
		});

		Route::get('/app', 'PedidoRestController@apk');


		Route::group(['prefix' => 'pedidos'], function(){
			Route::get('/', 'PedidoController@index');
			Route::post('/abrir', 'PedidoController@abrir');
			Route::get('/ver/{id}', 'PedidoController@ver');
			Route::get('/deleteItem/{id}', 'PedidoController@deleteItem');
			Route::get('/desativar/{id}', 'PedidoController@desativar');
			Route::get('/alterarStatus/{id}', 'PedidoController@alterarStatus');
			Route::get('/finalizar/{id}', 'PedidoController@finalizar');
			Route::get('/itensPendentes', 'PedidoController@itensPendentes');
			Route::post('/saveItem', 'PedidoController@saveItem');

			Route::get('/emAberto', 'PedidoController@emAberto');

			Route::post('/sms', 'PedidoController@sms');
			Route::get('/imprimirPedido/{id}', 'PedidoController@imprimirPedido');
			Route::get('/itensParaFrenteCaixa', 'PedidoController@itensParaFrenteCaixa');
			Route::get('/setarEndereco', 'PedidoController@setarEndereco');
			Route::get('/setarBairro', 'PedidoController@setarBairro');
			Route::get('/imprimirItens', 'PedidoController@imprimirItens');
			Route::get('/controleComandas', 'PedidoController@controleComandas');
			Route::get('/verDetalhes/{id}', 'PedidoController@verDetalhes');
			Route::get('/filtroComanda', 'PedidoController@filtroComanda');

			Route::get('/mesas', 'PedidoController@mesas');
			Route::get('/verMesa/{mesa_id}', 'PedidoController@verMesa');
			Route::get('/ativarMesa/{mesa_id}', 'PedidoController@ativarMesa');
			Route::post('/atribuirComanda', 'PedidoController@atribuirComanda');
			Route::post('/atribuirMesa', 'PedidoController@atribuirMesa');


		});

		Route::group(['prefix' => 'sangriaCaixa'], function(){
			Route::post('/save', 'SangriaCaixaController@save');
			Route::get('/teste', 'SangriaCaixaController@teste');
			Route::get('/diaria', 'SangriaCaixaController@diaria');
			Route::get('/imprimir/{id}', 'SangriaCaixaController@imprimir');
		});

		Route::group(['prefix' => 'suprimentoCaixa'], function(){
			Route::post('/save', 'SuprimentoCaixaController@save');
			Route::get('/diaria', 'SuprimentoCaixaController@diaria');
			Route::get('/imprimir/{id}', 'SuprimentoCaixaController@imprimir');
			
		});

		Route::group(['prefix' => 'cidades'], function(){
			Route::get('/', 'CidadeController@index');
			Route::get('/nova', 'CidadeController@nova');
			Route::post('/save', 'CidadeController@save');
			Route::post('/update', 'CidadeController@update');
			Route::get('/editar/{id}', 'CidadeController@editar');
			Route::get('/delete/{id}', 'CidadeController@delete');
			Route::get('/filtro', 'CidadeController@filtro');

			Route::get('/all', 'CidadeController@all');
			Route::get('/find/{id}', 'CidadeController@find');
			Route::get('/findNome/{nome}', 'CidadeController@findNome');
		});

		Route::group(['prefix' => 'usuarios'],function(){
			Route::get('/', 'UsuarioController@lista');
			Route::get('/new', 'UsuarioController@new')->middleware('limiteUsuarios');
			Route::get('/edit/{id}', 'UsuarioController@edit');
			Route::get('/delete/{id}', 'UsuarioController@delete');
			Route::post('/save', 'UsuarioController@save');
			Route::post('/update', 'UsuarioController@update');
			Route::get('/setTema', 'UsuarioController@setTema');
			Route::get('/historico/{id}', 'UsuarioController@historico');

		});
		Route::get('/401', function(){
			return view('401');
		});
		Route::get('/402', function(){
			return view('402');
		});
		Route::get('/403', function(){
			return view('403');
		});

		Route::group(['prefix' => 'categorias'],function(){
			Route::get('/', 'CategoryController@index');
			Route::get('/delete/{id}', 'CategoryController@delete');
			Route::get('/edit/{id}', 'CategoryController@edit');
			Route::get('/new', 'CategoryController@new');

			Route::post('/request', 'CategoryController@request');
			Route::post('/save', 'CategoryController@save');
			Route::post('/update', 'CategoryController@update');
			Route::post('/quickSave', 'CategoryController@quickSave');
		});

		Route::group(['prefix' => 'subcategorias'],function(){
			Route::get('/list/{categoria_id}', 'SubsCategoriaController@index');
			Route::get('/delete/{id}', 'SubsCategoriaController@delete');
			Route::get('/edit/{id}', 'SubsCategoriaController@edit');
			Route::get('/new/{categoria_id}', 'SubsCategoriaController@new');

			Route::post('/save', 'SubsCategoriaController@save');
			Route::post('/update', 'SubsCategoriaController@update');
			Route::post('/quickSave', 'SubsCategoriaController@quickSave');
		});

		Route::group(['prefix' => 'marcas'],function(){
			Route::get('/', 'MarcaController@index');
			Route::get('/delete/{id}', 'MarcaController@delete');
			Route::get('/edit/{id}', 'MarcaController@edit');
			Route::get('/new', 'MarcaController@new');

			Route::post('/save', 'MarcaController@save');
			Route::post('/update', 'MarcaController@update');
			Route::post('/quickSave', 'MarcaController@quickSave');
		});

		Route::group(['prefix' => 'gruposCliente'],function(){
			Route::get('/', 'GrupoClienteController@index');
			Route::get('/delete/{id}', 'GrupoClienteController@delete');
			Route::get('/edit/{id}', 'GrupoClienteController@edit');
			Route::get('/list/{id}', 'GrupoClienteController@list');
			Route::get('/new', 'GrupoClienteController@new');

			Route::post('/save', 'GrupoClienteController@save');
			Route::post('/update', 'GrupoClienteController@update');
		});

		Route::group(['prefix' => 'acessores'],function(){
			Route::get('/', 'AcessorController@index');
			Route::get('/delete/{id}', 'AcessorController@delete');
			Route::get('/edit/{id}', 'AcessorController@edit');
			Route::get('/list/{id}', 'AcessorController@list');
			Route::get('/new', 'AcessorController@new');

			Route::post('/save', 'AcessorController@save');
			Route::post('/update', 'AcessorController@update');
		});

		Route::group(['prefix' => 'divisaoGrade'],function(){
			Route::get('/', 'DivisaoGradeController@index');
			Route::get('/delete/{id}', 'DivisaoGradeController@delete');
			Route::get('/edit/{id}', 'DivisaoGradeController@edit');
			Route::get('/new', 'DivisaoGradeController@new');

			Route::post('/save', 'DivisaoGradeController@save');
			Route::post('/update', 'DivisaoGradeController@update');
		});

		Route::group(['prefix' => 'contaBancaria'],function(){
			Route::get('/', 'ContaBancariaController@index');
			Route::get('/delete/{id}', 'ContaBancariaController@delete');
			Route::get('/edit/{id}', 'ContaBancariaController@edit');
			Route::get('/new', 'ContaBancariaController@new');

			Route::get('/find/{id}', 'ContaBancariaController@find');


			Route::post('/save', 'ContaBancariaController@save');
			Route::post('/update', 'ContaBancariaController@update');
		});

		Route::group(['prefix' => 'naturezaOperacao'],function(){
			Route::get('/', 'NaturezaOperacaoController@index');
			Route::get('/delete/{id}', 'NaturezaOperacaoController@delete');
			Route::get('/edit/{id}', 'NaturezaOperacaoController@edit');
			Route::get('/new', 'NaturezaOperacaoController@new');

			Route::post('/request', 'NaturezaOperacaoController@request');
			Route::post('/save', 'NaturezaOperacaoController@save');
			Route::post('/update', 'NaturezaOperacaoController@update');
		});

		Route::group(['prefix' => 'categoriasServico'],function(){
			Route::get('/', 'CategoriaServicoController@index');
			Route::get('/delete/{id}', 'CategoriaServicoController@delete');
			Route::get('/edit/{id}', 'CategoriaServicoController@edit');
			Route::get('/new', 'CategoriaServicoController@new');

			Route::post('/request', 'CategoriaServicoController@request');
			Route::post('/save', 'CategoriaServicoController@save');
			Route::post('/update', 'CategoriaServicoController@update');
			Route::post('/update', 'CategoriaServicoController@update');
		});

		Route::group(['prefix' => 'categoriasConta'],function(){
			Route::get('/', 'CategoriaContaController@index');
			Route::get('/delete/{id}', 'CategoriaContaController@delete');
			Route::get('/edit/{id}', 'CategoriaContaController@edit');
			Route::get('/new', 'CategoriaContaController@new');

			Route::post('/request', 'CategoriaContaController@request');
			Route::post('/save', 'CategoriaContaController@save');
			Route::post('/update', 'CategoriaContaController@update');
		});


		Route::group(['prefix' => 'contasPagar'],function(){
			Route::post('/salvarParcela', 'ContasPagarController@salvarParcela');
			Route::get('/', 'ContasPagarController@index');
			Route::get('/filtro', 'ContasPagarController@filtro');
			Route::get('/new', 'ContasPagarController@new');
			Route::get('/edit/{id}', 'ContasPagarController@edit');
			Route::get('/delete/{id}', 'ContasPagarController@delete');
			Route::get('/pagar/{id}', 'ContasPagarController@pagar');

			Route::post('/save', 'ContasPagarController@save');
			Route::post('/update', 'ContasPagarController@update');
			Route::post('/pagar', 'ContasPagarController@pagarConta');
			Route::post('/relatorio', 'ContasPagarController@relatorio');
		});

		Route::group(['prefix' => 'contasReceber'],function(){
			Route::post('/salvarParcela', 'ContaReceberController@salvarParcela');
			Route::get('/', 'ContaReceberController@index');
			Route::get('/filtro', 'ContaReceberController@filtro');
			Route::get('/new', 'ContaReceberController@new');
			Route::get('/edit/{id}', 'ContaReceberController@edit');
			Route::get('/delete/{id}', 'ContaReceberController@delete');
			Route::get('/receber/{id}', 'ContaReceberController@receber');

			Route::post('/save', 'ContaReceberController@save');
			Route::post('/update', 'ContaReceberController@update');
			Route::post('/receber', 'ContaReceberController@receberConta');
			Route::post('/relatorio', 'ContaReceberController@relatorio');

			Route::post('/receberSomente', 'ContaReceberController@receberSomente');
			Route::post('/receberComDivergencia', 'ContaReceberController@receberComDivergencia');
			Route::post('/receberComOutros', 'ContaReceberController@receberComOutros');
			Route::get('/detalhes_venda/{conta_id}', 
				'ContaReceberController@detalhesVenda');
		});

		Route::group(['prefix' => 'produtos'],function(){
			Route::get('/', 'ProductController@index');
			Route::get('/delete/{id}', 'ProductController@delete');
			Route::get('/edit/{id}', 'ProductController@edit');
			Route::get('/editGrade/{id}', 'ProductController@editGrade');
			Route::get('/new', 'ProductController@new')->middleware('limiteProdutos');
			Route::get('/all', 'ProductController@all');
			Route::get('/composto', 'ProductController@composto');
			Route::get('/naoComposto', 'ProductController@naoComposto');
			Route::get('/getProduto/{id}', 'ProductController@getProduto');
			Route::get('/getProdutoCodigoReferencia/{codigo}', 'ProductController@getProdutoCodigoReferencia');

			Route::get('/getProdutoVenda/{id}/{lista_id}', 'ProductController@getProdutoVenda');

			Route::get('/getProdutoCodBarras/{id}', 'ProductController@getProdutoCodBarras');
			Route::get('/receita/{id}', 'ProductController@receita');
			Route::get('/duplicar/{id}', 'ProductController@duplicar');
			
			Route::get('/pesquisa', 'ProductController@pesquisa');
			Route::get('/filtroCategoria', 'ProductController@filtroCategoria');
			Route::get('/getUnidadesMedida', 'ProductController@getUnidadesMedida');

			Route::post('/request', 'ProductController@request');
			Route::post('/save', 'ProductController@save');
			Route::post('/update', 'ProductController@update');
			Route::post('/getValue', 'ProductController@getValue');
			Route::post('/salvarProdutoDaNota', 'ProductController@salvarProdutoDaNota');
			Route::post('/salvarProdutoDaNotaComEstoque', 'ProductController@salvarProdutoDaNotaComEstoque');
			Route::post('/setEstoque', 'ProductController@setEstoque');

			Route::get('/movimentacao/{id}', 'ProductController@movimentacao');
			Route::get('/movimentacaoImprimir/{id}', 'ProductController@movimentacaoImprimir');
			Route::post('/relatorio', 'ProductController@relatorio');

			Route::get('/importacao', 'ProductController@importacao');
			Route::get('/downloadModelo', 'ProductController@downloadModelo');
			Route::post('/importacao', 'ProductController@importacaoStore');

			Route::get('/grade/{id}', 'ProductController@grade');
			Route::post('/quickSave', 'ProductController@quickSave');
			Route::post('/atualizarGradeCompleta', 'ProductController@atualizarGradeCompleta');

			Route::get('/autocomplete', 'ProductController@autocomplete');
			Route::get('/autocompleteProduto', 'ProductController@autocompleteProduto');
			Route::get('/gerarCodigoEan', 'ProductController@gerarCodigoEan');
			Route::get('/etiqueta/{id}', 'ProductController@etiqueta');
			Route::post('/etiquetaStore', 'ProductController@etiquetaStore');
		});

		Route::group(['prefix' => 'receita'],function(){
			Route::post('/save', 'ReceitaController@save');
			Route::post('/update', 'ReceitaController@update');
			Route::post('/saveItem', 'ReceitaController@saveItem');
			Route::get('/deleteItem/{id}', 'ReceitaController@deleteItem');

		});

		Route::group(['prefix' => 'vendasEmCredito'],function(){
			Route::get('/', 'CreditoVendaController@index');
			Route::get('/receber', 'CreditoVendaController@receber');
			Route::get('/receber', 'CreditoVendaController@receber');
			Route::get('/delete/{id}', 'CreditoVendaController@delete');
			Route::get('/somaVendas/{cliente_id}', 'CreditoVendaController@somaVendas');

			Route::get('/emitirNFe', 'CreditoVendaController@emitirNFe');
			Route::get('/filtro', 'CreditoVendaController@filtro');
			Route::get('/apenasReceber', 'CreditoVendaController@apenasReceber');

		});

		Route::group(['prefix' => 'vendasCaixa'],function(){
			Route::post('/save', 'VendaCaixaController@save');
			Route::get('/diaria', 'VendaCaixaController@diaria');
			Route::get('/calcComissao', 'VendaCaixaController@calcComissao');
			
		});

		Route::group(['prefix' => 'tributos'], function(){
			Route::get('/', 'TributoController@index');
			Route::post('/save', 'TributoController@save');
		});

		Route::group(['prefix' => 'funcionamentoDelivery'], function(){
			Route::get('/', 'FuncionamentoDeliveryController@index');
			Route::post('/save', 'FuncionamentoDeliveryController@save');
			Route::get('/edit/{id}', 'FuncionamentoDeliveryController@edit');
			Route::get('/alterarStatus/{id}', 'FuncionamentoDeliveryController@alterarStatus');

		});

		Route::group(['prefix' => 'enviarXml'],function(){
			Route::get('/', 'EnviarXmlController@index');
			Route::get('/filtro', 'EnviarXmlController@filtro');
			Route::get('/download', 'EnviarXmlController@download');
			Route::get('/downloadNfce', 'EnviarXmlController@downloadNfce');
			Route::get('/downloadCte', 'EnviarXmlController@downloadCte');
			Route::get('/downloadMdfe', 'EnviarXmlController@downloadMdfe');
			Route::get('/downloadEntrada', 'EnviarXmlController@downloadEntrada');
			Route::get('/downloadDevolucao', 'EnviarXmlController@downloadDevolucao');
			Route::get('/email/{d1}/{d2}', 'EnviarXmlController@email');
			Route::get('/emailNfce/{d1}/{d2}', 'EnviarXmlController@emailNfce');
			Route::get('/emailCte/{d1}/{d2}', 'EnviarXmlController@emailCte');
			Route::get('/emailMdfe/{d1}/{d2}', 'EnviarXmlController@emailMdfe');
			Route::get('/emailEntrada/{d1}/{d2}', 'EnviarXmlController@emailEntrada');
			Route::get('/emailDevolucao/{d1}/{d2}', 'EnviarXmlController@emailDevolucao');
			Route::get('/send', 'EnviarXmlController@send');
			
			Route::get('/filtroCfop', 'EnviarXmlController@filtroCfop');
			Route::get('/filtroCfopGet', 'EnviarXmlController@filtroCfopGet');
			Route::get('/filtroCfopImprimir', 'EnviarXmlController@filtroCfopImprimir');
			Route::get('/filtroCfopImprimirGroup', 'EnviarXmlController@filtroCfopImprimirGroup');
		});

		Route::group(['prefix' => 'nf'],function(){
			Route::post('/gerarNf', 'NotaFiscalController@gerarNf')->middleware('limiteNFe');
			Route::get('/xmlTemp/{id}', 'NotaFiscalController@xmlTemp');
			Route::get('/gerarNf/{id}', 'NotaFiscalController@testeGerar');
			Route::get('/imprimir/{id}', 'NotaFiscalController@imprimir');
			Route::get('/imprimirSimples/{id}', 'NotaFiscalController@imprimirSimples');
			Route::get('/escpos/{id}', 'NotaFiscalController@escpos');
			Route::get('/imprimirCce/{id}', 'NotaFiscalController@imprimirCce');
			Route::get('/imprimirCancela/{id}', 'NotaFiscalController@imprimirCancela');
			Route::get('/consultar_cliente/{id}', 'NotaFiscalController@consultar_cliente');
			Route::post('/cancelar', 'NotaFiscalController@cancelar');
			Route::post('/consultar', 'NotaFiscalController@consultar');
			Route::post('/cartaCorrecao', 'NotaFiscalController@cartaCorrecao');
			Route::get('/teste', 'NotaFiscalController@teste');
			Route::get('/consultaCadastro', 'NotaFiscalController@consultaCadastro');
			Route::post('/inutilizar', 'NotaFiscalController@inutilizar');
			Route::get('/certificado', 'NotaFiscalController@certificado');
			Route::get('/enviarXml', 'NotaFiscalController@enviarXml');

		});

		Route::group(['prefix' => 'cte'],function(){
			Route::get('/', 'CteController@index');
			Route::get('/nova', 'CteController@nova');
			Route::get('/lista', 'CteController@lista');
			
			Route::get('/detalhar/{id}', 'CteController@detalhar');
			Route::get('/edit/{id}', 'CteController@edit');

			Route::get('/delete/{id}', 'CteController@delete');
			Route::post('/salvar', 'CteController@salvar');
			Route::post('/update', 'CteController@update');
			Route::get('/filtro', 'CteController@filtro');
			Route::get('/custos/{id}', 'CteController@custos');
			Route::post('/saveReceita', 'CteController@saveReceita');
			Route::post('/saveDespesa', 'CteController@saveDespesa');
			Route::post('/importarXml', 'CteController@importarXml');

			Route::get('/deleteReceita/{id}', 'CteController@deleteReceita');
			Route::get('/deleteDespesa/{id}', 'CteController@deleteDespesa');

			Route::get('/consultaChave', 'EmiteCteController@consultaChave');
			Route::get('/chaveNfeDuplicada', 'CteController@chaveNfeDuplicada');

		});

		Route::group(['prefix' => 'cteSefaz'],function(){
			Route::post('/enviar', 'EmiteCteController@enviar');
			Route::get('/imprimir/{id}', 'EmiteCteController@imprimir');
			Route::get('/imprimirCCe/{id}', 'EmiteCteController@imprimirCCe');
			Route::get('/imprimirCancela/{id}', 'EmiteCteController@imprimirCancela');
			Route::post('/cancelar', 'EmiteCteController@cancelar');
			Route::post('/consultar', 'EmiteCteController@consultar');
			Route::post('/inutilizar', 'EmiteCteController@inutilizar');
			Route::post('/cartaCorrecao', 'EmiteCteController@cartaCorrecao');
			Route::get('/teste/{id}', 'EmiteCteController@teste');
			Route::get('/enviarXml', 'EmiteCteController@enviarXml');
			Route::get('/baixarXml/{id}', 'EmiteCteController@baixarXml');
			Route::get('/xmlTemp/{id}', 'EmiteCteController@xmlTemp');

		});


		Route::group(['prefix' => 'mdfe'],function(){
			Route::get('/', 'MdfeController@index');
			Route::get('/nova', 'MdfeController@nova');
			Route::get('/lista', 'MdfeController@lista');
			Route::get('/detalhar/{id}', 'MdfeController@detalhar');
			Route::get('/delete/{id}', 'MdfeController@delete');
			Route::get('/edit/{id}', 'MdfeController@edit');
			Route::post('/salvar', 'MdfeController@salvar');
			Route::post('/update', 'MdfeController@update');
			Route::get('/filtro', 'MdfeController@filtro');

		});

		Route::group(['prefix' => 'mdfeSefaz'],function(){
			Route::post('/enviar', 'EmiteMdfeController@enviar')->middleware('limiteMDFe');
			Route::get('/imprimir/{id}', 'EmiteMdfeController@imprimir');
			Route::get('/baixarXml/{id}', 'EmiteMdfeController@baixarXml');
			Route::post('/cancelar', 'EmiteMdfeController@cancelar');
			Route::post('/consultar', 'EmiteMdfeController@consultar');

			Route::get('/naoEncerrados', 'EmiteMdfeController@naoEncerrados');
			Route::post('/encerrar', 'EmiteMdfeController@encerrar');
			Route::get('/enviarXml', 'EmiteMdfeController@enviarXml');
			Route::get('/xmlTemp/{id}', 'EmiteMdfeController@xmlTemp');
		// Route::get('/imprimirCancela/{id}', 'EmiteMdfeController@imprimirCancela');
		});

		Route::group(['prefix' => 'nfce'],function(){
			Route::post('/gerar', 'NFCeController@gerar')->middleware('limiteNFCe');
			Route::get('/xmlTemp/{id}', 'NFCeController@xmlTemp');
			Route::get('/imprimir/{id}', 'NFCeController@imprimir');
			Route::get('/imprimirNaoFiscal/{id}', 'NFCeController@imprimirNaoFiscal');
			Route::get('/imprimirNaoFiscalCredito/{id}', 'NFCeController@imprimirNaoFiscalCredito');
			Route::post('/cancelar', 'NFCeController@cancelar');
			Route::get('/deleteVenda/{id}', 'NFCeController@deleteVenda');
			Route::get('/consultar/{id}', 'NFCeController@consultar');
			Route::get('/baixarXml/{id}', 'NFCeController@baixarXml');
			Route::get('/detalhes/{id}', 'NFCeController@detalhes');
			Route::get('/estadoFiscal/{id}', 'NFCeController@estadoFiscal');
			Route::post('/estadoFiscal', 'NFCeController@estadoFiscalStore');

	// Route::post('/consultar', 'NotaFiscalController@consultar');
			Route::get('/teste', 'NFCeController@teste');
			Route::post('/inutilizar', 'NFCeController@inutilizar');
			
		});

		Route::group(['prefix' => 'clientes'],function(){
			Route::get('/', 'ClienteController@index');
			Route::get('/delete/{id}', 'ClienteController@delete');
			Route::get('/edit/{id}', 'ClienteController@edit');
			Route::get('/new', 'ClienteController@new')->middleware('limiteClientes');
			Route::get('/all', 'ClienteController@all');
			Route::get('/verificaLimite', 'ClienteController@verificaLimite');
			Route::get('/find/{id}', 'ClienteController@find');
			Route::get('/pesquisa', 'ClienteController@pesquisa');

			Route::post('/request', 'ClienteController@request');
			Route::post('/quickSave', 'ClienteController@quickSave');
			Route::post('/save', 'ClienteController@save');
			Route::post('/update', 'ClienteController@update');
			Route::get('/cpfCnpjDuplicado', 'ClienteController@cpfCnpjDuplicado');

			Route::get('/importacao', 'ClienteController@importacao');
			Route::get('/downloadModelo', 'ClienteController@downloadModelo');
			Route::post('/importacao', 'ClienteController@importacaoStore');
			Route::post('/relatorio', 'ClienteController@relatorio');
			Route::get('/consultaCadastrado/{doc}', 'ClienteController@consultaCadastrado');

		});

		Route::group(['prefix' => 'clientesDelivery'],function(){
			Route::get('/', 'ClienteDeliveryController@index');
			Route::get('/edit/{id}', 'ClienteDeliveryController@edit');
			Route::get('/delete/{id}', 'ClienteDeliveryController@delete');
			Route::get('/all', 'ClienteDeliveryController@all');
			Route::post('/update', 'ClienteDeliveryController@update');


			Route::get('/pedidos/{id}', 'ClienteDeliveryController@pedidos');
			Route::get('/enderecos/{id}', 'ClienteDeliveryController@enderecos');
			Route::get('/enderecosEdit/{id}', 'ClienteDeliveryController@enderecoEdit');
			Route::get('/enderecosMap/{id}', 'ClienteDeliveryController@enderecosMap');
			Route::get('/favoritos/{id}', 'ClienteDeliveryController@favoritos');
			Route::get('/push/{id}', 'ClienteDeliveryController@push');
			Route::post('/updateEndereco', 'ClienteDeliveryController@updateEndereco');

			Route::get('/pesquisa', 'ClienteDeliveryController@pesquisa');
		});


		Route::group(['prefix' => 'transportadoras'],function(){
			Route::get('/', 'TransportadoraController@index');
			Route::get('/delete/{id}', 'TransportadoraController@delete');
			Route::get('/edit/{id}', 'TransportadoraController@edit');
			Route::get('/new', 'TransportadoraController@new');
			Route::get('/all', 'TransportadoraController@all');
			Route::get('/find/{id}', 'TransportadoraController@find');

			Route::post('/save', 'TransportadoraController@save');
			Route::post('/update', 'TransportadoraController@update');
		});

		Route::group(['prefix' => 'fornecedores'],function(){
			Route::get('/', 'ProviderController@index');
			Route::get('/delete/{id}', 'ProviderController@delete');
			Route::get('/edit/{id}', 'ProviderController@edit');
			Route::get('/new', 'ProviderController@new')->middleware('limiteFornecedor');
			Route::get('/all', 'ProviderController@all');
			Route::get('/find/{id}', 'ProviderController@find');

			Route::post('/request', 'ProviderController@request');
			Route::post('/save', 'ProviderController@save');
			Route::post('/update', 'ProviderController@update');
			Route::get('/consultaCadastrado/{doc}', 'ProviderController@consultaCadastrado');
			
		});

		Route::group(['prefix' => 'compraFiscal', 'middleware' => ['limiteProdutos', 'limiteClientes']],function(){
			Route::get('/', 'CompraFiscalController@index');
			Route::post('/new', 'CompraFiscalController@new');
			Route::post('/salvarNfFiscal', 'CompraFiscalController@salvarNfFiscal');
			Route::post('/salvarItem', 'CompraFiscalController@salvarItem');
			Route::get('/read', 'CompraFiscalController@read');
			Route::get('/teste', 'CompraFiscalController@teste');
		});

		Route::group(['prefix' => 'compraManual'],function(){
			Route::get('/', 'CompraManualController@index');
			Route::post('/salvar', 'CompraManualController@salvar');
			Route::post('/salvarNfFiscal', 'CompraManualController@salvarNfFiscal');
			Route::post('/salvarItem', 'CompraManualController@salvarItem');
			Route::get('/read', 'CompraManualController@read');

			Route::get('/ultimaCompra/{produtoId}', 'CompraManualController@ultimaCompra');
		});

		Route::group(['prefix' => 'funcionarios'],function(){
			Route::get('/', 'FuncionarioController@index');
			Route::get('/delete/{id}', 'FuncionarioController@delete');
			Route::get('/edit/{id}', 'FuncionarioController@edit');
			Route::get('/new', 'FuncionarioController@new');
			Route::get('/all', 'FuncionarioController@all');
			Route::get('/contatos/{id}', 'FuncionarioController@contatos');
			Route::get('/editContato/{id}', 'FuncionarioController@editContato');
			Route::get('/deleteContato/{id}', 'FuncionarioController@deleteContato');
			Route::post('/saveContato', 'FuncionarioController@saveContato');
			Route::post('/updateContato', 'FuncionarioController@saveContato');

			Route::post('/request', 'FuncionarioController@request');
			Route::post('/save', 'FuncionarioController@save');
			Route::post('/update', 'FuncionarioController@update');

			Route::get('/comissao', 'FuncionarioController@comissao');
			Route::get('/pagarComissao', 'FuncionarioController@pagarComissao');
			Route::get('/comissaoFiltro', 'FuncionarioController@comissaoFiltro');
		});

		Route::group(['prefix' => 'contatoFuncionario'],function(){
			Route::get('/{funcionaId}', 'FuncionarioController@index');
			Route::get('/delete/{id}', 'FuncionarioController@delete');
			Route::get('/edit/{id}', 'FuncionarioController@edit');
			Route::get('/new/{funcionarioId}', 'FuncionarioController@new');
			Route::post('/save', 'FuncionarioController@save');
			Route::post('/update', 'FuncionarioController@update');
		});

		Route::group(['prefix' => 'servicos'],function(){
			Route::get('/', 'ServiceController@index');
			Route::get('/delete/{id}', 'ServiceController@delete');
			Route::get('/edit/{id}', 'ServiceController@edit');
			Route::get('/new', 'ServiceController@new');
			Route::get('/all', 'ServiceController@all');

			Route::post('/request', 'ServiceController@request');
			Route::post('/save', 'ServiceController@save');
			Route::post('/update', 'ServiceController@update');
			Route::get('/pesquisa', 'ServiceController@pesquisa');
			Route::post('/getValue', 'ServiceController@getValue');
		});

		Route::group(['prefix' => 'orcamento'],function(){
			Route::get('/', 'BudgetController@index');
			Route::get('/delete/{id}', 'BudgetController@delete');
			Route::get('/new', 'BudgetController@new');

			Route::get('/searchClient', 'BudgetController@searchClient');
			Route::get('/searchDate', 'BudgetController@searchDate');

			Route::get('/os/{id}', 'BudgetController@os');
			Route::post('/save', 'BudgetController@save');
		});

		Route::group(['prefix' => 'ordemServico'],function(){
			Route::get('/', 'OrderController@index');
			Route::get('/new', 'OrderController@new');
			Route::get('/servicosordem/{id}', 'OrderController@servicosordem');
			Route::get('/deleteServico/{id}', 'OrderController@deleteServico');
			Route::get('/addRelatorio/{id}', 'OrderController@addRelatorio');
			Route::get('/editRelatorio/{id}', 'OrderController@editRelatorio');
			Route::get('/deleteRelatorio/{id}', 'OrderController@deleteRelatorio');
			Route::get('/alterarEstado/{id}', 'OrderController@alterarEstado');
			Route::post('/alterarEstado', 'OrderController@alterarEstadoPost');
			Route::get('/filtro', 'OrderController@filtro');

			Route::post('/addRelatorio', 'OrderController@saveRelatorio');
			Route::post('/updateRelatorio', 'OrderController@updateRelatorio');
			Route::get('/cashFlowFilter', 'OrderController@cashFlowFilter');
			Route::post('/save', 'OrderController@save');
			Route::post('/addServico', 'OrderController@addServico');
			Route::post('/find', 'OrderController@find');

			Route::get('/print/{id}', 'OrderController@print');

			Route::get('/deleteFuncionario/{id}', 'OrderController@deleteFuncionario');
			Route::post('/saveFuncionario', 'OrderController@saveFuncionario');

			Route::get('/alterarStatusServico/{id}', 'OrderController@alterarStatusServico');
			Route::get('/imprimir/{id}', 'OrderController@imprimir');
			Route::get('/delete/{id}', 'OrderController@delete');

		});

		Route::group(['prefix' => 'semRegistro'],function(){
			Route::get('/', 'ApplianceNotFounController@index');
			Route::get('/delete/{id}', 'ApplianceNotFounController@delete');
		});


		Route::group(['prefix' => 'fluxoCaixa'],function(){
			Route::get('/', 'FluxoCaixaController@index');
			Route::get('/filtro', 'FluxoCaixaController@filtro');
			Route::get('/relatorioIndex', 'FluxoCaixaController@relatorioIndex');
			Route::get('/relatorioFiltro/{data1}/{data2}', 'FluxoCaixaController@relatorioFiltro');
		});

		Route::group(['prefix' => 'orcamentoCliente'],function(){
			Route::get('/', 'ClientTempController@index');
			Route::get('/delete/{id}', 'ClientTempController@delete');
		});

		Route::group(['prefix' => 'vendas'],function(){
			Route::get('/', 'VendaController@index');
			Route::get('/nova', 'VendaController@nova');
		// Route::get('/lista', 'VendaController@lista');
			Route::get('/detalhar/{id}', 'VendaController@detalhar');
			Route::get('/delete/{id}', 'VendaController@delete');
			Route::get('/edit/{id}', 'VendaController@edit');
			Route::post('/salvar', 'VendaController@salvar');
			Route::post('/atualizar', 'VendaController@atualizar');
			Route::post('/salvarCrediario', 'VendaController@salvarCrediario');
			Route::get('/filtro', 'VendaController@filtro');
			Route::get('/rederizarDanfe/{id}', 'VendaController@rederizarDanfe');
			Route::get('/baixarXml/{id}', 'VendaController@baixarXml');
			Route::get('/imprimirPedido/{id}', 'VendaController@imprimirPedido');
			Route::get('/clone/{id}', 'VendaController@clone');
			Route::get('/gerarXml/{id}', 'VendaController@gerarXml');
			Route::post('/clone', 'VendaController@salvarClone');

			Route::get('/calculaFrete', 'VendaController@calculaFrete');
			Route::get('/importacao', 'VendaController@importacao');
			Route::post('/importacao', 'VendaController@importacaoStore');
			Route::post('/importStore', 'VendaController@importStore');
			Route::get('/estadoFiscal/{id}', 'VendaController@estadoFiscal');
			Route::post('/estadoFiscal/', 'VendaController@estadoFiscalStore');
			Route::get('/carne', 'CarneController@index');
			Route::get('/calcComissao', 'VendaController@calcComissao');

		});

		Route::group(['prefix' => 'compras'],function(){
			Route::get('/', 'PurchaseController@index');
			Route::get('/filtro', 'PurchaseController@filtro');
			Route::get('/view/{id}', 'PurchaseController@view');
			Route::get('/delete/{id}', 'PurchaseController@delete');
			Route::get('/detalhes/{id}', 'PurchaseController@detalhes');
			Route::get('/pesquisa', 'PurchaseController@pesquisa');
			Route::get('/downloadXml/{id}', 'PurchaseController@downloadXml');
			Route::get('/downloadXmlCancela/{id}', 'PurchaseController@downloadXmlCancela');
			Route::post('/save', 'PurchaseController@save');

			Route::get('/emitirEntrada/{id}', 'PurchaseController@emitirEntrada');
			Route::get('/danfeTemporaria', 'PurchaseController@danfeTemporaria');
			Route::get('/xmlTemporaria', 'PurchaseController@xmlTemporaria');
			Route::post('/gerarEntrada', 'PurchaseController@gerarEntrada');
			Route::post('/cancelarEntrada', 'PurchaseController@cancelarEntrada');
			Route::post('/consultar', 'PurchaseController@consultar');

			Route::get('/imprimir/{id}', 'PurchaseController@imprimir');

			Route::get('/produtosSemValidade', 'PurchaseController@produtosSemValidade');
			Route::post('/salvarValidade', 'PurchaseController@salvarValidade');
			Route::get('/validadeAlerta', 'PurchaseController@validadeAlerta');


		});

		Route::group(['prefix' => 'estoque'],function(){
			Route::get('/', 'StockController@index');
			Route::get('/pesquisa', 'StockController@pesquisa');
			Route::get('/view/{id}', 'StockController@view');
			Route::get('/deleteApontamento/{id}', 'StockController@deleteApontamento');
			Route::get('/apontamentoProducao', 'StockController@apontamento');
			Route::get('/todosApontamentos', 'StockController@todosApontamentos');
			Route::get('/apontamentoManual', 'StockController@apontamentoManual');
			Route::get('/filtroApontamentos', 'StockController@filtroApontamentos');
			Route::post('/saveApontamento', 'StockController@saveApontamento');
			Route::post('/saveApontamentoManual', 'StockController@saveApontamentoManual');
			Route::get('/listApontamentos', 'StockController@listApontamentos');
			Route::get('/listApontamentos/delete/{id}', 'StockController@listApontamentosDelte');

			Route::get('/add1', 'StockController@add1');
		});

		Route::group(['prefix' => 'cotacao'],function(){
			Route::get('/', 'CotacaoController@index');
			Route::get('/new', 'CotacaoController@new');
			Route::post('/salvar', 'CotacaoController@salvar');

			Route::get('/deleteItem/{id}', 'CotacaoController@deleteItem');
			Route::get('/delete/{id}', 'CotacaoController@delete');
			Route::get('/edit/{id}', 'CotacaoController@edit');
			Route::get('/alterarStatus/{id}/{status}', 'CotacaoController@alterarStatus');
			Route::post('/saveItem', 'CotacaoController@saveItem');

			Route::get('/view/{id}', 'CotacaoController@view');
			Route::get('/clonar/{id}', 'CotacaoController@clonar');
			Route::post('/clonarSave', 'CotacaoController@clonarSave');


			Route::get('/response/{code}', 'CotacaoController@response');
			Route::get('/filtro', 'CotacaoController@filtro');


			Route::get('/searchProvider', 'CotacaoController@searchProvider');
			Route::get('/searchPiece', 'CotacaoController@searchPiece');


			Route::get('/sendMail/{id}', 'CotacaoController@sendMail');
			Route::get('/listaPorReferencia', 'CotacaoController@listaPorReferencia');
			Route::get('/listaPorReferencia/filtro', 'CotacaoController@listaPorReferenciaFiltro');
			Route::get('/referenciaView/{referencia}', 'CotacaoController@referenciaView');
			Route::get('/escolher/{id}', 'CotacaoController@escolher');
			Route::get('/imprimirMelhorResultado', 'CotacaoController@imprimirMelhorResultado');
		});

		Route::group(['prefix' => 'frenteCaixa'],function(){
			Route::get('/', 'FrontBoxController@index');
			Route::get('/list', 'FrontBoxController@list');
			Route::get('/devolucao', 'FrontBoxController@devolucao');
			Route::get('/filtro', 'FrontBoxController@filtro');

			Route::get('/filtroCliente', 'FrontBoxController@filtroCliente');
			Route::get('/filtroNFCe', 'FrontBoxController@filtroNFCe');
			Route::get('/filtroValor', 'FrontBoxController@filtroValor');
			Route::get('/filtroData', 'FrontBoxController@filtroData');
			Route::get('/fechar', 'FrontBoxController@fechar');
			Route::post('/fechar', 'FrontBoxController@fecharPost');
			Route::get('/fechamentos', 'FrontBoxController@fechamentos');
			Route::get('/listaFechamento/{id}', 'FrontBoxController@listaFechamento');

			Route::get('/deleteVenda/{id}', 'FrontBoxController@deleteVenda');
			Route::get('/config', 'FrontBoxController@config');
			Route::post('/configSave', 'FrontBoxController@configSave');

		});


		Route::get('/ola', function() {
			return view('default/ola')->with('title', 'Bem vindo ao teste do SlymPDV');
		});

		Route::group(['prefix' => 'clienteDelivery'],function(){
			Route::get('/all', 'AppUserController@all');

		});

		Route::group(['prefix' => 'push'],function(){
			Route::get('/', 'PushController@index');
			Route::get('/new', 'PushController@new');
			Route::post('/save', 'PushController@save');
			Route::post('/update', 'PushController@update');

			Route::get('/send/{id}', 'PushController@send');
			Route::get('/edit/{id}', 'PushController@edit');
			Route::get('/delete/{id}', 'PushController@delete');

		});

		Route::group(['prefix' => 'codigoDesconto'],function(){
			Route::get('/', 'CodigoDescontoController@index');
			Route::get('/new', 'CodigoDescontoController@new');
			Route::post('/save', 'CodigoDescontoController@save');
			Route::post('/update', 'CodigoDescontoController@update');
			Route::get('/edit/{id}', 'CodigoDescontoController@edit');

			Route::get('/delete/{id}', 'CodigoDescontoController@delete');
			Route::get('/push/{id}', 'CodigoDescontoController@push');
			Route::post('/push', 'CodigoDescontoController@savePush');
			Route::get('/sms/{id}', 'CodigoDescontoController@sms');
			Route::post('/sms', 'CodigoDescontoController@saveSms');
			Route::get('/alterarStatus/{id}', 'CodigoDescontoController@alterarStatus');
		});

		Route::group(['prefix' => 'tamanhosPizza'],function(){
			Route::get('/', 'TamanhoPizzaController@index');
			Route::get('/new', 'TamanhoPizzaController@new');
			Route::post('/save', 'TamanhoPizzaController@save');
			Route::post('/update', 'TamanhoPizzaController@update');
			Route::get('/edit/{id}', 'TamanhoPizzaController@edit');

			Route::get('/delete/{id}', 'TamanhoPizzaController@delete');

		});

		Route::group(['prefix' => 'categoriaDespesa'],function(){
			Route::get('/', 'CategoriaDespesaController@index');
			Route::get('/new', 'CategoriaDespesaController@new');
			Route::post('/save', 'CategoriaDespesaController@save');
			Route::post('/update', 'CategoriaDespesaController@update');
			Route::get('/edit/{id}', 'CategoriaDespesaController@edit');

			Route::get('/delete/{id}', 'CategoriaDespesaController@delete');

		});

		Route::group(['prefix' => 'veiculos'],function(){
			Route::get('/', 'VeiculoController@index');
			Route::get('/new', 'VeiculoController@new');
			Route::post('/save', 'VeiculoController@save');
			Route::post('/update', 'VeiculoController@update');
			Route::get('/edit/{id}', 'VeiculoController@edit');
			Route::get('/delete/{id}', 'VeiculoController@delete');
		});

		Route::group(['prefix' => 'devolucao'],function(){
			Route::get('/', 'DevolucaoController@index');
			Route::get('/nova', 'DevolucaoController@new');
			Route::post('/new', 'DevolucaoController@renderizarXml');
			Route::post('/salvar', 'DevolucaoController@salvar');
			Route::post('/enviarSefaz', 'DevolucaoController@enviarSefaz');
			Route::post('/cancelar', 'DevolucaoController@cancelar');
			Route::get('/ver/{id}', 'DevolucaoController@ver');
			Route::get('/delete/{id}', 'DevolucaoController@delete');
			Route::get('/imprimir/{id}', 'DevolucaoController@imprimir');
			Route::get('/downloadXmlEntrada/{id}', 'DevolucaoController@downloadXmlEntrada');
			Route::get('/downloadXmlDevolucao/{id}', 'DevolucaoController@downloadXmlDevolucao');
			Route::get('/filtro', 'DevolucaoController@filtro');
			Route::get('/xmltemp/{id}', 'DevolucaoController@xmltemp');
			Route::get('/danfeTemp/{id}', 'DevolucaoController@danfeTemp');

			Route::post('/consultar', 'DevolucaoController@consultar');
			
			Route::post('/cartaCorrecao', 'DevolucaoController@cartaCorrecao');
			Route::get('/imprimirCce/{id}', 'DevolucaoController@imprimirCce');
			Route::get('/imprimirCancela/{id}', 'DevolucaoController@imprimirCancela');
			
		});

		Route::group(['prefix' => 'controleCozinha'],function(){
			Route::get('/', 'CozinhaController@index');
			Route::get('/buscar', 'CozinhaController@buscar');
			Route::get('/concluido', 'CozinhaController@concluido');
		});

		Route::get('/graficos', 'HomeController@index');

		Route::group(['prefix' => 'graficos'],function(){
			Route::get('/faturamentoDosUltimosSeteDias', 'HomeController@faturamentoDosUltimosSeteDias');
			Route::get('/faturamentoFiltrado', 'HomeController@faturamentoFiltrado');

			Route::get('/boxConsulta/{dias}', 'HomeController@boxConsulta');

		});

		Route::group(['prefix' => 'bairrosDelivery'],function(){
			Route::get('/', 'BairroDeliveryController@index');
			Route::get('/delete/{id}', 'BairroDeliveryController@delete');
			Route::get('/edit/{id}', 'BairroDeliveryController@edit');
			Route::get('/new', 'BairroDeliveryController@new');

			Route::post('/request', 'BairroDeliveryController@request');
			Route::post('/save', 'BairroDeliveryController@save');
			Route::post('/update', 'BairroDeliveryController@update');
		});

		Route::group(['prefix' => 'cidadeDelivery'],function(){
			Route::get('/', 'CidadeDeliveryController@index');
			Route::get('/delete/{id}', 'CidadeDeliveryController@delete');
			Route::get('/edit/{id}', 'CidadeDeliveryController@edit');
			Route::get('/new', 'CidadeDeliveryController@new');

			Route::post('/request', 'CidadeDeliveryController@request');
			Route::post('/save', 'CidadeDeliveryController@save');
			Route::post('/update', 'CidadeDeliveryController@update');
		});

		Route::group(['prefix' => 'produtosDestaque'],function(){
			Route::get('/', 'DestaqueDeliveryMasterController@index');
			Route::get('/novoProduto', 'DestaqueDeliveryMasterController@novoProduto');
			Route::post('/save', 'DestaqueDeliveryMasterController@saveProduto');
		});

		Route::group(['prefix' => 'categoriasParaDestaque'],function(){
			Route::get('/', 'DestaqueDeliveryMasterController@listaCategoria');

			Route::get('/delete/{id}', 'DestaqueDeliveryMasterController@deleteCategoria');
			Route::get('/edit/{id}', 'DestaqueDeliveryMasterController@editCategoria');
			Route::get('/new', 'DestaqueDeliveryMasterController@newCategoria');

			Route::post('/save', 'DestaqueDeliveryMasterController@saveCategoria');
			Route::post('/update', 'DestaqueDeliveryMasterController@updateCategoria');
		});

		Route::group(['prefix' => 'categoriaMasterDelivery'],function(){
			Route::get('/', 'CategoriaMasterDeliveryController@index');
			Route::get('/delete/{id}', 'CategoriaMasterDeliveryController@delete');
			Route::get('/edit/{id}', 'CategoriaMasterDeliveryController@edit');
			Route::get('/new', 'CategoriaMasterDeliveryController@new');

			Route::post('/request', 'CategoriaMasterDeliveryController@request');
			Route::post('/save', 'CategoriaMasterDeliveryController@save');
			Route::post('/update', 'CategoriaMasterDeliveryController@update');
		});

		Route::group(['prefix' => 'mesas'],function(){
			Route::get('/', 'MesaController@index');
			Route::get('/delete/{id}', 'MesaController@delete');
			Route::get('/edit/{id}', 'MesaController@edit');
			Route::get('/new', 'MesaController@new');

			Route::post('/save', 'MesaController@save');
			Route::post('/update', 'MesaController@update');
			Route::get('/gerarQrCode', 'MesaController@gerarQrCode');
			Route::get('/issue/{id}', 'MesaController@issue');
			Route::get('/issue2/{id}', 'MesaController@issue2');
			Route::get('/imprimirQrCode/{id}', 'MesaController@imprimirQrCode');

		});

		Route::group(['prefix' => 'bannerTopo'],function(){
			Route::get('/', 'BannerTopoController@index');
			Route::get('/delete/{id}', 'BannerTopoController@delete');
			Route::get('/edit/{id}', 'BannerTopoController@edit');
			Route::get('/new', 'BannerTopoController@new');

			Route::post('/save', 'BannerTopoController@save');
			Route::post('/update', 'BannerTopoController@update');
		});

		Route::group(['prefix' => 'bannerMaisVendido'],function(){
			Route::get('/', 'BannerMaisVendidoController@index');
			Route::get('/delete/{id}', 'BannerMaisVendidoController@delete');
			Route::get('/edit/{id}', 'BannerMaisVendidoController@edit');
			Route::get('/new', 'BannerMaisVendidoController@new');

			Route::post('/save', 'BannerMaisVendidoController@save');
			Route::post('/update', 'BannerMaisVendidoController@update');
		});

		Route::group(['prefix' => 'delivery'], function(){

			Route::get('/', 'MercadoController@index');
			Route::get('/categorias', 'MercadoController@categorias');
			Route::get('/produto/{id}', 'MercadoController@produto');
			Route::get('/login', 'MercadoController@login');
			Route::get('/logoff', 'MercadoController@logoff');
			Route::post('/login', 'MercadoController@loginUser');
			Route::get('/cadastrar', 'MercadoController@cadastrar');
			Route::get('/produtos/{categoria_id}', 'MercadoController@produtos');
			Route::post('/salvarRegistro', 'MercadoController@salvarRegistro');
			Route::post('/validaToken', 'MercadoController@validaToken');
			Route::get('/carrinho', 'MercadoController@carrinho');
			Route::post('/finalizar', 'MercadoController@finalizar');
			Route::post('/finalizarPedido', 'MercadoController@finalizarPedido');
			Route::get('/finalizado/{id}', 'MercadoController@finalizado');
			Route::get('/pedidoPendente', 'MercadoController@pedidoPendente');
			Route::get('/meusPedidos', 'MercadoController@meusPedidos');
			Route::get('/detalhePedido/{id}', 'MercadoController@detalhePedido');
			Route::get('/pedir_novamente/{id}', 'MercadoController@pedir_novamente');
			Route::get('/pesquisaProduto', 'MercadoController@pesquisaProduto');

			Route::get('/esqueci-senha', 'MercadoController@recuperarSenha');
			Route::post('/esqueci-senha', 'MercadoController@enviarSenha');

		});

		Route::group(['prefix' => 'deliveryProduto'], function(){
			Route::post('/addProduto', 'MercadoProdutoController@addProduto');
			Route::get('/addProduto/{id}', 'MercadoProdutoController@adicionarProduto');
			Route::post('/downProduto', 'MercadoProdutoController@downProduto');
			Route::get('/novo_cliente', 'MercadoProdutoController@novoCliente');
			Route::get('/carrinho', 'MercadoProdutoController@carrinho');
			Route::post('/alterCart', 'MercadoProdutoController@alterCart');
		});

		Route::group(['prefix' => 'orcamentoVenda'], function(){
			Route::get('/', 'OrcamentoController@index');
			Route::post('/salvar', 'OrcamentoController@salvar');
			Route::get('/detalhar/{id}', 'OrcamentoController@detalhar');
			Route::get('/delete/{id}', 'OrcamentoController@delete');
			Route::get('/imprimir/{id}', 'OrcamentoController@imprimir');
			Route::get('/imprimirCompleto/{id}', 'OrcamentoController@imprimirCompleto');
			Route::get('/rederizarDanfe/{id}', 'OrcamentoController@rederizarDanfe');
			Route::get('/enviarEmail', 'OrcamentoController@enviarEmail');
			Route::get('/deleteItem/{id}', 'OrcamentoController@deleteItem');
			Route::post('/addItem', 'OrcamentoController@addItem');
			Route::post('/gerarVenda', 'OrcamentoController@gerarVenda');
			Route::post('/setValidade', 'OrcamentoController@setValidade');
			Route::post('/addPag', 'OrcamentoController@addPag');
			Route::get('/deleteParcela/{id}', 'OrcamentoController@deleteParcela');
			Route::get('/filtro', 'OrcamentoController@filtro');
			Route::get('/reprovar/{id}', 'OrcamentoController@reprovar');

			Route::get('/relatorioItens/{data1}/{data2}', 'OrcamentoController@relatorioItens');
			Route::post('/gerarPagamentos', 'OrcamentoController@gerarPagamentos');
			
		});

		Route::group(['prefix' => 'percentualuf'], function(){
			Route::get('/', 'PercentualController@index');
			Route::get('/novo/{uf}', 'PercentualController@novo');
			Route::get('/edit/{uf}', 'PercentualController@edit');
			Route::post('/save', 'PercentualController@save');
			Route::post('/update', 'PercentualController@update');
			Route::get('/verProdutos/{uf}', 'PercentualController@verProdutos');
			Route::get('/editPercentual/{id}', 'PercentualController@editPercentual');
			Route::post('/updatePercentualSingle', 'PercentualController@updatePercentualSingle');
			
		});

		Route::group(['prefix' => 'listaDePrecos'], function(){
			Route::get('/', 'ListaPrecoController@index');
			Route::get('/delete/{id}', 'ListaPrecoController@delete');
			Route::get('/edit/{id}', 'ListaPrecoController@edit');
			Route::get('/new', 'ListaPrecoController@new');

			Route::post('/save', 'ListaPrecoController@save');
			Route::post('/update', 'ListaPrecoController@update');

			Route::get('/ver/{id}', 'ListaPrecoController@ver');
			Route::get('/gerar/{id}', 'ListaPrecoController@gerar');
			Route::get('/editValor/{id}', 'ListaPrecoController@editValor');

			Route::post('/salvarPreco', 'ListaPrecoController@salvarPreco');

			Route::get('/pesquisa', 'ListaPrecoController@pesquisa');
			Route::get('/filtro', 'ListaPrecoController@filtro');

		});

		Route::group(['prefix' => 'pedido', 'middleware' => ['pedidoAtivo']], function(){
			Route::get('/', 'PedidoQrCodeController@index');
			Route::get('/open/{id}', 'PedidoQrCodeController@open');
			Route::get('/erro', 'PedidoQrCodeController@erro');
			Route::get('/cardapio/{id}', 'PedidoQrCodeController@cardapio');

			Route::get('/escolherSabores', 'PedidoQrCodeController@escolherSabores');
			Route::post('/adicionarSabor', 'PedidoQrCodeController@adicionarSabor');
			Route::get('/verificaPizzaAdicionada', 'PedidoQrCodeController@verificaPizzaAdicionada');
			Route::get('/removeSabor/{id}', 'PedidoQrCodeController@removeSabor');
			Route::get('/adicionais/{id}', 'PedidoQrCodeController@adicionais');
			Route::get('/adicionaisPizza', 'PedidoQrCodeController@adicionaisPizza');
			Route::get('/pesquisa', 'PedidoQrCodeController@pesquisa');
			Route::get('/pizzas', 'DeliveryController@pizzas');
			Route::get('/ver', 'PedidoQrCodeController@ver');

			Route::post('/addPizza', 'PedidoQrCodeController@addPizza')->middleware('mesaAtiva');
			Route::post('/addProd', 'PedidoQrCodeController@addProd')->middleware('mesaAtiva');

			Route::get('/refreshItem/{id}/{quantidade}', 'PedidoQrCodeController@refreshItem');
			Route::get('/removeItem/{id}', 'PedidoQrCodeController@removeItem');
			Route::get('/finalizar', 'PedidoQrCodeController@finalizar');
		});

		Route::group(['prefix' => 'configEcommerce'], function(){
			Route::get('/', 'ConfigEcommerceController@index');
			Route::post('/save', 'ConfigEcommerceController@save');
			Route::get('/verSite', 'ConfigEcommerceController@verSite');
		});

		Route::group(['prefix' => 'categoriaEcommerce'],function(){
			Route::get('/', 'CategoriaProdutoEcommerceController@index');
			Route::get('/delete/{id}', 'CategoriaProdutoEcommerceController@delete');
			Route::get('/edit/{id}', 'CategoriaProdutoEcommerceController@edit');
			Route::get('/new', 'CategoriaProdutoEcommerceController@new');

			Route::post('/save', 'CategoriaProdutoEcommerceController@save');
			Route::post('/update', 'CategoriaProdutoEcommerceController@update');
		});

		Route::group(['prefix' => 'clienteEcommerce'],function(){
			Route::get('/', 'ClienteEcommerceController@index');
			Route::get('/delete/{id}', 'ClienteEcommerceController@delete');
			Route::get('/edit/{id}', 'ClienteEcommerceController@edit');
			Route::get('/new', 'ClienteEcommerceController@new');

			Route::post('/save', 'ClienteEcommerceController@save');
			Route::post('/update', 'ClienteEcommerceController@update');
		});

		Route::group(['prefix' => 'enderecosEcommerce'],function(){
			Route::get('/{cliente_id}', 'EnderecoEcommerceController@index');
			Route::get('/edit/{id}', 'EnderecoEcommerceController@edit');
			Route::post('/update', 'EnderecoEcommerceController@update');
			
		});

		Route::group(['prefix' => 'produtoEcommerce'], function(){
			Route::get('/', 'ProdutoEcommerceController@index');
			Route::get('/delete/{id}', 'ProdutoEcommerceController@delete');
			Route::get('/deleteImagem/{id}', 'ProdutoEcommerceController@deleteImagem');
			Route::get('/edit/{id}', 'ProdutoEcommerceController@edit');
			Route::get('/editGrade/{id}', 'ProdutoEcommerceController@editGrade');
			Route::get('/listGrade/{referecia}', 'ProdutoEcommerceController@listGrade');
			Route::get('/galeria/{id}', 'ProdutoEcommerceController@galeria');
			Route::get('/deleteImagem/{id}', 'ProdutoEcommerceController@deleteImagem');
			Route::get('/new', 'ProdutoEcommerceController@new');
			Route::get('/pesquisa', 'ProdutoEcommerceController@pesquisa');

			Route::post('/save', 'ProdutoEcommerceController@save');
			Route::post('/update', 'ProdutoEcommerceController@update');
			Route::post('/saveImagem', 'ProdutoEcommerceController@saveImagem');

			Route::get('/alterarStatus/{id}', 'ProdutoEcommerceController@alterarStatus');
			Route::get('/alterarControlarEstoque/{id}', 
				'ProdutoEcommerceController@alterarControlarEstoque');
			Route::get('/alterarDestaque/{id}', 'ProdutoEcommerceController@alterarDestaque');
			
		});

		Route::group(['prefix' => 'pedidosEcommerce'], function(){
			Route::get('/', 'PedidoEcommerceController@index');
			Route::get('/filtro', 'PedidoEcommerceController@filtro');
			Route::get('/detalhar/{id}', 'PedidoEcommerceController@detalhar');
			Route::get('/gerarNFe/{id}', 'PedidoEcommerceController@gerarNFe');
			Route::get('/imprimir/{id}', 'PedidoEcommerceController@imprimir');

			Route::post('/salvarVenda', 'PedidoEcommerceController@salvarVenda');
			Route::get('/delete/{id}', 'PedidoEcommerceController@delete');
			Route::get('/verificaPagamentos', 'PedidoEcommerceController@verificaPagamentos');

			Route::post('/alterarStatus', 'PedidoEcommerceController@alterarStatus');
			
		});

		Route::group(['prefix' => 'carrosselEcommerce'],function(){
			Route::get('/', 'CarrosselEcommerceController@index');
			Route::get('/delete/{id}', 'CarrosselEcommerceController@delete');
			Route::get('/edit/{id}', 'CarrosselEcommerceController@edit');
			Route::get('/new', 'CarrosselEcommerceController@new');

			Route::post('/save', 'CarrosselEcommerceController@save');
			Route::post('/update', 'CarrosselEcommerceController@update');
		});

		Route::group(['prefix' => 'autorPost'],function(){
			Route::get('/', 'AutorPostController@index');
			Route::get('/delete/{id}', 'AutorPostController@delete');
			Route::get('/edit/{id}', 'AutorPostController@edit');
			Route::get('/new', 'AutorPostController@new');

			Route::post('/save', 'AutorPostController@save');
			Route::post('/update', 'AutorPostController@update');
		});

		Route::group(['prefix' => 'categoriaPosts'],function(){
			Route::get('/', 'CategoriaPostController@index');
			Route::get('/delete/{id}', 'CategoriaPostController@delete');
			Route::get('/edit/{id}', 'CategoriaPostController@edit');
			Route::get('/new', 'CategoriaPostController@new');

			Route::post('/save', 'CategoriaPostController@save');
			Route::post('/update', 'CategoriaPostController@update');
		});

		Route::group(['prefix' => 'postBlog'],function(){
			Route::get('/', 'PostblogController@index');
			Route::get('/delete/{id}', 'PostblogController@delete');
			Route::get('/edit/{id}', 'PostblogController@edit');
			Route::get('/new', 'PostblogController@new');

			Route::post('/save', 'PostblogController@save');
			Route::post('/update', 'PostblogController@update');
		});

		Route::group(['prefix' => 'contatoEcommerce'],function(){
			Route::get('/', 'ContatoEcommerceController@index');
			Route::get('/pesquisa', 'ContatoEcommerceController@pesquisa');
			Route::get('/delete/{id}', 'ContatoEcommerceController@delete');
		});

		Route::group(['prefix' => 'informativoEcommerce'],function(){
			Route::get('/', 'InformativoController@index');
			Route::get('/pesquisa', 'InformativoController@pesquisa');
			Route::get('/delete/{id}', 'InformativoController@delete');
		});

	});

Route::group(['prefix' => 'loja', 'middleware' => 'validaEcommerce'], function(){
	Route::get('/{link}', 'EcommerceController@index');
	Route::get('/{link}/categorias', 'EcommerceController@categorias');
	Route::get('/{link}/{id}/categorias', 'EcommerceController@produtosDaCategoria');

	//blog
	Route::get('/{link}/blog', 'EcommerceController@blog');
	Route::get('/{link}/contato', 'EcommerceController@contato');
	Route::get('/{link}/{id}/verPost', 'EcommerceController@verPost');
	Route::get('/{link}/{id}/verProduto', 'EcommerceController@verProduto');

	Route::post('/{link}/addProduto', 'EcommerceController@addProduto');
	Route::get('/{link}/carrinho', 'EcommerceController@carrinho');
	Route::get('/{link}/curtidas', 'EcommerceController@curtidas');
	Route::get('/{link}/{id}/deleteItemCarrinho', 'EcommerceController@deleteItemCarrinho');
	Route::get('/{link}/{id}/deleteItemCarrinho', 'EcommerceController@deleteItemCarrinho');
	Route::get('/{link}/carrinho/atualizaItem', 'EcommerceController@atualizaItem');

	Route::get('/{link}/checkout', 'EcommerceController@checkout');
	Route::post('/{link}/checkout', 'EcommerceController@checkoutStore');
	Route::get('/{link}/logoff', 'EcommerceController@logoff');
	Route::get('/{link}/login', 'EcommerceController@login');
	Route::post('/{link}/login', 'EcommerceController@loginPost');
	Route::post('/{link}/pagamento', 'EcommerceController@pagamento');
	// Route::get('/{link}/pagamento', 'EcommerceController@pagamento');
	Route::get('/{link}/endereco', 'EcommerceController@endereco');
	Route::get('/{link}/esquecisenha', 'EcommerceController@esquecisenha');
	Route::post('/{link}/esquecisenha', 'EcommerceController@esquecisenhaPost');
	Route::get('/{link}/{id}/curtirProduto', 'EcommerceController@curtirProduto');

	Route::get('/{link}/pedido_detalhe/{id}', 'EcommerceController@pedidoDetalhe');
	Route::get('/{link}/pesquisa', 'EcommerceController@pesquisa');


});

Route::post('/ecommerceContato', 'EcommerceController@saveContato');
Route::post('/ecommerceInformativo', 'EcommerceController@saveInformativo');
Route::get('/ecommerceCalculaFrete', 'EcommerceController@calculaFrete');
Route::post('/ecommerceSetaFrete', 'EcommerceController@setaFrete');
Route::post('/ecommerceUpdateCliente', 'EcommerceController@ecommerceUpdateCliente');
Route::post('/ecommerceUpdateSenha', 'EcommerceController@ecommerceUpdateSenha');
Route::post('/ecommerceSaveEndereco', 'EcommerceController@ecommerceSaveEndereco');

Route::group(['prefix' => 'ecommercePay'], function(){
	Route::post('/boleto', 'EcommercePayController@paymentBoleto');
	Route::post('/pix', 'EcommercePayController@paymentPix');
	Route::post('/cartao', 'EcommercePayController@paymentCartao');
	Route::get('/consulta/{transacao_id}', 'EcommercePayController@consultaPagamento');
	Route::get('/finalizado/{hash}', 'EcommercePayController@finalizado');
});

Route::get('lojainexistente', function(){
	return view('lojainexistente');
});

Route::get('/habilitadoApi', function(){
	return view('habilitadoApi');
});
