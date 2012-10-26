<?php

FW_Router::Connect(
array (
        "url" 			 => "/system/cache",
        "type"           => "cron",
        "cache"          => false,
        "authentication" => false,
        "module" 		 => "cron",
        "controller"	 => "cache",
        "action"		 => "cache",
        "internal"		 => true,
        "pattern"		 => "#^/system/cache[/]*$#",
        "parameterOrder" => array()        
    )
);

FW_Router::Connect(
array (
        "url" 			 => "/system/saludar/:name",
        "type"           => "cron",
        "cache"          => false,
        "authentication" => false,
        "module" 		 => "cron",
        "controller"	 => "cache",
        "action"		 => "saludar",
        "internal"		 => true,
        "pattern"		 => "#^/system/saludar/(?:([^/]+))[/]*$#",
        "parameter"		 => array (
            "name" => array (
                  'name' => 'name',
                  'type' => 'string',
                  'format' => false,
                  'required' => true,
            ),
        ),
        "parameterOrder" => array(
              0 => 'name'   
         )        
    )
);
?>