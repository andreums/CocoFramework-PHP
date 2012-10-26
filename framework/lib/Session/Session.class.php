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
 * Session handler
 * PHP Version 5.2
 *
 * @package  Session
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es>
 * @license  BSD Style
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * Class Session
 *
 * A basic session handler
 *  
 * @package  Session
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es> 
 * @link     http://www.andresmartinezsoto.es
 *
 */

class FW_Session extends FW_Singleton {
    /**
     * Sets or specifies the value for an index of the session
     *
     * @param string $index The index to set
     * @param mixed  $value The value for the index
     * @param string $namespace The namespace of the index
     */
    public static function set($index, $value, $namespace="default") {
        $_SESSION["FRAMEWORK_SESSION"][$namespace][$index] = $value;
    }
    /**
     * Gets the value for an index of the session
     *
     * @param string $index The index to get
     * @param string $namespace The namespace of the index
     * @return mixed
     */
    public static function get($index, $namespace="default"){
        if (isset($_SESSION["FRAMEWORK_SESSION"][$namespace][$index])) {
            return $_SESSION["FRAMEWORK_SESSION"][$namespace][$index];
        }
        else {
            return null;
        }
    }
    /**
     * Unsets an index on the session
     *
     * @param string $index The index to unset
     * @param string $namespace The namespace of the index
     */
    public static function unsetData($index, $namespace="default") {        
        unset($_SESSION["FRAMEWORK_SESSION"][$namespace][$index]);
    }
    /**
     * Check if an index is set in the session
     *
     * @param string $index The index to get
     * @param string $namespace The namespace of the index
     * @return bool
     */
    public static function issetData($index, $namespace="default") {
        return (isset($_SESSION["FRAMEWORK_SESSION"][$namespace][$index]));
    }


    /**
     * Gets the id of the session
     *
     * @return mixed
     */
    public static function getId() {
        return (session_id());
    }


    /**
     * Destroys the session
     * 
     * @return void
     */
    public static function destroy() {        
        @session_destroy();
    }

    /**
     * Starts the session
     * 
     * @return void
     */
    public static function start() {
        @session_start();
    }

    /**
     * Serializes the session
     * 
     * @return string
     */
    private static function _serialize() {
        return serialize($_SESSION);
    }

    /**
     * Unserializes session data
     * 
     * @param string $data The data to unserialize
     * 
     * @return void
     */
    private static function _unserialize($data) {
        $_SESSION = array();
        $_SESSION = unserialize($data);
    }

    /**
     * Writes the session data to a file
     *
     * @param string $filename The filename
     *
     * @static
     * @return bool
     */
    public static function dumpToFile($filename) {
        $serializedData = $this->_serialize();
        if (is_file($filename)) {
            if (file_put_contents($filename,$serializedData)!==false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Loads session data from a file
     *
     * @param string $filename The filename
     *
     * @static
     * @return bool
     */
    public static function loadFromFile($filename) {
        if (is_file($filename)) {
            $data = file_get_contents($filename);
            if ($data!==null) {
                $this->_unserialize($data);
                return true;
            }
            return false;
        }

        return false;
    }
    
    /**
     * Regenerates the session identifer
     * 
     * @param bool $deleteOldSession Delete the old session?
     * 
     * @return void
     */
    public static function regenerateId($deleteOldSession=false) {
        session_regenerate_id($deleteOldSession);        
    }
}
?>