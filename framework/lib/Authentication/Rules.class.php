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
 * @author andreu
 *
 */
class FW_Authentication_Rules {

    /**
     * An access to the configuration
     * 
     * @var Config
     */
    private $_config;
    
    /**
     * The name of the rule
     * 
     * @var string
     */
    private $_type;
    
    /**
     * Field lengths for this rule
     * 
     * @var array
     */
    private $_lengths;
    
    
    /**
     * Datasource for this rule
     * 
     * @var array
     */
    private $_dataSource;
    
    /**
     * Datasource for the roles
     * 
     * @var array
     */
    private $_roleSource;
    
    
    /**
     * Authentication error codes
     * 
     * @var array
     */
    private $_codes;
    
    
    /**
     * Datasource for user information
     * 
     * @var array
     */
    private $_userSource;

    
    /**
     * Constructs an Authentication Rule
     * 
     * @param string $type The name of the authentication
     * rule
     * 
     * @return void
     */
    public function __construct($type) {
        $this->_configure($type);
        $this->_initialize();
    }

    
    /**
     * Gets a property of the Authentication rule
     * 
     * @param string $property The name of the property
     * @return mixed
     */
    public function __get($property) {
        $property = "_{$property}";
        if (isset($this->$property)) {
            $prop = (object) $this->$property;
            return $prop;
        }
    }

    
    /**
     * Configures an authentication rule
     * 
     * @param string $type The name of the rule
     * @return void
     */
    private function _configure($type) {
        $this->_type       =   $type;
        $this->_config     = FW_Config::getInstance();
        $this->_codes      = array("success"=>null,"forbidden"=>null,"error"=>null,"blocked"=>null);
        $this->_lengths    = array("min"=>null,"max"=>null);
        $this->_dataSource = array();
        $this->_roleSource = array();
        $this->_userSource = null;
    }

    /**
     * Initializes the process to generate an authentication
     * rule
     *
     *    
     * @return bool
     */
    private function _initialize() {
        $rules = null;        
        $rules = $this->_config->authentication->getAuthenticationRule($this->_type);
        if ($rules===null) {
            throw new FW_Authentication_Exception("The selected authentication rule doesn't exists. Please create an authentication rule to be able to use the Authentication component");
        }
        return $this->_setUpRules($rules);
    }
     

     /**
     * Sets information about password lengths     
     *
     * @param  array $rules The rule
     * @return void
     */
    private function _setLengths($rules) {
        $this->_lengths["min"] = (isset($rules["lengths"]["min"]) ? (int) $rules["lengths"]["min"]:6);
        $this->_lengths["max"] = (isset($rules["lengths"]["max"]) ? (int) $rules["lengths"]["max"]:50);
    }

     /**
     * Sets information about the error codes 
     * to be using in the Authentication proccess
     *
     * @param  array $rules The rule
     * @return void
     */
    private function _setCodes($rules) {
        $this->_codes["success"]   = (isset($rules["codes"]["success"]) ? (int) $rules["codes"]["success"]:1);
        $this->_codes["forbidden"] = (isset($rules["codes"]["forbidden"]) ? (int) $rules["codes"]["forbidden"]:0);
        $this->_codes["blocked"]   = (isset($rules["codes"]["blocked"]) ? (int) $rules["codes"]["blocked"]:-1);
        $this->_codes["error"]     = (isset($rules["codes"]["error"]) ? (int) $rules["codes"]["error"]:-2);
    }

     /**
     * Sets information about authentication
     * within a database
     *
     * @param  array $rules The rule
     * @return void
     */
    private function _setDatabaseSource($rules) {
        $this->_dataSource["table"]    = (isset($rules["datasource"]["table"]) ? (string) $rules["datasource"]["table"]:"user");
        $this->_dataSource["username"] = (isset($rules["datasource"]["username"]) ? (string) $rules["datasource"]["username"]:"username");
        $this->_dataSource["password"] = (isset($rules["datasource"]["password"]) ? (string) $rules["datasource"]["password"]:"password");
        $this->_dataSource["status"]   = (isset($rules["datasource"]["status"]) ? (string) $rules["datasource"]["status"]:"status");
        $this->_dataSource["role"]     = (isset($rules["datasource"]["role"]) ? (string) $rules["datasource"]["role"]:"role");
        $this->_dataSource["crypt"]    = (isset($rules["datasource"]["crypt"]) ? (string) $rules["datasource"]["crypt"]:"sha1");
    }

     /**
     * Sets information about authentication within
     * an apache httpasswd file
     *
     * @param  array $rules The rule
     * @return void
     */
    private function _setHtpasswdSource($rules) {
        $this->_dataSource["filename"] = $rules["datasource"]["filename"];
        $this->_dataSource["crypt"]    = $rules["datasource"]["crypt"];
    }


    /**
     * Sets information about authentication via
     * IMAP protocol
     *
     * @param  array $rules The rule
     * @return void
     */
    private function _setImapSource($rules) {
        $this->_dataSource["host"]     = $rules["datasource"]["host"];
        $this->_dataSource["port"]     = $rules["datasource"]["port"];
    }

    /**
     * Sets information about the roles
     *
     * @param  array $rules The rule
     * @return void
     */
    private function _setRoles($rules) {
         
        if (isset($rules["roles"])) {
            $this->_roleSource["roles"]          = (isset($rules["roles"]["multiple"]) ? (bool) $rules["roles"]["multiple"]:false);
            $this->_roleSource["table"]          = (isset($rules["roles"]["table"]) ? (string) $rules["roles"]["table"]:"role");
            $this->_roleSource["joinTable"]      = (isset($rules["roles"]["joinTable"]) ? (string) $rules["roles"]["joinTable"]:"user_has_roles");
            $this->_roleSource["joinUserColumn"] = (isset($rules["roles"]["joinUserColumn"]) ? (string) $rules["roles"]["joinUserColumn"]:"username");
            $this->_roleSource["joinRoleColumn"] = (isset($rules["roles"]["joinRoleColumn"]) ? (string) $rules["roles"]["joinRoleColumn"]:"role");
        }
         
    }

    /**
     * Sets the source for user information
     *
     * @param  array $rules The rule
     * @return void
     */
    private function _setUserSource($rules) {
        if (isset($rules["usersource"])) {
            $this->_userSource = $rules["usersource"];
        }
    }


    /**
     * Sets up one Authentication rule
     *
     * @param  array $rules The rule
     * @return bool
     */
    private function _setUpRules($rules=null) {
        if ($rules===null) {
            throw new FW_Authentication_Exception("No data for rule to setup!");
        }

        /* Set lengths */
        if (isset($rules["lengths"])) {
            $this->_setLengths($rules);
        }
        else {
            $this->_lengths["min"] = 6;
            $this->_lengths["max"] = 50;
        }

        /* Set codes */
        if (isset($rules["codes"])) {
            $this->_setCodes($rules);
        }
        else {
            $this->_codes["success"]   = 1;
            $this->_codes["forbidden"] = 0;
            $this->_codes["blocked"]   = -1;
            $this->_codes["error"]     = -2;
        }
        /* Set datasource */
        if (isset($rules["datasource"])) {
            $this->_dataSource["type"]     = $rules["datasource"]["type"];
             
            if ($this->_dataSource["type"]==="database") {
                $this->_setDatabaseSource($rules);
            }

            if ($this->_dataSource["type"]==="htpasswd") {
                $this->_setHtpasswdSource($rules);
            }
            if ($this->_dataSource["type"]==="imap") {
                $this->_setImapSource($rules);
            }
             
            $this->_setRoles($rules);
             
        }
        else {
            throw new FW_Authentication_Exception("The selected rule hasn't got a datasource. It cannot authenticate througth null!");
        }
         
        if (isset($rules["usersource"])) {
            $this->_setUserSource($rules);
        }
        return true;
    }


    /**
     * Gets the encrypt algorithm to
     * encrypt passwords
     *
     * @return string
     */
    public function getCryptAlgorithm() {
        return ($this->_dataSource["crypt"]);
    }




};
?>