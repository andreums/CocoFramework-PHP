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
 * Plugin Registry
 *
 * PHP Version 5.2
 *
 * @category Framework
 * @package  Plugin
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es>
 * @license  BSD Style
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * Class PluginRegistry
 *
 * @category Framework
 * @package  Plugin
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es>
 * @license  BSD Style
 * @link     http://www.andresmartinezsoto.es
 *
 */
class FW_Plugin_Registry extends FW_Registry implements IComponent {

    private static $_instance;

    /**
     * The constructor of Plugin_Registry
     *
     * @return void
     */
    public function __construct() {
        $this->configure();
    }

    /**
     * Gets the instance of Plugin_Registry (implements Singleton pattern)
     *
     * @return FW_Plugin_Registry
     */

    public static function getInstance() {
        if (!(self::$_instance instanceof self))   {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /* (non-PHPdoc)
     * @see framework/lib/interfaces/IComponent#configure($parameters)
     */
    public function configure(FW_Container_Parameter $parameter=null) {
        $this->_discoverPlugins();
        return true;
    }

    /* (non-PHPdoc)
     * @see framework/lib/interfaces/IComponent#initialize($arguments)
     */
    public function initialize(array $arguments=array()) {
        return true;
    }


    public function load($name) {
        $path = FW_Config::getInstance()->get("core.global.basePath");
        /*if (getcwd()!==$path) {
            chdir($path);
        }*/
        $path = "app/lib/plugins/{$name}";
        $file    = "{$path}/{$name}.class.php";
        if (is_dir($path)) {
            if (is_file($file)) {
                require_once $file;
                $plugin = new $name();
                return $plugin;               
            }            
        }        
    }

    /**
     * Discovers the plugins
     *
     * @return void
     */
    private function _discoverPlugins() {        
        $path    = "app/lib/plugins";
        $plugins = scandir($path);
        array_shift($plugins);
        array_shift($plugins);

        if (count($plugins)) {
            foreach ($plugins as $plugin) {
                $file = "{$path}/{$plugin}/{$plugin}.class.php";
                if (is_file($file)) {
                    $this->set($plugin,$plugin);
                }
            }
        }
    }


    /**
     * Checks if a plugin exists
     *
     * @param string $name The name of the plugin
     *
     * @return bool
     */
    public function hasPlugin($name) {
        return ($this->exists($name));
    }


    /**
     * Gets the names of the registered plugins
     *
     * @return array
     */
    public function getPlugins() {
        return $this->keys();
    }


    /**
     * Gets a plugin
     *
     * @param string $name The name of the plugin
     *
     * @return FW_Plugin_Base
     */
    public function getPlugin($name) {
        if ($this->exists($name)) {
            $data = $this->get($name);
            if ($data instanceof FW_Plugin_Base) {
                return $data;
            }
            else {
                $filename = "app/lib/plugins/{$name}/{$name}.class.php";
                if (is_file($filename)) {
                    include_once $filename;
                    $plugin = new $name();
                    $this->set($name,$plugin);
                    return $plugin;
                }
            }
        }
        throw new FW_Plugin_Exception("Plugin {$name} doesn't exists");
    }


    /**
     * Installs a plugin
     *
     * @param string $name The name of the plugin
     *
     * @return mixed
     */
    public function install($name,array $arguments=array()) {
        if ($this->exists($name)) {
            $plugin = $this->getPlugin($name);
            return call_user_func_array(array($name,"install"),$arguments);
        }
    }

    /**
     * Uninstalls a plugin
     *
     * @param string $name The name of the plugin
     *
     * @return mixed
     */
    public function uninstall($name,array $arguments=array()) {
        if ($this->exists($name)) {
            $plugin = $this->getPlugin($name);
            return call_user_func_array(array($name,"uninstall"),$arguments);
        }
    }

    /**
     * Gets the models of the plugins
     *
     * @return array
     */
    public function getPluginModels() {
        $models = array();
        $path   = "";
        foreach ($this->_objects as $key=>$value) {
            $path  = "app/lib/plugins/{$key}/models";
            $files = scandir($path);
            foreach ($files as $file) {
                $filename = "{$path}/{$file}";
                if (!is_dir($filename)) {
                    if (substr($file,-10)===".class.php") {
                        $name     = substr($file,0,-10);
                        $models [$name]=$filename;
                    }
                }
            }
        }
        return $models;
    }
};
?>