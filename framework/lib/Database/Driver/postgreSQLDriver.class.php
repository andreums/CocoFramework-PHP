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
 * Data Access Provider for PostgreSQL
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
 * Class postgreSQLDriver
 *
 * Data Access Provider for PostgreSQL
 *
 * @package  DataBase
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es>
 * @license  BSD Style
 * @link     http://www.andresmartinezsoto.es
 *
 */

class FW_Database_Driver_postgreSQLDriver extends FW_Database_Driver_Base {

    /**
     * The user of the database
     *
     * @var string
     */
    private $user;

    /**
     * The password of the database
     *
     * @var string
     */
    private $password;

    /**
     * The name of the database
     *
     * @var string
     */
    private $database;

    /**
     * The host of the database
     *
     * @var string
     */
    private $host;

    /**
     * Result of a query
     * @var mixed
     */
    private $result;

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
     * The constructor of the PostgreSQL driver
     *
     * @param $host string The hostname of the database
     * @param $dbname string The name of the database
     * @param $username string The username of the database
     * @param $password string The password of the database
     * @param $prefix
     * @return unknown_type
     */

    /**
     * The constructor of PostgreSQL
     *
     * @param array $options An array with the parameters to use with PostgreSQL
     * @access public
     * @return void
     */
    public function __construct($options=array())  {
        $this->result   = null;
        $this->host     = $options["host"];
        $this->database = $options["database"];
        $this->user     = $options["username"];
        $this->password = $options["password"];
        $this->connect();
    }

    /**
     * Gets information about the current driver
     *
     * @access public
     * @return string
     */
    public function getInfo() {
        return "This is the PostgreSQL database driver for myFramework1.5";
    }


    /**
     * Connects to a PostgreSQL database
     *
     * @access: public
     * @return  void
     */
    public function connect() {

        $connectionString = "host=".$this->host." port=5432 dbname=".$this->database." user=".$this->user." password=".$this->password;
        try {
            self::$_link = pg_connect($connectionString);
            $this->select();
            if (! self::$_link ) {
                throw new Exception("No he podido conectar al servidor PostgreSQL. ");
            }
        }
        catch (Exception $e) {
            die($e->getMessage() );
        }

    }

    /**
     * Terminates a PostgreSQL connection
     *
     * @access public
     * @return  void
     */
    function disconnect() {
        @pg_close(self::$_link);
    }

    /**
     * Selects a DataBase on the existing PostgreSQL connection
     *
     * @access public
     * @return  void
     */
    function select() {
        return true;
    }


    /**
     * Queries the database
     *
     * @param string $query The SQL query
     * @access public
     * @return  mixed The result of the query
     */
    function query($query) {
        $result=pg_query(self::$_link,$query);
        if (!$result) {
            $this->dataBaseError($query,pg_errormessage());
            return;
        }
        $this->result = $result;
        $this->querycount++;

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
        $count=@pg_numrows();
        return $count;
    }

    /**
     Gets the number of rows of the last result
     *
     * @access public
     * @return  integer
     */
    public function numRows() {
        $count=@pg_numrows($this->result);
        return $count;
    }


    /**
     * Fetches the result of a query as Row
     *
     * @access public
     * @return Array
     */
    public function fetchRow() {
        $row=@pg_fetch_row($this->result);
        return $row;
    }

    /**
     * Fetches the result of a query as array
     *
     * @access public
     * @return Array
     */
    function fetchArray() {
        $row=@pg_fetch_array($this->result);
        return $row;
    }

    /**
     * Fetches the result of a query as an associative array
     *
     * @access public
     * @return Array
     */
    function fetchAssoc() {
        $row=@pg_fetch_assoc($thi->result);
        return $row;
    }

    /**
     * Fetches the result of a query as an object
     *
     * @access public
     * @return object
     */
    function fetchObject() {
        $row=pg_fetch_object($this->result);
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
     * @access public
     * @return  boolean
     */
    public function existsTable($tableName) {
        $exists = pg_meta_data(self::$_link,$tableName);
        if ($exists===false) {
            return false;
        }
        else {
            return true;
        }
    }

    /**
     * Gets information about a table of the DataBase
     *
     * @param string $tableName The table to get the info
     * @access public
     * @return  Array with the information about the table
     */
    public function getTableFields($tableName) {

        $fields = array();

        // Obtain the Primary_Key of the table
        $pkQuery = "SELECT column_name FROM information_schema.key_column_usage WHERE table_name='{$tableName}' AND constraint_name='{$tableName}_id_key' ; ";
        $pkRes = pg_query(self::$_link,$pkQuery);
        $pkArr = pg_fetch_array($pkRes);

        // Obtain the number of fields in the table
        $query = "SELECT * FROM {$tableName} LIMIT 1";
        $res = pg_query(self::$_link,$query);
        $fcount = pg_num_fields($res);

        if ($data!==false) {

            for($i=0;$i<$fcount;$i++) {

                $name = pg_field_name($res,$i);
                $type = pg_field_type($res,$i);
                //$flags = $data[pg_field_name($res,$i)];
                $flags = array();

                if ( in_array($name,$pkArr) ) {
                    $flags[] = "primary_key";
                }
                $field = array("name"=>$name,"value"=>null,"type"=>$type,"flags"=>$flags);
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