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
 * A Config system implementation
 *
 * PHP Version 5.3
 *
 * @package  Config
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * A Config system implementation
 *
 * @package Config
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Config implements IComponent {


    private static $_isConfigured;

    /**
     * The data for the configuration
     *
     * @var array
     * @static
     *
     */
    private static $_data;


    /**
     * The instance of the Config component
     *
     * @var FW_Config
     * @static
     *
     */
    private static $_instance;


    /**
     * An array of FW_Config_Handler handlers
     *
     * @var array
     * @static
     */
    private static $_handlers;


    /**
     * An access to the Config_Cache component
     *
     * @var FW_Config_Cache
     */
    private static $_cache;
    
    private static $_loadedFiles;


    /**
     * The constructor of the FW_Config component
     *
     * @return void
     */
    public function __construct() {        
        if (self::$_isConfigured===null) {
            $this->configure(null);
            $this->initialize(array());
        }
    }

    /**
     * Gets the instance of the Config component
     * (see Singleton design pattern)
     *
     * @static
     * @return FW_Config
     */
    public static function getInstance() {
        if (self::$_instance===null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /*
     * Configures the Config component
     *
     * @return void
     */
    public function configure(FW_Container_Parameter $parameters=null) {
        self::$_handlers     = array();        
        self::$_data         = array();
        self::$_loadedFiles  = array();
        self::$_isConfigured = true;
    }

    /*
     * Initializes the Config component
     *
     * @return void
     */
    public function initialize(array $arguments=array()) {
        self::$_cache  = FW_Config_Cache::getInstance();
        $path          = BASE_PATH."framework/config";
        $this->_discoverConfigFiles($path);        
        $path          = BASE_PATH."app/config";
        $this->_discoverConfigFiles($path);        
    }
    
    /**
     * Discovers the configuration files
     *
     * @param string $path The path where the config files reside
     *
     * @return void
     */
    private function _discoverConfigFiles($path) {        
        $handle = opendir($path);
        while($file=readdir($handle)) {
            $name = substr($file,0,-4);            
            if (substr($file,-4)===".php") {
                //require "{$path}/{$file}";                
                self::$_loadedFiles[$name] = "{$path}/{$file}";                
            }            
        }  
        closedir($handle);
    }
    
    /**
     * Reloads a configuration 
     * 
     * @param string $name The name of the configuration to reload
     * 
     * @return void
     */
    public function reload($name) {
        if (isset(self::$_loadedFiles[$name])) {
            $filename = self::$_loadedFiles[$name];
            self::$_data[$name] = array();
            self::$_cache->cleanConfigCache($name);            
            require $filename;
        }
    }
    
    
    /**
     * Gets the filename of a configuration
     *
     * @param string $name The name of the configuration
     *
     * @return mixed
     */    
    public function getFileForConfig($name) {
        if (isset(self::$_loadedFiles[$name])) {
            return self::$_loadedFiles[$name];
        }
    }


    /**
     * Creates a configuration
     *
     * @param string $name The name of the config
     *
     * @return void
     */
    public function _createConfig($name) {
        if (!isset(self::$_data[$name])) {
            self::$_data[$name] = array(
                    "sections" => array(),
                    "global"  => array()
            );
        }
    }

    /**
     * Creates a configuration
     *
     * @param string $name The name of the config
     * @static
     *
     * @return void
     */
    public static function createConfig($name) {
        FW_Config::getInstance()->_createConfig($name);
    }


    /**
     * Extracts an array of parameters and gets the PHP code
     * to get that parameters
     *
     * @param string $parameters The parameters in the form
     * config.section.foo.bar.tar
     *
     * @return string
     */
    private function _extractParameters($parameters) {
        $parameters  = explode('.',$parameters);
        $code        = "self::\$_data";        
        foreach ($parameters as $parameter) {
            $code .= '["'.$parameter.'"]';
        }
        $code .= " ";
        return $code;
    }

    private function _extractConfigParameter($parameters) {
        $parameters  = explode('.',$parameters);
        return $parameters[0];
    }

    /**
     * Checks if a config value exists
     *
     * @param string $key The key for the config
     *
     * @return bool
     */
    public function exists($key) {
        $exists = false;
        $key    = explode('.',$key);

        if (count($key)>2) {
            $config  = $key[0];
            if (isset(self::$_data[$config])) {
                $section = $key[1];
                if ( ($section==="global") || ($section==="sections") ) {
                    $key    = implode('.',$key);
                    $code   = $this->_extractParameters($key);
                    $code   = "\$exists = isset({$code});";
                    eval($code);
                }
            }

        }
        return $exists;
    }
     

    /**
     * Gets the value of the config
     *
     * @param string $key The key for the config
     *
     * @return mixed
     */
    public function get($key) {                        
        return $this->getValue($key);
    }

    /**
     * Gets the value of the config
     *
     * @param string $key The key for the config
     *
     * @return mixed
     */
    public function getValue($key) {        
        $value  = null;
        $exists = null;                
        /*if (self::$_cache->hasKey($key)) {
            $value = self::$_cache->getValue($key);
            return $value;
        } */       
        $config = $this->_extractConfigParameter($key);        
        if (!$this->_existsConfig($config)) {            
            if ($this->_existsConfigFile($config)) {                    
                $this->_loadConfigFile($config);
            }
            else {
                throw new FW_Exception("Can't locate configuration file for config {$config}");                    
            }                
        }
                    
        $code = $this->_extractParameters($key);        
        $set     = "\$exists = isset({$code});";
        eval($set);
        if ($exists) {                        
            $code = "\$value={$code};";
            eval($code);            
            //self::$_cache->setValue($key,$value);
        }
        return $value;
    }

    private function _loadConfigFile($config) {
        $file = self::$_loadedFiles[$config];                    
        require_once $file;
    }
    
    private function _existsConfig($config) {
        return (isset(self::$_data[$config]));
    }
    
    private function _existsConfigFile($config) {
        return (isset(self::$_loadedFiles[$config]));
    }


    /**
     * Sets the value of an existing config parameter
     *
     * @param string $key The parameter
     * @param mixed  $value The value to set
     *
     * @return void
     */
    public function setValue($key,$value) {        
        if ($this->exists($key)) {
            $code = $this->_extractParameters($key);
            $code = "{$code} = \$value;";
            eval($code);
            self::$_cache->setValue($key,$value);
        }
    }

    /**
     * Creates a new value on a config and sets its value
     *
     * @param string $key The parameter to set
     * @param mixed $value The value to set
     *
     * @return void
     */
    public function addValue($key,$value) {        
        $keyCopy = explode('.',$key);
        $config  = $keyCopy[0];
        if (count($keyCopy)>1) {
            $section = $keyCopy[1];
            if ($section==="global" || $section==="sections") {
                if (isset(self::$_data[$config])) {
                    $copy = $this->_extractParameters($key);
                    
                    array_pop($keyCopy);                    
                    $test = $this->_extractParameters(implode('.',$keyCopy));
                    $code = "\$exists = isset({$test});";                    
                    eval($code);                    
                    if ($exists===true) {                              
                        if (is_array($this->get(implode('.',$keyCopy)))) {
                            $code = "{$copy} = \$value;";
                            eval($code);                    
                            self::$_cache->setValue($key,$value);                            
                        }                        
                    }
                }
            }
        }

    }
     

    /**
     * Gets the global parameters of a config
     *
     * @param string $config The name of the config
     *
     * @return array
     */
    public function getGlobal($config) {
        if (isset(self::$_data[$config]["global"])) {
            return self::$_data[$config]["global"];
        }
    }


    /**
     * Gets the sections of a config
     *
     * @param string $config The name of the config
     *
     * @return array
     */
    public function getSections($config) {
        if (isset(self::$_data[$config]["sections"])) {
            return self::$_data[$config]["sections"];
        }
    }

    /**
     * Sets a value on the config
     *
     * @param string $config The name of the config to set
     * @param array $value The value for the config
     *
     * @return void
     */
    public function set($config,array $value) {
        if (!isset(self::$_data[$config])) {
            return;
        }
        else {
            self::$_data[$config] = $value;
        }
    }


    /**
     * Sets the value of a configuration
     *
     * @param $config The key of the config to set
     * @param array $value The value of the key
     * @static
     *
     * @return void
     */
    public static function setConfig($config,array $value) {
        FW_Config::getInstance()->set($config,$value);
    }


    /**
     * Gets the name of the configurations stored
     * in this configuration system
     *
     * @return array
     */
    public function getConfigs() {
        return array_keys(self::$_data);
    }
    
    /**
     * Checks if there is a configuration stored
     * 
     * @param string $name The name of the configuration
     * @return boolean
     */
    public function hasConfig($name) {
        return (isset(self::$_data[$name]));
    }

    /**
     * Loads a config handler
     *
     * @param string $name The name of the handler
     *
     * @return FW_Config_Handler
     */
    private function _loadConfigHandler($name) {        
        $name  = ucfirst($name);
        $class = "FW_Config_Handler_{$name}";        
        $file  = BASE_PATH."framework/lib/Config/Handler/{$name}.class.php";
        if (is_file($file)) {
            include_once $file;
            $handler = new $class();
            return $handler;
        }
        else {
            $file  = BASE_PATH."framework/app/Config/Handler/{$name}.class.php";            
            if (is_file($file)) {
                include_once $file;
                $handler = new $class();
                return $handler;
            }
            else {
                $file  = BASE_PATH."app/config/Handler/{$name}.class.php";                
                if (is_file($file)) {                    
                    include_once $file;                                    
                    $handler = new $class();                    
                    return $handler;
                }
                else {
                    throw new FW_Config_Exception("Handler {$name} wasn't found. Aborting");
                }
            }

        }
    }

    /**
     * Gets a config handler
     *
     * @param string $property The name of the handler
     *
     * @return mixed
     */
    public function __get($property) {        
        if (!isset(self::$_handlers[$property])) {
            $handler                    = $this->_loadConfigHandler($property);
            if ($handler!==null) {
                self::$_handlers[$property] = $handler;
            }
            else {
                return null;
            }
        }
        return self::$_handlers[$property];
    }   
    
    public function getHandler($handler) {
        if (!isset(self::$_handlers[$handler])) {
            $handlerObject = $this->_loadConfigHandler($handler);
            if ($handlerObject!==null) {               
                self::$_handlers[$handler] = $handlerObject;
            }
            else {
                return null;
            }
        }
        return self::$_handlers[$handler];        
    }
    

    /**
     * Saves the configuration in a file
     *
     * @param string $config The name of the config to save
     * @param string $filename The filename where save
     *
     * @return bool
     */
    public function save($config,$filename="") {
        $data = null;        
        if (isset(self::$_data[$config])) {
            $data = self::$_data[$config];
            $content  = "<?php\n\n";
            $content .= "\$config = ";
            $content .= var_export($data,true);
            $content .= ";\n\n";
            $content .= "FW_Config::createConfig(\"{$config}\");\n";
            $content .= "FW_Config::setConfig(\"{$config}\",\$config);\n";
            $content .= "\n\n?>";
            if (strlen($filename)===0) {
                if (isset(self::$_loadedFiles[$config])) {
                    $filename = self::$_loadedFiles[$config];
                }
            }
            return file_put_contents($filename,$content);
        }
    }
    
    
    public function loadConfig($name) {        
        if (isset(self::$_loadedFiles[$name])) {
            require_once self::$_loadedFiles[$name];
            return true;                        
        }
        return false;
    }

};
?>