<?php
$config = array(
    "sections" => array (
        "external" => array (
            "index" => array (
                "controllers" => array (
                    "index" => array (
                        "app" => "default"
                    )
                )                                     
            ),
            "admin" => array (
                "controllers" => array (
                ),
                "global" => array( 
                    "app" => "default"
                )                     
            ),
            "content" => array (
                "controllers" => array (
                ),
                "global" => array( 
                    "app" => "default"
                )                     
            ),
            "user" => array (
                "controllers" => array (
                ),
                "global" => array( 
                    "app" => "default"
                )                     
            ),
            "webservices" => array (
                "controllers" => array (
                ),
                "global" => array( 
                    "app" => "default"
                )                     
            )
        ),
        "internal" => array () 
    
    
    ),
    "global"   => array (
        "allowLayouts" => true
    )
);

FW_Config::createConfig("layouts");
FW_Config::setConfig("layouts",$config);
?>