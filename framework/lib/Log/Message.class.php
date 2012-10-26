<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Log_Message {
        
        private $_message;
        private $_timestamp;
        private $_backtrace;
        
        public function __construct($message,$backtrace=false) {                       
            $this->_message   = $message;                       
            $this->_backtrace = $backtrace;
            $this->_timestamp = time();            
        }
        
        public function getMessage() {
            return ($this->_message);
        }
        
        public function getTimestamp() {
            return ($this->_timestamp);
        }
        
        public function getLevel() {
            return ($this->_level);
        }

        public function getBacktrace() {
           if (!$this->_backtrace) { return ""; }
           return ($this->_getDebugBacktracteAsString());            
        }
        
        public function getFile() {
            return ($this->_backtrace[0]["file"]);
        }
        
        public function get() {
            //return (array("message"=>$this->_message,"timestamp"=>$this->_timestamp,"level"=>$this->_level,"backtrace"=>$this->_backtrace));    
        }
        
        
        private function _getDebugBacktracteAsString() {
            if ($this->_backtrace===false) {
                return '';
            }
            else {
                ob_start();
                debug_print_backtrace();
                $trace = ob_get_contents();
                ob_end_clean();
                    
                // Remove first item from backtrace as it's this function which
                // is redundant.
                $trace = preg_replace ('/^\#(.*)?/','', $trace, 3);
        
                // Renumber backtrace items.                
                
                $trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);
                $trace = trim($trace);
                return $trace;
            }
        }
        
        
        
    };
?>