<?php
    class circle {
                
        private $_name;              
        private $_radius;
        private $_center;
        private $_strokeWeight;
        private $_strokeColor;
        private $_strokeOpacity;
        private $_fillColor;
        private $_fillOpacity; 
        
        
        public function __construct($name,latLng $center=null,$radius=null) {
            $this->_name    = $name;
            if ($center!==null) {
                $this->_center = $center;
            }
            if ($radius!==null) {
                $this->_radius   = $radius;
            }
        }
        
        public function setRadius($radius) {
            $this->_radius = abs($radius);            
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
        
        public function display($div) {
            $name = $this->_name;                                    
            $code = "var circle_{$name} = new google.maps.Circle({                
                  map: {$div},
                  center: ".$this->_center->getJavaScript().",
                  radius: {$this->_radius},                  
                  strokeColor: \"#000000\",
                  strokeOpacity: 1,
                  strokeWeight: 3.5,
                  fillColor: \"#C67171\"  
            });\n\n";
                                
            return $code;
        }
        
        
        
    };
?>