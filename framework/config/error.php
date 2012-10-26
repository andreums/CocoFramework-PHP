<?php
$config = array(
    "sections" => array (        
        "403" => array (
            "module"     => "index",
            "controller" => "index",
            "action"     => "error403", 
            "internal"   => false,
            "type"	     => "app"
        ),
        "404" => array (
            "module"     => "index",
            "controller" => "index",
            "action"     => "error404", 
            "internal"   => false,
            "type"	     => "app"
        ),
        "500" => array (
            "module"     => "system",
            "controller" => "error", 
            "action"     => "error404", 
            "internal"   => false,
            "type"	     => "app"
        )        
    ),
    "global" => array (
    )         
);

FW_Config::createConfig("error");
FW_Config::setConfig("error",$config);
?>