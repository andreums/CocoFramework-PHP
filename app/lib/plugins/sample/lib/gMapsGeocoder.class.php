<?php
class gMapsGeocoder {

    const GEOCODER_URL = "http://maps.google.com/maps/api/geocode/json?sensor=SENSOR&language=LANGUAGE";
    private $_sensor;
    private $_url;

    /*
     * "OK" indicates that no errors occurred; the address was successfully parsed and at least one geocode was returned.
     * "ZERO_RESULTS" indicates that the geocode was successful but returned no results. This may occur if the geocode was passed a non-existent address or a latlng in a remote location.
     * "OVER_QUERY_LIMIT" indicates that you are over your quota.
     * "REQUEST_DENIED" indicates that your request was denied, generally because of lack of a sensor parameter.
     * "INVALID_REQUEST" generally indicates that the query (address or latlng) is missing.
     */

    /**
     * @param $format
     * @param $sensor
     * @return unknown_type
     */
    public function __construct($sensor="true") {
        $this->_sensor = $sensor;
    }


    private function _httpConnection($url,$method="GET",$postContent=array()) {

        if (count($postContent)>0) {
            $postBody = "";
            foreach($postContent as $key=>$value) {
                $postBody .= urlencode($key).'='.urlencode($value).'&';
            }
        }



        $connection = curl_init();
        $cookie = "";
        $timeout = 15;

        curl_setopt($connection,CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
        curl_setopt($connection,CURLOPT_URL, $url );
        curl_setopt($connection,CURLOPT_ENCODING, "" );
        curl_setopt($connection,CURLOPT_RETURNTRANSFER, true );
        curl_setopt($connection,CURLOPT_AUTOREFERER, true );
        curl_setopt($connection,CURLOPT_CONNECTTIMEOUT, $timeout );
        curl_setopt($connection,CURLOPT_TIMEOUT, $timeout );
        curl_setopt($connection,CURLOPT_MAXREDIRS, 10 );
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        if ($method=="GET") {
            curl_setopt ($connection, CURLOPT_HTTPGET, 1);
        }
        if ($method=="POST") {
            curl_setopt ($connection, CURLOPT_POST, 1);
            curl_setopt ($connection, CURLOPT_POSTFIELDS, $postBody);
        }
        if ($method=="PUT") {
            curl_setopt ($connection, CURLOPT_PUT, 1);
        }
        if ($method=="DELETE") {
            curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        $contents = curl_exec($connection);
        $response = curl_getinfo($connection);
        curl_close ($connection);

        if (!$response) {
            $error = curl_error($connection);
            trigger_error("PLUGIN | BulkSMS plugin exception (cURL failed to load url {$url} and got {$error}",E_USER_WARNING);
        }

        // we have a redirect
        if ($response["http_code"] == 301 || $response["http_code"] == 302)    {
            //ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/10.1");
            $headers = get_headers($response["url"]);
            $location = "";
            foreach( $headers as $value )  {
                if ( substr( strtolower($value), 0, 9 ) == "location:" ) {
                    $newURL = trim(substr($value,9,strlen($value)));
                    return $this->_httpConnection($newURL,$method,$postContent);
                }
            }
        }
        return $contents;
    }

    private function _builGeocoderURL() {
        $url        = self::GEOCODER_URL;
        $url        = str_replace("SENSOR",$this->_sensor,$url);
        $url        = str_replace("LANGUAGE",FW_Locale::getInstance()->getLocale(),$url);
        $this->_url = $url;
        return $url;
    }

    public function getCoordinates($address) {
        $address     = urlencode($address);
        $url         = $this->_builGeocoderURL();
        $url        .= "&address={$address}";
        
        $results      = $this->_checkResultInCache($url);
        if ($results===null) {        
            $data        = $this->_httpConnection($url);
            $results     = $this->_process($data);
            $this->_setResultInCache($url,$results);
        }
        return $this->_getResult($results);
    }

    public function getAddress($lat,$lng) {
        $address     = "";
        $url         = $this->_builGeocoderURL();
        $url        .= "&latlng={$lat},{$lng}";        
        $results      = $this->_checkResultInCache($url);
        if ($results===null) {        
            $data        = $this->_httpConnection($url);
            $results     = $this->_process($data);
            $this->_setResultInCache($url,$results);
        }
        return $this->_getResult($results);
    }

    private function _process($data) {
        $results    = array();
        $json       = json_decode($data);
        if ($json!=null) {
            if ($json->status=="OK") {
                foreach ($json->results as $result) {
                    $results []= $this->_processResult($result);
                }
            }
            else {
                return array();
            }
        }        
        return $results;
    }

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

    private function _processAddressComponent($component) {
        $data = array (
            "long_name"  => $component->long_name,
            "short_name" => $component->short_name,
            "types"		 => $component->types	
        );
        return $data;
    }

    private function _processGeometry($geometry) {
        $data = array (
            "location" 	=> new gMapsLatLng($geometry->location->lat,$geometry->location->lng),
            "viewport"  => array (
                "southwest" => new gMapsLatLng($geometry->viewport->southwest->lat,$geometry->viewport->southwest->lng),
                "northeast" => new gMapsLatLng($geometry->viewport->northeast->lat,$geometry->viewport->northeast->lng)
             ),
            "location_type" => $geometry->location_type
        );
        return $data;
    }


    private function _getAddressFromResults($results) {
        $address = array();
        if (count($results)>0) {
            foreach ($results as $result) {
                $address []= $result["formated_address"];
            }
        }
        return $address;
    }

    private function _getCoordinatesFromResults($results) {
        $coordinates = array();
        if (count($results)>0) {
            foreach ($results as $result) {
                $coordinates []= $result["geometry"]["location"];
            }
        }
        return $coordinates;
    }
    
    private function _getResult($results) {
         $addresses   = $this->_getAddressFromResults($results);
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

    
    private function _checkResultInCache($url) {        
        $id    = md5($url);
        $cache = new FW_Cache();
        $data  = $cache->get($id,"gMapsPluginCache");        
        if ($data!==null) {            
            $contents = $data->getContents();            
            $results  = unserialize($contents);
            return $results;
        }                
    }
    
    private function _setResultInCache($url,$result) {       
        $result = serialize($result);        
        $id     = md5($url);
        $cache  = new FW_Cache();
        return  $cache->save($id,"gMapsPluginCache",$result,2592000);
    }
        




};
?>