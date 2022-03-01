<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Cidade;
use App\Models\GrupoCliente;
use App\Models\Pais;
use App\Imports\ProdutoImport;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use App\Rules\ValidaDocumento;
use App\Models\CreditoVenda;
use App\Models\Acessor;
use App\Models\Funcionario;
use Dompdf\Dompdf;

class ClienteController extends Controller
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

    public function pesquisa(Request $request){
        $pesquisa = $request->input('pesquisa');
        $aniversariante = $request->input('aniversariante') ? true : false;

        if($pesquisa == "" && !$aniversariante){
            return redirect('/clientes');
        }

        $clientes = Cliente::
        where('empresa_id', $this->empresa_id)
        ->where('razao_social', 'LIKE', "%$pesquisa%")
        ->get();

        if($aniversariante){
            $mes = date('m');
            $temp = [];
            foreach($clientes as $c){
                if($c->data_aniversario != ""){
                    if($mes == substr($c->data_aniversario, 3, 5)){
                        array_push($temp, $c);
                    }
                }
            }

            $clientes = $temp;
        }

        return view('clientes/list')
        ->with('clientes', $clientes)
        ->with('pesquisa', $pesquisa)
        ->with('paraImprimir', true)
        ->with('aniversariante', $aniversariante)
        ->with('title', 'Filtro Clientes');
    }

    public function relatorio(Request $request){
        $pesquisa = $request->input('pesquisa');
        $aniversariante = $request->input('aniversariante') ? true : false;

        $clientes = Cliente::
        where('empresa_id', $this->empresa_id)
        ->where('razao_social', 'LIKE', "%$pesquisa%")
        ->orWhere('nome_fantasia', 'LIKE', "%$pesquisa%")
        ->get();

        if($aniversariante){
            $mes = date('m');
            $temp = [];
            foreach($clientes as $c){
                if($c->data_aniversario != ""){
                    if($mes == substr($c->data_aniversario, 3, 5)){
                        array_push($temp, $c);
                    }
                }
            }

            $clientes = $temp;
        }

        $p = view('clientes/relatorio_clientes')
        ->with('clientes', $clientes);

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("relatorio clientes.pdf");

    }

    public function index(){

        $clientes = Cliente::
        where('empresa_id', $this->empresa_id)
        ->paginate(20);

        $totalGeralClientes = sizeof(Cliente::
        where('empresa_id', $this->empresa_id)
        ->get());
        return view('clientes/list')
        ->with('clientes', $clientes)
        ->with('totalGeralClientes', $totalGeralClientes)
        ->with('links', true)
        ->with('title', 'Clientes');
    }

    public function new(){
        $estados = Cliente::estados();
        $cidades = Cidade::all();
        $pais = Pais::all();
        $grupos = GrupoCliente::
        where('empresa_id', $this->empresa_id)
        ->get();

        $acessores = Acessor::
        where('empresa_id', $this->empresa_id)
        ->get();

        $funcionarios = Funcionario::
        where('empresa_id', $this->empresa_id)
        ->get();

        return view('clientes/register')
        ->with('pessoaFisicaOuJuridica', true)
        ->with('cidadeJs', true)
        ->with('cidades', $cidades)
        ->with('estados', $estados)
        ->with('acessores', $acessores)
        ->with('funcionarios', $funcionarios)
        ->with('grupos', $grupos)
        ->with('pais', $pais)
        ->with('title', 'Cadastrar Cliente');
    }

    public function save(Request $request){

        $cidade = $request->input('cidade');
        $cidade = explode("-", $cidade);
        $cidade = $cidade[0];   

        $cidadeTemp = Cidade::find($cidade);
        if($cidadeTemp == null){
            $request->merge([ 'cidade' => 'a']);
        }

        $cliente = new Cliente();
        $this->_validate($request);

        $limite = $request->limite_venda ? $request->limite_venda : 0;
        $limite = str_replace(",", ".", $limite);
        $request->merge([ 'limite_venda' => $limite]);
        $request->merge([ 'celular' => $request->celular ?? '']);
        $request->merge([ 'telefone' => $request->telefone ?? '']);
        $request->merge([ 'ie_rg' => $request->ie_rg ? strtoupper($request->ie_rg) :
            'ISENTO']);
        $request->merge([ 'razao_social' => strtoupper($request->razao_social)]);
        $request->merge([ 'nome_fantasia' => strtoupper($request->nome_fantasia)]);
        $request->merge([ 'rua' => strtoupper($request->rua)]);
        $request->merge([ 'numero' => strtoupper($request->numero)]);
        $request->merge([ 'bairro' => strtoupper($request->bairro)]);
        $request->merge([ 'email' => $request->email ?? '']);
        $request->merge([ 'observacao' => $request->observacao ?? '']);

        $request->merge([ 'rua_cobranca' => strtoupper($request->rua_cobranca ?? '')]);
        $request->merge([ 'numero_cobranca' => strtoupper($request->numero_cobranca ?? '')]);
        $request->merge([ 'bairro_cobranca' => strtoupper($request->bairro_cobranca ?? '')]);
        $request->merge([ 'cep_cobranca' => strtoupper($request->cep_cobranca ?? '')]);
        $request->merge([ 'cidade_cobranca_id' => NULL]); // inicia NULL
        $request->merge([ 'id_estrangeiro' => $request->id_estrangeiro ?? '']); // inicia NULL
        $request->merge([ 'contador_nome' => $request->contador_nome ?? '']); 
        $request->merge([ 'contador_telefone' => $request->contador_telefone ?? '']); 
        $request->merge([ 'contador_email' => $request->contador_email ?? '']); 
        $request->merge([ 'data_aniversario' => $request->data_aniversario ?? '']); 

        $cidade = $request->input('cidade');
        $cidade = explode("-", $cidade);
        $cidade = $cidade[0];

        $request->merge([ 'cidade_id' => $cidade]);

        if($request->input('cidade_cobranca') != "-"){
            $cidade = $request->input('cidade_cobranca');
            $cidade = explode("-", $cidade);
            $cidade = $cidade[0];
            $request->merge([ 'cidade_cobranca_id' => $cidade]);
        }

        $result = $cliente->create($request->all());

        if($result){
            session()->flash("mensagem_sucesso", "Cliente cadastrado com sucesso!");
        }else{

            session()->flash('mensagem_erro', 'Erro ao cadastrar cliente!');
        }
        
        return redirect('/clientes');
    }

    public function edit($id){
        $cliente = new Cliente(); //Model
        $estados = Cliente::estados();
        $resp = $cliente
        ->where('id', $id)->first();  

        $cidades = Cidade::all();
        $pais = Pais::all();

        $grupos = GrupoCliente::
        where('empresa_id', $this->empresa_id)
        ->get();

        $acessores = Acessor::
        where('empresa_id', $this->empresa_id)
        ->get();

        $funcionarios = Funcionario::
        where('empresa_id', $this->empresa_id)
        ->get();

        
        if(valida_objeto($resp)){
            return view('clientes/register')
            ->with('pessoaFisicaOuJuridica', true)
            ->with('cidadeJs', true)
            ->with('cliente', $resp)
            ->with('pais', $pais)
            ->with('funcionarios', $funcionarios)
            ->with('estados', $estados)
            ->with('grupos', $grupos)
            ->with('acessores', $acessores)
            ->with('cidades', $cidades)
            ->with('title', 'Editar Cliente');
        }else{
            return redirect('/403');
        }

    }

    public function update(Request $request){
        $cliente = new Cliente();

        $id = $request->input('id');
        $resp = $cliente
        ->where('id', $id)->first(); 

        $request->merge([ 'ie_rg' => $request->ie_rg ? strtoupper($request->ie_rg) :
            'ISENTO']);
        $request->merge([ 'celular' => $request->celular ?? '']);

        $this->_validate($request);
        $limite = $request->limite_venda;
        $limite = str_replace(",", ".", $limite);

        $cidade = $request->input('cidade');
        $cidade = explode("-", $cidade);
        $cidade = $cidade[0];
        
        $resp->razao_social = strtoupper($request->input('razao_social'));
        $resp->nome_fantasia = strtoupper($request->input('nome_fantasia'));
        $resp->cpf_cnpj = $request->input('cpf_cnpj');
        $resp->ie_rg = $request->input('ie_rg');
        $resp->limite_venda = $limite;
        $resp->cidade_id = $cidade;

        $resp->rua = strtoupper($request->input('rua'));
        $resp->numero = strtoupper($request->input('numero'));
        $resp->bairro = strtoupper($request->input('bairro'));

        $resp->telefone = $request->input('telefone') ?? '';
        $resp->celular = $request->input('celular') ?? '';
        $resp->email = $request->input('email');
        $resp->cep = $request->input('cep');
        $resp->consumidor_final = $request->input('consumidor_final');
        $resp->contribuinte = $request->input('contribuinte');
        $resp->cod_pais = $request->input('cod_pais');
        $resp->id_estrangeiro = $request->input('id_estrangeiro');
        $resp->grupo_id = $request->input('grupo_id');

        $resp->rua_cobranca = $request->input('rua_cobranca') ?? '';
        $resp->bairro_cobranca = $request->input('bairro_cobranca') ?? '';
        $resp->numero_cobranca = $request->input('numero_cobranca') ?? '';
        $resp->cep_cobranca = $request->input('cep_cobranca') ?? '';
        $resp->acessor_id = $request->input('acessor_id');
        $resp->contador_nome = $request->input('contador_nome');
        $resp->contador_telefone = $request->input('contador_telefone');
        $resp->contador_email = $request->input('contador_email');
        $resp->data_aniversario = $request->input('data_aniversario');
        $resp->funcionario_id = $request->input('funcionario_id');
        $resp->observacao = $request->input('observacao') ?? '';

        if($request->input('cidade_cobranca') != '-'){
            $cidade = $request->input('cidade_cobranca');
            $cidade = explode("-", $cidade);
            $cidade = $cidade[0];
            $resp->cidade_cobranca_id = $cidade;
        }

        $result = $resp->save();
        if($result){
            session()->flash('mensagem_sucesso', 'Cliente editado com sucesso!');
        }else{
            session()->flash('mensagem_erro', 'Erro ao editar cliente!');
        }
        
        return redirect('/clientes'); 
    }

    public function delete($id){
        try{
            $cliente = Cliente
            ::where('id', $id)
            ->first();
            if(valida_objeto($cliente)){
                if($cliente->delete()){

                    session()->flash('mensagem_sucesso', 'Registro removido!');
                }else{

                    session()->flash('mensagem_erro', 'Erro!');
                }
                return redirect('/clientes');
            }
        }catch(\Exception $e){
            return view('errors.sql')
            ->with('title', 'Erro ao deletar cliente')
            ->with('motivo', 'Não é possivel remover clientes, presentes vendas ou pedidos!');
        }
    }

    private function _validate(Request $request){
        $doc = $request->cpf_cnpj;

        $rules = [
            'razao_social' => 'required|max:80',
            'nome_fantasia' => strlen($doc) > 14 ? 'required|max:80' : 'max:80',
            'cpf_cnpj' => [ 'required', new ValidaDocumento ],
            'rua' => 'required|max:80',
            'numero' => 'required|max:10',
            'bairro' => 'required|max:50',
            'telefone' => 'max:20',
            'celular' => 'max:20',
            'email' => 'max:40',
            'cep' => 'required|min:9',
            'cidade' => 'required',
            'consumidor_final' => 'required',
            'contribuinte' => 'required',
            'rua_cobranca' => 'max:80',
            'numero_cobranca' => 'max:10',
            'bairro_cobranca' => 'max:50',
            'cep_cobranca' => 'max:9'
        ];

        $messages = [
            'razao_social.required' => 'O Razão social/Nome é obrigatório.',
            'razao_social.max' => '50 caracteres maximos permitidos.',
            'nome_fantasia.required' => 'O campo Nome Fantasia é obrigatório.',
            'nome_fantasia.max' => '80 caracteres maximos permitidos.',
            'cpf_cnpj.required' => 'O campo CPF/CNPJ é obrigatório.',
            'cpf_cnpj.min' => strlen($doc) > 14 ? 'Informe 14 números para CNPJ.' : 'Informe 14 números para CPF.',
            'rua.required' => 'O campo Rua é obrigatório.',
            'rua.max' => '80 caracteres maximos permitidos.',
            'numero.required' => 'O campo Numero é obrigatório.',
            'cep.required' => 'O campo CEP é obrigatório.',
            'cep.min' => 'CEP inválido.',
            'cidade.required' => 'O campo Cidade é obrigatório.',
            'numero.max' => '10 caracteres maximos permitidos.',
            'bairro.required' => 'O campo Bairro é obrigatório.',
            'bairro.max' => '50 caracteres maximos permitidos.',
            'telefone.required' => 'O campo Telefone é obrigatório.',
            'telefone.max' => '20 caracteres maximos permitidos.',
            'consumidor_final.required' => 'O campo Consumidor final é obrigatório.',
            'contribuinte.required' => 'O campo Contribuinte é obrigatório.',
            'celular.max' => '20 caracteres maximos permitidos.',

            'email.required' => 'O campo Email é obrigatório.',
            'email.max' => '40 caracteres maximos permitidos.',
            'email.email' => 'Email inválido.',

            'rua_cobranca.max' => '80 caracteres maximos permitidos.',
            'numero_cobranca.max' => '10 caracteres maximos permitidos.',
            'bairro_cobranca.max' => '30 caracteres maximos permitidos.',
            'cep_cobranca.max' => '9 caracteres maximos permitidos.',

        ];
        $this->validate($request, $rules, $messages);
    }

    public function all(){
        $clientes = Cliente::all();
        $arr = array();
        foreach($clientes as $c){
            $arr[$c->id. ' - ' .$c->razao_social] = null;
                //array_push($arr, $temp);
        }
        echo json_encode($arr);
    }

    public function find($id){
        $cliente = Cliente::
        where('id', $id)
        ->first();
        
        echo json_encode($this->getCidade($cliente));
    }

    public function verificaLimite(Request $request){
        $cliente = Cliente::
        where('id', $request->id)
        ->first();

        $somaVendas = $this->somaVendasCredito($cliente);
        if($somaVendas != null){
            $cliente->soma = $somaVendas->total;
        }else{
            $cliente->soma = 0;
        }
        echo json_encode($cliente);
    }

    private function somaVendasCredito($cliente){
        return CreditoVenda::
        selectRaw('sum(vendas.valor_total) as total')
        ->join('vendas', 'vendas.id', '=', 'credito_vendas.venda_id')
        ->where('credito_vendas.cliente_id', $cliente->id)
        ->where('status', 0)
        ->first();
    }

    private function getCidade($transp){
        $temp = $transp;
        $transp['cidade'] = $transp->cidade;
        return $temp;
    }

    public function cpfCnpjDuplicado(Request $request){
        $cliente = Cliente::
        where('empresa_id', $request->empresa_id)
        ->where('cpf_cnpj', $request->cpf_cnpj)
        ->first();

        echo json_encode($cliente);
    }

    public function importacao(){
        $zip_loaded = extension_loaded('zip') ? true : false;
        if ($zip_loaded === false) {
            session()->flash('mensagem_erro', "Por favor instale/habilite o PHP zip para importar");
            return redirect()->back();
        }
        return view('clientes/importacao')
        ->with('title', 'Importação de clientes');
    }

    public function downloadModelo(){
        try{
            $public = getenv('SERVIDOR_WEB') ? 'public/' : '';
            return response()->download($public.'files/import_clients_csv_template.xlsx');
        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }

    public function importacaoStore(Request $request){

        if ($request->hasFile('file')) {

            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            $rows = Excel::toArray(new ProdutoImport, $request->file);
            $retornoErro = $this->validaArquivo($rows);

            if($retornoErro == ""){

                //armazenar no bd
                $teste = [];

                $cont = 0;

                foreach($rows as $row){
                    foreach($row as $key => $r){
                        if($r[0] != 'RAZÃO SOCIAL*'){
                            try{
                                $objeto = $this->preparaObjeto($r);

                                // print_r($objeto);
                                // die;

                                Cliente::create($objeto);
                                $cont++;
                            }catch(\Exception $e){
                                echo $cont;
                                echo $e->getMessage();
                                die;
                                session()->flash('mensagem_erro', $e->getMessage());
                                return redirect()->back();
                            }
                        }
                    }
                }

                session()->flash('mensagem_sucesso', "Clientes inseridos: $cont!!");
                return redirect('/clientes');

            }else{

                session()->flash('mensagem_erro', $retornoErro);
                return redirect()->back();
            }

        }else{
            session()->flash('mensagem_erro', 'Nenhum Arquivo!!');
            return redirect()->back();
        }

    }

    private function preparaObjeto($row){

        $cid = $row[7];
        $cidade = null;
        if(is_numeric($cid)){
            $cidade = Cidade::find($cid);
        }else{
            $uf = "";
            $temp = explode("-", $cid);
            if(isset($temp[1])){
                $uf = $temp[1];
                $cid = $temp[0];
            }
            if($uf != ""){

                $cidade = DB::select("select * from cidades where nome = '$cid' and uf = '$uf'");

                if($cidade == null){
                    $cidade = DB::select("select * from cidades where nome like '%$cid%' and uf = '$uf'");
                }

            }else{
                $cidade = DB::select("select * from cidades where nome = '$cid'");
                if($cidade == null){
                    $cidade = DB::select("select * from cidades where nome like '%$cid%'");
                }
            } 
            if($cidade != null){
                $cidade = $cidade[0]->id;
            }else{
                $cidade = NULL;
            }
        }


        $doc = $this->adicionaMascara($row[2]);

        $arr = [
            'razao_social' => $row[0],
            'nome_fantasia' => $row[1] ?? $row[0],
            'bairro' => $row[6],
            'numero' => $row[5],
            'rua' => $row[4],
            'cpf_cnpj' => $doc,
            'telefone' => $row[8] ?? '',
            'celular' => $row[9] ?? '',
            'email' => $row[10] ?? '',
            'cep' => $row[11],
            'ie_rg' => $row[3] ?? '',
            'consumidor_final' => 1,
            'limite_venda' => $row[12] != "" ? __replace($row[12]) : 0,
            'cidade_id' => $cidade != null ? $cidade : 1,
            'contribuinte' => 1,
            'rua_cobranca' => '',
            'numero_cobranca' => '',
            'bairro_cobranca' => '',
            'cep_cobranca' => '',
            'cidade_cobranca_id' => NULL,
            'empresa_id' => $this->empresa_id
        ];
        return $arr;

    }

    private function adicionaMascara($doc){
        if(strlen($doc) == 14){

            $cnpj = substr($doc, 0, 2);
            $cnpj .= ".".substr($doc, 2, 3);
            $cnpj .= ".".substr($doc, 5, 3);
            $cnpj .= "/".substr($doc, 8, 4);
            $cnpj .= "-".substr($doc, 12, 2);
            return $cnpj;
        }else{
            $cpf = substr($doc, 0, 3);
            $cpf .= ".".substr($doc, 3, 3);
            $cpf .= ".".substr($doc, 6, 3);
            $cpf .= "-".substr($doc, 9, 2);

            return $cpf;
        }
    }

    private function validaArquivo($rows){
        $cont = 0;
        $msgErro = "";
        foreach($rows as $row){
            foreach($row as $key => $r){

                $razaoSocial = $r[0];
                $cnpj = $r[2];
                $ie = $r[3];
                $rua = $r[4];
                $numero = $r[5];
                $bairro = $r[6];
                $cidade = $r[7];
                $cep = $r[11];

                if(strlen($razaoSocial) == 0){
                    $msgErro .= "Coluna razão social em branco na linha: $cont | "; 
                }

                if(strlen($cnpj) == 0){
                    $msgErro .= "Coluna cnpj/cpf em branco na linha: $cont | "; 
                }

                if(strlen($ie) == 0){
                    $msgErro .= "Coluna ie/rg em branco na linha: $cont"; 
                }

                if(strlen($rua) == 0){
                    $msgErro .= "Coluna rua em branco na linha: $cont"; 
                }

                if(strlen($numero) == 0){
                    $msgErro .= "Coluna numero em branco na linha: $cont"; 
                }

                if(strlen($bairro) == 0){
                    $msgErro .= "Coluna bairro em branco na linha: $cont"; 
                }

                if(strlen($cidade) == 0){
                    $msgErro .= "Coluna cidade em branco na linha: $cont"; 
                }

                if(strlen($cep) == 0){
                    $msgErro .= "Coluna cep em branco na linha: $cont"; 
                }

                if($msgErro != ""){
                    return $msgErro;
                }

                $cont++;
            }

        }

        return $msgErro;
    }

    public function consultaCadastrado($doc){
        $doc = str_replace("_", "/", $doc);
        $cliente = Cliente::
        where('cpf_cnpj', $doc)
        ->where('empresa_id', $this->empresa_id)
        ->first();

        return response()->json($cliente, 200);
    }

    public function quickSave(Request $request){
        try{
            $data = $request->data;

            $cli = [
                'razao_social' => $data['razao_social'],
                'nome_fantasia' => $data['razao_social'],
                'bairro' => $data['bairro'] ?? '',
                'numero' => $data['numero'] ?? '',
                'rua' => $data['rua'] ?? '',
                'cpf_cnpj' => $data['cpf_cnpj'] ?? '',
                'telefone' => $data['telefone'] ?? '',
                'celular' => $data['celular'] ?? '',
                'email' => $data['email'] ?? '',
                'cep' => $data['cep'] ?? '',
                'ie_rg' => $data['ie_rg'] ?? '',
                'consumidor_final' => $data['consumidor_final'] ?? 1,
                'limite_venda' => 0,
                'cidade_id' => $data['cidade_id'] ?? 1, 
                'contribuinte' => $data['contribuinte'] ?? 1,
                'rua_cobranca' => '',
                'numero_cobranca' => '',
                'bairro_cobranca' => '',
                'cep_cobranca' => '', 
                'empresa_id' => $this->empresa_id, 
                'cidade_cobranca_id' => NULL
            ];

            $res = Cliente::create($cli);
            return response()->json($res, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }
}
