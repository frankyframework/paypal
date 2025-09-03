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
        "total" => $response["purchase_units"][0]['payments']['captures'][0]['amount']['value'],
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

function cancelOrderPaypal($id){

    global $MyMessageAlert;
    $token = getTokenPaypal();
    $EcommercePaymentModel = new \Ecommerce\model\EcommercePaymentModel();
    $EcommercePaymentEntity = new \Ecommerce\entity\EcommercePaymentEntity();
    $EcommercePaymentEntity->setOrderId($id);
    $EcommercePaymentEntity->setPaymentMethod('paypal');
    

    $EcommercePaymentModel->setTampag(1);
    if($EcommercePaymentModel->getData($EcommercePaymentEntity->getArrayCopy()) != REGISTRO_SUCCESS)
    {
        return false;
    }

    $registro = $EcommercePaymentModel->getRows();
    $extraData = json_decode($registro["extra_data"],true);
   

    $data = [
        "amount" =>  $extraData['purchase_units'][0]["payments"]["captures"][0]["amount"],
        "invoice_id" => $registro["id"],
        "custom_id" =>  $registro["id"]
    ];

    $url = (getCoreConfig('ecommerce/paypal/sandbox') == 1 ? getCoreConfig('ecommerce/paypal/urlapisandbox') : getCoreConfig('ecommerce/paypal/urlapi'));;

    $curl = curl_init($url.'v2/payments/captures/'.$extraData['purchase_units'][0]["payments"]["captures"][0]['id'].'/refund');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json',
    'PayPal-Request-Id: '.$registro["id"]."_".time(),
    'Prefer: return=representation'
    ));
    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if (!empty($response)){
        $response = json_decode($response,true);
    } else {
        $response = [];
    }

    $EcommercelogstatusModel    = new \Ecommerce\model\EcommerceStatusHistoryModel();
    $EcommercelogstatusEntity   = new \Ecommerce\entity\EcommerceStatusHistoryEntity();
    $statusCode = explode("_",getCoreConfig('ecommerce/ventas/status-canceled'));
    $status = $statusCode[1];
    $state = $statusCode[0];

    $EcommercelogstatusEntity->setState($state);
    $EcommercelogstatusEntity->setStatus($status);
    $EcommercelogstatusEntity->setCreatedAt(date('Y-m-d H:i:s'));
    $EcommercelogstatusEntity->setOrderId($id);
    if(isset($response['status']) && $response['status'] == "COMPLETED") {
        $EcommercelogstatusEntity->setComment("Reenvolso paypal: ".getFormatoPrecio($extraData['purchase_units'][0]["payments"]["captures"][0]["amount"]["value"],true,DATA_STORE_CONFIG['simbolo'],DATA_STORE_CONFIG['abreviatura']));
        $MyMessageAlert->Message("paypal_order_canceled_success");
    } else {
        $EcommercelogstatusEntity->setComment("No se pudo hacer el reenvolso desde paypal: ".getFormatoPrecio($extraData['purchase_units'][0]["payments"]["captures"][0]["amount"]["value"],true,DATA_STORE_CONFIG['simbolo'],DATA_STORE_CONFIG['abreviatura']));
        $MyMessageAlert->Message("paypal_order_canceled_error");
    }
    $EcommercelogstatusModel->save($EcommercelogstatusEntity->getArrayCopy());

    $EcommercePaymentEntity->setIsCanceled(1);
    $EcommercePaymentEntity->setCancelData(json_encode($response));
    $EcommercePaymentEntity->setId($registro["id"]);
    $EcommercePaymentModel->save($EcommercePaymentEntity->getArrayCopy());
              
}

?>