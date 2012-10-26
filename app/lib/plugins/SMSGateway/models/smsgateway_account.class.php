<?php
    class smsgateway_account extends FW_ActiveRecord_Model {

        protected $id;
        protected $name;
        protected $description;
        protected $provider;
        protected $username;
        protected $password;
        protected $sender;
        protected $active;
        protected $creator;


        static $belongs_to = array (
            array(
                "property" => "creator",
                "table"    => "user",
                "srcColumn"=> "creator",
                "dstColumn"=> "username",
                "update"   => "cascade",
                "delete"   => "cascade"
            )
        );

    };
?>