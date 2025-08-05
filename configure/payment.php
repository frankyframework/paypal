<?php
return [
    [
    "code" => "paypal",
    "data" => [
        "name" =>  getCoreConfig("ecommerce/paypal/name"),
        "enabled" => getCoreConfig("ecommerce/paypal/enabled"),
        "html" => render(PROJECT_DIR."/modulos/paypal/diseno/button.phtml"),
        "execute" => "placeOrderPaypal",
        "callback" => getCallbackPayPal(),
        "email_template" =>  getCoreConfig("ecommerce/paypal/email-order"),
        ]
    ]
];