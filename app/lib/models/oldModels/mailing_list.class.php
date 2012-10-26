<?php
    class mailing_list extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $name;
        protected $description;
        protected $created_at;
        protected $author;
        protected $status;
        
        
        public static $has_and_belongs_to_many = array (
            array(
                "property"  => "suscribers",
                "srcTable"   => "mailing_list",
                "srcColumn" => "id",
                "dstTable"  => "mailing_list_suscriber",
                "dstColumn" => "id",
                "throughTable" => "suscriber_in_mailing_list",
                "throughTableSrcColumn" => "id_list",
                "throughTableDstColumn" => "id_suscriber",
                "update" => "restrict",
                "delete" => "restrict"
            )
        );
        
        public static $belongs_to = array (
             array(
                "property" => "author",
                "table"    => "user",
                "srcColumn"=> "author",
                "dstColumn"=> "username",
                "update"   => "restrict",
                "delete"   => "restrict"
             )
         );
         
         public function getSuscriberCount() {
             return count($this->getSuscribers());
         }
         
         public function getId() {
             return $this->id;
         }
         
          public function getStatus() {
            return (intval($this->status));
        }
        
         public function getStatusAsText() {
            $status = $this->getStatus();
            if ($status===1) {
                return _("Activa");
            }
            return  _("Inactiva");
        }
        
        public function getStatusJSON() {
            return htmlentities($this->getStatusAsText(),ENT_QUOTES,"UTF-8");
        }
         
         public function getSuscribers() {
            return $this->suscribers;
         }
         
         public function getCountSuscribers() {
             return count($this->suscribers);
         }
         
         public function getName() {
             return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");
         }
         
         public function getNameJSON() {
             return htmlentities($this->getName(),ENT_QUOTES,"UTF-8");
         }
         
         public function getDescription() {
             return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");
         }
         
         public function getDate() {
             return date("d/m/Y H:i:s",strtotime($this->created_at));
         }
         
         public function getAuthor() {
             return $this->author->first()->getDisplayName();
         }
         
         public function getAuthorJSON() {
             return htmlentities($this->getAuthor(),ENT_QUOTES,"UTF-8");
         }
         
         public function hasSuscriber($id) {
             $result         = false;
             $suscribers = $this->getSuscribers();
             if (count($suscribers)) {
                 foreach ($suscribers as $suscriber) {
                     if ($suscriber->getId()===$id) {
                         $result = true;
                     }
                 }
             }
             return $result;
         }
         
         public function unsuscribe($suscriber) {
             $database = FW_Database::getInstance();
             $prefix      = $database->getPrefix();
             $query      = "DELETE FROM {$prefix}suscriber_in_mailing_list WHERE ( id_list='{$this->id}' AND id_suscriber='{$suscriber}' )";             
             $database->query($query);
             return ($database->affectedRows());               
         }
        
    };
?>