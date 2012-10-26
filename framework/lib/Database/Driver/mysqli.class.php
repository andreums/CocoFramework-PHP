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
 * Data Access Provider for MySQLi
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
 * Class mysqliDriver
 *
 * Data Acces Provider for MySQLi
 *
 * @package  DataBase
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es>
 * @license  BSD Style
 * @link     http://www.andresmartinezsoto.es
 *
 */

class FW_Database_Driver_mysqli extends FW_Database_Driver_Base {

    /**
     * The user of the database
     *
     * @var string
     */
    private $_user;

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

    /**
     * Result of a query
     * @var mixed
     */
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
     * The constructor of MySQLiDriver
     *
     * @param array $options An array with the parameters to use with MySQLi
     * @access public
     * @return: void
     */
    public function initialize(array $arguments= array()) {
        $this->_result   = null;
        $this->_host     = $this->_configuration->getParameter("host");
        $this->_database = $this->_configuration->getParameter("database");
        $this->_username = $this->_configuration->getParameter("username");
        $this->_password = $this->_configuration->getParameter("password");        
        $this->connect();
    }


    /**
     * The destructor of MySQLDriver
     *
     * @access public
     * @return: void
     */
    public function __destruct() {
        $this->disconnect();
    }


    /**
     * Gets the connection resource of a MySQLi connection
     *
     * @access public
     * @return  mixed The MySQL connection resource
     */
    public function getLink() {
        return self::$_link;
    }


    /**
     * Gets information about the current driver
     *
     * @access public
     * @return string
     */
    public function info() {
        return "This is the MySQLImproved database driver for myFramework1.0";
    }

    /**
     * Connects to a MySQL database
     *
     * @access public
     * @return  void
     */
    public function connect() {
        try {
            self::$_link = new MySQLi($this->_host,$this->_username,$this->_password,$this->_database);
            if (self::$_link->connect_error) {
                die('Connect Error (' . self::$_link->connect_errno . ') '.self::$_link->connect_error);
            }

            if (mysqli_connect_error()) {
                die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
            }

            $this->select();

        }
        catch (Exception $e) {
            die($e->getMessage() );
        }

    }

    /**
     * Terminates a MySQLi connection
     *
     * @access public
     * @return  void
     */
    function disconnect() {
        if ($this->result!==null) {
            //mysqli_free_result($this->result);
            //mysqli_close(self::$_link);
        }
    }

    /**
     * Selects a DataBase on the existing MySQLi connection
     *
     * @access public
     * @return  void
     */
    function select() {
        self::$_link->select_db($this->_database);
    }

    /**
     * Queries the database
     *
     * @param string $query The SQL query
     * @access public
     * @return  mixed The result of the query
     */
    function query($query) {
        $_result = self::$_link->query($query);
        if (!$_result) {
            $this->dataBaseError($query,mysqli_error(self::$_link));
            return;
        }
        $this->result = $_result;
        return $this->result;
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
        $count=self::$_link->affected_rows;
        return $count;
    }

    /**
     Gets the number of rows of the last result
     *
     * @access public
     * @return  integer
     */
    public function numRows() {
        $count=$this->result->num_rows;
        return $count;
    }


    /**
     * Fetches the result of a query as Row
     *
     * @access public
     * @return Array
     */
    public function fetchRow() {
        $row=$this->result->fetch_row();
        return $row;
    }

    /**
     * Fetches the result of a query as array
     *
     * @access public
     * @return Array
     */
    function fetchArray() {
        $row=$this->result->fetch_array(MYSQLI_BOTH);
        return $row;
    }

    /**
     * Fetches the result of a query as an associative array
     *
     * @access public
     * @return Array
     */
    function fetchAssoc() {
        $row=$this->result->fetch_assoc();
        return $row;
    }

    /**
     * Fetches the result of a query as an object
     *
     * @access public
     * @return object
     */
    function fetchObject() {
        if (!$this->result) {
            return;
        }
        if ($this->result===true) {

        }
        else {
            $row=$this->result->fetch_object();
        }
        return $row;
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
    public function existsTable($tableName) {

        $link = mysql_pconnect($this->host,$this->user,$this->password);
        mysql_select_db($this->database, $link);

        $query = "SHOW TABLES;";
        $res = mysql_query($query,$link);
        while ($table = mysql_fetch_object($res) ) {
            $name = "Tables_in_{$this->database}";
            if ($table->$name == $tableName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets information about a table of the DataBase
     *
     * @param string $tableName The table to get the info
     *
     * @access public
     * @return  Array with the information about the table
     */
    public function getTableFields($tableName) {

        $link = mysql_pconnect($this->host,$this->user,$this->password);
        mysql_select_db($this->database, $link);

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

};
?>