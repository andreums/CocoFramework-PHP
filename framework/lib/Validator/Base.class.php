<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
abstract class FW_Validator_Base extends FW_Registry {
    
    protected $_name;
    protected $_success;
    protected $_error;
    protected $_required;
    protected $_result;


    public function __construct($name,$parameters=array()) {
        $this->_name = $name;
        $this->initialize($parameters);        
    }
    
    protected function getParameterName() {
        return $this->_name;
    }
    
    public function setSuccessMessage($message) {
        $this->_success = $message;
    }
    
    public function setErrorMessage($message) {
        $this->_error   = $message;
    }
    
    public function setRequiredMessage($message) {
        $this->_required = $message;
    }
    
    public function getSuccessMessage() {
        return $this->_success;
    }
    
    public function getErrorMessage() {
        return $this->_error;
    }
    
    public function getRequiredMessage() {
        return $this->_required;
    }
    
    public function getName() {
         $reflect = new ReflectionClass($this);
         if ($reflect!==null) {
             return $reflect->name;
         }
    }

    public function initialize(array $parameters) {
        if (count($parameters)) {
            foreach ($parameters as $key=>$parameter) {
                $this->set($key,$parameter);
            }
        }        
    }

    public function getParameter($name) {
        if ($this->exists($name)) {
            return $this->get($name);
        }
    }    
    
    public function hasParameter($name) {
        return $this->exists($name);
    }
    
    public function setParameter($name,$value) {
        $this->set($name,$value);
    }
      
    abstract function execute(&$value);
    
    protected function setResult(FW_Validator_Result $result) {        
        $this->_result = $result;        
    }
    
    protected function setSuccessResult($value) {
        $this->setResult(new FW_Validator_Result($this->getParameterName(),$value,true,$this->getSuccessMessage()));
    }
    
    protected function setErrorResult($value) {
        $this->setResult(new FW_Validator_Result($this->getParameterName(),$value,false,$this->getErrorMessage()));
    }
    public function getResult() {
        return $this->_result;        
    }
};
?>