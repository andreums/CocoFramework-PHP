<?php
    class piso extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_inmueble;
        protected $num_habitaciones;
        protected $num_banyos;                
        protected $planta;        
        
        public function getNumHabitaciones() {
            return $this->num_habitaciones;
        }
        
        public function getNumBanyos() {
            return $this->num_banyos;
        }
        
        public function getPlanta() {
            return intval($this->planta);
        }
                
    };
?>