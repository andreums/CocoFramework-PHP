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
class MySQLDriver extends FW_Database_Driver_Base {
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
     * The constructor of MySQLDriver
     *
     * @param array $options An array with the parameters to use with MySQL
     *
     * @access public
     * @return: void
     */
    public function __construct($options=array())
    {
        $this->result   = null;
        $this->host     = $options["host"];
        $this->database = $options["database"];
        $this->user     = $options["username"];
        $this->password = $options["password"];
        $this->connect();
    }

    /**
     * The destructor of MySQLDriver
     *
     * @access public
     * @return: void
     */
    public function __destruct()
    {
        $this->disconnect();
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
    public function getInfo()
    {
        return "This is the MySQL database driver for myFramework1.0";
    }

    /**
     * Connects to a MySQL database
     *
     * @access public
     * @return  void
     */
    public function connect()
    {
        try
        {
            self::$_link = mysql_connect($this->host, $this->user, $this->password);
            $this->select();
            if (self::$_link===false) {
                throw new Exception("No he podido conectar al servidor MySQL. ");
            }
        }
        catch (Exception $e)
        {
            die($e->getMessage() );
        }

    }

    /**
     * Terminates a MySQL connection
     *
     * @access public
     * @return  void
     */
    function disconnect()
    {
        @mysql_close(self::$_link);
    }

    /**
     * Selects a DataBase on the existing MySQL connection
     *
     * @access public
     * @return  void
     */
    function select()
    {
        mysql_select_db($this->database, self::$_link);
    }

    /**
     * Queries the database
     *
     * @param string $query The SQL query
     * @access public
     * @return  mixed The result of the query
     */
    public function query($query)
    {
        $result  = mysql_query($query, self::$_link);
        if (!$result) {
            $this->dataBaseError($query,mysql_error(self::$_link));
            return;
        }
        $this->result = $result;
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
    public function affectedRows()
    {
        $count = @mysql_affected_rows(self::$_link);
        return $count;
    }

    /**
     Gets the number of rows of the last result
     *
     * @access public
     * @return  integer
     */
    public function numRows()
    {
        $count = @mysql_num_rows($this->result);
        return $count;
    }

    /**
     * Fetches the result of a query as Row
     *
     * @access public
     * @return Array
     */
    public function fetchRow()
    {
        $row = @mysql_fetch_row($this->result);
        return $row;
    }

    /**
     * Fetches the result of a query as array
     *
     * @access public
     * @return Array
     */
    function fetchArray()
    {
        $row = @mysql_fetch_array($this->result);
        return $row;
    }

    /**
     * Fetches the result of a query as an associative array
     *
     * @access public
     * @return Array
     */
    function fetchAssoc()
    {
        $row = @mysql_fetch_assoc($this->result);
        return $row;
    }

    /**
     * Fetches the result of a query as an object
     *
     * @access public
     * @return object
     */
    function fetchObject()
    {
        $row = @mysql_fetch_object($this->result);
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
    public function existsTable($tableName)
    {
        $query = "SHOW TABLES;";
        $res   = mysql_query($query, self::$_link);
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
    public function getTableFields($tableName)
    {
        $fields = array();
        $query  = "SELECT * FROM {$tableName} LIMIT 1";
        $res    = mysql_query($query, self::$_link);
        if ($res!==false) {
            $fcount = mysql_num_fields($res);
            for ($i=0; $i<$fcount; $i++) {
                $field    = array(
                "name" => mysql_field_name($res, $i),
                "value" => null,
                "type" => mysql_field_type($res, $i),
                "flags" => explode(" ", mysql_field_flags($res, $i))
                );
                $fields[] = $field;
            }
            return $fields;
        } else {
            return null;
        }
    }
};




?>