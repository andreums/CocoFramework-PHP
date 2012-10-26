<?php
class documento_promocion extends FW_ActiveRecord_Model {

    protected $id;
    protected $id_promocion;
    protected $title;
    protected $filename;
    protected $description;
    protected $creator;
    protected $created_at;
    protected $status;
    
    public static $belongs_to = array (
         array(
             "property"    => "promocion",
             "table"           => "promocion",
             "srcColumn"=> "id_promocion",
             "dstColumn"=> "id",
             "update"        => "restrict",
             "delete"         => "restrict"
        )
     ); 

};
?>