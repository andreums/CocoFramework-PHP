<?php
    class promocion_has_anuncio extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_anuncio;
        protected $id_promocion;     
        
        public function getIdAnuncio() {
            return intval($this->id_anuncio);
        }
        
    };
?>