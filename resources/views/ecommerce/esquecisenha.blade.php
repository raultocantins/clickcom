@extends('ecommerce.default')
@section('content')

<section class="checkout">
    <div class="container">

        <div class="checkout__form">
            <h4>Esqueci minha senha
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
                                    <input autofocus autocomplete="off" name="email" type="email">
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-lg btn-block primary-btn">Recuperar</button>
                            </div>
                        </div>

                    </div>
                    
                </div>
            </form>
        </div>
    </div>
</section>
@endsection 
