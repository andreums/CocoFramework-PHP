<?php
    class provincia extends FW_ActiveRecord_Model {
        protected $idprovincia;
        protected $provincia;
        protected $provinciaseo;
        protected $provincia3;

        public function capitalize($value) {
            return strtoupper($value);
        }
        
        public function decapitalize($value) {
            return strtolower($value);
        }
        
        public function reverse($value) {
            return strrev($value);
        }

        
        public static $has_many = array (
            array(
                "property" => "poblaciones",
                "table"    => "poblacion",
                "srcColumn"=> "idprovincia",
                "dstColumn"=> "idprovincia",
                "update"   => "cascade",
                "delete"   => "cascade"
            )
        );
        
        public static $has_many_by_sql = array (
            array(
                "property" => "poblaciones",
                "table"    => "poblacion",
                "srcColumn"=> "idprovincia",
                "dstColumn"=> "idprovincia",
                "update"   => "cascade",
                "delete"   => "cascade",
                "conditions" => array("status='1'")
            )
        );
        
      
    };
?>