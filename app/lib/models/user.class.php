<?php
class user extends FW_ActiveRecord_Model {


    protected $username;
    protected $password;
    protected $email;    
    protected $name;
    protected $activation_key;
    protected $date_register;
    protected $language;
    protected $theme;
    protected $display_name;
    protected $status;    
    protected $type;
    protected $telephone;
    protected $description;
    protected $role;
    protected $avatar;
    protected $notifications;
    protected $question;
    protected $answer;

    public static $has_and_belongs_to_many = array (
            array(
                    "property"  => "role",
                    "srcTable"  => "user",
                    "srcColumn" => "username",
                    "dstTable"  => "role",
                    "dstColumn" => "role",
                    "throughTable" => "user_has_roles",
                    "throughTableSrcColumn" => "username",
                    "throughTableDstColumn" => "role",
                    "update" => "restrict",
                    "delete" => "restrict"
                )
        );
        
        public function getUserWithAvatar() {
            $name         = "";
            $avatar        = "";
            $name         = $this->getDisplayName();
            $avatar        = $this->getAvatar();
            if (strlen($avatar)>0) {
                $name = "<p class=\"span-6\"><img class=\"span-2 column\" src=\"{$avatar}\" style=\"width:20px !important;\"  /><span class=\"span-4 column last\">{$name}</span></p>";
            }
            else {
                $name = "<p>{$name}</p>";
            }            
            return $name;
            
        }
        
        
        
        public function getUsername() {
            return $this->username;
        }
        
        public function getFullName() {
            return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");
        }
        
        public function getName() {
            return $this->name;
            //return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");
        }
        
        public function getNameJSON() {
            return htmlentities(html_entity_decode($this->name,ENT_QUOTES,"UTF-8"),ENT_QUOTES,"UTF-8");
        }
        public function getTelephone() {
            return $this->telephone;
        }       
        
        public function getDescription() {
            return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");
        }
        
        public function getEmail() {
            return $this->email;
        }
        
        public function getRoles() {                                                            
            $roles = array();
            if (count($this->role)>0) {
                foreach ($this->role as $role) {                
                    $roles []= $role->role;
                }            
            } 
            return $roles;
        }
        
        public function hasRole($role) {
            $roles = $this->getRoles();
            if (in_array($role,$roles)) {
                return true;
            }
            return false;
        }




    public function getDisplayName() {
        return html_entity_decode($this->display_name,ENT_QUOTES,"UTF-8");
    }

    public function validateUsername() {
        if ( strlen($this->username)>5 && strlen($this->username)<20 && (!$this->_filter->isNumeric($this->username[0])) ) {
            if ($this->_filter->isAlphaNumeric($this->username)) {
                return true;
            }
            else {
                return false;
            }

        }
        else {
            return false;
        }
    }



    public function getPreferences() {
        return array("username"=>$this->username,"email"=>$this->email,"name"=>$this->_filter->convertForOutput($this->name),"language"=>$this->preferredLanguage,"notifications"=>$this->notifications);
    }

    public function setPreferences($preferences) {

        if (isset($preferences["name"]) && strlen($preferences["name"])>0) {
            $this->name = $this->_filter->sanitizeString($preferences["name"],true);
        }

        if (isset($preferences["email"]) && strlen($preferences["email"])>0) {
            $this->email = $this->_filter->sanitizeString($preferences["email"]);
        }

        if (isset($preferences["language"]) && strlen($preferences["language"])>0) {
            $this->preferredLanguage = $this->_filter->sanitizeString($preferences["language"]);
        }

        if (isset($preferences["notifications"]) && strlen($preferences["notifications"])>0) {
            if ( ($preferences["notifications"]=="on") || ($preferences["notifications"]=="off") ) {
                $this->notifications = $preferences["notifications"];
            }
        }

        if ($this->validateData()) {
            $this->save();
            return true;
        }
        else {
            return false;
        }
    }

    public function block() {


        if ($this->status==="0") {
            $this->status = "1";
            if ($this->save()) {
                return "b";
            }
            else {
                return false;
            }
        }
        else {
            $this->status = "0";
            if ($this->save()) {
                return "ub";
            }
            else {
                return false;
            }
        }
        return false;
    }
    
    public function isBlocked() {
        $status =$this->getStatusAsInteger();
        return ( ($status===0) || ($status===2) );
    }
    
    public function getStatusAsInteger() {
        return intval($this->status);
    }

    public function getStatus() {
        if (intval($this->status)===1) {
            return utf8_decode(_("Activo"));
        }
        else {
            return utf8_decode(_("Bloqueado"));
        }
    }
    
    public function getStatusAsText() {
        $status = "";
        if (intval($this->status)===1) {
            $status = _("Activo");
        }
        else {
            $status = _("Bloqueado");
        }
        return html_entity_decode($status,ENT_QUOTES,"UTF-8");
    }

    public function getDateRegister() {
        return (date("d/m/Y H:i:s",strtotime($this->date_register)));
    }

    public function getRole() {

        $roles = array();
        if ($this->role instanceof FW_ActiveRecord_Relation) {
            foreach ($this->role as $role) {
                $roles []= $role->role;
            }
            return implode(' ',$roles);
        }

        return $this->role;
    }

    public function activate($code) {
        if ($this->activation_key === $code) {
            $this->status         = 0;
            $this->activation_key = md5(rand(0,1000).uniqid());
            $this->save();
            return true;
        }
        return false;
    }
    
    public function getType() {
        return $this->type;        
    }
    
    public function isProfessional() {
        $count = professional::count("id"," creator='{$this->username}' ");
        if (intval($count)>0) {
            return true;
        }
        return false;
    }
    
    public function getProfessional() {
        $professional = professional::find("creator='{$this->username}' ");
        if ($professional->hasResult()) {
            return $professional->first();
        }                
    }
    
    public function getAvatar() {
        $avatar = ""; 
        $base    = $this->_getBaseURL();        
        $path    = "{$base}/uploads/images/avatar";                
        if (strlen($this->avatar)) {
            $avatar = "{$path}/{$this->avatar}";
        }
        else {
            $avatar = "{$path}/no_avatar.png";
        }        
        return $avatar;        
    }
    
    public function getSecurityQuestion() {
        return html_entity_decode($this->question,ENT_QUOTES,"UTF-8");
    }
    
    public function getSecurityQuestionAnswer() {
        return html_entity_decode($this->answer,ENT_QUOTES,"UTF-8");
    }
    
    public function changePassword($password) {
        $this->password = sha1($password);
        return $this->save();
    }
    
    
    // Estadísticas de anuncios
    public function getAnuncios() {
        $conditions = " username='{$this->username}' AND status='1' ";
        $anuncios    = anuncio::find($conditions);
        return $anuncios;                
    }
    
    public function getAnunciosTexto() {
        $conditions = " username='{$this->username}' AND status='1' ";
        $anuncios    = anuncio_texto::find($conditions);
        return $anuncios;                
    }
    
    
    public function getCountAnuncios() {
        $conditions = " username='{$this->username}' ";
        $count          = anuncio::count("id",$conditions);
        return intval($count);        
    }
    
    
    public function getCountAnunciosByType() {
           
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
            $query         = "SELECT type,COUNT(DISTINCT(id)) AS qty FROM {$prefix}anuncio WHERE username='{$this->username}'  GROUP BY(type)";
            $database->query($query);
            while ($row=$database->fetchAssoc()) {
                $options[$row["type"]]["qty"]  = intval($row["qty"]);                                
            }            
            return $options;            
        }
        
        public function getCountAnunciosByInmuebleType() {
            $result     = array(
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
                        
            $database  = FW_Database::getInstance();
            $prefix        = $database->getPrefix();
                        
            $query         = "SELECT inmueble_type,inmueble_subtype,COUNT(DISTINCT(id)) AS qty FROM {$prefix}anuncio WHERE username='{$this->username}'  GROUP BY inmueble_type,inmueble_subtype";            
            $database->query($query);
            while ($row=$database->fetchAssoc()) {
                $result[$row["inmueble_type"]][$row["inmueble_subtype"]]["qty"] = intval($row["qty"]);
            }            
            return $result;              
        }   
        
        public function getCountAnunciosByTypeAndInmuebleType() {
            $result       = array();   
                        
            $options = array(
                0 => array(
                    0 => array(
                        "label"=>_("Piso"),
                        "data" => array (
                            0 => array("qty"=>0,"label"=>_("Venta")),
                            1 => array("qty"=>0,"label"=>_("Alquiler")),
                            2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                            3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                            4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                            5 => array("qty"=>0,"label"=>_("Permuta")),
                            6 => array("qty"=>0,"label"=>_("Traspaso")),
                        )
                    ),
                    1 => array(
                        "qty"=>0,"label"=>_("Adosado"),
                        "data" => array (
                            0 => array("qty"=>0,"label"=>_("Venta")),
                            1 => array("qty"=>0,"label"=>_("Alquiler")),
                            2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                            3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                            4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                            5 => array("qty"=>0,"label"=>_("Permuta")),
                            6 => array("qty"=>0,"label"=>_("Traspaso")),
                        )                            
                    ),
                    2 => array(
                    "qty"=>0,"label"=>_("Apartamento"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        )
                    ),
                    3 => array(
                        "qty"=>0,"label"=>_("Ático"),
                        "data" => array (
                            0 => array("qty"=>0,"label"=>_("Venta")),
                            1 => array("qty"=>0,"label"=>_("Alquiler")),
                            2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                            3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                            4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                            5 => array("qty"=>0,"label"=>_("Permuta")),
                            6 => array("qty"=>0,"label"=>_("Traspaso"))
                            )                    
                    ),
                    4 => array("qty"=>0,"label"=>_("Casa"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        )
                    ),
                    5 => array("qty"=>0,"label"=>_("Dúplex"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        )
                    ),
                    6 => array("qty"=>0,"label"=>_("Estudio"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        )
                    ),
                    7 => array("qty"=>0,"label"=>_("Loft"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        )
                    ),
                    8 => array("qty"=>0,"label"=>_("Planta baja"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        )
                    ),
                    9 => array("qty"=>0,"label"=>_("Habitación"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        )
                    ),                    
                ),                
                1 => array(
                    -1 => array("qty"=>0,"label"=>_("Oficina"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        )
                    ),
                ),
                2 => array(
                    -1 => array("qty"=>0,"label"=>_("Nave"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        )
                    ),
                ),
                3 => array(
                    -1 => array("qty"=>0,"label"=>_("Local"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        ),
                        )
                ),
                4 => array(
                    -1 => array("qty"=>0,"label"=>_("Garaje"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        ),
                        )
                ),
                5 => array(
                    -1 => array("qty"=>0,"label"=>_("Trastero"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        ),
                        )
                ),
                6 => array(
                    -1 => array("qty"=>0,"label"=>_("Edificio"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        ),
                        )
                ),
                7 => array(
                    -1 => array("qty"=>0,"label"=>_("Hotel"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        ),
                    )
                ),
                8 => array(
                    -1 => array("qty"=>0,"label"=>_("Terreno"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        ),
                    )
                ),
                9 => array(
                    -1 => array("qty"=>0,"label"=>_("Casa de madera"),
                    "data" => array (
                        0 => array("qty"=>0,"label"=>_("Venta")),
                        1 => array("qty"=>0,"label"=>_("Alquiler")),
                        2 => array("qty"=>0,"label"=>_("Alquiler con opción a compra")),
                        3 => array("qty"=>0,"label"=>_("Alquiler vacacional")),
                        4 => array("qty"=>0,"label"=>_("Alquiler compartido")),
                        5 => array("qty"=>0,"label"=>_("Permuta")),
                        6 => array("qty"=>0,"label"=>_("Traspaso")),
                        ),
                    )
                )                            
            );
            
            $database  = FW_Database::getInstance();
            $prefix        = $database->getPrefix();
            
            $query         = "SELECT type,inmueble_type,inmueble_subtype,COUNT(DISTINCT(id)) AS qty FROM {$prefix}anuncio WHERE username='{$this->username}' GROUP BY inmueble_type,inmueble_subtype";            
            $database->query($query);
            while ($row=$database->fetchAssoc()) {
                $type        = $row["type"];
                $itype        = $row["inmueble_type"];
                $isubtype = $row["inmueble_subtype"];
                
                                
                $options[$itype][$isubtype]["data"][$type]["qty"] = intval($row["qty"]);
            }            
            return $options;                          
        }   


        public function getZonesAnuncios() {
            $results      = array();
            $database  = FW_Database::getInstance();
            $prefix        = $database->getPrefix();
            
            $query         = "SELECT id_zone,COUNT(DISTINCT(id)) AS qty FROM {$prefix}anuncio WHERE username='{$this->username}' GROUP BY id_zone";            
            $database->query($query);            
            while ($row=$database->fetchAssoc()) {
                $zone = zone::find(" id='{$row["id_zone"]}' ");
                if ($zone->hasResult()) {
                    $zone        = $zone->first();
                    $results []= array("zone"=>$zone->getName(),"qty"=>intval($row["qty"]));
                }
            }            
            return $results;                  
        }
        
        
        // Estadísticas de anuncios de texto
        public function getCountAnunciosTexto() {
            $conditions = " username='{$this->username}' ";
            $count          = anuncio_texto::count("id",$conditions);
            return intval($count);
        }
        
        public function getZonesAnunciosTexto() {
            $results      = array();
            $database  = FW_Database::getInstance();
            $prefix        = $database->getPrefix();
            
            $query         = "SELECT id_zone,COUNT(DISTINCT(id)) AS qty FROM {$prefix}anuncio_texto WHERE username='{$this->username}' GROUP BY id_zone";            
            $database->query($query);            
            while ($row=$database->fetchAssoc()) {
                $zone = zone::find(" id='{$row["id_zone"]}' ");
                if ($zone->hasResult()) {
                    $zone        = $zone->first();
                    $results []= array("zone"=>$zone->getName(),"qty"=>intval($row["qty"]));
                }
            }            
            return $results;                  
        }
        
        public function getCountAnunciosTextoByType() {
            $results = array (
                0 => array("label"=> _("Piso"),"qty"=>0),
                1 => array("label"=>_("Casa/Chalet"),"qty"=>0),
                2 => array("label"=>_("Oficina"),"qty"=>0),
                3 => array("label"=>_("Local"),"qty"=>0),
                4 => array("label"=>_("Nave"),"qty"=>0),
                5 => array("label"=>_("Garaje"),"qty"=>0),
                6 => array("label"=>_("Trastero"),"qty"=>0),
                7 => array("label"=>_("Edificio"),"qty"=>0),
                8 => array("label"=>_("Hotel"),"qty"=>0),
                9 => array("label"=>_("Terreno"),"qty"=>0),
                10 => array("label"=>_("Casa de madera"),"qty"=>0)
            );            
            
            $database  = FW_Database::getInstance();
            $prefix        = $database->getPrefix();
            
            $query         = "SELECT type,COUNT(DISTINCT(id)) AS qty FROM {$prefix}anuncio_texto WHERE username='{$this->username}' GROUP BY(type) ORDER BY type ASC";            
            $database->query($query);
            while ($row=$database->fetchAssoc()) {
                $type = $row["type"];
                $qty   = intval($row["qty"]);                
                $results[$type]["qty"]  = $qty;                                
            }
            return $results;
        }        
        
        
        // Estadísticas de newsletter
        public function getNewsletterStats() {
            $results = array(
                "sent"            => 0,
                "opened"      => 0,
                "unopened"  => 0,
                "campaigns" => 0,
                "ratio"             => 0            
            );
            
            $suscriber = mailing_list_suscriber::find(" username='{$this->username}' ");
            if ($suscriber->hasResult()) {
                $suscriber = $suscriber->first();
                
                $sent                    = email::count("id"," id_suscriber='{$suscriber->getId()}' ");                
                $results["sent"] = intval($sent);                
                
                $opened                     = email::count("id"," id_suscriber='{$suscriber->getId()}' AND opened_at IS NOT NULL ");                
                $results["opened"] = intval($opened);
                
                $unopened                     = email::count("id"," id_suscriber='{$suscriber->getId()}' AND opened_at IS NULL ");                
                $results["unopened"]  = intval($unopened);
                
                $ratio                    = round(doubleval(($results["opened"]*100)/$results["sent"]),3);
                $results["ratio"] = $ratio;  
                
                $campaigns                    = email::count("DISTINCT(id_campaign)","id_suscriber='{$suscriber->getId()}' ");
                $results["campaigns"] = intval($campaigns);
            }                       
            
            return $results;
        }
        
        
    

};
?>
