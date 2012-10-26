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
 * A routing system implementation
 *
 * PHP Version 5.3
 *
 * @package  Router
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

 
/**
 * Class that implements a routing system
 *
 * @package Router
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Router extends FW_Singleton implements IComponent {
    
    /**
     * An array with the names of the
     * routes.php files
     *
     * @var array
     */
    private $_files;

    /**
     * The service is Router routing
     * for
     *
     * @var string
     */
    private $_service;


    /**
     * Flag to indicate if the Router component
     * has been configured
     *
     * @static
     * @var bool
     *
     */
    private static $_isConfigured;

    /**
     * Flag to indicate if the Router component
     * has been initialized
     *
     * @static
     * @var bool
     *
     */
    private static $_isInitialized;

    /**
     * The routed route
     *
     * @static
     * @var array
     */
    private static $_routedRoute;

    /**
     * The loaded routes
     *
     * @static
     * @var array
     */
    private static $_routes;


    private static $_urlRegistry;


    /**
     * Constructs the Router component
     *
     * @param string $service The service the router will use
     *
     * @return void
     */
    public function __construct($service="app") {
        $parameters = new FW_Container_Parameter();
        $parameters->setParameter("service",$service);
        $context        = FW_Context::getInstance();
        if (!isset($context->router->configured)) {            
            $context->router->configured = true;                        
            $this->configure($parameters);
            $this->initialize(array());
            
        }
    }

    /* (non-PHPdoc)
     * @see framework/lib/interfaces/IComponent#configure($parameters)
     */
    public function configure(FW_Container_Parameter $parameters=null) {
        if ($parameters->hasParameter("service") ) {
            $service        = $parameters->getParameter("service");
            $this->_service = $service;
        }
    }

    /* (non-PHPdoc)
     * @see framework/lib/interfaces/IComponent#initialize($arguments)
     */
    public function initialize(array $arguments=array()) {        
        self::$_routes   = array(
            "app"  => array(),
            "cron" => array()            
        );
        $this->_loadRoutes();
        self::$_urlRegistry = array();
    }


    /**
     * Loads routes into the enrouting system
     *
     * @return void
     */
    private function _loadRoutes() {        
        if (FW_Environment::getInstance()->getEnvironmentName()==="develop"){
        	$this->_files    = $this->_discoverRoutesFiles();			
            $this->_loadRoutesFromFile();
        }
        else {
            if ($this->_existsSerializedRoutesFile()) {            
                $this->_loadRoutesFromSerializedFile();
            }
            else {            
                $this->_files    = $this->_discoverRoutesFiles();				
                $this->_loadRoutesFromFile();
                $this->_serializeRoutes();
            }
        }
    }
    
    
    /*
     * TODO: Put exceptions here 
     * 
     * 
     * */     
    private function _readRoutesDirectory($path) {
        $files  = array();
        $handle = opendir($path);
        while($file=readdir($handle)) {
            if ($file[0]!=='.') {
                $dir = "{$path}/{$file}/config/routes.php";
                if (is_file($dir)) {
                    $files []= $dir;                    
                }             
            }
        }
        closedir($handle);
        return $files;
    }


    /**
     * Discovers routes files
     *
     * @access private
     *
     * @return void
     */
    private function _discoverRoutesFiles() {
        try {
            $files = $this->_readRoutesDirectory(BASE_PATH."app/modules");
            $files = array_merge($this->_readRoutesDirectory(BASE_PATH."framework/app/modules"),$files);
            $files = array_merge($this->_readRoutesDirectory(BASE_PATH."app/lib/plugins"),$files);
            if (is_file(BASE_PATH."framework/config/routes.php")) {
                $files []= "framework/config/routes.php";
            }
        }
        catch (Exception $exception) {
            throw new FW_Router_Exception("Can't access to file: {$exception->getMessage()}");            
        }
        return $files;
    }


    /**
     * Checks if the serialized routes file (routes.ser) exists
     *
     * @access private
     *
     * @return bool
     */
    private function _existsSerializedRoutesFile() {        
        $path  = BASE_PATH."framework/cache/framework/router/{$this->_service}.ser";        
        return (is_file($path));
    }

    /**
     * Loads routes from routes.ser serialized file
     *
     * @access private
     *
     * @return void
     */
    private function _loadRoutesFromSerializedFile() {
        $path   = BASE_PATH."framework/cache/framework/router/{$this->_service}.ser";        
        $routes = file_get_contents($path);
        $routes = unserialize($routes);        
        self::$_routes[$this->_service] = $routes;
    }

    /**
     * Loads routes from routes.php files
     *
     * @access private
     *
     * @return void
     */
    private function _loadRoutesFromFile() {
        if (count($this->_files)) {
            foreach ($this->_files as $file) {
                require_once $file;
            }
        }
    }

    /**
     * Serializes all the loaded routes
     * to a serialized file
     *
     * @access private
     *
     * @return void
     */
    private function _serializeRoutes() {
        $cachePath = BASE_PATH."framework/cache/framework/router/";
        if (!is_dir($cachePath)) {
            if (!is_dir(BASE_PATH."framework/cache/framework/")) {
                if (mkdir(BASE_PATH."framework/cache/framework/",775)) {
                    if (!mkdir(BASE_PATH."framework/cache/framework/router",755)) {
                        throw new FW_Router_Exception("Can't create route cache");
                    }
                }
                else {
                    throw new FW_Router_Exception("Can't create route cache");
                }
            }
            if (!is_dir(BASE_PATH."framework/cache/framework/router")) {
                mkdir(BASE_PATH."framework/cache/framework/router");
            }
        }

        foreach (array_keys(self::$_routes) as $key) {
            $file             = $cachePath.$key.".ser";
            $serializedRoutes = serialize(self::$_routes[$key]);                        
            file_put_contents($file,$serializedRoutes);
        }
    }

    /**
     * Adds a route to the system
     *
     * @param array $route The route to add
     * @access public
     *
     * @return void
     */
    public static function addRoute(array $route) {        
        $type                    = $route["type"];
        $compiled                = self::compileRoute($route);
        $route["pattern"]        = $compiled["regexp"];    
        $route["parameterOrder"] = $compiled["parameters"];
        
        if (!isset($route["internal"])) {
            $route["internal"]  = false;
        }
        if (!isset($route["cache"])) {
            $route["cache"] = false;
        }
        
        if (!isset($route["authentication"])) {
            $route["authentication"] = false;
        }
                
        
        if ( ($type==="app") || ($type==="xml") || ($type==="json") || ($type==="mime") || ($type==="static") || ($type==="redirect") || ($type==="plugin"))  {
            if  (($type==="app") || ($type==="xml") || ($type==="json") || ($type==="mime")) {
                $parameters  = "";
                if (isset($route["parameters"]) && is_array($route["parameters"])) {
                                       
                    ksort($route["parameters"]);                              
                    $parameters = implode('|',array_keys($route["parameters"]));
                }                
                $key =  md5("{$route["module"]}|{$route["controller"]}|{$route["action"]}|{$route["internal"]}|{$parameters}");                        
                self::$_routes["app"]    [$key]= $route;
            }
            else {
                self::$_routes["app"]  []= $route;
            }
            
        }

        if ($type==="cron") {
            self::$_routes["cron"]   []= $route;
        }
              
    }


    /**
     * Starts the enroute process
     *
     * @param string $url The url to enroute
     * @access public
     *
     * @return bool
     */
    public function enroute() {                       
        $url     = FW_Request::getInstance()->getUrl();
        if (strlen($url)===0) {
            $url = '/';
        }
        return $this->_toParams($url);          

       /* if ($this->_service==="cron") {
            if (!isset($_SERVER["argv"])) {
                $pos  = strpos($url,"cron");
                if ($pos!==false && $pos<=5) {
                    $url = substr($url,5);
                }
            }
            
            return $this->_toParams($url);
        }    */    
    }


    

    /**
     * Gets the route parameters from an url
     *
     * @param string $url The url to process
     * @access private
     *
     * @return bool
     */
    private function _toParams($url) {
        
        $format = null;
        $url    = rtrim($url,'/');
        $routes = self::$_routes[$this->_service];        
        $method = FW_Request::getInstance()->getMethod();
        

        if (count($routes)>0) {
        	
            foreach ($routes as $route) {

                $pattern    = "";
                $parameters = array();
                
                
                if (!isset($route["pattern"])) {                                        
                    $patterns    = self::compileRoute($route);                    
                    $pattern     = $patterns["regexp"];
                    $parameters  = $patterns["parameters"];
                }
                else {                    
                    $pattern     = $route["pattern"];
                    $parameters  = $route["parameterOrder"];
                }                
                
                
                if (preg_match($pattern,$url,$matches)) {
                                        
                    $parameters                   = $this->_orderParameters($matches,$parameters);                           
                    $route["processedParameters"] = $parameters;                                        

                    if (count($parameters)) {
                        $this->_registerParameters($parameters);                        
                    }                    

                    if (!$this->_checkRouteMethod($route, $method)) {                        
                        break;
                    }
                    else {
                        $params = $this->_generateRouteContextParameters($route, $parameters, $method, $format);
                        FW_Context::getInstance()->setParameter("router",$params);
                        return true;
                    }
                    
                }
            }
        }        
        return false;
    }

    private function _registerParameters($parameters) {
        $request = FW_Request::getInstance();
        foreach ($parameters as $key=>$value) {
            $request->registerParameter($key,$value);
        }
    }

    private function _generateRouteContextParameters($route,$parameters,$method,$format) {
        $param             = new FW_Container_Parameter();
        $param->result     = true;
        $param->route      = $route;
        $param->parameters = $parameters;
        $param->type       = $route["type"];
        $param->method     = $method;
        
        if (!empty($format)) {
            $param->format   = $format;
        }
        
        if (isset($route["mime"])) {
            $param->mime    = $route["mime"];
        }
        return $param;        
    }

    private function _checkRouteMethod($route,$method) {
        if ( (isset($route["method"])) && ($route["method"]!==$method) ) {
            return false;
        }        
        return true;
    }

/**
     * Generates a regular expression from the route
     *
     * @param array $route The route to compile
     * @static
     *
     * @return array
     */
    public static function compileRoute(array $route) {        
        $url            = rtrim($route["url"],'/');
        $elements       = array();
        $params         = array();
        $parameterOrder = array();
        $parsed         = preg_quote($url,"#");
       

        if (isset($route["parameters"]) && count($route["parameters"])>0) {

            foreach ($route["parameters"] as $key=>$value) {                                
                $pattern = '#:('.$key.')#';
                if ( preg_match($pattern,$url,$matches) ) {
                    $elements []= $matches[1];
                    $name            = $matches[1];
                    if (isset($route["parameters"][$name])) {
                        $format = false;
                        $type      = $route["parameters"][$name]["type"];
                        if (isset($route["parameters"][$name]["format"])) {
                            $format = $route["parameters"][$name]["format"];
                        }
                        $name   = "\:{$name}";

                        if ($format===false) {

                            if ($type==="string") {
                                $params[$name] = '(?:([^/]+))';
                            }
                            else if ( ($type==="integer" || $type==="numeric" || $type==="int")) {
                                $params[$name] = '(?:([\d+^/]+))';
                            }
                            else if ( ($type==="float" || $type==="double" || $type==="real") && $format===false) {
                                $params[$name] = '(?:([0-9]*[.][0-9]+))';
                            }

                            else {
                                $params[$name] = '(?:([^/]+))';
                            }
                        }
                        else {
                            $params[$name] = '(?:('.$format.'+))';
                        }
                    } 
                }
            }
        }
        krsort($params);
        $parsed = str_replace(array_keys($params), array_values($params), $parsed);
        $compiledRoute = '#^' . $parsed . '[/]*$#';
        if (preg_match_all('#:([A-Za-z0-9_-]+[A-Z0-9a-z])#', $url, $matches) ) {
            $parameterOrder = $matches[1];
        }        
        return array("regexp"=>$compiledRoute,"parameters"=>$parameterOrder);
    }

    /**
     *
     * @param array $route
     * @param unknown_type $matches
     * @param unknown_type $parameters
     * @return multitype:
     */
    private function _orderParameters($matches=array(),$parameters=array()) {
                                
        if (count($parameters)===0 && count($matches)===1) {
            $parameters = array();
        }
        else if ( (count($parameters)===0) && (count($matches)>1) ) {
            array_shift($matches);            
        }
        else if ( (count($parameters)!==0) && (count($matches)===0) ) {

        }

        else {            
            if ((count($matches)>1) && count($parameters)) {
                                
                $parameters = array_combine($parameters,$matches);
            }
        }        
        return $parameters;
    }

    /**
     * Gets the URL for the HMVC action
     *
     * @param string $module The name of the module
     * @param string $controller The name of the controller
     * @param string $action The name of the action
     * @param bool $internal If the module is internal or external
     * @access public
     *
     * @return string
     */
    public function toURL($module,$controller,$action,$internal=false,array $parameters=array()) {    	
        ksort($parameters);                       
        $key     =  md5("{$module}|{$controller}|{$action}|{$internal}|".implode('|',array_keys($parameters)));
		        
        if (isset(self::$_urlRegistry[$key])){            
            $route = self::$_urlRegistry[$key];                                
            return $route;
        }        
        else {            
            $routes = self::$_routes["app"];            
            if (isset($routes[$key])) {                                
                self::$_urlRegistry[$key] = $routes[$key];
                return $routes[$key];
            }            
        }        
    }


    /**
     * Adds information about a route into the Router
     *
     * @param array $route Information about a route
     * @static
     * @access public
     *
     * @return void
     */
    public static function connect(array $route) {
        self::addRoute($route);
    }



};
?>