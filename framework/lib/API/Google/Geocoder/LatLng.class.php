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
class FW_API_Google_Geocoder_LatLng {

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
    
    public function toString() {
        return "{$this->_lat},{$this->_lng}";
    }
    
};

?>