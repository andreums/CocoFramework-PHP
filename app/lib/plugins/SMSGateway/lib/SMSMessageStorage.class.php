<?php
    class SMSMessageStorage {
        
        private $_accountManager;
        
        private static $_instance;
        private static $_dataBase;

        public function __construct() {
            $this->_accountManager = new Account_Manager();            
            self::$_dataBase = FW_Database::getInstance();
        }

        public static function getInstance()   {
            if (!(self::$_instance instanceof self))   {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function getMessageById($id) {
            if (Authentication::loggedRole()=="root") {
                $message = sms_message::findByColumn("id",$id);
                if ($message!=null) {
                    return $message[0];
                }
            }
            else {
                return null;
            }
            return null;

        }

        public function getMessageByIdAndAccount($id) {
            if (Authentication::loggedRole()=="root") {
                $conditions = array(
                    array (
                    	"name"     => "type",
						"operator" => "=",
						"value"    => "1"
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => self::$_configuration->name
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "id",
						"operator" => "=",
						"value"    => $id
			        )
		        );

                $message = sms_message::find($conditions);
                if ($message!=null) {
                    return $message[0];
                }
            }
            else {
                $conditions = array(
                    array (
                    	"name"     => "type",
						"operator" => "=",
						"value"    => "1"
			        ), 
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => self::$_configuration->name
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "id",
						"operator" => "=",
						"value"    => $id
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "username",
						"operator" => "=",
						"value"    => Authentication::loggedUser()
			        )
		        );
		        $message = sms_message::find($conditions);
		        if ($message!=null) {
		            return $message[0];
		        }
            }
            return null;
        }

        public function deleteAccountMessages($name="") {
            if (empty($name)) {
                return false;
            }
            else {
                $conditions = array(
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => $name
			        )
		        );
		        $messages = sms_message::find($conditions);
		        if ($messages!=null) {
		            foreach ($messages as $message) {
		                if (!$message->delete()) {
		                    return false;
		                }
		            }
		            return true;
		        }
		        return false;
            }
        }

        public function getInbox($account="default",$username="",$type="all") {


            $messages = array();
            if ($this->existsAccount($account)) {

                $orders =  array (
		            array (
		        		"column" => "date",
		        		"type"   => "DESC"
		            )
		        );

                $conditions = array(
                    array (
                    	"name"     => "type",
						"operator" => "=",
						"value"    => "1"
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => $account
			        )
		        );

		        if (!empty($username)) {
		            $conditions []= array( "name"=>"username","operator"=>"=","value"=>$username);
		        }

		        switch ($type) {
		            default:
                    case "all":

                    break;

                    case "readed":
                        $conditions []= array ("condition"=>"AND");
                        $conditions []= array ("name"=>"readed","operator" => "=","value"=>"1");
                    break;

                    case "unreaded":
                        $conditions []= array ("condition"=>"AND");
                        $conditions []= array ("name"=>"readed","operator" => "=","value"=>"0");
                    break;
		        }
		        $messages = sms_message::find($conditions,$orders);
            }
            return $messages;
        }

        public function getOutbox($account="default",$username="",$type="all") {

            $messages = array();

            if ($this->existsAccount($account)) {

                $orders =  array (
		            array (
		        		"column"    => "date",
		        		"type" => "DESC"
		            )
		        );

                $conditions = array(
                    array (
                    	"name"     => "type",
						"operator" => "=",
						"value"    => "0"
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => $account
			        )
		        );

		        if (!empty($username)) {
		            $conditions []= array( "name"=>"username","operator"=>"=","value"=>$username);
		        }

		        switch ($type) {
		            default:
                    case "all":

                    break;

                    case "delivered":
                        $conditions []= array ("condition"=>"AND");
                        $conditions []= array ("name"=>"status","operator" => "IN","value"=>"(10,11)");
                    break;

                    case "undelivered":
                        $conditions []= array ("condition"=>"AND");
                        $conditions []= array ("name"=>"status","operator" => "IN","value"=>"(0,31,40,50,51,52,53,54)");
                    break;

                    case "unknown":
                        $conditions []= array ("condition"=>"AND");
                        $conditions []= array ("name"=>"status","operator" => "IN","value"=>"(0,10,11,31,40,50,51,52,53,54)");
                    break;
		        }
		        $messages = sms_message::find($conditions,$orders);
            }
            return $messages;
        }

        public function getAllMessagesByAccount($name="default") {
            if ($this->existsAccount($name)) {

                $messages = array( "inbox"=>array() , "outbox"=>array() );

                $orders =  array (
		            array (
		        		"column"    => "date",
		        		"type" => "DESC"
		            )
		        );

                $inboxConditions = array(
                    array (
                    	"name"     => "type",
						"operator" => "=",
						"value"    => "1"
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => $name
			        )
		        );

		        $outboxConditions = array(
                    array (
                    	"name"     => "type",
						"operator" => "=",
						"value"    => "0"
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => $name
			        )
		        );

		        $inbox = sms_message::find($inboxConditions,$orders);
		        $outbox = sms_message::find($outboxConditions,$orders);

		        $messages["inbox"]  = $inbox;
		        $messages["outbox"] = $outbox;


                return $messages;
            }
            return null;
        }

        public function getMessagesCountByAccount($name="default") {
            if ($this->existsAccount($name)) {

                $messages = array( "inbox"=>array() , "outbox"=>array() );

                $orders =  array (
		            array (
		        		"column"    => "date",
		        		"type" => "DESC"
		            )
		        );

                $inboxConditions = array(
                    array (
                    	"name"     => "type",
						"operator" => "=",
						"value"    => "1"
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => $name
			        )
		        );

		        $outboxConditions = array(
                    array (
                    	"name"     => "type",
						"operator" => "=",
						"value"    => "0"
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => $name
			        )
		        );

		        $inbox  = sms_message::count("id",$inboxConditions);
		        $outbox = sms_message::count("id",$outboxConditions);

		        $messages["inbox"]  = $inbox;
		        $messages["outbox"] = $outbox;


                return $messages;
            }
            return null;
        }

        public function markAsReaded($id,$account="default",$username="") {

            $action = 999;
            if ($this->existsAccount($account)) {
                $conditions = array(
                    array (
                    	"name"     => "type",
						"operator" => "IN",
						"value"    => "(0,1)"
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => $account
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "id",
						"operator" => "=",
						"value"    => $id
			        )
		        );

		        if (!empty($username)) {
		            $conditions []= array ("condition"=>"AND");
		            $conditions []= array ("name"=>"username","operator" => "=","value"=>$username);
		        }

		        $message = sms_message::find($conditions);
		        if ($message!=null) {
                    $message = $message[0];
                    if (intval($message->readed)==0 || $message->readed==NULL) {
                        $message->readed = 1;
                        $action = 1;
                    }
                    else {
                        $message->readed = 0;
                        $action = 0;
                    }
                    if ($message->validateData() && $message->save() ) {
                        return $action;
                    }
                    return -1;
		        }
		        else {
		            return -1;
		        }
            }
            return -1;
        }

        public function deleteMessage($id,$account="default",$username="") {
            if ($this->existsAccount($account)) {
                $conditions = array(
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => $account
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "id",
						"operator" => "=",
						"value"    => $id
			        )
		        );

		        if (!empty($username)) {
		            $conditions []= array ("condition"=>"AND");
		            $conditions []= array ("name"=>"username","operator" => "=","value"=>$username);
		        }

		        $message = sms_message::find($conditions);
		        if ($message!=null) {
		            $message = $message[0];
		            return $message->delete();
		        }
		        return false;
            }
            return false;
        }

        public function getMessage($id,$account="default",$username="") {
            if ($this->existsAccount($account)) {
                $conditions = array(
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => $account
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "id",
						"operator" => "=",
						"value"    => $id
			        )
		        );

		        if (!empty($username)) {
		            $conditions []= array ("condition"=>"AND");
		            $conditions []= array ("name"=>"username","operator" => "=","value"=>$username);
		        }

		        $message = sms_message::find($conditions);
		        if ($message!=null) {
		            $message = $message[0];
		            return $message;
		        }
		        return null;
            }
            return null;
        }

        public function getMessagesForPoll($account="default",$pollId="") {

            $messages = array();

            if ($this->existsAccount($account)) {

                $orders =  array (
		            array (
		        		"column" => "date",
		        		"type"   => "DESC"
		            )
		        );

                $conditions = array(
                    array (
                    	"name"     => "type",
						"operator" => "=",
						"value"    => "1"
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => $account
			        ),
			        array ("condition"=>"AND"),
			        array  (
			            "name"	   => "UPPER(text)",
			            "operator" => "LIKE",
			            "value"	   => "{$pollId}%"
			        )
		        );

		        $messages = sms_message::find($conditions,$orders);
            }
            return $messages;
        }

        public function getMessagesForTelephone($account,$telephone,$username="") {
            $messages = array( "inbox"=>array() , "outbox"=>array() );
            $orders   =  array (
                array (
                	"column"    => "date",
		        	"type" => "DESC"
	            )
            );
            if ($this->existsAccount($account)) {


                $inboxConditions = array(
                    array (
                    	"name"     => "type",
						"operator" => "=",
						"value"    => "1"
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => $account
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "telephone",
						"operator" => "=",
						"value"    => $telephone
			        )
		        );

		        $outboxConditions = array(
                    array (
                    	"name"     => "type",
						"operator" => "=",
						"value"    => "0"
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "account",
						"operator" => "=",
						"value"    => $account
			        ),
			        array ("condition"=>"AND"),
			        array (
        				"name"     => "telephone",
						"operator" => "=",
						"value"    => $telephone
			        )
		        );

		        if ($username!="") {

		            $usernameCond =  array (
        				"name"     => "username",
						"operator" => "=",
						"value"    => $username
			        );

			        $inboxConditions  []= array ("condition"=>"AND");
			        $inboxConditions  []= $usernameCond;
			        $outboxConditions []= array ("condition"=>"AND");
			        $outboxConditions []= $usernameCond;


		        }

		        $inbox              = sms_message::find($inboxConditions,$orders);
		        $outbox             = sms_message::find($outboxConditions,$orders);
		        $messages["inbox"]  = $inbox;
		        $messages["outbox"] = $outbox;

                return $messages;
            }
            return null;
        }



        public function getOutboxForUser($username="") {

            $messages = array();

            $orders =  array (
	            array (
	        		"column" => "date",
	        		"type"   => "DESC"
	            )
	        );

            $conditions = array(
                array (
                	"name"     => "type",
					"operator" => "=",
					"value"    => "0"
		        )
            );


	        if (!empty($username)) {
	            $conditions []= array("condition"=>"AND");
	            $conditions []= array( "name"=>"username","operator"=>"=","value"=>$username);
	        }

	        $messages = sms_message::find($conditions,$orders);
            return $messages;
        }


        public function getInboxForUser($username="") {

            $messages = array();

            $orders =  array (
	            array (
	        		"column" => "date",
	        		"type"   => "DESC"
	            )
	        );

            $conditions = array(
                array (
                	"name"     => "type",
					"operator" => "=",
					"value"    => "1"
		        )
            );


	        if (!empty($username)) {
	            $conditions []= array("condition"=>"AND");
	            $conditions []= array( "name"=>"username","operator"=>"=","value"=>$username);
	        }

	        $messages = sms_message::find($conditions,$orders);
            return $messages;
        }
        
        private function existsAccount($name) {
            return $this->_accountManager->hasAccount($name);
        }


    };
?>