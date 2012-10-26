<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Authentication_Handler_File extends FW_Authentication_Handler_Base {

    private $_database;
    private $_prefix;
    private $_filename;
    private $_crypt;

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
        if ($this->_rules->dataSource->type!=="htpasswd") {
            throw new FW_Authentication_Exception("The Authentication Rules you're trying to use with this Handler are not for htpasswd files. Aborting");
        }
        else {
            $this->_filename = $this->_rules->dataSource->filename;
            $this->_crypt    = $this->_rules->dataSource->crypt;
        }

    }

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
        $line = $this->_getPasswordLineFromFile($username);
        if (strlen($line)===0) {
            return ($this->_rules->codes->forbidden);
        }
        if ($this->_analysePasswordLine($line,$password)) {
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

    private function _analysePasswordLine($line,$password) {
        $result             = false;
        list($lineUsername,$linePassword) = explode(':',$line);

        if ($this->_crypt==="des") {
            $salt     = substr($linePassword,0,2);
            $password = crypt($password,$salt);
            if ($password===$linePassword) {
                $result = true;
            }
        }

        if ($this->_crypt==="md5") {
            $password = $this->_cryptApr1MD5($password,$linePassword);
            if ($password===$linePassword) {
                $result = true;
            }
        }

        if ($this->_crypt==="sha") {
            $password = "{SHA}".base64_encode(pack("H*", sha1($password)));
            if ($password===$linePassword) {
                $result = true;
            }
        }

         
        return $result;
    }


    private function _getPasswordLineFromFile($username) {
        $pwdline = "";
        if ( (is_file($this->_filename)) && (is_readable($this->_filename)) ) {
            $fp = fopen($this->_filename,'r');
            if (!$fp) {
                throw new FW_Authentication_Exception("Can't open authentication .htpasswd file {$this->_filename}");
            }
            while($line=fgets($fp)) {
                $line               = preg_replace('`[\r\n]$`','',$line);
                list($fuser,$fpass) = explode(':',$line);
                if($fuser===$username){
                    $pwdline = $line;
                    break;
                }
            }
            fclose($fp);
        }
        else {
            throw new FW_Authentication_Exception("Can't open authentication .htpasswd file {$this->_filename}");
        }
        return $pwdline;
    }


    // method taken from EZComponents
    private function _cryptApr1MD5($plain,$salt )   {
        if ( preg_match( '/^\$apr1\$/', $salt ) )    {
            $salt = preg_replace( '/^\$apr1\$([^$]+)\$.*/', '\\1', $salt );
        }
        else     {
            $salt = substr( $salt, 0, 8 );
        }
        $text = $plain . '$apr1$' . $salt;
        $bin = pack( 'H32', md5( $plain . $salt . $plain ) );
        for ( $i = strlen( $plain ); $i > 0; $i -= 16 )
        {
            $text .= substr( $bin, 0, min( 16, $i ) );
        }
        for ( $i = strlen( $plain ); $i; $i >>= 1 )
        {
            $text .= ( $i & 1 ) ? chr( 0 ) : $plain{0};
        }
        $bin = pack( 'H32', md5( $text ) );
        for ( $i = 0; $i ^ 1000; ++$i )
        {
            $new = ( $i & 1 ) ? $plain : $bin;
            if ( $i % 3 )
            {
                $new .= $salt;
            }
            if ( $i % 7 )
            {
                $new .= $plain;
            }
            $new .= ( $i & 1 ) ? $bin : $plain;
            $bin = pack( 'H32', md5( $new ) );
        }
        $tmp = '';
        for ( $i = 0; $i ^ 5; ++$i )
        {
            $k = $i + 6;
            $j = $i + 12;
            if ( $j === 16 )
            {
                $j = 5;
            }
            $tmp = $bin[$i] . $bin[$k] . $bin[$j] . $tmp;
        }
        $tmp = chr( 0 ) . chr( 0 ) . $bin[11] . $tmp;
        $tmp = strtr( strrev( substr( base64_encode( $tmp ), 2 ) ),
        'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/',
        './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' );
        return '$apr1$' . $salt . '$' . $tmp;
    }


    private function _getRolesQuery($username) {
        $query = "";
        if ($this->_rules->roleSource->roles===true) {
            $query = "SELECT r.role,r.enabled FROM {$this->_prefix}{$this->_rules->roleSource->table} r JOIN {$this->_prefix}{$this->_rules->roleSource->joinTable} j ON (j.{$this->_rules->roleSource->joinRoleColumn}=r.role) WHERE (j.{$this->_rules->roleSource->joinUserColumn}='{$username}' AND r.enabled='1')";
        }
        return $query;
    }

    private function _getUserQuery($username) {        
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

};
?>