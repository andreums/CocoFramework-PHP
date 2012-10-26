<?php

$config = array (
  'sections' => 
  array (
    'default' => 
    array (
      'driver' => 'pdo',
      'host' => 'localhost',
      'database' => 'mydatabase',
      'username' => 'username',
      'password' => 'password',
      'prefix' => 'prefix_',
      'dsn' => 'mysql:host=localhost;dbname=mydatabase',            
    ), 
  ),
  'global' => 
  array (
    'activeRecord' => 
    array (
      'builder' => 'SQL',
      'builderOptions' => 
      array (
        'databaseConnection' => 'default',
      ),
      'encoding' => 'UTF-8',
    ),
  ),
);

FW_Config::createConfig("database");
FW_Config::setConfig("database",$config);

?>
