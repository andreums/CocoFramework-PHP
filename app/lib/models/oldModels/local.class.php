<?php
    class local extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_inmueble;
        protected $ubicacion;      
        
        public function getUbicacion() {
            return intval($this->ubicacion);
        }  
        
    };
?>