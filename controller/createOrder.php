<?php
use \Ecommerce\model\CarritoProductoModel;

$MyCarritoProducto =  new CarritoProductoModel();
$token = getTokenPaypal();
$MyCarrito = getMyIdCarrito();
$data = [
  "purchase_units" => [
      [
        "amount" => [
          "currency_code" => DATA_STORE_CONFIG['abreviatura'],
          "value" => $MyCarrito->getTotal(),
          "breakdown" => [
            "item_total"=> [
              "currency_code"=> DATA_STORE_CONFIG['abreviatura'],
              "value"=> $MyCarrito->getTotalItems()
            ],
            "shipping"=> [
              "currency_code"=> DATA_STORE_CONFIG['abreviatura'],
              "value"=> $MyCarrito->getShippingPrice()
            ],
            "discount"=> [
              "currency_code"=> DATA_STORE_CONFIG['abreviatura'],
              "value"=> $MyCarrito->getDiscount()
            ]

              
          ],
          "reference_id" => md5($MyCarrito->getId())
        ]
      ]
    ],
    "intent" => "CAPTURE",
    "payment_source" => [
          "paypal"=> [
            "experience_context"=>[
              "payment_method_preference"=> "IMMEDIATE_PAYMENT_REQUIRED",
              "payment_method_selected"=> "PAYPAL",
              "brand_name"=> getCoreConfig('ecommerce/paypal/brand'),
              "locale"=> str_replace("_","-",getCoreConfig("base/theme/baselang")),
              "landing_page"=> "LOGIN",
              "shipping_preference"=> "GET_FROM_FILE",
              "user_action"=> "PAY_NOW",
              "return_url"=> "",
              "cancel_url"=> ""
      ]
    ]
  ]
];


$MyCarritoProducto->setTampag(1000);
if($MyCarritoProducto->getData("", $MyCarrito->getId()) == REGISTRO_SUCCESS)
{

    while($registro = $MyCarritoProducto->getRows())
    {

      $data["purchase_units"][0]["items"][] = [
        "name"=> $registro["name"],
        "description"=> "",
        "unit_amount"=> [
          "currency_code"=> DATA_STORE_CONFIG['abreviatura'],
          "value"=> $registro["price"]
        ],
        "quantity"=> $registro["qty"],
        "sku"=> $registro["sku"],
      //  "image_url"=> $MyRequest->link($registro["image"],true,true),
        "url"=> $MyRequest->link($registro["url"],true,true)
        ];
      
      }
}
$url = (getCoreConfig('ecommerce/paypal/sandbox') == 1 ? getCoreConfig('ecommerce/paypal/urlapisandbox') : getCoreConfig('ecommerce/paypal/urlapi'));;

$curl = curl_init($url.'v2/checkout/orders');
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
'Authorization: Bearer ' . $token,
'Accept: application/json',
'Content-Type: application/json'
));
$response = curl_exec($curl);

curl_close($curl);
if (!empty($response)){
  header('Content-Type: application/json; charset=utf-8');
  echo $response;
}
              