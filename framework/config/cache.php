<?php
$config = array(
    "sections" => array (
    
        "default"   => array (
            "enabled"    => true,
            "driver"     => "file",
            "parameters" => array (
                
            )
        ),        
        "database" => array (
            "enabled"    => true,
            "driver"     => "database",
            "parameters" => array (                
                "table" => "cache_object"
            )        
        ),
        "memcache" => array (
            "enabled"    => true,
            "driver"     => "memcache",
            "parameters" => array (                
                "host" => "localhost",
                "port" => 11211
            )        
        ),        
        "sqlite" => array (
            "enabled"    => true,
            "driver"     => "SQLite",
            "parameters" => array (                
                
            )        
        )            
    )
);

FW_Config::createConfig("cache");
FW_Config::setConfig("cache",$config);
?>
