<?php
class Account_Manager extends FW_Plugin_Library {

    private $_accounts;

    public function __construct() {
        $this->_accounts = array();
        $this->loadOptions();
        $this->_loadAccounts();
    }


    private function _loadAccounts() {
        $accounts = $this->getOption("accounts");
        if ($accounts!==null) {
            $accounts = array_keys($accounts);
            foreach ($accounts as $account) {
                $key  = "accounts.{$account}";
                $data = $this->getOption($key);
                if ($data!==null) {
                    $data["name"]              = $account;
                    $acc                       = new Account();
                    $acc->fromArray($data);
                    $this->_accounts[$account] = $acc;
                }
            }
        }

    }

    public function getAccounts() {
        return $this->_accounts;
    }

    public function getAccount($name) {
        if (isset($this->_accounts[$name])) {
            return $this->_accounts[$name];
        }
    }

    public function hasAccount($name) {
        return (isset($this->_accounts[$name]));
    }


    public function updateAccount($name,$data) {
        if ($this->hasAccount($name)) {
            $account = $this->getAccount($name);

            $description = $data["description"];
            $provider    = $data["provider"];

            $account->setDescription($data["description"]);
        }
    }




};
?>