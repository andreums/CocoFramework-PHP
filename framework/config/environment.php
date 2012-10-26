<?php
$config = array(
    "sections" => array (

        "develop"   => array (
             "log"      => array("all"),
             "debug"    => array("all"),
             "error"    => array("all"),
             "database" => array("default"),
             "cache"    => array("default")
        ),
        "testing"   => array (
             "log"      => array("all"),
             "debug"    => array("all"),
             "error"    => array("all"),
             "database" => array("default"),
             "cache"    => array("default")
        ),
        "production"   => array (
			 "log"      => array("error"),
             "debug"    => array("default"),
             "error"    => array("fatal"),
             "database" => array("default"),
             "cache"    => array("default")
        ),
    ),
    "global"    => array (
        "environmentInUse" => "develop"

    )
);

FW_Config::createConfig("environment");
FW_Config::setConfig("environment",$config);
?>