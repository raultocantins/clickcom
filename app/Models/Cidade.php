<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{

    protected $fillable = [
        'nome', 'uf', 'codigo'
    ];

    public static function getCidadeCod($codMun){
    	return Cidade::
    	where('codigo', $codMun)
    	->first();
	}

	public static function getId($id){
    	return Cidade::
    	where('id', $id)
    	->first();
	}

    public static function estados(){
        return [
            "AC",
            "AL",
            "AM",
            "AP",
            "BA",
            "CE",
            "DF",
            "ES",
            "GO",
            "MA",
            "MG",
            "MS",
            "MT",
            "PA",
            "PB",
            "PE",
            "PI",
            "PR",
            "RJ",
            "RN",
            "RS",
            "RO",
            "RR",
            "SC",
            "SE",
            "SP",
            "TO"    
        ];
    }
}
