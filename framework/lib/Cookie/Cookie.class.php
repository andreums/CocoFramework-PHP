<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class FW_Cookie {
    
    private $_request;
    private $_config;
    
    private $_defaultDomain;
    private $_defaultExpires;
    private $_defaultPath;
    private $_defaultHttpOnly;
    private $_defaultSecure;     
    
    
    public function __construct() {
        $this->_setUp();
    }
    
    private function _setUp() {
        $config = $this->config();
        $this->_defaultDomain      = $config->get("cookies.global.defaultDomain");        
        $this->_defaultExpires       = $config->get("cookies.global.defaultExpires");
        $this->_defaultPath            = $config->get("cookies.global.defaultPath");
        $this->_defaultHttpOnly  = $config->get("cookies.global.defaultHttpOnly");
        $this->_defaultSecure       = $config->get("cookies.global.defaultSecure");
    } 
    
    private function config() {
        if ($this->_config===null) {
            $this->_config = FW_Config::getInstance();
        }
        return $this->_config;
    }
    
    private function request() {
        if ($this->_request===null) {
            $this->_request = FW_Request::getInstance();
        }
        return $this->_request;
    }
    
    public function delete($name, $path=null, $domain=null, $secure=null) {
        $this->set($name, '', time() - 86400, $path, $domain, $secure);        
    }
    
    public function get($name ) {
        $cookie = $this->request()->cookie($name);
        if ($cookie!==null) {
            return $cookie;
        }        
    }
    
    
    public function set($name, $value, $expires=null, $path=null, $domain=null, $secure=null, $httponly=null)   {
        if ( ($expires===null) && ($this->_defaultExpires!==null) ) {
                $expires = $this->_defaultExpires;
        }
        
        if ($expires && !is_numeric($expires)) {
            $expires = strtotime($expires); 
        }       
        
        if ( ($path===null) && ($this->_defaultPath!==null) ) {
            $path = $this->_defaultPath;
        }        
        
        if ( ($domain===null) && ($this->_defaultDomain!==null) ) {
            $domain = $this->_defaultDomain;
        }
        
        if ( ($secure===null) && ($this->_defaultSecure!==null) ) {
            $secure = $this->_defaultSecure;
        }
        
        if (  ($httponly===null) && ($this->_defaultHttpOnly!==null) ) {
            $httponly = $this->_defaultHttpOnly;
        }                
        if ( (strlen($value)>0) && ($httponly) ) {            
            setcookie($name, $value, $expires, $path, $domain, $secure, true);                     
        }
        else {            
            setcookie($name, $value, $expires, $path, $domain, $secure);
        }
    }       
    
};
?>