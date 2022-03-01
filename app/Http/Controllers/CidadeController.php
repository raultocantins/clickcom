<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cidade;

class CidadeController extends Controller
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

    public function index(){
        $cidades = Cidade::
        paginate(100);

        $count = Cidade::count();

        return view('cidades/list')
        ->with('cidades', $cidades)
        ->with('count', $count)
        ->with('links', true)
        ->with('title', 'Cidades');
    }

    public function filtro(Request $request){
        $cidades = Cidade::
        where('nome', 'LIKE', "%$request->nome%")
        ->get();

        $count = Cidade::count();

        return view('cidades/list')
        ->with('cidades', $cidades)
        ->with('count', $count)
        ->with('title', 'Cidades');
    }

    public function nova(){

        return view('cidades/register')
        ->with('title', 'Nova Cidade');
    }

    public function editar($id){
        $cidade = Cidade::find($id);
        return view('cidades/register')
        ->with('cidade', $cidade)
        ->with('title', 'Editar Cidade');
    }

    public function delete($id){
        $cidade = Cidade::find($id);
        try{

            $cidade->delete();
            session()->flash('mensagem_sucesso', 'Cidade removida!!');

            return redirect()->back();

        }catch(\Exception $e){
            session()->flash('mensagem_erro', 'Erro: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function save(Request $request){
        $this->_validate($request);

        try{

            Cidade::create($request->all());
            session()->flash('mensagem_sucesso', 'Cidade cadastrada!!');

            return redirect('/cidades');

        }catch(\Exception $e){
            session()->flash('mensagem_erro', 'Erro: ' . $e->getMessage());
            return redirect('/cidades');
        }
    }

    public function update(Request $request){
        $this->_validate($request);

        try{
            $cidade = Cidade::find($request->id);

            $cidade->nome = $request->nome;
            $cidade->uf = $request->uf;
            $cidade->codigo = $request->codigo;

            $cidade->save();
            session()->flash('mensagem_sucesso', 'Cidade editada!!');

            return redirect('/cidades');

        }catch(\Exception $e){
            session()->flash('mensagem_erro', 'Erro: ' . $e->getMessage());
            return redirect('/cidades');
        }
    }

    private function _validate(Request $request){
        $rules = [
            'nome' => 'required|max:40',
            'codigo' => 'required|max:7|min:7'
        ];

        $messages = [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.max' => '40 caracteres maximos permitidos.',
            'codigo.required' => 'O campo códgo é obrigatório.',
            'codigo.max' => 'Digite 7 caracteres.',
            'codigo.min' => 'Digite 7 caracteres.',
        ];
        $this->validate($request, $rules, $messages);
    }
    
    public function all(){
    	$cidades = Cidade::all();
        $arr = array();
        foreach($cidades as $c){
            $arr[$c->id. ' - ' .$c->nome.'('.$c->uf.')'] = null;
                //array_push($arr, $temp);
        }
        echo json_encode($arr);
    }

    public function find($id){
    	$cidade = Cidade::
    	where('id', $id)
    	->first();
        echo json_encode($cidade);
    }

    public function findNome($nome){
        $cidade = Cidade::
        where('nome', $nome)
        ->first();
        echo json_encode($cidade);
    }
}
