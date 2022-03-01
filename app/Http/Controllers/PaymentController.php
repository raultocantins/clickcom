<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Plano;
use App\Models\ConfigNota;
use App\Models\Payment;
use App\Models\PlanoEmpresa;

class PaymentController extends Controller
{
    protected $empresa_id = null;
    public function __construct(){
        $this->middleware(function ($request, $next) {

            $this->empresa_id = $request->empresa_id;
            $value = session('user_logged');
            if(!$value){
                return redirect("/login");
            }

            $config = ConfigNota::where('empresa_id', $this->empresa_id)->first();
            if($config == null){
                session()->flash("mensagem_erro", "Informe o emitente primeiramente");
                return redirect('/configNF');
            }

            return $next($request);
        });

        
    }

    public function index(){
        $empresa = Empresa::find($this->empresa_id);

        $planos = Plano::
        where('visivel', true)
        ->get();


        $plano = $empresa->planoEmpresa;

        if($plano == null){
            session()->flash("mensagem_erro", "Defina um plano!!");
            return redirect('/empresas');
        }
        $pay = $plano->payment ?? null;

        return view('payment/index')
        ->with('empresa', $empresa)
        ->with('plano', $plano)
        ->with('pay', $pay)
        ->with('planos', $planos)
        ->with('title', 'Pagamento de plano');
    }

    public function setPlano(Request $request){
        $empresa = Empresa::find($this->empresa_id);
        $planoEscolhido = Plano::find($request->plano);
        $plano = $empresa->planoEmpresa;

        $plano->plano_id = $planoEscolhido->id;
        $plano->save();

        return redirect('/payment/finish');
    }

    public function finish(){
        $empresa = Empresa::find($this->empresa_id);

        $plano = $empresa->planoEmpresa;

        return view('payment/finish')
        ->with('empresa', $empresa)
        ->with('plano', $plano)
        ->with('title', 'Pagamento de plano');
    }

    public function paymentCard(Request $request){

        // \MercadoPago\SDK::setAccessToken(getenv("MERCADOPAGO_ACCESS_TOKEN"));
        if(getenv("MERCADOPAGO_AMBIENTE") == 'sandbox'){
            \MercadoPago\SDK::setAccessToken(getenv("MERCADOPAGO_ACCESS_TOKEN"));
        }else{
            \MercadoPago\SDK::setAccessToken(getenv("MERCADOPAGO_ACCESS_TOKEN_PRODUCAO"));
        }
        $payment = new \MercadoPago\Payment();

        $payment->transaction_amount = (float)$request->transactionAmount;
        $payment->token = $request->token;
        $payment->description = $request->description;
        $payment->installments = (int)$request->installments;
        $payment->payment_method_id = $request->paymentMethodId;
        // $payment->issuer_id = (int)$request->issuer;

        $payer = new \MercadoPago\Payer();
        $payer->email = $request->email;
        $payer->identification = array(
            "type" => $request->docType,
            "number" => $request->docNumber
        );
        $payment->payer = $payer;

        $payment->save();

        if($payment->error){
            session()->flash("mensagem_erro", $payment->error);
            return redirect()->back();
        }else{

            Payment::where('plano_id', $request->plano_empresa_id)->delete();
            $data = [
                'empresa_id' => $this->empresa_id,
                'plano_id' => $request->plano_empresa_id,
                'valor' => (float)$request->transactionAmount,
                'transacao_id' => $payment->id,
                'status' => 0,
                'forma_pagamento' => 'Cartão',
                'link_boleto' => $payment->transaction_details->external_resource_url ?? '',
                'status_detalhe' => $payment->status_detail,
                'descricao' => $payment->description,
                'qr_code_base64' => '',
                'qr_code' => '',
            ];

            Payment::create($data);
            if($payment->status == 'approved'){
                $planoEmpresa = PlanoEmpresa::find($request->plano_empresa_id);
                $this->setarLicenca($planoEmpresa);
                session()->flash("mensagem_sucesso", "Pagamento aprovado com sucesso!!");
                return redirect('/payment/'.$payment->id);
            }

        }

        // echo "<pre>";
        // print_r($request->all());
        // echo "</pre>";
    }

    private function setPagamentoRepresentante(){
        
    }

    public function paymentBoleto(Request $request){
        if(getenv("MERCADOPAGO_AMBIENTE") == 'sandbox'){
            \MercadoPago\SDK::setAccessToken(getenv("MERCADOPAGO_ACCESS_TOKEN"));
        }else{
            \MercadoPago\SDK::setAccessToken(getenv("MERCADOPAGO_ACCESS_TOKEN_PRODUCAO"));
        }
        $payment = new \MercadoPago\Payment();

        $payment->transaction_amount = (float)$request->transactionAmount;
        $payment->description = $request->description;
        $payment->payment_method_id = "bolbradesco";


        $config = ConfigNota::where('empresa_id', $this->empresa_id)->first();

        $cep = str_replace("-", "", $config->cep);
        $payment->payer = array(
            "email" => $request->payerEmail,
            "first_name" => $request->payerFirstName,
            "last_name" => $request->payerLastName,
            "identification" => array(
                "type" => $request->docType,
                "number" => $request->docNumber
            ),
            "address"=>  array(
                "zip_code" => $cep,
                "street_name" => $config->logradouro,
                "street_number" => $config->numero,
                "neighborhood" => $config->bairro,
                "city" => $config->municipio,
                "federal_unit" => $config->UF
            )
        );

        $payment->save();

        if($payment->transaction_details){
            
            Payment::where('plano_id', $request->plano_empresa_id)->delete();
            $data = [
                'empresa_id' => $this->empresa_id,
                'plano_id' => $request->plano_empresa_id,
                'valor' => (float)$request->transactionAmount,
                'transacao_id' => (string)$payment->id,
                'status' => $payment->status,
                'forma_pagamento' => 'Boleto',
                'link_boleto' => $payment->transaction_details->external_resource_url,
                'status_detalhe' => $payment->status_detail,
                'descricao' => $payment->description,
                'qr_code_base64' => '',
                'qr_code' => '',
            ];

            Payment::create($data);
            session()->flash("mensagem_sucesso", "Boleto gerado");

            return redirect('/payment/'.(string)$payment->id);
        }else{
            session()->flash("mensagem_erro", $payment->error);
            return redirect()->back();
        }

    }

    public function paymentPix(Request $request){

        // \MercadoPago\SDK::setAccessToken(getenv("MERCADOPAGO_ACCESS_TOKEN"));
        if(getenv("MERCADOPAGO_AMBIENTE") == 'sandbox'){
            \MercadoPago\SDK::setAccessToken(getenv("MERCADOPAGO_ACCESS_TOKEN"));
        }else{
            \MercadoPago\SDK::setAccessToken(getenv("MERCADOPAGO_ACCESS_TOKEN_PRODUCAO"));
        }
        $payment = new \MercadoPago\Payment();

        $payment->transaction_amount = (float)$request->transactionAmount;
        $payment->description = $request->description;
        $payment->payment_method_id = "pix";

        $config = ConfigNota::where('empresa_id', $this->empresa_id)->first();

        $cep = str_replace("-", "", $config->cep);
        $payment->payer = array(
            "email" => $request->payerEmail,
            "first_name" => $request->payerFirstName,
            "last_name" => $request->payerLastName,
            "identification" => array(
                "type" => $request->docType,
                "number" => $request->docNumber
            ),
            "address"=>  array(
                "zip_code" => $cep,
                "street_name" => $config->logradouro,
                "street_number" => $config->numero,
                "neighborhood" => $config->bairro,
                "city" => $config->municipio,
                "federal_unit" => $config->UF
            )
        );

        $payment->save();
    
        if($payment->transaction_details){
            // print_r($payment);
            // die();
            Payment::where('plano_id', $request->plano_empresa_id)->delete();

            $data = [
                'empresa_id' => $this->empresa_id,
                'plano_id' => $request->plano_empresa_id,
                'valor' => (float)$request->transactionAmount,
                'transacao_id' => (string)$payment->id,
                'status' => 0,
                'forma_pagamento' => 'Pix',
                'link_boleto' => '',
                'status_detalhe' => $payment->status_detail,
                'descricao' => $payment->description,
                'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
                'qr_code' => $payment->point_of_interaction->transaction_data->qr_code,
            ];

            Payment::create($data);

            // $this->setarLicenca($payment->plano);

            session()->flash("mensagem_sucesso", "Código pix gerado!");

            return redirect('/payment/'.(string)$payment->id);
        }else{

            $err = $this->trataErros($payment->error);
            session()->flash("mensagem_erro", $err);
            return redirect()->back();
        }

    }

    private function trataErros($arr){
        $cause = $arr->causes[0];
        $errorCode = $cause->code;
        $arrCode = $this->arrayErros($arr);
        return $arrCode[$errorCode];
    }

    private function arrayErros($arr){
        return [
            '2067' => 'Número documento inválido!',
            '13253' => 'Ative o QR code do cadastro!'
        ];
    }

    public function detalhesPagamento($code){
        $payment = Payment::where('transacao_id', $code)
        ->first();

        // \MercadoPago\SDK::setAccessToken(getenv("MERCADOPAGO_ACCESS_TOKEN"));
        if(getenv("MERCADOPAGO_AMBIENTE") == 'sandbox'){
            \MercadoPago\SDK::setAccessToken(getenv("MERCADOPAGO_ACCESS_TOKEN"));
        }else{
            \MercadoPago\SDK::setAccessToken(getenv("MERCADOPAGO_ACCESS_TOKEN_PRODUCAO"));
        }
        $payStatus = \MercadoPago\Payment::find_by_id($code);

        if($payStatus->status != $payment->status){
            $this->setarLicenca($payment->plano);
        }

        if($payStatus->status != "approved"){
            $payment->status = $payStatus->status;
            $payment->status_detalhe = $payStatus->status_detail;
            $payment->descricao = $payStatus->description;

            $payment->save();
        }
        
        return view('payment/detalhes_pagamento')
        ->with('payment', $payment)
        ->with('payStatus', $payStatus)
        ->with('title', 'Detalhes de pagamento');
    }

    private function setarLicenca($planoEmpresa){
        $plano = $planoEmpresa->plano;
        $exp = date('Y-m-d', strtotime("+$plano->intervalo_dias days",strtotime( 
          date('Y-m-d'))));

        $planoEmpresa->expiracao = $this->parseDate($exp);
        $planoEmpresa->save();

    }

    private function parseDate($date){
        return date('Y-m-d', strtotime(str_replace("/", "-", $date)));
    }

    public function consultaPagamento($transacao_id){
        if(getenv("MERCADOPAGO_AMBIENTE") == 'sandbox'){
            \MercadoPago\SDK::setAccessToken(getenv("MERCADOPAGO_ACCESS_TOKEN"));
        }else{
            \MercadoPago\SDK::setAccessToken(getenv("MERCADOPAGO_ACCESS_TOKEN_PRODUCAO"));
        }
        $payment = Payment::where('transacao_id', $transacao_id)
        ->first();

        if($payment){
            $payStatus = \MercadoPago\Payment::find_by_id($payment->transacao_id);

            $payment->status = $payStatus->status;
            $payment->save();

            if($payStatus->status == "approved"){
                $this->setarLicenca($payment->plano);
            }
            return response()->json($payStatus->status, 200);

        }else{
            return response()->json("erro", 404);
        }
    }
}
