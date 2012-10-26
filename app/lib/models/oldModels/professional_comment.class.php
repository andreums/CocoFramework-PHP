<?php

	class professional_comment extends FW_ActiveRecord_Model {
		
		protected $id;
        protected $id_news;        
        protected $created_at;
        protected $title;                
        protected $content;                
        protected $author;
        protected $status;
        protected $ip;
        protected $is_user;
        
        
        public static $belongs_to = array (
            array(
                "property" => "news",
                "table"    => "news",
                "srcColumn"=> "id_news",
                "dstColumn"=> "id",
                "update"   => "cascade",
                "delete"   => "cascade"
            )
        );
        
       public static $has_one = array (       		
            array(
                "property" => "author",
                "table"    => "user",
                "srcColumn"=> "author",
                "dstColumn"=> "username",
                "update"   => "restrict",
                "delete"   => "restrict"
            )
        );
        
        public function getId() {
            return $this->id;
        }
        
        public function getIp() {
            return $this->ip;
        }
        
        public function getAuthor() {
            $result = "";
            if (intval($this->is_user)===1) {
                $author = user::find("username='{$this->author}' ");
                if ($author->hasResult()) {
                    $result = $author->first()->getDisplayName();
                }
            }
            else {
                $result = $this->author;                             
            }
            return $result;
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }
        
	    public function getTitle() {
	        return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");
	    }
        
        public function getContent() {
            return html_entity_decode($this->content,ENT_QUOTES,"UTF-8");
        }

	};
?>