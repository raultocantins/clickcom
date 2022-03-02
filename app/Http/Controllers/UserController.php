<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\UsuarioAcesso;
use App\Models\ConfigNota;
use App\Models\Empresa;
use App\Models\CategoriaConta;
use App\Models\Plano;
use App\Models\Contrato;
use App\Models\EmpresaContrato;
use App\Models\PlanoEmpresa;
use App\Models\Categoria;
use App\Models\NaturezaOperacao;
use App\Models\Cliente;
use App\Models\Tributacao;
use App\Models\PerfilAcesso;
use App\Helpers\Menu;
use Mail;
use Dompdf\Dompdf;
use Illuminate\Support\Str;
use NFePHP\Common\Certificate;

class UserController extends Controller
{

  public function newAccess(){
    $sessaoAtiva = $this->sessaoAtiva();

    $loginCookie = (isset($_COOKIE['CookieLogin'])) ? 
    base64_decode($_COOKIE['CookieLogin']) : '';
    $senhaCookie = (isset($_COOKIE['CookieSenha'])) ? 
    base64_decode($_COOKIE['CookieSenha']) : '';
    $lembrarCookie = (isset($_COOKIE['CookieLembrar'])) ? 
    $_COOKIE['CookieLembrar'] : '';

    $planos = Plano::all();

    return view('login/'.(getenv("PAG_LOGIN") != null ? getenv("PAG_LOGIN") : 'access'))
    ->with('loginCookie', $loginCookie)
    ->with('senhaCookie', $senhaCookie)
    ->with('lembrarCookie', $lembrarCookie)
    ->with('planos', $planos)
    ->with('sessaoAtiva', $sessaoAtiva);
  }

  private function sessaoAtiva(){
    $value = session('user_logged');
    if($value){
      $acesso = UsuarioAcesso::
      where('usuario_id', $value['id'])
      ->where('status', 0)
      ->first();

      if($acesso == null) return false;

      if($acesso->hash == $value['hash']) return true;
    }else{
      return null;
    }
  }

  public function request(Request $request){
    $login = $request->input('login');
    $senha = $request->input('senha');

    $user = new Usuario();

    $usr = $user
    ->where('login', $login)
    ->where('senha', md5($senha))
    ->first();

    $lembrar = $request->lembrar;

    if($lembrar){
      $expira = time() + 60*60*24*30;
      setCookie('CookieLogin', base64_encode($login), $expira);
      setCookie('CookieSenha', base64_encode($senha), $expira);
      setCookie('CookieLembrar', 1, $expira);
    }else{
      setCookie('CookieLogin');
      setCookie('CookieSenha');
      setCookie('CookieLembrar');
    }

    if($usr != null){

      $planoExpirado = false;
      $planoExpiradoDias = 0;
      $empresa = $usr->empresa;
      
      if($usr->ativo == 0){
        session()->flash('mensagem_login', 'Usuário desativado');
        return redirect('/login');
      }

    //   if($login != getenv("USERMASTER")){
      if(!isSuper($login)){
        if($usr->empresa->status == 0){
          if($usr->empresa->mensagem_bloqueio != ""){
            session()->flash('mensagem_login', $usr->empresa->mensagem_bloqueio);
          }else{
            session()->flash('mensagem_login', 'Empresa desativada');
          }
          return redirect('/login');
        }

        if(!$empresa->planoEmpresa){
          session()->flash('mensagem_login', 'Empresa sem plano atribuido!!');
          return redirect('/login');
        }

        $hoje = date('Y-m-d');
        $exp = $empresa->planoEmpresa ? $empresa->planoEmpresa->expiracao : null;
        $dif = strtotime($exp) - strtotime($hoje);
        $planoExpiradoDias = $dif/60/60/24;

        if(strtotime($hoje) > strtotime($exp) && $empresa->planoEmpresa->expiracao != '0000-00-00'){

          $config = ConfigNota::where('empresa_id', $usr->empresa->id)->first();
          if($config == null){
            session()->flash("mensagem_login", "Plano expirado e sem emissor cadastrado, entre em contato com suporte!");
            return redirect('/login');
          }

          $planoExpirado = true;
        }
      }

      $config = ConfigNota::
      where('empresa_id', $usr->empresa_id)
      ->first();
      $ambiente = 'Não configurado';
      if($config != null){
        $ambiente = $config->ambiente == 1 ? 'Produção' : 'Homologação'; 
      }

      $hash = Str::random(20);

      $session = [
        'id' => $usr->id,
        'nome' => $usr->nome,
        'adm' => $usr->adm,
        'ambiente' => $ambiente,
        'empresa' => $usr->empresa_id,
        'delivery' => getenv("DELIVERY") == 1 || getenv("DELIVERY_MERCADO") == 1 ? true : false,
        'super' => isSuper($login),
        'empresa_nome' => $usr->empresa->nome,
        'tipo_representante' => $usr->empresa->tipo_representante,
        'hash' => $hash,
        'ip_address' => $this->get_client_ip()
      ];

      if(!isSuper($login)){
        $exp = $empresa->planoEmpresa ? $empresa->planoEmpresa->expiracao : null;
        $hoje = date('Y-m-d');
        $dif = strtotime($exp) - strtotime($hoje);
        $dias = $dif/60/60/24;

        if($dias <= getenv("ALERTA_PAGAMENTO_DIAS")){
          if($empresa->planoEmpresa->expiracao != '0000-00-00'){
            session()->flash('mensagem_pagamento', "Faça o pagamento do plano, faltam $dias dia(s) para expirar");
          }
        }

      }

      if($empresa->certificado != null){

        $certifiadoDiasExpira = $this->expiraCertificado($empresa);

        if($certifiadoDiasExpira <= getenv("ALERTA_VENCIMENTO_CERTIFICADO") && $certifiadoDiasExpira != -1){

          if($certifiadoDiasExpira <= 0){
            session()->flash('mensagem_certificado', "Certificado Digital Vencido");
          }else{
            session()->flash('mensagem_certificado', "Faltam $certifiadoDiasExpira dia(s) para expirar seu certificado digital");
          }
        }
      }

      $sessaoAtiva = $this->getSessaoAtiva($usr->id, $empresa->id);

      if($sessaoAtiva){
        session()->flash('mensagem_login', 'Já existe uma sessão ativa com outro usuário IP: '. $sessaoAtiva->ip_address . ' - Login as : ' . \Carbon\Carbon::parse($sessaoAtiva->created_at)->format('H:i:s'));

        return redirect("/login");
      }

      UsuarioAcesso::create(
        [
          'usuario_id' => $usr->id,
          'status' => 0,
          'hash' => $hash,
          'ip_address' => $session['ip_address']
        ]
      );

      session(['user_logged' => $session]);
     
      if($request->uri == ""){
        return redirect('/' . getenv('ROTA_INICIAL'));
      }else{
        return redirect($request->uri);
      }
      
    
    }else{
    //   __set($request);

      session()->flash('mensagem_login', 'Credencial(s) incorreta(s)!');
      return redirect('/login')->with('login', $login);
    }
  }

  private function expiraCertificado($empresa){
    try{
      if($empresa->certificado){
        $certificado = $empresa->certificado;
        $infoCertificado = Certificate::readPfx($certificado->arquivo, $certificado->senha);
        $publicKey = $infoCertificado->publicKey;
        $expiracao = $publicKey->validTo->format('Y-m-d');
        $dataHoje = date('Y-m-d');

        $dif = strtotime($expiracao) - strtotime($dataHoje);
        $dias = $dif/60/60/24;
        return $dias;
      }

      return -1;
    }catch(\Exception $e){
      return -1;
    }
  }

  private function getSessaoAtiva($id, $empresa_id){
    $acesso = UsuarioAcesso::
    select('usuario_acessos.*')
    ->join('usuarios', 'usuarios.id' , '=', 'usuario_acessos.usuario_id')
    ->where('usuario_id', $id)
    ->where('status', 0)
    ->where('empresa_id', $empresa_id)
    ->orderBy('id', 'desc')
    ->first();

    if(!$acesso) return false;
    $agora = date('Y-m-d H:i:s');
    $dif = strtotime($agora) - strtotime($acesso->updated_at);
    $minutos = $dif/60;

    if($minutos > getenv("SESSION_LOGIN")){
      return false;
    }else{
      return $acesso;
    }
  }

  private function get_client_ip() {
    $ipaddress = '';
    // if (isset($_SERVER['HTTP_CLIENT_IP']))
    //   $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    // else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    //   $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    // else if(isset($_SERVER['HTTP_X_FORWARDED']))
    //   $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    // else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
    //   $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    // else if(isset($_SERVER['HTTP_FORWARDED']))
    //   $ipaddress = $_SERVER['HTTP_FORWARDED'];
    // else if(isset($_SERVER['REMOTE_ADDR']))
    //   $ipaddress = $_SERVER['REMOTE_ADDR'];
    // else

    if($_SERVER['HTTP_DO_CONNECTING_IP']){
      $ipaddress=$_SERVER['HTTP_DO_CONNECTING_IP'];
    }else{
      $ipaddress = 'UNKNOWN';
    }

  

     
    return $ipaddress;
  }

  public function logoff(){
    $value = session('user_logged');

    if($value){
      $usuarioSessao = UsuarioAcesso::
      where('usuario_id', $value['id'])
      ->where('status', 0)
      ->get();

      foreach($usuarioSessao as $u){
        $u->status = 1;
        $u->save();
      }
    }
    session()->forget('user_logged');
    session()->flash('mensagem_login', 'Logoff realizado.');
    return redirect("/login");
  }

  public function plano(){
    $planos = Plano::
    where('visivel', true)
    ->get();
    return view('login/plano')
    ->with('planos', $planos);
  }

  public function cadastro(Request $request){

    if(!$request->plano){
      return redirect('/plano');
    }

    $p = Plano::find($request->plano);

    if($p == null){
      return redirect('/plano');
    }

    $planos = Plano::all();
    return view('login/cadastro')
    ->with('planos', $planos)
    ->with('plano', $request->plano);
  }

  private function permissoesTodas(){
    $menu = new Menu();
    $menu = $menu->getMenu();
    $temp = [];
    foreach($menu as $m){
      foreach($m['subs'] as $s){
        array_push($temp, $s['rota']);
      }
    }

    return $temp;

  }

  public function salvarEmpresa(Request $request){

    $usr = Usuario::where('login', $request->usuario)->first();
    // if($usr != null){
    //   session()->flash("mensagem_erro", "Já existe um cadastro com este usuário, informe outro por gentileza!");
    //   return redirect()->back();
    // }
    $this->_validate($request);

    $planoAutomaticoNome = getenv("PLANO_AUTOMATICO_NOME");

    $plano = Plano::where('nome', $planoAutomaticoNome)->first();

    if($request->plano > 0){
      $plano = Plano::find($request->plano);
      $perfil = PerfilAcesso::find($plano->perfil_id);
      $permissoesTodas = $perfil->permissao ?? '[]';
    }else{
      $permissoesTodas = json_encode($this->permissoesTodas());
    }

    $data = [
      'nome' => $request->nome_empresa,
      'rua' => '',
      'numero' => '',
      'bairro' => '',
      'cidade' => $request->cidade,
      'telefone' => $request->telefone,
      'email' => $request->email,
      'cnpj' => $request->cnpj,
      'status' => 1,
      'permissao' => $permissoesTodas
    ];



    $empresa = Empresa::create($data);
    if(getenv("AVISO_EMAIL_NOVO_CADASTRO") != ""){
      Mail::send('mail.nova_empresa', ['data' => $data], function($m){
        $nomeEmail = getenv('MAIL_NAME');
        $nomeEmail = str_replace("_", " ", $nomeEmail);
        $m->from(getenv('MAIL_USERNAME'), $nomeEmail);
        $m->subject('Nova empresa cadastrada');
        $m->to(getenv("AVISO_EMAIL_NOVO_CADASTRO"));
      });
    }

    $data = [
      'nome' => $request->login, 
      'senha' => md5($request->senha),
      'login' => $request->login,
      'adm' => 1,
      'img' => '',
      'ativo' => 1,
      'email' => $request->email,
      'empresa_id' => $empresa->id,
      'permissao' => $permissoesTodas
    ];

    $usuario = Usuario::create($data);

    CategoriaConta::create([
      'nome' => 'Compras',
      'empresa_id' => $empresa->id,
      'tipo'=> 'pagar'
    ]);
    CategoriaConta::create([
      'nome' => 'Vendas',
      'empresa_id' => $empresa->id,
      'tipo'=> 'receber'
    ]);

    $planoPagamento = getenv("PLANO_PAGAMENTO_DIAS");
    
    if(getenv("HERDAR_DADOS_SUPER") == 1){
      $this->herdaSuper($empresa);
    }

    if($plano != null){

      $contrato = $this->gerarContrato($empresa->id);

      session()->flash("mensagem_sucesso", "Bem vindo ao nosso sistema, obrigado por se cadastrar :)");
      $this->setarPlano($empresa, $plano);
      $this->criaSessao($usuario);
      return redirect('/' . getenv('ROTA_INICIAL'));

    }
    else if($planoPagamento > 0){
      $plano = Plano::find($request->plano);
      if($plano != null){
        session()->flash("mensagem_sucesso", "Bem vindo ao nosso sistema, obrigado por se cadastrar :)");
        $this->setarPlano($empresa, $plano);
        $this->criaSessao($usuario);
        return redirect('/' . getenv('ROTA_INICIAL'));

      }else{
        session()->flash("mensagem_login", "Erro inesperado!!");
        return redirect('/login');
      }
    }

    else{
      session()->flash("mensagem_sucesso", "Obrigado por se cadastrar, aguarde a ativação do cadastro!");
      return redirect('/login');
    }

  }

  private function setarPlano($empresa, $plano){
    $dias = getenv("PLANO_AUTOMATICO_DIAS");
    $exp = date('Y-m-d', strtotime("+$dias days",strtotime( 
      date('Y-m-d'))));
    $data = [
      'empresa_id' => $empresa->id,
      'plano_id' => $plano->id,
      'expiracao' => $exp
    ];

    PlanoEmpresa::create($data);
  }

  private function criaSessao($usr){
    $ambiente = 'Não configurado';

    $hash = Str::random(20);
    $session = [
      'id' => $usr->id,
      'nome' => $usr->nome,
      'adm' => $usr->adm,
      'ambiente' => $ambiente,
      'empresa' => $usr->empresa_id,
      'empresa_nome' => $usr->empresa->nome,
      'super' => 0,
      'tipo_representante' => false,
      'hash' => $hash,
      'ip_address' => $this->get_client_ip()
    ];

    UsuarioAcesso::create(
      [
        'usuario_id' => $usr->id,
        'status' => 0,
        'hash' => $hash,
        'ip_address' => $session['ip_address']
      ]
    );
    session(['user_logged' => $session]);
  }

  private function _validate(Request $request){

    $rules = [
      'nome_empresa' => 'required|min:3',
      'telefone' => 'required|min:12',
      'cidade' => 'required|min:3',
      'login' => 'required|min:5|unique:usuarios',
      'senha' => 'required|min:5',
      'email' => 'required|email',
      'cnpj' => 'required|unique:empresas',
    ];

    $messages = [
      'nome_empresa.required' => 'Campo obrigatório.',
      'cnpj.required' => 'Campo obrigatório.',
      'cidade.required' => 'Campo obrigatório.',
      'telefone.required' => 'Campo obrigatório.',
      'login.required' => 'Campo obrigatório.',
      'senha.required' => 'Campo obrigatório.',
      'email.required' => 'Campo obrigatório.',
      'nome_empresa.min' => 'Minimo de 3 caracteres.',
      'telefone.min' => 'Informe telefone corretamente.',
      'cidade.min' => 'Minimo de 3 caracteres.',
      'login.min' => 'Minimo de 5 caracteres.',
      'senha.min' => 'Minimo de 5 caracteres.',
      'email.email' => 'Informe um email válido.',
      'login.unique' => 'Usuário já cadastrado em nosso sistema.',
      'cnpj.unique' => 'Documento já cadastrado em nosso sistema.'
    ];
    $this->validate($request, $rules, $messages);
  }

  public function gerarContrato($empresa_id){
    try{
      $contrato = Contrato::first();

      $empresa = Empresa::find($empresa_id);

      $texto = $this->preparaTexto($contrato->texto, $empresa);

      $domPdf = new Dompdf(["enable_remote" => true]);
      $domPdf->loadHtml($texto);

      $pdf = ob_get_clean();

      $domPdf->setPaper("A4");
      $domPdf->render();

      $output = $domPdf->output();
      $cnpj = str_replace("/", "", $empresa->cnpj);
      $cnpj = str_replace(".", "", $cnpj);
      $cnpj = str_replace("-", "", $cnpj);
      $cnpj = str_replace(" ", "", $cnpj);


      if(!is_dir(public_path('contratos'))){
        mkdir(public_path('contratos'), 0777, true);
      }
      file_put_contents(public_path('contratos/'.$cnpj.'.pdf'), $output);

      EmpresaContrato::create(
        [
          'empresa_id' => $empresa->id, 'status' => 0
        ]
      );
      return true;
    }catch(\Exception $e){
      echo $e->getMessage();
      return false;
    }
  }

  private function preparaTexto($texto, $empresa){
    $texto = str_replace("{{nome}}", $empresa->nome, $texto);
    $texto = str_replace("{{rua}}", $empresa->rua, $texto);
    $texto = str_replace("{{numero}}", $empresa->numero, $texto);
    $texto = str_replace("{{bairro}}", $empresa->bairro, $texto);
    $texto = str_replace("{{email}}", $empresa->email, $texto);
    $texto = str_replace("{{cnpj}}", $empresa->cnpj, $texto);
    $texto = str_replace("{{data}}", date("d/m/Y H:i"), $texto);

    return $texto;
  }

  public function recuperarSenha(Request $request){
    $email = $request->email;
    $usuario = Usuario::where('email', $email)->first();

    if($usuario == null){
      session()->flash("mensagem_login", "Email não encontrado!!");
      return redirect('/login');
    }

    try{
      $novaSenha = rand(10000, 99999);

      $usuario->senha = md5($novaSenha);
      $usuario->save();
      Mail::send('mail.nova_senha_painel', ['usuario' => $usuario, 'novaSenha' => $novaSenha], function($m) use($usuario){
        $nomeEmail = getenv('MAIL_NAME');
        $nomeEmail = str_replace("_", " ", $nomeEmail);
        $m->from(getenv('MAIL_USERNAME'), $nomeEmail);
        $m->subject('Recuperação de senha');
        $m->to($usuario->email);
        session()->flash("mensagem_sucesso", "Uma nova senha foi enviada para o email informado!!");
      });

    }catch(\Excption $e){
      session()->flash("mensagem_login", "Erro ao enviar email de redefinição!!");
    }
    return redirect('/login');
  }

  private function herdaSuper($novaEmpresa){
    $usuario = Usuario::
    where('login', getSuper())
    ->first();
    if($usuario){
      $empresaId = $usuario->empresa->id;

      $categorias = Categoria::
      where('empresa_id', $empresaId)
      ->get();

      foreach($categorias as $c){
        $c->empresa_id = $novaEmpresa->id;

        $cat = $c->toArray();
        unset($cat['id']);
        unset($cat['created_at']);
        unset($cat['updated_at']);
        Categoria::create($cat);
      }

      $naturezas = NaturezaOperacao::
      where('empresa_id', $empresaId)
      ->get();

      foreach($naturezas as $c){
        $c->empresa_id = $novaEmpresa->id;

        $nat = $c->toArray();
        unset($nat['id']);
        unset($nat['created_at']);
        unset($nat['updated_at']);
        NaturezaOperacao::create($nat);
      }

      $tributacao = Tributacao::
      where('empresa_id', $empresaId)
      ->first();

      if($tributacao != null){

        $tributacao->empresa_id = $novaEmpresa->id;

        $trib = $tributacao->toArray();
        unset($trib['id']);
        unset($trib['created_at']);
        unset($trib['updated_at']);
        Tributacao::create($nat);
      }

      $clientes = Cliente::
      where('empresa_id', $empresaId)
      ->get();

      foreach($clientes as $c){
        $c->empresa_id = $novaEmpresa->id;

        $cli = $c->toArray();
        unset($cli['id']);
        unset($cli['created_at']);
        unset($cli['updated_at']);

        Cliente::create($cli);
      }

    }
  }

}
