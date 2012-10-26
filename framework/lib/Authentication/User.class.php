<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class FW_Authentication_User extends FW_Singleton {

    
    /**
     * The username 
     * 
     * @var string
     */
    public $username;
    
    
    /**
     * The roles of an user
     * 
     * @var mixed
     */
    public $role;
        
    
    /**
     * An string to hold the name of this 
     * website crypted (used to check the authentication
     * in shared hosting environments)
     * 
     * @var string
     */
    public $key;
    
    /**
     * An array to hold the name of the
     * properties used into a LoggedUser.
     * 
     * @see This user has dynamic properties that
     * the developer has to configure in the Authentication
     * Rule
     *  
     * @var array
     */
    private $_keys;

    /**
     * Constructs the Logged User
     * 
     * @param array $data User data
     * 
     * @return void
     */
    public function __construct($data) {
         
        if ($data!==null) {            
            foreach ($data as $key=>$value) {
                $this->$key  = $value;
            }
            $keys        = array_keys($data);            
            $this->_keys = $keys;
        }
        if (isset($data["role"])) {
            $this->role = array($this->role);
        }

        $baseURL = FW_Context::getInstance()->getParameter("baseURL");
        $this->key = md5($baseURL);
    }
    
    
    /**
     * Sets the roles of the Logged User
     * 
     * @param array $roles The roles
     * 
     * @return void
     */
    public function setRoles(array $roles) {        
        $this->role = $roles;
    }

    /**
     * Gets the username
     * 
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }
    
    
    /**
     * Gets a property of the object
     * 
     * @param string $property The name of the property
     * 
     * @return mixed
     */
    public function __get($property) {
        if (in_array($property,$this->_keys)) {
            if (isset($this->$property)) {
                return $this->$property;
            }
        }
    }
    
    /**
     * Gets the roles
     * 
     * @return mixed
     */
    public function getRole() {
        return $this->role;
    }
    
    public function getRoles() {
        return $this->role;
    }

    /**
     * Gets the key
     * 
     * @return unknown_type
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * Checks if the Logged User has a role
     * 
     * @param string $role The name of the role
     * @return bool
     */
    public function hasRole($role) {        
        $result = false;
        if (is_array($this->role)) {
            if (empty($role)) {
                return true;
            }            
            if (is_array($role)) {                                
                foreach ($role as $aux) {
                    if (in_array($aux,$this->role)) {                        
                        $result = true;                        
                    }
                    
                }
                
            }
            else {
                if (in_array($role,$this->role)) {                    
                    $result = true;
                }
                else {
                    $result = false;
                }
            }
        }
        else {            
            if ($this->role===$role) {                
                $result = true;
            }
            else {                
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Method to deserialize the object
     * 
     * @return unknown_type
     */
    public function __wakeup() {}
    
    /**
     * Method to serialize the object
     * 
     * @return array
     */
    public function __sleep() {
        $sleep = array("username","key","role","_keys");
        $sleep = array_merge($sleep,$this->_keys);        
        return $sleep;
    }
};
?>