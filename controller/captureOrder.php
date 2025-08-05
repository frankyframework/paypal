<?php
use \Ecommerce\model\CarritoProductoModel;

$MyCarritoProducto =  new CarritoProductoModel();
$data =  json_decode(file_get_contents('php://input'),true);

$MySession->SetVar("PaypalCapture",$data);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);