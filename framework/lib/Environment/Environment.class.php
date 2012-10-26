<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Environment {

        private $_config;
        private $_database;
        private $_environment;        
        private $_isConfigured = null;
        private static $_instance;

        public function __construct() {
            if ($this->_isConfigured===null) {                            
                $this->_configure();
                $this->_isConfigured = true;
            }          
        }
        
        public static function getInstance() {
            if (self::$_instance===null) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        private function _configure() {
            $this->_config = FW_Config::getInstance();                        
            $environment   = $this->_config->environment->getCurrentEnvironment();            
            if ($environment!==null) {
                $this->_environment = $environment;
                $this->_database    = $this->_config->environment->getDatabaseInUse();
                $this->_cache          = $this->_config->environment->getCacheInUse();                
            }
        }
        
        public function getEnvironmentName() {
            $env =  $this->_environment["environment"];
            return $env;
        }
        
        public function getDatabase() {            
            return $this->_database;
        }
        
        public function getCache() {
            return $this->_cache;
        }
    };
?>