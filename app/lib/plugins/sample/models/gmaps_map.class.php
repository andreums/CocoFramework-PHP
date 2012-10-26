<?php
class gmaps_map extends FW_ActiveRecord_Model {

    protected $id;
    protected $name;
    protected $center_lat;
    protected $center_lon;
    protected $type;
    protected $zoom;
    protected $controls;
    protected $width;
    protected $height;
    protected $draggable;
    protected $username;
    protected $created_at;
    protected $title;
    protected $description;

    protected $_center;

    public static $has_many = array (
    array(
            "property" => "markers",
            "table"    => "gmaps_marker",
            "srcColumn"=> "id",
            "dstColumn"=> "id_map",
            "update"   => "restrict",
            "delete"   => "restrict"
            )
            );

            private function _getMapTypeId() {
                $mapType = $this->type;
                switch ($mapType) {
                    case "hybrid":
                        return "google.maps.MapTypeId.HYBRID";
                        break;

                    case "satellite":
                        return "google.maps.MapTypeId.SATELLITE";
                        break;

                    case "terrain":
                        return "google.maps.MapTypeId.TERRAIN";
                        break;

                    default:
                    case "road":
                        return "google.maps.MapTypeId.ROADMAP";
                        break;
                }
            }

            public function afterFind() {
                $this->_makeCenter();
            }

            private function _makeCenter() {
                $this->_center = new gMapsLatLng($this->center_lat,$this->center_lon);
            }

            public function setCenter($latitude,$longitude) {
                $this->center_lat = $latitude;
                $this->center_lon = $longitude;
            }

            public function setCenterByAddress($address) {
                $geocoder = new gMapsGeocoder("json","true");
                $results  = $geocoder->getCoordinates($address);
                if (count($results)>0) {
                    $results[0]["coordinates"];
                    return true;
                }
                return false;
            }

            public function display() {
                $code = "";
                $code .= $this->_getJavaScriptHeaderAndIncludes();
                $code .= $this->_getMapJavaScriptWithOptions();
                $code .= "</script>";
                return $code;
            }


            private function _getJavaScriptHeaderAndIncludes() {
                $code  = "<script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=true\"> </script>\n";
                $code .= "<script type=\"text/javascript\">\n";
                return $code;
            }

            private function _getMapJavaScriptWithOptions() {
                $name = $this->name;
                $code  =  "var {$name}_options = { zoom: {$this->zoom}, center: ".$this->_center->getJavaScript().",mapTypeId: ".$this->_getMapTypeId($name)." };\n";
                $code .=  "var {$name} = new google.maps.Map(document.getElementById(\"{$name}\"),{$name}_options);\n";
                $code .=  $this->_getMarkersJavaScript($name);

                return $code;
            }


            private function _getMarkersJavaScript() {
                $markers = $this->markers;
                $code    = "";

                for ($i=0;$i<count($markers);$i++) {
                    $markerName = "{$this->name}_marker{$i}";
                    $code      .= "var {$markerName} = ".$markers[$i]->generateJavaScript($this->name)."\n";
                    $code      .=  $markers[$i]->generateInfoWindowJavaScript($this->name,$markerName)."\n";
                }

                return $code;

            }



};
?>