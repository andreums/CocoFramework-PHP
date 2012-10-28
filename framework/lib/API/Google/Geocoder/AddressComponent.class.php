<?php
    class FW_API_Google_Geocoder_AddressComponent {
        
        private $_longName;
        private $_shortName;
        private $_types;
        
        
        public function __construct($data=null) {
            $this->_types     = $data->types;
            $this->_longName  = $data->long_name;
            $this->_shortName = $data->short_name;                        
        }
        
        
        public function getShortName() {
            return $this->_shortName;
        }
        
        public function getLongName() {
            return $this->_longName;
        }
        
        public function getTypes() {
            return $this->_types;            
        }
        
        public function isType($type) {
            return (in_array($type,$this->_types));
        }
        
    };
?>    
