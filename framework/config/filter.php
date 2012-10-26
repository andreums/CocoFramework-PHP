<?php
$config = array(
    "sections" => array (   
        
        ),
        "global" => array (
            "default" => array (
                "Render",                
                "Authentication",
                "Cache"                
            )            
        )         
);

FW_Config::createConfig("filter");
FW_Config::setConfig("filter",$config);
?>