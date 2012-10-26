<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
/**
 * An implementation of the Registry design pattern
 *
 * PHP Version 5.3
 *
 * @package  Registry
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * An implementation of the Registry design pattern
 *
 * @abstract
 * @package Registry
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
abstract class FW_Registry {

    /**
     * An array of objects
     *
     * @var array
     */
    protected $_objects;

    /**
     * Constructs a new Registry
     *
     * @param  $objects
     * @return unknown_type
     */
    public function __construct($objects=array()) {
        if (!empty($objects)) {
            $this->_objects = $objects;
        }
    }

    /**
     * Clears the Registry
     *
     * @return void
     */
    protected function clear() {
        $this->_objects = array();
    }

    /**
     * Gets the number of elements stored in the
     * Registry
     *
     * @return int
     */
    protected function count() {
        return (count($this->_objects));
    }

    /**
     * Gets an element stored in the Registry
     *
     * @param string $key The key of the element
     *
     * @return mixed
     */
    protected function get($key) {
        if (isset($this->_objects[$key])) {
            return $this->_objects[$key];
        }
    }


    /**
     * Stores an object in the Registry
     *
     * @param string $key The key of the object to be stored
     * @param mixed $value The value of the object to be stored
     *
     * @return void
     */
    protected function set($key,$value) {
        $this->_objects[$key] = $value;
    }



    /**
     * Checks if an objects is stored in the Registry
     *
     * @param string $key The key of the object
     *
     * @return bool
     */
    protected function exists($key) {
        if (isset($this->_objects[$key])) {
            return true;
        }
        return false;
    }

    /**
     * Removes an object from the Registry
     *
     * @see   delete
     * @param string $key The key of the object
     *
     * @return bool
     */
    protected function remove($key) {
        return $this->delete($key);
    }

    /**
     * Removes an object from the Registry
     *
     * @param string $key The key of the object
     *
     * @return bool
     */
    protected function delete($key) {

        $original = $this->_objects;
        $new      = array();
        $i        = 0;

        if ($this->exists($key)) {
            $count = count($this->_objects);

            for($i=0;$i<$count;$i++) {
                if (key($this->_objects)==$key) {
                    break;
                }
                next($this->_objects);
            }
            $aux1 = array_slice($original,0,($i-1),true);
            $aux2 = array_slice($original,($i+1),-1,true);
            $new  = array_merge($aux1,$aux2);
            $this->_objects = $new;

            return true;
        }
        return false;
    }

    /**
     * Gets all objects in the Registry
     *
     * @return array
     */
    protected function getAllObjects() {
        return $this->_objects;
    }

    /**
     * Gets all the keys of the stored objects
     *
     * @return array
     */
    protected function keys() {
        if (is_array($this->_objects)) {
            return array_keys($this->_objects);
        }
        return array();
    }

    /**
     * Helper to serialize the Registry
     *
     * @return array
     */
    protected function __sleep() {
        return array("_objects");
    }

    /**
     * Helper to deserialize the Registry
     * @return void
     */
    protected function __wakeup() {}

    /**
     * Gets a serialized version of this registry
     *
     * @return mixed
     */
    protected function serialize() {
        return serialize($this);
    }

    /**
     * Unserializes registry data and replaces the objects
     * with the objects in the data
     *
     * @param mixed $serializedData Serialized data
     * @return void
     */
    protected function unserialize($serializedData) {
        $this->clear();
        $this->_objects = unserialize($serializedData);
    }

    /**
     * Writes registry data to a file
     *
     * @param string $filename The filename
     *
     * @return bool
     */
    public function dumpToFile($filename) {
        $serializedData = $this->serialize();
        if (is_file($filename)) {
            if (file_put_contents($filename,$serializedData)!==false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Loads registry data from a file
     *
     * @param string $filename The filename
     *
     * @return bool
     */
    public function loadFromFile($filename) {
        if (is_file($filename)) {
            $data = file_get_contents($filename);
            if ($data!==null) {
                $this->unserialize($data);
                return true;
            }
            return false;
        }

        return false;
    }

    public function fromArray(array $data=array()) {
        if (count($data)) {
            foreach ($data as $key=>$value) {
                $this->set($key,$value);
            }
        }
        return $this;
    }

    /**
     * Converts all the parameters stored in this
     * registry to an array
     *
     * @return array
     */
    public function toArray() {
        $data = array();
        $keys = $this->keys();
        foreach ($keys as $key) {
            $data[$key] = $this->get($key);
        }
        return $data;
    }

};
?>