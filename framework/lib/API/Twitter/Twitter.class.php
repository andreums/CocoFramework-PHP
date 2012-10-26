<?php
    class FW_API_Twitter {
        
        
        private $_url;                
        private $_consumerKey;
        private $_consumerSecret;
        private $_token;
        private $_tokenSecret;
        
        
        public function __construct() {
            echo "YELP API Connect";
            $this->_configure();            
        }
        
        private function _configure() {
            $config                = FW_Config::getInstance();
            $secrets               = $config->get("twitter.global.secrets");
            $this->_consumerKey    = $secrets["consumer_key"];
            $this->_consumerSecret = $secrets["consumer_secret"];
            $this->_token          = $secrets["token"];
            $this->_tokenSecret    = $secrets["token_secret"];
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
        var_dump($cache);
        $data  = $cache->get($id,"TwitterCache");        
        if ($data!==null) {            
            $contents = $data->getContents();            
            $results  = unserialize($contents);
            return $results;
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
        var_dump($result);       
        $result = serialize($result);        
        $id     = md5($url);
        $cache  = new FW_Cache();
        return  $cache->save($id,"TwitterCache",$result,2592000);
    }
};
?>
