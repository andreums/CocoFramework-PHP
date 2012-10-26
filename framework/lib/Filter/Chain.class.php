<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Filter_Chain implements IComponent {

    private $_chain;
    private $_index;

    public function __construct(array $filters=null) {
        if ($filters!==null) {
            $this->_setFilters($filters);
        }
    }

    public function configure(FW_Container_Parameter $parameters=null) {
        $this->_index = -1;
        if ($parameters!==null) {
            if ($parameters->hasParameter("filters")) {
                $this->_setFilters($parameters->getParameter("filters"));                
            }
        }

    }

    private function _setFilters(array $filters=array()) {        
        if (count($filters)) {
            foreach ($filters as $key=>$properties) {
                
                $props = null;
                
                if (!is_array($properties)) {
                    $filter = $properties;
                }                
                else {
                    $filter = $key;            
                    $props  = new FW_Container_Parameter();
                    $props->fromArray($properties);
                }
                $class  = "FW_Filter_{$filter}";
                $filter = new $class();
                if ($props!==null) {
                    $filter->configure($props);
                }                               
                $this->_register($filter);
            }
        }
    }
     

    public function initialize(array $arguments=array()) {        
    }


    private function _register(FW_Filter $filter)   {        
        $this->_chain []= $filter;
    }

    public function execute() {        
        ++$this->_index;        
        if ($this->_index < count($this->_chain)) {
            $this->_chain[$this->_index]->execute($this);
        }
        return true;
    }

};
?>