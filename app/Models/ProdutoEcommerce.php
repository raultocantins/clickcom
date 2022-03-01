<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoEcommerce extends Model
{
	protected $fillable = [
		'produto_id', 'categoria_id', 'empresa_id', 'descricao', 'controlar_estoque', 'status',
		'valor', 'destaque', 'cep', 'percentual_desconto_view'
	];

	public function galeria(){
		return $this->hasMany('App\Models\ImagemProdutoEcommerce', 'produto_id', 'id');
	}

    protected $appends = ['image_url'];

	public function getImageUrlAttribute()
    {
        if (sizeof($this->galeria) > 0) {
            $image_url = asset('/ecommerce/produtos/' . rawurlencode($this->galeria[0]->img));
        } else {
            $image_url = asset('/imgs/default.png');
        }
        return $image_url;
    }

	public function produto(){
		return $this->belongsTo(Produto::class, 'produto_id');
	}

	public function categoria(){
		return $this->belongsTo(CategoriaProdutoEcommerce::class, 'categoria_id');
	}

	public function isNovo(){
		$strCadastro = strtotime($this->created_at);
		$strHoje = strtotime(date('Y-m-d H:i:s'));
		$dif = $strHoje - $strCadastro;
		$dif = $dif/24/60/60;
		if($dif < 7) return 1;
		else return 0;
	}

}
