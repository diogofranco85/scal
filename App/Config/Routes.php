<?php
use Core\Route as route;
$request = new Core\Routes\Request();

route::get('/', 'HomeController@indexAction');
route::get('/dashboard', 'HomeController@indexAction');
route::get('/logs-de-erros-no-model','HomeController@logsmodel');
route::get('/logs-de-erros-do-php','HomeController@errorsphp');
/*************************
 * Usuarios
 **************************/
/** desconectar usuário do sistema  **/
route::get('/desconectar-usuario',function(){

    $control = new \Core\Controller();
    $control->session->usuario = null;
    $control->session->logged = false;
    $control->redirect('/sim');

});

route::get('/verficarlogin', 'SimController@verificarLogin');

/*************************
 *  Autenticar no SIM
 **************************/
route::post('/public_html/validaLogin','SimController@autenticar');
route::get('/public_html','SimController@index');
route::get('/sim', 'SimController@sim');
/*************************
 *  Clientes
 **************************/
route::get('/cliente','ClienteController@index');
route::post('cliente/validate/cnpj','ClienteController@cnpj');
route::post('/cliente/data','ClienteController@edit');
route::post('/cliente/json','JsonController@gridCliente');
route::post('/cliente','ClienteController@save');
route::delete('/cliente/delete','ClienteController@delete');
route::get('/cliente/csv','ClienteController@csv');
/*************************
 *  Amostras
 **************************/
route::get('/amostra','AmostrasController@index');
route::get('/json-amostras-contratos/{id}','AmostrasController@loadContrato');
route::post('/amostra/read/codebar','AmostrasController@loadfields');
route::post('/amostra/store', 'AmostrasController@save');
route::post('/amostra/edit','AmostrasController@edit');
route::delete('/amostra','AmostrasController@delete');
/*************************
 * Tipo de Amostras
 **************************/
route::get('/tipo_amostra','TipoAmostrasController@index');
route::post('/tipo_amostra/data','TipoAmostrasController@edit');
route::post('/tipo_amostra/store','TipoAmostrasController@save');
/*************************
 *  Contratos
 **************************/
route::get('/contratos/view/{id}', 'ContratosController@index');
route::post('/contrato/data','ContratosController@edit');
route::post('/contrato/store','ContratosController@save');
route::post('/contrato/delete','ContratosController@delete');
/*************************
 *  Teste
 **************************/
route::get('/teste_amostra', 'AmostraTesteController@index');
route::post('/teste/loadfields','AmostraTesteController@loadfields');
route::post('/teste/store','AmostraTesteController@save');
route::post('/teste/dtfinal','AmostraTesteController@savedtfinal');
route::post('/teste_amostra/loadview','AmostraTesteController@loadview');
/*************************
 *  Etiquetas
 **************************/
route::get('/etiqueta','EtiquetasController@index');
route::post('/etiqueta/search/contrato','EtiquetasController@search');
route::post('/gerar-etiquetas','EtiquetasController@gerar');
route::get('/etiqueta/print/all/cliente/{idcliente}/contrato/{idcontrato}/','EtiquetasController@viewAll');
route::get('/etiqueta/list/cliente/{idcliente}/{idcontrato}/','EtiquetasController@listview');
route::get('/etiqueta/imprimir/{id}','EtiquetasController@printer');
route::post('/etiqueta/validar','EtiquetasController@validarEtiqueta');

/*************************
 *  Endereçamento
 **************************/
route::get('/enderecamento', 'EnderecamentoController@index');
route::get('/enderecamento/fields','EnderecamentoController@loadfields');
//route::get('/enderecamento/local','EnderecamentoController@prateleiras');
route::post('/enderecamento/{idcliente}/store','EnderecamentoController@store');
route::post('/enderecamento/validar/etiqueta', 'EnderecamentoController@validar');
route::get('/movimentacao/{idestoque}/estoque/{idetiqueta}/editar', 'EnderecamentoController@edit');
/*************************
 *  Operador
 **************************/
route::post('/operador/get',"OperadorController@getOperador");
route::get('/operador/{id}', "OperadorController@index");
/*************************
 *  Finalizar Contrato
 **************************/
route::get('/finalizar/contrato', 'FinalizarController@index');
route::get('/contrato/{id}/finalizar', 'FinalizarController@delete');
route::get('/contrato/{id}/reabrir', 'FinalizarController@reopen');
route::get('/contrato/{id}/finalizar/justify', 'FinalizarController@close');
/*************************
 * movimentacao
 **************************/
route::get('/movimentacao/{idarm}/estoque/{idetiqueta}', 'MovimentacaoController@index');
route::post('/movimentar/estoque', 'MovimentacaoController@move');
route::post('/movimentacao/store', 'EnderecamentoController@storeMovimentacao');


route::get('/perfil', 'PerfilController@index');

/*************************
 * safra
 **************************/
route::get('/safra','SafraController@index');
route::post('/safra', 'SafraController@store');
route::put('/safra/{id}', 'SafraController@update');
route::delete('/safra', 'SafraController@delete');

/*************************
 * Relatorios
 **************************/
route::get('/relatorios','RelatoriosController@index');
route::get('/relatorio/rastreabilidade/{idetiqueta}','RelatoriosController@rastreabilidade');
/*************************
 *  Resolve Router
 **************************/
route::resolve($request);