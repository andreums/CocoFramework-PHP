<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

define("BASE_PATH","/var/www/cocoframeworkphp/");

class bootstrap {

    private static $_instance = null;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        $this -> _bootstrap();
    }
    
    private function _explodeClassName($class) {
        $classFile = "";
        $prefix    = substr($class, 0, 2);        
        if (strcmp($prefix,'FW') === 0) {                        
            $class = str_replace('_', '/', substr($class, 3));            
            $pos   = strpos($class, '/');
            
            if ($pos === false) {                
                $classFile = "framework/lib/{$class}/{$class}.class.php";                                
            }
            else {                
                $package   = substr($class, 0, $pos);
                if (strpos($class,"API")!==false) {
                    $class     = explode('/',$class);
                    array_shift($class);
                    if (count($class)>1) {
                        $class     = implode('/',$class);
                        $pos       = strpos($class, '/');
                        $package   = substr($class, 0, $pos);                    
                        $classFile = "framework/lib/API/{$class}.class.php";
                    }
                    else {
                        $class     = implode('/',$class);
                        $pos       = strpos($class, '/');
                        $package   = substr($class, 0, $pos);                    
                        $classFile = "framework/lib/API/{$class}/{$class}.class.php";                        
                    }
                }
                else {
                    $classFile = "framework/lib/{$class}.class.php";
                }               
            }
        } 
        else {
            if ((strlen($class) > 0) && ($class[0] == 'I') && (ctype_upper($class[1]))) {
                $classFile = "framework/lib/interfaces/{$class}.class.php";
            }
        }        
        $classFile = BASE_PATH.$classFile;
        return $classFile;
    }

    public function autoloader($class) {
        $classFile = "";
        $package   = "";
        $classFile = $this->_explodeClassName($class);
        
        if (strlen($classFile)>0) {            
            if (is_file($classFile)) {                
                include $classFile;                
                return true;                
            }            
        }
        return false;
    }

    /* Model autoloader */
    public function modelAutoloader($model) {                

        $filename = "app/lib/models/{$model}.class.php";
        if (file_exists($filename)) {
            include $filename;
            return true;
        } else {
            $registry = new FW_Plugin_Registry();
            $plugins = $registry -> getPlugins();
            if (count($plugins) > 0) {
                foreach ($plugins as $plugin) {
                    $filename = "app/lib/plugins/{$plugin}/models/{$model}.class.php";
                    if (file_exists($filename)) {
                        include $filename;
                        return true;
                    }
                    return false;
                }
            }
        }
        return false;
    }

    /* Plugin autoloader */
    public function pluginAutoloader($plugin) {        
        $path = "framework/lib/plugins";
        $filename = "{$path}/{$plugin}/{$plugin}.class.php";
        if (file_exists($filename)) {
            include $filename;
            return true;
        } else {
            $path = "app/lib/plugins";
            $filename = "{$path}/{$plugin}/{$plugin}.class.php";
            if (file_exists($filename)) {
                include $filename;
                return true;
            } else {
                return false;
            }
        }
    }

    /* Helper autoloader */
    public function helperAutoloader($helper) {        
        $path = "framework/lib/Helpers";
        $filename = "{$path}/{$helper}.class.php";
        if (file_exists($filename)) {
            include $filename;
            return true;
        }
        else {
            $path = "app/lib/helpers";
            $filename = "{$path}/{$helper}.class.php";
            if (file_exists($filename)) {
                include $filename;
                return true;
            } else {
                return false;
            }
        }
    }

    /* Widget autoloader */
    public function widgetAutoloader($widget) {        
        $path = "app/lib/widgets";
        $filename = "{$path}/{$widget}.class.php";
        if (file_exists($filename)) {
            include $filename;
            return true;
        } else {
            $path = "framework/lib/widgets";
            $filename = "{$path}/{$widget}.class.php";
            if (file_exists($filename)) {
                include $filename;
                return true;
            }
        }
        return false;
    }

    public function appAutoloader($class) {
        $path = "app/lib/classes";
        $filename = "{$path}/{$class}.class.php";
        if (file_exists($filename)) {
            include $filename;
            return true;
        }
        return false;
    }

    public function validatorAutoloader($class) {
        $path = "framework/lib/Validator/validators";
        $filename = "{$path}/{$class}.class.php";
        if (file_exists($filename)) {
            include $filename;
            return true;
        } else {
            $path = "app/lib/validators";
            $filename = "{$path}/{$class}.class.php";
            if (file_exists($filename)) {
                include $filename;
                return true;
            }
        }
        return false;
    }

    public function registerArgv() {
        return true;
        if (isset($_SERVER["argv"])) {
            $file = $_SERVER["PHP_SELF"];
            if (strpos($file, "cron.php") !== false) {
                $path = explode('/', $file);
                if (count($path) > 1) {
                    array_pop($path);
                    $path = implode('/', $path);
                } else {
                    $path = '.';
                }
                chdir($path);
            }
        }
    }

    private function _autoRegister() {
        spl_autoload_register(null, false);
        spl_autoload_extensions('.php, .class.php');
        spl_autoload_register(array($this, "autoloader"));
        spl_autoload_register(array($this, "helperAutoloader"));
        spl_autoload_register(array($this, "widgetAutoloader"));
        spl_autoload_register(array($this, "appAutoloader"));
        spl_autoload_register(array($this, "modelAutoloader"));
        spl_autoload_register(array($this, "pluginAutoloader"));
        spl_autoload_register(array($this, "validatorAutoloader"));
    }

    private function _bootstrap() {
        include BASE_PATH."framework/lib/interfaces/IComponent.class.php";
        include BASE_PATH."framework/lib/Singleton/Singleton.class.php";
        include BASE_PATH."framework/lib/Registry/Registry.class.php";
        include BASE_PATH."framework/lib/Config/Config.class.php";
        include BASE_PATH."framework/lib/Config/Handler.class.php";
        include BASE_PATH."framework/lib/Config/Cache.class.php";
        include BASE_PATH."framework/lib/Container/Parameter.class.php";         
        include BASE_PATH."framework/lib/Session/Session.class.php";
        include BASE_PATH."framework/lib/Request/Request.class.php";
        include BASE_PATH."framework/lib/Router/Router.class.php";        
        include BASE_PATH."framework/lib/Environment/Environment.class.php";        
        include BASE_PATH."framework/lib/Context/Context.class.php";
        include BASE_PATH."framework/lib/Authentication/Authentication.class.php";
        include BASE_PATH."framework/lib/Locale/Locale.class.php";
        include BASE_PATH."framework/lib/FrontController/FrontController.class.php";
        include BASE_PATH."framework/lib/FrontController/Base.class.php";
        include BASE_PATH."framework/lib/Exception/Exception.class.php";
        include BASE_PATH."framework/lib/Exception/Handler.class.php";
        include BASE_PATH."framework/lib/Error/Error.class.php";
        include_once BASE_PATH."framework/lib/Plugin/Registry.class.php";

        
        /*set_exception_handler(array(FW_Exception_Handler::getInstance(), 'handler'));
        set_error_handler(array(FW_Error::getInstance(),'handler'));*/

        $this->_autoRegister();
        $this->_registerContext();
        FW_Authentication::checkUserAuthentication();
    }

    private function _registerContext() {
        $context = FW_Context::getInstance();
        $context -> setParameter("request", FW_Request::getInstance());
        $context -> setParameter("router", new FW_Container_Parameter());
        $context -> setParameter("baseURL", FW_Config::getInstance() -> get("core.global.baseURL"));
        $context -> setParameter("user", FW_Authentication::getUser());
        $context -> setParameter("cache", array());
    }

};

if (!function_exists("LoadPlugin")) {
    function LoadPlugin($name) {
        return FW_Plugin_Registry::getInstance() -> load($name);
    }

}

if (!function_exists("LoadExternal")) {
    function LoadExternal($name, array $options = array()) {
        $file = "";
        $path = "framework/lib/external";
        $file = "{$path}/{$name}/init.php";
        if (!file_exists($file)) {
            $path = "app/lib/external";
            $file = "{$path}/{$name}/init.php";
            if (!file_exists($file)) {
                throw new FW_Exception("External library {$name} couldn't be loaded");
            }
        }
        require_once $file;
    }

}
?>