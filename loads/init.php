<?php
use Franky\Core\ObserverManager;
$ObserverManager = new ObserverManager;

include 'util.php';

__bindtextdomain("paypal",'paypal');


if (function_exists('bind_textdomain_codeset'))
{
    bind_textdomain_codeset("paypal", 'UTF-8');
}
if(getCoreConfig("ecommerce/paypal/enabled") == 1){
    
    if(getCoreConfig("ecommerce/paypal/sandbox") == 1) {
        $MyMetatag->setCode('<script src="'.getCoreConfig("ecommerce/paypal/urljssandbox").'?client-id='.getCoreConfig("ecommerce/paypal/keysandbox").'&currency='.DATA_STORE_CONFIG['abreviatura'].'&components=buttons"></script>');
    } else {
        $MyMetatag->setCode('<script src="'.getCoreConfig("ecommerce/paypal/urljssandbox").'?client-id='.getCoreConfig("ecommerce/paypal/keysandbox").'&currency='.DATA_STORE_CONFIG['abreviatura'].'&components=buttons"></script>');
    }
    $ObserverManager->addObserver('cancel_order','cancelOrderPaypal');
    $ObserverManager->addObserver('show_payment_data','showPaymentPaypalData');
    

}

?>