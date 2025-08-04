<?php
function pago_paypal()
{
        global $MyMessageAlert;
        global $MySession;
        global $MyRequest;
        
        $ObserverManager = new \Franky\Core\ObserverManager;
        
        $ObserverManager->dispatch('prepara_orden_ajax_ecommerce',[]);

        $respuesta = array("error" => false,"html" => "");

        $data = $MySession->GetVar('checkout');

        $productos_comprados = getCarrito();
        $data['descuento'] = $productos_comprados['descuento'];
        if(!empty($productos_comprados['productos']))
        {
            if($MySession->LoggedIn())
            {
                $respuesta["html"] = render(PROJECT_DIR.'/modulos/ecommerce/diseno/paypal/paypal.button.phtml');
                
                $respuesta["js"] = getJSEmbebed(render(PROJECT_DIR.'/modulos/ecommerce/diseno/paypal/validate.paypal.phtml',['data' => $data]));
            }
            else
            {
                $respuesta["message"] = $MyMessageAlert->Message("sin_privilegios");
                $respuesta["error"] = true;
            }
	         }
        else{
           $respuesta["message"] = $MyMessageAlert->Message("ecommerce_carrito_vacio");
           $respuesta["error"] = true;
        }
	return $respuesta;
}
/******************************** EJECUTA *************************/
$MyAjax->register("pago_paypal");
?>
