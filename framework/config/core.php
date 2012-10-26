<?php
$config = array(
    "sections" => array (
        "locale" => array (
            "default" => "ca_ES",
            "hack"    => ".UTF-8"
        ),
        "breadcrumbs" => array (
            "homeText" => "Principal",
            //"textTransform" => ""        
        )
    ),
    "global"    => array (
        "baseURL" => "http://www.cocoframework.org",
        "baseURI" => "//",
        "basePath"=> "/var/www/cocoframeworkphp",
        "title"   => "CocoPHP Framework"
    )
);

FW_Config::createConfig("core");
FW_Config::setConfig("core",$config);
?>
