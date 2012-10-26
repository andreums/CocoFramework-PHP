<?php
$config = array(
    "sections" => array (          
            "database" => array (
                
                "lengths" => array (
                    "min" => 6,
                    "max" => 50
                ),
                "datasource" => array (
                    "type"     => "database",
                    "table"    => "user",
                    "username" => "username",
                    "password" => "password",
                    "status"   => "status",
                    "role"     => "role",
                    "crypt"    => "sha1"                
                ),
                "roles" => array (
                    "multiple" => true,
                    "table"    => "role",
                    "join"     => "user_has_roles",
                    "user"     => "username",
                    "role"     => "role"
                ),
                "codes" => array (
                    "success"   => 200,
                    "forbidden" => 403,
                    "blocked"   => 402,
                    "error"     => 500                
                ),
                "usersource" => array (
                    "table"    => "user",
                    "username" => "username",
                    "columns"  => array (
                        "username",
                        "email",
                        "name",
                        "language",
                        "theme",
                        "display_name",
                        "status"                        
                    )
                )           
            ),
            "file" => array (
                "lengths" => array (
                    "min" => 6,
                    "max" => 50
                ),
                "datasource" => array (
                    "type"     => "htpasswd",
                    "filename" => "framework/.htpasswd",
                    "crypt"    => "sha"              
                ),
                "roles" => array (
                    "multiple" => true,
                    "table"    => "role",
                    "join"     => "user_has_roles",
                    "user"     => "username",
                    "role"     => "role"
                ),
                "codes" => array (
                    "success"   => 200,
                    "forbidden" => 403,
                    "blocked"   => 402,
                    "error"     => 500                
                ),
                "usersource" => array (
                    "table"    => "user",
                    "username" => "username",
                    "columns"  => array (
                        "username",
                        "email",
                        "name",
                        "language",
                        "theme",
                        "display_name",
                        "status"                        
                    )
                )              
            ),
            "imap" => array (
                "lengths" => array (
                    "min" => 6,
                    "max" => 50
                ),
                "datasource" => array (
                    "type"     => "imap",
                    "host"     => "imap.gmail.com",                    
                    "port"     => "993"              
                ),
                "roles" => array (
                    "multiple" => true,
                    "table"    => "role",
                    "join"     => "user_has_roles",
                    "user"     => "username",
                    "role"     => "role"
                ),
                "codes" => array (
                    "success"   => 200,
                    "forbidden" => 403,
                    "blocked"   => 402,
                    "error"     => 500                
                ),
                "usersource" => array (
                    "table"    => "user",
                    "username" => "username",
                    "columns"  => array (
                        "username",
                        "email",
                        "name",
                        "language",
                        "theme",
                        "display_name",
                        "status"                        
                    )
                )            
            )                            
        ),
        "global" => array (
            "default" => "database"
        )         
);

FW_Config::createConfig("authentication");
FW_Config::setConfig("authentication",$config);
?>