<?php
function _paypal($txt)
{
    return dgettext("paypal",$txt);
}

function getCallbakPayPal(){
    global $MyRequest;
    return $MyRequest->url(PAYPAL_SUCCESS_PAGE);
}



function paypalCheck($paymentID){

    global $MyRequest;
    $ch = curl_init();
    $clientId = (getCoreConfig('ecommerce/paypal/sandbox') == 1 ? getCoreConfig('ecommerce/paypal/keysandbox') : getCoreConfig('ecommerce/paypal/key'));
    $secret = (getCoreConfig('ecommerce/paypal/sandbox') == 1 ? getCoreConfig('ecommerce/paypal/secretsandbox') : getCoreConfig('ecommerce/paypal/secret'));;
    curl_setopt($ch, CURLOPT_URL, 'https://api.'.(getCoreConfig('ecommerce/paypal/sandbox') == 1 ? 'sandbox.' : '' ).'paypal.com/v1/oauth2/token');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    $result = curl_exec($ch);
    $accessToken = null;


    if (empty($result)){
        return false;
    }

    else {
        $json = json_decode($result);
        $accessToken = $json->access_token;
        $curl = curl_init('https://api.'.(getCoreConfig('ecommerce/paypal/sandbox') == 1 ? 'sandbox.' : '' ).'paypal.com/v1/payments/payment/' . $paymentID);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $accessToken,
        'Accept: application/json',
        'Content-Type: application/xml'
        ));
        $response = curl_exec($curl);
        $result = json_decode($response);
        curl_close($ch);
        curl_close($curl);
        if (empty($result)){
            return false;
        }
        return $result;


    }

}



function paypalRefund($paymentID){

    global $MyRequest;
    $ch = curl_init();
    $clientId = (getCoreConfig('ecommerce/paypal/sandbox') == 1 ? getCoreConfig('ecommerce/paypal/keysandbox') : getCoreConfig('ecommerce/paypal/key'));
    $secret = (getCoreConfig('ecommerce/paypal/sandbox') == 1 ? getCoreConfig('ecommerce/paypal/secretsandbox') : getCoreConfig('ecommerce/paypal/secret'));;
    curl_setopt($ch, CURLOPT_URL, 'https://api.'.(getCoreConfig('ecommerce/paypal/sandbox') == 1 ? 'sandbox.' : '' ).'paypal.com/v1/oauth2/token');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    $result = curl_exec($ch);
    $accessToken = null;


    if (empty($result)){
        return false;
    }

    else {
        $json = json_decode($result);
        $accessToken = $json->access_token;
        $curl = curl_init('https://api.'.(getCoreConfig('ecommerce/paypal/sandbox') == 1 ? 'sandbox.' : '' ).'paypal.com/v1/payments/sale/' . $paymentID.'/refund');
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $accessToken,
        'Accept: application/json',
        'Content-Type: application/xml'
        ));
        $response = curl_exec($curl);
        $result = json_decode($response);
        curl_close($ch);
        curl_close($curl);
        if (empty($result)){
            return false;
        }
        return $result;


    }

}

function paypal_PDT_request($tx,$pdt_identity_token) {

    global $MyRequest;


    $request = curl_init();

    // Set request options
    curl_setopt_array($request, array
        (
          CURLOPT_URL => 'https://www.'.(getCoreConfig('ecommerce/paypal/sandbox') == 1  ? "sandbox." : "").'paypal.com/cgi-bin/webscr',
          CURLOPT_POST => TRUE,
          CURLOPT_POSTFIELDS => http_build_query(array
              (
                'cmd' => '_notify-synch',
                'tx' => $tx,
                'at' => $pdt_identity_token,
              )
          ),
          CURLOPT_RETURNTRANSFER => TRUE,
          CURLOPT_HEADER => FALSE,
          // CURLOPT_SSL_VERIFYPEER => TRUE,
          // CURLOPT_CAINFO => 'cacert.pem',
        )
    );

    // Realizar la solicitud y obtener la respuesta
    // y el código de status
    $response = curl_exec($request);
    $status   = curl_getinfo($request, CURLINFO_HTTP_CODE);

    // Cerrar la conexión
    curl_close($request);

    $response = explode("\n",$response);
    $array_response = array();
    foreach($response as $val)
    {
        $val = explode("=",$val);
        $array_response[$val[0]] = $val[1];
    }

    return $array_response;
}
?>