<?php
/**
 * A Google Geocoder client
 *
 * PHP Version 5.3
 * 
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * A Google Geocoder client
 * 
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * 
 */
class FW_API_Google_Geocoder extends FW_API {
            
    /**
     * The complete geocoder URL
     * 
     * @var string
     */
    private $_endpoint;
    
    
    /**
     * The language to use with the geocoder
     * (default is Freamework Locale)
     * 
     * 
     * 
     * @var string
     */
    private $_language;
    
    
    public function __construct() {
        parent::__construct();
    }
    
    
    protected function _setup($options=null) {
        if ($this->config()->get("google.sections.geocoder.locale")===null) {
            $this->_language = $this->getLocale();
        }
        else {
            $this->_language = $this->config()->get("google.sections.geocoder.locale");
        }
        $this->_endpoint = $this->config()->get("google.sections.geocoder.endpoint");
    }
    
    public function setLanguage($locale) {
        $this->_language = $locale;        
    }
    
    
    
        
    /**
     * Gets the addres that belongs to the coordinates
     * 
     * @param double $lat The latitude
     * @param double $lng The longitude   
     * 
     * @return mixed 
     */
    public function getAddressFromCoordinates($lat,$lng) {
        $address     = "";
        $url         = $this->_builGeocoderURL()."&latlng={$lat},{$lng}";        
        $results     = $this->getCachedData($url,"googleMapsGeocoder");
        if ($results===null) {
            $data    = $this->httpRequest()->get($url,"json");            
            $this->setCachedData($url,"googleMapsGeocoder",$data,295000);
        }
        return new FW_API_Google_Geocoder_Results($results);
    }
    
    
    /**
     * Gets the coordinates from an address
     * 
     * @param string $address The address     
     * 
     * @return mixed 
     */
    public function getCoordinatesFromAddress($address) {        
        $address     = urlencode($address);
        $url         = $this->_builGeocoderURL()."&address={$address}";        
        $results     = $this->getCachedData($url,"googleMapsGeocoder");
        if ($results===null) {        
            $results  = $this->httpRequest()->get($url,"json");            
            $this->setCachedData($url,"googleMapsGeocoder",$results,295000);
        }        
        return new FW_API_Google_Geocoder_Results($results);
    }    
  
    /**
     * Builds the URL for the geocoder service
     * 
     * @return string
     */
    private function _builGeocoderURL() {        
        return (str_replace("LANGUAGE",$this->_language,$this->_endpoint));        
    }   
};
?>