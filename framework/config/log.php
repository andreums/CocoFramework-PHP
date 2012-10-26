<?php
$config = array(
    "sections" => array (

        "file"	=> array (
            "path"        => "framework/log",
            "transaction" => false,
        	"append"	  => true,
            "lock_mode"	  => LOCK_EX|LOCK_NB   

        ),
        "database" => array (
            "table" => "log",
            
        )
    ),    
    "global" => array (
        "active" => true
    )         
);

FW_Config::createConfig("log");
FW_Config::setConfig("log",$config);
?>