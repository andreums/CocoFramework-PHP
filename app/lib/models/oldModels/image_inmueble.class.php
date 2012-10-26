<?php
    class image_inmueble extends FW_ActiveRecord_Model {

        protected $id;
        protected $id_inmueble;
        protected $title;
        protected $description;
        protected $filename;
        protected $username;
        protected $created_at;
        protected $status;
        protected $image_order;
        
        
     
     public function getId() {
         return intval($this->id);
     }
     
     public function getIdInmueble() {
         return intval($this->id_inmueble);
     }


    public function getThumb() {
         $url   = FW_Config::getInstance()->get("core.global.baseURL");
         $url .= "/uploads/images/inmuebles/{$this->filename}";
         return $url;                   
     }
         
     public function getFilename() {
         $url   = FW_Config::getInstance()->get("core.global.baseURL");
         $url .= "/uploads/images/inmuebles/{$this->filename}";
         return $url;                   
     }
     
     public function getTitle() {
         return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");
     }
     
     public function getDescription() {
         return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");
     }

    };
?>