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
 * Adapter to convert an HMVC action to a SOAP webservice
 *
 * PHP Version 5.3
 *
 * @package  SOAP
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * Class that implements an adapter to convert an HMVC action to a SOAP webservice
 *
 * @package SOAP
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Soap_Adapter {


    /**
     * The dispatch map of the adapter
     *
     * @var array
     */
    public $__dispatch_map;


    /**
     * The type map of the adapter
     *
     * @var array
     */
    public $__typedef;


    /**
     * The object that is adapted
     *
     * @var object
     */
    private $_object;


    /**
     * The action of the adapted object
     *
     * @var string
     */
    private $_action;


    /**
     * The route that contains information
     * about the adapted object
     *
     * @var array
     */
    private $_route;
    
    /**
     * The name of the service
     * 
     * @var string
     */
    private $_name;
    
    
    /**
     * The description for this service
     * 
     * @var string
     */
    private $_description;



    /**
     * Constructs an adapter object
     * 
     * @param array $route The route with information of the object to adapt
     * @access public
     *  
     * @return void
     */
    public function __construct(array $route=array()) {
        $this->_configure($route);
        $this->_initialize();
    }

    /**
     * Configures the adapter
     * 
     * @param array $route The router with information of the object to adapt
     * @access private
     * 
     * @return void 
     */
    private function _configure(array $route=array()) {

        $this->__dispatch_map = array();
        $this->__typedef = array();
        
        $this->_description = "";
        $this->_description = "";
        $module             = $route["module"];
        $controller         = $route["controller"];
        $action             = $route["action"];
        $internal           = $route["internal"];        

        if ($this->_includeController($module,$controller,$internal)) {
            $class         = "{$controller}Controller";
            $object        = new $class();

            if (!method_exists($object,$action)) {
                throw new FW_Soap_Exception("Error, action {$action} doesn't exists in {$controller} controller. Aborting");
            }
            $this->_object = $object;
            $this->_action = $action;
            $this->_route  = $route;
            
            if (isset($this->_route["description"])) {
                $this->_description = $this->_route["description"];
            }
            if (isset($this->_route["serviceName"]))  {
                $this->_name        = $this->_route["serviceName"];
            }
        }
        else {
            throw new FW_Soap_Exception("Error, can't find {$controller} controller to include. Aborting");
        }
    }

    /**
     * Initializes the adapter
     *  
     * @access private
     *   
     * @return void
     */
    private function _initialize() {
        $this->_generateDispatchMap();
    }

    /**
     * Generates the dispatch map of an adapted object
     *
     * @access private
     * 
     * @return void
     */
    private function _generateDispatchMap() {
        if ( ($this->_object!==null) && ($this->_action!==null) ) {

            $route    = $this->_route;
            $action   = $route["action"];

            // Input parameters
            $inParams = array();
            // Output parameters
            $outParams = array();

            if (isset($route["parameters"])) {
                if ($route["parameters"]!==false) {
                    foreach ($route["parameters"] as $key=>$parameter) {
                        $inParams [$key]= $parameter["type"];
                    }
                }
            }
            if (isset($route["return"])) {
                if ($route["return"]!==false) {
                    foreach ($route["return"] as $key=>$value) {                        
                        $outParams [$key]= $value;
                    }
                }
            }
            if (isset($route["typedef"])) {
                if ($route["typedef"]!==false) {
                    foreach ($route["typedef"] as $key=>$type) {
                        $this->__typedef[$key] = array ();
                        if (count($type)) {
                            foreach ($type as $k=>$v) {
                                $this->__typedef[$key][$k] = $v;
                            }
                        }
                    }
                }
            }

            $this->__dispatch_map[$action] = array (
     			"in"  => $inParams,
     			"out" => $outParams
            );

        }
    }
    /**
     * Includes a controller of a module 
     * 
     * @param string $module The name of the module
     * @param string $controller The name of the controller
     * @param bool   $internal Indicates if the module is external or internal     
     * @access private
     *      
     * @return bool
     */
    private function _includeController($module,$controller,$internal=false) {

        $path       = "";
        $file       = "";
        $controller = "{$controller}Controller";

        if ($internal===false) {
            $path = "app/modules/{$module}";
        }
        else {
            $path = "framework/app/modules/{$module}";
        }

        $file       = "{$path}/controller/{$controller}.class.php";
        if (is_file($file)) {
            require_once $file;
            return true;
        }
        return false;
    }
    
     /**
     * Gets the description of this service
     *
     * @access public
     *      
     * @return string
     */    
    public function getDescription() {
        return $this->_description;
    }
    
    
     /**
     * Gets the name of the service
     *
     * @access public
     * 
     * @return string
     */
    public function getName() {
        return $this->_name;
    }
     
    /**
     * Dispatches a SOAP call
     *
     * @param string $name The method to call
     * @access public
     * 
     * @return mixed
     */
    public function __dispatch($name)  {        
        if (isset($this->__dispatch_map[$name])) {
            return $this->__dispatch_map[$name];
        }
        return null;
    }

    /**
     * Call to a method of the adapted object
     *
     * @param $method string The method of the adapted object
     * @param $arguments array An array of arguments
     * @access public
     * 
     * @return mixed
     */
    public function __call($method,$arguments) {
        return call_user_func_array(array($this->_object, $method), $arguments);
    }



};
?>