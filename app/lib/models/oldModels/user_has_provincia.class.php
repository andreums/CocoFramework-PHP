<?php
class user_has_provincia extends FW_ActiveRecord_Model {
    protected $idprovincia;
    protected $username;
    protected $datos;
    
     public static $belongs_to = array (
            array(
                "property" => "provincia",
                "table"    => "provincia",
                "srcColumn"=> "idprovincia",
                "dstColumn"=> "idprovincia",
                "update"   => "cascade",
                "delete"   => "cascade"
            ),
            array(
                "property" => "usuario",
                "table"    => "user",
                "srcColumn"=> "username",
                "dstColumn"=> "username",
                "update"   => "cascade",
                "delete"   => "cascade"
            )
        );
};
?>