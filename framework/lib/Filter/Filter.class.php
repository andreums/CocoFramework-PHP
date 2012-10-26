<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
abstract class FW_Filter extends FW_Registry implements IComponent {

    public static $filterCalled = array();
    protected $_config;

    abstract public function execute (FW_Filter_Chain $filterChain);
    
    public function configure(FW_Container_Parameter $parameters=null) {
        if ($parameters!==null) {
            foreach ($parameters->toArray() as $key=>$value) {
                $this->$key = $value;
            }
        }        
    }

    public function initialize(array $arguments=array()) {
        $this->_index = -1;
    }


    protected function isFirstCall()   {
        $class = get_class($this);
        if (isset(self::$filterCalled[$class]))  {
            return false;
        }
        else {
            self::$filterCalled[$class] = true;
            return true;
        }
    }
    
    public function setParameter($name,$value) {
        $this->_objects[$name] = $value;
    }
    
    public function hasParameter($name) {
        return $this->exists($name);
    }
    
    public function getParameter($name) {
        return $this->get($name);
    }
    
    protected function getContext() {
        return FW_Context::getInstance();
    }
    
    protected function getSession() {
        return FW_Session::getInstance();
    }
    
    protected function getConfig() {
        return $this->config();
    }
    
    protected function config() {
        return FW_Config::getInstance();
    }
    
    

};
?>