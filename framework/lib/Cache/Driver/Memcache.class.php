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
 * Memecache Driver for Cache
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
 * Class to implements a Memcache driver for
 * Cache system
 *
 * @package Cache
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Cache_Driver_Memcache implements FW_Cache_ICache {


    /**
     * The memcached handler
     * 
     * @var Memcache
     */
    private static $_handler;

    /**
     * The port where the memcached server could be
     * contacted
     * 
     * @var int
     */
    private $_port;
    
    
    /**
     * The host where the memcached server could be
     * contacted
     * 
     * @var string
     */
    private $_host;


    /**
     * The constructor
     * 
     * @access public
     * 
     * @return void
     */
    public function __construct($parameters) {
        $this->initialize($parameters);
    }

    /* 
     * A method to initialize this driver
     * 
     * @access public
     * @param FW_Container_Parameter $parameters A container of parameters
     * 
     * @return void     
     */
    public function initialize(FW_Container_Parameter $parameters=null) {

        self::$_handler = new Memcache;

        if ($parameters!==null) {
            if ($parameters->hasParameter("host")) {
                $this->_host = $parameters->getParameter("host");
            }
            else {
                throw new FW_Cache_Exception("Couldn't connect to a memcached server without the hostname");
            }

            if ($parameters->hasParameter("port")) {
                $this->_port = $parameters->getParameter("port");
            }
            else {
                throw new FW_Cache_Exception("Couldn't connect to a memcached server without the port");
            }
        }
        else {
            throw new FW_Cache_Exception("Couldn't connect to a memcached server without the hostname");
        }
        self::$_handler->connect($this->_host,$this->_port);
    }

    /**
     * Removes all data of a namespace from the cache
     *
     * @access public     
     * @param string $namespace The namespace to remove
     *
     * @return mixed
     */
    public function clean($namespace="") {
        if (self::$_handler!==null) {
            return self::$_handler->flush();
        }
        return false;
    }


    /**
     * Obtains data from the cache
     *
     * @access public     
     * @param  string $id The id of the data stored in the cache
     * @param  string $namespace The namespace of the data stored in the cache
     * 
     * @return mixed
     */
    public function get($id, $namespace) {
        
        $result = null;
        $oid     = "{$id}|{$namespace}";
        $oid     = md5($oid);
        
        if (self::$_handler!==null) {
            $result = self::$_handler->get($oid);
            if ($result!=null) {
                $result = array("value"=>$result,"id"=>$id,"namespace"=>$namespace,"lifetime"=>60,"cached_at"=>strtotime(date("Y-m-d H:i:s")));                
                return new FW_Cache_Object($result);
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
        
        $id = "{$id}|{$namespace}";
        $id = md5($id);

        if (self::$_handler!==null) {
            if (self::$_handler->set($id,$value,MEMCACHE_COMPRESSED,$lifetime)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Removes data from cache
     *
     * @access public
     * @param string $id The id of the contents
     * @param string $namespace A namespace
     * 
     * @return bool
     */
    public function remove($id, $namespace) {

        $object = $this->get($id,$namespace);        
        if ($object!==false) {            
            if (self::$_handler!==null) {
                
                $id = "{$id}|{$namespace}";
                $id = md5($id);        
        
                return self::$_handler->delete($id);
            }            
        }
        return false;
    }

};
?>