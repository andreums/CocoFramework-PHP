<?php
$config = array(
    "sections" => array (       
        "default" => array (
        	"smtp"	=> array (
                "security" => true,
            	"host"	   => "smtp.gmail.com",
                "port"	   => 587 ,
                "authentication" => array (
                    "username" => "yourusername",
                    "password" => "yourpassword"
                )
            ),
            "identity" => array (
                "from"  => "Mail from <mailfrom@serverfrom.tld>",
                "email" => "Mail from <mailfrom@serverfrom.tld>"                 
            )
        )                      
    ),
    "global" => array (
        "defaultAccount" => "default",
            "paths" => array (
                "template"    => "app/resources/mail/templates"
            )
        )
    );
    FW_Config::createConfig("mail");
    FW_Config::setConfig("mail", $config);
?>
