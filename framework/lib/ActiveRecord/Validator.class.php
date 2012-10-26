<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    /**
     * Active Record validation
     * Validates datatype for
     * every property of a model
     * that uses ActiveRecord
     *
     * @author andreu
     * @package ActiveRecord
     */
    class FW_ActiveRecord_Validator {

        protected static $_schemaStorage;
        protected $_schema;
        protected $_schemaDataBaseColumns;
        protected $_object;
        protected $_validationMethods;
        protected $_name;
        protected $_filter;

        /**
         * The constructor of ActiveRecordValidator
         *
         * @param string $modelName The name of the
         * model to validate
         * @param object $object The model to validate
         *
         * @return null
         */
        public function __construct($modelName,$object) {            
            self::$_schemaStorage     = FW_ActiveRecord_Metadata_Manager::getInstance();
            $this->_validationMethods = array();
            if (self::$_schemaStorage->hasSchema($modelName)) {
                $this->_name   = $modelName;
                $this->_schema = self::$_schemaStorage->getSchema($modelName);
                $this->_object = $object;
                $this->_schemaDataBaseColumns = $this->_schema->getDataBaseColumns();
                $this->_discoverValidationCallbacks();
            }
            else {                                
                throw new FW_ActiveRecord_Exception("Model {$modelName} not found");
            }
        }

        /**
         * Checks the model for custom validation callbacks
         *
         * @return this
         */
        private function _discoverValidationCallbacks() {
            $reflect = new ReflectionClass($this->_object);
            $methods = $reflect->getMethods();
            $count   = count($methods);
            for($i=0;$i<$count;$i++) {
                if ($methods[$i]->class == $this->_name) {
                    if (strpos($methods[$i]->name,"validate_")!==false) {
                        $this->_validationMethods []= $methods[$i]->name;
                    }
                }
            }
            return $this;
        }

        /**
         * Initiates the validation of a model
         * object
         *
         * @return bool
         */
        public function validate()  {            
            if (!empty($this->_validationMethods)) {
                foreach ($this->_validationMethods as $method) {
                   if (!call_user_func(array($this->_object,$method)) ) {
                       throw new FW_ActiveRecord_Exception("Model {$this->_name} validation method {$method} does not pass the validation");
                       return false;
                   }
                }
            }

            if (!empty($this->_schemaDataBaseColumns)) {
                foreach ($this->_schemaDataBaseColumns as $column) {
                    $name   = $column["name"];
                    $type   = $column["type"];
                    $flags  = $column["flags"];
                    $length = $column["length"];                    
                    $value = $this->_object->$name;
                    
                    if (!$this->_validateColumn($name,$value,$type,$length,$flags)) {                        
                        $gtype = gettype($value);
                        print "Model {$this->_name}: Error while validating {$name} {$value} ({$gtype}) is not a valid value for {$type} type ";
                        throw new FW_ActiveRecord_Exception("Model {$this->_name}: Error while validating {$name} {$value} ({$gtype}) is not a valid value for {$type} type ");
                        return false;
                    }

                }
            }
            return true;
        }

        /**
         * Validates a column within its type
         * on the database
         *
         * @param string $name Name of the column
         * @param mixed  $value Value of the model column
         * @param string $type DataType for the column
         * @param int	 $length Length of the column value
         * @param array  $flags Flags for the column
         *
         * @return bool
         */
        private function _validateColumn($name,$value,$type,$length,$flags) {
            $valid = new FW_Validator_Valid();
            $null  = false;
            // Not null columns
            if (in_array("not_null",$flags)) {
                $null = true;
            }

            // Ignore all the auto_increment columns
            if (in_array("auto_increment",$flags)) {
                return true;
            }

            if ($null) {
                if ( ( ($value==null) || ($value=="") ) && (intval($value)!=0)  ) {
                        return false;
                }
            }
            else {
                if ($value==null) {
                    return true;
                }
            }
            
            if (is_object($value)) {            
                return true;
            }

            $valueLength = strlen($value);
            if ($valueLength>$length) {
                return false;
            }

            switch($type) {
                
                case "text":
                case "string":
                case "varchar":
                case "VAR_STRING":                    
                    $result1 = $valid->isString($value);
                    $result2 = $valid->isInteger($value);
                    
                    if ($result1 || $result2) {
                        return true;
                    }
                    return true;
                break;

                case "int":
                    $res = $valid->isNumeric($value);
                    if (!$res && ($value==null || $value=="NULL" || !$value || $value==0 || $value=="0") ) {
                        return true;
                    }
                    if ($res) {
                        return true;
                    }
                break;


                case "LONG":
                case "float":
                    return $valid->isFloat($value);
                    break;

                case "bool":
                    return $valid->isBoolean($value);
                    break;

                case "blob":
                    return true;
                break;

                case "real":
                    return true;
                break;

                case "date":
                case "datetime":
                    if ($value==="0000-00-00 00:00:00") {
                        return true;
                    }
                    if (strtotime($value)) {
                        return true;
                    }
                    else {
                        return false;
                    }
                break;

                case "time":
                case "timestamp":
                        return true;
                break;

                default: return true;
            };

            return true;

        }


    };
?>