<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    abstract class FW_Database_Driver_Base implements IComponent {
        
        protected $_configuration;

        public function configure(FW_Container_Parameter $parameters=null) {            
           if ($parameters!==null) {
               $this->_configuration = $parameters;
            }        
        }

        public function initialize(array$arguments= array()) {            
        }

        /**
         * Gets information about the current driver
         *
         * @access public
         * @return string
         */
        abstract public function info();

        /**
         * Connects to the database
         *
         * @access public
         * @return bool
         */
        abstract public function connect();

        /**
         * Disconnects from the database
         *
         * @access public
         * @return bool
         */
        abstract public function disconnect();

        /**
         * Queries the database
         *
         * @param  string The SQL query
         * @access public
         * @return mixed The result
         */
        abstract public function query($query);
        
        /**
         * Gets the affected rows for a query
         *
         * @access public
         * @return int Number of affected rows
         */
        abstract public function affectedRows();

        /**
         * Gets the number of rows from a result
         *
         * @access public
         * @return int Number of rows of the result
         */
        abstract public function numRows();            

        /**
         * Fetches the result of a query as array
         *
         * @access public
         * @return Array
         */
        abstract public function fetchArray();

        /**
         * Fetches the result of a query as Row
         *
         * @access public
         * @return Array
         */
        abstract public function fetchRow();

        /**
         * Fetches the result of a query as an associative array
         *
         * @access public
         * @return Array
         */
        abstract public function fetchAssoc();

        /**
         * Fetches the result of a query as an object
         *
         * @access public
         * @return object
         */
        abstract public function fetchObject();
        
        

    }
?>