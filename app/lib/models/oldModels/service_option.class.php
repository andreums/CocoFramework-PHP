<?php
    class service_option extends  FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_service;
        protected $name;
        protected $slug;
        protected $description;
        protected $status;
        protected $created_at;
        protected $username;
        protected $unit_price;
        protected $tax;
        protected $price;
        
        public function getId() {
            return intval($this->id);
        } 
        
        public function getName() {
            return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");
        }
        
        public function getDescription() {
            return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");
        }
        
        public function getNameJSON() {
            return htmlentities($this->getName(),ENT_QUOTES,"UTF-8");            
        }
        
        public function getDescriptionJSON() {
            return htmlentities($this->getDescription(),ENT_QUOTES,"UTF-8");            
        }
        
        public function getPrice() {            
            return floatval($this->unit_price);
        }
        
        
        
                    
    };
?>    
        