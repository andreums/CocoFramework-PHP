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
 * The basic model structure and methods
 * PHP Version 5.2
 *
 * @package  MVC
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es>
 * @license  BSD Style
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * Class BaseModel
 *
 * A basic model with all the functionality for models
 *
 * @package  MVC
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es>
 * @license  BSD Style
 * @link     http://www.andresmartinezsoto.es
 *
 */
abstract class FW_mvc_BaseModel  {

    
    /**
     * An access to the database
     * 
     * @var Database
     */
    protected $_database;
    
    
    /**
     * The prefix of the database
     * 
     * @var string
     */
    protected $_dbprefix;     


    protected $_user;

    /**
     * The constructor of BaseModel
     *
     * @desc   The constructor of BaseModel
     * @access public
     * @return void
     */
    public function __construct() {}
    
    /**
     * Gets the database handler
     * 
     * @return FW_Database
     */
    public function database() {        
        if ($this->_database===null) {
            $this->_database = FW_Database::getInstance();
            $this->_dbprefix = $this->_database->getPrefix();
        }        
        return $this->_database;
    }
    
    /**
     * Obtains the logged user
     *
     * @return FW_Authentication_User
     */
    public function user() {
        if (!isset($this -> _user)) {
            if (FW_Authentication::getInstance() -> isLoggedIn()) {
                $this -> _user = FW_Authentication::getUser();
            } else {
                $this -> _user = false;
            }
        }
        return $this -> _user;
    }

};
?>