@extends('ecommerce.default')
@section('content')

<section class="">
    <div class="container">

        <div class="checkout__form">
            <h4>Informe suas credenciais
            </h4>

            <form method="post">
                @csrf

                <div class="row">
                    <div class="col-lg-4">
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <label>Email</label>
                                    <input autocomplete="" name="email" type="email">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <label>Senha</label>
                                    <input name="senha" type="password">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-lg btn-block primary-btn">Login</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12" style="margin-top: 5px; margin-bottom: 5px;">
                                <a href="{{$rota}}/esquecisenha" class="btn btn-outline-danger" href="">Esqueci minha senha</a>
                            </div>
                        </div>

                        @if($default['config']->politica_privacidade != "")
                        <div class="row">
                            <div class="col-lg-12" style="margin-top: 10px;">
                                <center>
                                    <a href="{{$rota}}/esquecisenha" class="text-info" data-toggle="modal" data-target="#modal-politica">Politica de privacidade</a>
                                </center>
                            </div>
                        </div>
                        @endif

                    </div>
                    
                </div>
            </form>
        </div>
    </div>
</section>

<div class="modal fade" id="modal-politica" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Politica de privacidade</h5>
            </div>
            <div class="modal-body">

                {{$default['config']->politica_privacidade}}

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
            </div>

        </div>
    </div>
</div>
@endsection 
