<?php
class documento_inmueble extends FW_ActiveRecord_Model {

    protected $id;
    protected $id_inmueble;
    protected $title;
    protected $description;
    protected $filename;    
    protected $username;
    protected $created_at;
    protected $status;
    
    /*
     * Type: Tipo mime
     * 
     *  0 => PDF
     *  1 => TXT
     *  2 => Word
     *  3 => OpenOffice.org Doc
     *  4 => Autocad
     */
    protected $type;
    
    public static $belongs_to = array (
         array(
             "property"    => "inmueble",
             "table"            => "inmueble",
             "srcColumn" => "id_inmueble",
             "dstColumn" => "id",
             "update"        => "restrict",
             "delete"         => "restrict"
        ),
        array(
             "property"    => "username",
             "table"           => "user",
             "srcColumn"=> "username",
             "dstColumn"=> "username",
             "update"        => "restrict",
             "delete"         => "restrict"
        )
     ); 

};
?>