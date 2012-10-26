<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Config_Cache extends FW_Registry {
        
        private static $_instance;
        
        /**
         * Gets the instance of the FW_Config_Cache
         * component
         * 
         * @static
         * 
         * @return FW_Config_Cache 
         */
        public static function getInstance() {
            if (self::$_instance===null) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        
        /**
         * Checks if the cache has a key
         * 
         * @param string $key The key to check
         * 
         * @return bool
         */
        public function hasKey($key) {
            return $this->exists($key);
        }
        
        /**
         * Sets a value in the cache
         * 
         * @param string $key The key
         * @param mixed $value The value
         * 
         * @return mixed
         */
        public function setValue($key,$value) {
            return $this->set($key,$value);
        }
        
        /**
         * Gets the value stored for this key
         * 
         * @param string $key The key
         * 
         * @return mixed
         */
        public function getValue($key) {
            return $this->get($key);
        }
        
        /**
         * Removes a key from the cache
         * 
         * @param string $key The key
         * 
         * @return void
         */
        public function remove($key) {
            if ($this->hasKey($key)) {                
                $this->delete($key);
            }
        }
        
        public function getKeys() {
            return $this->keys();
        }
        
        public function cleanConfigCache($config) {            
            $keys = $this->getKeys();            
            if (count($keys)>0) {
                foreach ($keys as $key) {
                    if (strpos($key,"{$config}.")===0) {                        
                        $this->remove($key);
                    }                    
                }
            }
        }
        
    };
?>