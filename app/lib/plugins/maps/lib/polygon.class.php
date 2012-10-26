<?php
    class polygon {
        private $_tooltip;
        private $_clickable;
        private $_fillColor;
        private $_fillOpacity;
        private $_strokeColor;
        private $_strokeOpacity;
        private $_strokeWeight;
        private $_onMouseDown;
        private $_onMouseOut;
        private $_onMouseOver;
        private $_onMouseMove;
        private $_onMouseUp;
        private $_onClick;
        private $_name;
        private $_points = array();
        private $_extraData;
        
        public function setExtraData(array $data=array()) {
           $this->_extraData = $data;
        }
        
        public function setOnClick($code) {
            $this->_onClick = $code;
        }
        
        public function setMouseMove($code) {
            $this->_onMouseMove = $code;
        }
        
        public function setMouseDown($code) {
            $this->_onMouseDown = $code;
        }

        public function setMouseOut($code) {
            $this->_onMouseOut = $code;
        }

        public function setMouseOver($code) {
            $this->_onMouseOver = $code;
        }

        public function setMouseUp($code) {
            $this->_onMouseUp = $code;
        }

        public function __construct($name, array $data = array()) {
            $this->_name = $name;
            if ( !empty($data) ) {
                $this->addPoints($data);
            }
        }

        private function _generateExtraData() {
            $code = "";
            if ( $this->_extraData !== null ) {
                foreach ( $this->_extraData as $key => $value ) {
                    $code .= " {$key}:'{$value}' ,";
                }
                $code = substr($code, 0, -1);
            }
            return $code;
        }

        public function setName($name) {
            $this->_name = $name;
        }
        
        public function getName() {
            return "polygon_{$this->_name}";
        }

        public function addPoints(array $data = array()) {
            foreach ( $data as $aux ) {
                if ( count($aux) !== 2 ) {
                    throw new FW_Exception("Se necesita al menos dos puntos...");
                }
                $this->_points[] = new latLng($aux[0], $aux[1]);
            }
        }

        public function addPoint(array $data = array()) {
            $this->_points[] = new latLng($data[0], $data[1]);
        }

        public function setTooltip($text) {
            $this->_tooltip = $text;
        }

        public function display($div) {
            $name = $this->_name;
            $code = "var points_{$name} = [\r\n";
            if ( count($this->_points) ) {
                foreach ( $this->_points as $point ) {
                    $code .= "new google.maps.LatLng({$point->lat()},{$point->lng()}),\r\n";
                }
            }
            $code = substr($code, 0, -1);
            $code .= "];\r\n";
            $code .= "var polygon_{$name} = new google.maps.Polygon({
                     path: points_{$name},
                     map: {$div},
                     strokeColor: '#000000',
                     strokeOpacity: 1,
                     strokeWeight: 1.5,
                     fillColor: '#ECECEC',
            ";
            $code .= $this->_generateExtraData();            
            $code .= "});\n";            
            //$code .=
            // "google.maps.event.addListener(polygon_{$name},\"mouseover\",function(){
            // this.setOptions({fillColor: \"#00FF00\"}); });";
            //$code .=
            // "google.maps.event.addListener(polygon_{$name},\"mouseout\",function(){
            // this.setOptions({fillColor: \"#ECECEC\"}); });";
            //$code .= "polyline.setMap({$div});";
            return $code;
        }

        /**
         * Generates the javascript code for the mouse events of the polygon
         *
         * @param string $mapName The name of the map
         * @param string $markerName The name of the marker
         *
         * @return string
         */
        public function generateMouseEventsJavaScript($mapName, $polygonName) {
            $code = "";
            if ( $this->_onMouseDown !== null ) {
                $code .= "google.maps.event.addListener({$polygonName},'mousedown',function(event) {\n";
                $code .= "\t {$this->_onMouseDown} \n});\n";
            }
            if ( $this->_onMouseOver !== null ) {
                $code .= "google.maps.event.addListener({$polygonName},'mouseover',function(event) {\n";
                $code .= "\t {$this->_onMouseOver} \n});\n";
            }
            if ( $this->_onMouseUp !== null ) {
                $code .= "google.maps.event.addListener({$polygonName},'mouseup',function(event) {\n";
                $code .= "\t {$this->_onMouseUp} \n});\n";
            }
            if ( $this->_onMouseOut !== null ) {
                $code .= "google.maps.event.addListener({$polygonName},'mouseout',function(event) {\n";
                $code .= "\t {$this->_onMouseOut} \n});\n";
            }
            if ( $this->_onMouseMove !== null ) {
                $code .= "google.maps.event.addListener({$polygonName},'mousemove',function(event) {\n";
                $code .= "\t {$this->_onMouseMove} \n});\n";
            }
            if ( $this->_onClick !== null ) {
                $code .= "google.maps.event.addListener({$polygonName},'click',function(event) {\n";
                $code .= "\t {$this->_onClick} \n});\n";
            }
            return $code;
        }

    };
?>