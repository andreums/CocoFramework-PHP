<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Exception extends Exception {
        /**
         * @var null|Exception
         */
        private $_previous = null;

        /**
         * Construct the exception
         *
         * @param  string $msg
         * @param  int $code
         * @param  Exception $previous
         * @return void
         */
        public function __construct($msg = '', $code = 0, Exception $previous = null) {
            if ( version_compare(PHP_VERSION, '5.3.0', '<') ) {
                parent::__construct($msg, (int)$code);
                $this->_previous = $previous;
            }
            else {
                parent::__construct($msg, (int)$code, $previous);
            }
        }

        /**
         * Overloading
         *
         * For PHP < 5.3.0, provides access to the getPrevious() method.
         *
         * @param  string $method
         * @param  array $args
         * @return mixed
         */
        public function __call($method, array $args) {
            if ( 'getprevious' == strtolower($method) ) {
                return $this->_getPrevious();
            }
            return null;
        }

        /**
         * String representation of the exception
         *
         * @return string
         */
        public function __toString() {
            if ( version_compare(PHP_VERSION, '5.3.0', '<') ) {
                if ( null !== ($e = $this->getPrevious()) ) {
                    return $e->__toString() . "\n\nNext " . parent::__toString();
                }
            }
            return parent::__toString();
        }

        /**
         * Returns previous Exception
         *
         * @return Exception|null
         */
        protected function _getPrevious() {
            return $this->_previous;
        }
        
        public static function text(Exception $e) {
            return sprintf('%s [ %s ]: %s ~ %s [ %d ]',get_class($e), $e->getCode(), strip_tags($e->getMessage()), $e->getFile(), $e->getLine());
        }

    };
?>