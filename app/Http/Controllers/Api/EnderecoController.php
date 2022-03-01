<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EnderecoEcommerce;
use App\Models\ClienteEcommerce;

class EnderecoController extends Controller
{
	public function salvar(Request $request){
		try{
			$endereco = $request->endereco;
			$token = $request->token;

			$cliente = ClienteEcommerce::
			where('token', $token)
			->first();

			$dataEndereco = [
				'rua' => $endereco['rua'],
				'numero' => $endereco['numero'],
				'bairro' => $endereco['bairro'],
				'cep' => $endereco['cep'],
				'cidade' => $endereco['cidade'],
				'uf' => $endereco['uf'],
				'complemento' => $endereco['complemento'] ?? '',
				'cliente_id' => $cliente->id
			];

			EnderecoEcommerce::create($dataEndereco);
			
			return response()->json("ok", 200);
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}
	}

	public function atualizar(Request $request){
		try{
			$end = $request->endereco;
			$id = $request->id;

			$endereco = EnderecoEcommerce::find($id);

			$endereco->rua = $end['rua'];
			$endereco->numero = $end['numero'];
			$endereco->bairro = $end['bairro'];
			$endereco->cep = $end['cep'];
			$endereco->cidade = $end['cidade'];
			$endereco->uf = $end['uf'];
			$endereco->complemento = $end['complemento'];
			
			$endereco->save();
			
			return response()->json("ok", 200);
		}catch(\Exception $e){
			return response()->json($e->getMessage(), 401);
		}
	}
}
