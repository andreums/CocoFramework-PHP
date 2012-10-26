<?php
    class anuncio_favorito extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $username;
        protected $id_anuncio;
        protected $created_at;
        
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
        
        public function getAnuncio() {
            $anuncio = anuncio::find(" id='{$this->id_anuncio}' AND status='1' ");
            if ($anuncio->hasResult()) {
                return $anuncio->first();
            }
        }
        
        public function getUserName(){
            return $this->$username;
        }
        
        public function getTitle() {
            $anuncio = $this->getAnuncio();
            $title         = htmlentities($anuncio->getTitle());
            $link          = html::link_to_internal("anuncios","anuncioDisplay","displayAnuncio",$title,array("slug"=>$anuncio->getSlug(),"id"=>$anuncio->getId()));
            return $link;
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }
        
    };
    
    
?>
