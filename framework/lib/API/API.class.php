<?php
    abstract class FW_API {
        
        private $_database;        
        private $_httpClient;
        private $_cache;
        private $_config;         
        
        
        public function __construct($options=null) {
            print "API!";
            $this->_setup($options);
        }
        
        protected function config() {
            if ($this->_config === null) {
                $this->_config = FW_Config::getInstance();                
            }
            return $this->_config;
        }
        
        
        protected function cache() {
            if ($this->_cache === null) {
                $this->_cache = FW_Cache::getInstance();
            }
            return $this->_cache;
        }
        
        
        protected function httpRequest() {
            if ($this->_httpClient === null) {
                $this->_httpClient = new FW_Rest_Client();
            }                        
            return $this->_httpClient;
        }
        
        
        protected function database() {
            if ($this->_database === null) {
                $this->_database = FW_Database::getInstance();
            }
            return $this->_database;
        }
        
        
        protected function getLocale() {
            return FW_Locale::getInstance()->getLocale();            
        }
        
        protected function translate($string) {
            return FW_Locale::getInstance()->translate($string);
        }
        
    
        /**
         * Checks if the result is in cache
         * 
         * @param string $url The url
         * 
         * @return mixed
         */
        protected function getCachedData($tag,$namespace) {        
        $id    = md5($tag);        
        $data  = $this->cache()->get($id,$namespace);        
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
        public function setCachedData($tag,$namespace,$data,$lifetime=600) {
            $id     = md5($tag);       
            $data   = serialize($data);
            return  $this->cache()->save($id,$namespace,$data,$lifetime);
        }
        
        
        protected abstract function _setup($options=null);        
    };
?>    

