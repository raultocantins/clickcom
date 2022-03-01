<!DOCTYPE html>

<html lang="br">
<!-- begin::Head -->

<head>
  <meta charset="utf-8" />

  <title>Login</title>
  <meta name="description" content="Updates and statistics">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!--begin::Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">

  <link href="/metronic/css/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
  <!-- <link href="/metronic/css/uppy.bundle.css" rel="stylesheet" type="text/css" /> -->
  <link href="/metronic/css/wizard.css" rel="stylesheet" type="text/css" />

  <link href="/css/style.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="/css/whatsapp.css">

  <!--end::Page Vendors Styles -->


  <!--begin::Global Theme Styles(used by all pages) -->
  <link href="/metronic/css/plugins.bundle.css" rel="stylesheet" type="text/css" />
  <link href="/metronic/css/prismjs.bundle.css" rel="stylesheet" type="text/css" />
  <link href="/metronic/css/style.bundle.css" rel="stylesheet" type="text/css" />

  <link href="/metronic/css/pricing.css" rel="stylesheet" type="text/css" />
  <!--end::Global Theme Styles -->

  <!--begin::Layout Skins(used by all pages) -->

  <link href="/metronic/css/light.css" rel="stylesheet" type="text/css" />
  <link href="/metronic/css/light-menu.css" rel="stylesheet" type="text/css" />
  <link href="/metronic/css/dark-brand.css" rel="stylesheet" type="text/css" />
  <link href="/metronic/css/dark-aside.css" rel="stylesheet" type="text/css" />

  <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

  <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

  <link rel="shortcut icon" href="/../../imgs/logo1.png" />

  <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
  
  <script>
    (function(h, o, t, j, a, r) {
      h.hj = h.hj || function() {
        (h.hj.q = h.hj.q || []).push(arguments)
      };
      h._hjSettings = {
        hjid: 1070954,
        hjsv: 6
      };
      a = o.getElementsByTagName('head')[0];
      r = o.createElement('script');
      r.async = 1;
      r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
      a.appendChild(r);
    })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
  </script>
  <!-- Global site tag (gtag.js) - Google Analytics -->

  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'UA-37564768-1');
  </script>


  <style type="text/css">
    .select2-selection__arroww:before {
      content: "";
      position: absolute;
      right: 7px;
      top: 42%;
      border-top: 5px solid #888;
      border-left: 4px solid transparent;
      border-right: 4px solid transparent;
    }


    @media (max-width : 520px) {
      .stk{
        height: 0px;
        visibility: hidden;
      }
    }
  </style>
</head>


<!-- end::Head -->

<!-- begin::Body -->

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">


  <div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-2 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login" style="margin-top: -50px;">
      <!--begin::Aside-->
      <div class="login-aside order-2 order-lg-1 d-flex flex-row-auto position-relative overflow-hidden">
        <!--begin: Aside Container-->
        <div class="d-flex flex-column-fluid flex-column justify-content-between py-9 px-7 py-lg-13 px-lg-35">
          <!--begin::Logo-->
          <a href="#" class="text-center pt-2">
            <img src="/imgs/logo1.png" class="max-h-200px" alt="" />
          </a>
          <!--end::Logo-->
          <!--begin::Aside body-->
          <div class="d-flex flex-column-fluid flex-column flex-center">
            <!--begin::Signin-->
            <div class="login-form login-signin py-11">
              <!--begin::Form-->
              <form method="post" action="/login/request" class="form animate__animated animate__backInDown" novalidate="novalidate" id="kt_login_signin_form">
                @csrf
                <!--begin::Title-->
                <div class="text-center pb-8">
                  <h2 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg">Acesso ao Painel</h2>

                </div>

                @if(session()->has('mensagem_sucesso'))
                <span style="width: 100%;" class="label label-xl label-inline label-light-success">{{ session()->get('mensagem_sucesso') }}</span>
                @endif
                <!--end::Title-->
                <!--begin::Form group-->

                @if(!$sessaoAtiva)

                <input type="hidden" value="{{session('uri')}}" name="uri">
                <div class="form-group">
                  <label class="font-size-h6 font-weight-bolder text-dark">Login</label>
                  <input autofocus @if(session('login') != null) value="{{ session('login') }}" @else @if(isset($loginCookie)) value="{{$loginCookie}}" @endif @endif name="login" class="form-control form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="username" autocomplete="off" />
                </div>
                <!--end::Form group-->
                <!--begin::Form group-->
                <div class="form-group">
                  <div class="d-flex justify-content-between mt-n5">
                    <label class="font-size-h6 font-weight-bolder text-dark pt-5">Senha</label>

                  </div>
                  <input name="senha" class="form-control form-control-solid h-auto py-7 px-6 rounded-lg" type="password" autocomplete="off" @if(isset($senhaCookie)) value="{{$senhaCookie}}" @endif />
                </div>

                <label class="checkbox checkbox-inline checkbox-primary">
                  <input type="checkbox" name="lembrar" @isset($lembrarCookie) @if($lembrarCookie == true) checked @endif @endif/>
                  <span></span> 
                  <b style="margin-left: 3px;">Lembrar-me</b>
                </label> 

                @if(session()->has('mensagem_login'))
                <p class="text-danger">{{ session()->get('mensagem_login') }}</p>
                @endif

                <!--end::Form group-->
                <!--begin::Action-->
                <div class="text-center pt-2">
                  <button id="kt_login_signin_submit" class="btn btn-dark btn-block font-weight-bolder font-size-h6 px-8 py-4 my-3">Login</button>
                </div>

                <a class="btn btn-outline-success btn-block" href="/cadastro">
                  <i class="la la-check"></i>
                  Quero cadastrar minha empresa
                </a>

                @else
                <div class="text-center pt-2">
                  <a id="" href="{{'/' . getenv('ROTA_INICIAL')}}" class="btn btn-dark btn-block font-weight-bolder font-size-h6 px-8 py-4 my-3">Painel</a>
                </div>
                @endif


                <!--end::Action-->
              </form>
              <!--end::Form-->
            </div>

            <form class="form" method="post" action="/recuperarSenha">
              @csrf
              <button id="btn-esqueci" type="button" class="btn btn-outline-danger btn-block">
                <i class="la la-refresh"></i>
                Esqueci minha senha
              </button>

              <div id="div-senha" class="animate__animated animate__backInDown" style="display: none">
                <div class="form-group">
                  <label class="font-size-h6 font-weight-bolder text-dark">Email</label>
                  <input name="email" class="form-control form-control-solid h-auto py-7 px-12 rounded-lg" type="email" autocomplete="off" />
                </div>

                <div class="text-center pt-2">
                  <button id="kt_login_signin_submit" class="btn btn-dark btn-block font-weight-bolder font-size-h6 px-8 py-4 my-3">Enviar</button>
                </div>
              </div>
            </form>
            <!--end::Signin-->
            <!--begin::Signup-->

            <!--end::Signup-->
            <!--begin::Forgot-->
            <div class="login-form login-forgot pt-11">
              <!--begin::Form-->
              <a target="_blank" class="txt2" href="http://wa.me/55{{getenv('CONTATO_SUPORTE')}}">
                <i class="fa fa-whatsapp" aria-hidden="true"></i>
                Suporte {{getenv("CONTATO_SUPORTE")}}

              </a>
              <!--end::Form-->
            </div>
            <!--end::Forgot-->
          </div>
          <!--end::Aside body-->

          <!--end: Aside footer for desktop-->
        </div>
        <!--end: Aside Container-->
      </div>
      <!--begin::Aside-->
      <!--begin::Content-->
      <div class="content order-1 order-lg-2 d-flex flex-column w-100 pb-0 stk" style="background-color: #FFF">
        <!--begin::Title-->
        <div class="d-flex flex-column justify-content-center text-center pt-lg-40 pt-md-5 pt-sm-5 px-lg-0 pt-5 px-7">
          <h3 class="display4 font-weight-bolder my-7 text-dark" style="color: #986923;">{{getenv("APP_NAME")}}</h3>
          <p class="font-weight-bolder font-size-h2-md font-size-lg text-dark opacity-70">{{getenv("APP_DESC")}}</p>
        </div>
        <!--end::Title-->
        <!--begin::Image-->
        <div class="content-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center" style="background-image: url(/imgs/performance.gif); margin-bottom: 100px;"></div>
        <!--end::Image-->
      </div>
      <!--end::Content-->
    </div>
    <div id="box_whatsapp" class="wcard">
      <div class="wcard-header">
        <div class="wcard-logo">
          <img src="/imgs/logo1.png" alt="Nome da empresa">
        </div>
        <div class="wcard-title">
          <h6>{{getenv("APP_NAME")}}</h6>
          <p><small>{{getenv("APP_DESC")}}</small></p>
          <p><small class="text-success">Online</small></p>
        </div>
      </div>
      <div class="wcard-body">
        <div id="form_whatsapp">
          <div class="wcard-campo">
            <label for="w_nome">Diga-nos seu nome:</label>
            <input type="text" name="w_nome" id="w_nome">
          </div>
          <div class="wcard-footer">
            <div class="wcard-mensagem">
              <textarea name="w_mensagem" id="w_mensagem" rows="1" placeholder="Digite sua mensagem"></textarea>
            </div>
            <div class="wcard-send">
              <button id="send-whats"><i class="la la-paper-plane"></i></button>
            </div>
          </div>
        </div>
      </div>
    </div>

    @if(getenv("CONTATO_SUPORTE") != "")
    <button id="btn_whatsapp" class="btn-whatsapp">
      <i class="icone-whatsapp lab la-whatsapp"></i>
    </button>
    @endif
    <!--end::Login-->
  </div>
  <script>var HOST_URL = "/metronic/theme/html/tools/preview";</script>
  <script>
    var KTAppSettings = {
      "breakpoints": {
        "sm": 576,
        "md": 768,
        "lg": 992,
        "xl": 1200,
        "xxl": 1400
      },
      "colors": {
        "theme": {
          "base": {
            "white": "#ffffff",
            "primary": "#3699FF",
            "secondary": "#E5EAEE",
            "success": "#1BC5BD",
            "info": "#8950FC",
            "warning": "#FFA800",
            "danger": "#F64E60",
            "light": "#E4E6EF",
            "dark": "#181C32"
          },
          "light": {
            "white": "#ffffff",
            "primary": "#E1F0FF",
            "secondary": "#EBEDF3",
            "success": "#C9F7F5",
            "info": "#EEE5FF",
            "warning": "#FFF4DE",
            "danger": "#FFE2E5",
            "light": "#F3F6F9",
            "dark": "#D6D6E0"
          },
          "inverse": {
            "white": "#ffffff",
            "primary": "#ffffff",
            "secondary": "#3F4254",
            "success": "#ffffff",
            "info": "#ffffff",
            "warning": "#ffffff",
            "danger": "#ffffff",
            "light": "#464E5F",
            "dark": "#ffffff"
          }
        },
        "gray": {
          "gray-100": "#F3F6F9",
          "gray-200": "#EBEDF3",
          "gray-300": "#E4E6EF",
          "gray-400": "#D1D3E0",
          "gray-500": "#B5B5C3",
          "gray-600": "#7E8299",
          "gray-700": "#5E6278",
          "gray-800": "#3F4254",
          "gray-900": "#181C32"
        }
      },
      "font-family": "Poppins"
    };
  </script>



  <!-- end::Global Config -->
  <!--begin::Global Theme Bundle(used by all pages) -->

  <script src="/metronic/js/plugins.bundle.js" type="text/javascript"></script>
  <script src="/metronic/js/prismjs.bundle.js" type="text/javascript"></script>
  <script src="/metronic/js/scripts.bundle.js" type="text/javascript"></script>
  <script src="/metronic/js/fullcalendar.bundle.js" type="text/javascript"></script>
  <script src="/metronic/js/file.js" type="text/javascript"></script>

  <script src="/metronic/js/wizard.js" type="text/javascript"></script>
  <script src="/metronic/js/user.js" type="text/javascript"></script>



  <script type="text/javascript" src="/js/jquery.mask.min.js"></script>
  <script type="text/javascript" src="/js/mascaras.js"></script>
  <script src="/metronic/js/select2.js" type="text/javascript"></script>
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script> -->
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <?php $path = getenv('PATH_URL') . "/"; ?>
  <script type="text/javascript">
    const path = "{{$path}}";
  </script>

  <script type="text/javascript">
    $('#btn-esqueci').click(() => {
      $('#div-senha').css('display', 'block')
      $('#kt_login_signin_form').css('display', 'none')
      $('#btn-esqueci').css('display', 'none')
    })
  </script>


  <script>
    jQuery(document).ready(function() {
      KTSelect2.init();
      $('.select2-selection__arrow').addClass('select2-selection__arroww')

      $('.select2-selection__arrow').removeClass('select2-selection__arrow')
        // Class definition
        var KTBootstrapDatepicker = function() {

          var arrows;
          if (KTUtil.isRTL()) {
            arrows = {
              leftArrow: '<i class="la la-angle-right"></i>',
              rightArrow: '<i class="la la-angle-left"></i>'
            }
          } else {
            arrows = {
              leftArrow: '<i class="la la-angle-left"></i>',
              rightArrow: '<i class="la la-angle-right"></i>'
            }
          }

          // Private functions
          var demos = function() {

            // minimum setup
            $('#kt_datepicker_1').datepicker({
              rtl: KTUtil.isRTL(),
              todayHighlight: true,
              orientation: "bottom left",
              templates: arrows
            });

            // minimum setup for modal demo
            $('#kt_datepicker_1_modal').datepicker({
              rtl: KTUtil.isRTL(),
              todayHighlight: true,
              orientation: "bottom left",
              templates: arrows
            });

            // input group layout
            $('#kt_datepicker_2').datepicker({
              rtl: KTUtil.isRTL(),
              todayHighlight: true,
              orientation: "bottom left",
              templates: arrows
            });

            // input group layout for modal demo
            $('#kt_datepicker_2_modal').datepicker({
              rtl: KTUtil.isRTL(),
              todayHighlight: true,

              orientation: "bottom left",
              templates: arrows
            });

            // enable clear button
            $('#kt_datepicker_3, #kt_datepicker_3_validate').datepicker({
              rtl: KTUtil.isRTL(),
              todayBtn: "linked",
              clearBtn: false,
              format: 'dd/mm/yyyy',
              todayHighlight: false,
              templates: arrows
            });

            // enable clear button for modal demo
            $('#kt_datepicker_3_modal').datepicker({
              rtl: KTUtil.isRTL(),
              todayBtn: "linked",
              clearBtn: false,
              format: 'dd/mm/yyyy',
              todayHighlight: false,
              templates: arrows
            });

            // orientation
            $('#kt_datepicker_4_1').datepicker({
              rtl: KTUtil.isRTL(),
              orientation: "top left",
              todayHighlight: true,
              templates: arrows
            });

            $('#kt_datepicker_4_2').datepicker({
              rtl: KTUtil.isRTL(),
              orientation: "top right",
              todayHighlight: true,
              templates: arrows
            });

            $('#kt_datepicker_4_3').datepicker({
              rtl: KTUtil.isRTL(),
              orientation: "bottom left",
              todayHighlight: true,
              templates: arrows
            });


          }

          return {
            // public functions
            init: function() {
              demos();
            }
          };
        }();

        KTBootstrapDatepicker.init(
        {
          format: 'dd/mm/yyyy'
        }
        );

      });


    $('#btn_whatsapp, .btn-abre-whatsapp').on('click', function(e){
      e.preventDefault();

      var btn = $('#btn_whatsapp');
      var box = $('#box_whatsapp');

      if(box.is(":visible")){
        btn.children('.icone-whatsapp').removeClass('la la-times').addClass('lab la-whatsapp');
        box.fadeOut(250);
      } else {
        btn.children('.icone-whatsapp').removeClass('lab la-whatsapp').addClass('la la-times');
        box.fadeIn(250);
      }
    })

    $('#send-whats').click(() => {
      let mensagem = $('#w_mensagem').val()
      let nome = $('#w_nome').val()

      let msg = ""
      if(nome){
        msg += "Olá meu nome é "+nome+ ", ";
      }

      msg += mensagem

      let num = {{getenv("CONTATO_SUPORTE")}}

      let uri = "https://wa.me/55"+num+"?text="+msg
      window.open(uri)

      var btn = $('#btn_whatsapp');
      var box = $('#box_whatsapp');
      btn.children('.icone-whatsapp').removeClass('la la-times').addClass('lab la-whatsapp');
      box.fadeOut(250);
    })
  </script>

</body>
<!-- end::Body -->

</html>