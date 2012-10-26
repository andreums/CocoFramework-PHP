<?php
class SMS_Storage extends FW_Singleton {

    public function getInbox($account,$username,$readed,$order) {
        $conditions = array();
        $orders     = array();

        if ($order===true) {
            $orders []= array (
                "column" => "date",
                "type" 	 => "DESC"
            );
        }
        else {
            $orders []= array (
                "column" => "date",
                "type" 	 => "ASC"
            );
        }


        $messages   = sms_message::find($conditions,$orders);
        return $messages;

    }



    /*public function getInbox($account="default",$username="",$type="all") {


        $messages = array();

        if ($this->existsAccount($account)) {



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
						$messages = sms_message::findByColumnsOperator($conditions,$orders);
        }
        return $messages;
    }*/


};
?>