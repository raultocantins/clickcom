<?php

namespace App\Http\Middleware;

use Closure;
use Response;
use App\Models\ConfigEcommerce;

class ValidaEcommerce
{
	public function handle($request, Closure $next){

		$urip = $_SERVER['REQUEST_URI'];
		$urip = explode("/", $urip);

		if(!isset($urip[2])){
			return redirect('/lojainexistente');
		}else{
			$config = ConfigEcommerce::
			where('link', strtolower($urip[2]))
			->first();

			if($config->usar_api){
				return redirect('/habilitadoApi');
			}

			if($config == null){
				return redirect('/lojainexistente');
			}
		}

		return $next($request);
		
	}


}