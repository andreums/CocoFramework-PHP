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
     * @author andreu
     *
     */
    abstract class FW_Config_Handler {
                
        /**
         * The name of this handler
         * 
         * @var string
         */
        protected $_name;
                
        /**
         * An access to the Config component
         * 
         * @var FW_Config
         */
        protected $_config;
        
        /**
         * A flag to indicate if the handler
         * has been configured
         * 
         * @var bool
         */
        protected $_isConfigured;

        
        /**
         * The constructor for any config handler
         * 
         * @return void
         */
        public function __construct() {            
            if ($this->_isConfigured===null) {
                $this->_configure();
                $this->_initialize();
                $this->_isConfigured = true;
            }            
        }
        
        /**
         * Configures this config handler
         * 
         * @return void
         */
        private function _configure() {
            //"FW_Config_Handler_",$class);
            $class               = get_called_class();
            $class               = substr($class,18);
            $name              = strtolower($class);
            $this->_name = $name;                                   
        }
        
        /**
         * Initializes this config handler
         * 
         * @return void
         */
        private function _initialize() {
            $this->_config       = FW_Config::getInstance();                        
            if (!$this->_config->hasConfig($this->_name)) {
                if (!$this->_config->loadConfig($this->_name)) { 
                    throw new FW_Config_Exception("Config doesn't contains a configuration named {$this->_name}.");
                }                
            }            
        }


        /**
         * Gets a parameter from the configuration
         * 
         * @param string $parameter The key for the parameter to get
         * 
         * @return mixed
         */
        protected function getParameter($parameter) {
            $parameter = "{$this->_name}.{$parameter}";
            return $this->_config->get($parameter);            
        }
        
        /**
         * Sets the value of a parameter of the configuration
         * 
         * @param string $parameter The key for the parameter to set
         * @param mixed  $value The value to set
         * 
         * @return mixed
         */        
        protected function setParameter($parameter,$value) {
            $parameter = "{$this->_name}.{$parameter}";            
            return $this->_config->setValue($parameter,$value);
        }
        
    	/**
         * Creates a new value on a config and sets its value
         * 
         * @param string $parameter The key for the parameter to set
         * @param mixed  $value The value to set
         * 
         * @return mixed
         */        
        protected function addParameter($parameter,$value) {
            $parameter = "{$this->_name}.{$parameter}";            
            return $this->_config->addValue($parameter,$value);
        }       
        
        /**
         * Checks if a parameter exists 
         * 
         * @param string $parameter The key for the parameter
         * 
         * @return bool
         */
        protected function existsParameter($parameter) {
            $parameter = "{$this->_name}.{$parameter}";
            return $this->_config->exists($parameter);
        }

        
         /**
         * Saves the current data of the configuration with the changes made.
         * Be aware that this operation needs write permissions on configuration
         * paths.
         * 
         *  @return bool
         */
        protected function save() {
            $filename = $this->_config->getFileForConfig($this->_name);
            return $this->_config->save($this->_name,$filename);        
        }
        
        
        /**
         * Reloads the configuration file and erases the content of the corresponding data in
         * both, configuration and configuration cache.
         * 
         *  @return void
         */
        protected function reload() {
            $this->_config->reload($this->_name);
        }
        
        
        /**
         * Gets the sections of a configuration         
         * 
         * @return array
         */        
        protected function getSections() {
             return $this->_config->getSections($this->_name);   
        }
        
        /**
         * Gets the parameters of the global section of a configuration         
         * 
         * @return array
         */
        protected function getGlobal() {
            return $this->_config->getGlobal($this->_name);
        }
        
        
        
        

    };
?>