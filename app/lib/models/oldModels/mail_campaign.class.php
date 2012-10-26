<?php
    class mail_campaign extends FW_ActiveRecord_Model {
        
        protected $id;        
        protected $title;
        protected $slug;
        protected $description;
        protected $author;
        protected $created_at;
        protected $date_to_send;
        protected $date_sent;
        protected $id_template;
        protected $variables;
        protected $contents;
        protected $subject;
        protected $mail_from;
        protected $status;
               
        
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
        
        public static $has_many = array (
            array(
                    "property"      => "emails",
                    "table"            => "email",
                    "srcColumn"  => "id",
                    "dstColumn"  => "id_campaign",
                    "update"         => "cascade",
                    "delete"          => "cascade"
            )            
        );
        
        public function getId() {
            return $this->id;
        }
        
        public function getTitle() {
            return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");            
        }
        
        public function getTitleJSON() {
            return htmlentities($this->getTitle(),ENT_QUOTES,"UTF-8");            
        }
        
        public function getDescription() {
            return html_entity_decode($this->description,ENT_QUOTES,"UTF-8");            
        }
        
        public function getSubject() {
            return html_entity_decode($this->subject,ENT_QUOTES,"UTF-8");            
        }
        
        public function getSubjectJSON() {
            return htmlentities($this->getSubject(),ENT_QUOTES,"UTF-8");
        }
        
        public function getMailFrom() {
            return html_entity_decode($this->mail_from,ENT_QUOTES,"UTF-8");            
        }
        
        public function getAuthor() {
        return $this -> author -> first() -> getDisplayName();
    }
    
    public function getAuthorJSON() {
        return htmlentities($this -> author -> first() -> getDisplayName(),ENT_QUOTES,"UTF-8");
    }
    
    
    public function getVariables() {
        return unserialize($this->variables);
    }
    
    public function setVariables($variables) {
        $this->variables = serialize($variables);
    }
    
    public function getContents() {
        return html_entity_decode($this->contents,ENT_QUOTES);
    }
    
    

    public function getDate() {
        return date("d/m/Y H:i:s", strtotime($this -> created_at));
    }
    
    
    public function getDateToSend() {
        return date("d/m/Y H:i:s", strtotime($this->date_to_send));
    }
    
    public function getSentDate() {        
        if ($this->date_sent===null) {
            return htmlentities(_("No enviada"),ENT_QUOTES,"UTF-8");
        }
        return date("d/m/Y H:i:s", strtotime($this->date_sent));
    }
    
    public function getEmail($id) {
        $email = $this->emails->find(" id='{$id}' ");
        if ($email->hasResult()) {
            return $email->first();
        }
    }
    
    public function getEmails($status="all") {        
        $conditions = "";
        if ($status==="sent") {
            $conditions .= " sent_at IS NOT NULL ";
        }
        if ($status==="unsent") {
            $conditions .= " sent_at IS NULL  ";
        }
        if ($status==="opened") {
            $conditions .= " (sent_at IS NOT NULL) AND (opened_at IS NOT NULL) AND (status='1')  ";
        }
        if ($status==="unopened") {
            $conditions .= " (sent_at IS NOT NULL) AND (opened_at IS NULL) AND (status='0')  ";
        }
        
        $orders = array(array ("column"=>"email","type"=>"ASC"));        
        $emails = $this->emails->find($conditions,$orders);
        
        return $emails;
    }
    
    public function getEmailsToSend() {        
        $conditions = "sent_at IS NULL ";        
        $orders         = array(array ("column"=>"id","type"=>"ASC"));        
        $emails        = $this->emails->find($conditions,$orders);        
        return $emails;
    }
    
    public function hasTemplate() {
        return ($this->id_template!==null);
    }
    
    public function getTemplate() {
        if ($this->id_template!==null) {
            $template = mail_template::find(" id='{$this->id_template}' ");
            if ($template->hasResult()) {
                return $template->first();
            }            
        }        
    }
    
    
    public function getStatusJSON() {
        $result = "";
        $status = intval($this->status);
        
        if ($status===0) {
            $result = _("Inactiva");
        }
        if ($status===1) {
            if ($this->date_sent===null) {
                $result = _("No enviado");
            }
            if ($this->date_sent!==null) {
                $result = _("Enviado");
            }
        }
        
        return htmlentities($result,ENT_QUOTES,"UTF-8");
    }
    
    
    /* BEGIN Stats */
    
    
    
        public function getCountUnsentEmails() {
           return  email::count("id"," id_campaign='{$this->id}' AND sent_at IS NULL ");                      
       }
        
       public function getCountSentEmails() {
           return email::count("id"," id_campaign='{$this->id}' AND (sent_at IS NOT NULL)");                      
       }
       
       public function getCountOpenedEmails() {
           return email::count("id"," id_campaign='{$this->id}' AND sent_at IS NOT NULL AND status='1' ");           
       }
       
       public function getCountUnopenedEmails() {
           return email::count("id"," id_campaign='{$this->id}' AND sent_at IS NOT NULL AND status='0' ");           
       }
       
       public function getEmailRatio() {
           $sent        = intval($this->getCountSentEmails());           
           $opened = intval($this->getCountOpenedEmails());
           if ($sent===0 || $opened===0) {
               return 0;
           }
           $ratio        = (($opened/$sent)*100);
           return round(doubleval($ratio),3);           
       }
       
       
       public function getHitsByPlace() {
        $hits             = array();
        $database = FW_Database::getInstance();
        $prefix        = $database->getPrefix();        
        $query        = "SELECT COUNT(id) AS qty,CONCAT(city,',',region,',',country) AS place FROM {$prefix}email WHERE id_campaign='{$this->id}' GROUP BY city,region,country HAVING country IS NOT NULL ORDER BY qty DESC";      
                
        $database->query($query);
        if ($database->numRows()>0) {
            while ($hit=$database->fetchAssoc()) {
                $hit["place"] = trim(rtrim(rtrim(trim($hit["place"],','))));
                $hits             []= array("place"=>$hit["place"],"qty"=>$hit["qty"]);            
            }
        }
        return $hits;
    }

    
    public function getStatsByDay() {
            $results = array();            
            $database = FW_Database::getInstance();
            $prefix = $database->getPrefix();
            $query = "SELECT COUNT(id) AS qty, DATE(opened_at) AS day FROM {$prefix}email WHERE (id_campaign='{$this->id}') AND (opened_at IS NOT NULL) AND (status='1')GROUP BY DATE(opened_at) ORDER BY day ASC";
            $database->query($query);
            while ( $row = $database->fetchAssoc() ) {
                $results[$row["day"]] = $row["qty"];
            }
            return $results;
        }
    
    public function getStats() {
        $opened       = intval($this->getCountOpenedEmails());
        $unopened  = intval($this->getCountUnopenedEmails());
        $sent             = intval($this->getCountSentEmails());
        $unsent        = intval($this->getCountUnsentEmails());
        $day              = $this->getStatsByDay();
        
         
        return array("unopened"=>$unopened,"opened"=>$opened,"sent"=>$sent,"unsent"=>$unsent,"day"=>$day);
    }
    
    public function getRecipients() {
        $result       = array();
        $database = FW_Database::getInstance();
        $prefix       = $database->getPrefix();
        
        $query = "SELECT DISTINCT(name),email FROM {$prefix}mailing_list_suscriber WHERE id IN (
            SELECT id_suscriber FROM {$prefix}email WHERE id_campaign='{$this->id}' 
        )";
        $database->query($query);
        while($row=$database->fetchAssoc()) {
            $result []= $row;           
        }
        return $result;         
     } 
    
      /*  END Stats */
        
        
    };
?>