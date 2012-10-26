<?php
    class destacado extends FW_ActiveRecord_Model {

        protected $id;
        protected $name;
        protected $description;
        protected $created_at;
        protected $username;
        protected $status;
        protected $css_classes;
        
        public static $belongs_to = array( array(
                "property" => "username",
                "table" => "user",
                "srcColumn" => "username",
                "dstColumn" => "username",
                "update" => "restrict",
                "delete" => "restrict"
        ));
            
            
        public function getId() {
            return $this->id;
        }
        
        public function getAuthor() {
            return $this->username->first()->getDisplayName();
        }
        
        public function getAuthorJSON() {
            return htmlentities($this->username->first()->getDisplayName(),ENT_QUOTES,"UTF-8");
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s", strtotime($this -> created_at));
        }
        
        public function getNameJSON() {
            return $this->name;
        }
        
        public function getName() {
            return html_entity_decode($this->name, ENT_QUOTES, "UTF-8");
        }

        public function getDescription() {
            return html_entity_decode($this->description, ENT_QUOTES, "UTF-8");
        }
        
        public function getStatus() {
            return intval($this->status);
        }
        
        public function getStatusAsText() {
            if (intval($this->status)===1) {
                return _("Activo");
            }
            return _("Inactivo");
        }
        
        public function getCssClasses() {
            return $this->css_classes;
        }
        
        
        public function getAnuncios($status="all") {
            $anuncios  = array();
            $database = $this->database();
            $prefix        = $database->getPrefix();
            
            if ($status==="all") {
                $query        = " SELECT id_anuncio FROM {$prefix}anuncio_has_destacado WHERE id_destacado='{$this->id}' ";
            }
            else if ($status==="active") {
                $query        = " SELECT id_anuncio FROM {$prefix}anuncio_has_destacado WHERE id_destacado='{$this->id}' AND status='1'";
            }
            else if ($status==="inactive") {
                $query        = " SELECT id_anuncio FROM {$prefix}anuncio_has_destacado WHERE id_destacado='{$this->id}' AND status='0'";
            }
            else {
                $query        = " SELECT id_anuncio FROM {$prefix}anuncio_has_destacado WHERE id_destacado='{$this->id}' ";
            }
            
            $database->query($query);
            if ($database->numRows()>0) {
                while ($result=$database->fetchRow) {
                    $anuncio = anuncio::find(" id='{$result[0]}' ");
                    if ($anuncio->hasResult()) {
                        $anuncios []= $anuncio->first();
                    }                    
                }                  
            }
            return $anuncios;
        }

    };
?>
