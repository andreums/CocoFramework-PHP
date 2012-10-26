<?php
    class private_message extends FW_ActiveRecord_Model {

        protected $id;
        protected $sender;
        protected $receiver;
        protected $subject;
        protected $content;
        protected $created_at;
        protected $readed;

        public static $belongs_to = array (
            array(
        		"property" => "sender",
            	"table"    => "user",
             	"srcColumn"=> "sender",
    			"dstColumn"=> "username",
            	"update"   => "cascade",
            	"delete"   => "cascade"
        	)
        );


        public static $has_one = array (
            array(
        		"property" => "receiver",
            	"table"    => "user",
             	"srcColumn"=> "receiver",
    			"dstColumn"=> "username",
            	"update"   => "cascade",
            	"delete"   => "cascade"
        	)
        );


        public function getSubject() {
            return $this->_filter->encodeUTF8($this->subject);
        }

        public function getDate() {
            return date("d/m/Y H:i:s",strtotime($this->created_at));
        }

        public function getSenderDisplayName() {
            return $this->_filter->encodeUTF8($this->sender->first()->getDisplayName());
        }
    };
?>