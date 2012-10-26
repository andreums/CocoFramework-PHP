<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_ActiveRecord_Model extends FW_ActiveRecord {

	/**
	 * An array to store the values of the primary key
	 *
	 * @var array
	 */
	protected $_oldPrimaryKey;

	/**
	 * A flag to indicate if this is a new record
	 * or an instancie of a row of a table on the
	 * database
	 *
	 * @var bool
	 */
	protected $_isNewRecord;

	/**
	 * An array to store the foreign keys of a
	 * model
	 *
	 * @var array
	 */
	protected $_foreignKeys;

	/**
	 * Flag to indicate if the object is readonly or not
	 *
	 * @var bool
	 */
	protected $_isReadOnly;

	protected $_oldValues;

	protected $_textProperties;

	/**
	 * Construct a model
	 *
	 * @param array $data An array with the data of the model
	 * @param bool $relations Check the relations of the model?
	 * @param FW_ActiveRecord_Model $parent The parent model of this model
	 *
	 * @return void
	 */
	public function __construct(array $data = null, $relations = false, &$parent = null) {

		$this -> _isNewRecord = true;
		$this -> _isReadOnly = false;

		if ($data !== null) {
			$this -> _setData($data);
		} else {
			if (method_exists($this, "afterCreate")) {
				$this -> afterCreate();
			}
		}

		if ($relations === true) {
			$this -> _loadRelations();
		}

	}

	/**
	 * Sets the data on this model object
	 *
	 * @param array $data An array with the data to set
	 *
	 * @return void
	 */
	protected function _setData(array $data) {
		$this -> _isNewRecord = false;
		foreach ($data as $key => $value) {
			$this -> $key = $value;
		}
		$this -> _oldValues = $data;
	}

	/**
	 * Gets the name of this model
	 *
	 * @return string
	 */
	final protected function _getName() {
		$name = get_class($this);
		return $name;
	}

	final protected function _getSchema() {
		/*if (getcwd() === "/") {
			$path = FW_Config::getInstance() -> get("core.global.basePath");
			chdir($path);
		}*/
		$name = $this -> _getName();
		$schema = FW_ActiveRecord_Metadata_Manager::getInstance() -> getSchema($name);
		if ($schema === null) {
			// TODO: Complete the exception
			throw new FW_ActiveRecord_Exception("...");
		}
		return $schema;
	}

	/**
	 * Gets the value of a property
	 *
	 * @param  string $property The name of the property
	 *
	 * @return mxied
	 */
	final public function __get($property) {
		if (strlen($property) > 0) {
			if ($property[0] !== "_") {
				return $this -> $property;
			}
		}
	}

	/**
	 * Sets the value of a property
	 *
	 * @param string $property The name of the property
	 * @param mixed $value The value of the property
	 *
	 * @return void
	 */
	final public function __set($property, $value) {

		if (!isset($this -> _foreignKeys)) {
			$this -> _foreignKeys = array();
			$schema = $this -> _getSchema();
			$relations = $schema -> getRelations();
			foreach ($relations as $relation) {
				$this -> _foreignKeys[$relation["srcColumn"]] = $relation["property"];
			}
		}

		if (strlen($property) > 0) {
			if ($property[0] !== "_") {
				if ($this -> _isReadOnly) {
					throw new FW_ActiveRecord_Exception("Can't set data in this object. This object is readonly and isn't writeable.");
				}
				if (in_array($property, array_keys($this -> _foreignKeys))) {
					if ($this -> _isNewRecord === false) {
						$relation = $this -> _foreignKeys[$property];
						$relation = $this -> $relation;
						$relation -> setOldValue($value);
					} else {
						$this -> $property = $value;
					}
				}
				$this -> $property = $value;
			}
		}
	}

	/**
	 * Sets the object to be readonly
	 * If anyone tries to set a value of a property
	 * it will raise an exception
	 *
	 * @return void
	 */
	public function readOnly() {
		$this -> _isReadOnly = true;
	}

	/**
	 * Sets the object to be writeable
	 *
	 * @return void
	 */
	public function writeable() {
		$this -> _isReadOnly = false;
	}

	/**
	 * Generates an array with the model data
	 *
	 * @param array $properties The properties to be included in the array
	 * @param array $transformations An array of transformations to be applied to
	 * a property
	 *
	 * @return array
	 */
	public function toArray(array $properties = array(), array $transformations = array(), array $rename = array()) {
		$data = array();
		if (empty($properties)) {
			$schema = $this -> _getSchema();
			if ($schema === null) {
				throw new FW_ActiveRecord_Exception("...");
			}
			$properties = $schema -> getModelProperties();
		}

		foreach ($properties as $property) {
			$value = $this -> $property;
			if (!empty($transformations)) {
				if (isset($transformations[$property])) {
					foreach ($transformations[$property] as $key => $transformation) {
						if (!is_array($transformation)) {
							$value = call_user_func(array($this, $transformation), $value);
						} else {
							if (count($transformation) > 1) {
								$value = array($value);
								$parameters = array_merge($transformation[1]);
								$value = call_user_func_array(array($this, $transformation[0]), $parameters);
							} else {
								$value = call_user_func(array($this, $transformation), $value);
							}

						}
					}
				}
			}
			if (count($rename) > 0) {
				if (isset($rename[$property])) {
					$property = $rename[$property];
				}
			}
			$data[$property] = $value;
		}

		return $data;
	}

	public function toCSV(array $properties = array(), array $transformations = array(), $headers = true, $delimiter = ',') {
		$csv = "";
		$data = $this -> toArray($properties, $transformations);
		if ($headers === true) {
			foreach ($data as $property => $value) {
				$csv .= "{$property} {$delimiter} ";

			}
			$csv = substr($csv, 0, -1);
			$csv .= "\n";
		}

		foreach ($data as $property => $value) {
			if ($value instanceof FW_ActiveRecord_Relation) {
				$value = $value -> getOldValue();
			}
			if (is_string($value)) {
				if (strpos($value, ',') !== false) {
					$value = '"' . $value . '"';
				}
				$value = preg_replace("/\r\n/", " ", $value);
				$value = preg_replace("/\n/", " ", $value);

			}
			$csv .= "{$value} {$delimiter}";

		}
		$csv = substr($csv, 0, -1);
		$csv .= "\n";

		return $csv;
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
	public function toXML($root = "", array $properties = array(), array $transformations = array(), array $cdatas = array(), array $rename = array()) {
		$xml = "";
		if (empty($root)) {
			$root = $this -> _getName();
		}
		$data = $this -> toArray($properties, $transformations, $rename);
		if ($data !== null) {

			$xml .= "<{$root}>";
			foreach ($data as $key => $value) {
				$xml .= "<{$key}>";
				if ($value instanceof FW_ActiveRecord_Relation) {
					$value = $value -> getOldValue();
				}
				$value = utf8_encode($value);
				if (in_array($key, $cdatas)) {
					$xml .= "<![CDATA[{$value}]]>";
				} else {
					if (strpos($value, "<") !== false || strpos($value, ">") !== false) {
						$xml .= "<![CDATA[{$value}]]>";
					} else {
						$xml .= "{$value}";
					}
				}
				$xml .= "</{$key}>";
			}
			$xml .= "</{$root}>";
		}
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
	public function toJSON(array $properties = array(), array $transformations = array(), array $rename = array(), $headers = true, $jsonFlags = null) {
		$json = "";

		$data = $this -> toArray($properties, $transformations, $rename);
		if (!$headers) {
			$data = array_values($data);
		}

		if (count($data) > 0) {
			foreach ($data as $key => $value) {
				if ($value instanceof FW_ActiveRecord_Relation) {
					$value = $value -> getOldValue();
				}
				if (!is_array($value)) {
					$value = utf8_decode($value);
				}
				$data[$key] = $value;
			}
		}

		/*array_walk_recursive($data, function(&$item, $key) {
		 if(is_string($item)) {
		 $item = htmlentities($item);
		 }
		 });*/

		if ($jsonFlags === null) {
			$json = json_encode($data);
		} else {
			$json = json_encode($data, $jsonFlags);
		}

		// boolean problem
		$json = str_replace("\"false\"", "false", $json);
		$json = str_replace("\"true\"", "true", $json);
		return $json;
	}

	/**
	 * Generates an object from JSON data
	 *
	 * @param string $json The JSON data
	 * @param bool $relations True to check relations once the object is created
	 *
	 * @return FW_ActiveRecord_Model
	 */
	public static function fromJSON($json = "", $relations = false) {
		$model = null;
		if (!empty($json)) {
			$data = json_decode($json, true);
			$model = self::fromArray($data, $relations);
		}
		return $model;
	}

	/**
	 * Generates an object from XML data
	 *
	 * @param string $xml The XML data
	 * @param bool $relations True to check relations once the object is created
	 *
	 * @return FW_ActiveRecord_Model
	 */
	public static function fromXML($xml = "", $relations = true) {
		$model = null;
		if (!empty($xml)) {
			$data = FW_Util_XML::XML2Array($xml);
			$model = self::fromArray($data, $relations);
		}
		return $model;
	}

	/**
	 * Generates an object from an associative array
	 *
	 * @param arrat $data An array with the data
	 * @param bool $relations True to check relations once the object is created
	 *
	 * @return FW_ActiveRecord_Model
	 */
	public static function fromArray(array $data, $relations = false) {
		$model = null;
		if (!empty($data)) {
			$class = get_called_class();
			$model = new $class($data, $relations);
		}
		return $model;
	}

	/**
	 * Obtains an array with the properties of the model
	 *
	 * @return array
	 */
	public function getModelProperties() {
		$schema = $this -> _getSchema();
		if ($schema !== null) {
			$properties = $schema -> getModelProperties();
		}
		return $properties;
	}

	/**
	 * Returns the serialized representation of the data of this object
	 *
	 * @return string
	 */
	public function serialize() {
		return serialize($this);
	}

	/**
	 * Method to deserialize the object
	 *
	 * @return void
	 */
	public function __wakeup() {
	}

	/**
	 * Method to serialize the object
	 *
	 * @return array
	 */
	public function __sleep() {
		$schema = $this -> _getSchema();
		if ($schema === null) {
			throw new FW_ActiveRecord_Exception("...");
		}
		$properties = $schema -> getModelProperties();
		return $properties;
	}

	protected function _encode() {
		$config = FW_ActiveRecord_Configuration::getInstance();
		if (!$config -> isUsingUTF8()) {
			$properties = $this -> _getSchema() -> getTextProperties();
			if (count($properties) > 0) {
				foreach ($properties as $property) {
					if (!$this -> $property instanceof FW_ActiveRecord_Relation) {
						$this -> $property = (utf8_encode($this -> $property));
					}
				}
			}
		}
	}

	protected function _decode() {
		$config = FW_ActiveRecord_Configuration::getInstance();
		if (!$config -> isUsingUTF8()) {
			$properties = $this -> _getSchema() -> getTextProperties();
			if (count($properties) > 0) {
				foreach ($properties as $property) {
					if (!$this -> $property instanceof FW_ActiveRecord_Relation) {
						$this -> $property = (utf8_decode($this -> $property));
					}
				}
			}
		}
	}

	protected function _getBaseURL() {
		return (rtrim(FW_Config::getInstance() -> get("core.global.baseURL"), '/'));
	}

	protected function database() {
		return FW_Database::getInstance();
	}

};
?>
