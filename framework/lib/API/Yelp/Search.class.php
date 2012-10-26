<?php
    class FW_API_Yelp_Search extends FW_API_Yelp {
        
        public function searchByBoundingBox($term,$sw_latitude,$sw_longitude,$ne_latitude,$ne_longitude,$limit=10) {
            if ($limit>20) {
                throw new FW_API_Yelp_Exception("API Limits to 20 results");
            }
            $url        = "http://api.yelp.com/v2/search?term={$term}&bounds={$sw_latitude},{$sw_longitude}%7C{$ne_latitude},{$ne_longitude}&limit={$limit}";
            $signedUrl  = $this->_getSignedURL($url);
            $results    = $this->_checkResultInCache($url);                    
            if ($results===null) {        
                $results       = $this->_httpConnection($signedUrl);                
                //$results     = $this->_process($data);
                $this->_setResultInCache($url,$results);
            }
            return $results;
        }
        
        
        public function searchByCoordinates($term,$latitude,$longitude,$limit) {
            if ($limit>20) {
                throw new FW_API_Yelp_Exception("API Limits to 20 results");
            }
            $url        = "http://api.yelp.com/v2/search?term={$term}&ll={$latitude},{$longitude}&limit={$limit}";
            $signedUrl  = $this->_getSignedURL($url);
            $results    = $this->_checkResultInCache($url);                        
            if ($results===null) {        
                $results       = $this->_httpConnection($signedUrl);
                $this->_setResultInCache($url,$results);
            }
            return $results;
        }
        
        
        public function searchByAddress($term,$location,$limit) {
            if ($limit>20) {
                throw new FW_API_Yelp_Exception("API Limits to 20 results");
            }
            $url        = "http://api.yelp.com/v2/search?term={$term}&location={$location}&limit={$limit}";
            $signedUrl  = $this->_getSignedURL($url);
            $results    = $this->_checkResultInCache($url);                                    
            if ($results===null) {        
                $results       = $this->_httpConnection($signedUrl);
                $this->_setResultInCache($url,$results);
            }
            var_dump($results);
            return $results;
        }
        
    };
?>    
    
