<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaProdutoEcommerce extends Model
{
    protected $fillable = [
        'nome', 'img', 'empresa_id', 'destaque'
    ];

    public function produtos(){
        return $this->hasMany('App\Models\ProdutoEcommerce', 'categoria_id', 'id');
    }

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (!empty($this->img)) {
            $image_url = asset('/ecommerce/categorias/' . rawurlencode($this->img));
        } else {
            $image_url = asset('/imgs/default.png');
        }
        return $image_url;
    }

    public function produtosAtivos(){
        $produtos = ProdutoEcommerce::
        where('categoria_id', $this->id)
        ->where('status', 1)
        ->get();
        $temp = [];
        foreach($produtos as $p){
            if(sizeof($p->galeria) > 0){
                array_push($temp, $p);
            }
        }
        return $temp;
    }
}
