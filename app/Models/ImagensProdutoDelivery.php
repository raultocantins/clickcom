<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagensProdutoDelivery extends Model
{	
	protected $fillable = [
		'produto_id', 'path'
	];

	public function produto(){
        return $this->hasOne('App\Models\ProdutoDelivery', 'id', 'produto_id');
    }
	
}
