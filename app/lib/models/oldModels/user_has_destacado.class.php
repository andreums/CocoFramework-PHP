<?php
    class user_has_destacado extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $id_destacado;
        protected $id_order;
        protected $id_anuncio;
        protected $username;
        protected $date_begin;
        protected $date_end;
        protected $status;
        protected $created_at;
        
        public function getId() {
            return intval($this->id);
        }
        
        public function getStatus() {
            return intval($this->status);
        }
        
        public function getStatusAsText() {
            if ($this->getStatus()===1) {
                return _("Activo");                
            }
            return _("Inactivo");
        }
        
        public function getOrder() {
            return $this->id_order;
        }
        
        public function getType() {
            $type            = "";
            $destacado = destacado::find(" id='{$this->id_destacado}' ");
            if ($destacado->hasResult()) {
                $type = $destacado->first()->getName();
            }
            return $type;
        }
        
       public function getTypeJSON() {                      
           return htmlentities($this->getType(),ENT_QUOTES,"UTF-8");
       }
       
       public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));            
        }
        
        public function getDateBegin() {
            return date("d/m/Y",strtotime($this->date_begin));            
        }
        
        public function getDateEnd() {
            return date("d/m/Y",strtotime($this->date_end));
        }
        
        public function getAnuncio() {
            $result = "";            
            $id         = $this->id_anuncio;
            if ($id!==null) {
                $anuncio = anuncio::find(" id='{$id}' AND status='1' AND username='{$this->username}' ");
                if ($anuncio->hasResult()) {
                    $anuncio = $anuncio->first();
                    $result     = $anuncio->getTitle();
                }                
            }
            else {
                $result = _("Sin anuncio asignado");
            }
            return $result;            
        }
        
        public function getAnuncioJSON() {
            return htmlentities($this->getAnuncio(),ENT_QUOTES,"UTF-8");
        }
        
    };
?>    
