<?php
    class news_hit extends FW_ActiveRecord_Model {
        
        protected $id;
        protected $ip;
        protected $browser;
        protected $browserversion;
        protected $os;
        protected $osversion;
        protected $url;
        protected $timestamp;
        protected $direction;
        protected $referrer;
        protected $user;                
        protected $country;
        protected $region;
        protected $city;
        protected $id_news;
        
        public static function isUniqueHit($id) {
            $request  = FW_Request::getInstance();
            $browser = FW_Browser::getInstance();
            $user        = FW_Authentication::getUser();
            if ($user!==null) {
                $user = $user->username;
            }
             
            $ip    = $request->getClientIP();
            $os   = $browser->getPlatformName();
            $osv = $browser->getPlatformVersion();
            $url  = $request->getServerURL();
            $ag   = $browser->getBrowserName();
            $agv = $browser->getBrowserVersion();
             
            $hit = news_hit::find(" ip='{$ip}' AND os='{$os}' AND osversion='{$osv}' AND id_news='{$id}' AND browser='{$ag}' AND browserversion='{$agv}'  AND DATE(timestamp)=DATE(NOW())");
            
            if ($hit->hasResult()) {
                return false;
            }
            return true;
        }
        
        
        public static function registerHit($id) {
            
            
            $request  = FW_Request::getInstance();
            $browser = FW_Browser::getInstance();
            $user        = FW_Authentication::getUser();                        
            if ($user!==null) {
                $user = $user->username;
            }   
            
            $country = "";
            $region   = "";
            $city        = "";
            
 
            
            $geo  = @geoip_record_by_name($request->getClientIP());
            if ($geo!==null) {
                $countryCode  = $geo["country_code"];
                $country            = $geo["country_name"];
                $region              = $geo["region"];                
                $city                   = $geo["city"];
                if (strlen($region)>0) {
                    $region             = geoip_region_name_by_code($countryCode,$region);                    
                }          
            }            
            
            $hit                         = new news_hit();
            $hit->ip                  = $request->getClientIP();
            $hit->url                = $request->getServerURL();
            $hit->timestamp = date("Y-m-d H:i:s");
            $hit->referrer      = $browser->getReferrer();            
            $hit->browser      = $browser->getBrowserName();
            $hit->browserversion = $browser->getBrowserVersion();
            $hit->os                  = $browser->getPlatformName();
            $hit->osversion  = $browser->getPlatformVersion();            
            $hit->direction   = 1;
            $hit->id_news     = $id;           
            
            if ($user!==null) {
                $hit->user             = $user;
            }
            $hit->country = $country;
            $hit->region   = $region;
            $hit->city        = $city;
                        
            return $hit->save();
        }         
        
        
    };
?>