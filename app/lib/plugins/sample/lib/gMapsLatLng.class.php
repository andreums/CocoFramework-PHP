<?php
class gMapsLatLng {

    private $_lat;
    private $_lng;

    public function __construct($lat,$lng) {
        $this->_lat = $lat;
        $this->_lng = $lng;
    }
    
    public function lat() {
        return $this->getLatitude();
    }
    
    public function lon() {
        return $this->getLongitude();
    }

    public function getLatitude() {
        return $this->_lat;
    }

    public function getLongitude() {
        return $this->_lng;
    }

    public function setLatitude($lat) {
        $this->_lat = $lat;
    }

    public function setLongitude($lng) {
        $this->_lat = $lat;
    }

    public function getJavaScript() {
        $lat  = str_replace(",",".",$this->_lat);
        $lng  = str_replace(",",".",$this->_lng);
        $code = "new google.maps.LatLng({$lat},{$lng})";

        return $code;
    }

    public function toUrlValue() {
        return round($this->_lat,6).",".round($this->lng,6);
    }
    
    
    public function __sleep() {
        return array("_lat","_lng");
    }
    public function __wakeup() {}


};

?>