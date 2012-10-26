<?php
    class guestbook_entry extends FW_ActiveRecord_Model {
        
        protected $id;       
        protected $username;
        protected $title;
        protected $contents;
        protected $is_highlighted;
        protected $status;
        protected $created_at;
        protected $ip;
        
        public function getId() {
            return $this->id;
        }
        
        public function getIP() {
            return $this->ip;
        }
        
        public function getUsername(){
            $username = strtolower($this->username);
            $username = explode(' ',$username);
            if (count($username)>1) {
                $username = implode(' ',$username);
            }
            else {
                $username = $username[0];
                $user             = user::find("username='{$username}' ");
                if ($user->hasResult()) {
                    return $user->first()->getDisplayName();
                }
            }
            return ucwords($username);
        }
        
        public function getTitle() {
            return html_entity_decode($this->title,ENT_QUOTES,"UTF-8");
        }
        
        public function getContents() {
            return html_entity_decode($this->contents,ENT_QUOTES,"UTF-8");
        }
        
        public function getStatus() {
            return intval($this->status);
        }
        
        public function getStatusAsText() {
            if ($this->getStatus()===1) {
                return _("Visible");
            }
            return _("No visible");
        }
        
        public function isHighLighted() {
            return (intval($this->is_highlighted)===1);
        }
        
        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }
        
        public function getSlug() {
            // aquest model NO enmagatzema cap slug a la base de dades,el genera ell.
            return FW_Util_Url::seoUrl(strip_tags($this->getTitle()));
        }
         
        
    };
?>