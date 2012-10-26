<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_validator_Result {
        
        private $_name;
        private $_result;
        private $_value;
        private $_message;
        
        public function __construct($name,$value,$result,$message) {
            $this->_name = $name;
            $this->_value = $value;
            $this->_result = $result;
           
            if (strpos($message,"{{value}}")!==false) {                
                $message = str_replace("{{value}}",$value,$message);                
            }
            $this->_message = $message;
        }
        
        public function isError() {
            return $this->_result === false;
        }
        
        public function isSuccess() {
            return $this->_result === true;
        }
        
        public function getMessage() {
            return $this->_message;
        }
        
          
        
    };
?>