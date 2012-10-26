<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Validator extends FW_Singleton {

        private $_request;
        private $_context;
        
        private $_rules;
        private $_parameters;
        
        private $_action;
        private $_module;
        private $_internal;
        private $_controller;
        
        
        
        private $_usingValidationFile;
        private $_validationFile;
        private $_validationFileData;
        
        

        private function _setUp() {

        }

        private function context() {
            if ( $this->_context == null ) {
                $this->_context = FW_Context::getInstance();
            }
            return $this->_context;
        }

        private function request() {
            if ( $this->_request == null ) {
                $this->_request = FW_Request::getInstance();
            }
            return $this->_request;
        }
        
        private function _setMVCInfo() {
            $this->_module      = $this->context()->router->route["module"];
            $this->_controller = $this->context()->router->route["controller"];
            $this->_action         = $this->context()->router->route["action"];
            $this->_internal      = $this->context()->router->route["internal"];
        }

        private function _loadValidationFile() {
            $this->_setMVCInfo();
            $validations  = null;
            $filename      = "";
            if ($this->_internal) {
                $filename .= "framework/app/modules/";
            }
            else {
                $filename .= "app/modules/";
            }
            $filename .= "{$this->_module}/config/validation.php";            
            if (is_file($filename)) {
                require_once $filename;
                if ($validations!==null) {                    
                    $this->_validationFileData = $validations;                                        
                }
            }            
        }
        
        private function _getValue($name,$type) {
            $value = null;
            if ($type==="parameters") {
                $value = $this->request($name);
            }
            if ($type==="get") {
                $value = $this->request()->get($name);
            }
            if ($type==="post") {
                $value = $this->request()->post($name);
            }
            return $value;
        }
        private function _getValidationDataForAction() {
            $validation = null;
            $data            = $this->_validationFileData;
            if (isset($data["controllers"][$this->_controller][$this->_action])) {
                $validation = $data["controllers"][$this->_controller][$this->_action];
            }            
            return $validation;
        }
        
        private function _setValidationRules() {            
            $rules             = array();
            $parameters = array(); 
            $messages    = array();
            $validation    = $this->_getValidationDataForAction();
            if ($validation!==null) {
                foreach ($validation as $key=>$value) {                    
                    $validators                = array();                
                    $messages                = isset($value["messages"])?$value["messages"]: array();    
                    $parameters[$key] = array("name"=>$key,"source"=>$value["source"],"value"=>$this->_getValue($key,$value["source"]),"messages"=>$messages);                    
                    if (isset($value["validators"])) {                                               
                        if (is_array($value["validators"])) {
                            foreach ($value["validators"] as $validator=>$data) {
                                if (!is_array($data)) {
                                    continue;                                    
                                }                       
                                $messages                        = isset($data["messages"])?$data["messages"]: array();
                                $config                              = isset($data["config"])? $data["config"]: array();
                                $validators[$validator] = array("messages"=>$messages,"config"=>$config);
                            }                        
                        }
                    }
                    $rules[$key] = $validators;               
                }                
            }
            $this->_rules             = $rules;
            $this->_parameters = $parameters;
        }        

        private function _getRulesForParameter($name) {
            if (isset($this->_rules[$name])) {
                return $this->_rules[$name];
            }
        }
        
        public function useValidationRulesFromFile() {
            $this->_usingValidationFile = true;
            $this->_loadValidationFile();
            $this->_setValidationRules();
        }
        
        private function _validateParameter($rules,$parameter) {            
            $results = array("result"=>true,"results"=>array());                  
            if ($rules!==null) {
                $parameterResult = array();
                foreach ($rules as $name=>$rule) {                    
                    $validator = "{$name}Validator";                            
                    $validator = new $validator($parameter["name"]);
                    if (isset($rule["messages"])) {
                        $validator->setSuccessMessage($rule["messages"]["success"]);
                        $validator->setErrorMessage($rule["messages"]["error"]);
                    }
                    if (isset($rule["config"])) {
                        foreach ($rule["config"] as $configKey=>$configValue) {
                            $validator->setParameter($configKey,$configValue);                                                       
                        }
                    }                    
                    $validator->execute($parameter["value"]);
                    $validatorResult = $validator->getResult();
                    if ($validatorResult->isError()) {
                        $results["result"]      = false;
                        $results["results"][] = $validatorResult->getMessage();
                    }
                    else {
                        $message = $validatorResult->getMessage();
                        if (strlen($message)>0) {
                            $results["results"][] = $message;                             
                        }
                    }                                         
                                        
                    if ($validator->getResult()->isError()) {
                        $results["result"] = false;
                    }                    
                }
            }            
            return $results;
        }
        
        public function validate() {
            $results = array("valid"=>true,"parameters"=>array());           
            if (count($this->_parameters)) {
                foreach ($this->_parameters as $name=>$parameter) {                   
                    $rules                     = $this->_getRulesForParameter($name);
                    $results["parameters"][$name]  = $this->_validateParameter($rules,$parameter);
                    if ($results["parameters"][$name]["result"]===false) {
                        $results["valid"] = false;
                    }
                }
            }                                   
            return $results;
        }        

    };
?>