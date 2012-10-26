<?php
    class anuncio_video extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_anuncio;
        protected $title;
        protected $description;
        protected $filename;
        protected $username;
        protected $created_at;
        protected $status;
        protected $owner;
        
        public function getId() {
            return intval($this->id);
        }
        
        public function getTitle() {
            return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");
        }
        
        public function getTitleJSON() {
            return htmlentities($this->getTitle(),ENT_QUOTES,"UTF-8");
        }
        
        public function getDescription() {
            return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");
        }
        
        public function getIdAnuncio() {
            return $this->id_anuncio;
        }
        
        
        public function getAuthor() {
            $user = user::find(" username='{$this->username}' ");
            if ($user->hasResult()) {
                return $user->first()->getDisplayName();
            }            
        }
        
        public function getOwner() {
            $user = user::find(" username='{$this->owner}' ");
            if ($user->hasResult()) {
                return $user->first()->getDisplayName();                
            }
        }
        
        public function getOwnerJSON() {
            return htmlentities($this->getOwner(),ENT_QUOTES,"UTF-8");
        }
        
        public function getOwnerUsername() {
            return $this->owner;
        }
        
        public function getAnuncio() {
            $result     = "";
            $anuncio = anuncio::find(" id='{$this->id_anuncio}'");
            if ($anuncio->hasResult()) {
                $anuncio = $anuncio->first();
                $result = "{$this->id_anuncio} - {$anuncio->getTitle()} " ;
            }            
            $result = html_entity_decode($result,ENT_QUOTES,"UTF-8");
            return $result;            
        }
        
        public function getAnuncioLink() {
            $result     = "";
            $anuncio = anuncio::find(" id='{$this->id_anuncio}'");
            if ($anuncio->hasResult()) {
                $anuncio = $anuncio->first();
                $title = "{$this->id_anuncio} - {$anuncio->getTitle()} " ;
            }            
            $title = html_entity_decode($title,ENT_QUOTES,"UTF-8");
            $link  = html::link_to_internal("anuncios","anuncioFrontend","displayAnuncio",$title,array("slug"=>$anuncio->getSlug(),"id"=>$anuncio->getId()));
            return $link;            
        }
        
        public function getAnuncioJSON() {
            return (htmlentities($this->getAnuncio(),ENT_QUOTES,"UTF-8"));            
        }
        
        public function getTipoAnuncio() {
            $result = "";
            $anuncio = anuncio::find(" id='{$this->id_anuncio}'");
            if ($anuncio->hasResult()) {
                $anuncio = $anuncio->first();
                $result = $anuncio->getInmuebleTypeAsJSON();                
            }            
            $result = html_entity_decode($result,ENT_QUOTES,"UTF-8");
            return $result;            
        }
        public function getTipoAnuncioJSON() {
            return (htmlentities($this->getTipoAnuncio(),ENT_QUOTES,"UTF-8"));                        
        }
        
        
        public function getDate() {
            return date("Y-m-d H:i:s");            
        }
        
        public function getStatusJSON() {
            $status = intval($this->status);
            if ($status===1) {
                $status = _("Activo");
            }
            else {
                $status = _("Inactivo");
            }
            return $status;
        }
        
        public function getStatusAsText() {
            $status = intval($this->status);
            if ($status===1) {
                $status = _("Activo");
            }
            else {
                $status = _("Inactivo");
            }
            return $status;
        }
        
        public function getFilename() {
            return $this->filename;
        }
        
        public function hasVideoUploaded() {
            return ($this->filename!==null);
        }
        
        
        
    };
?>    
