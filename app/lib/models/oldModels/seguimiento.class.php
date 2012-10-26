<?php
    class seguimiento extends FW_ActiveRecord_Model  {
        
        protected $id;
        protected $title;
        protected $username;
        protected $created_at;
        protected $type;
        protected $id_object;
        protected $comment;
        
        public static $belongs_to = array (
        array(
            "property"   => "username",
            "table"           => "user",
            "srcColumn"=> "username",
            "dstColumn"=> "username",
            "update"       => "restrict",
            "delete"        => "restrict"
        )
    );
    
    public function getType() {
        return "Vivienda . {$this->id_object}";        
    }
    
    public function getTitle() {
        return utf8_encode($this->title);
    }
    
    public function getComment() {
        return utf8_encode($this->comment);
    }
    
    public function getDate() {            
            return (date("d/m/Y H:i:s",strtotime($this->created_at)));
        }
            
    };
?>