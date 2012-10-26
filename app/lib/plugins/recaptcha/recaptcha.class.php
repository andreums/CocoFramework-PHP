<?php
    class recaptcha extends FW_Plugin_Base {
        
          /**
     * @param array $parameters
     */
    protected function _initialize(array $parameters=null) {}

    /**
     * @param array $parameters
     */
    protected function _configure(array $parameters=null) {}


    public function install(array $arguments=array()) {

    }

    public function uninstall(array $arguments=array()) {

    }
        
        public function createCaptcha() {
            $key = $this->getOption("publicKey");
            return recaptcha_get_html($key);
                        
        }
        
        
        public function verifyCaptcha() {
            $key   = $this->getOption("privateKey");
            $resp = recaptcha_check_answer ($key,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
            if (!$resp->is_valid)  {
                return false;
            }
            return true;
        }
            
    };
?>    
    
        