<?php
    class service_category extends FW_ActiveRecord_Tree {
        protected $id;
        protected $id_parent;
        protected $name;
        protected $slug;
        protected $description;
        protected $image;
        protected $status;
        protected $created_at;
        protected $author;
        public static $acts_as_tree = array(
            "idColumn" => "id",
            "parentColumn" => "id_parent",
            "siblingOrder" => array( array(
                    "column" => "name",
                    "type" => "ASC"
                ))
        );
        public static $belongs_to = array( array(
                "property" => "author",
                "table" => "user",
                "srcColumn" => "author",
                "dstColumn" => "username",
                "update" => "restrict",
                "delete" => "restrict"
            ));
        public function getId() {
            return intval($this->id);
        }

        public function getAuthor() {
            return $this->author->first()->getDisplayName();
        }

        public function getName() {
            return html_entity_decode($this->name, ENT_QUOTES, "UTF-8");
        }

        public function getNameJSON() {
            return htmlentities($this->getName(), ENT_QUOTES, "UTF-8");
        }

        public function getAuthorJSON() {
            return htmlentities($this->author->first()->getDisplayName(), ENT_QUOTES, "UTF-8");
        }

        public function getSlug() {
            return $this->slug;
        }

        public function getStatus() {
            return intval($this->status);
        }

        public function getStatusAsJSON() {
            $status = intval($this->enabled);
            if ( $status === 1 ) {
                return _("Activa");
            }
            return _("Inactiva");
        }

        public function getDescription() {
            return html_entity_decode($this->description, ENT_QUOTES, 'UTF-8');
        }

        public function getDate() {
            return date("d/m/Y H:i:s", strtotime($this->created_at));
        }

        public function hasImage() {
            $result = false;
            if ( $this->image !== null ) {
                if ( is_file("images/services/categories/{$this->image}") ) {
                    $result = true;
                }
            }
            return $result;
        }

        public function getImage() {
            $path = rtrim($this->_getBaseURL(), '/');
            if ( $this->hasImage() ) {
                $image = "{$path}/images/services/categories/{$this->image}";
            }
            else {
                $image = "{$path}/images/services/categories/noimage.png";
            }
            return $image;
        }
        
        public function hasServices() {
            $services  = service::find("id_category='{$this->id}'  ");
            return ($services->hasResult());
        }
        
        public function getServices() {
            $services  = service::find("id_category='{$this->id}'  ",array(array("column"=>"name","type"=>"ASC")));            
            return $services;
        }
        
        
        public function hasActiveServices() {
            $services  = service::find("id_category='{$this->id}' AND status='1' ");
            return ($services->hasResult());
        }
        
        public function getActiveServices() {
            $services  = service::find("id_category='{$this->id}' AND status='1' ",array(array("column"=>"name","type"=>"ASC")));            
            return $services;
        }

    };
?>
