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
 * Database SQL Driver for Cache
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
 * Class to implements a Database driver for
 * Cache system
 *
 * @package Cache
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Cache_Driver_Database implements FW_Cache_ICache {

    /**
     * A handler for the database operations
     *
     * @var FW_Database
     */
    private static $_database;



    /**
     * The name of the table wich holds
     * the cache entries
     *
     * @var string
     */
    private $_table;
    
    
    /**
     * The prefix of the tables of the database
     * 
     * @var string
     */
    private $_prefix;

    /**
     * @return void
     */
    public function __construct($parameters) {
        $this->initialize($parameters);
    }

    public function initialize(FW_Container_Parameter $parameters=null) {
        
        self::$_database = FW_Database::getInstance();
        $this->_prefix   = self::$_database->getPrefix();

        if ($parameters!==null) {
            if ($parameters->hasParameter("table")) {
                $this->_table = $parameters->getParameter("table");
            }
            else {
                $this->_table = "cache_object";
            }
        }
        else {
            $this->_table = "cache_object";
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
        
        $prefix = $this->_prefix;
        $table  = $this->_table;

        $query  = "DELETE FROM {$prefix}{$table} ";
        if ($namespace!="") {
            $query .= " WHERE namespace='{$namespace}' ";
        }
        self::$_database->query($query);
        if (self::$_database->affectedRows()) {
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

        $result = null;
        $prefix = $this->_prefix;
        $table  = $this->_table;

        $query = "SELECT * FROM {$prefix}{$table} WHERE (id='{$id}' AND namespace='{$namespace}') ";        
        self::$_database->query($query);

        if (self::$_database->numRows()>0) {
            $result=self::$_database->fetchAssoc();
            $result["cached_at"] = strtotime($result["cached_at"]);
            if ($result!=null) {
                return new FW_Cache_Object($result);
            }
        }
        return false;
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
        
        $prefix = $this->_prefix;
        $table  = $this->_table;
        $data = date("Y-m-d H:i:s");


        if (!$this->get($id,$namespace)) {
            $query  = "INSERT INTO {$prefix}{$table} (id,namespace,value,lifetime,cached_at) VALUES (";
            $query .= " '{$id}','{$namespace}','{$value}','{$lifetime}','{$data}') ";
        }
        else {
            $query = "UPDATE {$prefix}{$table} SET namespace='{$namespace}',value='{$value}',lifetime='{$lifetime}',cached_at='{$data}' WHERE id='{$id}' AND namespace='{$namespace}' ";
        }
        
        self::$_database->query($query);
        if (self::$_database->affectedRows()>0) {
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

        $prefix = $this->_prefix;
        $table  = $this->_table;
        $object = $this->get($id,$namespace);

        if ($object!==false) {
            $query = "DELETE FROM {$prefix}{$prefix} WHERE (id='{$id}' AND namespace='{$namespace}')";
            self::$_database->query($query);
            if (self::$_database->affectedRows()>0) {
                return true;
            }
            return false;
        }
        return false;
    }

};
?>