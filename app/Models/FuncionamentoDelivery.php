<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuncionamentoDelivery extends Model
{

    protected $fillable = [
        'ativo', 'dia', 'inicio_expediente', 'fim_expediente', 'empresa_id'
    ];

    public static function dias(){
    	return [
            'DOMINGO',
    		'SEGUNDA',	
    		'TERÇA',	
    		'QUARTA',	
    		'QUINTA',	
    		'SEXTA',	
    		'SABADO'
    	];
    }
}
