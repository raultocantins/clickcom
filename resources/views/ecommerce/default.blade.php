<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Ogani Template">
    <meta name="keywords" content="Ogani, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$title}}</title>

    <style type="text/css">
        :root {
            --color-default: {{$default['config']->cor_principal}};
        }
    </style>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">

    

    @if($default['config']->logo != "")
    <link rel="shortcut icon" href="/ecommerce/logos/{{$default['config']->logo}}" type="image/x-icon" />
    @else
    <link rel="shortcut icon" href="/ecommerce/logo.png" type="image/x-icon" />
    @endif
    <!-- Css Styles -->
    <link rel="stylesheet" href="/ecommerce/assets/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="/ecommerce/assets/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="/ecommerce/assets/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="/ecommerce/assets/css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="/ecommerce/assets/css/jquery-ui.min.css" type="text/css">
    <link rel="stylesheet" href="/ecommerce/assets/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="/ecommerce/assets/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="/ecommerce/assets/css/style.css" type="text/css">
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Humberger Begin -->
    <div class="humberger__menu__overlay"></div>
    <div class="humberger__menu__wrapper">
        <div class="humberger__menu__logo">
            @if($default['config']->logo != "")
            <img height="80" src="/ecommerce/logos/{{$default['config']->logo}}" alt="">
            @else
            <img height="80" src="/ecommerce/logo.png" alt="">
            @endif
        </div>
        <div class="humberger__menu__cart">
            <ul>
                <li><a href="{{$rota}}/curtidas"><i class="fa fa-heart"></i> <span>{{ $default['curtidas']}}</span></a></li>
                <li><a href="{{$rota}}/carrinho"><i class="fa fa-shopping-bag"></i> <span>{{ $default['carrinho'] != null ? sizeof($default['carrinho']->itens) : 0}}</span></a></li>
            </ul>
            <div class="header__cart__price">Carrinho: <span>R$ 0,00</span></div>
        </div>
        <div class="humberger__menu__widget">

            <div class="header__top__right__auth">
                <a href="{{$rota}}/login"><i class="fa fa-user"></i> Login</a>
            </div>
        </div>
        <nav class="humberger__menu__nav mobile-menu">
            <ul>


                <li><a href="{{$rota}}">Home</a></li>
                <li><a href="{{$rota}}/categorias">Categorias</a></li>

                @if($default['postBlogExists'])
                <li><a href="{{$rota}}/blog">Blog</a></li>
                @endif
                <li><a href="{{$rota}}/contato">Contato</a></li>
            </ul>


        </nav>
        <div id="mobile-menu-wrap"></div>
        <div class="header__top__right__social">
            @if($default['config']['link_facebook'] != "")
            <a target="_blank" href="{{$default['config']['link_facebook']}}"><i class="fa fa-facebook"></i></a>
            @endif
            @if($default['config']['link_twitter'] != "")
            <a target="_blank" href="{{$default['config']['link_twitter']}}"><i class="fa fa-twitter"></i></a>
            @endif
            @if($default['config']['link_instagram'] != "")
            <a target="_blank" href="{{$default['config']['link_instagram']}}"><i class="fa fa-instagram"></i></a>
            @endif

        </div>
        <div class="humberger__menu__contact">
            <ul>
                <li><i class="fa fa-envelope"></i> {{$default['config']['email']}}</li>
                <li>Frete gratis acima de R$ 
                    {{number_format($default['config']['frete_gratis_valor'], 2, ',', '.')}}
                </li>
            </ul>
        </div>
    </div>
    <!-- Humberger End -->

    <!-- Header Section Begin -->
    <header class="header">

        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="header__logo">
                        <a href="{{$rota}}">
                            @if($default['config']->logo != "")
                            <img height="80" src="/ecommerce/logos/{{$default['config']->logo}}" alt="">
                            @else
                            <img height="80" src="/ecommerce/logo.png" alt="">
                            @endif
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <nav class="header__menu">
                        <ul>
                            <li @if($default['active'] == 'home') class="active" @endif><a href="{{$rota}}">Home</a></li>
                            <li @if($default['active'] == 'categorias') class="active" @endif><a href="{{$rota}}/categorias">Categorias</a></li>

                            <!-- @if($default['postBlogExists']) -->
                            <li @if($default['active'] == 'blog') class="active" @endif><a href="{{$rota}}/blog">Blog</a></li>
                            <!-- @endif -->
                            <li @if($default['active'] == 'contato') class="active" @endif><a href="{{$rota}}/contato">Contato</a></li>
                        </ul>
                    </nav>
                </div>

                <div class="col-lg-3">
                    <div class="header__cart">
                        <ul>
                            <li>
                                <a href="{{$rota}}/login">
                                    <i class="fa fa-user @if(session('user_ecommerce')) text-success @endif"></i> 
                                </a>
                            </li>
                            <li>
                                <a href="{{$rota}}/curtidas"><i class="fa fa-heart"></i> 
                                    <span>
                                        {{ $default['curtidas'] }}
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{$rota}}/carrinho"><i class="fa fa-shopping-bag"></i> 
                                    <span>{{ $default['carrinho'] != null ? sizeof($default['carrinho']->itens) : 0}}</span>
                                </a>
                            </li>
                        </ul>
                        <div class="header__cart__price">Carrinho: 
                            <span>
                                R$ {{ $default['carrinho'] != null ? number_format($default['carrinho']->somaItens(), 2, ',', '.') : '0,00'}}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="humberger__open">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </header>
    <!-- Header Section End -->

    <!-- Hero Section Begin -->
    <section class="hero hero-normal">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="hero__categories">
                        <div class="hero__categories__all">
                            <i class="fa fa-bars"></i>
                            <span>Categorias</span>
                        </div>
                        <ul>
                            @foreach($default['categorias'] as $c)
                            <li><a href="{{$rota}}/{{$c->id}}/categorias">{{$c->nome}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="hero__search">
                        <div class="hero__search__form">
                            <form action="{{$rota}}/pesquisa">

                                <input type="text" name="pesquisa" placeholder="O que você procura?">
                                <button type="submit" class="site-btn">Buscar</button>
                            </form>
                        </div>
                        <div class="hero__search__phone">
                            <div class="hero__search__phone__icon">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div class="hero__search__phone__text">
                                <h5>{{$default['config']->telefone}}</h5>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->

    @if(session()->has('mensagem_sucesso'))
    <div class="escfff" style="background: #fff;">
        <div class="container">
            <div class="alert alert-custom alert-success fade show" role="alert">

                <div class="alert-text"><i class="fa fa-check"></i> 
                    {{ session()->get('mensagem_sucesso') }} 
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="fa fa-close"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

    @if(session()->has('mensagem_erro'))
    <div class="escfff" style="background: #fff;">
        <div class="container">
            <div class="alert alert-custom alert-danger fade show" role="alert">

                <div class="alert-text"><i class="fa fa-check"></i> 
                    {{ session()->get('mensagem_erro') }} 
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="fa fa-close"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif


    @yield('content')

    <!-- Footer Section Begin -->
    <footer class="footer spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer__about">
                        <div class="footer__about__logo">
                            <a href="{{$rota}}">

                                @if($default['config']->logo != "")
                                <img height="80" src="/ecommerce/logos/{{$default['config']->logo}}" alt="">
                                @else
                                <img height="80" src="/ecommerce/logo.png" alt="">
                                @endif
                            </a>
                        </div>
                        <ul>
                            <li>Endereço: {{$default['config']->rua}}, {{$default['config']->numero}} - {{$default['config']->bairro}}</li>
                            <li>Telefone: {{$default['config']->telefone}}</li>
                            <li>Email: {{$default['config']->email}}</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 offset-lg-1">

                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="footer__widget">
                        <h6>Junte-se ao nosso boletim informativo agora</h6>
                        <p>Receba atualizações por e-mail sobre nossa última loja e ofertas especiais.</p>

                        @if($errors->has('email'))
                        <p class="text-danger">{{ $errors->first('email') }}</p>
                        @endif

                        <form action="/ecommerceInformativo" method="post">
                            @csrf
                            <input type="hidden" value="{{$default['config']->empresa_id}}" name="empresa_id">
                            <input type="email" value="{{old('email')}}" placeholder="Seu melhor email" name="email_info">
                            <button type="submit" class="site-btn">Assinar</button>
                        </form>
                        <div class="footer__widget__social">
                            @if($default['config']['link_facebook'] != "")
                            <a target="_blank" href="{{$default['config']['link_facebook']}}"><i class="fa fa-facebook"></i></a>
                            @endif
                            @if($default['config']['link_instagram'] != "")
                            <a target="_blank" href="{{$default['config']['link_instagram']}}"><i class="fa fa-instagram"></i></a>
                            @endif
                            @if($default['config']['link_twiter'] != "")
                            <a target="_blank" href="{{$default['config']['link_twiter']}}"><i class="fa fa-twitter"></i></a>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </footer>
    <!-- Footer Section End -->

    <!-- Js Plugins -->
    <script src="/ecommerce/assets/js/jquery-3.3.1.min.js"></script>
    <script src="/ecommerce/assets/js/bootstrap.min.js"></script>
    <script src="/ecommerce/assets/js/jquery.nice-select.min.js"></script>
    @if(!isset($payJs))
    <script src="/ecommerce/assets/js/jquery-ui.min.js"></script>
    <script src="/ecommerce/assets/js/jquery.slicknav.js"></script>
    @endif
    <script src="/ecommerce/assets/js/mixitup.min.js"></script>
    <script src="/ecommerce/assets/js/owl.carousel.min.js"></script>
    <script src="/ecommerce/assets/js/main.js"></script>
    <script type="text/javascript" src="/js/jquery.mask.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    
    @isset($carrinhoJs)
    <script src="/ecommerce/assets/js/carrinho.js"></script>
    @endisset

    @isset($payJs)
    <script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>

    <script type="text/javascript">
        window.Mercadopago.setPublishableKey('{{$default['config']->mercadopago_public_key}}')
    </script>

    <script src="/ecommerce/assets/js/pay.js"></script>
    @endisset

    @yield('javascript')

</body>

</html>