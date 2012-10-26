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
 * SQLite driver for Cache
 *
 * PHP Version 5.3
 *
 * @package  Cache
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * Class that implements an SQLite driver
 * for Cache system
 *
 * @package Cache
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Cache_Driver_SQLite implements FW_Cache_ICache {


    private static $_handler;
    private $_dbfile;

    public function __construct($parameters) {
        $this->initialize($parameters);
    }

    /*
     * A method to initialize this driver
     *
     * @param FW_Container_Parameter $parameters A container of parameters
     *
     * @return void
     */
    public function initialize(FW_Container_Parameter $parameters=null) {

        $dbfile =  "framework/cache/data/cache.db3";
        if ($parameters!==null) {
            if ($parameters->hasParameter("dbfile")) {
                $dbfile = $parameters->getParameter("dbfile");
            }
        }

        $this->_dbfile  = $dbfile;
        self::$_handler = sqlite_open($dbfile);
        if (!self::$_handler) {
            throw new FW_Cache_Exception("SQLite3 driver couldn't connect to the database {$this->_dbfile}");
        }
        else {
            $query  = "SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND tbl_name='cache_object'";
            $result = sqlite_query(self::$_handler,$query);
            $count  = sqlite_fetch_single($result);
            if(!$count) {
                $query = "CREATE TABLE cache_object (id TEXT PRIMARY KEY, namespace TEXT, value TEXT, lifetime TEXT, cached_at DATETIME)";
                sqlite_exec(self::$_handler,$query);
            }
        }
    }

    /**
     * Removes all data of a namespace from the cache
     *
     * @access public
     *
     * @param string $namespace The namespace to remove
     *
     * @return mixed
     */
    public function clean($namespace="") {
        $query = "DELETE FROM cache_object ";
        if ($namespace!="") {
            $query .= " WHERE namespace='{$namespace}' ";
        }
        sqlite_exec(self::$_handler,$query);
        if (sqlite_changes(self::$_handler)) {
            return true;
        }
        return false;
    }


    /**
     * Obtains data from the cache
     *
     * @access public
     *
     * @param  string $id The id of the data stored in the cache
     * @param  string $namespace The namespace of the data stored in the cache
     * @return mixed
     */
    public function get($id, $namespace) {
        $query = "SELECT * FROM cache_object WHERE (id='{$id}' AND namespace='{$namespace}') ";
        $result = sqlite_query(self::$_handler,$query);
        
        if (sqlite_num_rows($result)) {            
            $res=sqlite_fetch_array($result);
            if ($res!=null) {
                return new FW_Cache_Object($res);
            }
        }        
    }

    /**
     * Stores data into the cache
     *
     * @access public
     * @param  string $id The id of the data stored in the cache
     * @param  string $namespace The namespace of the data stored in the cache
     * @param  mixed  $value The value to store in cache
     * @param  double $lifetime The lifetime of the data to be stored
     *
     * @return mixed
     */
    public function save($id, $namespace, $value, $lifetime) {

        $data = date("Y-m-d H:i:s");

        $query  = "SELECT * FROM cache_object WHERE id='{$id}' AND namespace='{$namespace}' ";
        $result = sqlite_query(self::$_handler,$query);

        if (sqlite_num_rows($result)>0) {
            $query = "UPDATE cache_object SET namespace='{$namespace}',value='{$value}',lifetime='{$lifetime}',cached_at='{$data}' WHERE id='{$id}' ";
        }
        else {
            $query  = "INSERT INTO cache_object (id,namespace,value,lifetime,cached_at) VALUES (";
            $query .= " '{$id}','{$namespace}','{$value}','{$lifetime}','{$data}') ";
        }

        $result = sqlite_query(self::$_handler,$query);
        if (sqlite_changes(self::$_handler)) {
            return true;
        }
        else {
            return false;
        }

    }

    /**
     * Removes data from cache
     *
     * @access public
     * @param string $id The id of the contents
     * @param string $namespace A namespace
     * @return bool
     */
    public function remove($id, $namespace) {
        $object = $this->get($id,$namespace);
        if ($object!==false) {
            $query = "DELETE FROM cache_object WHERE (id='{$id}' AND namespace='{$namespace}')";
            $result = sqlite_exec(self::$_handler,$query);
            if (sqlite_changes(self::$_handler)) {
                return true;
            }
            return false;
        }
        return false;
    }

};
?>