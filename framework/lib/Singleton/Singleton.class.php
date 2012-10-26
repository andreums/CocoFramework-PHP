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
 *
 * This is an implementation of the Singleton pattern.
 * @author Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @version 1.5
 * @package Framework
 *
 */
/**
 *
 * Class Singleton
 *
 * @package  Framework
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es>
 * @license  BSD Style
 * @link     http://www.andresmartinezsoto.es
 * @abstract
 *
 */
abstract class FW_Singleton {
    /**
     * Array of cached singleton objects
     *
     * @var array
     */
    private static $instances = array();

    /**
     * Static method for instantiating a singleton object.
     *
     * @return object
     */
    final public static function getInstance() {
        $className = get_called_class();
        if ( !isset(self::$instances[$className]) ) {
            self::$instances[$className] = new $className;
        }
        return self::$instances[$className];
    }

    /**
     * Singleton objects should not be cloned
     *
     * @return void
     */
    final private function __clone() {  }
};
?>