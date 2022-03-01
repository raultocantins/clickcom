<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InformativoEcommerce;

class InformativoController extends Controller
{
    protected $empresa_id = null;
    public function __construct(){

        $this->middleware(function ($request, $next) {
            $this->empresa_id = $request->empresa_id;
            $value = session('user_logged');
            if(!$value){
                return redirect("/login");
            }
            return $next($request);
        });

    }

    public function index(){
    	$infos = InformativoEcommerce::
    	where('empresa_id', $this->empresa_id)
    	->orderBy('id', 'desc')
    	->paginate(30);

    	return view('informativoEcommerce/list')
    	->with('infos', $infos)
    	->with('links', true)
    	->with('title', 'Informativo Ecommerce');
    }

    public function pesquisa(Request $request){
    	$infos = InformativoEcommerce::
    	where('empresa_id', $this->empresa_id)
    	->where('email', 'LIKE', "%$request->pesquisa%")
    	->orderBy('id', 'desc')
    	->get();

    	return view('informativoEcommerce/list')
    	->with('infos', $infos)
    	->with('title', 'Informativo Ecommerce');
    }

    public function delete($id){
        try{
            $contato = ContatoEcommerce
            ::where('id', $id)
            ->first();
            if(valida_objeto($contato)){
                if($contato->delete()){

                    session()->flash('mensagem_sucesso', 'Registro removido!');
                }else{

                    session()->flash('mensagem_erro', 'Erro!');
                }
                return redirect('/contatoEcommerce');
            }
        }catch(\Exception $e){
            return view('errors.sql')
            ->with('title', 'Erro ao deletar')
            ->with('motivo', 'Não é possivel remover este registro');
        }
    }
}
