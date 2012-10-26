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
 * An object to represent and keep cached data
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
 * An object to represent and keep cached data
 *
 * @package Cache
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Cache_Object  {

    /**
     * An ID for this cache object
     *
     * @var string
     */
    public $id;
     
    /**
     * The namespace of this cache object
     *
     * @var string
     */
    public $namespace;
     
    /**
     * The value of the data of this cache object
     *
     * @var mixed
     */
    public $value;
     
    /**
     * The lifetime of the data of this cache object
     *
     * @var double
     */
    public $lifetime;
     
    /**
     * The timestamp of the time wich this cache object was generated
     *
     * @var string
     */
     
    public $cached_at;
     
    /*
     * Initializes the cache object
     *
     * @return void
     */

    public function __construct($data=null) {
         
        if ($data!==null) {
            $this->_configure($data);
        }
    }
     
    /**
     * Sets the data of a cache Object
     *
     * @return void
     */
    private function _configure($data) {
         
        if ($data["id"]!==null) {
            $this->id = $data["id"];
        }
        if ($data["namespace"]!==null) {
            $this->namespace = $data["namespace"];
        }
        if ($data["value"]!==null) {
            $this->value = $data["value"];
        }
        if ($data["lifetime"]!==null) {
            $this->lifetime = $data["lifetime"];
        }
        if ($data["cached_at"]!==null) {
            $this->cached_at = $data["cached_at"];
        }
    }
     
    /**
     * Checks if the cache object data has expired
     *
     * @return bool
     */
    public function hasExpired() {
        $timeNow    = strtotime(date("Y-m-d H:i:s"));
         
        if (!is_numeric($this->cached_at)) {
            $timeCached = strtotime($this->cached_at);
        }
        else {
            $timeCached = $this->cached_at;
        }

        $timeElapsed = ($timeNow-$timeCached);
        if ($timeElapsed>$this->lifetime) {
            return true;
        }
        return false;
    }
     
    /**
     * Gets the contents of a cache
     * object
     *
     * @return mixed
     */
    public function getContents() {
        return $this->value;
    }
    
    public function getValue() {
        return $this->getContents();
    }
     
    /**
     * Gets the date when this cache_object
     * was cached
     *
     * @return string
     */
    public function getCachedAt() {
        return $this->cached_at;
    }


};
?>