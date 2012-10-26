<?php
    class contact extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $name;
        protected $email;
        protected $telephone;
        protected $subject;
        protected $message;
        protected $created_at;
        protected $readed;
        protected $ip;
        
        public function getReplies() {
            $replies = contact_reply::find("id_contact='{$this->id}' ");
            return $replies;
        }
        
        public function getSubject() {
            return html_entity_decode($this->subject,ENT_QUOTES,"UTF-8");            
        }
        
        public function getMessage() {
            return html_entity_decode($this->message,ENT_QUOTES,"UTF-8");            
        }
        
        public function getName() {
            return html_entity_decode($this->name,ENT_QUOTES,"UTF-8");            
        }
        
        public function getNameJSON() {
            return $this->name;            
        }
        
        public function getSubjectJSON() {
            return $this->subject;
        }
        
        public function getEmail() {
            return html_entity_decode($this->email,ENT_QUOTES,"UTF-8");
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }
        
        public function getIp() {
            return $this->ip;
        }
        
        public function getStatusAsText() {
            $status = "";
            if (intval($this->readed)===0) {
                $status =  _("No le&iacute;do");
            }
            else if  (intval($this->readed)===1) {
                $status =  _("Le&iacute;do");
            }
            else if (intval($this->readed)===2) {
                $status = _("Respondido");
            }
            return $status;
        }
        
        public function getStatus() {
            return intval($this->readed);
        }
        
        public function markAsReaded() {
            if (intval($this->readed)!==2) {
                $this->readed = 1;
            }
            $this->save();
        }
        
        public function markAsReplied() {
            $this->readed = 2;            
            $this->save();
        }
        
        public function getId() {
            return $this->id;
        }
        

    };
?>    
