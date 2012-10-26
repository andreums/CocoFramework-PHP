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
 * File driver for Cache
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
 * Class that implements a File driver
 * for Cache system
 *
 * @package Cache
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Cache_Driver_File implements FW_Cache_ICache {

    /**
     * Directory where the cache should store the files
     * 
     * @var string 
     */
    protected $_cacheDir;

    /**
     * The constructor 
     * 
     * @param mixed $parameters Parameters to initialize this driver 
     * 
     * @return void
     */
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
        if ($parameters!==null) {
            if ($parameters->hasParameter("cacheDir")) {
                $this->_cacheDir = $parameters->getParameter("cacheDir");
            }
            else {
                $this->_cacheDir = "framework/cache/data";    
            }
        }
        else {
            $this->_cacheDir = "framework/cache/data";
        }
    }

    /**
     * Gets the filename for a cache object
     * 
     * @param string $id The id of the cached data
     * @param string $namespace The namespace of the cached data
     * 
     * @return string The name of the file
     */
    private function _getFilename($id,$namespace) {
        $fileName = "{$this->_cacheDir}/".md5($namespace)."/{$id}_".md5($namespace).".cache";
        return $fileName;
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
        if (empty($namespace)) {
            try {
                if ( rmdir($this->_cacheDir) ) {
                    mkdir($this->_cacheDir);
                }
            }
            catch (Exception $ex) {
                print $ex->getMessage();
            }
        }
        else {
            $namespace = md5($namespace);
            try {
                if ( rmdir($this->_cacheDir.'/'.$namespace) ) {
                    mkdir($this->_cacheDir.'/'.$namespace);
                }
            }
            catch (Exception $ex) {
                print $ex->getMessage();
            }

        }
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

        $data = "";
        $file = $this->_getFilename($id,$namespace);

        if (is_file($file)) {
            try {
                $data     = file_get_contents($file);
                $filetime = filemtime($file);
                $lifetime = explode("\n\n",$data);                
                $lifetime = trim($lifetime[0]);  
                $contents = explode("\n\n",$data);                             
                $contents = $contents[1];
                
                $cacheObj            = new FW_Cache_Object();
                $cacheObj->id        = $id;
                $cacheObj->namespace = $namespace;
                $cacheObj->value     = $contents;
                $cacheObj->lifetime  = $lifetime;
                $cacheObj->cached_at = $filetime;                
                return $cacheObj;
            }
            catch (Exception $ex) {
                print $ex->getMessage();
            }
        }
        return null;
    }


    /**
     * Removes data from cache
     *
     * @access public
     * @param string $id The id of the contents
     * @param string $namespace A namespace
     * @return bool
     */
    public function remove($id,$namespace) {
        $file = $this->_getFilename($id,$namespace);
        if (!is_file($file)) {
            return true;
        }
        try {
            if (unlink($file)) {
                return true;
            }
        }
        catch (Exception $ex) {
            print $ex->getMessage();
            return false;
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
        $dir = $this->_cacheDir.'/'.md5($namespace);
        if ($lifetime==null) {
            $lifetime = 600;
        }
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $file = $this->_getFilename($id,$namespace);        

        $data = "{$lifetime}\n\n{$value}";
        if (file_put_contents($file,$data)) {
            return true;
        }
        return false;
    }

}
?>