<?php
    class professional extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $username;
        protected $razon_social;
        protected $nombre;
        protected $cif;
        protected $status;
        protected $created_at;
        protected $logotipo;
        protected $icono;
        protected $descripcion_corta;
        protected $descripcion;
        protected $tipo;
        protected $creator;
        protected $slug;
        protected $telefono;
        protected $fax;
        protected $webpage;
        protected $approved;
        protected $numero;
        protected $via;
        protected $municipio;
        protected $codigo_postal;
        protected $provincia;
        protected $estado;
        protected $pais;
        protected $paginas;
        protected $oficinas;
        protected $promociones;
        protected $twitter;
        protected $facebook;
        
        public function getId() {
            return $this->id;
        }

        public function getSlug() {
            return $this->slug;
        }

        public function getAddress() {
            $direccion = "{$this->via} {$this->numero} {$this->municipio} {$this->codigo_postal} {$this->provincia} {$this->estado} {$this->pais}";
            return utf8_encode($direccion);
        }

        public function getContents() {
            $contents = $this->paginas->find("b.status='1'");
            return $contents;
        }

        public function getContent($id, $slug) {
            $conditions = " b.status='1' AND b.id='{$id}' AND b.slug='{$slug}' ";
            $content = $this->paginas->find($conditions);
            if ( $content->hasResult() ) {
                return $content->first();
            }
        }

        public function getContentById($id) {
            $conditions = "b.id='{$id}'  AND b.author='{$this->getUser()->username}' ";
            $content = $this->paginas->find($conditions);
            if ( $content->hasResult() ) {
                return $content->first();
            }
        }

        public function getOficinas($active = true) {
            $conditions = "";
            if ( $active ) {
                $conditions = "b.status='1'";
            }
            $orders = array(
                array(
                    "column" => "municipio",
                    "type" => "ASC"
                ),
                array(
                    "column" => "nombre",
                    "type" => "ASC"
                )
            );
            $oficinas = $this->oficinas->find($conditions, $orders);
            return $oficinas;
        }

        public function getOficina($slug, $id) {
            $oficina = $this->oficinas->find("b.slug='{$slug}' AND b.id='{$id}' AND b.status='1'");
            if ( $oficina->hasResult() ) {
                return $oficina->first();
            }
        }

        public function getOficinaById($id) {
            $oficina = $this->oficinas->find("b.id='{$id}'");
            if ( $oficina->hasResult() ) {
                return $oficina->first();
            }
        }

        //Empiezo com promociones
        public function getPromociones($active = true) {
            $conditions = "";
            if ( $active ) {
                $conditions = "b.status='1'";
            }
            $orders = array(
                array(
                    "column" => "municipio",
                    "type" => "ASC"
                ),
                array(
                    "column" => "nombre",
                    "type" => "ASC"
                )
            );
            $promociones = $this->promociones->find($conditions, $orders);
            return $promociones;
        }

        public function getPromocion($slug, $id) {
            $promocion = $this->promociones->find("b.slug='{$slug}' AND b.id='{$id}' AND b.status='1'");
            if ( $promocion->hasResult() ) {
                return $promocion->first();
            }
        }

        public function getPromocionById($id) {
            $promocion = $this->promociones->find("b.id='{$id}'");
            if ( $promocion->hasResult() ) {
                return $promocion->first();
            }
        }

        public function getUsername() {
            $user = user::find(" username='{$this->creator}' ");
            $user = $user->first();
            return $user->getUsername();
        }

        public function getUser() {
            return $this->creator->first();
        }

        public function getRazonSocial() {
            return utf8_encode($this->razon_social);
        }

        public function getNombre() {
            return utf8_encode($this->nombre);
        }

        public function getTelefono() {
            return utf8_encode($this->telefono);
        }

        public function getFax() {
            return utf8_encode($this->fax);
        }

        public function getWeb() {
            return $this->webpage;
        }

        public function getDescripcionCorta() {
            return utf8_encode($this->descripcion_corta);
        }

        public function getDescripcion() {
            return html_entity_decode($this->descripcion, ENT_COMPAT, 'UTF-8');
        }

        public function getVia() {
            return utf8_encode($this->via);
        }

        public function getNumero() {
            return utf8_encode($this->numero);
        }

        public function getMunicipio() {
            return utf8_encode($this->municipio);
        }

        public function getCodigoPostal() {
            return utf8_encode($this->codigo_postal);
        }

        public function getProvincia() {
            return utf8_encode($this->provincia);
        }

        public function getEstado() {
            return utf8_encode($this->estado);
        }

        public function getPais() {
            return utf8_encode($this->pais);
        }

        public function getCIF() {
            return utf8_encode($this->cif);
        }

        public function hasImage() {
            $result = false;
            $type = get_class($this);
            $path = rtrim($this->_getBaseURL(), '/');
            $file = $this->logotipo;
            if ( is_file($file) ) {
                return $file;
            }
            return $result;
        }

        public function getImage() {
            $path = rtrim($this->_getBaseURL(), '/');
            $image = $this->hasImage();
            if ( $image === false ) {
                $image = "{$path}/images/noimage.png";
            }
            $image = FW_Config::getInstance()->get("core.global.baseURL") . "/{$image}";
            return $image;
        }

        public function hasContents() {
            return (count($this->getContents()) > 0);
        }

        public function getTitleType() {
            $tipo = $this->getType();
            if ( $tipo === "promotor" ) {
                $tipo = _("del promotor");
            }
            if ( $tipo === "inmobiliaria" ) {
                $tipo = _("de la inmobiliaria");
            }
            if ( $tipo === "banco" ) {
                $tipo = _("del banco");
            }
            return $tipo;
        }

        public function getBreadCrumbType() {
            $tipo = $this->getType();
            if ( $tipo === "promotor" ) {
                $tipo = _("del promotor");
                $tipo = _("Promotores");
            }
            if ( $tipo === "inmobiliaria" ) {
                $tipo = _("de la inmobiliaria");
                $tipo = _("Inmobiliarias");
            }
            if ( $tipo === "banco" ) {
                $tipo = _("del banco");
                $tipo = _("Bancos");
            }
            return $tipo;
        }

        public function getType() {
            return get_class($this);
        }
        
        public function getTwitter() {
            return html_entity_decode($this->twitter,ENT_QUOTES,"UTF-8");
        }
        
        
        public function getFacebook() {
            return html_entity_decode($this->facebook,ENT_QUOTES,"UTF-8");
        }

    };
?>