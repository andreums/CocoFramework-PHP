<?php
    class polyline {
        
        private $_name;
        private $_points = array();
        
        
        public function __construct($name,array $data=array()) {
            $this->_name = $name;            
            if (!empty($data)) {
                $this->addPoints($data);                                
            }            
        }
        
        public function setName($name) {
            $this->_name = $name;
        }
        
        public function addPoints(array $data=array()) {
            foreach ($data as $aux) {
                    if (count($aux)!==2) {
                        throw new FW_Exception("Se necesita al menos dos puntos...");
                    }
                    $this->_points []= new latLng($aux[0],$aux[1]);
            }
        }
        
        public function addPoint(array $data=array()) {
            $this->_points []= new latLng($data[0],$data[1]);            
        }
        
        
        public function display($div) {
            $name = $this->_name;
            $code  = "var points_{$name} = [\r\n";
            if (count($this->_points)) {
                foreach ($this->_points as $point) {
                    $code .= "new google.maps.LatLng({$point->lat()},{$point->lng()}),\r\n";
                }
            }            
            $code  = substr($code,0,-1);
            $code .= "];\r\n";
            $code .= "var polyline_{$name} = new google.maps.Polyline({ path: points_{$name} ,map: {$div}, strokeColour: \"#000000\",strokeOpacity: 1, strokeWeight: 1.5  });\n";
            //$code .= "polyline.setMap({$div});";                    
            return $code;
        }
        
    };
?>