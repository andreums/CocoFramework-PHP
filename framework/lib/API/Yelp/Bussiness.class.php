<?php
    class FW_API_Yelp_Bussiness extends FW_API_Yelp {
        
        public function getBussiness($id) {
            $url        = "http://api.yelp.com/v2/business/{$id}";
            $signedUrl  = $this->_getSignedURL($url);
            $results    = $this->_checkResultInCache($url);            
            if ($results===null) {        
                $results       = $this->_httpConnection($signedUrl);                
                //$results     = $this->_process($data);
                $this->_setResultInCache($url,$results);
            }
            return $results;
        }
        
    };
?>    
    
