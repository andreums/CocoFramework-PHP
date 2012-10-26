<?php
    class anuncio_has_destacado extends FW_ActiveRecord_Model {
        
        protected $id_anuncio;
        protected $id_destacado;
        protected $status;
        protected $date_begin;
        protected $date_end;
        
        
        public static $has_one = array( 
            array(
                "property" => "anuncio",
                "table" => "anuncio",
                "srcColumn" => "id_anuncio",
                "dstColumn" => "id",
                "update" => "restrict",
                "delete" => "restrict"
            ),
            array(
                "property" => "destacado",
                "table" => "destacado",
                "srcColumn" => "id_destacado",
                "dstColumn" => "id",
                "update" => "restrict",
                "delete" => "restrict"
            )
    );
        
    };
?>    
