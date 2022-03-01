<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Nfse\Rps;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use App\Models\Certificado;
use NFePHP\Common\Certificate;

class NfseController extends Controller
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

	public function gerar(){
		$nfse = new Rps([]);
		$doc = new \DOMDocument();
		$xml = $nfse->getXml();

		// $doc->load($xml);
		file_put_contents("xml.xml", $xml);
		$doc->load('xml.xml');

		$objDSig = new XMLSecurityDSig();
		$objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
		$objDSig->addReference(
			$doc, 
			XMLSecurityDSig::SHA1, 
			[
				'http://www.w3.org/2000/09/xmldsig#enveloped-signature',
				'http://www.w3.org/TR/2001/REC-xml-c14n-20010315'
			],
			["force_uri" => true]
		);

		$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type'=>'private'));

		$certificado = Certificado::
		where('empresa_id', $this->empresa_id)
		->first();

		$cert = Certificate::readPfx($certificado->arquivo, $certificado->senha);

		$objKey->loadKey($cert->privateKey, false);

		// $objKey->loadKey(public_path() . '/privkey.pem', true);
		$objDSig->sign($objKey);

		$objDSig->add509Cert($cert->publicKey);

		// $objDSig->add509Cert(file_get_contents(public_path() . '/mycert.pem'));

		$objDSig->appendSignature($doc->documentElement);
		$xml = $doc->saveXML();
		$this->transmitir($xml, $certificado->arquivo, $certificado->senha);
		// return response($xml)
		// ->header('Content-Type', 'application/xml');

	}

	private function transmitir($xml, $certificado, $senha){
		$wsdl = "http://e-gov.betha.com.br/e-nota-contribuinte-test-ws/nfseWS?wsdl";
		$endpoint = "http://e-gov.betha.com.br/e-nota-contribuinte-test-ws/nfseWS";

		$options = [
			'location' => $endpoint,
			'keep_alive' => true,
			'trace' => true,
			'local_cert' => $certificado,
			'passphrase' => $senha,
			'cash_wsdl' => WSDL_CACHE_NONE,
		];

		try{
			$client = new \SoapClient($wsdl, $options);
			$function = "RecepcionarLoteRps";
			$arguments = [
				'nfseCabecMsg' => '',
				'nfseDadosMsg' => $xml

			];
			$options = [];
			$result = $client->__soapCall($function, $arguments, $options);
			// print_r($result);
			foreach($result as $r){
				echo $r . "<br>";
			}
		}catch(\Excption $e){

		}
		
	}
}
