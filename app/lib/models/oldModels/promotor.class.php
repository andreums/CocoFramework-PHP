<?php
class promotor extends professional {

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
    
    public static $has_and_belongs_to_many = array (
            array(
                    "property"  => "paginas",
                    "srcTable"   => "promotor",
                    "srcColumn" => "id",
                    "dstTable"  => "page",
                    "dstColumn" => "id",
                    "throughTable" => "promotor_has_pages",
                    "throughTableSrcColumn" => "id_promotor",
                    "throughTableDstColumn" => "id_page",
                    "update" => "cascade",
                    "delete"  => "cascade"
                ),
                array(
                    "property"  => "oficinas",
                    "srcTable"   => "promotor",
                    "srcColumn" => "id",
                    "dstTable"  => "oficina_profesional",
                    "dstColumn" => "id",
                    "throughTable" => "promotor_has_oficinas",
                    "throughTableSrcColumn" => "id_promotor",
                    "throughTableDstColumn" => "id_oficina",
                    "update" => "restrict",
                    "delete" => "restrict"
                ),
                array(
                    "property"  => "promociones",
                    "srcTable"   => "promotor",
                    "srcColumn" => "id",
                    "dstTable"  => "promocion",
                    "dstColumn" => "id",
                    "throughTable" => "promotor_has_promociones",
                    "throughTableSrcColumn" => "id_promotor",
                    "throughTableDstColumn" => "id_promocion",
                    "update" => "restrict",
                    "delete" => "restrict"
                )
        );
        
        public static $belongs_to = array (
         array(
            "property" => "creator",
            "table"    => "user",
            "srcColumn"=> "creator",
            "dstColumn"=> "username",
            "update"   => "restrict",
            "delete"   => "restrict"
         )
    );

    public function getUser() {
        return $this->creator->first();
    } 

    

    
    public function getOficinaById($id) {
        $oficina = $this -> oficinas -> find("b.id='{$id}'");
        if ($oficina -> hasResult()) {
            return $oficina -> first();
        }
    }
    
    public function getPaginasAdmin($status="all") {
        $conditions = "";
        if ($status==="unpublished") {
            $paginas = $this -> paginas -> find("b.status='0'");            
        }
        else if ($status==="published") {
            $paginas = $this -> paginas -> find("b.status='1'");
        }
        else if ($status==="all") {
            $paginas = $this -> paginas -> find();
        }                        
        return $paginas;
    }
    
    public function getPaginas() {
        $paginas = $this -> paginas -> find("b.status='1'");
        return $paginas;
    }
    
    public function getPaginasNoPublicadas() {
        $paginas = $this -> paginas -> find("b.status='0'");
        return $paginas;
    }

    public function getPaginasPublicadas() {
        $paginas = $this -> paginas -> find("b.status='1'");
        return $paginas;
    }

    public function getPaginaPromotor($slug) {
        $pagina = $this -> paginas -> find("b.slug='{$slug}' AND b.status='1'");
        if ($pagina -> hasResult()) {
            return $pagina -> first() ;
        }
    }
    
    public function getPaginaById($id) {
        $pagina = $this -> paginas -> find("b.id='{$id}' ");
        if ($pagina -> hasResult()) {
            return $pagina -> first() ;
        }        
    }

    

    public function getWebpage() {
        return $this -> webpage;
    }

    public function getDireccion() {
        $direccion = "{$this->via} {$this->numero} {$this->municipio} {$this->codigo_postal} {$this->provincia} {$this->estado} {$this->pais}";
        return utf8_encode($direccion);
    }

    public function getInfoWindowText() {
        $text = "<h5>{$this->nombre}</h5><br/>" . $this -> getDireccion() . "<br/>" . _("Teléfono") . "{$this->getTelefono()}<br/>" . _("Fax") . "{$this->getFax()}";
        return $text;
    }
    
    public function getPromocion($slug,$id) {
        $promocion = $this->promociones->find("b.id='{$id}' AND  b.slug='{$slug}' ");
        if ($promocion->hasResult()) {
            return $promocion->first();
        }        
    }
    
    

};
?>
