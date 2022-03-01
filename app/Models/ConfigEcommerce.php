<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigEcommerce extends Model
{
	protected $fillable = [
		'nome', 'link', 'logo', 'rua', 'numero', 'bairro', 'cidade', 'cep', 'telefone',
		'email', 'link_facebook', 'link_twiter', 'link_instagram', 'frete_gratis_valor', 
		'mercadopago_public_key', 'mercadopago_access_token', 'funcionamento', 'latitude',
		'longitude', 'politica_privacidade', 'empresa_id', 'src_mapa', 'cor_principal', 
		'google_api', 'tema_ecommerce', 'uf', 'habilitar_retirada', 'desconto_padrao_boleto',
		'desconto_padrao_pix', 'desconto_padrao_cartao', 'api_token', 'usar_api', 'fav_icon', 
		'timer_carrossel', 'img_contato', 'cor_fundo', 'cor_btn', 'mensagem_agradecimento'
	];
}
