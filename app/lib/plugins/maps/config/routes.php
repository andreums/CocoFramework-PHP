<?php 

FW_Router::Connect(
array (
  'url' => '/msaludar/:name',
  'type' => 'plugin',
  'cache' => false,
  'plugin' => 'maps',
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
  'pattern' => '#^/msaludar/(?:([^/]+))[/]*$#',
  'parameterOrder' => 
  array (
    0 => 'name',
  ),
)
);
 
?>