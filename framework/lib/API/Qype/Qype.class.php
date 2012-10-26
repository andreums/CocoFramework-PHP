<?php
    class FW_API_Qype {    
        private $_consumerKey;
        private $_consumerSecret;
        private $_token;
        private $_tokenSecret;
        
        
        public function __construct() {
            echo "Qype API Connect";
            $this->_configure();            
        }
        
        private function _configure() {
            $config                = FW_Config::getInstance();
            $secrets               = $config->get("qype.global.secrets");
            $this->_consumerKey    = $secrets["consumer_key"];
            $this->_consumerSecret = $secrets["consumer_secret"];
            $this->_token          = $secrets["token"];
            $this->_tokenSecret    = $secrets["token_secret"];
        }
        
        protected function _requestToken() {
            $url             = "http://api.qype.com/oauth/request_token";
            $consumer        = new FW_OAuth_Consumer($this->_consumerKey, $this->_consumerSecret);
            $signatureMethod = new FW_OAuth_SignatureMethod_HMACSHA1();
            $oauthRequest    = new FW_OAuth_Request("GET",$url,array("oauth_callback_confirmed"=>true));        
            $oauthRequest->sign_request($signatureMethod, $consumer, null);
            $url             = $oauthRequest->to_url();
            
            //$token           = new FW_OAuth_Token($this->_token,$this->_tokenSecret);
            /*
            $signatureMethod = new FW_OAuth_SignatureMethod_HMACSHA1();
            $oauthRequest    = FW_OAuth_Request::from_consumer_and_token($consumer, null, 'GET', $url);        
            $oauthRequest->sign_request($signatureMethod, $consumer, null);
            $url             = $oauthRequest->to_url();
            var_dump($url);
            exit;*/
            return $url;
        }
        
        
        protected function _getSignedURL($url) {
            $token           = new FW_OAuth_Token($this->_token,$this->_tokenSecret);
            $consumer        = new FW_OAuth_Consumer($this->_consumerKey, $this->_consumerSecret);
            $signatureMethod = new FW_OAuth_SignatureMethod_HMACSHA1();
            $oauthRequest    = FW_OAuth_Request::from_consumer_and_token($consumer, $token, 'GET', $url);        
            $oauthRequest->sign_request($signatureMethod, $consumer, $token);
            $url             = $oauthRequest->to_url();
            return $url;
        }
        
        protected function _httpConnection($url) {
            $parameters = new FW_Container_Parameter();
            $parameters->endpoint = $url;
            $parameters->method   = "GET";
            $parameters->type     = "json";
            
            $client   = new FW_Rest_Client($parameters);
            if ($client->exec()) {
                $response = $client->getResponse();                            
                return $response->getBody();
            }
            return null;
        }
        
        
        /**
     * Checks if the result is in cache
     * 
     * @param string $url The url
     * 
     * @return mixed
     */
    protected function _checkResultInCache($url) {          
        $id    = md5($url);
        $cache = FW_Cache::getInstance();        
        $data  = $cache->get($id,"YelpCache");        
        if ($data!==null) {            
            $contents = $data->getContents();
            $contents = json_decode($contents);
            return $contents;
        }                
    }
    
    /**
     * Stores the result in cache for a month
     * 
     * @param string $url The url      
     * @param mixed $result The result of the geocoder
     * 
     * @return bool
     */
    protected function _setResultInCache($url,$result) {               
        $result = json_encode($result);        
        $id     = md5($url);
        $cache  = new FW_Cache();
        return  $cache->save($id,"YelpCache",$result,2592000);
    }
    
    
    public function authenticate() {
        //$url        = "http://api.qype.com";
        $url        = $this->_requestToken();
        //$signedUrl  = $this->_getSignedURL($url);
        $results    = $this->_httpConnection($url);
        var_dump($url);
        var_dump($results);
    }
};
?>
