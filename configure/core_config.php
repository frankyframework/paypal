<?php

return array(
  'ecommerce-paypal' => array(
          'menu' => "PAYPAL",
          'title' => "Configuración de paypal",
          'config' =>  array(
                array('path' => 'ecommerce/paypal/enabled',
                            'type' => 'select',
                            'label' => 'Habilitar metodo de pago',
                            'validation' => array('required' => true),
                            'data' => ['0' => 'No','1' => 'Sí'],
                            'value' => '0'
                          ),
                array('path' => 'ecommerce/paypal/name',
                          'type' => 'text',
                          'label' => 'Nombre',
                          'validation' => array('required' => true),
                          'value' => ''
                        ),
                        array('path' => 'ecommerce/paypal/brand',
                        'type' => 'text',
                        'label' => 'Marca',
                        'validation' => array('required' => true),
                        'value' => ''
                      ),
                array('path' => 'ecommerce/paypal/sandbox',
                              'type' => 'select',
                              'label' => 'SANDBOX',
                              'validation' => array('required' => true),
                              'data' => ['0' => 'No','1' => 'Sí'],
                              'value' => '1'
                            ),
                array('path' => 'ecommerce/paypal/key',
                              'type' => 'text',
                              'label' => 'API KEY',
                              'validation' => array('required' => false),
                              'value' => ''
                            ),
                array('path' => 'ecommerce/paypal/secret',
                              'type' => 'text',
                              'label' => 'SECRET KEY',
                              'validation' => array('required' => false),
                              'value' => ''
                        ),
                array('path' => 'ecommerce/paypal/urljs',
                        'type' => 'text',
                        'label' => 'URL JS SDK',
                        'validation' => array('required' => false),
                        'value' => ''
                  ),
                  array('path' => 'ecommerce/paypal/urlapi',
                        'type' => 'text',
                        'label' => 'URL API REST',
                        'validation' => array('required' => false),
                        'value' => ''
                  ),
                array('path' => 'ecommerce/paypal/keysandbox',
                              'type' => 'text',
                              'label' => 'API KEY SANDBOX',
                              'validation' => array('required' => false),
                              'value' => ''
                            ),
                array('path' => 'ecommerce/paypal/secretsandbox',
                        'type' => 'text',
                        'label' => 'SECRET KEY SANDBOX',
                        'validation' => array('required' => false),
                        'value' => ''
                        ),
                        array('path' => 'ecommerce/paypal/urljssandbox',
                        'type' => 'text',
                        'label' => 'URL JS SDK SANDBOX',
                        'validation' => array('required' => false),
                        'value' => ''
                  ),
                  array('path' => 'ecommerce/paypal/urlapisandbox',
                        'type' => 'text',
                        'label' => 'URL API REST SANDBOX',
                        'validation' => array('required' => false),
                        'value' => ''
                  ),
                array('path' => 'ecommerce/paypal/email-order',
                        'type' => 'select',
                        'label' => 'Template E-mail para nueva orden',
                        'validation' => array('required' => true),
                        'data' => getTemplatesEmail(),
                        'value' => ''
                        ),
                array('path' => 'ecommerce/paypal/callback',
                        'type' => 'text',
                        'label' => 'Succes page',
                        'validation' => array('required' => false),
                        'value' => ''
                )

          )
  )
);

?>