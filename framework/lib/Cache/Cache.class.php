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
 * A Cache memory implementation
 *
 * PHP Version 5.3
 *
 * @package  Cache
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * Class that implements a Cache
 *
 * @package Cache
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Cache implements IComponent {



    /**
     * The driver used for the cache
     *
     * @var object
     */
    private $_driver;


    /**
     * A flag to indicate if caching is enabled
     *
     * @var bool
     */
    private $_status;

    /**
     * An array of extra parameters for configuration
     *
     * @var array
     */
    private $_params;


    /**
     * An array wich holds the configuration data
     * for the Cache system
     *
     * @var array
     */
    private $_config;
    
    
    private static $_instance;



    /**
     * The constructor of Cache
     * @access public
     *
     * @return void
     */
    public function __construct(FW_Container_Parameter $parameters=null) {
        $this->configure($parameters);
        $this->initialize(array());

    }
    
    public static function getInstance() {
        if (self::$_instance===null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

     /**
     * Configures the cache
     *
     *
     * @return void
     */
    public function configure(FW_Container_Parameter $parameters=null) {

        $enabled = "";
        $driver  = "";
        $params  = array();

        if ($parameters===null) {
            $config  = FW_Environment::getInstance()->getCache();
            if ($config!==null) {
                $this->_config = $config;
                $configs = array_keys($config);
                if (!empty($configs)) {
	                $default = $configs[0];                    
	                $enabled = $config[$default]->enabled;                    
	                $driver  = $config[$default]->driver;
	                if (isset($config[$default]->parameters)) {
	                    $params  = $config[$default]->parameters;
	                }
                }
                else {
                    $driver  = false;
                    $enabled = false;
                }
            }
            else {
                throw new FW_Exception("Cache config couldn't be initialized");
            }
        }
        else {
            if ($parameters->hasParameter("enabled")) {
                $enabled = $parameters->getParameter("enabled");
            }
            if ($parameters->hasParameter("driver")) {
                $driver  = $parameters->getParameter("driver");
            }
            if ($parameters->hasParameter("parameters")) {
                $params  = $parameters->getParameter("parameters");
            }
        }
        if ($enabled==="" || $driver==="" ) {
            throw new FW_Exception("Couldn't configure Cache");
        }

        $this->_status = $enabled;
        $this->_driver = $driver;
        $this->_params = $params;
    }

    /**
     * Initializes the Cache
     *
     * @return void
     */
    public function initialize(array $arguments=null) {
        if ($this->_status===true) {
            $driver         = $this->_driver;
            $params         = new FW_Container_Parameter();
            $params->fromArray($this->_params);
            $this->_loadDriver($driver,$params);
        }
    }


    public function useConfiguration($name) {
        if (isset($this->_config[$name])) {
            $config     = $this->_config[$name];

            $parameters = new FW_Container_Parameter();
            $parameters->enabled    = $config->enabled;
            $parameters->driver     = $config->driver;
            if (isset($config->parameters)) {
                $parameters->parameters = $config->parameters;
            }

            $this->configure($parameters);
            $this->initialize(array());

        }
        else {
            throw new FW_Cache_Exception("Couldn't find a configuration named {$name}");
        }
    }


    private function _loadDriver($driver,FW_Container_Parameter $parameters=null) {
        $driver        = ucfirst($driver);
        $driver        = "FW_Cache_Driver_{$driver}";
        $this->_driver = new $driver($parameters);
    }


    /**
     * Removes all data of a namespace from the cache
     *
     * @access public
     *
     * @param string $namespace The namespace to remove
     *
     * @return mixed
     */
    public function clean($namespace) {
        return $this->_driver->clean($namespace);
    }

    /**
     * Obtains data from the cache
     *
     * @access public
     *
     * @param  string $id The id of the data stored in the cache
     * @param  string $namespace The namespace of the data stored in the cache
     * @return mixed
     */
    public function get($id,$namespace) {
        return $this->_driver->get($id,$namespace);
    }

    /**
     * Stores data into the cache
     * @see save
     * @access public
     *
     *
     * @param  string $id The id of the data stored in the cache
     * @param  string $namespace The namespace of the data stored in the cache
     * @param  mixed  $value The value to store in cache
     * @param  double $lifetime The lifetime of the data to be stored
     *
     * @return mixed
     */
    public function set ($id, $namespace, $value, $lifetime) {
        return $this->save($id,$namespace,$value,$lifetime);
    }

    /**
     * Stores data into the cache
     *
     * @access public
     * @param  string $id The id of the data stored in the cache
     * @param  string $namespace The namespace of the data stored in the cache
     * @param  mixed  $value The value to store in cache
     * @param  double $lifetime The lifetime of the data to be stored
     *
     * @return mixed
     */
    public function save($id, $namespace, $value, $lifetime) {        
        return $this->_driver->save($id,$namespace,$value,$lifetime);
    }


    /**
     * Removes data from cache
     *
     * @access public
     * @param string $id The id of the contents
     * @param string $namespace A namespace
     * @return bool
     */
    public function remove($id, $namespace) {
        return $this->_driver->remove($id,$namespace);
    }

    /**
     * Checks if Cache is active
     * @access public
     *
     * @return bool
     */
    public static function isEnabled() {
        return FW_Cache::getInstance()->enabled();
    }

    public function enabled() {
        return (bool) $this->_status;
    }
};
?>