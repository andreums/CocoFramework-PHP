<?php
    class edificio extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_inmueble;
        protected $num_plantas;
        protected $tipo;
        
         public function getTipo() {
            return intval($this->tipo);
        }
        
        public function getNumPlantas() {
            return intval($this->num_plantas);            
        }
        
    };
?>