<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class FW_Authentication_Handler_Imap extends FW_Authentication_Handler_Base {

    private $_hostname;
    private $_port;
    private $_database;
    private $_prefix;

    protected function _configure(FW_Container_Parameter $parameters=null) {
        $rules = "";
        if ($parameters!==null) {
            if ($parameters->hasParameter("rules")) {
                $rules = $parameters->getParameter("rules");
            }
        }
        else {
            $rules = $this->config()->get("authentication.global.defaultRules");
        }

        $this->_rulesName = $rules;
    }

    protected function _initialize(array $arguments=array()) {
        if ($this->_rules->dataSource->type!=="imap") {
            throw new FW_Authentication_Exception("The Authentication Rules you're trying to use with this Handler are not for imap. Aborting");
        }
        else {
            $this->_port     = $this->_rules->dataSource->port;
            $this->_hostname = $this->_rules->dataSource->host;
        }

    }


    public function login() {
        // Get username && password
        $username = $this->_credentials->getUsername();
        $password = $this->_credentials->getPassword();

        if ($username==='' || $password==='') {
            return ($this->_rules->codes->forbidden);
        }
        else {
            if ( strlen($username)<($this->_rules->lengths->min) || strlen($username)>($this->_rules->lengths->max) ) {
                return ($this->_rules->codes->forbidden);
            }
        }

        $imapString = "";
        if ($this->_port==="993") {
            $imapString = "{".$this->_hostname.":993/ssl}";
        }
        else {
            $imapString = "{".$this->_hostname.":".$this->_port."}";
        }
        $mbox = imap_open($imapString,$username,$password);
        if (!imap_errors()) {
            $this->_database = FW_Database::getInstance();
            $this->_prefix   = $this->_database->getPrefix();
            $user = $this->_getSessionUser();
            if ($user!==null) {
                $this->_setSessionUser($user);
            }
            return ($this->_rules->codes->success);
        }
        else {
            return ($this->_rules->codes->forbidden);
        }
        return ($this->_rules->codes->error);
    }

    public function logout() {
        try {
            FW_Session::destroy();
        }
        catch (Exception $ex) {
            FW_Session::start();
            FW_Session::destroy();
        }

        if (!headers_sent()) {
            $config = FW_Config::getInstance();
            $baseURL = $config->getParameter("base","default","baseurl");
            $location = "Location: {$baseURL}";
            header($location);
            return;
        }
    }


    
  
     
};
?>