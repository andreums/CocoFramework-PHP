<?php
$validations = array (
    "controllers" => array (
           
        "index" => array (                        
            "register" => array (
                "a" => array(
                    "source"       => "post",
                    "validators" => array (
                        "required" => array (
                            "messages"  => array (
                                "success"  => "",
                                "error"       => _("El valor A debe tener al menos un carácter")
                            )
                        ),
                        "valueLessThan" => array (
                            "messages"  => array (
                                "success"  => "",
                                "error"       => _("El valor A {{value}} debe tener un valor menor de 5")
                            ),
                            "config" => array("max"=>5)
                        )                        
                    )                    
                ),
                "b" => array(
                    "source"       => "post",
                    "validators" => array (
                        "required" => array (
                            "messages"  => array (
                                "success"  => "",
                                "error"       => _("El valor B debe tener al menos un carácter")
                            )
                        ),
                        "valueLessThan" => array (
                            "messages"  => array (
                                "success"  => "",
                                "error"       => _("El valor B  {{value}} debe tener un valor menor de 5")
                            ),
                            "config" => array("max"=>5)
                        )                       
                    )                      
                )              
            )      
        )
        
    )
);
?>