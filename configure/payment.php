<?php
return [
    [
    "code" => "paypal",
    "data" => [
        "name" =>  getCoreConfig("ecommerce/paypal/name"),
        "enabled" => getCoreConfig("ecommerce/paypal/enabled"),
        "html" => render("button.phtml"),
        "execute" => "placeOrderPaypal",
        "callback" => getCallbakPayPal(),
        "email_template" =>  getCoreConfig("ecommerce/paypal/email-order"),
        ]
    ]
];