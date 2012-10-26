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
 * An object to handle the HTTP Request
 *
 * PHP Version 5.3
 *
 * @package  Request
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * An object to handle the HTTP Request
 *
 * @package Request
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Request implements IComponent {

    /**
     * An access to the browser component
     *
     * @var FW_Browser
     */
    private $_browser;

    /**
     * The HTTP verb used for this request
     *
     * @var string
     */
    private $_method;

    /**
     * Specifies if this request is using HTTPS
     *
     * @var bool
     */
    private $_https;

    /**
     * Specifies is this request has been made
     * with AJAX
     *
     * @var bool
     */
    private $_isAjaxRequest;


    /**
     * An array of parameters passed via GET
     *
     * @var array
     */
    private $_get;

    /**
     * An array of parameters passed via POST
     *
     * @var array
     */
    private $_post;

    /**
     * The files parameters of an HTTP Request
     *
     * @var array
     */
    private $_files;

    /**
     * An array of parameters passed via the URL
     *
     * @var array
     */
    private $_parameters;


    /**
     * The URL of this HTTP request
     *
     * @var string
     */
    private $_requestUrl;
    
    
    private static $_instance;
    
    public static function getInstance() {
        if (self::$_instance===null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * The construtor of Request
     *
     * @return void
     */
    private function __construct() {
        $this->configure(null);
        $this->initialize(array());
    }

    /**
     *  Configures the Request Component
     * @see framework/lib/interfaces/IComponent#configure($parameters)
     */
    public function configure(FW_Container_Parameter $parameters=null) {    	
        $this->_get      = $_GET;        
        $this->_post    = $_POST;                
        $this->_files    = $_FILES;
        $this->_parameters = array();
    }

    /**
     * Initializes the Request component
     * @see framework/lib/interfaces/IComponent#initialize($arguments)
     */
    public function initialize(array $arguments=array()) {
        if (isset($_SERVER["REQUEST_URI"])) {
            $this->_requestUrl = urldecode(trim($_SERVER["REQUEST_URI"],'/'));
        }
        else {
            $this->_requestUrl = "";
        }
				
    }



    /**
     * Gets the URL of this request
     *
     * @return string
     */
    public function getServerURLClean() {
        $url = $this->getServerURL();
        $pos = strpos($url,".php");
        if ($pos!==false) {
            $cleanURL = substr($url,$pos+4);
            if ($cleanURL===false) {
                return "/";
            }
            return $cleanURL;
        }
        return $url;        
    }


    /**
     * Gets the URL
     *
     * @return mixed
     */
    public function getServerURL() {
        return $_SERVER["PHP_SELF"];
    }
    
    public function getFullURL() {
        $url = '/';
        if ( (isset($_SERVER["REQUEST_URI"])) && (isset($_SERVER["SERVER_NAME"])) ) {        
            $url = "http://{$_SERVER["SERVER_NAME"]}{$_SERVER["REQUEST_URI"]}";
        }
        return $url;
    }


    /**
     * Checks the request was made by AJAX
     *
     * @return bool
     */
    public function isAjaxRequest() {
        $this->_isAjaxRequest = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])==="xmlhttprequest") {
            $this->_isAjaxRequest = true;
        }
        return $this->_isAjaxRequest;
    }

    /**
     * Checks if the request was
     * made using HTTPS
     *
     * @return boold
     */
    public function isHttps() {
        $this->_https = false;
        if (isset($_SERVER["HTTPS"]) && ((bool) $_SERVER["HTTPS"]===true) ) {
            $this->_https = true;
        }
        return $this->_https;
    }

    public function isPost() {
        return ($this->getMethod()==="POST");
    }

    public function isGet() {
        return ($this->getMethod()==="GET");
    }

    public function isPut() {
        return ($this->getMethod()==="PUT");
    }

    public function isDelete() {
        return ($this->getMethod()==="DELETE");
    }




    /**
     * Gets the HTTP method for this
     *  request
     *
     * @return string
     */
    public function getMethod() {
        $this->_method    = "";
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->_method = $_SERVER['REQUEST_METHOD'];
        }
        return strtoupper($this->_method);
    }


    /**
     * Gets the HTTP Referrer for this request
     *
     * @return string
     */
    public function getHttpReferrer() {
        $referrer = "";
        if (isset($_SERVER["HTTP_REFERER"])) {
            $referrer = $_SERVER["HTTP_REFERER"];
        }
        return $referrer;
    }

    /**
     * Gets the URL
     *
     * @return void
     */
    public function getUrl() {
        $url = "/";
        if (isset($_SERVER["argv"])) {
            $argv =  FW_Context::getInstance()->argv;
            if (count($argv)>1) {
                $url = $argv[1];
            }
            return $url;
        }
        if (isset($_SERVER["REQUEST_URI"])) {
	        $url     = $_SERVER["REQUEST_URI"];
	        $pos     = strpos($url,".php");

	        if (strpos($url,"index.php")) {
	            $url = explode("index.php",$url);
	            $url = $url[1];
	        }
	        if (strpos($url,"cron.php")) {
	            $url = explode("cron.php",$url);
	            $url = $url[1];
	        }
	        $baseURL = FW_Config::getInstance()->get("core.global.baseURI");
	        $url            = explode($baseURL,$url);            
	        if (count($url)>1) {
	            $url = rtrim($url[1],'/');
	            return '/'.$url;                
	        }
	        else {
	            $url = rtrim($url[0],'/');
	            return $url;
                
	        }	        
        }        
    }





    /**
     * Gets all parameters passed by the URL
     *
     * @return void
     */
    public function getParameters() {
        //return FW_Util_String::getInstance()->sanitize($this->_parameters);
    }
    
    public function getRawParameters() {
        return $this->_parameters;
    }


    /**
     * Gets a parameter passed by the URL
     *
     * @param string $name Name of the parameter
     *
     * @return mixed
     */
    public function getParameter($name) {
        if (isset($this->_parameters[$name])) {
            return FW_Util_String::getInstance()->sanitize($this->_parameters[$name]);
        }
    }
    
    public function getRawParameter($name) {
        if (isset($this->_parameters[$name])) {
            return $this->_parameters[$name];
        }
    }

    /**
     * Registers a parameter
     *
     * @param string $name Name of the parameter
     * @param string $value Value for the parameter
     *
     * @return void
     */
    public function registerParameter($name,$value) {
        $this->_parameters[$name]        = FW_Util_String::getInstance()->sanitize($value);        
    }

    /**
     * Gets all the parameters passed via GET
     *
     * @return array
     */
    public function getGetParameters() {
        return $this->_get;
    }

    /**
     * Gets a parameter sent via GET
     *
     * @param string $name Parameter name
     * @return string
     */
    public function getGetParameter($name) {
        if (isset($this->_get[$name])) {
            return $this->_get[$name];
        }

    }

    /**
     * Gets all the parameters sent via POST
     *
     * @return array
     */
    public function getPostParameters() {
        return $this->_post;
    }


    /**
     * Gets a parameter sent via POST
     *
     * @param string $name Parameter name
     * @return string
     */
    public function getPostParameter($name) {
        if (isset($this->_post[$name])) {
            return $this->_post[$name];
        }
    }

    /**
     * Gets all the file parameters
     *
     * @return array
     */
    public function getFileParameters() {
        return $this->_files;
    }


    /**
     * Gets a file parameter
     *
     * @param string $name Parameter name
     * @return string
     */
    public function getFileParameter($name) {
        if (isset($this->_files[$name])) {
            return $this->_files[$name];
        }
    }


    /**
     * Gets the IP address of the http request
     *
     * @return string
     */
    public function getClientIP() {
        $ip = "";
        if (!empty($_SERVER["HTTP_CLIENT_IP"]))    {
            $ip    =    $_SERVER["HTTP_CLIENT_IP"];
        }
        elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip    =    $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else {
            $ip    =    $_SERVER["REMOTE_ADDR"];
        }
        return $ip;
    }


    /**
     * Gets the user agent of the request
     *
     * @return string
     */
    public function getUserAgent() {
        $agent = "";
        if (isset($_SERVER['HTTP_USER_AGENT']))  {
            $agent =  $_SERVER['HTTP_USER_AGENT'];
        }
        return $agent;
    }

    /**
     * Gets the name of the running script
     *
     * @return string
     */
    public function getScriptName() {
        return $_SERVER["PHP_SELF"];
    }


    /**
     * Gets the accepted languages (specified in the
     * HTTP Request headers).
     *
     * @return array
     */
    public function getAcceptedLanguages() {
        $langs   = array();
        $pattern = '/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i';
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            preg_match_all($pattern, $_SERVER['HTTP_ACCEPT_LANGUAGE'],$matches);
            if (count($matches[1])) {
                $langs = array_combine($matches[1],$matches[4]);

                foreach ($langs as $lang => $val) {
                    if ($val === '') {
                        $langs[$lang] = 1;
                    }
                }
                arsort($langs, SORT_NUMERIC);
            }
        }
       return $langs;
    }


    public function browser() {
        if ($this->_browser===null) {
            $this->_browser = FW_Browser::getInstance();
        }
        return $this->_browser;
    }

    public function get($parameter="",$stripTags=true) {
        $value = null;
        if (!empty($parameter)) {
            $value = $this->getGetParameter($parameter);
        }
        else {
            $value = $this->getGetParameters();
        }
        $value = FW_Util_String::getInstance()->sanitize($value,$stripTags);
        return $value;    
    }
    
    public function rawGet($parameter="")    {
        if (!empty($parameter)) {
            return $this->getGetParameter($parameter);
        }
        return $this->getGetParameters();
    }

    public function post($parameter="",$stripTags=true) {
        $value = null;
        if (!empty($parameter)) {
            $value = $this->getPostParameter($parameter);
        }
        else {
            $value = $this->getPostParameters();
        }
        $value = FW_Util_String::getInstance()->sanitize($value,$stripTags);
        return $value;
    }
    
    public function rawPost($parameter="")    {
        if (!empty($parameter)) {
            return $this->getPostParameter($parameter);
        }
        return $this->getPostParameters();
    }

    public function parameter($parameter="",$stripTags=true) {
        $value = null;
        if (!empty($parameter)) {
            $value =  $this->getParameter($parameter);
        }
        else {
            $value = $this->getParameters();
        }
        $value = FW_Util_String::getInstance()->sanitize($value,$stripTags);
        return $value;
    }
    
    public function rawParameter($parameter="") {
        if (!empty($parameter)) {
            return $this->getParameter($parameter);
        }
        return $this->getParameters();
    }
    
    public function cookie($parameter="") {
        if (!empty($parameter)) {
            if (isset($_COOKIE[$parameter])) {
                return $_COOKIE[$parameter];
            }
        }
        return $_COOKIE;
    }

    public function file($parameter="") {        
        if (!empty($parameter)) {
            return $this->getFileParameter($parameter);
        }
        return $this->getFileParameters();
    }

};
?>