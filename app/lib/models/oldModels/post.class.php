<?php
    class post extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $title;
        protected $excerpt;
        protected $content;
        protected $created_at;        
        protected $author;
        protected $is_commentable;
        protected $permalink;        
        protected $status;
        
        
        public function isCommentable() {
            if ($this->is_commentable==="1") {
                return true; 
            }
            return false;
        }
        
        public function getAuthor() {
            return $this->author->first()->getDisplayName();
        }
        
        public function getDate() {            
            return timeHelper::getFullHumanDate($this->created_at);
        }
        
        public function getImage() {
            $image   = "";
            $base      = FW_Config::getInstance()->get("core.global.baseURL");            
            $pattern = '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
            if (preg_match($pattern,(string) $this->content, $matches)) {
                $image = $matches[1];                                
            }
            if (empty($image)) {
                $image = "{$base}/images/noimage.png";
            }            
            return $image;
        }
        
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
        
        public static $has_many = array (
            array(
                "property" => "comments",
                "table"    => "comment",
                "srcColumn"=> "id",
                "dstColumn"=> "id_post",
                "update"   => "cascade",
                "delete"   => "cascade"
            )
        );
        
       
    };
?>