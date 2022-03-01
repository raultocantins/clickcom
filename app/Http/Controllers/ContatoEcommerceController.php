<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContatoEcommerce;

class ContatoEcommerceController extends Controller
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
    	$contatos = ContatoEcommerce::
    	where('empresa_id', $this->empresa_id)
    	->orderBy('id', 'desc')
    	->paginate(30);

    	return view('contatoEcommerce/list')
    	->with('contatos', $contatos)
    	->with('links', true)
    	->with('title', 'Contato Ecommerce');
    }

    public function pesquisa(Request $request){
    	$contatos = ContatoEcommerce::
    	where('empresa_id', $this->empresa_id)
    	->where('nome', 'LIKE', "%$request->pesquisa%")
    	->orderBy('id', 'desc')
    	->get();

    	return view('contatoEcommerce/list')
    	->with('contatos', $contatos)
    	->with('title', 'Contato Ecommerce');
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
