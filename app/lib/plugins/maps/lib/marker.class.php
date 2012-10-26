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
class marker {
    
    private $_id;    
    
    /**
     * The position of the marker
     * 
     * @var mixed
     */
    private $_position;
    
    /**
     * The title of the marker
     * 
     * @var string
     */
    private $_title;
        
    /**
     * The text of the InfoWindow of the marker
     * 
     * @var string
     */
    private $_text;
    
    
    /**
     * The icon of the marker
     * 
     * @var string
     */
    private $_icon;
    
    private $_onMouseDown;
    private $_onMouseOut;
    private $_onMouseOver;        
    private $_onMouseUp;
    
    
    private $_extraData;
    
    public function setMouseDown($code) {
        $this->_onMouseDown = $code;        
    }
    
    public function setMouseOut($code) {
        $this->_onMouseOut = $code;        
    }
    
    public function setMouseOver($code) {
        $this->_onMouseOver = $code;        
    }
    
    public function setMouseUp($code) {
        $this->_onMouseUp = $code;        
    }
    
    public function getId() {        
        $id = html_entity_decode($this->_id,ENT_QUOTES,"UTF-8");        
        //$id = str_replace('_','-',FW_Util_Url::seoUrl($id));        
        return $id;        
    }
    
    /**
     * Creates a Marker
     * 
     * @param mixed $position The position of the marker
     * @param string $title The title of the marker
     * @param string $text The text to the infowindow of the marker
     * @param string $icon An url to an icon for the marker
     */
    public function __construct($position,$id,$title,$text="",$icon="") {
        $this->_position = $position;
        $this->_title    = $title;
        $this->_text     = $text;
        $this->_icon     = $icon;
        $this->_id          = $id;

        if (!$position instanceof latLng) {            
            $geocoder = new geocoder();
            $result   = $geocoder->getCoordinates($position);                        
            if (!empty($result)) {
                $this->_position = $result[0]["coordinates"];
            }
            else {
                $this->_position = new latLng(0,0);              
            }
        }
    }
    
    private function _generateExtraData() {
        $code = "";
        if ($this->_extraData!==null) {
            foreach ($this->_extraData as $key=>$value) {
                $code .= " {$key}:\"{$value}\" ,";
            }
            $code = substr($code,0,-1);           
        }        
        return $code;
    }

    
    /**
     * Generates the javascript code for the marker
     * 
     * @param string $mapName The name of the map
     * 
     * @return string
     */
    public function generateJavaScript($mapName) {           
        $code  = "new google.maps.Marker ({\n";
        $code .= "\t position: ".$this->_position->getJavaScript().",\n";
        $code .= "\t map: {$mapName},\n";
        if (!empty($this->_icon)) {
            $code .= "\t icon: \"{$this->_icon}\",\n";
        }
        $code .= "\t title: \"{$this->_title}\", \n";
        $code .= $this->_generateExtraData();
        $code .= "});\n\n";        
        return $code;
    }
    
    

    /**
     * Generates the javascript code for the infowindow of the marker
     * 
     * @param string $mapName The name of the map
     * @param string $markerName The name of the marker
     * 
     * @return string
     */
    public function generateInfoWindowJavaScript($mapName,$markerName) {
        $code  = "var {$markerName}_infoWindow = new google.maps.InfoWindow ({\n";
        $code .= "\t content: \"{$this->_text}\" \n});\n";
         
        $code .= "google.maps.event.addListener({$markerName},'click',function() {\n";
        $code .= "\t {$markerName}_infoWindow.open({$mapName},{$markerName}); \n});\n";
        return $code;
    }
    
    
    /**
     * Generates the javascript code for the mouse events of the marker
     * 
     * @param string $mapName The name of the map
     * @param string $markerName The name of the marker
     * 
     * @return string
     */
    public function generateMouseEventsJavaScript($mapName,$markerName) {
        $code = "";
        if ($this->_onMouseDown!==null) {
            $code .= "google.maps.event.addListener({$markerName},'mousedown',function(event) {\n";
            $code .= "\t {$this->_onMouseDown} \n});\n";            
        }
        if ($this->_onMouseOver!==null) {
            $code .= "google.maps.event.addListener({$markerName},'mouseover',function(event) {\n";
            $code .= "\t {$this->_onMouseOver} \n});\n";            
        }
        if ($this->_onMouseUp!==null) {
            $code .= "google.maps.event.addListener({$markerName},'mouseup',function(event) {\n";
            $code .= "\t {$this->_onMouseUp} \n});\n";            
        }
        if ($this->_onMouseOut!==null) {
            $code .= "google.maps.event.addListener({$markerName},'mouseout',function(event) {\n";
            $code .= "\t {$this->_onMouseOut} \n});\n";            
        }
        return $code;
    }
    
    public function setExtraData(array $data=array()) {
        $this->_extraData = $data;
    }


    /**
     * Checks if this marker has an infowindow
     * 
     * @return boolean
     */
    public function hasInfoWindow() {
        return (!empty($this->_text));
    }
    
    
    public function getTitle() {
        return $this->_title;
    }
    
    public function getInfoWindowText() {
        return $this->_text;
    }
    
    public function getPosition() {
        return $this->_position;
    }

};
?>