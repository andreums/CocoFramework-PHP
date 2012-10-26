<?php
$config = array(
    "sections" => array (), 
    "global"    => array (
        "enabled" => true,         
        "database" => array (
            "source"   => "user",
            "column"   => "theme",
            "username" => "username"            
        ),
        "defaultTheme"         => "default",
        "userThemeHasPriority" => true
    )
);

FW_Config::createConfig("style");
FW_Config::setConfig("style",$config);
?>