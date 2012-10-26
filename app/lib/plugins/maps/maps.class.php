<?php
/**
 * A Google Maps plugin
 *
 * PHP Version 5.3
 *
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * A Google Maps plugin
 *
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class maps extends FW_Plugin_Base {

    /**
     * The map
     *
     * @var map
     */
    private $_map;


    /**
     * @param array $parameters
     */
    protected function _initialize(array $parameters=null) {}

    /**
     * @param array $parameters
     */
    protected function _configure(array $parameters=null) {}


    public function install(array $arguments=array()) {

    }

    public function uninstall(array $arguments=array()) {

    }
    /**
     * Creates a new map
     *
     * @param string $name The name of the map
     * @param string $zoom The zoom of the map
     * @param string $type The type of the map
     *
     * @return void
     */
    public function createMap($name="map",$zoom=10,$type="road") {
        $map        = new map($name,$zoom,$type);
        return $map;
    }

    /**
     * Displays the map
     *
     * @return string
     */
    public function display() {
        if ($this->_map!==null) {
            return $this->_map->display();
        }
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
        if ($this->_map!==null) {
            $this->_map->setCenter($latitude,$longitude);
        }
    }

    /**
     * Sets the center of the map by an address
     *
     * @param string $address The address
     *
     * @return bool
     */
    public function setCenterByAddress($address) {
        if ($this->_map!==null) {
            $this->_map->setCenterByAddress($address);
        }
    }

    



};
?>