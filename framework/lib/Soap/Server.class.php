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
 * A wrapper class over the PEAR:SOAP server
 *
 * PHP Version 5.3
 *
 * @package  SOAP
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/*
 * Remove this line if you have been installed PEAR::SOAP
 */
require_once "framework/lib/external/soap/includes.php";


/**
 *  A wrapper class over the PEAR:SOAP server to adapt HMVC actions to 
 *  be used as as SOAP webservice
 *
 * @package SOAP
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Soap_Server extends FW_Singleton implements IComponent {


    /**
     * The routes to serve in this service
     * 
     * @var array
     */
    private $_routes;    
     

    /**
     * The server object
     * 
     * @var PEAR::SOAP
     */
    private $_server;
    
    /**
     * The name of this webservice
     * 
     * @var string
     */
    private $_name;

    /**
     * The description of this webservice
     *
     * @var strint
     */
    private $_description;



    /**
     * The namespace for this webservice
     *
     * @var string
     */
    private $_namespace;

    /**
     * The constructor of the SOAP server
     *
     * @param FW_Container_Parameter $parameters Parameters to configure the server
     *
     * @return void
     */
    public function __construct(FW_Container_Parameter $parameters=null) {
        $this->configure($parameters);
        $this->initialize(array());
    }

    /**
     * Configures the SOAP server
     *
     * @param FW_Container_Parameter $parameters
     *
     * @return void
     */
    public function configure(FW_Container_Parameter $parameters=null) {

        $routes      = array();
        $name        = "soap";
        $namespace   = "soap";
        $description = "soap";
        $context     = FW_Context::getInstance();
        $config      = $this->_getConfiguration();        

        if (isset($config["namespace"])) {
            $namespace = $config["namespace"];
        }

        if (isset($config["name"])) {
            $name      = $config["name"];
        }

        if (isset($config["description"])) {
            $description = $config["description"];
        }


        if ($parameters!==null) {

            if ($parameters->hasParameter("namespace")) {
                $namespace = $parameters->getParameter("namespace");
            }

            if ($parameters->hasParameter("routes")) {
                $routes    = $parameters->getParameter("routes");
            }
             
        }


        $soapRoutes = $context->router->route;
        if ($soapRoutes!==null) {
            $routes = $soapRoutes;
        }
        
        $namespace = "proves";
        $name      = "proves";

        $this->_name        = $name;
        $this->_routes      = $routes;
        $this->_namespace   = $namespace;
        $this->_description = $description;

    }

    /**
     * Initializes the SOAP Server
     * 
     * @param array $arguments
     * 
     * @return void
     */
    public function initialize(array $arguments=null) {
        $this->_server = new SOAP_Server();
        $this->_server->setDefaultNamespace($this->_namespace);
        $this->setServiceRoutes();
        $this->serve();
    }

    /**
     * Sets the routes that will be used in this webservice
     *
     * @param array $routes The routes that will be used in the webservice
     *
     * @return void
     */
    public function setServiceRoutes(array $routes=array()) {
        if (empty($routes)) {
            $routes = $this->_routes;
        }
        if (count($routes)>0) {
            foreach ($routes as $route) {
                $adapter      = new FW_Soap_Adapter($route);
                $this->_addService($adapter);
                $this->_description .= "<br/>".$adapter->getDescription();
            }
        }
    }


    /**
     * Adds a webservice to the soap server
     *
     * @param FW_Soap_Adapter $soapAdapter The adapter which contains the service
     * to serve
     *
     * @return void
     */
    private function _addService(FW_Soap_Adapter $soapAdapter) {
        $this->_server->addObjectMap($soapAdapter,$this->_namespace,$this->_name,$this->_description);
    }

    /**
     * Puts the server in service mode
     * 
     * @return void
     */
    public function serve() {

        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']=='POST') {
            if (!isset($HTTP_RAW_POST_DATA)){
                $HTTP_RAW_POST_DATA = file_get_contents('php://input');
            }
            $this->_server->service($HTTP_RAW_POST_DATA);
        }
        else {            
            $disco = new SOAP_DISCO_Server($this->_server,$this->_name,$this->_description);
            header("Content-type: text/xml");
            if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'],'wsdl') == 0) {
                echo $disco->getWSDL();
            }
            else {
                echo $disco->getDISCO();
            }
        }
        exit;
    }

    /**
     * Gets the description for a SOAP Webservice
     *
     * @return mixed
     */
    private function _getConfiguration() {
        $data      = "";
        $context   = FW_Context::getInstance();
        $config    = FW_Config::getInstance();
        $service   = trim($context->router->route[0]["url"]);
        $service   = explode('/',$service);
        $service   = $service[count($service)-1];        
        $parameter = "soap.sections.{$service}";        
        if ($config->exists($parameter)) {
            $data      = $config->get($parameter);            
        }
        return $data;
    }

};
?>