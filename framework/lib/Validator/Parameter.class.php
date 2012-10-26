<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_validator_Parameter {
        
        private $_name;
        private $_method;
        private $_required;
        private $_value;
        private $_validators;      
        private $_request; 

        public function __construct($name,$method,$required) {
            $this->_name     = $name;
            $this->_method   = $method;
            $this->_required = $required;
            $this->_request  = FW_Request::getInstance();
            $this->_setValue();
        }
        
        public function addValidator(FW_Validator_Base $validator) {
            if (!isset($this->_validators)) {
                $this->_validators = array();
            }
            $this->_validators[$validator->getName()] = $validator;
        }
        
        private function _setValue() {
                        
            $value   = null;           
            
            if ($this->_method==="post") {
                $value = $this->_request->getPostParameter($this->_name);
            }
            if ($this->_method==="get") {
                $value = $this->_request->getGetParameter($this->_name);
            }
            if ($this->_method==="param") {                
                $value = $this->_request->getParameter($this->_name);
            }

            if ($value===null) {
                if ($this->_required) {
                    throw new Exception("Can't get value for parameter {$this->_name} via method {$this->_method}");
                }
            }
            else {
                $this->_value = $value;
            }            
        }
        
        
        public function execute() {
            if (!empty($this->_validators)) {
                foreach ($this->_validators as $validator) {
                    if (!$validator->execute($this->_value)) {
                        return false;
                    }
                    
                }                 
            }
        }
    };
?>