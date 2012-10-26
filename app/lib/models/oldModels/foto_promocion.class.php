<?php
class foto_promocion extends FW_ActiveRecord_Model {

    protected $id;
    protected $id_promocion;
    protected $title;
    protected $filename;
    protected $creator;
    protected $created_at;
    protected $status;
    
    /* TIPO de la foto: 
     *  INTEGER (2)
     *  0.  Normal (cualquier foto)
     *  1. Thumbnail (miniatura)
     */
    protected $type;
    
    
    
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