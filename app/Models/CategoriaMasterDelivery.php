<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaMasterDelivery extends Model
{
    protected $fillable = [ 'nome', 'img' ];

    public function produtos(){
        return $this->hasMany('App\Models\ProdutoDestaqueMasterDelivery', 'categoria_id', 'id');
    }
}
