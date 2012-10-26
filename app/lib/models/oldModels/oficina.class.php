<?php
    class oficina extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_inmueble;
        protected $distribucion;
        protected $tipo_edificio;
        protected $exterior;        
        
        public function getDistribucion() {
            return intval($this->distribucion);
        }
        
        public function getTipoEdificio() {
            return intval($this->tipo_edificio);
        }
        
        public function getExterior() {
           return intval($this->exterior);
        }
        
        
    };
?>