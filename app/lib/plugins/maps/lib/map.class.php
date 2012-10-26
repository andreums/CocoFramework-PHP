<?php
/**
 * A Google Maps gMarker wrapper
 *
 * PHP Version 5.3
 *
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * A Google Maps gMarker wrapper
 *
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class map {
    /**
     * The name of the map
     *
     * @var string
     */
    private $_name;

    /**
     * The type of the map
     *
     * @var string
     */
    private $_type;

    /**
     * The zoom of the map
     *
     * @var int
     */
    private $_zoom;

    /**
     * The center of the map
     *
     * @var latLng
     */
    private $_center;

    /**
     * An array of markers
     *
     * @var array
     */
    private $_markers;
    
    /**
     * An array of polylines
     *
     * @var array
     */
    private $_polylines;
    
    /**
     * An array of polygons
     *
     * @var array
     */
    private $_polygons;
    
    /**
     * An array of circles
     *
     * @var array
     */
    private $_circles;

    /**
     * Creates a new map
     *
     * @param string $name The name of the map
     * @param int $zoom The zoom of the map
     * @param string $type The type of the map
     */
    public function __construct($name = "map", $zoom = 10, $type = "road") {

        if (!empty($name)) {
            $this -> setName($name);
        }
        if (is_int($zoom)) {
            $this -> setZoom($zoom);
        }

        if (!empty($type)) {
            $this -> setType($type);
        }

        $this -> _markers = array();
        $this -> _center = new latLng(0, 0);
    }

    /**
     * Sets the name of the map
     *
     * @param string $name The name of the map
     *
     * @return void
     */
    public function setName($name) {
        $this -> _name = $name;
    }

    /**
     * Sets the Zoom of the map
     *
     * @param int $zoom The zoom of the map (values between 1 and 18)
     *
     * @return void
     */
    public function setZoom($zoom) {
        if (is_int($zoom)) {
            if ($zoom > 0 || $zoom < 19) {
                $this -> _zoom = $zoom;
            }
        }
    }

    /**
     * Sets the type of the map
     *
     * @param string $type The type of the map
     */
    public function setType($type) {
        $this -> _type = $type;
    }

    /**
     * Gets the JavaScript map type
     *
     * @return string
     */
    private function _getMapTypeId() {
        $mapType = $this -> _type;
        switch ($mapType) {
            case "hybrid" :
                return "google.maps.MapTypeId.HYBRID";
                break;

            case "satellite" :
                return "google.maps.MapTypeId.SATELLITE";
                break;

            case "terrain" :
                return "google.maps.MapTypeId.TERRAIN";
                break;

            default :
            case "road" :
                return "google.maps.MapTypeId.ROADMAP";
                break;
        };
    }

    /**
     * Sets the center of the map by coordinates
     *
     * @param double $latitude The latitude
     * @param double $longitude The longitude
     *
     * @return void
     */
    public function setCenter($latitude,$longitude) {
        $this -> center_lat = $latitude;
        $this -> center_lon= $longitude;
        $this -> _center      = new latLng($this -> center_lat, $this -> center_lon);
    }

    /**
     * Sets the center of the map by an address
     *
     * @param string $address The address
     * @return bool
     */
    public function setCenterByAddress($address) {
        $geocoder = new geocoder();
        $results = $geocoder -> getCoordinates($address);
        if (count($results) > 0) {
            $coordinates = $results[0]["coordinates"];
            $this -> center_lat = $coordinates -> lat();
            $this -> center_lon = $coordinates -> lng();
            $this -> _center = new latLng($this -> center_lat, $this -> center_lon);
            return true;
        }
        return false;
    }

    /**
     * Adds a marker to the map
     *
     * @param marker $marker The marker to add
     *
     * @return void
     */
    private function _addMarker(marker $marker) {
        $this -> _markers[$marker->getTitle()] = $marker;
    }

    /**
     * Displays the map
     *
     * @return string
     */
    public function display($div="") {
        $code = "";
        //$code .= $this -> _getJavaScriptIncludes();
        //$code .= "<script type=\"text/javascript\">\n";
        $code .= $this -> _getMapJavaScript($div);        
        //$code .= "</script>";
        return $code;
    }

    /**
     * Returns the Include of JavaScript to the Google Maps V3 API
     * @return string
     */
    private function _getJavaScriptIncludes() {
        /*$code = "<script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=true\"> </script>\n";
        return $code;*/
       return "";
    }

    /**
     * Gets the map JavaScript code
     *
     * @return string
     */
    private function _getMapJavaScript($div="") {
        $name = $this -> _name;        
        if (strlen($div)===0) {
            $div = $name;            
        }        
        $code = " markers =  Array();\n";
        $code .= " {$name}_options = {\n \t zoom: {$this->_zoom},\n \t center: " . $this -> _center -> getJavaScript() . ",\n \t mapTypeId: " . $this -> _getMapTypeId($name) . " \n};\n";
        $code .= "{$name} = new google.maps.Map(document.getElementById(\"{$div}\"),{$name}_options);\n\n";
        $code .= $this -> _getMarkersJavaScript($name);
        $code .= $this->_getPolylinesJavaScript($name);
        $code .= $this->_getPolygonsJavaScript($name);
        $code .= $this->_getCirclesJavaScript($name);
        return $code;
    }

    /**
     * Gets the JavaScript code of the markers
     *
     * @return string
     */
    private function _getMarkersJavaScript() {
        $i             = 0;
        $code    = "";
        $markers = $this -> _markers;
        foreach ($this->_markers as $marker) {
            //$name = "{$this->_name}_marker{$i}";
            $name = $marker->getId();
            $code .= "var {$name} = " . $marker -> generateJavaScript($this -> _name) . "\n";
            $code .= $marker->generateInfoWindowJavaScript($this -> _name, $name) . "\n";
            $code .= $marker->generateMouseEventsJavaScript($this -> _name,$name)."\n";
            $code .= "markers.{$name} = {$name};\n";
            $i++;            
            
        }
        return $code;
    }
    
    
    private function _getPolylinesJavaScript($name) {
        $i              = 0;
        $code    = "";
        $lines     = $this->_polylines;
        if (count($lines)) {
            foreach ($lines as $line) {            
                $code .= $line->display($name);
                $i++;
            }
        }
        return $code;        
    }
    
    private function _getCirclesJavaScript($name) {
        $i              = 0;
        $code    = "";
        $circles = $this->_circles;
        if (count($circles)) {
            foreach ($circles as $circle) {            
                $code .= $circle->display($name);
                $i++;
            }
        }
        return $code;        
    }
    
    private function _getPolygonsJavaScript($name) {
        $i              = 0;
        $code    = "";
        $lines     = $this->_polygons;
        if (count($lines)) {
            foreach ($lines as $line) {            
                $code .= $line->display($name);
                $code .= $line->generateMouseEventsJavaScript($name,$line->getName());
                $i++;
            }
        }
        return $code;        
    }

    /**
     * Creates a Marker and adds to the map
     *
     * @param mixed $position The position of the marker
     * @param string $title The title of the marker
     * @param string $text The text to the infowindow of the marker
     * @param string $icon An url to an icon for the marker
     */
    public function addMarker($position, $title="", $text = "", $icon = "",array $extra=array()) {
        if (is_a($position,"marker")) {
            $this->_addMarker($position);
        }        
        else {
            $marker = new marker($position, $title, $text, $icon);
            if (!empty($extra)) {
                $marker->setExtraData($extra);
            }
            return $this->_addMarker($marker);
        }
    }
    
    
    public function appendMarker(marker $marker) {        
        return $this->_addMarker($marker);
    }
    
    
    public function getMarker($title) {
        if (isset($this->_markers[$title])) {
            return $this->_markers[$title];
        }        
    }
    
    public function getMarkers() {
        return $this->_markers;
    }
    
    
    public function addPolyline(polyline $polyline) {
        $this->_polylines []= $polyline;
    }
    
    public function addPolygon(polygon $polygon) {
        $this->_polygons []= $polygon;
    }
    
    public function addCircle(circle $circle) {
        $this->_circles []= $circle;
    }

};
?>