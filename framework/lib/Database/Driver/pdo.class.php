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
 * Data Access Provider for MySQL
 *
 * PHP Version 5.2
 *
 * @package  DataBase
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es>
 * @license  BSD Style
 * @link     http://www.andresmartinezsoto.es
 *
 */


/**
 * Class mysqlDriver
 *
 * Data Acces Provider for MySQL
 *
 * @package  DataBase
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es>
 * @license  BSD Style
 * @link     http://www.andresmartinezsoto.es
 *
 */
class FW_Database_Driver_pdo extends FW_Database_Driver_Base {

    private $_result;

    /**
     * How many queries do we have executed?
     *
     * @var int
     */
    private $querycount;


    /**
     * The connection object for this database
     *
     * @var mixed
     * @static
     */
    private static $_link;

    /**
     * The user of the database
     *
     * @var string
     */
    private $_username;

    /**
     * The password of the database
     *
     * @var string
     */
    private $_password;

    /**
     * The name of the database
     *
     * @var string
     */
    private $_database;

    /**
     * The host of the database
     *
     * @var string
     */
    private $_host;

    private $_dsn;


    public function configure(FW_Container_Parameter $parameters=null) {
        if ($parameters!==null) {
            $this->_configuration = $parameters;
        }
    }

    public function initialize(array$arguments= array()) {        
        $this->_result   = null;
        $this->_host     = $this->_configuration->getParameter("host");
        $this->_database = $this->_configuration->getParameter("database");
        $this->_username = $this->_configuration->getParameter("username");
        $this->_password = $this->_configuration->getParameter("password");
        $this->_dsn      = $this->_configuration->getParameter("dsn");
        $this->connect();
    }

    /**
     * The destructor of MySQLDriver
     *
     * @access public
     * @return: void
     */
    public function __destruct()   {
        self::$_link = null;        
    }

    /**
     * Gets the connection resource of a MySQL connection
     *
     * @param  none
     * @access public
     * @return  mixed The MySQL connection resource
     */
    public function getLink()
    {
        return self::$_link;
    }

    /**
     * Gets the info of an existing MySQL connection
     *
     * @access public
     * @return  mixed Information about the existing MySQL connection
     */
    public function info() {

    }


    /**
     * Connects to a MySQL database
     *
     * @access public
     * @return  void
     */
    public function connect() {
        self::$_link = new PDO($this->_dsn,$this->_username,$this->_password,array(PDO::ATTR_PERSISTENT => true));
        $this->select();
        if (self::$_link===false) {
            throw new Exception("No he podido conectar al servidor de bases de datos. ");
        }
        self::$_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }

    /**
     * Terminates a MySQL connection
     *
     * @access public
     * @return  void
     */
    function disconnect()  {
        self::$_link = null;
    }

    /**
     * Selects a DataBase on the existing MySQL connection
     *
     * @access public
     * @return  void
     */
    function select()   {
        return true;
    }

    /**
     * Queries the database
     *
     * @param string $query The SQL query
     * @access public
     * @return  mixed The result of the query
     */
    public function query($query)   {                
        $this->_result = null;
        
        if (self::$_link===null) {
            $this->connect();
        }
                
        $result = null;        
        if (preg_match('/SELECT(.*)?/',$query)) {            
            $result  = self::$_link->query($query);            
        }
        else if ( (preg_match('/UPDATE(.*)?/',$query)) || (preg_match('/INSERT(.*)?/',$query)) || (preg_match('/CREATE(.*)?/',$query)) ) {            
            $result  = self::$_link->exec($query);            
        }
        else {
            $result  = self::$_link->exec($query);
        }

        if ($result===false) {
            return false;
        }
        $this->_result = $result;
        return $result;
    }

    /**
     * Tries to log an error
     *
     * @param string $query The SQL query that produced the error
     * @param string $error The error that produced the query
     * @access public
     * @return string
     */
    public function dataBaseError($query,$error)   {
        trigger_error("DATABASE | The query {$query} has failed producing the following error {$error}",E_USER_WARNING);
    }

    /**
     * Gets the number of affected rows as a result
     * of executing an SQL query
     *
     * @access public
     * @return integer
     */
    public function affectedRows() {
        if (is_object($this->_result)) {
            $count = $this->_result->columnCount();
        }
        else {
            $count = $this->_result;
        }
        return $count;
    }

    /**
     Gets the number of rows of the last result
     *
     * @access public
     * @return  integer
     */
    public function numRows()  {
        if ($this->_result instanceof PDOStatement) {
            return $this->_result->rowCount();
        }
        else {
            return null;
        }
    }

    /**
     * Fetches the result of a query as Row
     *
     * @access public
     * @return Array
     */
    public function fetchRow()   {
        if (is_object($this->_result)) {
            $row = $this->_result->fetch(PDO::FETCH_NUM);
            return $row;
        }
        else {
            return null;
        }
    }

    /**
     * Fetches the result of a query as array
     *
     * @access public
     * @return Array
     */
    function fetchArray()    {
        if (is_object($this->_result)) {
            $row = $this->_result->fetch(PDO::FETCH_NUM);
            return $row;
        }
        else {
            return null;
        }

    }

    /**
     * Fetches the result of a query as an associative array
     *
     * @access public
     * @return Array
     */
    function fetchAssoc()    {
        if (is_object($this->_result)) {
            $row = $this->_result->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        else {
            return null;
        }
    }

    /**
     * Fetches the result of a query as an object
     *
     * @access public
     * @return object
     */
    function fetchObject()    {
        if (is_object($this->_result)) {
            $row = $this->_result->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        else {
            return null;
        }
    }

    /**
     * Fetches the result as an XML string
     *
     * @access public
     * @return string
     */
    public function fetchXML()
    {
        return DataBase::toXML($this->fetchObject());
    }

    /**
     * Fetches the result as a JSON array
     *
     * @access public
     * @return mixed
     */
    public function fetchJSON()
    {
        return DataBase::toJSON($this->fetchObject());
    }

    /**
     * Check if a table exists
     *
     * @param string $tableName The table to check
     *
     * @param  none
     * @access public
     * @return  boolean
     */
    public function existsTable($tableName)    {
        $query = "SELECT 1 FROM {$tableName};";
        $res   = $this->query($query);
        if ( (!$res) || ($this->_result=false) ) {
            return false;
        }
        return true;
    }

    /**
     * Gets information about a table of the DataBase
     *
     * @param string $tableName The table to get the info
     *
     * @access public
     * @return  Array with the information about the table
     */
    public function getTableFields($tableName)   {
        if (strpos($this->_dsn,"mysql")!==false) {
            return ($this->_getMySQLTableFields($tableName));
        }
        else {
            $fields = array();
            $query  = "SELECT * FROM {$tableName} LIMIT 1";
            $res    = $this->query($query);
            if ( ($res!==false) && ($this->_result) ) {
                $fcount = $res->columnCount();
                for ($i=0; $i<$fcount; $i++) {
                    $metadata = $this->_result->getColumnMeta($i);
                    $field    = array(
                    	"name"  => $metadata["name"],
                    	"value" => null,
                    	"type"  => $this->_translateNativeType($metadata["native_type"]),
                    	"flags" => $metadata["flags"]
                    );
                    $fields[] = $field;
                }
                return $fields;
            } else {
                return null;
            }
        }
    }


    public function getLastInsertId() {
        return (self::$_link->lastInsertId());
    }



    private function _translateNativeType($orig) {
        $trans = array(
            'VAR_STRING' => 'string',
            'STRING' => 'string',
            'BLOB' => 'blob',
            'LONGLONG' => 'int',
            'LONG' => 'int',
            'SHORT' => 'int',
            'DATETIME' => 'datetime',
            'DATE' => 'date',
            'DOUBLE' => 'real',
            'TIMESTAMP' => 'timestamp'
            );
            return $trans[$orig];
    }

    private function _getMySQLTableFields($tableName) {
        $link = mysql_pconnect($this->_host,$this->_username,$this->_password);
        mysql_select_db($this->_database, $link);

        $fields = array();
        $query = "SELECT * FROM {$tableName} LIMIT 1";
        $res = mysql_query($query,$link);
        if ($res!==false) {
            $fcount = mysql_num_fields($res);
            for($i=0;$i<$fcount;$i++) {
                $field = array("name"=>mysql_field_name($res,$i),"type"=>mysql_field_type($res,$i),"length"=>mysql_field_len($res,$i),"flags"=>explode(" ",mysql_field_flags($res,$i)));
                $fields[] = $field;
            }
            return $fields;
        }
        else {
            return null;
        }
    }
    
    
    public function lastInsertedId() {
        if (self::$_link) {
            return self::$_link->lastInsertId();
        }        
    }


};
?>