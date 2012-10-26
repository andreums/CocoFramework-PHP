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
 * @author andreu
 *
 */
class FW_ActiveRecord_Result implements ArrayAccess, Countable, Iterator {

    /**
     * An array with the objects of the result
     *
     * @var array
     */
    protected $_objects;

    /**
     * The name of the results model
     *
     * @var string
     */
    private $_modelName;


    /**
     * The position of the iterator
     *
     * @var int
     */
    private $_position;

    /**
     * Indicates if the iterator is in valid position
     * @var int
     */
    private $_valid;

    /**
     * The constructor for this component
     *
     * @param array $data The data
     */
    public function __construct($data=array()) {

        $this->_position = 0;
        $this->_objects  = array();
        $this->_valid    = false;


        if (count($data)>0) {
            $this->clear();
            $this->_objects   = $data;
            $this->_modelName = get_class($data[0]);
        }

    }

    /**
     * Adds data to this result
     *
     * @param mixed $item The item to add
     *
     * @return void
     */
    public function addData($item) {
        $this->_objects []= $item;
    }

    /**
     * Sets the data of this result
     *
     * @param array $data The data to set
     *
     * @return void
     */
    public function setData(array $data) {
        $this->clear();
        $this->_objects = $data;
    }

    /**
     * Returns the type (name of model)
     * of the stored results
     *
     * @return string
     */
    public function getType() {
        return $this->getResultType();
    }

    /**
     * Clears the objects in the result
     * leaving it empty
     *
     * @return void
     */
    public function clear() {
        $this->_objects = array();
    }

    /**
     * Returns the first item of the result
     *
     * @return mixed
     */
    public function first() {
        if (count($this->_objects)>0) {
            if ($this->_objects[0]!==null && isset($this->_objects[0])) {
                $obj = $this->_objects[0];
                return $obj;
            }
        }
        return null;
    }

    /**
     * Returns the last item of the result
     *
     * @return mixed
     */
    public function last() {
        $last = (count($this->_objects)-1);
        if (isset($this->_objects[$last])) {
            return $this->_objects[$last];
        }
        return null;
    }

    /**
     * Gets the type of the results
     * (the name of the model)
     *
     * @return string
     */
    public function getResultType() {
        if (isset($this->_objects[0])) {
            if (is_object($this->_objects[0])) {
                $reflect = new ReflectionClass($this->_objects[0]);
                return $reflect->name;
            }
        }
    }



    /**
     * Returns the number of data in this result
     *
     * @return number
     */
    public function count() {
        return count($this->_objects);
    }

    /**
     * Returns the number of data in this result
     *
     * @return number
     */
    public function numResults() {
        return $this->count();
    }

    /**
     * Returns true if the result has data, false if none.
     *
     * @return bool
     */
    public function hasResult() {
        return ($this->count()>0);
    }

    /**
     * Returns the last element of the result
     *
     * @return mixed
     */
    public function end() {
        return $this->last();
    }


    function rewind() {
        return reset($this->_objects);
    }

    function current() {
        return current($this->_objects);
    }

    function key() {
        return key($this->_objects);
    }

    function next() {
        return next($this->_objects);
    }

    function valid() {
        return key($this->_objects) !== null;
    }

    public function offsetExists ($offset) {
        return isset($this->_objects[$offset]);
    }

    public function offsetGet($offset) {
        if (isset($this->_objects[$offset])) {
            return $this->_objects[$offset];
        }
    }

    public function offsetSet($offset,$value) {
        throw new FW_ActiveRecord_Exception("This ActiveRecordResult is readonly");
    }

    public function offsetUnset($offset) {
        throw new FW_ActiveRecord_Exception("This ActiveRecordResult is readonly");
    }

    /**
     * Gets the objects stored in this result
     *
     * @return array
     */
    public function getObjects() {
        return $this->_objects;
    }
    
    /**
     * Gets the objects stored in this result
     *
     * @return array
     */
    public function getResult() {
        return $this->getObjects();        
    }


    /**
     * Method to deserialize the result
     *
     * @return mixed
     */
    public function __wakeup() {}

    /**
     * Method to serialize the result
     *
     * @return array
     */
    public function __sleep()  { return array("_objects"); }

    /**
     * Generates an array with the model data
     *
     * @param array $properties The properties to be included in the array
     * @param array $transformations An array of transformations to be applied to
     * a property
     *
     * @return array
     */
    public function toArray(array $properties=array(),array $transformations=array(),array $rename=array()) {
        $data = array();
        if ($this->hasResult()) {
            foreach ($this->_objects as $object) {
                $data []= ($object->toArray($properties,$transformations,$rename));
            }
        }
        return $data;
    }

    /**
     * Generates XML data from the properties of the model
     *
     * @param string $root The name of the root node
     * @param array $properties The properties to be included in the XML
     * @param array $transformations An array of transformations to be applied to
     * a property
     * @param array $cdatas An array with elements that have to be converted to CDATA in XML
     * (ie to include html tags inside XML)
     *
     * @return string
     */
    public function toXML($root,$elementRoot="",array $properties=array(),array $transformations=array(),array $cdatas=array(),array $rename=array()) {
        $xml = "";
        if (empty($root)) {
            $root = $this->_getName();
        }
        $xml .= "<{$root}>";
        foreach ($this->_objects as $object) {
            $xml .= $object->toXML($elementRoot,$properties,$transformations,$cdatas,$rename);
        }
        $xml .= "</{$root}>";
        return $xml;
    }

    /**
     * Generates the JSON representation of this model
     *
     * @param array $properties The properties to be included in the XML
     * @param array $transformations An array of transformations to be applied to a property
     * @param bool $headers Set true to display the headers on the JSON representation
     * @param long $jsonFlags The flags for json_encode function (@see function documentation)
     *
     * @return string
     */
    public function toJSON(array $properties=array(),array $transformations=array(),array $rename=array(),$headers=true,$jsonFlags=null) {
        $json = "[";
        foreach ($this->_objects as $object) {
            $json .= $object->toJSON($properties,$transformations,$rename,$headers,$jsonFlags);
            $json .= ',';
        }
        if ($this->count()>0) {
            $json  = substr($json,0,-1);
        }      
        $json .= "]";
        return $json;
    }

    /**
     * Converts the data of the properties of the result to a CSV format
     *
     * @param array $properties The properties to be converted in the CSV
     * @param array $transformations Transformations to be applied to the properties
     * @param string $delimiter The delimiter for the CSV format
     *
     * @return string
     */
    public function toCSV (array $properties=array(),array $transformations=array(),$delimiter=",") {
        $csv        = "";
        if (empty($properties)) {
            $properties = $this->first()->getModelProperties();
        }
        foreach ($properties as $key=>$value) {
            if (!$value instanceof FW_ActiveRecord_Relation) {
                $csv .= "{$value} {$delimiter}";
            }
        }
        $csv  = substr($csv,0,-1);
        $csv .= "\n";
        foreach ($this->_objects as $object) {
            $csv .= $object->toCSV($properties,$transformations,false,$delimiter);
            $csv  = substr($csv,0,-1);
            $csv .= "\n";
        }

        return $csv;
    }



};
?>