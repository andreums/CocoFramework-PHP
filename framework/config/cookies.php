<?php
$config = array(
    "sections" => array (),
    "global"     => array (
        "defaultDomain" => "www.vendoyodev.es",
        "defaultExpires" => 3600,
        "defaultPath"      => "/",
         "defaultHttpOnly" => true,
         "defaultSecure" => false    
    )
);

FW_Config::createConfig("cookies");
FW_Config::setConfig("cookies",$config);
?>