<?php
$config = array(
    "sections" => array (
        "geocoder" => array (
            "key"      => "",
            "endpoint" => "http://maps.google.com/maps/api/geocode/json?sensor=true&language=LANGUAGE",
            "locale"   => "es_ES"
        )
    ),    
    "global"    => array (
    )
);

FW_Config::createConfig("google");
FW_Config::setConfig("google",$config);
?>
