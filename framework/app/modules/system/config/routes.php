<?php

FW_Router::Connect(
array (
        "url"  => "/not-found",
        "type" => "app",
        "cache" => false,
        "authentication" => false,
        "module" 		 => "system",
        "controller"	 => "error",
        "action"		 => "error404",
        "internal"		 => true,
        "pattern"		 => "#^/not-found[/]*$#",
        "parameterOrder" => array()        
    )
);
FW_Router::Connect(
    array (
        "url"  => "/forbidden",
        "type" => "app",
        "cache" => false,
        "authentication" => false,
        "module" 		 => "system",
        "controller"	 => "error",
        "action"		 => "error403",
        "internal"		 => true,
        "pattern"		 => "#^/forbidden[/]*$#",
        "parameterOrder" => array()        
    )
);

?>