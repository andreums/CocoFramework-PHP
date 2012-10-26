<?php
    class contact_reply extends  FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_contact;
        protected $email;
        protected $username;
        protected $created_at;
        protected $subject;
        protected $message;
        
         public static $belongs_to = array(
             
            array(
                    "property"    => "username",
                    "table"            => "user",
                    "srcColumn" => "username",
                    "dstColumn" => "username",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            )      
        );        
        
        public function getSubject() {
            return html_entity_decode($this->subject,ENT_QUOTES,"UTF-8");            
        }
        
        public function getMessage() {
            return html_entity_decode($this->message,ENT_QUOTES,"UTF-8");            
        }
        
        
        public function getAuthor() {
            return $this->username->first()->getDisplayName();
        }
        
        public function getSubjectJSON() {
            return $this->subject;
        }
        
        public function getEmail() {
            return html_entity_decode($this->email,ENT_QUOTES,"UTF-8");
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }
        
     
        
    };
?>    
