<?php
    class anuncio_texto extends FW_ActiveRecord_Model {
            
        protected $id;        
        protected $title;        
        protected $slug;
        protected $short_description;
        protected $description;
        protected $type;
        
        protected $numero;
        protected $via;
        protected $municipio;
        protected $codigo_postal;
        protected $provincia;
        protected $estado;
        protected $pais;
        
        protected $status;
        protected $date_begin;
        protected $date_end;                        
        protected $map_lat;
        protected $map_lng;
        
        protected $show_telephone;
        protected $radius;
        protected $id_zone;
        protected $username;
        
        
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
        
        public static $has_one = array (            
            array(
                "property"    => "zone",
                "table"           => "zone",
                "srcColumn"=> "id_zone",
                "dstColumn"=> "id",
                "update"       => "restrict",
                "delete"        => "restrict"
            )
        );
        
        public function getId() {
            return $this->id;
        }
        
        public function getStatus() {
            if (intval($this->status)===1) {
                return _("Activo");            
            }
            return _("Inactivo");
        } 
        
        public function setStatus($status) {
            $this->status = $status;
            return $this->save();
        }
        
        
        public function getStatusAsJSON() {
            $result = "";
            $status = intval($this->status);
            if ($status===1) {
                $result = _("Activo");             
            }
            if ($status===0) {
                $result = _("Inactivo");
            }
            if ($status===2) {
                $result = _("Bloqueado por el administrador");
            }
            return htmlentities($result,ENT_QUOTES,"UTF-8");
        }
        
        public function getStatusAsInteger() {
            return (intval($this->status));
        }
        
        public function getAddress() {          
            $direccion = "{$this->via} {$this->numero}, {$this->municipio}, {$this->codigo_postal} ,{$this->provincia} ,{$this->estado} ,{$this->pais}";            
            return html_entity_decode($direccion,ENT_QUOTES,"UTF-8");
        }
        
        public function getInfoWindowText() {
            //$text = "<h5>{$this->title}</h5><hr/><p>{$this->getDescription()}</p><p>"._("En un radio de ").$this->radius.(" metros de ").$this->getAddress();
            //$text = html_entity_decode($text,ENT_QUOTES,"UTF-8");
            $text = "";
            return $text;
        }
        
        public function getRadius() {
            return $this->radius;
        }
        
        public function getSlug() {
            return $this->slug;
        }        
        
        public function getTitle() {
            return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");
        }
        
        public function getTitleAsJSON() {
            return htmlentities($this->getTitle(),ENT_QUOTES,"UTF-8");
        }
        
        public function getDescription() {
            return trim(html_entity_decode($this->description,ENT_COMPAT,"UTF-8"),' ');
        }       
        
        public function getShortDescription() {
            return trim(html_entity_decode($this->short_description,ENT_COMPAT,"UTF-8"),' ');
        }
        
        public function getTypeAsInteger() {
            return (intval($this->type));
        }
        
        public function getType() {
            $result = "";
            $type   = intval($this->type);
            
            if ($type===0) {
                $result = _("Piso");                                
            }
            
            if ($type===1) {
                $result = _("Casa/Chalet");                                
            }
            
            if ($type===2) {
                $result = _("Oficina");
            }
            
            if ($type===3) {
                $result = _("Local");
            }
            
            if ($type===4) {
                $result = _("Nave");
            }
            
            if ($type===5) {
                $result = _("Garaje");
            }
            
            if ($type===6) {
                $result = _("Trastero");
            }
            
            if ($type===7) {
                $result = _("Edificio");
            }
            
            if ($type===8) {
                $result = _("Hotel");
            }
            
            if ($type===9) {
                $result = _("Terreno");
            }
            
            if ($type===10) {
                $result = _("Casa de madera");
            }
            
            return htmlentities($result,ENT_QUOTES,"UTF-8");
        }
        
        public function canShowTelephone() {
            if (intval($this->show_telephone)===1) {
                return true;
            }
            return false;
        }
        
        public function getUserName() {
            if ($this->user!==null) {
                return $this->user->first()->getDisplayName();
            }
        }
        
        public function getAuthor() {
            if ($this->username!==null) {
                return $this->username->first()->getDisplayName();
            }
        }
        
        public function getTelephone() {
            $phone = false;
            if ($this->username!==null) {
                $user      = $this->username->first();
                $phone = $user->telephone;
            }
            return $phone;
        }
        
        public function lat() {
            return doubleval($this->map_lat);
        }
        
        public function lng() {
            return doubleval($this->map_lng);
        }
        
        public function getLatLng() {
            LoadPlugin("maps");
            return new latLng($this->map_lat,$this->map_lng);
        }
        
        public function getLocation() {
            return array("lat"=>$this->map_lat,"lng"=>$this->map_lng);
        }
        
        public function getDate() {
            return date("d/m/Y H:i",strtotime($this->date_begin));
        }
        
        
        /* STATS */
    public function getTotalHits() {        
        $hits = anuncio_texto_hit::count("id","id_anuncio='{$this->id}' ");        
        if ($hits!==null) {
            return $hits;
        }
    }
    
    
    public function getAvgHitsByDay() {        
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT COUNT(id) AS qty, DATE(timestamp) AS date FROM {$prefix}anuncio_texto_hit WHERE id_anuncio='{$this->id}'  GROUP BY DATE(timestamp)";                        
        $database->query($query);        
        if ($database->numRows()>0) {
            $total = array();
            while($row=$database->fetchAssoc()) {
                $total []= $row["qty"];                
            }
            $sum = array_sum($total);
            return ($sum/count($total));            
        }       
        return 0;
    }
   
    
    public function getHitsByBrowser() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(browser), browserversion, COUNT(id) AS qty FROM {$prefix}anuncio_texto_hit WHERE id_anuncio='{$this->id}' GROUP BY browser,browserversion ORDER BY qty ASC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                $hits [] = array("browser"=>"{$row["browser"]}{$row["browserversion"]}","qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    public function getHitsByOs() {
        $hits           = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(os),osversion, COUNT(id) AS qty FROM {$prefix}anuncio_texto_hit WHERE id_anuncio='{$this->id}' GROUP BY os,osversion ORDER BY qty ASC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                $hits [] = array("os"=>"{$row["os"]}{$row["osversion"]}","qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    public function getHitsByReferrer() {
        $hits              = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();
        
        $query = "SELECT DISTINCT(referrer), COUNT(id) AS qty FROM {$prefix}anuncio_texto_hit WHERE id_anuncio='{$this->id}' GROUP BY referrer ORDER BY qty ASC";        
        $database->query($query);
        if ($database->numRows()>0) {
            while ($row=$database->fetchAssoc()) {
                if ($row["referrer"]===null || $row["referrer"]==="") {
                    $row["referrer"] = _("Hits directos");                     
                }
                else {
                    $row["referrer"] = "<a href=\"{$row["referrer"]}\" title=\"{$row["referrer"]}\">{$row["referrer"]}</a>";                    
                }
                $hits [] = array("referrer"=>$row["referrer"],"qty"=>$row["qty"]);
            }
        }        
        return $hits;
    }
    
    
    public function getTodayHits() {                                
        $hits = anuncio_texto_hit::count("id","id_anuncio='{$this->id}'  AND DATE(timestamp)=DATE(NOW()) ");        
        if ($hits!==null) {
            return $hits[0];
        } 
    }
    
    public function getYesterdayHits() {        
        $hits = anuncio_texto_hit::count("id","id_anuncio='{$this->id}'  AND DATE(timestamp)=DATE(NOW()-INTERVAL 1 DAY)  ");        
        if ($hits!==null) {
            return $hits[0];
        } 
    }
    
    public function getHitsByPlace() {
        $hits             = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();
        
        $query         = "SELECT COUNT(id) AS qty,CONCAT(country,' , ',region,' , ',city) AS place FROM {$prefix}anuncio_texto_hit WHERE id_anuncio='{$this->id}' GROUP BY city,region,country HAVING country IS NOT NULL";
        $database->query($query);
        while ($hit=$database->fetchAssoc()) {
            $hits []= array("place"=>$hit["place"],"qty"=>$hit["qty"]);            
        }
        return $hits;                
    }
    
    public function registerHit() {
        return anuncio_texto_hit::registerHit($this->id);
    }
        
    
    /* END STATS */        
         
    };
?>
