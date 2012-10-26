<?php
    class professional_cms_block_has_pages extends FW_ActiveRecord_Model {
        
        protected $id_block;
        protected $id_page;
        protected $position;
        
           public static $belongs_to = array( 
            array(
                    "property"    => "block",
                    "table"            => "professional_cms_block",
                    "srcColumn" => "id_block",
                    "dstColumn" => "id",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            )
        );

        public static $has_one = array( 
            array(
                    "property"    => "page",
                    "table"            => "professional_page",
                    "srcColumn" => "id_page",
                    "dstColumn" => "id",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            )
        );
        
        
        public function getPage() {            
            if ($this->page->hasResult()) {
                return $this->page->first();
            }
        }
    };
?>    
