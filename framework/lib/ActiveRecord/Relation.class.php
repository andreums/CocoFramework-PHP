<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_ActiveRecord_Relation extends FW_ActiveRecord_Result {

        private $_oldValue;
        private $_relationInfo;

        private $_database;
        private $_prefix;

        public function __construct() {
        }

        public function __destruct() {
            $this -> _objects = null;
        }

        /**
         * Configures the relation
         *
         * @param array $relation Information about the relation
         *
         * @return void
         */
        public function configure(array $relation = array()) {
            $this -> _relationInfo = $relation;
        }

        /**
         * Sets the data of this result
         *
         * @see framework/lib/ActiveRecord/FW_ActiveRecord_Result#setData($data)
         * @param array $data The data to set
         *
         * @return void
         */
        public function setData(array $data) {
            $this -> clear();
            $this -> _objects = $data;
        }

        /**
         * Configures the database for use with this relation
         *
         * @return void
         */
        private function _configureDatabase() {
            $this -> _database = FW_Database::getInstance();
            $this -> _prefix = $this -> _database -> getPrefix();
        }

        public function find($conditions = "", array $orders = array(), $limit = 0, $offset = 0) {
            $this -> _configureDatabase();
            $result = null;
            $type = $this -> _relationInfo["type"];
            
            if (($type === "has_many_through") || ($type === "has_one_through")) {
                $result = $this -> _findThroughRelations($conditions, $orders, $limit, $offset);
            }
            else {
                $result = $this -> _findRelationResult($conditions, $orders, $limit, $offset);
            }
            return $result;

        }

        /*else {
         $builder   = new FW_ActiveRecord_QueryBuilder($model);

         $query     =
        $builder->getSQLForRelation($this->_relationInfo,$conditions,$orders,$limit,$offset);
         $query     = str_replace("?",$value,$query);
         $this->_database->query($query);
         while ($result = $this->_database->fetchAssoc()) {
         $object = new $model($result,true);
         if (method_exists($object,"afterFind")) {
         $object->afterFind();
         }
         $results[] = $object;
         }
         return new FW_ActiveRecord_Result($results);
         }*/

        /**
         * Finds the data for a relation
         *
         * @param array $relation Data of the relation
         *
         * @return void
         */
        private function _findRelationResult($conditions = "", array $orders = array(), $limit = 0, $offset = 0) {
            $results = array();
            $model = $this -> _relationInfo["dstTable"];
            $type = $this -> _relationInfo["type"];
            $value = $this -> _oldValue;
            $builder = new FW_ActiveRecord_QueryBuilder($model);

            $query = $builder -> getSQLForRelation($this -> _relationInfo, $conditions, $orders, $limit, $offset);            
            $query = str_replace("?", $value, $query);
            $this -> _database -> query($query);
            while ($result = $this -> _database -> fetchAssoc()) {
                $object = new $model($result, true);
                if (method_exists($object, "afterFind")) {
                    $object -> afterFind();
                }
                $results[] = $object;
            }
            return new FW_ActiveRecord_Result($results);
        }

        /**
         * Finds the data for relations of type
         * has_many_through and has_one_through
         *
         * @param array $relation Data of the relation
         *
         * @return void
         */
        private function _findThroughRelations($conditions = "", array $orders = array(), $limit = 0, $offset = 0) {
            $results = array();
            $results2 = array();

            $property = $this -> _relationInfo["property"];
            $throughModel = $this -> _relationInfo["throughTable"];
            $througDstColumn = $this -> _relationInfo["throughTableDstColumn"];
            $model = $this -> _relationInfo["dstTable"];
            $fkey = $this -> _relationInfo["srcColumn"];

            $value = $this -> _oldValue;

            $builder = new FW_ActiveRecord_QueryBuilder($model);
            $query = $builder -> getSQLForRelation($this -> _relationInfo, $conditions);

            $queryA = $query[0];
            $queryA = str_replace("?", $value, $queryA);
            $queryB = $query[1];
            $queryB = str_replace("?", $value, $queryB);

            $database = new FW_Database();
            $databaseB = new FW_Database();
            $database -> query($queryA);
            $databaseB -> query($queryB);

            while ($result = ($database -> fetchAssoc())) {
                $object = new $throughModel($result, true, $this);
                while ($result2 = ($databaseB -> fetchAssoc())) {

                    $object2 = new $model($result2, true, $this);
                    if (method_exists($object2, "afterFind")) {
                        $object2 -> afterFind();
                    }
                    $results2[] = $object2;
                }
                $rel2 = new FW_ActiveRecord_Relation();
                $rel2 -> configure($this -> _relationInfo);
                $rel2 -> setData($results2);
                $rel2 -> setOldValue($object -> $througDstColumn);
                $object -> $througDstColumn = $rel2;

                $results[] = $object;
            }
            $res = new FW_ActiveRecord_Result($results);
            return $res;
        }

        public function add(FW_ActiveRecord_Model $object) {
            $this -> _configureDatabase();
            $result = false;
            $model = get_class($object);
            $expectedModel = $this -> _relationInfo["dstTable"];
            $foreignKey = $this -> _relationInfo["srcColumn"];
            $dstColumn = $this -> _relationInfo["dstColumn"];
            $type = $this -> _relationInfo["type"];

            if ($expectedModel !== $model) {
                throw new FW_ActiveRecord_Exception("Can't insert data into this relation. Expecting {$expectedModel} but given {$model}. Aborting");
            }

            $schema = FW_ActiveRecord_Metadata_Manager::getInstance() -> getSchema($model);
            if ($schema === null) {
                throw new FW_ActiveRecord_Exception("...");
            }

            $primaryKey = $schema -> getPrimaryKey();

            if (in_array($type, array(
                    "has_one",
                    "has_many",
                    "belongs_to"
            ))) {
                $fkValue = $this -> _oldValue;
                $object -> $foreignKey = $fkValue;

                if ($object -> save()) {
                    $result = true;
                }
                else {
                    $result = false;
                }
            }

            if ($type === "has_many_and_belongs_to") {

                if ($object -> save()) {
                    $fkValue = $this -> _oldValue;
                    $throughTable = $this -> _relationInfo["throughTable"];
                    $throughTable = $this -> _prefix . $throughTable;
                    $throughSrcColumn = $this -> _relationInfo["throughTableSrcColumn"];
                    $throughDstColumn = $this -> _relationInfo["throughTableDstColumn"];

                    $query = "SELECT * FROM {$throughTable} WHERE ( {$throughSrcColumn}='{$fkValue}' AND {$throughDstColumn}='{$object->$dstColumn}' )";                    
                    $this -> _database -> query($query);
                    if ($this -> _database -> numRows() === 0) {
                        $query = "INSERT INTO {$throughTable} ({$throughSrcColumn},{$throughDstColumn}) VALUES ('{$fkValue}','{$object->$dstColumn}')";                        
                        $this -> _database -> query($query);
                        if ($this -> _database -> affectedRows()) {
                            $result = true;
                        }
                        else {
                            $result = false;
                        }
                    }

                }
            }

            return $result;
        }

        public function _getLastInsertedFor($pk, $name) {
            $this -> _setUpDataBase();
            $name = self::$_dbPrefix . $name;
            $pKeys = array_keys($pk);
            $idKey = $pKeys[0];
            $query = "SELECT MAX({$idKey}) FROM {$name}";
            self::$_dataBase -> query($query);
            if (self::$_dataBase -> numRows() > 0) {
                $lastId = (self::$_dataBase -> fetchRow());
                return $lastId[0];
            }
            return null;
        }

        public function update() {
            $result = false;
            $type = $this -> _relationInfo["update"];
            $value = $this -> getOldValue();
            $foreignKey = $this -> _relationInfo["dstColumn"];

            // TODO: Refactor
            $this -> setDatabase();

            $type = $this -> _relationInfo["type"];
            
            /*$ttable = $this -> _relationInfo["throughTable"];
            $ttsrccol = $this -> _relationInfo["throughTableSrcColumn"];
            $ttdstcol = $this -> _relationInfo["throughTableDstColumn"];
            $dstCol = $this -> _relationInfo["dstColumn"];*/

            if ($type === "restrict") {
                $result = false;
            }

            if ($type === "set null") {
                if (count($this -> _objects)) {
                    foreach ($this->_objects as $object) {
                        $object -> $foreignKey = null;
                        if (!$object -> update()) {
                            $result = false;
                        }
                    }
                    if ($result !== false) {
                        $result = true;
                    }
                }
            }

            if ($type === "cascade") {
                if (count($this -> _objects)) {
                    foreach ($this->_objects as $object) {
                        $object -> $foreignKey = $value;
                      
                        /*if ($type==="has_many_and_belongs_to" || $type==="has_many_through" || $type==="has_one_through") {
                            $query = "UPDATE {$this->_prefix}{$ttable} SET {$ttsrccol}='{$this->_oldValue}' AND {$ttdstcol}='{$object->$dstCol}'";
                            if ($this->_database->affectedRows()===0) {
                                $result = false;
                            }
                        }*/
                        if (!$object -> update()) {
                            $result = false;
                        }
                    }
                    if ($result !== false) {
                        $result = true;
                    }
                }
            }
            return $result;
        }

        /**
         * Deletes all the objects in the relation
         *
         * @return bool
         */
        public function delete() {
            $result = false;
            $type    = $this -> _relationInfo["delete"];
            $rtype  = $this -> _relationInfo["type"];

            if ($type === "restrict") {
                $result = false;
            }
            if ($type === "set null") {
                $foreignKey = $this -> _relationInfo["dstColumn"];
                if (count($this -> _objects)) {
                    foreach ($this->_objects as $object) {
                        $object -> $foreignKey = null;
                        if (!$object -> save()) {
                            $result = false;
                        }
                    }
                    if ($result !== false) {
                        $result = true;
                    }
                }
            }

            if ($type === "cascade") {
                // TODO: Refactor
                $this -> setDatabase();
                
                if ($rtype==="has_many_and_belongs_to" || $type === "has_many_through" || $type === "has_one_through") {                                    
                    $ttable    = $this -> _relationInfo["throughTable"];
                    $ttsrccol = $this -> _relationInfo["throughTableSrcColumn"];
                    $ttdstcol = $this -> _relationInfo["throughTableDstColumn"];
                    $dstCol     = $this -> _relationInfo["dstColumn"];

                    if (count($this -> _objects)) {
                        foreach ($this->_objects as $object) {                            
                                $query = "DELETE FROM {$this->_prefix}{$ttable} WHERE {$ttsrccol}='{$this->_oldValue}' AND {$ttdstcol}='{$object->$dstCol}'";
                                if ($this -> _database -> affectedRows() === 0) {
                                    $result = false;
                                }
                            }
                            if (!$object -> delete()) {
                                $result = false;
                            }
                            if ($result !== false) {
                                $result = true;
                            }
                        }
                    }

                    else if ($rtype==="has_one" || $rtype==="has_many" || $rtype==="has_many_by_sql")  {
                        if (count($this->_objects)) {
                            foreach ($this->_objects as $object) {
                                if (!$object->delete()) {
                                    $result = false;
                                }                               
                            }
                        }
                        if ($result!==false) {
                            $result = true;
                        }                        
                    }
            }
            return $result;
        }

        /**
         * Sets the value of the foreign key
         *
         * @param mixed $value The value of the foreign key
         *
         * @return void
         */
        public function setOldValue($value) {
            $this -> _oldValue = $value;
        }

        /**
         * Gets the value of the foreign key
         *
         * @return mixed
         */
        public function getOldValue() {
            return $this -> _oldValue;
        }

        public function __toString() {            
            return $this -> _oldValue;
        }

        public function setDatabase() {
            $this -> _database = FW_Database::getInstance();
            $this -> _prefix = $this -> _database -> getPrefix();
        }

    };
?>