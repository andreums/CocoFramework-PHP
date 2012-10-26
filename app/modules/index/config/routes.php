<?php

FW_Router::Connect(
    array (
      'url' => '/',
      'type' => 'app',
      'cache' => false,
      'authentication' => false,
      'module' => 'index',
      'controller' => 'index',
      'action' => 'index'            
    )
);


FW_Router::Connect(
    array (
      'url' => '/json',
      'type' => 'json',
      'cache' => false,
      'authentication' => false,
      'module' => 'index',
      'controller' => 'index',
      'action' => 'jsonTest',                  
    )
);

FW_Router::Connect(
    array (
      'url' => '/xml',
      'type' => 'xml',
      'cache' => false,
      'authentication' => false,
      'module' => 'index',
      'controller' => 'index',
      'action' => 'xmlTest',                  
    )
);

FW_Router::Connect(
    array (
      'url' => '/not-found',
      'type' => 'app',
      'cache' => false,
      'authentication' => false,
      'module' => 'index',
      'controller' => 'index',
      'action' => 'error404'            
    )
);

FW_Router::Connect(
    array (
      'url' => '/forbidden',
      'type' => 'app',
      'cache' => false,
      'authentication' => false,
      'module' => 'index',
      'controller' => 'index',
      'action' => 'error403'            
    )
);



?>
