<?php
    class notification extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $user_from;
        protected $user_to;
        protected $subject;
        protected $body;
        protected $status;
        protected $created_at;      
        protected $from_email;
        protected $to_email;
        protected $from_name;
        protected $to_name;
        
        public function read() {
            $this->status= 1;
            return $this->save();
        }
        
        public function getId() {
            return $this->id;            
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }
        
        public function getSubject() {
            return utf8_encode($this->subject);
        }
        
        public function getFrom() {
            // TODO: Obtener el nombre del usuario
            return $this->user_from;
        }
        
        public function getTo() {
            // TODO: Obtener el nombre del usuario
            return $this->user_to;
        }
        
        public function getFromWithAvatar() {
            $name         = "";
            $avatar        = "";
            $username = $this->user_from;
            if (strlen($this->user_from)>0) {
                $user           = user::find(" username='{$username}' ");
                if ($user->hasResult()) {
                    $user = $user->first();
                    $name =  $user->display_name;
                    $avatar = $user->getAvatar();
                }
                else {
                    $name = $this->user_from;
                }                
                if (strlen($avatar)>0) {
                        $name = "<p class=\"span-6\"><img class=\"span-2 column\" src=\"{$avatar}\" style=\"width:20px !important;\"  /><span class=\"span-4 column last\">{$name}</span></p>";
                }            
                return $name;
            }
            else {
                return $this->from_name;                
            }
            
        }
        
        public function getFromName() {
            $name         = "";
            $avatar        = "";
            $username = $this->user_from;
            if (strlen($this->user_from)>0) {
                $user           = user::find(" username='{$username}' ");
                if ($user->hasResult()) {
                    $user = $user->first();
                    $name =  $user->display_name;
                    $avatar = $user->getAvatar();
                }
                else {
                    $name = $this->user_from;
                }                
                if (strlen($avatar)>0) {
                        $name = "&nbsp;&nbsp;{$name}&nbsp;&nbsp;<img src=\"{$avatar}\" style=\"width:20px !important;\"  />&nbsp;&nbsp;";
                }            
                return $name;
            }
            else {
                return $this->from_name;                
            }
            
        }
        
        public function getBody() {            
            return html_entity_decode($this->body,ENT_QUOTES,"UTF-8");                               
        }
        
        public function markAsReaded() {
            if (intval($this->status)===0) {
                $this->status = 1;
                return $this->save();
            }                
        }
    };
?>