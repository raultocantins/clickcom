<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ConfigEcommerce;
use App\Models\InformativoEcommerce;
use App\Models\ContatoEcommerce;

class ConfigController extends Controller
{
	public function index(Request $request){

		try{
			$config = ConfigEcommerce::
			where('empresa_id', $request->empresa_id)
			->first();

			return response()->json($config, 200);
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}

	}

	public function salvarEmail(Request $request){
		try{
			$email = $request->email;
			$empresa_id = $request->empresa_id;


			$info = InformativoEcommerce::
			where('email', $email)
			->where('empresa_id', $empresa_id)
			->first();

			if($info != null){
				return response()->json("Email já registrado!!", 404);
			}

			$i = InformativoEcommerce::create([
				'empresa_id' => $empresa_id,
				'email' => $email
			]);

			return response()->json($i, 200);
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}
	}

	public function salvarContato(Request $request){
		try{
			$data = $request->data;

			$empresa_id = $request->empresa_id;

			$contato = [
				'nome' => $data['nome'],
				'email' => $data['email'],
				'texto' => $data['mensagem'],
				'empresa_id' => $empresa_id
			];

			$result = ContatoEcommerce::create($contato);
			return response()->json($result, 200);
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}
	}

}
