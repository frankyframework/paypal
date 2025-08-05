<?php
function _paypal($txt)
{
    return dgettext("paypal",$txt);
}

function getCallbackPayPal(){
    global $MyRequest;
    if(!empty(getCoreConfig("ecommerce/paypal/callback"))) {
        return getCoreConfig("ecommerce/paypal/callback");
    }
    return $MyRequest->url(ECOMMERCE_SUCCESS_PAGE);
}

function placeOrderPaypal() {
    global $MySession;
    $token = getTokenPaypal();

    if(getCoreConfig("ecommerce/paypal/enabled") == 0) {
        return false;
    }
    $MyCarrito = getMyIdCarrito();
    if($MyCarrito->getTotal() == 0)
    {
        return false;
    }

    $url = (getCoreConfig('ecommerce/paypal/sandbox') == 1 ? getCoreConfig('ecommerce/paypal/urlapisandbox') : getCoreConfig('ecommerce/paypal/urlapi'));;
    $data = $MySession->GetVar('PaypalCapture');
    $curl = curl_init($url.'v2/checkout/orders/'.$data['orderID'].'/capture');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json',
    'PayPal-Request-Id: '.md5($MyCarrito->getId().time())
    ));
    $response = curl_exec($curl); 
    curl_close($curl);
    if(!empty($response)){
        $response = json_decode($response, true);
    } else {
        $response = [];
    }
    
    if(!isset($response["status"]) || $response["status"] != "COMPLETED") {
        return false;
    }
    
    return [
        "total" => $response["purchase_units"][0]['payments']['captures'][0]['amount']['vañue'],
        "extra_data" => $response,
        "payment_method" => "paypal",
        "created_at" => date('Y-m-d H:i:s'),
        "status" => "processing",
        "state" => "processing",
        "data_email" => []
    ];
}

function getTokenPaypal(){

    $accessToken = "";
    $ch = curl_init();
    $clientId = (getCoreConfig('ecommerce/paypal/sandbox') == 1 ? getCoreConfig('ecommerce/paypal/keysandbox') : getCoreConfig('ecommerce/paypal/key'));
    $secret = (getCoreConfig('ecommerce/paypal/sandbox') == 1 ? getCoreConfig('ecommerce/paypal/secretsandbox') : getCoreConfig('ecommerce/paypal/secret'));;
    $url = (getCoreConfig('ecommerce/paypal/sandbox') == 1 ? getCoreConfig('ecommerce/paypal/urlapisandbox') : getCoreConfig('ecommerce/paypal/urlapi'));;

    curl_setopt($ch, CURLOPT_URL,$url.'v1/oauth2/token');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId.":".$secret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    $result = curl_exec($ch);
    curl_close($ch); 
    $accessToken = null;


    if (empty($result)){
        return false;
    }
    else {
        $json = json_decode($result);
        $accessToken = $json->access_token;
        
    }
    return $accessToken;
}

?>