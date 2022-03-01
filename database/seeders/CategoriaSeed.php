<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaConta;

class CategoriaSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CategoriaConta::create([
        	'nome' => 'Compras',
            'empresa_id' => 1,
            'tipo' => 'pagar'
        ]);
        CategoriaConta::create([
        	'nome' => 'Vendas',
            'empresa_id' => 1,
            'tipo' => 'receber'
        ]);
    }
}
