<?php
class gmaps_marker extends FW_ActiveRecord_Model {

    protected $id;
    protected $id_map;
    protected $title;
    protected $latitude;
    protected $longitude;
    protected $clickable;
    protected $draggable;
    protected $flat;
    protected $icon;
    protected $visible;
    protected $description;
    protected $username;
    protected $created_at;

    private $_position;

    public function afterFind() {
        $this->_makePosition();
    }

    private function _makePosition() {
        $this->_position = new gMapsLatLng($this->latitude,$this->longitude);
    }


    public function setPosition(gMapsLatLng $position) {
        $this->latitude  = $position->getLatitude();
        $this->longitude = $position->getLongitude();
    }

    public function generateJavaScript($mapName) {
        $code  = "new google.maps.Marker ({\n";
        $code .= "position:".$this->_position->getJavaScript().",\n";
        $code .= "map: {$mapName},\n";
        if (!empty($this->icon)) {
            $code .= "icon: \"{$this->icon}\",\n";
        }
        $code .= "title: \"{$this->title}\" \n";
        $code .= "});\n\n";
        return $code;
    }

    public function generateInfoWindowJavaScript($mapName,$markerName) {
        $code  = "var {$markerName}_infoWindow = new google.maps.InfoWindow ({\n";
        $code .= "content: \"{$this->description}\" });\n";
         
        $code .= "google.maps.event.addListener({$markerName},'click',function() {\n";
        $code .= "{$markerName}_infoWindow.open({$mapName},{$markerName}); });\n";
        return $code;
    }

    public function hasInfoWindow() {
        return (!empty($this->description));
    }


    public static function create($name,$title,$lat,$lng,$text,$icon) {
        $marker = new gMarker (new gLatLng($lat,$lng),$title,$text,$icon);
        $this->_maps[$name]["markers"] []= $marker;
    }

    public function createByAddress($name,$title,$address,$text,$icon) {

    }
};
?>