<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;

class MarcaController extends Controller
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

        $marcas = Marca::
        where('empresa_id', $request->empresa_id)
        ->get();

        return view('marcas/list')
        ->with('marcas', $marcas)
        ->with('title', 'Marcas');
    }

    public function new(){
        return view('marcas/register')
        ->with('title', 'Cadastrar Marca');
    }

    public function save(Request $request){

        $marca = new Marca();
        $this->_validate($request);

        $result = $marca->create($request->all());

        $msgSucesso = "Marca cadastrada com sucesso";

        if($result){
            session()->flash("mensagem_sucesso", $msgSucesso);
        }else{
            session()->flash('mensagem_erro', 'Erro ao cadastrar marca.');
        }

        return redirect('/marcas');
    }

    public function edit($id){
        $marca = new Marca(); 

        $resp = $marca
        ->where('id', $id)->first();  

        if(valida_objeto($resp)){
            return view('marcas/register')
            ->with('marca', $resp)
            ->with('title', 'Editar Marca');
        }else{
            return redirect('/403');
        }

    }

    public function update(Request $request){
        $marca = new Marca();

        $id = $request->input('id');
        $resp = $marca
        ->where('id', $id)->first(); 

        $this->_validate($request);


        $resp->nome = $request->input('nome');

        $result = $resp->save();
        if($result){
            session()->flash('mensagem_sucesso', 'Marca editada com sucesso!');
        }else{
            session()->flash('mensagem_erro', 'Erro ao editar marca!');
        }

        return redirect('/marcas'); 
    }

    public function delete($id){
        try{
            $marca = Marca
            ::where('id', $id)
            ->first();

            if(sizeof($marca->produtos) > 0){
                session()->flash('mensagem_erro', 'Esta marca possui vínculo com produto(s), não é possível remover!!');
                return redirect('/marcas');
            }
            if(valida_objeto($marca)){
                if($marca->delete()){
                    session()->flash('mensagem_sucesso', 'Registro removido!');
                }else{

                    session()->flash('mensagem_erro', 'Erro!');
                }
                return redirect('/marcas');
            }else{
                return redirect('403');
            }
        }catch(\Exception $e){
            return view('errors.sql')
            ->with('title', 'Erro ao deletar marca')
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

    public function quickSave(Request $request){
        try{
            $nome = $request->nome;

            $res = Marca::create(
                [
                    'nome' => $nome,
                    'empresa_id' => $request->empresa_id
                ]
            );
            return response()->json($res, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }
}
