<?php
    class promocion extends FW_ActiveRecord_Model {
        protected $id;
        protected $name;
        protected $slug;
        protected $descripcion;
        protected $descripcion_corta;
        protected $tipo;
        protected $creator;
        protected $numero;
        protected $via;
        protected $municipio;
        protected $codigo_postal;
        protected $provincia;
        protected $estado;
        protected $pais;
        protected $map_lat;
        protected $map_lng;
        protected $status;
        protected $created_at;
        public static $has_many = array(
            array(
                "property" => "statuses",
                "table" => "status_promocion",
                "srcColumn" => "id",
                "dstColumn" => "id_promocion",
                "update" => "cascade",
                "delete" => "cascade"
            ),
            array(
                "property" => "fotos",
                "table" => "foto_promocion",
                "srcColumn" => "id",
                "dstColumn" => "id_promocion",
                "update" => "cascade",
                "delete" => "cascade"
            ),
            array(
                "property" => "documentos",
                "table" => "documento_promocion",
                "srcColumn" => "id",
                "dstColumn" => "id_promocion",
                "update" => "cascade",
                "delete" => "cascade"
            )
        );
        public function getLat() {
            $latitud = $this->map_lat;
            return $latitud;
        }

        public function getLng() {
            $longitud = $this->map_lng;
            return $longitud;
        }

        public function getFotosPromocion() {
            $fotos = $this->fotos;
            return $fotos;
        }

        public function getDireccion() {
            $direccion = "{$this->via} {$this->numero} {$this->municipio} {$this->codigo_postal} {$this->provincia} {$this->estado} {$this->pais}";
            return utf8_encode($direccion);
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

        public function getNombre() {
            return utf8_encode($this->name);
        }

        public function getDescription() {
            return utf8_encode($this->descripcion);
        }

        public function getDescriptionCorta() {
            return utf8_encode($this->descripcion_corta);
        }

        public function activate() {
            if ( $this->status === "0" ) {
                $this->status = 1;
                if ( $this->save() ) {
                    return "active";
                }
            }
            else {
                if ( $this->status === "1" ) {
                    $this->status = 0;
                    if ( $this->save() ) {
                        return "inactive";
                    }
                }
            }
            return false;
        }

        //Metodos que devuelven JSON
        public function getDate() {
            return date("Y-m-d H:i:s", strtotime($this->created_at));
        }

        public function getNameAsJSON() {
            return htmlentities($this->name, ENT_QUOTES, "UTF-8");
        }

        public function getIdAsJSON() {
            return htmlentities($this->id, ENT_QUOTES, "UTF-8");
        }

        public function getStatusAsJSON() {
            $status = intval($this->status);
            if ( $status === 0 ) {
                $status = _("Inactivo");
            }
            if ( $status === 1 ) {
                $status = _("Activo");
            }
            if ( $status === 2 ) {
                $status = _("Bloqueado por el administrador");
            }
            return htmlentities($status, ENT_QUOTES, "UTF-8");
        }

        private function _getPromocionOwner($username) {
            $user = promotor::find("creator='{$username}'");
            if ( $user->hasResult() ) {
                return $user->first();
            }
        }

        public function getHTMLPromocion() {
            $code = '';
            $urlBase = FW_Config::getInstance()->get("core.global.baseURL");
            //$type, $slug, $id,$slugpromocion,$idpromocion
            $user = $this->_getPromocionOwner($this->creator);
            $link = html::link_for_internal("profesionales", "promocion", "displayPublicPromocion", array(
                "type" => "promotores",
                "slug" => $user->slug,
                "id" => $user->id,
                "slugpromocion" => $this->slug,
                "idpromocion" => $this->id
            ));
            $code .= "<div class=\"promocion\">";
            $code .= "<div class = \"resultTitle\"><p><a href=\"" . $link . "\" class=\"resultTitleText\">" . $this->name . "</a>";
            $code .= "<img class = \"sendToFriendIcon\" id = \"" . strval($this->id) . "\" src=\"" . $urlBase . "/images/icons/vendoyo/Icono_Favoritos.png\" alt=\"\" height=\"25\" width=\"25\">";
            $code .= "<img class = \"favoritesIcon\" id = \"" . strval($this->id) . "\" src=\"" . $urlBase . "/images/icons/vendoyo/Icono_SobreAzul.png\" alt=\"\" height=\"23\" width=\"23\">";
            $code .= "</p></div>";
            $code .= "<div class=\"resultContent\">";
            $code .= "<a href=\"" . $link . "\" class=\"leerMasButton\">" . _("Ver más...") . "</a>";
            $code .= "</div>";
            $code .= "</div>";
            return html_entity_decode($code, ENT_QUOTES, "UTF-8");
        }

    };
?>