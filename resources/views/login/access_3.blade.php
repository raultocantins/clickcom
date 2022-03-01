<!doctype html>
<html lang="en">
<head>
  <title>Login</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  
  <link rel="stylesheet" href="/login3/css/style.css">

</head>
<body>
  <section class="ftco-section">
    <div class="container">

      <div class="row justify-content-center">
        <div class="col-md-12 col-lg-10">
          <div class="wrap d-md-flex">
            <div class="text-wrap p-4 p-lg-5 text-center d-flex align-items-center order-md-last">
              <div class="text w-100">
                <h2>{{getenv("APP_NAME")}}</h2>
                <p>{{getenv("APP_DESC")}}</p>
                <a href="/cadastro" class="btn btn-white btn-outline-white">Quero cadastrar minha empresa</a>
              </div>
            </div>

            @if(session()->has('mensagem_sucesso'))
            <span style="width: 100%;" class="label label-xl label-inline label-light-success">{{ session()->get('mensagem_sucesso') }}</span>
            @endif

            @if(!$sessaoAtiva)
            <input type="hidden" value="{{session('uri')}}" name="uri">
            
            <div class="login-wrap p-4 p-lg-5">

              <div class="d-flex">
                <div class="w-100">
                  <h3 class="mb-4">Login</h3>
                </div>
                
              </div>
              <form method="post" action="/login/request" class="signin-form" id="kt_login_signin_form">
                @csrf
                <div class="form-group mb-3">
                  <label class="label" for="name">Login</label>
                  <input type="text" autocomplete="off" class="form-control" autofocus @if(session('login') != null) value="{{ session('login') }}" @else @if(isset($loginCookie)) value="{{$loginCookie}}" @endif @endif name="login">
                </div>
                <div class="form-group mb-3">
                  <label class="label" for="password">Senha</label>
                  <input type="password" class="form-control" placeholder="Password" name="senha" autocomplete="off" @if(isset($senhaCookie)) value="{{$senhaCookie}}" @endif>
                </div>
                <div class="w-50 text-left">
                  <label class="checkbox-wrap checkbox-primary mb-0">Lembrar-me
                    <input type="checkbox" name="lembrar" @isset($lembrarCookie) @if($lembrarCookie == true) checked @endif @endif>
                    <span class="checkmark"></span>
                  </label>
                </div>
                <div class="form-group">
                  <button type="submit" class="form-control btn btn-primary submit px-3">Login</button>
                </div>
                <div class="form-group d-md-flex">
                  <button style="width: 100%;" type="button" class="btn btn-outline-warning" id="btn-esqueci">
                    Esqueci minha senha
                  </button>
                </div>

              </form>

              @if(session()->has('mensagem_login'))
              <p class="text-danger">{{ session()->get('mensagem_login') }}</p>
              @endif
              <form class="form" method="post" action="/recuperarSenha">
                @csrf
                <div id="div-senha" class="animate__animated animate__backInDown" style="display: none">
                  <div class="form-group">
                    <label class="font-size-h6 font-weight-bolder text-dark">Email</label>
                    <input name="email" class="form-control" type="email" autocomplete="off" />
                  </div>

                  <div class="text-center pt-2">
                    <button type="submit" id="kt_login_signin_submit" class="form-control btn btn-primary submit px-3">Enviar</button>
                  </div>
                </div>
              </form>
            </div>

            @else
            <div class="login-wrap p-4 p-lg-5">

              <div class="d-flex">
                <div class="w-100">
                  <h3 class="mb-4">Login</h3>
                </div>
                
              </div>
              <div class="text-center pt-2">
                <a id="" href="{{'/' . getenv('ROTA_INICIAL')}}" class="btn btn-dark btn-block font-weight-bolder font-size-h6 px-8 py-4 my-3">Acessar Painel</a>
              </div>
            </div>
            @endif

          </div>
          <a target="_blank" class="txt2" href="http://wa.me/55{{getenv('CONTATO_SUPORTE')}}">
            <i class="fa fa-whatsapp" aria-hidden="true"></i>
            Suporte {{getenv("CONTATO_SUPORTE")}}
          </a>
        </div>
        
      </div>

    </div>
  </div>
</section>

<script src="/login3/js/jquery.min.js"></script>
<script src="/login3/js/popper.js"></script>
<script src="/login3/js/bootstrap.min.js"></script>
<script src="/login3/js/main.js"></script>

<script type="text/javascript">
  $('#btn-esqueci').click(() => {
    $('#div-senha').css('display', 'block')
    $('#kt_login_signin_form').css('display', 'none')
    $('#btn-esqueci').css('display', 'none')
  })
</script>

</body>
</html>

