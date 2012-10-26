<?php
    class mailing_list_suscriber extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $name;
        protected $username;
        protected $email;
        protected $created_at;
        protected $status;
        protected $password;
        protected $has_confirmed;
        protected $type;
                
        public static $belongs_to = array (
             array(
                "property" => "username",
                "table"    => "user",
                "srcColumn"=> "username",
                "dstColumn"=> "username",
                "update"   => "restrict",
                "delete"   => "restrict"
             )
        ); 
        
        public function getId() {
            return intval($this->id);
        }
        
        public function getUsername() {
            if ($this->username!==null && $this->username->hasResult()) {
                return $this->username->first()->username;
            }
            return "";            
        }
        
        
        public function getName() {
            return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");            
        }
        
        public function getNameJSON() {
            $name = $this->getName();
            return htmlentities($name,ENT_QUOTES,"UTF-8");            
        }
        
        public function getEmail() {
            return html_entity_decode($this->email,ENT_QUOTES,"UTF-8");            
        }
        
        public function getEmails() {
            return email::find(" email='{$this->getEmail()}' ");
        }
        public function getHasConfirmedAsText() { 
            if (intval($this->has_confirmed)===1) {
                return _("S&iacute;");
            }
            return _("No");
        } 
        
        public function getStatus() {
            return intval($this->status);
        }
        
        public function getDate( ) {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }
        
        public function getSuscribedLists() {
            $results       = array();
            $database  = FW_Database::getInstance();
            $prefix       = $database->getPrefix();
            $query        = "SELECT id_list FROM {$prefix}suscriber_in_mailing_list WHERE id_suscriber='{$this->id}' ";            
            $database->query($query);
            if ($database->numRows()>0) {
                while ($row=$database->fetchRow()) {                    
                    $list = mailing_list::find("id='{$row[0]}'");
                    if ($list->hasResult()) {                        
                        $results []= array("id"=>$list->first()->getId(),"name"=>$list->first()->getName());
                    }
                }
            }
            return $results;
        }
        
        public function getPassword() {
            return $this->password;
        }
        
        /* BEGIN Stats */
       public function getCountSentEmails() {
           return email::count("id"," email='{$this->getEmail()}' ");                      
       }
       
       public function getCountOpenedEmails() {
           return email::count("id"," email='{$this->getEmail()}' AND status='2' ");           
       }       
       
       public function getCountEmailsThisMonth() {
           return email::count("id","email='{$this->getEmail()}' AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())  ");           
       }
       
       public function getEmailRatio() {
           $sent        = intval($this->getCountSentEmails());           
           $opened = intval($this->getCountOpenedEmails());
           if ($sent===0 || $opened===0) {
               return 0;
           }
           $ratio        = (($opened/$sent)*100);
           return $ratio;           
       }
       
      /*  END Stats */
        
        
    };
?>