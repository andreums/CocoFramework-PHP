<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Authentication_Handler_Database extends FW_Authentication_Handler_Base {
        
        
        protected function _configure(FW_Container_Parameter $parameters=null) {
            $rules = "";
            if ($parameters!==null) {                
                if ($parameters->hasParameter("rules")) {
                    $rules = $parameters->getParameter("rules");                                                            
                }
            }            
            else {
                $rules = $this->config()->get("authentication.global.default");           
            }
            
            $this->_rulesName = $rules;                                    
        }
        
        /* (non-PHPdoc)
         * @see framework/lib/Authentication/Handler/FW_Authentication_Handler_Base#_initialize($arguments)
         */
        protected function _initialize(array $arguments=array()) {
            if ($this->_rules->dataSource->type!=="database") {
                throw new FW_Authentication_Exception("The Authentication Rules you're trying to use with this Handler are not for database. Aborting");
            }
            $this->_database = FW_Database::getInstance();
            $this->_prefix   = $this->_database->getPrefix();
        }
        
      
        /* Logs in to the system
         * @see framework/lib/Authentication/Handler/FW_Authentication_Handler_Base#login()
         */
        public function login() {             
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
            
            $cryptedPassword = $this->_cryptPassword(($this->_rules->getCryptAlgorithm()),$password);
            $query                          = $this->_getAuthenticationSQL($username,$cryptedPassword);                        
            
            $this->_database->query($query);
            
            
            if ($this->_database->numRows()===0) {
                return ($this->_rules->codes->forbidden);
            }
            else {
                FW_Session::destroy();
                FW_Session::start();
                $userData = $this->_database->fetchAssoc();
                
                if ($userData != null) {
                    if ( ($userData[($this->_rules->dataSource->status)]===0) || ($userData[($this->_rules->dataSource->status)]==="0") || ($userData[($this->_rules->dataSource->status)]==="false") ) {
                        return ($this->_rules->codes->blocked);
                    }
                    else {                        
                        $user = $this->_getSessionUser();                        
                        if ($user!==null) {
                            $this->_setSessionUser($user);
                        }                        
                        return ($this->_rules->codes->success);
                    }
                }
                else {
                    return ($this->_rules->codes->error);
                }
              
            }
            return ($this->_rules->codes->forbidden);            
        }
        
        
            
        /**
         * Gets the SQL query to authenticate an user
         * 
         * @param string $username The username
         * @param string $password The password         
         * 
         * @return string The query
         */
        private function _getAuthenticationSQL($username,$password) {
            $query = "SELECT {$this->_rules->dataSource->username},{$this->_rules->dataSource->status} FROM {$this->_prefix}{$this->_rules->dataSource->table} WHERE ( {$this->_rules->dataSource->username}='{$username}' AND {$this->_rules->dataSource->password}='{$password}')";
            return $query;
        }
        
        
        /**
         * Gets the SQL query to change the password
         * of an user
         * 
         * @param string $username The username
         * @param string $oldPassword The old password
         * @param string $newPassword The new password
         * 
         * @return string The query 
         */
        private function _getPasswordChangeSQL($username,$oldPassword,$newPassword) {
            $query = "UPDATE {$this->_prefix}{$this->_rules->dataSource->table} SET {$this->_rules->dataSource->password}='{$newPassword}' WHERE {$this->_rules->dataSource->username}='{$username}' AND {$this->_rules->dataSource->password}='{$oldPassword}' ";
            return $query;
        }
    
        
        /**
         * Changes the password of an user
         * 
         * @param string $username The username
         * @param string $oldPassword The old password
         * @param string $newPassword The new password
         * 
         * @return bool
         */
        public function changePassword($username,$oldPassword,$newPassword) {
            
            $result         = false;            
            $cryptAlgorithm = $this->_rules-> getCryptAlgorithm();
         
            // Encrypt the password            
            $oldPasswordCrypted   = $this->_cryptPassword($cryptAlgorithm,$oldPassword);
            $newPasswordCrypted = $this->_cryptPassword($cryptAlgorithm,$newPassword);
                        
            $query = $this->_getAuthenticationSQL($username,$oldPasswordCrypted);
            $this->_database->query($query);
            if ($this->_database->numRows()===0) {                               
                $result = false;
            }
            else {
                $query = $this->_getPasswordChangeSQL($username,$oldPasswordCrypted,$newPasswordCrypted);                                
                $this->_database->query($query);
                if ($this->_database->affectedRows()>0) {                               
                    $result = true;
                }
                else {
                    $result = false;
                }                
            }
            return $result;
        }


    public function logout() {
        session_destroy();
        session_start();        
    }
    
    };
?>