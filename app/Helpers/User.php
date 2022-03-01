<?php 
use Illuminate\Support\Facades\DB;

function is_adm(){
	$usr = session('user_logged');
	return $usr['adm'];
}

function get_id_user(){
	$usr = session('user_logged');
	return $usr['id'];
}

function __replace($valor){
	return str_replace(",", ".", $valor);
}

function valida_objeto($objeto){
	$usr = session('user_logged');
	if(isset($objeto['empresa_id']) && $objeto['empresa_id'] == $usr['empresa']){
		return true;
	}else{
		return false;
	}
}

function tabelasArmazenamento(){
	// indice nome da tabela, valor em kb
	return [
		'clientes' => 5,
		'produtos' => 8,
		'fornecedors' => 4,
		'vendas' => 4,
		'venda_caixas' => 4,
		'transportadoras' => 4,
		'orcamentos' => 4,
		'categorias' => 4,
	];
}

function isSuper($login){
	$arrSuper = explode(',', getenv("USERMASTER"));

	if(in_array($login, $arrSuper)){
		return true;
	}
	return false;
}

function getSuper(){
	$arrSuper = explode(',', getenv("USERMASTER"));

	return $arrSuper[0];
}

