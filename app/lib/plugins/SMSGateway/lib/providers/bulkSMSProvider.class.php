<?php
class bulkSMSProvider extends FW_Plugin_Library implements ISMSProvider {


    private $_configuration;

    protected $_providerConfiguration;

    private $_accountName;
    private $_username;
    private $_password;
    private $_sender;
    private $_host;


    private $_credits;
    private $_lastRetrievedId;
    private $_response;

    private $_receivedMessages;
    private $_repliable;
    private $_lastResponse;


    public function configure(array $parameters=array()) {
        $configuration = null;
        if (!empty($parameters) && isset($parameters["configuration"])) {
            $configuration = $parameters["configuration"];
        }
        if ($configuration!=null) {
            $this->_host        = "http://bulksms.com.es";
            $this->_accountName = $configuration->getName();
            $this->_username    = $configuration->getUsername();
            $this->_password    = $configuration->getPassword();
            $this->_sender      = $configuration->getSender();
            //$this->_lastRetrievedId = $this->_getLastMessageId();
            //$this->getCredit();
            $this->_configuration = $configuration;
            return true;
        }
        return null;
    }

    protected function _getLastMessageId() {
        $accountName = $this->_configuration->getName();
        $name = "{$accountName}_last_retreived_id";
        $lastId = $this->getDBOption($name);
        return $lastId;
    }

    protected function _setLastMessageId($id) {
        $accountName = $this->_configuration->getName();
        $this->setDBOption("{$accountName}_last_retreived_id",$id);
    }


    /**
     * Gets the remaining credits of the
     * BulkSMS account
     *
     * @return mixed
     */
    public function getCredit() {
        $url = $this->_host."/eapi/user/get_credits/1/1.1";
        $postContent = array (
                "username" => $this->_username,
                "password" => $this->_password
        );
        $parameters  = new FW_Container_Parameter();
        $parameters->endpoint = $url;
        $parameters->method   = "POST";
        $parameters->data     = $postContent;
        $parameters->type     = "";

        $client = new FW_Rest_Client($parameters);
        if ($client->exec()) {
            $this->_response =  $client->getResponse()->getBody();
            if (!empty($this->_response)) {
                $responseExploded = explode("|",$this->_response);
                $errorCode = intval($responseExploded[0]);
                if ($errorCode==0) {
                    $this->_credits = floatval($responseExploded[1]);
                    return $this->_credits;
                }
                else {
                    trigger_error("PLUGIN | BulkSMS Plugin Error: There was an error retreving the credits {$this->_response}",E_USER_ERROR);
                    return null;
                }
            }
        }
        else {
            return false;
        }

    }

    public function sendMessage($telephone,$text,$repliable=false,$in_reply_to="") {

        $text          = $this->_encodeText($text);
        $creditsNeeded = $this->_quoute($telephone,$text);
        if ( ($this->_credits-$creditsNeeded)>0 ) {

            $url = $this->_host."/eapi/submission/send_sms/2/2.0";

            if ($repliable) {
                $postContent = array (
                    	"username" => $this->_username,
                    	"password" => $this->_password,
                    	"msisdn"   => $telephone,
                    	"message"  => $text,
                    	"repliable"   => 1
                );
            }
            else {
                $postContent = array (
                    	"username" => $this->_username,
                    	"password" => $this->_password,
                    	"msisdn"   => $telephone,
                    	"message"  => $text,
                    	"sender"   => $this->_sender
                );

            }

            $parameters  = new FW_Container_Parameter();
            $parameters->endpoint = $url;
            $parameters->method   = "POST";
            $parameters->data     = $postContent;
            $parameters->type     = "";

            $client = new FW_Rest_Client($parameters);
            if ($client->exec()) {
                $this->_response =  $client->getResponse()->getBody();
                if (!empty($this->_response)) {
                    $responseExploded = explode("|",$this->_response);
                    $errorCode = intval($responseExploded[0]);
                    if ($errorCode==0) {
                        $msg = new sms_message();
                        $msg->id        = intval($responseExploded[2]);
                        $msg->account   = self::$_configuration->name;
                        if (Authentication::loggedUser()==null) {
                            $msg->username  = "cron";
                        }
                        else {
                            $msg->username  = Authentication::loggedUser();
                        }
                        $msg->telephone    = $telephone;
                        $msg->text         = $text;
                        $msg->date         = date("Y-m-d H:i:s");
                        $msg->status       = 0;
                        $msg->type         = 0;
                        $msg->readed       = 0;
                        $msg->batch_id     = intval($responseExploded[2]);
                        $msg->is_delivered = 0;
                        if ($in_reply_to!="") {
                            $msg->in_reply_to = $in_reply_to;
                        }
                        $msg->save();
                        return intval($responseExploded[2]);
                    }
                    if ($errorCode==25) {
                        trigger_error("PLUGIN | BulkSMS Plugin Error: There was an error sending SMS, you don't have enough credits to send this SMS {$this->_lastResponse}",E_USER_ERROR);
                        return 25;
                    }
                    if ($errorCode==27) {
                        trigger_error("PLUGIN | BulkSMS Plugin Error: There was an error sending SMS, you have exceeded your daily quota to send this SMS {$this->_lastResponse}",E_USER_ERROR);
                        return 27;
                    }
                }
            }
        }
        else {
            return -1;
        }
    }

    private function _quoute($telephone,$text) {
        $url = $this->_host."/eapi/submission/quote_sms/2/2.0";

        $postContent = array (
            	"username" => $this->_username,
                "password" => $this->_password,
                "msisdn"   => $telephone,
                "message"  => utf8_decode($text),
        );

        $parameters  = new FW_Container_Parameter();
        $parameters->endpoint = $url;
        $parameters->method   = "POST";
        $parameters->data     = $postContent;
        $parameters->type     = "";

        $client = new FW_Rest_Client($parameters);
        if ($client->exec()) {
            $this->_response =  $client->getResponse()->getBody();
            if (!empty($this->_response)) {
                $responseExploded = explode("|",$this->_response);
                $errorCode = intval($responseExploded[0]);
                if ($errorCode===0) {
                    $neededCredits = floatval($responseExploded[2]);
                    return $neededCredits;
                }
                else {
                    trigger_error("PLUGIN | BulkSMS Plugin Error: There was an error quoting form credits {$this->_response}",E_USER_ERROR);
                    return null;
                }

            }
        }
    }

    public function receiveMessages() {

        $id          = $this->_getLastMessageId();
        $msgs        = array();
        $url         = $this->_host."/eapi/reception/get_inbox/1/1.0";
        if ($id===null) {
            $id = 0;
        }
        $postContent = array (
            "username"          => $this->_username,
            "password"          => $this->_password,
            "last_retrieved_id" => $id
        );
        $parameters  = new FW_Container_Parameter();
        $parameters->endpoint = $url;
        $parameters->method   = "POST";
        $parameters->data     = $postContent;
        $parameters->type     = "";

        $client = new FW_Rest_Client($parameters);
        if ($client->exec()) {
            $this->_response =  $client->getResponse()->getBody();
            if (!empty($this->_response)) {
                $numMessages = 0;
                $response = $this->_response;
                $responseLines = explode("\n",$response);
                if (count($responseLines)>0) {
                    $statusLine = explode("|",$responseLines[0]);
                    $error = intval($statusLine[0]);

                    if ( ($error==0) && (count($statusLine)>1) ) {

                        $numMessages = intval($statusLine[2]);

                        for ($i=0;$i<$numMessages;$i++) {
                            $message = explode("|",$responseLines[$i+2]);

                            $msg = new sms_message();
                            $msg->id        = $message[0];
                            $msg->account   = $this->_configuration->getName();
                            if (FW_Authentication::getInstance()->getUser()==null) {
                                $msg->username  = "cron";
                            }
                            else {
                                $msg->username  = FW_Authentication::getInstance()->getUser();
                            }
                            $msg->telephone = $message[1];
                            $msg->text      = $message[2];
                            $msg->date      = $message[3];
                            $msg->status    = 999;
                            $msg->type      = 1;
                            $msg->readed    = 0;
                            $msg->batch_id  = $message[5];
                            $msg->delivered = 0;
                            $msg->in_reply_to = 0;
                            $msg->failed_to_deliver = 0;

                            $msg->save();
                            $msgs[] = $msg;
                        }

                        if ($i!=0) {
                            $this->_setLastMessageId($msg->id);
                        }
                        return $msgs;
                    }
                    else {
                        trigger_error("PLUGIN | BulkSMS Plugin Error: There was an error retreving the inbox {$this->_response}",E_USER_ERROR);
                        return false;
                    }
                }
            }
        }
        return false;
    }


    public function checkNewMessages() {
        $url = $this->_host."/eapi/reception/get_inbox/1/1.0";
        $postContent = array (
                "username" => $this->_username,
                "password" => $this->_password,
                "last_retrieved_id" => $this->_getLastMessageId()
        );

        $this->_response =  $this->_httpConnection($url,"POST",$postContent);


        if (!empty($this->_response)) {
            $numMessages = 0;
            $response = $this->_response;
            $responseLines = explode("\n",$response);
            if (count($responseLines)>0) {
                $statusLine = explode("|",$responseLines[0]);
                $error = intval($statusLine[0]);
                if ( ($error==0) && (count($statusLine)>1) ) {
                    $numMessages = intval($statusLine[2]);
                    return $numMessages;
                }
                return 0;
            }
            return 0;
        }
        return 0;
    }


    /**
     * Updates all the sms status using the
     * E-API get_report function
     *
     * @return void
     */
    public function updateSMSStatus() {
        return true;
        $conditions = array(
        array(
                	"name"    =>"account",
                	"operator"=>"=",
                	"value"   =>$this->_accountName
        ),
        array ("condition"=>"AND"),
        array(
                	"name"    =>"type",
                	"operator"=>"=",
                	"value"   =>"0"
                	),
                	array ("condition"=>"AND"),
                	array(
                	"name"    =>"delivered",
                	"operator"=>"=",
                	"value"   =>"0"
                	)
                	);

                	$orders = array (
                	array (
                    "column"  =>"id",
                	"type" 	  =>"ASC"
                	)
                	);
                	$sms          = sms_message::find($conditions,$orders);
                	$url          = $this->_host."/eapi/status_reports/get_report/2/2.0";
                	$parameters  = new FW_Container_Parameter();
                	$parameters->endpoint = $url;
                	$parameters->method   = "POST";
                	$parameters->data     = $postContent;
                	$parameters->type     = "";

                	$client = new FW_Rest_Client($parameters);
                	$requestGroup = new RollingCurl(array($this,"_updateSMSStatusCallback"));
                	$requestGroup->window_size = count($sms);
                	if (count($sms)) {
                	    foreach ($sms as $msg) {
                	        $postContent = array (
                    	"username" => $this->_username,
            			"password" => $this->_password,
                    	"batch_id" => $msg->batch_id
                	        );
                	        $request = new RollingCurlRequest($url,"POST",$postContent);
                	        $requestGroup->add($request);
                	    }
                	    $requestGroup->execute();
                	}
    }

    public function _updateSMSStatusCallback($response, $info, $request) {
        if ($info["http_code"]=="200") {
            $idSMS         = ($request->post_data["batch_id"]);
            $responseLines = explode("\n",$response);
            if (count($responseLines)>0) {
                $statusLine= explode("|",$responseLines[0]);
                $error     = intval($statusLine[0]);
                if ( ($error==0) && (count($statusLine)>1) ) {
                    $reportLine = explode("|",$responseLines[2]);
                    if (count($reportLine)>=2) {
                        $telephone = $reportLine[0];
                        $status    = $reportLine[1];
                        $msg = sms_message::findByColumn("id",$idSMS,array(array("column"=>"id","type"=>"ASC")));
                        $msg = $msg->first();
                        $msg->status = $status;
                        switch (intval($status)) {
                            case 11:
                                $msg->delivered = 1;
                                break;

                            case 52:
                            case 53:
                            case 54:
                                $msg->failed_to_deliver = 1;
                                break;

                            default:
                                $msg->delivered = 0;
                                break;
                        };
                        $msg->save();
                    }
                }
            }
        }
        else {
            trigger_error("PLUGIN | BulkSMS Plugin Error: There was an error retreving the credits {$this->_lastResponse}",E_USER_ERROR);
            return null;
        }
    }

    private function _encodeText($body) {

        $body = str_replace('á','a',$body);
        $body = str_replace('í','i',$body);
        $body = str_replace('ó','o',$body);
        $body = str_replace('ú','u',$body);
        $body = str_replace('ç','c',$body);



        $special_chrs = array(
    			'Δ'=>'0xD0', 'Φ'=>'0xDE', 'Γ'=>'0xAC', 'Λ'=>'0xC2', 'Ω'=>'0xDB',
    			'Π'=>'0xBA', 'Ψ'=>'0xDD', 'Σ'=>'0xCA', 'Θ'=>'0xD4', 'Ξ'=>'0xB1',
    			'¡'=>'0xA1', '£'=>'0xA3', '¤'=>'0xA4', '¥'=>'0xA5', '§'=>'0xA7',
    			'¿'=>'0xBF', 'Ä'=>'0xC4', 'Å'=>'0xC5', 'Æ'=>'0xC6', 'Ç'=>'0xC7',
    			'É'=>'0xC9', 'Ñ'=>'0xD1', 'Ö'=>'0xD6', 'Ø'=>'0xD8', 'Ü'=>'0xDC',
    			'ß'=>'0xDF', 'à'=>'0xE0', 'ä'=>'0xE4', 'å'=>'0xE5', 'æ'=>'0xE6',
    			'è'=>'0xE8', 'é'=>'0xE9', 'ì'=>'0xEC', 'ñ'=>'0xF1', 'ò'=>'0xF2',
    			'ö'=>'0xF6', 'ø'=>'0xF8', 'ù'=>'0xF9', 'ü'=>'0xFC', 'í'=>'i',
                'ó'=>'0'   , 'ú'=>'u'   , 'ç'=>'0xE7'
                );

                $ret_msg = '';
                mb_detect_encoding($body, "UTF-8") == "UTF-8"? null: $body = utf8_encode($body);
                for ( $i = 0; $i < mb_strlen( $body, 'UTF-8' ); $i++ ) {
                    $c = mb_substr( $body, $i, 1, 'UTF-8' );
                    if( isset( $special_chrs[ $c ] ) ) {
                        $ret_msg .= chr( $special_chrs[ $c ] );
                    }
                    else {
                        $ret_msg .= $c;
                    }
                }
                return $ret_msg;
    }


};
?>