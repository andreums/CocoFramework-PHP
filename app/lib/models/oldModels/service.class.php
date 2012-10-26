<?php
    class service extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_category;
        protected $name;
        protected $short_description;
        protected $description;
        protected $conditions;
        protected $slug;
        protected $created_at;
        protected $username;
        protected $status;
        
        public function getName() {
            return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");
        }
        
        public function getShortDescription() {
            return html_entity_decode($this->short_description,ENT_QUOTES,"UTF-8");
        }
        
        public function getDescription() {
            return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");
        }
        
        public function getConditions() {
            return html_entity_decode($this->conditions,ENT_QUOTES,"UTF-8");
        }
        
        public function getId() {
            return intval($this->id);
        }
        
        public function getSlug() {
            return $this->slug;            
        }
        
        public function getStatus() {
            return intval($this->status);
        }
        
        public function getOptions() {
            $result     = array();
            $result  [-1]= array("value"=>_("Por favor, seleccione una opción"),"selected"=>true);
            $options  = service_option::find("id_service='{$this->id}' AND status='1' ");
            if ($options->hasResult()) {
                foreach ($options as $option) {
                    $result [$option->getId()]= array("value"=>$option->getName());                    
                }                
            }
            return $result;            
        }
        
        public function getOption($id) {
            $option = service_option::find("id_service='{$this->id}' AND status='1'  AND id='{$id}' ");
            if ($option->hasResult()) {
                return $option->first();
            }                        
        }
        
    };
?>    
        