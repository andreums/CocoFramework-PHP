<?php
    class gMarker {
        
        protected static $_dataBase;
        
        private $_cursor;
        private $_clickable;
        private $_draggable;
        private $_flat;
        private $_icon; 
        private $_position;
        private $_title;
        private $_visible;
        private $_infoWindowText;
        
        /**
         * @param gLatLng $position
         * @param $title
         * @param $infoWindowText
         * @return unknown_type
         */
        public function __construct(gLatLng $position,$title,$infoWindowText="",$icon="") {
            $this->_position       = $position;
            $this->_title          = $title;
            $this->_infoWindowText = $infoWindowText;
            $this->_icon           = $icon;            
        }
        
        /**
         * @param $mapName
         * @return unknown_type
         */
        public function generateJavaScript($mapName) {
             $code  = "new google.maps.Marker ({\n";
             $code .= "position:".$this->_position->getJavaScript().",\n";
             $code .= "map: {$mapName},\n";
             if (!empty($this->_icon)) {
                 $code .= "icon: \"{$this->_icon}\",\n";
             }
             $code .= "title: \"{$this->_title}\" \n";
             $code .= "});\n\n";
             return $code;
        }
        
        public function generateInfoWindowJavaScript($mapName,$markerName) {
             $code  = "var {$markerName}_infoWindow = new google.maps.InfoWindow ({\n";
             $code .= "content: \"{$this->_infoWindowText}\" });\n";
             
             $code .= "google.maps.event.addListener({$markerName},'click',function() {\n";
             $code .= "{$markerName}_infoWindow.open({$mapName},{$markerName}); });\n";
             return $code;            
        }
        
        
        public function hasInfoWindow() {
            return (!empty($this->_infoWindowText));
        }
        
    };
?>