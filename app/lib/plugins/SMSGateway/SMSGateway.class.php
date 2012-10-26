<?php
// BulkSMS plugin
class SMSGateway extends FW_Plugin_Base {


    private   $_configuration;
    private   $_provider;
    private   $_storage;




    /**
     * @param array $parameters
     */
    protected function _initialize(array $parameters=null) {
        $this->useConfiguration("default");
        $this->_provider = $this->_loadProvider();
        $this->_storage = SMSMessageStorage::getInstance();
    }

    /**
     * @param array $parameters
     */
    protected function _configure(array $parameters=null) {

    }

    public function install(array $arguments=array()) {

    }

    public function uninstall(array $arguments=array()) {

    }


    /* BEGIN Account configuration */
    public function accounts() {
        $flash    = FW_Flash::getInstance();
        $manager  = new Account_Manager();
        $this->setSlot("flash","flash",array("messages"=>$flash->getFlashMessages()));
        $accounts = $manager->getAccounts();
        $this->setSlot("content","admin/accounts/main",array("accounts"=>$accounts));
        $this->renderLayout("default");

        $data     = date("d/m/Y H:i:s");
        if (intval(date("i"))%2===0) {
            $flash->addMessage("<h2>Son las {$data}</h2>");
        }
    }

    public function editAccount($account) {
        $manager  = new Account_Manager();
        if ($this->request()->isGet()) {
            if ($manager->hasAccount($account)) {
                $account  = $manager->getAccount($account);
                $this->setSlot("content","admin/accounts/edit",array("account"=>$account));
            }
            else {
                $this->setSlot("content","admin/accounts/notFound");
            }
        }
        else {
            if ($manager->hasAccount($account)) {
                $data   = $this->request()->post();
                $manager->updateAccount($account,$data);
            }
            var_dump($_POST);
            exit;
        }
        $this->renderLayout("default");
    }

    public function createAccount() {
        $flash    = FW_Flash::getInstance();
        $this->setSlot("flash","flash",array("messages"=>$flash->getFlashMessages()));

        $post = $this->request()->isPost();
        if ($post) {
            $post = "EsPost!";
        }
        else {
            $post = "EsGet!";
        }
        $flash->addMessage("<h2>Son las {$post}</h2>");
        $this->setSlot("content","admin/accounts/create");
        $this->renderLayout("default");
    }

    public function configureAccounts() {

    }
    /* END Account configuration */


    /*
     * Sets the options of the plugin
     *
     * @access protected
     * @return void
     */
    protected function _setOptions($options) {
        $this->_options = $options;
        return;
    }

    public function useConfiguration($name="default") {

        $this->_configuration =  $this->_getConfiguration($name);
        $this->_provider      = $this->_loadProvider();
    }

    protected function _getConfiguration($name) {
        $account = null;
        $manager = new Account_Manager();
        if ($manager->hasAccount($name)) {
            $account = $manager->getAccount($name);
        }
        return $account;
    }

    private final function _loadProvider() {
        $providerName  = $this->_configuration->getProvider();
        $providerClass = "{$providerName}Provider";
        $provider = new $providerClass($this->_configuration);
        $provider->configure(array("configuration"=>$this->_configuration));
        return $provider;
    }



    public function getAvailableProviders() {
        $providers = array();
        $files = scandir($this->getPluginPath().DS."providers");
        for ($i=0;$i<count($files);$i++) {
            if ($files[$i]=="." || $files[$i]==".." || $files[$i][0]=="." || !strpos($files[$i],".class.php") ) {
                continue;
            }
            else {
                $pos = strpos($files[$i],"Provider");
                if ($pos!==false) {
                    $providerName = substr($files[$i],0,$pos);
                    $providers []=($providerName);
                }
            }
        }
        return $providers;
    }

    public function getStats() {
        $stats = array();
        $account = smsgateway_account::findByColumnsOperator(array( array("name"=>"name","operator"=>"=","value"=>$name)));
        if ($account!=null) {
            $account      = $account[0];
            $acName       = $account->name;
            $acProvider   = $account->provider;


            $inboxCount    = sms_message::count("id",array (array("name"=> "account","operator" => "=","value"=>"$acName"),array ("name"=>"type","operator"=>"=","value"=>"1")));
            $outboxCount   = sms_message::count("id",array(array("name"=> "account","operator" => "=","value"=>"$acName"),array ("name"=>"type","operator"=>"=","value"=>"0")));
            $outboxCount   = sms_message::count("id",array (array("name"=> "account","operator" => "=","value"=>"$acName"),array ("name"=>"type","operator"=>"=","value"=>"0"),array ("name"=>"readed","operator"=>"=","value"=>"0")));
            $total = ($inboxCount[0]+$outboxCount[0]);
            $stats []= array("account"=>$acName,"provider"=>$acProvider,"inbox"=>$inboxCount[0],"outbox"=>$outboxCount[0],"unreaded"=>$unreaded[0],"total"=>$total);

        }
        return $stats;

    }

    public function getStatsForAccount($name="default") {
        $stats = array();
        $account = smsgateway_account::findByColumnsOperator(array( array("name"=>"name","operator"=>"=","value"=>$name)));
        if ($account!=null) {
            $account      = $account[0];
            $acName       = $account->name;
            $acProvider   = $account->provider;


            $inboxCount    = sms_message::count("id",array (array("name"=> "account","operator" => "=","value"=>"$acName"),array ("name"=>"type","operator"=>"=","value"=>"1")));
            $outboxCount   = sms_message::count("id",array(array("name"=> "account","operator" => "=","value"=>"$acName"),array ("name"=>"type","operator"=>"=","value"=>"0")));
            $unreadedCount   = sms_message::count("id",array (array("name"=> "account","operator" => "=","value"=>"$acName"),array ("name"=>"type","operator"=>"=","value"=>"1"),array ("name"=>"readed","operator"=>"=","value"=>"0")));
            $total = ($inboxCount[0]+$outboxCount[0]);
            $stats []= array("account"=>$acName,"provider"=>$acProvider,"inbox"=>$inboxCount[0],"outbox"=>$outboxCount[0],"unreaded"=>$unreadedCount[0],"total"=>$total);

        }
        return $stats;
    }


    public function getStatsForUsername($username) {
        $stats = array();
        $accounts = $this->getAccountsNames();
        for ($i=0;$i<count($accounts);$i++) {

            $inboxConditions = array (
            array("name"=> "account","operator" => "=","value"=>"$accounts[$i]"),
            array("condition"=>"AND"),
            array("name"=>"type","operator"=>"=","value"=>"1"),
            array("condition"=>"AND"),
            array("name"=>"username","operator"=>"=","value"=>$username)
            );
            $inboxCount    = sms_message::count("id",$inboxConditions);

            $outboxConditions = array (
            array("name"=> "account","operator" => "=","value"=>"$accounts[$i]"),
            array("condition"=>"AND"),
            array("name"=>"type","operator"=>"=","value"=>"0"),
            array("condition"=>"AND"),
            array("name"=>"username","operator"=>"=","value"=>$username)
            );
            $outboxCount   = sms_message::count("id",$outboxConditions);
            $total = ($inboxCount[0]+$outboxCount[0]);
            $stats [$accounts[$i]]= array("account"=>$accounts[$i],"inbox"=>$inboxCount[0],"outbox"=>$outboxCount[0],"total"=>$total);
        }
        return $stats;
    }


    /**
     * Makes an http request to the url with the selected method and the data to send
     *
     * @param $url string The URL for the request
     * @param $method string The method of the request
     * @param $postContent array The content of the request, only for POST method
     * @return mixed
     */
    public function _httpConnection($url,$method="GET",$postContent=array()) {

        $url = str_replace( "&amp;", "&", urldecode(trim($url)) );

        if (count($postContent)>0) {
            $postBody = "";
            foreach($postContent as $key=>$value) {
                $postBody .= urlencode($key).'='.urlencode($value).'&';
            }
        }

        $connection = curl_init();
        $cookie = "";
        $timeout = 15;

        curl_setopt($connection,CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
        curl_setopt($connection,CURLOPT_URL, $url );
        curl_setopt($connection,CURLOPT_ENCODING, "" );
        curl_setopt($connection,CURLOPT_RETURNTRANSFER, true );
        curl_setopt($connection,CURLOPT_AUTOREFERER, true );
        curl_setopt($connection,CURLOPT_CONNECTTIMEOUT, $timeout );
        curl_setopt($connection,CURLOPT_TIMEOUT, $timeout );
        curl_setopt($connection,CURLOPT_MAXREDIRS, 10 );
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        if ($method=="GET") {
            curl_setopt ($connection, CURLOPT_HTTPGET, 1);
        }
        if ($method=="POST") {
            curl_setopt ($connection, CURLOPT_POST, 1);
            curl_setopt ($connection, CURLOPT_POSTFIELDS, $postBody);
        }
        if ($method=="PUT") {
            curl_setopt ($connection, CURLOPT_PUT, 1);
        }
        if ($method=="DELETE") {
            curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        $contents = curl_exec($connection);
        $response = curl_getinfo($connection);
        curl_close ($connection);

        if (!$response) {
            $error = curl_error($connection);
            trigger_error("PLUGIN | BulkSMS plugin exception (cURL failed to load url {$url} and got {$error}",E_USER_WARNING);
        }

        // we have a redirect
        if ($response["http_code"] == 301 || $response["http_code"] == 302)    {
            ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
            $headers = get_headers($response["url"]);
            $location = "";
            foreach( $headers as $value )  {
                if ( substr( strtolower($value), 0, 9 ) == "location:" ) {
                    $newURL = trim(substr($value,9,strlen($value)));
                    return $this->_httpConnection($newURL,$method,$postContent);
                }
            }
        }
        return $contents;
    }

    /*public function createAccount($account) {
        if ($account) {
            if ($this->existsAccount($account["name"])) {
                return false;
            }
            $acc = new smsgateway_account();
            $acc->name        = $account["name"];
            $acc->username    = $account["username"];
            $acc->password    = $account["password"];
            $acc->provider    = $account["provider"];
            $acc->username    = $account["username"];
            $acc->active      = $account["active"];
            $acc->description = $account["description"];
            $acc->sender      = $account["sender"];
            $acc->creator     = Authentication::loggedUser();
            return $acc->save();
        }
        return false;
    }*/

    public function activateAccount($account="") {
        $res = 0;
        if (empty($account)) {
            return false;
        }
        $acc = smsgateway_account::findByColumn("name",$account);
        if ($acc!=null){
            $acc = $acc[0];
            $active = intval($acc->active);

            if ($active===0) {
                $acc->active = 1;
                $res = 1;
            }
            else {
                $acc->active = 0;
                $res = 0;
            }


            if ($acc->validateData() && $acc->save() ) {
                return $res;
            }
            return -1;
        }
        return -1;
    }

    public function updateAccount($account) {
        if ($account) {
            $acc = smsgateway_account::findByColumn("name",$account["name"]);
            if ($acc!=null) {
                $acc = $acc[0];
                $acc->username    = $account["username"];
                $acc->password    = $account["password"];
                $acc->provider    = $account["provider"];
                $acc->username    = $account["username"];
                $acc->active      = $account["active"];
                $acc->description = $account["description"];
                $acc->sender      = $account["sender"];
                $acc->creator     = Authentication::loggedUser();

                if ($acc->active=="on" || $acc->active==1 || $acc->active=="1") {
                    $acc->active="1";
                }
                else {
                    $acc->active="0";
                }
                if  ($acc->validateData() && $acc->save()) {
                    return true;
                }
                else {
                    return false;
                }
            }
            return false;
        }
        return false;
    }

    public function getAccounts() {
        $accounts = array();
        $acc = smsgateway_account::findAll();
        if ($acc!=null) {
            foreach ($acc as $account) {
                $accounts [$account->name]= array (
                    "id"          => $account->id,
                    "name"   	  => $account->name,
                    "provider"	  => $account->provider,
                    "description" => $account->description,
                    "active"	  => $account->active,
                	"username"	  => $account->username,
                	"password"	  => $account->password,
                    "sender"	  => $account->sender,
                    "stats"		  => $this->_storage->getMessagesCountByAccount($account->name)
                );
            }
        }
        return $accounts;
    }

    public function getActiveAccounts() {
        $accounts = array();
        $acc = smsgateway_account::findByColumn("active","1");
        if ($acc!=null) {
            foreach ($acc as $account) {
                $accounts [$account->name]= array (
                    "id"          => $account->id,
                    "name"   	  => $account->name,
                    "provider"	  => $account->provider,
                    "description" => $account->description,
                    "active"	  => $account->active,
                	"username"	  => $account->username,
                	"password"	  => $account->password,
                	"sender"	  => $account->sender
                );
            }
        }
        return $accounts;
    }
    public function getActiveAccountsNames() {
        $accounts = array();
        $acc = smsgateway_account::findByColumn("active","1");
        if ($acc!=null) {
            foreach ($acc as $account) {
                $accounts []= $account->name;
            }
        }
        return $accounts;
    }


    public function getAccountsNames() {
        $accounts = array();
        $acc = smsgateway_account::findAll();
        if ($acc!=null) {
            foreach ($acc as $account) {
                $accounts []= $account->name;
            }
        }
        return $accounts;
    }



    public function getAccount($name="default") {
        $acc = smsgateway_account::findByColumn("name",$name,array(array("column"=>"name","type"=>"ASC")));
        if ($acc->hasResult()) {
            $account = $acc->first();
            return array (
                    "id"          => $account->id,
                    "name"   	  => $account->name,
                    "provider"	  => $account->provider,
                    "description" => $account->description,
                    "active"	  => $account->active,
                    "username"	  => $account->username,
                	"password"	  => $account->password,
                    "sender"	  => $account->sender
            );
        }
        else {
            return null;
        }

    }

    public function existsAccount($name) {
        if (empty($name)) {
            return false;
        }
        else {
            $acc = smsgateway_account::findByColumn("name",$name,array(array("column"=>"name","type"=>"ASC")));
            if ($acc->hasResult()) {
                return true;
            }
            return false;
        }
    }

    public function deleteAccount($name="") {
        if (empty($name)) {
            return false;
        }
        else {
            $acc = smsgateway_account::findByColumn("name",$name);
            if ($acc!=null) {
                $acc = $acc[0];
                $this->_storage->deleteAccountMessages($name);
                return $acc->delete();
            }
            return false;
        }
        return false;
    }

    public function getConfiguration() {
        return $this->_configuration;
    }

    public function getCredit() {
        return $this->_provider->getCredit();
    }

    public function sendMessage($telephone,$text,$repliable=false) {
        return $this->_provider->sendMessage($telephone,$text,$repliable);
    }

    public function checkNewMessages() {
        return $this->_provider->checkNewMessages();
    }

    public function receiveMessages() {
        return $this->_provider->receiveMessages();
    }

    public function receiveMessagesCronTask() {
        $accounts = $this->getActiveAccountsNames();
        for ($i=0;$i<count($accounts);$i++) {
            $this->useConfiguration($accounts[$i]);
            $this->receiveMessages();
        }
    }

    public function receiveMessagesForAllAccounts() {
        $messages = array();
        $accounts = $this->getActiveAccountsNames();
        for ($i=0;$i<count($accounts);$i++) {
            $this->useConfiguration($accounts[$i]);
            $num = count($this->getInbox($accounts[$i],"","unreaded"));
            $messages []= array("account"=>$accounts[$i],"messages"=>$num);
        }
        return $messages;
    }


    public function updateSMSStatus() {
        return $this->_provider->updateSMSStatus();
    }

    public function getAllMessagesByAccount($name="default") {
        return $this->_storage->getAllMessagesByAccount($name);
    }

    public function getMessagesForPoll($account="default",$pollId="") {
        return $this->_storage->getMessagesForPoll($account,$pollId);
    }

    /**
     * Sends a SMS in batch mode
     *
     * @param $telephones array An array of telephones
     * @param $text string The text of the message
     * @return bool
     */
    public function sendBatchMessage($telephones=array(),$text) {
        if (count($telephones)>0) {
            foreach ($telephones as $telephone) {
                $this->sendMessage($telephone,$text);
            }
            return true;
        }
        return false;
    }

    /**
     * Sends a SMS
     *
     * @param $telephone string The telephone number of the receiver
     * @param $text string The text to be send
     * @return bool
     */


    public function getLastRetreivedId() {
        return $this->_getDBOption("last_retrieved_id");
    }

    public function getOutbox($account="default",$username="",$type="all") {
        $this->updateSMSStatus();
        return $this->_storage->getOutbox($account,$username,$type);
    }

    public function getInbox($account="default",$username="",$type="all") {
        $this->receiveMessages();
        $this->updateSMSStatus();
        return $this->_storage->getInbox($account,$username,$type);
    }

    public function getOutboxForUser($username="") {
        $this->updateSMSStatus();
        return $this->_storage->getOutboxForUser($username);
    }

    public function getInboxForUser($username="") {
        $this->receiveMessages();
        $this->updateSMSStatus();
        return $this->_storage->getInboxForUser($username);
    }

    public function markAsReaded($id,$account="default",$username="") {
        return $this->_storage->markAsReaded($id,$account,$username);
    }

    public function deleteMessage($id,$account="default",$username="") {
        return $this->_storage->deleteMessage($id,$account,$username);
    }

    public function getMessage($id,$account="default",$username="") {
        return $this->_storage->getMessage($id,$account,$username);
    }

    /*


    public function getConfig() {
    $username = $this->_getDBOption("username");
    $password = $this->_getDBOPtion("password");
    $country  = $this->_getDBOption("country");
    $sender   = $this->_getDBOption("sender");
    return array("username"=>$username,"password"=>$password,"country"=>$country,"sender"=>$sender);
    }

    public function setConfig($config) {

    if (isset($config["username"]) && ($config["username"]!=null) ) {
    $username = $config["username"];
    $this->_setDBOption("username",$username);
    }

    if (isset($config["country"]) && ($config["country"]!=null)) {
    $country = $config["country"];
    $this->_setDBOption("country",$country);

    }


    if (isset($config["password"]) && ($config["password"]!=null)) {
    $password = $config["password"];
    $this->_setDBOption("password",$password);
    }


    if (isset($config["sender"]) && ($config["sender"]!=null)) {
    $sender = $config["sender"];
    $this->_setDBOption("sender",$sender);
    }

    return true;
    }
    */
    public function getInboxMessages() {
        $this->_updateSMSStatus();

        $orders =  array (
        array (
		        "column"    => "date",
		        "orderType" => "DESC"
		        )
		        );

		        $messages = BulkSMSMessage::findByColumn("type","1",$orders);
		        if ($messages!=null) {
		            return $messages;
		        }
		        return null;
    }


    public function getMessagesForTelephone($account,$telephone,$username) {
        return $this->_storage->getMessagesForTelephone($account,$telephone,$username);
    }




    //// test
    public function test() {
        $this->useConfiguration("default");        
        var_dump($this->getInbox());
        exit;
    }


}

?>