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
 * The main point for the Authentication ACL-based system
 *
 * PHP Version 5.3
 * 
 * @package  Authentication
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * The main point for the Authentication ACL-based system
 *
 * @package Authentication
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Authentication extends FW_Singleton implements IComponent {

    /**
     * The type of the Authentication
     * 
     * @var string 
     */
    protected $_type;

    
    /**
     * Authentication handler
     * 
     * @var Authentication_Handler
     */
    protected $_handler;
    
    
    /**
     * Authentication credentials
     * 
     * @var Authentication_Credentials
     */
    protected $_credentials;

    /**
     * Constructs the Authentication component
     * 
     * @param FW_Container_Parameter $parameters The configuration of the Authentication component
     * 
     * @return void
     */
    public function __construct(FW_Container_Parameter $parameters=null) {
       if ($parameters!==null) {
            $this->configure($parameters);
            $this->initialize(array("parameters"=>$parameters));
       }
    }

    /* 
     * Configures the Authentication component
     * (non-PHPdoc)
     * @see framework/lib/interfaces/IComponent#configure($parameters)
     */
    public function configure(FW_Container_Parameter $parameters=null) {

        $type        = "database";
        $credentials = new FW_Authentication_Credentials();
         
        if ($parameters!==null) {

            if ($parameters->hasParameter("type")) {
                $type        = $parameters->getParameter("type");                
            }

            if ($parameters->hasParameter("credentials")) {
                $credentials = $parameters->getParameter("credentials");
            }
        }
        $this->_type        = $type;
        $this->_credentials = $credentials;
    }
    

    /* 
     * Initializes the Authentication component
     * 
     * @see framework/lib/interfaces/IComponent#initialize($arguments)
     */
    public function initialize(array $arguments=array()) {
        $type        = ucfirst($this->_type);
        $credentials = $this->_credentials;
        $className   = "FW_Authentication_Handler_{$type}";
        $handler     = new $className($arguments["parameters"]);
        if ($handler!==null) {
            $handler->setCredentials($credentials);
            $this->_handler = $handler;
        }
    }

     

    /**
     * A method to be sure that the authenticated user
     * is authenticated in this website and not in
     * other website in the same shared hosting
     * 
     * @return bool
     */
    public static function checkUserAuthentication() {        
        $result = false;
        if (self::getInstance()->isLoggedIn()) {
            $user = self::getInstance()->user();
            if ($user!==null) {
                $baseurl   = FW_Config::getInstance()->get("core.global.baseURL");
                $key       = md5($baseurl);
                if ($user->getKey()===$key) {
                    $result = true;
                }
                else {
                    $result = false;
                    session_destroy();
                }                
            }
            else {
                $result = false;
            }            
        }        
        return $result;
    }
       
     

    /**
     * Logs out and return the user
     * to the index page
     *
     * @return null
     */
    public function logout() {
        session_destroy();
        session_regenerate_id();        
        session_start(); 
    } 

    /**
     * Checks if the user is logged in
     *
     * @return bool
     */
    public function isLoggedIn() {
        if (FW_Session::get("user","login") != null) {
            return true;
        }
        return false;
    }
    
    /**
     * Gets the logged in user
     * 
     * @return FW_Authentication_User
     */
    public static function getUser() {               
        $user = null;        
        if (FW_Session::get("user","login")!==null) {
            $user = FW_Session::get("user","login");
            $user = unserialize($user);
        }        
        return $user;
    }

    

    /**
     * Checks if the logged user has a role
     * 
     * @param string $role The role to check
     * 
     * @return bool
     */
    public static function hasRole($role) {
        $authentication = FW_Authentication::getInstance();
        if ($authentication->isLoggedIn()) {
            $user = $authentication->user();            
            if ($user != null) {
                return $user->hasRole($role);
            }
        }
        return false;
    }
     
    /**
     * Logs in to the system via 
     * an Authentication handler
     * 
     * @return mixed
     */
    public function login() {        
        if (!$this->isLoggedIn()) {
            session_destroy();            
            session_start();
            if (!isset($this->_handler)) {
                throw new FW_Authentication_Exception("Authentication handler is not set. Aborting!");
            }
            else {
                return $this->_handler->login();
            }
        }
        return true;
    }

    /**
     * Gets the logged user
     *
     * @return LoggedUser
     */
    public function user() {
        $user = null;
        if ($this->isLoggedIn()) {
            $user = FW_Session::get("user","login");
            $user = unserialize($user);
        }
        return $user;
    }
    
    
    public function __call($method,$arguments) {
        if ($this->_handler!==null) {
            return call_user_func_array(array($this->_handler,$method),$arguments);
        }
    }



};
?>