<?php
    class anuncio extends FW_ActiveRecord_Model {

        protected $id;
        protected $id_inmueble;
        protected $type;
        protected $inmueble_type;
        protected $inmueble_subtype;
        protected $username;
        protected $date_begin;
        protected $date_end;
        protected $status;
        protected $short_description;
        protected $price;
        protected $rent_price;
        protected $conditions;
        protected $is_profesional;
        protected $is_new;
        protected $id_zone;
        protected $title;
        protected $slug;
        protected $has_lowered_price;
        protected $last_lowered_price_date;
        protected $has_been_highlighted;
        protected $last_highlighted_date;
        protected $tipo_vacacional;
        
                
        

        public static $has_one = array( 
            array(
                    "property"    => "inmueble",
                    "table"            => "inmueble",
                    "srcColumn" => "id_inmueble",
                    "dstColumn" => "id",
                    "update"         => "restrict",
                    "delete"          => "restrict"
            )
        );
        
        public static $has_many = array( 
            array(
                    "property"     => "historial_precios",
                    "table"            => "precio_inmueble",
                    "srcColumn" => "id",
                    "dstColumn" => "id_anuncio",
                    "update"        => "restrict",
                    "delete"         => "restrict"
            )
        );
        
        public function getStatus() {
            return intval($this->status);
        }
        
        public function setStatus($status) {
            $this->status = $status;
            return $this->save();
        }
        
        public function getId() {
            return $this->id;
        }
        
        public function getSlug() {
            return $this->slug;
        }
        
        public function getPrice() {
            $prices        = array();
            $operation = intval($this->type);
            switch ($operation) {
                case 0: 
                    $prices["price"] = $this->price;
                break;                    
                
                case 1: 
                    $prices["rent_price"] = $this->rent_price;
                break;
                
                case 2:
                    $prices["price"]           = $this->price; 
                    $prices["rent_price"] = $this->rent_price;
                break;
                
                case 3: 
                    $prices["price"] = $this->price;
                break;
                
                case 4: 
                    $prices["price"] = $this->price;
                break;
                
                case 5: 
                    $prices["price"]           = $this->price;
                break;
                
                case 6:
                    $prices["price"]           = $this->price; 
                    $prices["rent_price"] = $this->rent_price;
                break;                    
            }
            return $prices;
        }
        
        
        public function getConditions() {
            return html_entity_decode($this->conditions,ENT_QUOTES,"UTF-8");
        }
        
        public function getPriceAsFloat() {
            return floatval($this->price);
        }
        
        public function getRentPriceAsFloat() {
            return floatval($this->rent_price);
        }
        
        
        public function getType() {
            return intval($this->type);
        }
        
        public function getTypeAsText() {
            $type = intval($this->type);
            
            switch ($type) {
                case 0:
                    $type = _("Venta");
                    break;
                    
                case 1:
                    $type = _("Alquiler");
                    break;                                    
                    
                case 2:
                    $type = _("Alquiler con opción a compra");
                    break;
                    
                case 3:
                    $type = _("Alquiler vacacional");
                    break;                                        
                    
                    
                case 4:
                    $type = _("Alquiler compartido");
                    break;
                    
                case 5:
                    $type = _("Permuta");
                    break;
                    
                case 6:
                    $type = _("Traspaso");
                    break;                    
                                                            
            };
            return html_entity_decode($type,ENT_QUOTES,"UTF-8");
        }

        public function getTypeAsJSON() {
            $type = intval($this->type);
            
            switch ($type) {
                case 0:
                    $type = _("Venta");
                    break;
                    
                case 1:
                    $type = _("Alquiler");
                    break;                                    
                    
                case 2:
                    $type = _("Alquiler con opción a compra");
                    break;
                    
                case 3:
                    $type = _("Alquiler vacacional");
                    break;                                        
                    
                    
                case 4:
                    $type = _("Alquiler compartido");
                    break;
                    
                case 5:
                    $type = _("Permuta");
                    break;
                    
                case 6:
                    $type = _("Traspaso");
                    break;                    
                                                            
            };
            
            return htmlentities($type,ENT_QUOTES,"UTF-8");
        }

    public function getInmuebleTypeAsJSON() {
            $type        = intval($this->inmueble_type);
            $subtype = intval($this->inmueble_subtype);
            
            if ($type===0) {
                if ($subtype===0) {
                    $type = _("Piso");
                }
                if ($subtype===1) {
                    $type = _("Adosado");
                }
                if ($subtype===2) {
                    $type = _("Apartamento");
                }
                if ($subtype===3) {
                    $type = _("Ático");
                }
                if ($subtype===4) {
                    $type = _("Casa");
                }
                if ($subtype===5) {
                    $type = _("Estudio");
                }
                if ($subtype===6) {
                    $type = _("Loft");
                }
                if ($subtype===7) {
                    $type = _("Planta baja");
                }
                if ($subtype===8) {
                    $type = _("Habitación");
                }
            }
            
            if ($type===1) {
                $type = _("Oficina");                
            }
            if ($type===2) {
                $type = _("Nave");                
            }
            if ($type===3) {
                $type = _("Local");                
            }
            if ($type===4) {
                $type = _("Garaje");                
            }
            if ($type===5) {
                $type = _("Trastero");                
            }
            if ($type===6) {
                $type = _("Edificio");                
            }
            if ($type===7) {
                $type = _("Hotel");                
            }
            if ($type===8) {
                $type = _("Terreno");                
            }
            if ($type===9) {
                $type = _("Casa de Madera");                
            }
            
            return htmlentities($type,ENT_QUOTES,"UTF-8");
        }

       public function getInmuebleForm() {
           $type = strtolower(html_entity_decode($this->getInmuebleTypeAsJSON(),ENT_QUOTES,"UTF-8"));
           $type = FW_Util_Url::seoUrl($type);
           return $type;           
       }

       public function getShortDescription() {
           return html_entity_decode($this->short_description,ENT_QUOTES,"UTF-8");
       }
       
       public function getAddress() {
           return $this->getInmueble()->getDireccion();
       }
       
       public function getLocation() {
            return $this->getInmueble()->getLocation();
        }
        
        public function getPrecioMetroCuadrado() {
            $precio   = $this->price;
            $metros = $this->getInmueble()->superficie_construida;            
            return (($precio/$metros));                        
        }
        
        private function _getPriceHistorialArray($type) {
            $result          = array();
            $conditions = " id_anuncio='{$this->id}' AND type='{$type}' ";
            $historial     = precio_inmueble::find($conditions,array(array("column"=>"precio_order","type"=>"ASC")));
            if (count($historial)) {
                foreach ($historial as $precio) {                        
                    $key                      = $precio->precio_order;
                    $from                   = date("d/m/Y H:i:s",strtotime($precio->date_begin));
                    if ($precio->date_end===null) {
                        $precio->date_end = date("Y-m-d H:i:s");
                    }
                    $to                        = date("d/m/Y H:i:s",strtotime($precio->date_end));
                    $price                  = $precio->precio;
                    $meterPrice      = round(($price/$this->getInmueble()->superficie_construida),3);
                    $date                   = $precio->date_begin;
                    $result    [$key]= array("from"=>$from,"to"=>$to,"price"=>$price,"meter"=>$meterPrice,"date"=>$date);
                }                 
            }
            return $result;            
        }
        
        public function getHistorialPrecios() {                        
            $type       = intval($this->type);
            $precios = array("type"=>$type);
            
            // Venta
            if ($type===0) {
                $result                    = $this->_getPriceHistorialArray(0);                                
                $precios["price"] = $result;                                          
            }
            
            // Alquiler
            if ($type===1) {
                $result                    = $this->_getPriceHistorialArray(1);                                
                $precios["rent_price"] = $result;                                          
            }
            
            // Alquiler con opción a compra
            if ($type===2) {
                $result                    = $this->_getPriceHistorialArray(0);                                
                $precios["price"] = $result;                                          
                $result                    = $this->_getPriceHistorialArray(1);                                
                $precios["rent_price"]   = $result;
            }
            
            // Alquiler 
            if ($type===3) {
                $result                    = $this->_getPriceHistorialArray(0);                                
                $precios["price"] = $result;
            }
            
            // Alquiler 
            if ($type===4) {
                $result                    = $this->_getPriceHistorialArray(0);                                
                $precios["price"] = $result;
            }
            
            // Alquiler 
            if ($type===5) {
                $result                    = $this->_getPriceHistorialArray(0);                                
                $precios["price"] = $result;
            }
            
            // Alquiler 
            if ($type===6) {
                $result                    = $this->_getPriceHistorialArray(0);                                
                $precios["price"] = $result;
                $result                    = $this->_getPriceHistorialArray(1);                                
                $precios["rent_price"]   = $result;
            }
            
            if (isset($precios["price"])) {
                if (count($precios["price"])===0) {
                    $key                  = 0;
                    $from               = date("d/m/Y H:i:s",strtotime($this->date_begin)); 
                    $to                    = date("d/m/Y H:i:s",strtotime($this->date_end));
                    $price              = $this->price;
                    $meterPrice  = round(($price/$this->getInmueble()->superficie_construida),3);
                    $date               = $this->date_begin;
                    $precios["price"][$key]= array("from"=>$from,"to"=>$to,"price"=>$price,"meter"=>$meterPrice,"date"=>$date);
                    $date               =  date("Y-m-d H:i:s", strtotime("+1 second"));
                    $precios["price"][($key+1)]= array("from"=>$from,"to"=>$to,"price"=>$price,"meter"=>$meterPrice,"date"=>$date);                    
                }                
            }

            
            if (isset($precios["rent_price"])) {
                if (count($precios["rent_price"])===0) {
                    $key                  = 0;
                    $from               = date("d/m/Y H:i:s",strtotime($this->date_begin)); 
                    $to                    = date("d/m/Y H:i:s",strtotime($this->date_end));
                    $price              = $this->rent_price;
                    $meterPrice  = round(($price/$this->getInmueble()->superficie_construida),3);
                    $date               = $this->date_begin;
                    $precios["rent_price"][$key]= array("from"=>$from,"to"=>$to,"price"=>$price,"meter"=>$meterPrice,"date"=>$date);
                    $date               =  date("Y-m-d H:i:s", strtotime("+1 second"));
                    $precios["rent_price"][($key+1)]= array("from"=>$from,"to"=>$to,"price"=>$price,"meter"=>$meterPrice,"date"=>$date);
             
                }                
            }       

            
            return $precios;
        }

        public function getStatusAsJSON() {
            $status = intval($this->status);
            if ($status===0) {
                $status = _("Inactivo");                
            }
            if ($status===1) {
                $status = _("Activo");                                
            }
            if ($status===2) {
                $status = _("Bloqueado por el administrador");                
            }
            return htmlentities($status,ENT_QUOTES,"UTF-8");
        }

        public function getDate() {
            return date("Y-m-d H:i:s",strtotime($this->date_begin));
        } 

        public function getTitle() {
            return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");
        }
        
        public function getTitleAsJSON() {
            return htmlentities($this->getTitle(),ENT_QUOTES,"UTF-8");
        }
                
        public function getInmueble() {
            return $this->inmueble->first();
        }
        
        public function isExactLocation() {
            return $this->getInmueble()->isExactLocation();
        }
        
        public function isProfesional() {
            return (intval($this->is_profesional)===1);
        }
              
        public function getAnunciante() {
            $anunciante = false;
            $user                = $this->getUser();               
            if ($this->isProfesional()) {
                $anunciante = $user->getProfesional();
            }
            else {
                $anunciante  = $user;
            }                        
            return $anunciante;
        }
        
        public function getZone() {
            return zone::find("id='{$this->id_zone}' AND status='1' ")->first();
        }
        
        public function getTipoVacacional() {
            $type       = intval($this->tipo_vacacional);
            $options = array (                
                 0 => _("diarios"),
                 1 => _("semanales"),
                 2 => _("quincenales"),
                 3 => _("mensuales"),
                 4 => _("trimestrales"),
                 5 => _("semestrales"),
                 6 => _("por fin de semana")
            );
            return html_entity_decode($options[$type],ENT_QUOTES,"UTF-8");
        }
        
        public function getReference() {
            // completar de manera menos chapucera
            return $this->id;
        }
        
        public function isFavourite(){
            $username = FW_Authentication::getUser();
            if($username === null){
                return false;
            }
            
            $favoritos = anuncio_favorito::find("username='{$username->getUserName()}'");
            if ($favoritos->hasResult()) {
                return true;
            }
            return false;
        }
        
        public function getHTMLAnuncio(){
            $code = '';
            $urlBase          = FW_Config::getInstance()->get("core.global.baseURL");            
            $link = html::link_for_internal("anuncios", "anuncioFrontend", "displayAnuncio",array("slug"=>$this->slug,"id"=>$this->id));            
            $inmueble = $this->getInmueble();
            switch($this->getSize()){
                case 0:
                    $divClass="smallResult";
                    break;
                case 1:
                    $divClass="mediumResult";
                    break;
                case 2:
                    $divClass="bigResult";
                    break;
            }
            
            $base  = FW_Config::getInstance()->get("core.global.baseURL");
            
            $code .= "<div class=\"{$divClass}\">";
            $code .= "<div class = \"resultTitle\"><p><a href=\"{$link}\" class=\"resultTitleText\">{$this->getTitle()}</a>";
            $code .= "<img class = \"sendToFriendIcon\" id = \"{$this->id}\" src=\"{$base}/images/icons/vendoyo/Icono_Favoritos.png\" alt=\"\" height=\"25\" width=\"25\">";
            $code .= "<img class = \"favoritesIcon\" id = \"{$this->id}\" src=\"{$base}/images/icons/vendoyo/Icono_SobreAzul.png\" alt=\"\" height=\"23\" width=\"23\">";
            $code .= "</p></div>";
            $code .= "<div class=\"resultContent span-20\">";
            
            // El anuncio tiene imágenes, mostrar primera imagen            
            if ($this->getInmueble()->getImages()->count()>0) {
                $code .= "<div class=\"span-4 column\">";
                $code .= "<img src=\"{$this->getInmueble()->getImages()->first()->getFilename()}\" style=\"width: 175px; \"/>";       
                $code .= "</div><div class=\"span-16 column last\">";                                                         
            }
            else {
                $code .= "<div class=\"span-16 column prepend-2 last\">";
            }
                        
            $code .= "<p>{$this->getShortDescription()}</p>";
            
            $code .= "<div class=\"resultPrice\">{$this->getAnuncioPrice()} &euro; ({$this->getPrecioMedio()} &euro; /m2)</div>";
            $code .= "<a href=\"{$link}\" class=\"leerMasButton\">" . _("Ver más...") . "</a>";
            $code .= "</div>";
            $code .= "</div>";
            $code .= "</div>";
            return html_entity_decode($code,ENT_QUOTES,"UTF-8");
        }

        public function getPrecioMedio() {
            $price = round(doubleval($this->price/$this->getInmueble()->getSuperficieConstruida()),3);
            return $price;            
        }
        
        
        public function getUserType(){
            $user = user::find("username='{$this->username}' AND status='1'");
            if ($user->hasResult()) {
                return $user->first()->type;                
            }            
        }
        
        public function getUser(){            
            $user = user::find("username='{$this->username}' AND status='1'");
            if ($user->hasResult()) {
                return $user->first();
            }
        }
        
        public function getUserAsText(){            
            $user = user::find("username='{$this->username}' AND status='1'");
            if ($user->hasResult()) {
                $user    = $user->first();
                $result = "{$user->getDisplayName()} ({$user->getUsername()}) ";
                return html_entity_decode($result,ENT_QUOTES,"UTF-8"); 
            }
        }
        
        public function hasDestacados(){
            $test = $this->id_inmueble;
            $destacados = anuncio_has_destacado::find("id_anuncio='{$test}' AND status='1'");
            if($destacados->hasResult()){
                return true;
            } else {
                return false;
            }
        }
        
        public function getTipoTarifaDuenyoAnuncio(){
            //$user = $this->getUser();
            //$tipoTarifa = user_has_tipotarifa::find("id_usuario='{$user->id}'");
            //if ($tipoTarifa->hasResult()) {
            //    return $tipoTarifa->first();
            //}
            //TODO: Aqui, cuando tengamos claro como organizar los tipos de tarifas, habrá que rehacer este método para que devuelva el tipo de tarifa.
            return 1;
        }
        
        public function getSize(){            
            //0-> pequenyo
            //1 -> medio
            //2 -> grande
            if($this->hasDestacados()){
                return 2;
            }
            //0 -> particular
            //Cualquier otro -> inmobiliaria
            if(!$this->is_profesional && !$this->getInmueble()->hasImages()){
               return 0;
            }
            if($this->is_profesional && $this->getTipoTarifaDuenyoAnuncio() === 0){
                return 0;
            }
            return 1;
        }
        
        
        public function getAnuncioPrice() {
            // TODO: Cambiar según tipo de operación comercial
            return $this->price;
        }
        
    };
?>