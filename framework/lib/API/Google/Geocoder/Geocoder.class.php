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
            $data    = $this->httpRequest()->get($url,"json");            
            $this->setCachedData($url,"googleMapsGeocoder",$data,295000);
        }        
        return new FW_API_Google_Geocoder_Results($results);
    }
    
    /**
     * Makes a query to the Geocoder webservice
     * 
     * @param string $url The url for the geocoder
     * 
     * @return mixed
     */
    private function _httpConnection($url) {        
        $parameters = new FW_Container_Parameter();
        $parameters->endpoint = $url;
        $parameters->method   = "GET";
        $parameters->type     = "json";
        
        $client   = new FW_Rest_Client($parameters);
        if ($client->exec()) {
            $response = $client->getResponse();            
            return $response->getBody();
        }
        return null;
    }

    /**
     * Builds the URL for the geocoder service
     * 
     * @return string
     */
    private function _builGeocoderURL() {        
        return (str_replace("LANGUAGE",$this->_language,$this->_endpoint));        
    }

    /**
     * Gets the coordinates from an address
     * 
     * @param string $address The address     
     * 
     * @return mixed 
     */
    public function getCoordinates($address) {        
        $address     = urlencode($address);
        $url         = $this->_builGeocoderURL();
        $url        .= "&address={$address}";
        var_dump($url);
        
        $results      = $this->_checkResultInCache($url);
        if ($results===null) {        
            $data        = $this->_httpConnection($url);            
            $results     = $this->_process($data);
            $this->_setResultInCache($url,$results);
        }        
        return $this->_getResult($results);
    }


    /**
     * Processes the geocoder result
     * 
     * @param mixed $data The result of the geocoder
     * 
     * @return array
     */
    private function _process($data) {
        $results    = array();
        $json       = json_decode($data);
        if ($json!=null) {
            if ($json->status=="OK") {
                foreach ($json->results as $result) {
                    $results []= $this->_processResult($result);
                }
            }            
        }        
        return $results;
    }

    /**
     * Proccesess every result of a geocoder request
     * 
     * @param array $result The result of a geocoder request
     *  
     * @return array
     */
    private function _processResult($result) {        
        $data                       = array();
        $data["types"]              = $result->types;
        $data["address_components"] = array();
        $data["formated_address"]   = $result->formatted_address;

        if (isset($result->address_components)) {
            foreach ($result->address_components as $component) {
                $data["address_components"] []= $this->_processAddressComponent($component);
            }
        }
        if (isset($result->geometry)) {
            $geometry = $this->_processGeometry($result->geometry);
        }
        $data ["geometry"] = $geometry;        
        return $data;
    }

    /**
     * Processes the address of a geocoder result
     * 
     * @param array $component
     * 
     * @return array 
     */
    private function _processAddressComponent($component) {
        $data = array (
            "long_name"  => $component->long_name,
            "short_name" => $component->short_name,
            "types"      => $component->types   
        );
        return $data;
    }

    /**
     * Processes the geometry of a geocoder result
     * 
     * @param array $geometry
     * 
     * @return array
     */
    private function _processGeometry($geometry) {
        $data = array (
            "location"  => new latLng($geometry->location->lat,$geometry->location->lng),
            "viewport"  => array (
                "southwest" => new latLng($geometry->viewport->southwest->lat,$geometry->viewport->southwest->lng),
                "northeast" => new latLng($geometry->viewport->northeast->lat,$geometry->viewport->northeast->lng)
             ),
            "location_type" => $geometry->location_type
        );
        return $data;
    }


    /**
     * Gets the addresses from a geocoder result
     * 
     * @param array $results The results
     * 
     * @return array
     */
    private function _getAddressFromResults($results) {
        $address = array();
        if (count($results)>0) {
            foreach ($results as $result) {
                $address []= $result["formated_address"];
            }
        }
        return $address;
    }

    /**
     * Gets coordinates from a geocoder result
     * 
     * @param array $results The results
     * 
     * @return array
     */
    private function _getCoordinatesFromResults($results) {
        $coordinates = array();
        if (count($results)>0) {
            foreach ($results as $result) {
                $coordinates []= $result["geometry"]["location"];
            }
        }
        return $coordinates;
    }
    
    /**
     * Gets the result of a geocoder request
     * 
     * @param array $results
     * 
     * @return array
     */
    private function _getResult($results) {
         $addresses   = $this->_getAddressFromResults($results);
         $coordinates = $this->_getCoordinatesFromResults($results);
         
         $return      = array();
         
         if ( (count($addresses)===count($coordinates)) && (count($addresses)>0)) {
             $elements = count($addresses);
             for ($i=0;$i<$elements;$i++) {
                 $return []= array (
                     "address"     => $addresses[$i],
                     "coordinates" => $coordinates[$i]    
                 );
             }
         }
         return $return;         
    }    
  
};
?>