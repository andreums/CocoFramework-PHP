<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Database extends FW_Singleton implements IComponent  {

    private $_connections;
    private $_connection;

    /**
     * The constructor of database
     *
     * @param FW_Container_Parameter $parameters parameters
     */
    public function __construct(FW_Container_Parameter$parameters=null) {        
        $this->configure($parameters);
        $this->initialize(array());
    }
    
    public function __desctruct() {
        $this->disconnect();
    }



    /**
     * Configures this component
     *
     * @param FW_Container_Parameter $parameters parameters for the configuration
     *
     *  @return void
     */
    public function configure(FW_Container_Parameter$parameters=null) {        
        $this->_connections = array();
        $connectionsConfig  = FW_Environment::getInstance()->getDataBase();
        if (count($connectionsConfig)) {
            foreach ($connectionsConfig as $key=>$config) {
                $connection = $this->_createConnection($key,$config);
                $this->_connections [$key]= $connection;
            }
        }
    }

    /**
     * Initializes this component
     *
     * @param array $arguments an aray of arguments
     *
     * @return void
     */
    public function initialize(array$arguments= array()) {
        if (!empty($this->_connections)) {
            $default = array_keys($this->_connections);
            $default = $default[0];
            $this->_connection =  $this->_connections[$default];
        }

    }

    /**
     * Creates the information about a database connection
     *
     * @param string $name The name for this connection
     * @param array $configuration An array with configuration for this connection
     *
     * @return array
     */
    private function _createConnection($name,array $configuration=array()) {
        $connection = array (
                "name"          => $name,
                "driver"        => $this->_createDriver($configuration["driver"],$configuration),
                "prefix"        => $configuration["prefix"],
                "configuration" => $configuration
        );
        return $connection;
    }

    /**
     * Creates and initializes a connection driver
     *
     * @param string $name The name of the driver
     * @param array $configuration The configuration for the driver
     *
     * @return FW_Database_Driver
     */
    private function _createDriver($name,$configuration) {
        $parameters = new FW_Container_Parameter();
        $parameters->fromArray($configuration);

        $driver = $this->_loadDriver($name);
        $driver->configure($parameters);
        $driver->initialize(array());
        return $driver;
    }

    /**
     * Use a connection defined in the configuration
     *
     * @param string $name The name of the connection to use
     */
    public function useConnection($name) {
        if (isset($this->_connections[$name])) {
            $this->_connection = $this->_connections[$name];
        }
    }

    /**
     * Loads the Driver of the DataBase
     *
     * @access private
     *
     * @return void
     */
    private function _loadDriver($name) {
        try {
            $driverName   = "{$name}";
            $driverName   = "FW_Database_Driver_{$driverName}";
            $driverObject = new $driverName();
            return $driverObject;
        }
        catch (Exception $ex) {
            FW_Database_Exception("Error while loading the database driver {$driverName} please check if the driver exists {$ex->getMessage()}");
        }
    }

    /**
     * Gets information about the current driver
     *
     * @access public
     *
     * @return string
     */
    public function getInfo() {
        return $this->_connection["driver"]->getInfo();
    }

    /**
     * Connects to the database
     *
     * @access public
     *
     * @return bool
     */
    public function connect() {
        return $this->_connection["driver"]->connect();
    }

    /**
     * Disconnects from the database
     *
     * @access public
     *
     * @return bool
     */
    public function disconnect() {
        return $this->_connection["driver"]->disconnect();
    }

    /**
     * Queries the database
     *
     * @param  string The SQL query
     * @access public
     *
     * @return mixed The result
     */
    public function query($query) {
        $fp = fopen("/tmp/sql.txt","a+");
        if ($fp) {
            $contents = "Query: {$query} ".date("Y-m-d H:i:s")."\n\n";
            fwrite($fp,$contents,strlen($contents));
        }
        fclose($fp);        
        return $this->_connection["driver"]->query($query);
    }

    /**
     * Gets the affected rows for a query
     *
     * @access public
     *
     * @return int Number of affected rows
     */
    public function affectedRows() {
        return $this->_connection["driver"]->affectedRows();
    }

    /**
     * Gets the number of rows from a result
     *
     * @access public
     *
     * @return int Number of rows of the result
     */
    public function numRows() {
        return $this->_connection["driver"]->numRows();
    }

    /**
     * Fetches the result of a query as array
     *
     * @access public
     *
     * @return Array
     */
    public function fetchArray() {
        return $this->_connection["driver"]->fetchArray();
    }

    /**
     * Fetches the result of a query as Row
     *
     * @access public
     *
     * @return Array
     */
    public function fetchRow() {
        return $this->_connection["driver"]->fetchRow();
    }

    /**
     * Fetches the result of a query as an associative array
     *
     * @access public
     *
     * @return Array
     */
    public function fetchAssoc() {
        return $this->_connection["driver"]->fetchAssoc();
    }

    /**
     * Fetches the result of a query as an object
     *
     * @access public
     *
     * @return object
     */
    public function fetchObject() {
        return $this->_connection["driver"]->fetchObject();
    }

    /**
     * Checks if a table exists
     *
     * @param string $tableName The name of the table
     * @access public
     *
     * @return bool
     */
    public function existsTable($name) {
        return $this->_connection["driver"]->existsTable($name);
    }

    /**
     * Gets info about a table of the database
     *
     * @param string $tableName The name of the table
     * @access public
     *
     * @return Array
     */
    public function getTableFields($tableName) {
        return $this->_connection["driver"]->getTableFields($tableName);
    }

    /**
     * Inits a transaction on the database (where available)
     *
     * @return mixed
     */
    public function begin() {
        return $this->query("BEGIN");
    }

    /**
     * Cancels a running transaction (where available)
     *
     * @return mixed
     */
    public function rollback() {
        return $this->query("ROLLBACK");
    }

    /**
     * Makes commit of a running transaction (where available)
     *
     * @return mixed
     */
    public function commit() {
        return $this->query("COMMIT");
    }

 	/**
     * Gets the last ID of an inserted row into the database
     * (if available in the driver)
     *
     * @return mixed
     */
    public function getLastInsertedId() {
        return $this->_connection["driver"]->lastInsertedId();
    }


    /**
     * Gets the prefix of the tables of the database
     *
     * @return string
     */
    public function getPrefix() {
        return $this->_connection["prefix"];
    }

    /**
     * Gets the name of the connection in use
     *
     * @return string
     */
    public function getConnectionInUse() {
        return $this->_connection["name"];
    }
    
    
    public function __call($method,$arguments) {
        return call_user_func_array(array($this->_connection["driver"],$method),$arguments);
    }




}
?>