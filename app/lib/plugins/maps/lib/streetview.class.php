<?php
    class streetview {
                
        private $_name;
        private $_center;
        
        public function __construct($name,latLng $center=null) {
            $this->_name    = $name;
            if ($center!==null) {
                $this->_center = $center;
            }            
        }
                
        public function setCenter(latLng $center) {
            $this->_center = $center;
        }
        
        public function setCenterByAddress($address) {
            $geocoder      = new geocoder();
            $coordinates = $geocoder->getCoordinates($address);
            if (count($coordinates)>0) {
                if (isset($coordinates[0]["coordinates"])) {
                    $center                = $coordinates[0]["coordinates"];
                    $this->_center = $center;
                }
            }            
        }
        
        public function hasStreetView(latLng $position=null) {
            if ($position===null) {
                $position = $this->_center;
            }
            
            $radius       = 10;            
            $url             = "http://maps.google.com/cbk?output=json&hl=es&ll=".$position->lat().','.$position->lng()."&radius={$radius}&cb_client=maps_sv&v=4";            
            $result       = $this->_getHasStreetViewResult($url);
            if ($result===true) {
                return true;
            }                 
            
            $radius += 25;
            $url             = "http://maps.google.com/cbk?output=json&hl=es&ll=".$position->lat().','.$position->lng()."&radius={$radius}&cb_client=maps_sv&v=4";            
            $result       = $this->_getHasStreetViewResult($url);
            if ($result===true) {
                return true;
            }
            
            $radius += 15;
            $url             = "http://maps.google.com/cbk?output=json&hl=es&ll=".$position->lat().','.$position->lng()."&radius={$radius}&cb_client=maps_sv&v=4";            
            $result       = $this->_getHasStreetViewResult($url);
            if ($result===true) {
                return true;
            }            
            return false;
        }
        
      private function _getHasStreetViewResult($url) {
          $result       = $this->_checkResultInCache($url);
          if ($result!==null) {
              return $result;
          }
          else {
              $response = $this->_httpConnection($url);
              if ($response==="{}") {
                  $result = false;
              }
              else {
                  $result = true;
              }
            $this->_setResultInCache($url, $result);
        }
          return $result;
      }        
        /**
     * Checks if the result is in cache
     * 
     * @param string $url The url
     * 
     * @return mixed
     */
    private function _checkResultInCache($url) {        
        $id          = md5($url);
        $cache = new FW_Cache();
        $data    = $cache->get($id,"mapsPluginCache");        
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
    private function _setResultInCache($url,$result) {       
        $result  = serialize($result);        
        $id           = md5($url);
        $cache  = new FW_Cache();
        return  $cache->save($id,"mapsPluginCache",$result,2592000);
    }
        
        private function _httpConnection($url) {
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
        
        public function autoRotate($ms=100,$increment=1) {
            $name = $this->_name;
            $name = "streetView_{$name}";
            $code  = "\n\nvar i=0; rotate=true;\n var rotateInterval = undefined; \n";            
            $code .= "function autoRotate()\n {                
                if (rotate===false) {
                    clearInterval(rotateInterval);
                    return false;
                }                
                pov                 =  {$name}.getPov();
                pov.heading = i;
                {$name}.setPov(pov);
                if (i>=359) {\n
                    i = 0;\n
                }            \n
                else {\n
                    i+={$increment};\n
                }\n
                return true;               
            }\n";
            $code  .= " function stopRotating() { clearInterval(rotateInterval); rotate=false; }";
            $code  .= " function startRotating() { rotate=true; rotateInterval = setInterval(\"autoRotate()\",{$ms});\n }";
            return $code;
        }
            
        public function setPov($heading,$zoom=1,$pitch=0) {
            $name = $this->_name;
            $code  = "\n streetView_{$name}.setPov({
                heading: {$heading},
                zoom:   {$zoom},
                pitch: {$pitch}
            });\n";
            return $code;
        }            
        
        public function setCorrectHeading() {
            $name   = $this->_name;
            $name   = "streetView_{$name}";
            $center = $this->_center;            
            $code    = "
var service = new google.maps.StreetViewService;
origin          = new google.maps.LatLng({$center->lat()},{$center->lng()});
service.getPanoramaByLocation(origin,50, function(panoData) {
    var panoCenter = panoData.location.latLng;    
    var heading = google.maps.geometry.spherical.computeHeading(panoCenter,origin);
    var pov = {$name}.getPov();    
    pov.heading = heading;
    {$name}.setPov(pov);    
});
";
            return $code;
        }
            
        
        public function display($div) {
            $name      = $this->_name;                                                
            $code       = "streetView_{$name} =  new google.maps.StreetViewPanorama(document.getElementById(\"{$div}\"), {
    position: new google.maps.LatLng({$this->_center->lat()},{$this->_center->lng()}),
    addressControlOptions: {
        position: google.maps.ControlPosition.TOP
    },
    linksControl: true,
    panControl: true,
    zoomControlOptions: {
        style: google.maps.ZoomControlStyle.SMALL                
    },
    enableCloseButton: false,
    visible: true
});";
                                
            return $code;
        }
        
        
        
    };
?>

