<?php
    class professional_cms_block extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $title;
        protected $description;
        protected $text;        
        protected $author;
        protected $status;        
        protected $created_at;
        protected $block_order;
        
        public static $belongs_to = array( 
            array(
                    "property"    => "author",
                    "table"            => "user",
                    "srcColumn" => "author",
                    "dstColumn" => "username",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            )
        );
        
        public static $has_many = array( 
            array(
                    "property"     => "pages",
                    "table"            => "professional_cms_block_has_pages",
                    "srcColumn" => "id",
                    "dstColumn" => "id_block",
                    "update"        => "restrict",
                    "delete"         => "restrict"
            )
        ); 
                
        public function getId() {
            return $this->id;
        } 
        
        public function getAuthor() {
            return $this->author->first()->getDisplayName();
        }
        
        public function getAuthorJSON() {
            return htmlentities($this->author->first()->getDisplayName(),ENT_QUOTES,"UTF-8");
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));            
        }
        
        public function getTitle() {
            return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");
        }
        
        public function  hasText() {
            return (strlen($this->text)>0);
        }
        
        public function getTitleJSON() {
            return htmlentities($this->getTitle(),ENT_QUOTES,"UTF-8");
        }
        
        public function getDescription() {
            return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");
        }
        
        public function getText() {
            return html_entity_decode($this->text,ENT_QUOTES,"UTF-8");
        }
                
        public function getStatus() {
            return intval($this->status);
        }
        
        public function getStatusJSON() {
            $text     = "";
            $status = $this->getStatus();
            if ($status===1) {
                $text = _("Activo");
            }
            else {
                $text =  _("Inactivo");
            }
            return htmlentities($text,ENT_QUOTES,"UTF-8");
        }
        public function getPages() {
            if (!$this->_isNewRecord) {
                $order = array(
                    array (
                        "column" => "position",
                        "type"       => "ASC"
                    )            
                );
                return $this->pages->find("",$order);
            }
            return null;                
        }
        
        public function getPagesAsArray() {
            $result = array();            
            $pages = $this->getPages();            
            if (count($pages)) {                
                foreach ($pages as $page) {
                    $p              = $page->getPage();
                    if (!is_null($p)) {                                        
                        $result []= $page->getPage()->getId();
                    }
                }
            }                        
            return $result;
        }
    };
?>