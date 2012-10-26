<?php
/**
 * A Google Maps gLatLng wrapper
 *
 * PHP Version 5.3
 * 
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * A Google Maps gLatLng wrapper
 * 
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * 
 */
class latLng {

    /**
     * The latitude
     * 
     * @var double
     */
    private $_lat;
    
    /**
     * The longitude 
     * 
     * @var double
     */
    private $_lng;

    /**
     * Constructor of latLng
     * 
     * @param double $lat The latitude
     * @param double $lng The longitude
     * 
     * @return void
     */
    public function __construct($lat,$lng) {
        $this->_lat = $lat;
        $this->_lng = $lng;
    }    
    
    /**
     * Gets the latitude
     * 
     * @return double
     */
    public function lat() {
        return $this->_lat;
    }    
    
    /**
     * Gets the longitude
     * 
     * @return double
     */
    public function lng() {
        return $this->_lng;
    }       
    
	/**
     * Sets the latitude
     * 
     * @param double $lat The latitude
     * 
     * @return void
     */
    public function setLatitude($lat) {
        $this->_lat = $lat;
    }

    /**
     * Sets the longitude
     * 
     * @param double $lng The longitude
     * 
     * @return void
     */
    public function setLongitude($lng) {
        $this->_lat = $lat;
    }

    /**
     * Gets the Javascript code for Google Maps V3
     * 
     * @return string
     */
    public function getJavaScript() {
        $lat  = str_replace(",",".",$this->_lat);
        $lng  = str_replace(",",".",$this->_lng);
        $code = "new google.maps.LatLng({$lat},{$lng})";

        return $code;
    }

    /**
     * Encodes the lat and lng for a value to be used in an URL
     * 
     * @return string
     */
    public function toUrlValue() {
        return round($this->_lat,6).",".round($this->lng,6);
    }    
    
    public function toArray() {
        return array("lat"=>$this->_lat,"lng"=>$this->_lng);
    }
    
};

?>