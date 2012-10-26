<?php
    class extra_inmueble extends FW_ActiveRecord_Model {
        
        protected $id;       
        protected $id_category; 
        protected $name;
        protected $description;
        protected $type;
        protected $default_value;
        protected $status;
        protected $is_searchable;
        protected $created_at;
        protected $creator;
        
        public static $belongs_to = array(
            array(
                    "property"    => "category",
                    "table"            => "extra_inmueble_category",
                    "srcColumn" => "id_category",
                    "dstColumn" => "id",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            )
        );
        
        public function getCategory() {
            return $this->category->first();                        
        }
        
        public function getIdCategory() {
            return $this->category->first()->getId();                        
        }
        
                        
        public function getId() {
            return $this->id;
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }       
        
        public function getCreator() {
            $creator = user::find("username='{$creator}'");
            if ($creator->hasResult()) {
                return $creator->first()->getDisplayName();                
            }            
            
        }
       
        
        public function getDataType() {
            $type = utf8_encode(_("Sí/No"));
            return $type;            
        }
        
        public function getStatus() {
            if ($this->isEnabled()) {
                return utf8_encode(_("Activo"));            
            }
            return utf8_encode(_("Inactivo"));
        }
        
        public function getSearchable() {
            if ($this->isSearchable()) {
                return utf8_encode(_("Sí"));            
            }
            return utf8_encode(_("No"));
        }
        
        public function getLabel() {
            return form::labelTag("extra_{$this->id}",$this->getName());            
        }
        
        public function getCheckbox($checked=false) {
            if ($checked) {
                return form::checkboxInput("extra_{$this->id}","","",true);
            }
            else {
                return form::checkboxInput("extra_{$this->id}","","",false);
            }            
        }
        
        public function getTooltip() {
            // TODO: Aquí hace falta JS para Tooltip
            $code = "<p>".$this->getDescription()."</p>";
            return $code;
        }
        
        
        public function getName() {
            return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");
        }        
        
        public function getDescription() {
            return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");
        }
        
        public function isEnabled() {
            if (intval($this->status)===1) {
                return true;
            }
            return false;
        }
        
        public function isSearchable() {
            if (intval($this->is_searchable)===1) {
                return true;
            }
            return false;
        }
                
    };
?>