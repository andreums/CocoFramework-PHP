<?php
/**
 * A Google Places client
 *
 * PHP Version 5.3
 * 
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * A Google Places client
 * 
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * 
 */
class places {
            
    /**
     * The complete places URL
     * 
     * @var string
     */
    private $_url;
    
    
    /**
     * The Google Places API Key
     * 
     * @var string
     */
    private $_key;
    
    
    /**
     * The Google Places API  Type parameter
     * 
     * @var array
     */
    private $_types;
    
    private $_name;
    
    
    /**
     * The Google Places API Radius parameter
     * 
     * @var array
     */
    private $_radius;
    
    public function __construct() {
        $this->_types  = array();
        $this->_radius = 1500;
    }
       
    /**
     * Makes a query to the Places webservice
     * 
     * @param string $url The url for the Places
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
     * Builds the URL for the Places service
     * 
     * @return string
     */
    private function _builPlacesURL() {
        $url        = "https://maps.googleapis.com/maps/api/place/search/json?&sensor=true&language=LANGUAGE&key=KEY&name=NAME&types=TYPES&radius=RADIUS";
        $url        = str_replace("LANGUAGE","es_ES",$url);        
        //$url        = str_replace("LANGUAGE",FW_Locale::getInstance()->getLocale(),$url);
        $url        = str_replace("KEY",$this->_key,$url);
        $url        = str_replace("RADIUS",$this->_radius,$url);
        if (count($this->_types)) {
            $url        = str_replace("TYPES",implode('|',$this->_types),$url);            
        }
        else {
            $url        = str_replace("TYPES","",$url);
        }        
        
        if (strlen($this->_name)) {
            $url        = str_replace("NAME",urlencode($this->_name),$url);
        }
        else {
            $url        = str_replace("NAME","",$url);
        }
        $this->_url = $url;        
        return $url;
    }
    
    /**
     * Builds the URL for the Places details
     * 
     * @return string
     */
    private function _builPlacesDetailURL() {
        $url        = "https://maps.googleapis.com/maps/api/place/details/json?&sensor=true&language=LANGUAGE&key=KEY";        
        $url        = str_replace("LANGUAGE","es_ES",$url);
        $url        = str_replace("KEY",$this->_key,$url);        
        $this->_url = $url;
        return $url;
    }
    
    public function setTypes(array $types=array()) {
        $this->_types = $types;
    }
    
    public function setName($name)  {
        $this->_name = $name;
    }
    
    public function setRadius($radius=500) {
        $this->_radius = $radius;
    }
    
    public function setKey($key) {
        $this->_key = $key;
    }
    
    public function getNearbyPlacesByAddress($address) {
        $geocoder    = new geocoder();
        $coordinates = $geocoder->getCoordinates($address);        
        $coordinates = $coordinates[0]["coordinates"];        
        return $this->getNearbyPlaces($coordinates);
    }
    
    public function getNearbyPlaces($lat,$lng=null) {
        $results = null;
        if ($lat instanceof latLng) {
            $latlng = $lat;
            $lat    = $latlng->lat();
            $lng    = $latlng->lng();
        }
        $url        = $this->_builPlacesURL();
        $url       .= "&location={$lat},{$lng}";                
        $results    = $this->_checkResultInCache($url);
        if ($results===null) {        
            $data     = $this->_httpConnection($url);            
            $results  = $this->_process($data);            
            $this->_setResultInCache($url,$results);
        }        
        return $results;
    }
    
    public function getPlaceDetails($reference) {
        $results = null;        
        $url     = $this->_builPlacesDetailURL();
        $url    .= "&reference={$reference}";
        $results = $this->_checkResultInCache($url);
        var_dump($url);               
        if ($results===null) {        
            $results        = $this->_httpConnection($url);
            $this->_setResultInCache($url,$results);
        }        
        return $results;
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
        $data                             = array();
        $data["id"]                   = $result->id;
        $data["types"]           = $result->types;
        if ($result->vicinity!==null) {
            $data["vicinity"]         = $result->vicinity;
        }
        $data["icon"]               = $result->icon;   
        $data["name"]           = $result->name;     
        $data["reference" ] = $result->reference;
        
        
        if ($result->geometry!==null) {
            $geometry = $this->_processGeometry($result->geometry);
            $data["geometry"]  = $geometry;            
        }        
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
        if (isset($geometry->location)) {        
            return  new latLng($geometry->location->lat,$geometry->location->lng);
        }
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
         $addresses     = $this->_getAddressFromResults($results);
         $coordinates = $this->_getCoordinatesFromResults($results);
         
         $return      = array();
         
         if ( (count($addresses)===count($coordinates)) && (count($addresses)>0)) {
             $elements = count($addresses);
             for ($i=0;$i<$elements;$i++) {
                 $return []= array (
                     "address"	   => $addresses[$i],
                     "coordinates" => $coordinates[$i]    
                 );
             }
         }
         return $return;         
    }  

    
    /**
     * Checks if the result is in cache
     * 
     * @param string $url The url
     * 
     * @return mixed
     */
    private function _checkResultInCache($url) {        
        $id          = md5($url);
        $cache = new FW_Cache();
        $data    = $cache->get($id,"mapsPluginCache");        
        if ($data!==null) {            
            $contents = $data->getContents();            
            $results  = unserialize($contents);
            return $results;
        }                
    }
    
    /**
     * Stores the result in cache for a month
     * 
     * @param string $url The url      
     * @param mixed $result The result of the geocoder
     * 
     * @return bool
     */
    private function _setResultInCache($url,$result) {       
        $result  = serialize($result);        
        $id           = md5($url);
        $cache  = new FW_Cache();
        return  $cache->save($id,"mapsPluginCache",$result,2592000);
    }
};
?>