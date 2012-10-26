<?php

	class comentario_inmueble extends FW_ActiveRecord_Model {
		
		protected $id;
        protected $id_inmueble;
        protected $username;
        protected $created_at;                
        protected $content;
        protected $active;
        
        
        
                
	    public function getDate() {            
            return timeHelper::getFullHumanDate($this->created_at);
        }

	};
?>