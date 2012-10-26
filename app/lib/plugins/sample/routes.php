<?php

FW_Router::Connect(
array (
  'url' => '/saludar/:name',
  'type' => 'plugin',
  'cache' => false,
  'plugin' => 'sample',
  'action' => 'saludar',
  'internal' => true,
  'mime' => 'text/html',
  'parameters' => 
  array (
    'name' => 
    array (
      'name' => 'name',
      'type' => 'string',
      'format' => false,
    ),
  ),
  'authentication' => false,
  'pattern' => '#^/saludar/(?:([^/]+))[/]*$#',
  'parameterOrder' => 
  array (
    0 => 'name',
  ),
)
);



?>