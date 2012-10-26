<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
abstract class FW_Plugin_Base {

    /**
     * An array of variables to the views
     *
     * @var array
     */
    protected $_variables;


    /**
     * An array of options
     *
     * @var array
     */
    protected $_options;


    /**
     * An access to the FW_Database component
     *
     * @var FW_Database
     */
    protected $_database;


    /**
     * The prefix of the database
     *
     * @var string
     */
    protected $_databasePrefix;

    /**
     * The name of the plugin
     *
     * @var string
     */
    protected $_name;

    /**
     * The path of the plugin
     *
     * @var string
     */
    protected $_path;

    protected $_slots;

    /**
     * The constructor of a plugin
     *
     * @return void
     */
    public function __construct() {
        $this->_autoload();
        $this->_configure(null);
        $this->_initialize(null);
    }

    /**
     * Initializes a plugin
     *
     * @abstract
     * @param array $parameters An array of parameters
     *
     * @return void
     */
    abstract protected function _initialize(array $parameters=null);

    /**
     * Configures a plugin
     *
     * @abstract
     * @param array $parameters An array of parameters
     *
     * @return void
     */
    abstract protected function _configure(array $parameters=null);


    /**
     * Installs a plugin
     *
     * @abstract
     * @param array $arguments An array of arguments
     *
     * @return mixed
     */
    abstract public function install(array $arguments=array());


    /**
     * Uninstalls a plugin
     *
     * @abstract
     * @param array $arguments An array of arguments
     *
     * @return mixed
     */
    abstract public function uninstall(array $arguments=array());

    /**
     * Gets the name of the plugin
     *
     * @final
     * @access protected
     *
     * @return string
     */
    final protected function  pluginName() {
        if ($this->_name===null) {
            $reflect     = new ReflectionClass($this);
            $parentClass = $reflect->getParentClass();
            if ($parentClass->name!="FW_Plugin_Base") {
                do {
                    $aux         = new ReflectionClass($parentClass->name);
                    $parentClass = $aux->getParentClass();
                    if ($parentClass->name=="FW_Plugin_Base") {
                        $parentClass = $aux;
                        break;
                    }
                } while ($parentClass->name!="FW_Plugin_Base");
                $this->_name = $parentClass->name;
            }
            else {
                $this->_name = $reflect->name;
            }
        }
        return $this->_name;
    }

    /**
     * Gets the path of a plugin
     *
     * @return string
     */
    protected function _getPluginPath() {
        if ($this->_path===null) {
            $name = $this->pluginName();
            $path = "app/lib/plugins/{$name}";
            $this->_path = $path;
        }
        return $this->_path;
    }



    /**
     * Loads the libraries of a plugin
     *
     * @return void
     */
    protected function _loadLibs($path="") {
        $base   = $this->_getPluginPath();
        if (empty($path)) {
            $path   = "{$base}/lib";
        }        
        $files  = scandir($path);

        array_shift($files);
        array_shift($files);

        if (!empty($files)) {
            foreach ($files as $file) {
                if (is_file("{$path}/{$file}")) {
                    try {
                        include_once "{$path}/{$file}";
                    }
                    catch (Exception $exception) {
                        throw new FW_Plugin_Exception("Plugin {$this->pluginName()} Can't load plugin lib {$file} {$exception->getMessage()}");
                    }
                }
                else {
                    $this->_loadLibs("{$path}/{$file}");
                }
            }
        }
    }

    /**
     * Loads the models of a plugin
     *
     * @return void
     */
    protected function _loadModels() {
        $base   = $this->_getPluginPath();
        $path   = "{$base}/models";
        $files  = scandir($path);

        array_shift($files);
        array_shift($files);

        if (!empty($files)) {
            foreach ($files as $file) {
                try {
                    include_once "{$path}/{$file}";
                }
                catch (Exception $exception) {
                    throw new FW_Plugin_Exception("Plugin {$this->pluginName()} Can't load plugin model {$file} {$exception->getMessage()}");
                }
            }
        }
    }

    /**
     * Loads the options of a plugin
     *
     * @return void
     */
    protected function loadOptions() {
        $base        = $this->_getPluginPath();
        $optionsFile = "{$base}/config/options.php";

        if (is_file($optionsFile)) {
            require $optionsFile;
            if ($options!==null) {
                $this->_options = $options;
            }
        }
    }

    /**
     * Loads the libraries and models of a plugin
     *
     * @return void
     */
    final private function _autoload() {
        $this->_loadLibs();
        $this->_loadModels();
        $this->loadOptions();
    }


    /**
     * Gets an instance of the Database
     *
     * @return FW_Database
     */
    protected function database() {
        if ($this->_database===null) {
            $this->_database       = FW_Database::getInstance();
            $this->_databasePrefix = $this->_database->getPrefix();
        }
        return $this->_database;
    }

    /**
     * Sets an option into the database
     *
     * @param string $name The name of the option
     * @param string $value The value of the option
     *
     * @return bool
     */
    final protected function setDBOption($name,$value) {
        $query      = "";
        $database   = $this->database();
        $plugin     = $this->pluginName();

        if ($this->getDBOption($name)!==null) {
            $query  = "UPDATE {$this->_databasePrefix}plugin_option SET value='{$value}' WHERE name='{$name}' AND plugin='{$plugin}' ";
        }
        else {
            $query  = "INSERT INTO {$this->_databasePrefix}plugin_option (plugin,name,value) VALUES ( ";
            $query .= " '{$plugin}','{$name}','{$value}' )   ";
        }
        $database->query($query);
        if ($database->numRows() || $database->affectedRows()) {
            return true;
        }
        return false;
    }

    /**
     * Gets an option of the database
     *
     * @param string $name The name of the option
     *
     * @return mixed
     */
    final protected function getDBOption($name) {

        $query      = "";
        $value      = null;
        $database   = $this->database();
        $plugin     = $this->pluginName();

        $query    = "SELECT value FROM {$this->_databasePrefix}plugin_option WHERE name='{$name}' AND plugin='{$plugin}' ";
        $database->query($query);
        if ($database->numRows()>0) {
            $value = $database->fetchAssoc();
            $value = $value["value"];
        }
        return $value;
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
        $code        = "\$this->_options";
        foreach ($parameters as $parameter) {
            $code .= '["'.$parameter.'"]';
        }
        $code .= " ";
        return $code;
    }


    /**
     * Gets the value of an option on a plugin
     *
     * @param string $name The name of the option
     *
     * @return mixed
     */
    protected function getOption($name) {
        $code = $this->_extractParameters($name);
        $code = "\$value = {$code};";
        eval($code);
        return $value;
    }



    /**
     * Sets the value of an option on a plugin
     *
     * @param string $name The name of the option
     * @param mixed $value The value of the option
     *
     * @return void
     */
    final protected function setOption($name,$value) {
        if ($this->existsOption($name)) {
            $code = $this->_extractParameters($name);
            $code = "{$code} = \$value;";
            eval($code);
        }
    }

    /**
     * Checks if a config value exists
     *
     * @param string $key The key for the config
     *
     * @return bool
     */
    final protected function existsOption($key) {
        $exists = false;
        $key    = explode('.',$key);
        if (isset($this->_options) && (!empty($this->_options)) ) {
            $key    = implode('.',$key);
            $code   = $this->_extractParameters($key);
            $code   = "\$exists = isset({$code});";
            eval($code);
        }
        return $exists;
    }

    /**
     * Gets all the options loaded from options.php
     *
     * @return array
     */
    final protected function getOptions() {
        return $this->_options;
    }

    /**
     * Creates a new value on a config and sets its value
     *
     * @param string $key The parameter to set
     * @param mixed $value The value to set
     *
     * @return void
     */
    final protected function addOption($key,$value) {

        print "La Key es {$key}<br/>\n";

        if (!$this->exists($key)) {
            $copy  = explode('.',$key);
            array_pop($copy);
            if (count($copy)===0) {
                $this->_options[$key] = $value;
                return;
            }
            else {
                $ccopy         = implode('.',$copy);
                $beforeElement = ($this->getOption($ccopy));
                if (is_array($beforeElement)) {
                    $code = $this->_extractParameters($key);
                    $code = "{$code}[]= \$value ;";
                    eval($code);
                }
            }

        }

    }

    /**
     * Saves the options of the plugin to its options.php file
     *
     * @return bool
     */
    final protected function saveOptions() {
        $data     = null;
        $base     = $this->_getPluginPath();
        $filename = "{$base}/config/options.php";
        if (isset($this->_options)) {
            $data     = $this->_options;
            $content  = "<?php\n\n";
            $content .= "\$options = ";
            $content .= var_export($data,true);
            $content .= ";\n\n";
            $content .= "\n\n?>";
            return file_put_contents($filename,$content);

        }
    }

    /**
     * Reloads plugin options from options.php file
     *
     * @return void
     */
    final protected function reloadOptions() {
        $this->_options = array();
        $this->loadOptions();
    }



    /**
     * Renders a view
     *
     * @param string $view The name of the view
     *
     * @return mixed
     */
    final protected function renderView($view,array $variables=array()) {
        $name     = $this->pluginName();
        $path     = "app/lib/plugins/{$name}/view/{$view}.php";
        if (!is_file($path)) {
            throw new FW_Plugin_Exception("View {$view} doesn't exist");
        }
        if (!empty($variables)) {
            extract($variables);
        }
        include $path;
    }

    /**
     * Renders an error view
     *
     * @param string $view The name of the view
     *
     * @return mixed
     */
    final protected function renderError($view) {
        $name     = $this->pluginName();
        $path     = "app/lib/plugins/{$name}/error/{$view}.php";
        if (!is_file($path)) {
            throw new FW_Plugin_Exception("Error view {$view} doesn't exist");
        }
        if (!empty($variables)) {
            extract($variables);
        }
        include $path;
    }

    /**
     * Sets a variable for a view
     *
     * @param string $name The name of the variable to set
     * @param mixed $value The value of the variable to set
     *
     * @return void
     */
    final protected function set($name,$value) {
        return $this->_variables[$name] = $value;
    }


    /**
     * Sets the view data in the context
     *
     * @return void
     */
    private function _createViewContext() {
        $this->context()->view = new FW_Container_Parameter();
    }


    /**
     * Renders a layout
     *
     * @param string $layout The name of the layout
     *
     * @return void
     */
    protected function renderLayout($layout) {
        $name     = $this->pluginName();
        $path     = "app/lib/plugins/{$name}/layout/{$layout}.php";

        if (is_file($path)) {
            if ($this->context()->view===null) {
                $this->_createViewContext();
            }
            $this->context()->view->layout = $path;
            $this->context()->view->slots  = $this->_slots;
        }
        else {
            throw new FW_Plugin_Exception("Can't load layout {$layout} to render!");
        }
    }

    /**
     * Sets the slot
     *
     * @param string $name The name of the slot
     * @param string $view The name of the view
     * @param array $variables an array of variables
     *
     * @return void
     */
    protected function setSlot($name,$view,array $variables=array()) {
        $this->_slots[$name] = array("view"=>$view,"variables"=>$variables);
    }

    /**
     * Gets the contents of a slot
     *
     * @param string $name The name of the slot
     *
     * @return string
     */
    protected function getSlot($name) {
        $contents = "";
        if ($this->hasSlot($name)) {
            ob_start();
            $this->renderView($this->_slots[$name]);
            $contents = ob_get_contents();
            ob_clean();
        }
        return $contents;
    }

    /**
     * Checks if a slot is defined
     *
     * @param string $name The name of the slot
     *
     * @return bool
     */
    protected function hasSlot($name) {
        if ($this->_slots[$name]!==null) {
            return true;
        }
        return false;
    }


    /**
     * Obtains the logged user
     *
     * @return FW_Authentication_User
     */
    protected function user() {
        if (!isset($this->_user)) {
            if (FW_Authentication::getInstance()->isLoggedIn()) {
                $this->_user = FW_Authentication::getUser();
            }
            else {
                $this->_user = false;
            }
        }
        return $this->_user;
    }

    /**
     * Obtains the request object
     *
     * @return FW_Request
     */
    protected function request() {
        if (!isset($this->_request)) {
            $this->_request = FW_Request::getInstance();
        }
        return $this->_request;
    }

    /**
     * Obtains the FW_Session object
     *
     * @return FW_Session
     */
    protected function session() {
        if ($this->_session === null) {
            $this->_session = FW_Session::getInstance();
        }
        return $this->_session;
    }

  	/**
     * Acceses to the context
     *
     * @return FW_Context
     */
    protected function context() {
        if (!isset($this->_context)) {
            $this->_context = FW_Context::getInstance();
        }
        return $this->_context;
    }





};
?>