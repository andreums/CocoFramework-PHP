<?php
    class terreno extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_inmueble;
        protected $tipo;   

        public function getTipo() {
            return intval($this->tipo);
        }    

    };
?>
