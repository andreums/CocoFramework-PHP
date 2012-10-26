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
 * A Cache memory interface
 * for driver implemention
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
 * An interface for all the Cache
 * drivers
 *
 * @package Cache
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
interface FW_Cache_ICache {

    /**
     * Obtains data from the cache
     *
     * @access public
     *
     * @param  string $id The id of the data stored in the cache
     * @param  string $namespace The namespace of the data stored in the cache
     * @return mixed
     */
    public function get($id, $namespace);

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
    public function save($id, $namespace, $value, $lifetime);

    /**
     * Removes all data of a namespace from the cache
     *
     * @access public
     *
     * @param string $namespace The namespace to remove
     *
     * @return mixed
     */
    public function clean($namespace=false);

    /**
     * Removes data from cache
     *
     * @access public
     * @param string $id The id of the contents
     * @param string $namespace A namespace
     * @return bool
     */
    public function remove($id, $namespace);
    
    
    /**
     * Configures and initializes a cache driver
     *
     * @access public
     * @param FW_Container_Parameter $parameters
     * 
     * @return void
     */
    public function initialize(FW_Container_Parameter $parameters=null);
};

?>