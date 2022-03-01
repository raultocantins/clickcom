@extends('ecommerce.default')
@section('content')

<section class="checkout spad">
    <div class="container">

        <div class="checkout__form">
            <h4>Realize seu cadastro :) 
                <a class="btn btn-info" href="{{$rota}}/login">Já sou cadastrado(a)</a>
            </h4>

            <form method="post">
                @csrf

                <input type="hidden" value="{{$default['config']->empresa_id}}" name="empresa_id">
                <input type="hidden" value="{{$default['carrinho']->id}}" name="pedido_id">

                <div class="row">
                    <div class="col-lg-8 col-md-6">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="checkout__input">
                                    <p>Nome<span>*</span></p>
                                    <input autofocus name="nome" value="{{ old('nome') }}" type="text">
                                    @if($errors->has('nome'))
                                    <label class="text-danger">{{ $errors->first('nome') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="checkout__input">
                                    <p>Sobre nome<span>*</span></p>
                                    <input value="{{ old('sobre_nome') }}" name="sobre_nome" type="text">
                                    @if($errors->has('sobre_nome'))
                                    <label class="text-danger">{{ $errors->first('sobre_nome') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="checkout__input">
                                    <p>Telefone<span>*</span></p>
                                    <input data-mask="(00) 00000-0000" value="{{old('telefone')}}" name="telefone" type="text">
                                    @if($errors->has('telefone'))
                                    <label class="text-danger">{{ $errors->first('telefone') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="checkout__input">
                                    <p>Email<span>*</span></p>
                                    <input value="{{old('email')}}" name="email" type="text">
                                    @if($errors->has('email'))
                                    <label class="text-danger">{{ $errors->first('email') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="checkout__input">
                                    <p>Senha<span>*</span></p>
                                    <input name="senha" type="password">
                                    @if($errors->has('senha'))
                                    <label class="text-danger">{{ $errors->first('senha') }}</label>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="checkout__input">
                                    <p>Tipo Doc.<span>*</span></p>
                                    <select name="tp_doc" id="tp_doc" class="form-control" style="height: 47px;">
                                        <option @if(old('tp_doc') == 'cpf') selected @endif value="cpf">CPF</option>
                                        <option @if(old('tp_doc') == 'cnpj') selected @endif value="cnpj">CNPJ</option>
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="checkout__input">
                                    <p class="lbl_doc">CPF<span>*</span></p>
                                    <input id="doc" value="{{old('cpf')}}" data-mask="000.000.000-00" data-mask-reverse="true" name="cpf" type="text">
                                    @if($errors->has('cpf'))
                                    <label class="text-danger">{{ $errors->first('cpf') }}</label>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-5 ie" style="display: none;">
                                <div class="checkout__input">
                                    <p>IE<span>*</span></p>
                                    <input id="doc" value="{{old('ie')}}" name="ie" type="text">
                                    @if($errors->has('ie'))
                                    <label class="text-danger">{{ $errors->first('ie') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-9">
                                <div class="checkout__input">
                                    <p>Rua<span>*</span></p>
                                    <input value="{{ $enderecoCep != null ? $enderecoCep->logradouro : old('rua') }}" name="rua" type="text">
                                    @if($errors->has('rua'))
                                    <label class="text-danger">{{ $errors->first('rua') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="checkout__input">
                                    <p>Nº<span>*</span></p>
                                    <input value="{{ old('numero') }}" name="numero" type="text">
                                    @if($errors->has('numero'))
                                    <label class="text-danger">{{ $errors->first('numero') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="checkout__input">
                                    <p>Bairro<span>*</span></p>
                                    <input value="{{ $enderecoCep != null ? $enderecoCep->bairro : old('bairro') }}" name="bairro" type="text">
                                    @if($errors->has('bairro'))
                                    <label class="text-danger">{{ $errors->first('bairro') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="checkout__input">
                                    <p>Cidade<span>*</span></p>
                                    <input value="{{ $enderecoCep != null ? $enderecoCep->localidade : old('cidade') }}" name="cidade" type="text">
                                    @if($errors->has('cidade'))
                                    <label class="text-danger">{{ $errors->first('cidade') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-2">
                                <div class="checkout__input">
                                    <p>UF<span>*</span></p>
                                    <input data-mask="AA" data-mask-reverse="true" value="{{ $enderecoCep != null ? $enderecoCep->uf : old('uf') }}" name="uf" type="text">
                                    @if($errors->has('uf'))
                                    <label class="text-danger">{{ $errors->first('uf') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="checkout__input">
                                    <p>CEP<span>*</span></p>
                                    <input data-mask="00000-000" data-mask-reverse="true" value="{{{ $default['carrinho']->observacao != '' ? $default['carrinho']->observacao : old('cep') }}}" name="cep" type="text">
                                    @if($errors->has('cep'))
                                    <label class="text-danger">{{ $errors->first('cep') }}</label>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="checkout__input">
                                    <p>Complemento<span></span></p>
                                    <input value="{{ old('complemento') }}" name="complemento" type="text">
                                    @if($errors->has('complemento'))
                                    <label class="text-danger">{{ $errors->first('complemento') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="checkout__input">
                            <p>Observação<span></span></p>
                            <input value="{{old('observacao')}}" name="observacao" type="text"
                            placeholder="Observação sobre entrega por exemplo">
                        </div>


                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="checkout__order">
                            <h4>Seu Pedido</h4>
                            <div class="checkout__order__products">Produtos <span>Total</span></div>
                            @if($default['carrinho'] != null)
                            @foreach($default['carrinho']->itens as $i)
                            <ul>
                                <li>{{$i->produto->produto->nome}} 
                                    <span>R$ {{number_format($i->produto->valor, 2, ',', '.')}}</span>
                                </li>
                            </ul>
                            @endforeach
                            @endif
                            <div class="checkout__order__subtotal">Frete <span>R$ {{number_format($default['carrinho']->valor_frete, 2, ',', '.')}}</span></div>
                            <div class="checkout__order__total">Total <span>R$ {{ $default['carrinho'] != null ? number_format($default['carrinho']->somaItens() + $default['carrinho']->valor_frete, 2, ',', '.') : '0,00'}}</span></div>
                            

                            <button type="submit" class="site-btn">SALVAR</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@section('javascript')
<script type="text/javascript">
    $(function(){
        changeDoc()
    })
    $('#tp_doc').change((target) => {
        changeDoc()
    })

    function changeDoc(){
        let v = $('#tp_doc').val()

        if(v == 'cpf'){
            $('#doc').mask('000.000.000-00', {reverse: true});
            $('.lbl_doc').html('CPF<span>*</span>');
            $('.ie').css('display', 'none');

        }else{
            $('#doc').mask('00.000.000/0000-00', {reverse: true});
            $('.lbl_doc').html('CNPJ<span>*</span>');
            $('.ie').css('display', 'block');
        }
    }
</script>
@endsection 

@endsection 

