<?php
use Franky\Core\ObserverManager;
$ObserverManager = new ObserverManager;

include 'util.php';

__bindtextdomain("paypal",'paypal');


if (function_exists('bind_textdomain_codeset'))
{
    bind_textdomain_codeset("paypal", 'UTF-8');
}


if($MyFrankyMonster->MySeccion() == CHECKOUT_ECOMMERCE){
    if(getController("ecommerce/paypal/sandbox") == 1) {
        $MyMetatag->setCode('<script src="'.getController("ecommerce/paypal/urljssandbox").'?client-id='.getController("ecommerce/paypal/keysandbox").'&components=buttons"></script>');
    } else {
        $MyMetatag->setCode('<script src="'.getController("ecommerce/paypal/urljssandbox").'?client-id='.getController("ecommerce/paypal/keysandbox").'&components=buttons"></script>');
    }
    
}


?>