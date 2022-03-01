<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfigEcommerce;
use Illuminate\Support\Str;

class ConfigEcommerceController extends Controller
{
	protected $empresa_id = null;
	public function __construct(){
		$this->middleware(function ($request, $next) {
			$this->empresa_id = $request->empresa_id;
			$value = session('user_logged');
			if(!$value){
				return redirect("/login");
			}
			return $next($request);
		});
	}

	public function index(){
		$config = ConfigEcommerce::
		where('empresa_id', $this->empresa_id)
		->first();

		return view('configEcommerce/index')
		->with('config', $config)
		->with('textAreaEditor', true)
		->with('title', 'Configurar Parametros de Ecommerce');
	}

	public function save(Request $request){
		$this->_validate($request);
		$result = false;
		if($request->id == 0){

			$nomeImagem = "";
			if($request->hasFile('file')){
    		//unlink anterior
				$file = $request->file('file');
				$nomeImagem = Str::random(20).".png";
				$upload = $file->move(public_path('ecommerce/logos'), $nomeImagem);
			}

			$result = ConfigEcommerce::create([
				'link_facebook' => $request->link_facebook ?? '',
				'link_twiter' => $request->link_twiter ?? '',
				'link_instagram' => $request->link_instagram ?? '',
				'telefone' => $request->telefone,
				'rua' => $request->rua,
				'numero' => $request->numero,
				'bairro' => $request->bairro,
				'cidade' => $request->cidade,
				'uf' => $request->uf,
				'cep' => $request->cep,
				'nome' => $request->nome,
				'link' => $request->link,
				'email' => $request->email,
				'latitude' => $request->latitude,
				'longitude' => $request->longitude,
				'frete_gratis_valor' => $request->frete_gratis_valor ? __replace($request->frete_gratis_valor) : 0,
				'desconto_padrao_boleto' => $request->desconto_padrao_boleto ? __replace($request->desconto_padrao_boleto) : 0,
				'desconto_padrao_pix' => $request->desconto_padrao_pix ? __replace($request->desconto_padrao_pix) : 0,
				'desconto_padrao_cartao' => $request->desconto_padrao_cartao ? __replace($request->desconto_padrao_cartao) : 0,
				'funcionamento' => $request->funcionamento,
				'politica_privacidade' => $request->politica_privacidade ?? '',
				'mercadopago_public_key' => $request->mercadopago_public_key ?? '',
				'mercadopago_access_token' => $request->mercadopago_access_token ?? '',
				'mercadopago_access_token' => $request->mercadopago_access_token ?? '',
				'cor_principal' => $request->cor_principal ?? '',
				'src_mapa' => $request->src_mapa ?? '',
				'habilitar_retirada' => $request->habilitar_retirada ? true : false,
				'usar_api' => $request->usar_api ? true : false,
				'api_token' => $request->api_token ?? '',
				'logo' => $nomeImagem,
				'google_api' => $request->google_api ?? '',
				'empresa_id' => $this->empresa_id,
				'tema_ecommerce' => $request->tema_ecommerce ?? 'ecommerce',

				'cor_fundo' => $request->cor_fundo ?? '#000',
				'cor_btn' => $request->cor_btn ?? '#000',
				'mensagem_agradecimento' => $request->mensagem_agradecimento ?? '',
				'timer_carrossel' => $request->timer_carrossel ?? 5

			]);
		}else{

			$config = ConfigEcommerce::
			where('empresa_id', $this->empresa_id)
			->first();

			$nomeImagem = $config->logo;

			if($request->hasFile('file')){

				if($config->logo != "" && file_exists(public_path('ecommerce/logos'). "/".$config->logo)){
					unlink(public_path('ecommerce/logos'). "/".$config->logo);
				}

				$file = $request->file('file');
				$nomeImagem = Str::random(20).".png";
				$upload = $file->move(public_path('ecommerce/logos'), $nomeImagem);

				$config->logo = $nomeImagem;

			}

			if($request->hasFile('img_contato')){

				if($config->img_contato != "" && file_exists(public_path('ecommerce/assets/img/contato'). "/".$config->img_contato)){
					unlink(public_path('ecommerce/assets/img/contato'). "/".$config->img_contato);
				}

				$file = $request->file('img_contato');
				$nomeImagem = Str::random(20).".png";
				$upload = $file->move(public_path('ecommerce/assets/img/contato'), $nomeImagem);
				$config->img_contato = $nomeImagem;

			}

			if($request->hasFile('fav_icon')){

				if($config->fav_icon != "" && file_exists(public_path('ecommerce/assets/img/favicon'). "/".$config->fav_icon)){
					unlink(public_path('ecommerce/assets/img/favicon'). "/".$config->fav_icon);
				}

				$file = $request->file('fav_icon');
				$nomeImagem = Str::random(20).".png";
				$upload = $file->move(public_path('ecommerce/assets/img/favicon'), $nomeImagem);
				$config->fav_icon = $nomeImagem;
				
			}

			$config->nome = $request->nome;
			$config->link = $request->link;
			$config->rua = $request->rua;
			$config->numero = $request->numero;
			$config->bairro = $request->bairro;
			$config->cidade = $request->cidade;
			$config->uf = $request->uf;
			$config->cep = $request->cep;
			$config->email = $request->email;
			$config->telefone = $request->telefone;
			$config->frete_gratis_valor = $request->frete_gratis_valor ? __replace($request->frete_gratis_valor) : 0;
			$config->desconto_padrao_boleto = $request->desconto_padrao_boleto ? __replace($request->desconto_padrao_boleto) : 0;
			$config->desconto_padrao_pix = $request->desconto_padrao_pix ? __replace($request->desconto_padrao_pix) : 0;
			$config->desconto_padrao_cartao = $request->desconto_padrao_cartao ? __replace($request->desconto_padrao_cartao) : 0;
			$config->latitude = $request->latitude;
			$config->longitude = $request->longitude;
			$config->funcionamento = $request->funcionamento;
			$config->politica_privacidade = $request->politica_privacidade ?? '';

			$config->mercadopago_public_key = $request->mercadopago_public_key ?? '';
			$config->mercadopago_access_token = $request->mercadopago_access_token ?? '';
			$config->link_facebook = $request->link_facebook ?? '';
			$config->link_twiter = $request->link_twiter ?? '';
			$config->link_instagram = $request->link_instagram ?? '';
			$config->src_mapa = $request->src_mapa ?? '';
			$config->google_api = $request->google_api ?? '';
			$config->cor_principal = $request->cor_principal ?? '';
			$config->tema_ecommerce = $request->tema_ecommerce ?? '';
			$config->habilitar_retirada = $request->habilitar_retirada ? true : false;
			$config->usar_api = $request->usar_api ? true : false;
			$config->api_token = $request->api_token ?? '';

			$config->cor_fundo = $request->cor_fundo ?? '#000';
			$config->cor_btn = $request->cor_btn ?? '#000';
			$config->mensagem_agradecimento = $request->mensagem_agradecimento ?? '';
			$config->timer_carrossel = $request->timer_carrossel ?? '';

			$result = $config->save();
		}

		if($result){
			session()->flash("mensagem_sucesso", "Configurado com sucesso!");
		}else{
			session()->flash('mensagem_erro', 'Erro ao configurar!');
		}

		return redirect('/configEcommerce');
	}

	private function _validate(Request $request){
		$rules = [
			'nome' => 'required|max:30',

			'link' => ['required', 'max:30', \Illuminate\Validation\Rule::unique('config_ecommerces')->ignore($request->id) ],

			'rua' => 'required|max:80',
			'numero' => 'required|max:10',
			'bairro' => 'required|max:30',
			'cidade' => 'required|max:30',
			'uf' => 'required|max:2',
			'cep' => 'required|max:10',
			'telefone' => 'required|max:15',
			'email' => 'required|max:60',
			'mercadopago_public_key' => 'max:120',
			'mercadopago_access_token' => 'max:120',
			'funcionamento' => 'required|max:120',
			'link_facebook' => 'max:120',
			'link_twiter' => 'max:120',
			'link_instagram' => 'max:120',
			'latitude' => 'required|max:10',
			'longitude' => 'required|max:10',
			'politica_privacidade' => 'max:400',
		];

		$messages = [
			'link_facebook.max' => '120 caracteres maximos permitidos.',
			'link_twiter.max' => '120 caracteres maximos permitidos.',
			'link_instagram.max' => '120 caracteres maximos permitidos.',

			'nome.required' => 'O campo nome é obrigatório.',
			'nome.max' => '80 caracteres maximos permitidos.',
			'link.required' => 'O campo link é obrigatório.',
			'link.max' => '30 caracteres maximos permitidos.',

			'telefone.required' => 'O campo Telefone é obrigatório.',
			'telefone.max' => '35 caracteres maximos permitidos.',
			'rua.required' => 'O campo rua é obrigatório.',
			'rua.max' => '80 caracteres maximos permitidos.',
			'numero.required' => 'O campo número é obrigatório.',
			'numero.max' => '10 caracteres maximos permitidos.',
			'bairro.required' => 'O campo bairro é obrigatório.',
			'bairro.max' => '30 caracteres maximos permitidos.',
			'cidade.required' => 'O campo cidade é obrigatório.',
			'cidade.max' => '30 caracteres maximos permitidos.',
			'uf.required' => 'O campo UF é obrigatório.',
			'uf.max' => '2 caracteres maximos permitidos.',
			'cep.required' => 'O campo cep é obrigatório.',
			'cep.max' => '10 caracteres maximos permitidos.',
			'email.required' => 'O campo email é obrigatório.',
			'email.max' => '120 caracteres maximos permitidos.',

			'mercadopago_public_key.max' => '120 caracteres maximos permitidos.',
			'mercadopago_access_token.max' => '120 caracteres maximos permitidos.',
			'politica_privacidade.max' => '400 caracteres maximos permitidos.',
			'funcionamento.required' => 'O campo funcionamento é obrigatório.',
			'funcionamento.max' => '60 caracteres maximos permitidos.',

			'latitude.required' => 'O campo Latitude é obrigatório.',
			'latitude.max' => '10 caracteres maximos permitidos.',
			'longitude.required' => 'O campo Longitude é obrigatório.',
			'longitude.max' => '10 caracteres maximos permitidos.',
			'link.unique' => 'Já existe um cadastro com este link.'

		];
		$this->validate($request, $rules, $messages);
	}

	public function verSite(){
		$config = ConfigEcommerce::
		where('empresa_id', $this->empresa_id)
		->first();
		if($config == null){
			session()->flash('mensagem_erro', 'Configure o ecommerce!');
			return redirect('/configEcommerce');
		}
		return redirect('/loja/'.strtolower($config->link));
	}
}
