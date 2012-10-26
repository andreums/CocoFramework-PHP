<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class FW_Database_Driver_sqliteDriver extends FW_Database_Driver_Base {

    private $user;
    private $password;
    private $database;
    private $host;
    private $result;
    private $querycount;
    private $filename;
    private $flags;
    private $key;

    protected static $link;

    public function __construct($options=array()) {

        $this->filename = $options["dbfile"];
        
        $this->connect();

    }


    public function getInfo() {
        return "This is the SQLite3 database driver for myFramework1.0";
    }



    function connect() {

        try {
            self::$link = SQLite3($this->filename,$this->flags,$this->key);

            if (! self::$link ) {
                throw new Exception("No he podido conectar al servidor MySQL. ");
            }
        }
        catch (Exception $e) {
            die($e->getMessage() );
        }

    }

    function disconnect() {
        self::$link->close();
    }

    function select() {
        return true;
    }

    function query($query) {
        $this->result = self::$link->query($query);
        $this->querycount++;
    }

    function dataBaseError($query,$error) {

        print "Query:".$query."<br/>";
        print "Error:".$error."<br/>";
        if ( checkUserRole("root") ) {
            print "<h3>Error de la base de datos</h3>";
            print "El query que produjo el error es: <br/>";
            print $query;
            print "<br/> y produjo el error <br/>";
            print $error;
            logError("internal",$query,$error);
        }

        else {
            print "<h2>Error interno de la aplicación</h2>";
            print "<p>Se ha producido un error interno en la aplicación, vuelva a intentarlo más tarde o envíe un mensaje de error al webmaster con los detalles para reproducir el error.  Muchas gracias <p/>";
        }
    }


    public function affectedRows() {

        $count=self::$link->changes();
        return $count;
    }

    public function numRows() {
        if ($this->result->numColumns() && $this->result->columnType(0) != SQLITE3_NULL) {
            return count($this->result->fetchArray());
        }
        else {
            return 0;
        }
    }

    public function fetchRow() {
        $row = $this->result->fetchArray(SQLITE3_NUM);
        return $row;
    }

    function fetchArray() {
        $row = $this->result->fetchArray(SQLITE3_NUM);
        return $row;
    }

    function fetchAssoc() {
        $row = $this->result->fetchArray(SQLITE3_BOTH);
        return $row;
    }

    function fetchObject() {
        $row = $this->result->fetchArray(SQLITE3_BOTH);
        $object = (object) $row;
        return $object;
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
        $query = "SELECT name FROM sqlite_master WHERE type='table' AND tbl_name='{$tableName}' ";
        $this->result = self::$link->query($query);
        $row = $this->result->fetchArray(SQLITE3_NUM);
        if ($row!=null) {
            return true;
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
        $res    = self::$link->query($query);
        if ($res!==false) {
            $fcount = $res->numColumns();
            for ($i=0; $i<$fcount; $i++) {
                $field    = array(
                    "name" => $res->columnName($i),
                    "value" => null,
                    "type" => $res->columnType($i),
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