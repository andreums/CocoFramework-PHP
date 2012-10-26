<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_ActiveRecord_Metadata_Schema {

    private $_name;
    private $_reflect;
    private $_reflectProperties;
    private $_properties;

    private $_prefix;
    private $_database;
    private $_table;

    private $_columns;
    private $_primaryKey;

    private $_actings;
    
    private $_textProperties;
    private $_relationProperties;

    private static $_reservedNames = array(
    	"has_one",
        "belongs_to",
        "has_many",
        "has_and_belongs_to_many",
        "has_many_by_sql",
        "has_many_through",
        "has_one_through",
        "acts_as_tree"
        );


        /**
         * The constructor of this class
         *
         * @param strong $name The name of the model
         *
         * @return void
         */
        public function __construct($name) {
            $this->_configure($name);
            $this->_initialize();
        }


        /**
         * Configures this schema
         *
         * @param array $arguments An array of parameters
         *
         * @return void
         */
        private function _configure($name) {

            if (empty($name)) {
                throw new FW_ActiveRecord_Exception("Model name can't be blank. Aborting");
            }

            $this->_reflect        = null;
            $this->_textProperties = null;
            $this->_name           = $name;
            $this->_columns        = array();
            $this->_properties     = array();
            $this->_relations      = array();
            $this->_primaryKey     = array();
            $this->_actings        = array();
            $this->_relationProperties = array();
        }

        /**
         * Initializes this schema
         *
         * @return void
         */
        private function _initialize() {

            if (!$this->_reflectModel()) {
                throw new FW_ActiveRecord_Exception("Model {$this->_name} doesn't exists");
            }

            $this->_initDataBase();
            $this->_discoverDataBaseColumns();
            $this->_discoverProperties();
            $this->_discoverActings();
            $this->_discoverRelations();            
            $this->_validate();

        }

        /**
         * Instantiates a ReflectionClass for this
         * model
         *
         * @param string $name The name of the model
         *
         * @return bool
         */
        private function _reflectModel() {
            $this->_reflect = new ReflectionClass($this->_name);
            if ($this->_reflect!==null) {
                $this->_reflectProperties = $this->_reflect->getProperties();
                return true;
            }
            return false;
        }

        /**
         * Initializes the database connection and
         * sets up the prefix of the connection
         *
         * @return void
         */
        private function _initDataBase() {
            $this->_database = FW_Database::getInstance();
            $this->_prefix   = $this->_database->getPrefix();
            $this->_table    = $this->_prefix.$this->_name;
        }


        /**
         * Discovers the properties of this model
         *
         * @return bool
         */
        private function _discoverProperties() {
            if ($this->_reflect!==null) {
                $classProperties = $this->_reflectProperties;
                $modelProperties = array();

                if($classProperties === null || empty($classProperties)) {
                    throw new FW_ActiveRecord_Exception("ActiveRecord | Model has no properties!");
                }

                $count = count($classProperties);
                for($i = 0; $i < $count; $i++) {
                    $name = $classProperties[$i] -> name;                    
                    if ( ($name[0] != "_") && (!in_array($name,self::$_reservedNames))) {
                        $keys = array_keys($this->_columns);
                        if (in_array($name,$keys)) {                            
                            $modelProperties[] = $name;
                        }                        
                    }
                }
                $this->_properties = $modelProperties;
            }
            return false;
        }


        /**
         * Discovers the columns of the corresponding table for this model
         * in the database
         *
         * @return $this
         */
        private function _discoverDataBaseColumns() {
            $columns    = array();
            $primaryKey = array();

            $tableColumns = $this->_database->getTableFields($this->_table);

            if(count($tableColumns)) {
                foreach($tableColumns as $column) {
                    $column         = $this->_analyseColumn($column);
                    $name           = $column["name"];
                    $columns[$name] = $column;
                    if ($column["primaryKey"]===true) {
                        $primaryKey[$name] = $column;
                    }
                    
                    if ($column["type"]==="string" || $column["type"]==="blob") {
                        $this->_textProperties []= $name;
                    }
                }
            }
            $this->_columns    = $columns;
            $this->_primaryKey = $primaryKey;            
        }

        private function _analyseColumn($column) {
            if (in_array("primary_key",$column["flags"])) {
                $column["primaryKey"] = true;
            }
            else {
                $column["primaryKey"] = false;
            }

            if (in_array("auto_increment",$column["flags"])) {
                $column["autoIncrement"] = true;
            }
            else {
                $column["autoIncrement"] = false;
            }

            if (in_array("unique_key",$column["flags"])) {
                $column["uniqueKey"] = true;
            }
            else {
                $column["uniqueKey"] = false;
            }

            if (in_array("multiple_key",$column["flags"])) {
                $column["foreignKey"] = true;
            }
            else {
                $column["foreignKey"] = false;
            }

            if (!in_array("not_null",$column["flags"]) && in_array("",$column["flags"])) {
                $column["null"] = true;
            }
            else {
                $column["null"] = false;
            }
            return $column;
        }

        /**
         * Discovers the actings of this model
         *
         * @return array
         */
        private function _discoverActings() {
            $actings = array();
            if ($this->_reflect!==null) {
                $properties = $this->_reflectProperties;
                $count      = count($properties);
                for($i = 0; $i < $count; $i++) {
                    $name = $properties[$i]->name;
                    if (in_array($name,self::$_reservedNames)) {
                        if($name==="acts_as_tree") {
                            $values  = $this->_reflect->getProperty($name)->getValue();
                            $actings = array_merge($actings,$this->_analyseActsAsTree($values));
                        }
                    }
                }
            }
            $this->_actings = $actings;
            return $actings;
        }

        /**
         * Analyses the acts_as_tree property
         *
         * @param array $values The data to analyse
         * @return array
         */
        private function _analyseActsAsTree($values) {
            $actings = array("idColumn" => $values["idColumn"],
            "parentColumn" => $values["parentColumn"],
            "siblingsOrders" => (isset($values["siblingOrder"]) ? ($values["siblingOrder"]) : false));

            return $actings;
        }

        private function _discoverRelations() {
            $relations = array();

            if ($this->_reflect!==null) {

                $properties = $this->_reflectProperties;
                $count      = count($properties);

                for ($i=0;$i<$count;$i++) {
                    $name = $properties[$i]->name;

                    if (in_array($name, self::$_reservedNames)) {

                        if($name === "has_one") {
                            $values    = $this->_reflect->getProperty($name)->getValue();
                            $relations = array_merge($relations,$this->_analyseHasOneRelations($values));
                        }
                        if($name === "has_many") {
                            $values    = $this->_reflect->getProperty($name)->getValue();
                            $relations = array_merge($relations,$this->_analyseHasManyRelations($values));
                        }
                        if($name === "has_and_belongs_to_many") {
                            $values    = $this->_reflect->getProperty($name)->getValue();
                            $relations = array_merge($relations,$this->_analyseHABTMRelations($values));
                        }
                        if($name === "belongs_to") {
                            $values    = $this->_reflect->getProperty($name)->getValue();
                            $relations = array_merge($relations,$this->_analyseBelongsToRelations($values));
                        }
                        if($name === "has_many_through") {
                            $values    = $this->_reflect->getProperty($name)->getValue();
                            $relations = array_merge($relations,$this->_analyseHasManyThroughRelations($values));
                        }
                        if($name === "has_one_through") {
                            $values    = $this->_reflect->getProperty($name)->getValue();
                            $relations = array_merge($relations,$this->_analyseHasOneThroughRelations($values));
                        }
                        if($name === "has_many_by_sql") {
                            $values    = $this->_reflect->getProperty($name)->getValue();
                            $relations = array_merge($relations,$this->_analyseHasManyBySQLRelations($values));
                        }

                    }
                }                
                foreach ($relations as $relation) {
                    $this->_relationProperties []= $relation["property"];                   
                }                
                $this->_relations = $relations;
                return true;
            }
            return false;
        }


        /**
         * Analyses the belongs_to relations
         *
         * @param array $values The relations to analise
         * @return array
         */
        private function _analyseBelongsToRelations($values) {
            $relations = array();

            if($values !== null) {
                foreach($values as $value) {
                    $relation = array(
                    "type"      => "belongs_to",
                    "property"  => $value["property"],
                    "srcTable"  => $this->_name,
                    "dstTable"  => $value["table"],
                    "srcColumn" => $value["srcColumn"],
                    "dstColumn" => $value["dstColumn"],
                    "update"    => $value["update"],
                    "delete"    => $value["delete"]
                    );
                    $relations[$relation["property"]] = $relation;
                }
            }
            return $relations;
        }

        /**
         * Analyses the has_one relations
         *
         * @param array $values The relations to analise
         * @return array
         */
        private function _analyseHasOneRelations($values) {
            $relations = array();

            if($values !== null) {
                foreach($values as $value) {
                    $relation = array(
                    "type"      => "has_one",
                    "property"  => $value["property"],
                    "srcTable"  => $this->_name,
                    "dstTable"  => $value["table"],
                    "srcColumn" => $value["srcColumn"],
                    "dstColumn" => $value["dstColumn"],
                    "update"    => $value["update"],
                    "delete"    => $value["delete"]
                    );
                    $relations[$relation["property"]] = $relation;
                }
            }
            return $relations;
        }

        /**
         * Analyses the has_many relations
         *
         * @param array $values The relations to analise
         * @return array
         */
        private function _analyseHasManyRelations($values) {
            $relations = array();

            if($values !== null) {
                foreach($values as $value) {
                    $relation = array(
                    "type"      => "has_many",
                    "property"  => $value["property"],
                    "srcTable"  => $this->_name,
                    "dstTable"  => $value["table"],
                    "srcColumn" => $value["srcColumn"],
                    "dstColumn" => $value["dstColumn"],
                    "update"    => $value["update"],
                    "delete"    => $value["delete"]
                    );
                    $relations[$relation["property"]] = $relation;
                }
            }
            return $relations;
        }

        /**
         * Analyses the has_and_belongs_to_many relations
         *
         * @param array $values The relations to analise
         * @return array
         */
        private function _analyseHABTMRelations($values) {
            $relations = array();

            if($values !== null) {
                foreach($values as $value) {
                    $relation = array(
                    "type"                  => "has_many_and_belongs_to",
                    "property"              => $value["property"],
                    "srcTable"              => $value["srcTable"],
                    "srcColumn"             => $value["srcColumn"],
                    "dstTable"              => $value["dstTable"],
                    "dstColumn"             => $value["dstColumn"],
                    "throughTable"          => $value["throughTable"],
                    "throughTableSrcColumn" => $value["throughTableSrcColumn"],
                    "throughTableDstColumn" => $value["throughTableDstColumn"],
                    "update"                => $value["update"],
                    "delete"                => $value["delete"]
                    );
                    $relations[$relation["property"]] = $relation;
                }
            }
            return $relations;
        }

        /**
         * Analyses the has many by sql relations
         *
         * @param array $values The relations to analise
         * @return array
         */
        private function _analyseHasManyBySQLRelations($values) {
            $relations = array();

            if($values !== null) {
                foreach($values as $value) {
                    $relation = array(
                    "type"       => "has_many_by_sql",
                    "property"   => $value["property"],
                    "srcTable"   => $this->_name,
                    "dstTable"   => $value["table"],
                    "srcColumn"  => $value["srcColumn"],
                    "dstColumn"  => $value["dstColumn"],
                    "update"     => $value["update"],
                    "delete"     => $value["delete"],
                    "conditions" => $value["conditions"]
                    );
                    $relations[$relation["property"]] = $relation;
                }
            }
            return $relations;
        }

        /**
         * Analyses the has many through relations
         *
         * @param array $values The relations to analise
         * @return array
         */
        private function _analyseHasManyThroughRelations($values) {
            $relations = array();

            if($values !== null) {
                foreach($values as $value) {
                    $relation = array(
                    	"type"                  => "has_many_through",
                    	"property"              => $value["property"],
                    	"srcTable"              => $value["srcTable"],
                    	"srcColumn"             => $value["srcColumn"],
                    	"dstTable"              => $value["dstTable"],
                    	"dstColumn"             => $value["dstColumn"],
                    	"throughTable"          => $value["throughTable"],
                    	"throughTableSrcColumn" => $value["throughTableSrcColumn"],
                    	"throughTableDstColumn" => $value["throughTableDstColumn"],
                    	"update"                => $value["update"],
                    	"delete"                => $value["delete"]
                    );
                    $relations[$relation["property"]] = $relation;
                }
            }
            return $relations;
        }

        /**
         * Analyses the has_one_through relations
         *
         * @param array $values The relations to analise
         * @return array
         */
        private function _analyseHasOneThroughRelations($values) {
            $relations = array();

            if($values !== null) {
                foreach($values as $value) {
                    $relation = array(
                    "type"                  => "has_one_through",
                    "property"              => $value["property"],
                    "srcTable"              => $value["srcTable"],
                    "srcColumn"             => $value["srcColumn"],
                    "dstTable"              => $value["dstTable"],
                    "dstColumn"             => $value["dstColumn"],
                    "throughTable"          => $value["throughTable"],
                    "throughTableSrcColumn" => $value["throughTableSrcColumn"],
                    "throughTableDstColumn" => $value["throughTableDstColumn"],
                    "update"                => $value["update"],
                    "delete"                => $value["delete"]
                    );
                    $relations[$relation["property"]] = $relation;
                }
            }
            return $relations;
        }


        /**
         * Validates this model.
         * Checks if every column in database is a property in the model
         *
         * @return bool
         */
        private function _validate() {
            if(count($this->_columns) > 0) {
                foreach($this->_columns as $key=>$column) {
                    if (!in_array($key,$this->_properties)) {
                        throw new FW_ActiveRecord_Exception("Property {$key} is not defined in the model properties for {$this->_name} model");
                    }
                }
            }
            $this->_isValidSchema = true;
            return true;
        }


        /**
         * Checks if this model has a property
         *
         * @param $property
         * @return unknown_type
         */
        public function hasProperty($property) {
            if(in_array($property, $this->_modelProperties)) {
                return true;
            }
            return false;
        }

        /**
         * Checks if this schema is valid
         * @return bool
         */
        public function isValidSchema() {
            $this->_validate();
            return $this->_isValidSchema;
        }

        /**
         * Gets the name of this model
         *
         * @return string
         */
        public function getName() {
            return $this->_name;
        }

        /**
         * Gets the properties of the model
         *
         * @return array
         */
        public function getModelProperties() {
            if($this->_properties === null) {
                $this->_discoverProperties();
            }
            return $this->_properties;
        }

        /**
         * Gets the primary key of this schema
         *
         * @return array
         */
        public function getPrimaryKey() {
            if($this->_primaryKey === null) {
                throw new FW_ActiveRecord_Exception("Model {$this->_name} doesn't have a Primary Key. Be sure to design the database again!");
            }
            return $this->_primaryKey;
        }

        /**
         * Gets the relations for this schema
         *
         * @return array
         */
        public function getRelations() {
            if($this->_relations === null) {
                $this->_discoverRelations();
            }
            return $this->_relations;
        }

        /**
         * Gets the actings of this schema
         *
         * @return array
         */
        public function getActings() {
            if($this->_actings === null) {
                $this->_discoverActings();
            }
            return $this->_actings;
        }

        /**
         * Gets the columns of the database of this schema
         *
         * @return array
         */
        public function getDataBaseColumns() {
            if($this->_columns === null) {
                $this->_discoverDataBaseColumns();
            }
            return $this->_columns;
        }

        /**
         * Gets a serialization of this schema
         *
         * @return string
         */
        public function getSerializedSchema() {
            return $this->serialize();
        }
        
        
        public function getTextProperties() {
            return $this->_textProperties;
        }

        /**
         * Method to serialize this schema
         *
         * @return array
         */
        public function __sleep() {
            return array("_name","_columns","_properties","_relations","_primaryKey","_table","_actings","_textProperties","_relationProperties");
        }

        /**
         * Method to deserialize this schema
         *
         * @return void
         */
        public function __wakeup() {
            return true;
        }


       /**
        * @return unknown_type
        */
        public function serialize() {
            return serialize($this);
        }
        
        
        
        public function getNotPrimaryKeyColumns() {
            $properties = $this->getModelProperties();        
            $pkeys      = $this->getPrimaryKey();
            $pk         = array();
            foreach ($pkeys as $key=>$value) {
                $pk []= $key;
            }        
            $properties = (array_diff($properties,$pk));
            return $properties;
        }
        
        
        public function isRelation($name) {                                    
            if (in_array($name,$this->_relationProperties)) {
                return true;
            }
            return false;
        }

};
?>