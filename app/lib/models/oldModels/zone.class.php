<?php
    class zone extends FW_ActiveRecord_Tree {
        protected $id;
        protected $id_parent;
        protected $name;
        protected $type;
        protected $description;        
        protected $center_lat;
        protected $center_lng;
        protected $creator;
        protected $created_at;
        protected $status;
        protected $slug;
        protected $path;
        protected $subzone_ids;
        
        private $_onMouseDown;
        private $_onMouseOut;
        private $_onMouseOver;
        private $_onMouseMove;
        private $_onMouseUp;
        private $_onClick;
        
        public static $acts_as_tree = array(
            "idColumn"            => "id", 
            "parentColumn"   => "id_parent", 
            "siblingOrder"      => array(array("column"=>"name","type"=>"ASC"))
        );
        
        
              
        
        
        public function beforeSave() {            
            return true;            
        }
        
        public function getId() {
            return $this->id;
        }
        
        public function getSlug() {
            return $this->slug;
        }
        
        public function getType() {
            $type = "";
            switch (intval($this->type)) {
                
                case 1: 
                    $type = _("País");
                break;
                
                case 2: 
                    $type = _("Comunidad/Estado");
                break;
                
                case 3: 
                    $type = _("Provincia");
                break;
                
                case 4: 
                    $type = _("Comarca");
                break;
                
                case 5: 
                    $type = _("Municipio");
                break;
                
                case 6: 
                    $type = _("Distrito/Barrio");
                break;
                
                case 7: 
                    $type = _("Otras divisiones zonales");
                break;
                
                case 8: 
                    $type = _("Otras divisiones zonales");
                break;
                    
            };
            return $type;
        }
        
        public function getParentZone() {
            $result = array();            
            if ($this->getParent()!==null) {
                $zone   =  $this->getParent();
                $result = array("message"=>_("Zona padre"),"zone"=>array("id"=>$zone->id,"name"=>$zone->getName())); 
            }            
            return $result;
        }
        
        public function getChildZonesJSON() {
            $json = "";
            if ($this->hasChildren()) {
                $zones = $this->getChildren();
                if ($zones!==null) {
                    $transformations = array (
                        "name"  => array("getName"),
                        "slug"     => array("getSlug"),                                       
                    );
                    $json = $zones->toJSON(array("id","slug","name"),$transformations,array(),true,false);
                    //$json = html_entity_decode($json,ENT_COMPAT,"UTF-8");
                    $json = json_decode($json);
                } 
            }
            return $json;
        }
        
        public function getChildZonesArray() {
            $result = array();
            if ($this->hasChildren()) {
                $zones = $this->getChildren();
                if ($zones!==null) {
                    $transformations = array (
                        "name"  => array("getName"),
                        "slug"     => array("getSlug"),                                       
                    );
                    $result = $zones->toArray(array("id","slug","name"),$transformations,array(),true,false);                    
                } 
            }
            return $result;
        }
        
        public function getChildZones() {
            $results      = array();
            $subzones = $this->subzone_ids;            
            if ($subzones==="") {
                $subzones = $this->getSubZonesIds();
                $subzones = '{'.implode(',',$subzones).'}';                
                $this->subzone_ids = $subzones;
                $this->save();
            }                          
            $subzones = substr($subzones,1,-1);            
            if (strlen($subzones)) {
                $subzones = explode(',',$subzones);
                                
                $type = intval($this->type);
                if ($type<5) {
                    $type = ($type+1);
                    $type = "'{$type}'";
                }
                else {
                    $type = " '6','7','8','9' ";
                }
                foreach ($subzones as $subzone) {                    
                    $conditions = " id='{$subzone}'  AND status='1' AND type IN ({$type}) ";                    
                    $aux              = zone::find($conditions);
                    if ($aux->hasResult()) {
                        $results []= $aux->first();
                    }                                        
                }
            }
            return $results;
        }        

        public function getChildZonesIds() {
            $results      = array();
            $subzones = $this->subzone_ids;            
            if ($subzones==="") {
                $subzones = $this->getSubZonesIds();
                $subzones = '{'.implode(',',$subzones).'}';                
                $this->subzone_ids = $subzones;
                $this->save();
            }                          
            $subzones = substr($subzones,1,-1);            
            if (strlen($subzones)) {
                $results = explode(',',$subzones);                
            }
            return $results;
        }        
        
        public function getSubZonesIds() {
            $ids = array();            
            if ($this->hasChildren()) {
                $children = $this->getChildren();
                foreach ($children as $subZone) {
                    $ids []= intval($subZone->getId());
                    $ids    = array_merge($ids,$subZone->getSubZonesIds());
                }
            }
            sort($ids);
            return $ids;                        
        }
        
        
        
        public function hasPoints() {
            return (strlen($this->path)>0);
        }
        
        public function getPoints() {            
            if (strpos($this->path,'@')!==false) {
                $data = explode('@',$this->path);
                if (count($data)>0) {
                    for($i=0;$i<count($data);$i++) {
                        $data[$i] = substr($data[$i],1,-1);
                    }
                }
            }
            else {
                $data = array(substr($this->path,1,-1));
            }
            return $data;
        }

        public function getJSONName() {
            $name = $this->getName();
            return htmlentities($name,ENT_QUOTES,"UTF-8");            
        }        
        
        public function getName() {
            return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");
        }
        
        public function getDescription() {
            return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }
        
        public function getUser() {
            $creator = user::find("username='{$this->creator}'");
            if ($creator->hasResult()) {
                return $creator->first()->getDisplayName();                
            }
            
        }
        
        public function getStatus() {            
            if ($this->status===0) {
                return _("Inactiva");
            }
            return _("Activa");
        }
        
        public function getMapDiv() {
            $name = "map_".str_replace('-','_',FW_Util_Url::seoUrl($this->getName()));
            return $name;             
        }
        
        public function getMapJS($name="zoneMap") {
            $map = $this->getMap($name);
            //$map = htmlentities($map);            
            return $map;
        }
        
        public function getPolygons() {
            LoadPlugin("maps");
            $polygons = array();
            if (strlen($this->path)!==0) {
                $points = $this->getPoints();
                if (count($points)) {
                    $i = 0;                    
                    foreach ($points as $shape) {
                        $name      = html_entity_decode($this->getName(),ENT_QUOTES,"UTF-8");
                        $name      = "polygon_".str_replace('-','_',FW_Util_Url::seoUrl($name))."_{$i}";                                                
                        $polygon = new polygon($name);
                        $shape     = explode('|',$shape);
                        foreach ($shape as $point) {
                            $point = explode(',',$point);                            
                            $polygon->addPoint(array($point[0],$point[1]));
                        }
                        $polygons []= $polygon;
                        $i++;
                    }                    
                }
            }
            return $polygons;
        }
        
        public function getMap($name="map") {
            $code  = "";
            /* HACK For toJSON */
            if (is_numeric($name)) {
                $name = "map";
            }
            LoadPlugin("maps");            
            $zoom = 15;
            switch (intval($this->type)) {
                case 1: $zoom=3; break;
                case 2: $zoom=7; break;
                case 3: $zoom=8; break;
                case 4: $zoom=9; break;
                case 5: $zoom=13; break;
                case 6: $zoom=17; break;                                
            };
            
            $map  = new map($name,$zoom,"road");
            $map->setCenter($this->center_lat,$this->center_lng);
            $map->addMarker(new latLng($this->center_lat,$this->center_lng),"marker",$this->getJSONName(),$this->getJSONName());            
            $polygons = $this->getPolygons();
            if (count($polygons)>0) {
                foreach ($polygons as $polygon) {
                    $map->addPolygon($polygon);
                }
            }
            $childZones = $this->getChildren();            
            foreach ($childZones as $aux) {                
               $polygons = $aux->getPolygons();
               if (count($polygons)>0) {
                   foreach ($polygons as $polygon) {
                       $polygon->setExtraData(array("id"=>$aux->getId(),"pname"=>$aux->getJSONName()));
                       
                       
                       if ($this->_onClick!==null) {
                           $polygon->setOnClick($this->_onClick);
                       }
                       
                       if ($this->_onMouseDown!==null) {
                           $polygon->setMouseDown($this->_onMouseDown);                           
                       }
                       
                       if ($this->_onMouseMove!==null) {
                           $polygon->setMouseMove($this->_onMouseMove);
                       }
                       
                       if ($this->_onMouseOut!==null) {
                           $polygon->setMouseOut($this->_onMouseOut);
                       }
                       
                       if ($this->_onMouseOver!==null) {
                           $polygon->setMouseOver($this->_onMouseOver);
                       }
                       
                       if ($this->_onMouseUp!==null) {
                           $polygon->setMouseUp($this->_onMouseUp);
                       }
                                                                
                       $map->addPolygon($polygon);                       
                   }                   
               }           
           }         
           $code = '<script type="text/javascript">$(document).ready(function() {'.$map->display("zoneMapDiv").'});</script>';                                   
           return $code;
        }

        public function isSubzone($id) {
            $subzones = $this->subzone_ids;
            if (strlen($subzones)>2) {
                $subzones = substr($subzones,1,-1);
                $subzones = explode(',',$subzones);
                if (in_array($id,$subzones)) {
                    return true;
                }                
            }
            return false;                        
        }

        public function getPromotores() {
            // TODO: REVISAR
            $results     = array();
            $database = $this->database();
            $prefix       = $database->getPrefix();
            $query        = "SELECT id_promotor FROM {$prefix}promotor_in_zone WHERE id_zone='{$this->id}' ";
            $database->query($query);
            while ($row=$database->fetchAssoc()) {
                $id                   = $row["id"];
                $profesional = promotor::find(" id='{$id}' AND status='1' " );
                if ($profesional->hasResult()) {
                    $results []= $profesional;
                }
            }
            return $results;
        }
        
        public function getInmobiliarias() {
            // TODO: REVISAR
            $results     = array();
            $database = $this->database();
            $prefix       = $database->getPrefix();
            $query        = "SELECT id_inmobiliaria FROM {$prefix}inmobiliaria_in_zone WHERE id_zone='{$this->id}' ";
            $database->query($query);
            while ($row=$database->fetchAssoc()) {
                $id                   = $row["id"];
                $profesional = inmobiliaria::find(" id='{$id}' AND status='1' " );
                if ($profesional->hasResult()) {
                    $results []= $profesional;
                }
            }
            return $results;            
        }
        
        public function getBancos() {
            // TODO: REVISAR
            $results     = array();
            $database = $this->database();
            $prefix       = $database->getPrefix();
            $query        = "SELECT id_inmobiliaria FROM {$prefix}banco_in_zone WHERE id_zone='{$this->id}' ";
            $database->query($query);
            while ($row=$database->fetchAssoc()) {
                $id                   = $row["id"];
                $profesional = banco::find(" id='{$id}' AND status='1' " );
                if ($profesional->hasResult()) {
                    $results []= $profesional;
                }
            }
            return $results;
        }
        
        public function getCenter() {
            return array("lat"=>$this->center_lat,"lng"=>$this->center_lng);
        }
        
        
        /* Eventos JavaScript */
        public function setOnClick($code) {
            $this->_onClick = $code;
        }
        
        public function setMouseMove($code) {
            $this->_onMouseMove = $code;
        }
        
        public function setMouseDown($code) {
            $this->_onMouseDown = $code;
        }

        public function setMouseOut($code) {
            $this->_onMouseOut = $code;
        }

        public function setMouseOver($code) {
            $this->_onMouseOver = $code;
        }

        public function setMouseUp($code) {
            $this->_onMouseUp = $code;
        }
        
        
        
        // Estadísticas de anuncios/zona
        public function getAnuncios() {
            $result       = array();
            
            $subzones = $this->getChildZonesIds();
            if (count($subzones)>0) {
                $in              = implode(',',$subzones);
                $anuncios = anuncio::find(" id_zone='{$this->id}' OR id_zone IN({$in}) ");                
            }
            else {
                $anuncios = anuncio::find(" id_zone='{$this->id}' ");
            }
            
            if ($anuncios->hasResult()) {
                $transformations = array(
                    "title" => array("getTitleAsJSON"),
                    "status" => array("getStatusAsJSON"),
                    "date_begin" => array("getDate"),
                    "inmueble_type" => array("getInmuebleTypeAsJSON"),
                    "type" => array("getTypeAsJSON"),
                    "username" => array("getUserAsText")
                );
                $result = $anuncios->toArray(array("id","title","type","inmueble_type","status","date_begin","username"), $transformations, array(), false, false);
            }
            return $result;    
        }
        public function getCountAnuncios() {        
            $result       = 0;   
            $id               = $this->id;
            $subzones = $this->getChildZonesIds();
            
            $database  = FW_Database::getInstance();
            $prefix        = $database->getPrefix();
            if (count($subzones)>0) {
                $in                = implode(',',$subzones);
                $query         = "SELECT COUNT(DISTINCT(id)) FROM {$prefix}anuncio WHERE id_zone IN($in) OR id_zone='{$this->id}' AND status='1'  ";
            }
            else {
                $query         = "SELECT COUNT(DISTINCT(id)) FROM {$prefix}anuncio WHERE id_zone='{$this->id}' AND status='1'  ";
            }
            $database->query($query);
            $result         = $database->fetchRow();
            $result         = intval($result[0]);
            return $result;
        }
        
        public function getCountAnunciosTexto() {
            $result       = 0;
            $id               = $this->id;
            $subzones = $this->getChildZonesIds();
            
            $database  = FW_Database::getInstance();
            $prefix        = $database->getPrefix();
            
            if (count($subzones)>0) {
                $in                = implode(',',$subzones);
                $query         = "SELECT COUNT(id) FROM {$prefix}anuncio_texto WHERE id_zone IN($in) OR id_zone='{$this->id}' AND status='1' ";
            }
            else {
                $query         = "SELECT COUNT(id) FROM {$prefix}anuncio_texto WHERE id_zone='{$this->id}' AND status='1' ";
            }
            $database->query($query);
            $result         = $database->fetchRow();
            $result         = intval($result[0]);
            return $result;            
        }
        
        public function getCountAnunciosByType() {
            $result       = array();   
            $id               = $this->id;
            $subzones = $this->getChildZonesIds();
            
            $options = array (
                0 => array("type"=> _("Venta"),"qty"=>0),
                1 => array("type"=>_("Alquiler"),"qty"=>0),
                2 => array("type"=>_("Alquiler con opción a compra"),"qty"=>0),
                3 => array("type"=>_("Alquiler vacacional"),"qty"=>0),
                4 => array("type"=>_("Alquiler compartido"),"qty"=>0),
                5 => array("type"=>_("Permuta"),"qty"=>0),
                6 => array("type"=>_("Traspaso"),"qty"=>0),                
            );
            
            
            $database  = FW_Database::getInstance();
            $prefix        = $database->getPrefix();
            
            if (count($subzones)>0) {
                $in                = implode(',',$subzones);
                $query         = "SELECT type,COUNT(DISTINCT(id)) AS qty FROM {$prefix}anuncio WHERE id_zone IN($in) OR id_zone='{$this->id}' AND status='1'  GROUP BY(type)";
            }
            else {
              $query         = "SELECT type,COUNT(DISTINCT(id)) AS qty FROM {$prefix}anuncio WHERE id_zone='{$this->id}' AND status='1'  GROUP BY(type)";  
            }
            $database->query($query);
            while ($row=$database->fetchAssoc()) {
                $options[$row["type"]]["qty"]  = intval($row["qty"]);                                
            }            
            return $options;            
        }
        
        public function getCountAnunciosByInmuebleType() {
            $result       = array();   
            $id               = $this->id;
            
            
            $options = array(
                0 => array(
                    0 => array("qty"=>0,"label"=>_("Piso")),
                    1 => array("qty"=>0,"label"=>_("Adosado")),
                    2 => array("qty"=>0,"label"=>_("Apartamento")),
                    3 => array("qty"=>0,"label"=>_("Ático")),
                    4 => array("qty"=>0,"label"=>_("Casa")),
                    5 => array("qty"=>0,"label"=>_("Dúplex")),
                    6 => array("qty"=>0,"label"=>_("Estudio")),
                    7 => array("qty"=>0,"label"=>_("Loft")),
                    8 => array("qty"=>0,"label"=>_("Planta baja")),
                    9 => array("qty"=>0,"label"=>_("Habitación")),
                    10 => array("qty"=>0)
                ),                
                1 => array(
                    -1 => array("qty"=>0,"label"=>_("Oficina")),
                ),
                2 => array(
                    -1 => array("qty"=>0,"label"=>_("Nave")),
                ),
                3 => array(
                    -1 => array("qty"=>0,"label"=>_("Local")),
                ),
                4 => array(
                    -1 => array("qty"=>0,"label"=>_("Garaje")),
                ),
                5 => array(
                    -1 => array("qty"=>0,"label"=>_("Trastero")),
                ),
                6 => array(
                    -1 => array("qty"=>0,"label"=>_("Edificio")),
                ),
                7 => array(
                    -1 => array("qty"=>0,"label"=>_("Hotel")),
                ),
                8 => array(
                    -1 => array("qty"=>0,"label"=>_("Terreno")),
                ),
                9 => array(
                    -1 => array("qty"=>0,"label"=>_("Casa de madera")),
                )                            
            );
            
            $subzones = $this->getChildZonesIds();
            
            $database  = FW_Database::getInstance();
            $prefix        = $database->getPrefix();
            
            if (count($subzones)>0) {
                $in                = implode(',',$subzones);
                $query         = "SELECT inmueble_type,inmueble_subtype,COUNT(DISTINCT(id)) AS qty FROM {$prefix}anuncio WHERE id_zone IN($in) OR id_zone='{$this->id}' AND status='1'  GROUP BY inmueble_type,inmueble_subtype";
            }
            else {
                $query         = "SELECT inmueble_type,inmueble_subtype,COUNT(DISTINCT(id)) AS qty FROM {$prefix}anuncio WHERE  id_zone='{$this->id}' AND status='1'  GROUP BY inmueble_type,inmueble_subtype";
            }            
            
            $database->query($query);
            while ($row=$database->fetchAssoc()) {
                $options[$row["inmueble_type"]][$row["inmueble_subtype"]]["qty"] = intval($row["qty"]);
            }            
            return $options;              
        }   

        public function getCountAnunciosByTypeAndInmuebleType() {
            $result       = array();   
            $id               = $this->id;
            
            
            $options = array(
                0 => array(
                    0 => array(
                        "label"=>_("Piso"),
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                    ),
                    1 => array(
                        "qty"=>0,"label"=>_("Adosado"),
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                    ),
                    2 => array(
                    "qty"=>0,"label"=>_("Apartamento"),
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                    ),
                    3 => array(
                        "qty"=>0,"label"=>_("Ático"),
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso"))                    
                    ),
                    4 => array("qty"=>0,"label"=>_("Casa"),
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                    ),
                    5 => array("qty"=>0,"label"=>_("Dúplex"),
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                    ),
                    6 => array("qty"=>0,"label"=>_("Estudio"),
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                    ),
                    7 => array("qty"=>0,"label"=>_("Loft"),0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),),
                    8 => array("qty"=>0,"label"=>_("Planta baja"),0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),),
                    9 => array("qty"=>0,"label"=>_("Habitación"),0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),),                    
                ),                
                1 => array(
                    -1 => array("qty"=>0,"label"=>_("Oficina"),0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),),
                ),
                2 => array(
                    -1 => array("qty"=>0,"label"=>_("Nave"),0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),),
                ),
                3 => array(
                    -1 => array("qty"=>0,"label"=>_("Local"),0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),),
                ),
                4 => array(
                    -1 => array("qty"=>0,"label"=>_("Garaje"),0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),),
                ),
                5 => array(
                    -1 => array("qty"=>0,"label"=>_("Trastero"),0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),),
                ),
                6 => array(
                    -1 => array("qty"=>0,"label"=>_("Edificio"),0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),),
                ),
                7 => array(
                    -1 => array("qty"=>0,"label"=>_("Hotel"),0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),),
                ),
                8 => array(
                    -1 => array("qty"=>0,"label"=>_("Terreno"),0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),),
                ),
                9 => array(
                    -1 => array("qty"=>0,"label"=>_("Casa de madera"),0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),),
                )                            
            );
            
            $subzones = $this->getChildZonesIds();
            
            $database  = FW_Database::getInstance();
            $prefix        = $database->getPrefix();
            $in                = implode(',',$subzones);
            $query         = "SELECT type,inmueble_type,inmueble_subtype,COUNT(DISTINCT(id)) AS qty FROM {$prefix}anuncio WHERE id_zone IN($in) OR id_zone='{$this->id}' AND status='1'  GROUP BY inmueble_type,inmueble_subtype";            
            $database->query($query);
            while ($row=$database->fetchAssoc()) {                
                $options[$row["inmueble_type"]][$row["inmueble_subtype"]][$row["type"]]["qty"] = intval($row["qty"]);
            }            
            return $options;                          
        }
           
        public function getCountAnunciosTextoByType() {
            $results = array (
                0 => array("type"=> _("Piso"),"qty"=>0),
                1 => array("type"=>_("Casa/Chalet"),"qty"=>0),
                2 => array("type"=>_("Oficina"),"qty"=>0),
                3 => array("type"=>_("Local"),"qty"=>0),
                4 => array("type"=>_("Nave"),"qty"=>0),
                5 => array("type"=>_("Garaje"),"qty"=>0),
                6 => array("type"=>_("Trastero"),"qty"=>0),
                7 => array("type"=>_("Edificio"),"qty"=>0),
                8 => array("type"=>_("Hotel"),"qty"=>0),
                9 => array("type"=>_("Terreno"),"qty"=>0),
                10 => array("type"=>_("Casa de madera"),"qty"=>0)
            );
            $id               = $this->id;
            $subzones = $this->getChildZonesIds();
            
            $database  = FW_Database::getInstance();
            $prefix        = $database->getPrefix();
            
            if (count($subzones)>0) {
                $in                = implode(',',$subzones);
                $query         = "SELECT type,COUNT(DISTINCT(id)) AS qty FROM {$prefix}anuncio_texto WHERE id_zone IN($in) OR id_zone='{$id}' AND status='1' GROUP BY(type) ORDER BY type ASC";
            }
            else {
                $query         = "SELECT type,COUNT(DISTINCT(id)) AS qty FROM {$prefix}anuncio_texto WHERE id_zone='{$id}' AND status='1' GROUP BY(type) ORDER BY type ASC";
            }            
            $database->query($query);
            
            while ($row=$database->fetchAssoc()) {
                $type = $row["type"];
                $qty   = intval($row["qty"]);                
                $results[$type]["qty"]  = $qty;                                
            }
            return $results;
        }        
        
        public function getZoomLevel() {
            $zoom = 0;
            $type   = intval($this->type);
            
            //  Continente
            if ($type===0) {
                $zoom = 3;                                
            }
            
            // Pais
            if ($type===1) {
                $zoom = 6;                
            }
            
            // Estado/Comunidad Autónoma
            if ($type===2) {
                $zoom = 7;                
            }
            
            // Provincia
            if ($type===3) {
                $zoom = 8;                
            }
            
            // Comarca
            if ($type===4) {
                $zoom = 10;                
            }
            
            // Municipio
            if ($type===5) {
                $zoom = 13;                
            }
            
            // Otros
            if ($type>5) {
                $zoom = 15;                
            }
            
            return $zoom;
        }
        
        
        
    };
?>