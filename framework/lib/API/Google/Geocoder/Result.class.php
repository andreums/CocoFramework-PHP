<?php
    class FW_API_Google_Geocoder_Result {
        
        private $_data;
        private $_types;
        private $_geometry;
        private $_addressComponents;
        private $_formatedAddress;
        
        public function __construct($data) {
            
            $this->_types             = $data->types;
            $this->_geometry          = $data->geometry;
            $this->_addressComponents = $data->address_components;
            $this->_formatedAddress   = $data->formatted_address;            
        }
        
        
        public function getCoordinates() {            
            return new FW_API_Google_Geocoder_LatLng($this->_geometry->location->lat,$this->_geometry->location->lng);           
        }
        
        public function getViewPort() {
            $vp       = $this->_geometry->viewport;            
            $viewPort = array(
                "ne" => new FW_API_Google_Geocoder_LatLng($vp->northeast->lat,$vp->northeast->lng),
                "se" => new FW_API_Google_Geocoder_LatLng($vp->southwest->lat,$vp->southwest->lng)
            );
            return $viewPort;         
        }
        
        public function getAddress() {
            return $this->_formatedAddress;
        }
        
        public function getTypes() {
            return $this->_types;
        }
        
        
    };
?>