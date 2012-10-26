<?php
$config = array(
    "sections" => array (
        "default"   => array (
            "builder" 		 => "SQL",
            "builderOptions" => array (
                "databaseConnection" => "default"
            ),
            "encoding"	=> "ISO-8559-15"
        )
    ),
    "global"  => array ()
);

FW_Config::createConfig("activerecord");
FW_Config::setConfig("activerecord",$config);
?>