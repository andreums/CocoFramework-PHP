<?php 
    class casa_madera extends inmueble {
        protected $id;
        protected $id_inmueble;
        protected $num_habitaciones;
        protected $num_banyos;
        protected $num_plantas;
        
        public function getNumBanyos() {
            return intval($this->num_banyos);
        }               
        
        public function getNumHabitaciones() {
            return intval($this->num_habitaciones);
        }
        
        public function getNumPlantas() {
            return intval($this->num_plantas);
        }
    };
?>
