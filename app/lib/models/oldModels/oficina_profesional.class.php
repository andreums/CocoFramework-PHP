<?php
    class oficina_profesional extends FW_ActiveRecord_Model {
        
        protected $id;        
        protected $nombre;
        protected $slug;
        protected $numero;
        protected $via;
        protected $municipio;
        protected $codigo_postal;
        protected $provincia;
        protected $estado;
        protected $pais;
        protected $telefono;
        protected $fax;
        protected $description;
        protected $status;
        protected $created_at; 
        protected $creator;
        protected $persona_contacto;
        protected $email;        
        protected $map_lat;
        protected $map_lng;
        
        
        public function getId() {
            return intval($this->id);
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }
        
        public function getOwner() {
            $conditions = " status='1' AND username='{$this->creator}' ";
            $owner         = user::find($conditions);
            if ($owner->hasResult()) {
                return $owner->first();
            }
        }
        
        
        public function getStatus() {
            if ($this->status==="1") {
                return _("Visible");            
            }
            return _("No visible");
        } 
        
        public function getDireccion() {          
            $direccion = "{$this->via} {$this->numero}, {$this->municipio}, {$this->codigo_postal} ,{$this->provincia} ,{$this->estado} ,{$this->pais}";            
            return html_entity_decode($direccion,ENT_QUOTES,"UTF-8");
        }
        
        public function getInfoWindowText() {        
            $text = "<h2>{$this->nombre}</h2><br/>".$this->getDireccion()."<br/>"._("Teléfono")."{$this->getTelefono()}<br/>"._("Fax")."{$this->getFax()}";
            return $text; 
        }
        public function getSlug() {
            return $this->slug;
        }
        
        public function getNombre() {
            return html_entity_decode($this->nombre,ENT_QUOTES,"UTF-8");
        }
        
        public function getTelefono() {
            return html_entity_decode($this->telefono,ENT_QUOTES,"UTF-8");
        }
        
        public function getFax() {
            return html_entity_decode($this->fax,ENT_QUOTES,"UTF-8");
        }
        
        public function getPersonaContacto() {
            return html_entity_decode($this->persona_contacto,ENT_QUOTES,"UTF-8");
        }
        
        public function getEmail() {
            return html_entity_decode($this->email,ENT_QUOTES,"UTF-8");
        }
        public function getDescripcion() {
            return trim(html_entity_decode($this->description,ENT_COMPAT,"UTF-8"),' ');
        }
        
        
        public function getVia() {
            return html_entity_decode($this->via,ENT_QUOTES,"UTF-8");
        }
        
        public function getNumero() {
            return html_entity_decode($this->numero,ENT_QUOTES,"UTF-8");
        }
        
        public function getMunicipio() {
            return html_entity_decode($this->municipio,ENT_QUOTES,"UTF-8");
        }
        
        public function getCodigoPostal() {
            return html_entity_decode($this->codigo_postal,ENT_QUOTES,"UTF-8");
        }
        
        public function getProvincia() {
            return html_entity_decode($this->provincia,ENT_QUOTES,"UTF-8");
        }
        
        public function getEstado() {
            return html_entity_decode($this->estado,ENT_QUOTES,"UTF-8");
        }
        
        public function getPais() {
            return html_entity_decode($this->pais,ENT_QUOTES,"UTF-8");
        }
        
         public function getMapDiv() {
            $name = "map_{$this->id}";
            return $name;             
        }
         
         public function getLat() {
             return $this->map_lat;
         }
         
         public function getLng() {
             return $this->map_lng;
         }
        
         public function getMap() {
            $code  = "";
            $name = $this->getMapDiv();
            $maps = new maps();
            $map   = new map($name,16,"hybrid");
            $map->setCenterByAddress($this->getDireccion());
            $map->addMarker($this->getDireccion(),$this->getNombre(),$this->getInfoWindowText());            
            $code = $map->display();
            return $code;
        }
         
        public function getMapjQuery() {
            $mapCode = $this->getMap();
            $code           = "<script type=\"text/javascript\">";
            $code          .= "jQuery(function() {";            
            $code          .=  $mapCode;
            $code          .= "});";
            $code          .=  "</script>";            
            return $code;
        }
         
        
        public function getPropietario() {
            $owner                  = null;
            $tipoPropietario = substr($this->id_objeto,0,1); 
            $propietario         = substr($this->id_objeto,1);
            
            switch ($tipoPropietario) {
                case "P":
                    $owner = promotor::find("id='{$this->id_objeto}'");                                        
                break;
                    
                case "I":
                    $owner = promotor::find("id='{$this->id_objeto}'");
               break;
                    
                case "B":
                    $owner = promotor::find("id='{$this->id_objeto}'");
                break;                
            };
            
            if ($owner!==null && $owner->hasResult()) {
                $owner = $owner->first();                               
            }
            return $owner;
        }
        
        
        
    };
?>
