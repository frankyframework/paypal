<?php
$MyPedidoTiendaEntity = new \Ecommerce\entity\pedidos();
$MyPedidoTienda = new \Ecommerce\model\pedidos();

$header = '';
$req = 'cmd=_notify-validate';


foreach($CONTEXT as $clave => $valor) {
    $valor = urlencode(stripslashes($valor));
    $req .= "&$clave=$valor";
}

 //Enviar un POST de vuelta a Paypal para verificación
 $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
 $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
 $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
 $fp = fsockopen ('www.'.(getCoreConfig('ecommerce/paypal/sandbox') == 1  ? "sandbox." : "").'paypal.com', 80, $errno, $errstr, 30);

 if(!$fp) {
     $message .= "\n HTTP ERROR. \n";
 }
 else
 {
     fputs ($fp, $header . $req);
     while (!feof($fp)) {
         $res = fgets ($fp, 1024);
         if (!strcmp ($res, "VERIFIED")) {

                $MyPedidoTienda->getData("", "","","",$MyRequest->getRequest('invoice'));
                $registro = $MyPedidoTienda->getRows();
                $MyPedidoTiendaEntity->setStatus($MyRequest->getRequest('payment_status'));
                $MyPedidoTiendaEntity->setId($registro["id"]);
                $MyPedidoTienda->save($MyPedidoTiendaEntity->getArrayCopy());


         } elseif (!strcmp($res, "INVALID")) {
             // INVALID - EMAIL FOR INVESTIGATION
         }
     }
     fclose ($fp);
 }
 ?>
