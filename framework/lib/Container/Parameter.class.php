<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Container_Parameter extends FW_Registry {
        
        
        /**
         * Gets the value of a parameter
         * 
         * @param string $name The name of the parameter
         * 
         * @return mixed
         */
        public function getParameter($name) {
            return $this->get($name);            
        }
        
        /**
         * Checks if this container has a parameter
         * 
         * @param string $name The name of the parameter
         *          
         * @return bool
         */
        public function hasParameter($name) {
            return $this->exists($name);            
        }
        
        /**
         * Sets the value of a parameter
         * 
         * @param string $property The name of the parameter
         * @param mixed  $value The value of the parameter
         * 
         * @return void
         */
        public function setParameter($name,$value) {
            $this->set($name,$value);
        }
        
        
        /**
         * Gets the names of the parameters
         * stored in this container
         * 
         * @return array
         */
        public function getKeys() {
            return $this->keys();            
        }
        
        
        /**
         * Generates a container with the values
         * of an array 
         * 
         * @param array $data The array wich holds the parameters and its values
         * 
         * @return void
         */
        public function fromArray(array $data=array()) {
            if (!empty($data)) {
                foreach ($data as $key=>$value) {
                    $this->set($key,$value);
                }
            }   
            return $this;         
        }
        
        /**
         * Converts all the parameters stored in this
         * container to an array
         * 
         * @return array
         */
        public function toArray() {
            $data = array();
            $keys = $this->keys();
            foreach ($keys as $key) {
                $data[$key] = $this->get($key);
            }
            return $data;            
        }
        
        /**
         * Converts all the parameters stored in this container
         * to a JSON object
         * 
         * @return string
         */
        public function toJson() {
            return json_encode($this->toArray());
        }
        
        
        /**
         * Converts all the parameters stored in this container
         * to a XML object
         * 
         * @param string $root the name of the root element
         * 
         * @return string
         */
        public function toXml($root="parameters") {            
            $xml  = "";
            $data = $this->toArray();
            $xml .= "<{$root}>"; 
            foreach ($data as $key=>$value) {
                $xml .= "<{$key}><![CDATA[{$value}]]></{$key}>";
            }                      
            $xml .= "</{$root}>";
            return $xml;            
        }
        
        /**
         * Gets the value of  a parameter
         * 
         * @param string $property The name of the parameter
         * 
         * @return mixed
         */
        public function __get($property) {
            if ($this->exists($property)) {
                return $this->get($property);
            }
        }
        
        /**
         * Sets the value of a parameter
         * 
         * @param string $property The name of the parameter
         * @param mixed  $value The value of the parameter
         * 
         * @return void
         */
        public function __set($property,$value) {
            $this->set($property,$value);
        }
        
    };
?>