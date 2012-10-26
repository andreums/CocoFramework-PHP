<?php
    class extra_inmueble_category extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $title;
        protected $description;
        protected $created_at;
        protected $creator;
        protected $status;
        protected $inmueble_type;
        protected $inmueble_subtype;
                
               
        
        
        public static $has_many = array (
            array(
                    "property"      => "extras",
                    "table"            => "extra_inmueble",
                    "srcColumn"  => "id",
                    "dstColumn"  => "id_category",
                    "update"         => "cascade",
                    "delete"          => "cascade"
            )            
        );
        
        public function getAuthor() {
            $creator = user::find(" username='{$this->creator}' ");            
            if ($creator->hasResult()) {
                return $creator->first()->getDisplayName();                
            }            
            
        }
        
        public function getTitle() {
            return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");
        }
        
        public function getDescription() {
            return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");
        }
        
        public function getInmuebleType() {
            $options = array (
                0 =>  _("Vivienda"),
                1 =>  _("Oficina"),
                2 =>  _("Nave"),
                3 =>  _("Local"),
                4 =>  _("Garaje"),
                5 =>  _("Trastero"),
                6 =>  _("Edificio"),
                7 =>  _("Hotel"),
                8 =>  _("Suelo/Terreno")
            );         
            return $options[$this->inmueble_type];
        }
        
        public function getInmuebleSubType() {
            $options = array (
                -1 => _("No aplicable"),            
                0 => _("Piso"),
                1 =>_("Casa/Chalet"),
                2 => _("Ático"),
                3 =>_("Estudio"),
                4 =>_("Dúplex"),
                5 =>_("Planta baja"),
                6 =>_("Adosado"),
                7 =>_("Loft"),
                8 =>_("Apartamento"),
                9 =>_("Casa de madera"),
                10 => _("Habitación")                                                                  
            );
            return $options[$this->inmueble_subtype];
        }
        
        public function getId() {
            return $this->id;
        }
        
        public function getExtras() {
            return $this->extras;
        }
       
        public function getEnabledExtras() {
            return $this->extras->find("status='1' ");            
        }
        
        public function getDisabledExtras() {
            return $this->extras->find("status='0' ");
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }       
        
        public function getCreator() {
            return $this->creator->first()->getDisplayName();
        }
        
        public function isEnabled() {
            if (intval($this->status)===1) {
                return true;
            }
            return false;
         }
        
        public function getStatus() {
            if ($this->isEnabled()) {
                return _("Habilitada");
            }
            return _("Inhabilitada");
        }
        
        public function getTipoInmueble() {
            $type = "";
            $tipo  = intval($this->inmueble_type);                        
            
            switch ($tipo) {
                    case 0:
                    default:
                        $type = _("Todos los inmuebles");
                    break;
            };
            return $type;
        }         
                
    };
?>

