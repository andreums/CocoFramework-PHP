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
abstract class FW_Authentication_Handler_Base {

    protected $_rules;
    protected $_config;
    protected $_rulesName;
    protected $_credentials;
    protected $_database;
    protected $_prefix;

    public function __construct(FW_Container_Parameter $parameters=null) {
        $this->_configure($parameters);
        $this->_loadRules();
        $this->_initialize(array());

    }

    abstract protected function _configure(FW_Container_Parameter $parameters=null);
    abstract protected function _initialize(array $arguments=array());
    abstract public function login();

    /**
     * Loads the Authentication rules to be used
     * with this Authentication handler
     * 
     * @return void
     */
    private function _loadRules() {
        $name = "";
        if (isset($this->_rulesName)) {
            $name = $this->_rulesName;
        }
        else {            
            $name = $this->config()->get("authentication.global.default");
        }
        $this->_rules = new FW_Authentication_Rules($name);
    }
    
    /**
     * Sets the user credentials for this handler
     * 
     * @param FW_Authentication_Credentials $credentials The credentials
     * 
     * @return void
     */

    public function setCredentials(FW_Authentication_Credentials $credentials) {
        $this->_credentials = $credentials;
    }

    /**
     * A method to get the Config component
     * 
     * @return FW_Config
     */
    protected function  config() {
        if ($this->_config===null) {
            $this->_config = FW_Config::getInstance();
        }
        return $this->_config;
    }


    /**
     * Encrypts the password using some encryption algorithm
     *
     * @param string $algorithm The algorithm to be used
     * @param string $password The password to encrypt
     *
     * @return string The password crypted
     */
    protected function _cryptPassword($algorithm,$password) {
        // Encrypt the password
        $cryptedPassword  = "";
        switch ($algorithm) {
            case "plain":
                $cryptedPassword = $password;
                break;

            case "md5":
                $cryptedPassword = md5($password);
                break;

            case "sha1":
                $cryptedPassword = sha1($password);
                break;

            case "crypt":
                $cryptedPassword = crypt($password);
                break;

            default:
                $cryptedPassword = $password;
                break;
        };
        return $cryptedPassword;
    }

     /**
     * Stores an FW_Authentication_User in the session     
     * 
     * @param FW_Authentication_User $user The user to be stored in the session
     * 
     * @return bool
     */

    protected function _setSessionUser(FW_Authentication_User $user) {
        $user = serialize($user);
        if ($user!==null) {
            FW_Session::set("user",$user,"login");
            return true;
        }
        return false;
    }

    /**
     * Gets the SQL query to get information about an user (to build
     * an FW_Authentication_User)
     * 
     * @param string $username The username
     * 
     * @return string
     */
    protected function _getUserQuery($username) {
        $query = "SELECT ";
        if (!empty($this->_rules->userSource->columns)) {
            $i   = 0;
            $max = count($this->_rules->userSource->columns);
            foreach ($this->_rules->userSource->columns as $column) {
                $query .= $column;
                if ($i<($max-1)) {
                    $query .= ',';
                }
                $i++;
            }
        }
        else {
            $query .= '*';
        }
        $query .= " FROM {$this->_prefix}{$this->_rules->userSource->table} WHERE {$this->_rules->userSource->username}='{$username}' ";
        return $query;
    }

    /**
     * Gets the logged user to be stored in the session
     * 
     * @return FW_Authentication_User
     */
    protected function _getSessionUser() {
        $roles    = array();
        $username = $this->_credentials->getUsername();
        $query    = $this->_getUserQuery($username);
        $this->_database->query($query);

        if ($this->_database->numRows()>0) {
            $user  = new FW_Authentication_User($this->_database->fetchAssoc());
            $query = $this->_getRolesQuery($username);
            $this->_database->query($query);            
            if ($this->_database->numRows()>0) {
                while ($row = $this->_database->fetchAssoc()) {                    
                    if ($row["enabled"]==="1") {
                        $roles []= $row["role"];
                    }
                }
            }
            $user->setRoles($roles);
            return $user;
        }
    }

    /**
     * Gets the SQL query to get the roles of an username
     * 
     * @param string $username The username
     * 
     * @return string
     */
    protected function _getRolesQuery($username) {
        $query = "";
        if ($this->_rules->roleSource->roles===true) {
            $query = "SELECT r.role,r.enabled FROM {$this->_prefix}{$this->_rules->roleSource->table} r JOIN {$this->_prefix}{$this->_rules->roleSource->joinTable} j ON (j.{$this->_rules->roleSource->joinRoleColumn}=r.role) WHERE (j.{$this->_rules->roleSource->joinUserColumn}='{$username}' AND r.enabled='1')";
        }
        return $query;
    }

   
    /**
     * Logs out the user of the system
     * 
     * @return void
     */
    public function logout() {
        try {
            FW_Session::destroy();
        }
        catch (Exception $ex) {
            FW_Session::start();
            FW_Session::destroy();
        }

        if (!headers_sent()) {
            $baseURL = FW_Context::getInstance()->getParameter("baseURL");
            $location = "Location: {$baseURL}";
            header($location);
            return;
        }
    }



};
?>