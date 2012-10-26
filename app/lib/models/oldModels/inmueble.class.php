<?php
    class inmueble extends FW_ActiveRecord_Model {

        protected $id;
        protected $is_new;
        protected $type;
        protected $subtype;
        protected $username;
        protected $status;
        protected $created_at;
        protected $description;
        protected $superficie_construida;
        protected $superficie_util;
        protected $superficie_comunes;
        protected $antiguedad;
        protected $estado_conservacion;
        protected $numero;
        protected $via;
        protected $municipio;
        protected $codigo_postal;
        protected $provincia;
        protected $estado;
        protected $pais;
        protected $map_lat;
        protected $map_lng;
        protected $real_location;
        protected $id_zone;
        protected $name;

        private $_datos;
            
            

        public static $has_many = array(
                array(
                        "property" => "images",
                        "table" => "image_inmueble",
                        "srcColumn" => "id",
                        "dstColumn" => "id_inmueble",
                        "update" => "restrict",
                        "delete" => "restrict"
                ),
                array(
                        "property" => "documents",
                        "table" => "documento_inmueble",
                        "srcColumn" => "id",
                        "dstColumn" => "id_inmueble",
                        "update" => "restrict",
                        "delete" => "restrict"
                )               
        );

        public static $has_and_belongs_to_many = array( array(
                    "property" => "extras",
                    "srcTable" => "inmueble",
                    "srcColumn" => "id",
                    "dstTable" => "extra_inmueble",
                    "dstColumn" => "id",
                    "throughTable" => "inmueble_has_extras",
                    "throughTableSrcColumn" => "id_inmueble",
                    "throughTableDstColumn" => "id_extra",
                    "update" => "restrict",
                    "delete" => "restrict"
            ));
            
            
        public function __call($method,array $arguments=array()) {
            $this->afterCreate();
            if ($this->_datos===null) {
                throw new FW_Exception("Strange thing... data of an inmueble cannot be null");
            }
            if (method_exists($this->_datos,$method)) {                
                return call_user_func_array(array($this->_datos,$method),$arguments);                
            }   
            else {
                throw new FW_Exception("Method {$name} doesn't exists on inmueble data");
            }         
        }
        
        public function getId() {
            return intval($this->id);
        }            
            
            
            
        public function getDescription() {
            return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");            
        }
                    
        /*public function getType() {
            var_dump(gettype($this->getDatos()));            
        }*/
         
         public function getAnuncios() {
             $results      = array();
             $database = FW_Database::getInstance();
             $prefix       = $database->getPrefix();
             $query       = "SELECT id FROM {$prefix}anuncio WHERE id_inmueble='{$this->id}' AND status='1' ";
             $database->query($query);
             if ($database->numRows()>0) {
                 while ($row=$database->fetchRow()) {
                     $results []= anuncio::find("id='{$row[0]}' ");                                          
                 }
             }
             return $results;             
         }            
            
         public function getNombre() {
             return utf8_encode($this->name);
         }            
            
         public function getLatLng() {
             return array("lat"=>$this->map_lat,"lng"=>$this->map_lng);
         }
         
         public function getLocation() {
            return array("lat"=>$this->map_lat,"lng"=>$this->map_lng);
        }

        public function getDireccion() {
            $direccion = "{$this->via} {$this->numero} , {$this->municipio}, {$this->codigo_postal} ,{$this->provincia} ,{$this->estado} ,{$this->pais}";
            return html_entity_decode($direccion,ENT_QUOTES,"UTF-8");
        }

        public function afterCreate() {            
            $this->_datos = $this->getDatos();
            return true;
        }

        public function getDatos() {
            $datos              = null;
            $type                = intval($this->type);
            $subtype         = intval($this->subtype);
            $condiciones = "id_inmueble='{$this->id}'";
            
            
            if ($type===0) {
                if (in_array($subtype,array(0,2,3,4,5,7,8,10))) {
                    $datos = piso::find($condiciones);
                }                
                if (in_array($subtype,array(1,6))) {
                    $datos = casa::find($condiciones);
                }                
            }
            
            if ($type===1) {
                $datos = oficina::find($condiciones);
            }
            if ($type===2) {
                $datos = nave::find($condiciones);
            }
            if ($type===3) {
                $datos = local::find($condiciones);
            }
            if ($type===4) {
                $datos = garaje::find($condiciones);
            }
            if ($type===5) {
                $datos = trastero::find($condiciones);
            }
            if ($type===6) {
                $datos = edificio::find($condiciones);
            }
            if ($type===7) {
                $datos = hotel::find($condiciones);
            }
            if ($type===8) {
                $datos = terreno::find($condiciones);                
            }
            if ($type===9) {
                $datos = casa_madera::find($condiciones);                
            }
            
            if ( $datos->hasResult() ) {
                $this->_datos = $datos->first();
            }
            return $this->_datos;
        }

        public function hasImages() {
            return (count($this->images)>0);
        }
        public function getImages() {
            $order = array (
                array (
                    "column" => "image_order",
                    "type"       => "ASC"                          
                )            
            );
            return $this->images->find("",$order);
        }

        public function getDocuments() {
            return $this->documents;
        }
        
        public function isExactLocation() {
            return (intval($this->real_location)===1);
        }
        
        public function hasExtra($id) {
            $database = FW_Database::getInstance();
            $prefix       = $database->getPrefix();
            $query       = "SELECT * FROM {$prefix}inmueble_has_extras WHERE id_extra='{$id}' AND id_inmueble='{$this->id}' ";
            $database->query($query);
            if ($database->numRows()) {
                return true;
            }
            return false;            
        }
        
        public function getExtraDetails() {
            var_dump($this->extras);
        }
        
        public function getExtraDetailsCategories() {
            $ids          = array();
            $results = array();
            $extras  = $this->extras;
            
            if (count($extras)) {
                foreach ($extras as $extra) {
                    $id = $extra->getIdCategory();
                    if (!in_array($id,$ids)) {
                        $ids          []= $id;
                        $results []= $extra->getCategory();                         
                    }                    
                }                
            }
            return $results;
        }
        
        public function getSuperficieConstruida() {
            return floatval($this->superficie_construida);
        }
        
        public function getSuperficieUtil() {
            return floatval($this->superficie_util);
        }
        
        public function getSuperficieElementosComunes() {
            return floatval($this->superficie_comunes);
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
        
        
        public function getType() {
            return intval($this->type);
        }
        
        public function getSubtype() {
            return intval($this->subtype);
        }
        
        public function getEstadoConservacion() {
            return intval($this->estado_conservacion);
        }
        
        public function getAntiguedad() {
            return intval($this->antiguedad);
        }
        

    };
?>