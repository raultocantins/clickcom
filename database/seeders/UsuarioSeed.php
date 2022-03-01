<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Helpers\Menu;
class UsuarioSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private function validaPermissao(){
        $menu = new Menu();
        $temp = [];
        $menu = $menu->getMenu();
        foreach($menu as $m){
            foreach($m['subs'] as $s){
                array_push($temp, $s['rota']);
            }
        }
        return $temp;
    }

    public function run()
    {
        Empresa::create([
            'nome' => 'Slym',
            'rua' => 'Aldo ribas',
            'numero' => '190',
            'bairro' => 'Centro',
            'cidade' => 'Jaguariaiva',
            'status' => 1,
            'email' => 'master@master.com',
            'telefone' => '00000000000',
            'cnpj' => '',
            'permissao' => '',
        ]);

        $todasPermissoes = $this->validaPermissao();

        Usuario::create([
        	'nome' => 'Usuário',
        	'login' => 'usuario',
        	'senha' => '202cb962ac59075b964b07152d234b70',
            'adm' => 1,
            'ativo' => 1,
            'permissao' => json_encode($todasPermissoes),
            'empresa_id' => 1,
            'img' => '',
            'tema' => 1,
            'email' => ''
        ]);
    }
}
