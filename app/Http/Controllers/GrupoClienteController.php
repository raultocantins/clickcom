<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrupoCliente;

class GrupoClienteController extends Controller
{
    public function __construct(){
        $this->middleware(function ($request, $next) {
            $value = session('user_logged');
            if(!$value){
                return redirect("/login");
            }
            return $next($request);
        });
    }

    public function index(Request $request){

        $grupos = GrupoCliente::
        where('empresa_id', $request->empresa_id)
        ->get();

        return view('grupoCliente/list')
        ->with('grupos', $grupos)
        ->with('title', 'Grupos de Cliente');
    }

    public function new(){
        return view('grupoCliente/register')
        ->with('title', 'Cadastrar Grupo de Cliente');
    }

    public function save(Request $request){

        $this->_validate($request);

        $result = GrupoCliente::create($request->all());


        if($result){
            session()->flash("mensagem_sucesso", "Grupo adicionado!");
        }else{
            session()->flash('mensagem_erro', 'Erro ao cadastrar grupo.');
        }

        return redirect('/gruposCliente');
    }

    public function edit($id){

        $resp = GrupoCliente::
        where('id', $id)->first();  

        if(valida_objeto($resp)){
            return view('grupoCliente/register')
            ->with('grupo', $resp)
            ->with('title', 'Editar Grupo');
        }else{
            return redirect('/403');
        }

    }

    public function update(Request $request){

        $id = $request->input('id');
        $resp = GrupoCliente::
        where('id', $id)->first(); 

        $this->_validate($request);

        $resp->nome = $request->input('nome');

        $result = $resp->save();
        if($result){
            session()->flash('mensagem_sucesso', 'Grupo editado com sucesso!');
        }else{
            session()->flash('mensagem_erro', 'Erro ao editar grupo!');
        }

        return redirect('/gruposCliente'); 
    }

    public function delete($id){
        try{
            $grupo = GrupoCliente
            ::where('id', $id)
            ->first();
            if(valida_objeto($grupo)){
                if($grupo->delete()){
                    session()->flash('mensagem_sucesso', 'Registro removido!');
                }else{

                    session()->flash('mensagem_erro', 'Erro!');
                }
                return redirect('/gruposCliente');
            }else{
                return redirect('403');
            }
        }catch(\Exception $e){
            return view('errors.sql')
            ->with('title', 'Erro ao deletar grupo')
            ->with('motivo', $e->getMessage());
        }
    }


    private function _validate(Request $request){
        $rules = [
            'nome' => 'required|max:50'
        ];

        $messages = [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.max' => '50 caracteres maximos permitidos.'
        ];
        $this->validate($request, $rules, $messages);
    }

    public function list($id){
        $grupo = GrupoCliente::find($id);

        return view('grupoCliente/clientes')
        ->with('grupo', $grupo)
        ->with('title', 'Lista de clientes');
    }

}
