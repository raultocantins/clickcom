<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagemProdutoEcommerce extends Model
{
    protected $fillable = [
        'produto_id', 'img'
    ];

    protected $appends = ['image_url'];

    public function produto(){
        return $this->hasOne('App\Models\ProdutoEcommerce', 'id', 'produto_id');
    }

    public function getImageUrlAttribute()
    {
        if (!empty($this->img)) {
            $image_url = asset('/ecommerce/produtos/' . rawurlencode($this->img));
        } else {
            $image_url = asset('/imgs/default.png');
        }
        return $image_url;
    }
}
