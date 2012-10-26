<?php
    class profesional_has_pages extends FW_ActiveRecord_Model {
        protected $id_profesional;
        protected $id_page;        
        
        public static $belongs_to = array (
            array(
                "property" => "page",
                "table"    => "page",
                "srcColumn"=> "id_page",
                "dstColumn"=> "id",
                "update"   => "restrict",
                "delete"   => "restrict"
            )            
        );
    }

?>