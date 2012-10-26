<?php

FW_Router::Connect(
array (
  'url' => '/sms/gateway/accounts',
  'type' => 'plugin',
  'cache' => false,
  'authentication' => false,
  'plugin' => 'SMSGateway',
  'action' => 'accounts',
  'pattern' => '#^/sms/gateway/accounts[/]*$#',
  'parameterOrder' => array (),
  'mime' => 'text/html'
)
);

FW_Router::Connect(
array (
  'url' => '/sms/gateway/accounts/edit/:account',
  'type' => 'plugin',
  'cache' => false,
  'authentication' => false,
  'plugin' => 'SMSGateway',
  'action' => 'editAccount',
  'parameterOrder' => array ( 0=>"account"),
  'parameters' => array (
    'account' => array (
        'name'   => 'account',
        'type'   => 'string',
        'format' => false
    )
  ),
  'mime' => 'text/html'
)
);


FW_Router::Connect(
array (
  'url' => '/sms/gateway/accounts/create',
  'type' => 'plugin',
  'cache' => false,
  'authentication' => false,
  'plugin' => 'SMSGateway',
  'action' => 'createAccount',
  'parameterOrder' => array ( ),
  'parameters' => array (),
  'mime' => 'text/html'
)
);


FW_Router::Connect(
array (
  'url' => '/sms/gateway/test',
  'type' => 'plugin',
  'cache' => false,
  'authentication' => false,
  'plugin' => 'SMSGateway',
  'action' => 'test',
  'parameterOrder' => array (),
  'mime' => 'text/html'
)
);


?>