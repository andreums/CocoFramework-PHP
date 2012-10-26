<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
 
abstract class FW_ActiveRecord {


    /**
     * The name of the model
     *
     * @static
     * @var string
     */
    private static $_name;

    /**
     * A handler to a Query Builder
     *
     * @static
     * @var FW_ActiveRecord_QueryBuilder
     */
    private static $_builder;

    /**
     * The prefix of the database
     *
     * @static
     * @var string
     */

    private static $_prefix;


    /**
     * A handler of the database
     *
     * @static
     * @var FW_Database
     */
    private static $_database;


    /**
     * Configures the model
     *
     * @access private
     * @static
     *
     * @return void
     */
    private static function _configure() {
        $name                 = get_called_class();
        $builder              = new FW_ActiveRecord_QueryBuilder($name);
        self::$_name    = $name;
        self::$_builder = $builder;
    }

    /**
     * Configures the database
     *
     * @access private
     * @static
     *
     * @return void
     */
    private static function _configureDatabase() {
        $database              = new FW_Database();
        $prefix                     = $database->getPrefix();
        self::$_database = $database;
        self::$_prefix        = $prefix;
    }


    /**
     * The main finder of ActiveRecord
     *
     * @param array $conditions An array of conditions
     * @param array $orders An array of orders
     * @param array $groups An array to make the GROUP BY
     * @param array $havings An array to make the GROUP BY ... HAVING
     * @param int $limit The number of results to retrieve
     * @param int $offset The offset of the results to retrieve
     *
     * @static
     * @return unknown_type
     */
    public static function find($conditions=array(),$orders=array(),$groups=array(),$havings=array(),$limit=false,$offset=false) {
        self::_configure();
        self::_configureDatabase();
        $model   = self::$_name;
        $results  = array();
        $query    = self::$_builder->select()->where($conditions)->groupBy($groups)->having($havings)->orderBy($orders)->limit($limit)->offset($offset)->getSelect();        
        self::$_database->query($query);                
        
        while ( ($result = self::$_database->fetchAssoc()) ) {            
            $object = new $model($result,true);            
            if (method_exists($object,"afterFind")) {
                $object->afterFind();
            }
            $results[] = $object;
        }
        return new FW_ActiveRecord_Result($results);
    }

    /**
     * Finds a model using pagination
     *
     * @param array $conditions An array of conditions
     * @param array $orders An array of orders
     * @param int $limit The number of results to retrieve
     * @param int $offset The offset of the results to retrieve
     *
     * @static
     * @return mixed
     */
    public static function findPaginated($conditions=array(),$orders=array(),$limit=false,$offset=false) {        
        return self::find($conditions,$orders,array(),array(),$limit,$offset);
    }

    public static function __callStatic($method,$arguments) {

        // findBy ...
        if (strpos($method,"findBy")!==false) {
            $parameter = explode("findBy",$method);
            $parameter = $parameter[1];

            $condition = array (
                array (
                    "name"     => $parameter,
                    "operator" => "=",
                    "value"	   => $arguments[0]
                )
            );

            return self::find($condition);
        }
    }



    /**
     * Loads data from model relations
     *
     * @return void
     */
    protected function _loadRelations() {

        self::_configure();

        $schema = FW_ActiveRecord_Metadata_Manager::getInstance()->getSchema(self::$_name);
        if ($schema!==null) {
            $relations = $schema->getRelations();
            if (count($relations)) {
                foreach ($relations as $relation) {
                    $this->_findRelation($relation);
                }
            }
        }
        else {
            // TODO: Throw exception...
            throw new FW_ActiveRecord_Exception("...");
        }
    }

    protected function _findRelation(array $relation=array()) {
        if (empty($relation)) {
            return;
        }
        else {
            if (($relation["type"]==="has_many_through") || ($relation["type"]==="has_one_through")) {
                $this->_findThroughRelations($relation);
            }
            else {
                $this->_findRelationResult($relation);
            }
        }
        return $this;
    }

    /**
     * Finds the data for a relation
     *
     * @param array $relation Data of the relation
     *
     * @return void
     */
    private function _findRelationResult(array $relation=array()) {
        $results  = array();
        $database = new FW_Database();

        $fkey     = $relation["srcColumn"];
        $prop     = $relation["property"];
        $value    = $this->$fkey;
        $query    = self::$_builder->getSQLForRelation($relation);
        $query    = str_replace("?",$value,$query);
        $model    = $relation["dstTable"];

        $database->query($query);
        while ($result = $database->fetchAssoc()) {

            $check  = ($relation["type"]==="belongs_to"?false:true);
            $object = new $model($result,$check,$this);
            if (method_exists($object,"afterFind")) { 
                $object->afterFind();
            }
            $results[] = $object;
        }

        $rel = new FW_ActiveRecord_Relation();
        $rel->configure($relation);
        $rel->setData($results);
        $rel->setOldValue($this->$fkey);

        $this->$prop = $rel;
    }

    /**
     * Finds the data for relations of type
     * has_many_through and has_one_through
     *
     * @param array $relation Data of the relation
     *
     * @return void
     */
    private function _findThroughRelations(array $relation=array()) {
        $results         = array();
        $results2        = array();
        $property        = $relation["property"];
        $throughModel    = $relation["throughTable"];
        $througDstColumn = $relation["throughTableDstColumn"];
        $model           = $relation["dstTable"];

        $fkey     = $relation["srcColumn"];
        $value    = $this->$fkey;
        $query    = self::$_builder->getSQLForRelation($relation);


        $queryA   = $query[0];
        $queryA   = str_replace("?",$value,$queryA);
        $queryB   = $query[1];
        $queryB   = str_replace("?",$value,$queryB);

        $database = FW_Database::getInstance();
        $database->query($queryA);

        self::$_database->query($queryB);

        while($result=($database->fetchAssoc())) {
            $object = new $throughModel($result,true,$this);
            while($result2=(self::$_database->fetchAssoc())) {
                $object2 = new $model($result2,true,$this);
                if (method_exists($object2,"afterFind")) {
                    $object2->afterFind();
                }
                $results2 []= $object2;
            }
            $rel2 = new FW_ActiveRecord_Relation();
            $rel2->configure($relation);
            $rel2->setData($results2);
            $rel2->setOldValue($object->$througDstColumn);
            $object->$througDstColumn = $rel2;

            $results []= $object;
        }
        $rel = new FW_ActiveRecord_Relation();
        $rel->configure($relation);
        $rel->setData($results);
        $rel->setOldValue($this->$fkey);
        $this->$property = $rel;
    }


    /**
     * Inits a transaction on the database (if available)
     *
     * @static
     * @return mixed
     */
    public static function begin() {
        return self::$_database->begin();
    }


    /**
     * Cancels a running transaction (where available)
     *
     * @static
     * @return mixed
     */
    public static function rollback(){
        return self::$_database->rollback();
    }

    /**
     * Makes commit of a running transaction (where available)
     *
     * @static
     * @return mixed
     */
    public static function commit(){
        return self::$_database->commit();
    }


    /**
     * Returns the SUM() aggregate function
     * value for the column passed as argument.
     *
     * @param $column     The column to make SUM()
     * @param $conditions Conditions to make SUM()
     * @static
     *
     * @return double
     */
    public static function sum($column,$conditions=array(),$orders=array()) {

        self::_configure();
        self::_configureDatabase();

        $results = array();
        $model   = self::$_name;
        $query   = self::$_builder->sum($column)->where($conditions)->orderBy($orders)->getFunctionSelect();
        self::$_database->query($query);
        while ($result = (self::$_database->fetchRow()) ) {
            $results []= $result[0];
        }
        return $results;
    }

    /**
     * Returns the MIN() aggregate function
     * value for the column passed as argument.
     *
     * @param $column     The column to make MIN()
     * @param $conditions Conditions to make MIN()
     * @static
     *
     * @return double
     */
    public static function min($column,$conditions=array(),$orders=array()) {
        self::_configure();
        self::_configureDatabase();

        $results = array();
        $model   = self::$_name;
        $query   = self::$_builder->min($column)->where($conditions)->orderBy($orders)->getFunctionSelect();
        self::$_database->query($query);
        while ($result = (self::$_database->fetchRow()) ) {
            $results []= $result[0];
        }
        return $results;
    }


    /**
     * Returns the MAX() aggregate function
     * value for the column passed as argument.
     *
     * @param $column     The column to make MAX()
     * @param $conditions Conditions to make MAX()
     * @static
     *
     * @return double
     */
    public static function max($column,$conditions=array(),$orders=array()) {
        self::_configure();
        self::_configureDatabase();

        $results = array();
        $model   = self::$_name;
        $query   = self::$_builder->max($column)->where($conditions)->orderBy($orders)->getFunctionSelect();
        self::$_database->query($query);
        while ($result = (self::$_database->fetchRow()) ) {
            $results []= $result[0];
        }
        return $results;
    }

    /**
     * Returns the AVG() aggregate function
     * value for the column passed as argument.
     *
     * @param $column     The column to make AVG()
     * @param $conditions Conditions to make AVG()
     * @static
     *
     * @return double
     */
    public static function avg($column,$conditions=array(),$orders=array(),$groups=array()) {
        self::_configure();
        self::_configureDatabase();

        $results = array();
        $model   = self::$_name;
        $query   = self::$_builder->avg($column)->where($conditions)->groupBy($groups)->orderBy($orders)->getFunctionSelect();
        self::$_database->query($query);
        while ($result = (self::$_database->fetchRow()) ) {
            $results []= $result[0];
        }
        return $results;
    }

    /**
     * Returns the number of rows in the table
     * of the database
     *
     * @param $column     The column to make the COUNT()
     * @param $conditions Conditions to make the COUNT()
     * @static
     *
     * @return double
     */
    public static function count($column='*',$conditions=array(),$orders=array()) {
        $results = array();
        self::_configure();
        self::_configureDatabase();
        $model   = self::$_name;

        $query   = self::$_builder->count($column)->where($conditions)->orderBy($orders)->getFunctionSelect();
        self::$_database->query($query);
        $result = (self::$_database->fetchRow());
        $result = $result[0];
        return $result;
    }


    /**
     * Checks if this object exists as a row
     * in the database
     *
     * @return bool
     */
    public function exists() {
        $this->_savePrimaryKey();

        $result = false;
        self::_configureDatabase();
        $schema = FW_ActiveRecord_Metadata_Manager::getInstance()->getSchema(self::$_name);

        if ($schema===null) {
            throw new FW_ActiveRecord_Exception("...");
        }
        if ( method_exists($this,"beforeExists") ) {
            if(!$this->beforeExists()) {
                return false;
            }
        }

        $properties = $schema->getNotPrimaryKeyColumns();
        $oldValues  = $this->_oldValues;

        $values         = array();
        foreach ($properties as $property) {
            $values[$property] = $oldValues[$property];
        }                        
        $query   = self::$_builder->exists($values,$this->_isNewRecord,$this->_oldPrimaryKey)->getExists();        
        
        self::$_database->query($query);
        if (self::$_database->numRows()>0) {
            $result = true;
        }
        else {
            $result = false;
        }
        if ( method_exists($this,"afterExists") ) {
            if(!$this->afterExists()) {
                return false;
            }
        }
        return $result;
    }


    /**
     * Deletes an object from the database
     *
     * @see    FW_ActiveRecord->delete();
     * @access public
     * @return bool
     */
    public function destroy() {
        return $this->delete();
    }


    /**
     * Deletes an object from the database
     *
     * @access public
     * @return bool
     */
    public function delete() {

        self::_configure();
        self::_configureDatabase();

        $values    = array();
        $relations = array();
        $schema    = FW_ActiveRecord_Metadata_Manager::getInstance()->getSchema(self::$_name);


        if ($schema===null) {
            throw new FW_ActiveRecord_Exception("...");
        }


        if (method_exists($this,"beforeDelete")) {
            if(!$this->beforeDelete()) {
                return false;
            }
        }       

        
        $properties = $schema->getNotPrimaryKeyColumns();
        $relations     = $schema->getRelations();        
        $properties  = $schema->getPrimaryKey();        
        foreach ($properties as $key=>$property) {
            $values [$key]= $this->$key;
        }
        
        
        $this->_savePrimaryKey();

        if (count($relations)) {
            foreach ($relations as $relation) {
                if ($relation["type"]!=="belongs_to") {
                    $property   = $relation["property"];        
                    if (!is_null($this->$property)) {        
                        $aux              = $this->$property->delete();
                    }
                }
            }
        }

        $query = self::$_builder->delete($values,$this->_oldPrimaryKey)->getDelete();

        self::$_database->query($query);
        $rows = self::$_database->affectedRows();
        if ($rows>0) {
            if ( method_exists($this,"afterDelete") ) {
                if(!$this->afterDelete()) {
                    return false;
                }
            }
            return true;
        }
        else {
            return false;
        }
    }


    /**
     * Saves the primary key.
     * Useful for operations like exists,
     * save, update, delete or insert
     *
     * @return void
     */
    protected function _savePrimaryKey() {
        self::_configure();
        $schema     = FW_ActiveRecord_Metadata_Manager::getInstance()->getSchema(self::$_name);
        if ($schema===null) {
            throw new FW_ActiveRecord_Exception("...");
        }
        $primaryKey = $schema->getPrimaryKey();
        if (count($primaryKey)>0) {
            foreach ($primaryKey as $key=>$val) {
                $name  = $key;
                $value = $this->$key;
                $this->_oldPrimaryKey[$name] = $value;
            }
        }
        return $this->_oldPrimaryKey;
    }


    /**
     * TODO
     * Saves the object in the database
     *
     * @return bool
     */
    public function save() {
        self::_configure();
        $result = -1;
        if ( method_exists($this,"beforeSave") ) {            
            if(!$this->beforeSave()) {
                return false;
            }
        }
        if ($this->validate()) {            
            if ($this->exists()) {                
                $result = $this->_update();
            }
            else {                
                $result =  $this->_insert();
            }
        }
        else {
            $result = false;
        }
        if ( method_exists($this,"afterSave") ) {
            if(!$this->afterSave()) {
                return false;
            }
        }
        return $result;
    }

    public function update() {
        return $this->_update();
    }


    /**
     * Updates a record on the database
     *
     * @return bool
     */
    private function _update() {
        self::_configure();
        self::_configureDatabase();
        $values = array();
        $result = false;
        
        if (!$this->valuesHaveChanged()) {
            return true;
        }        

        if ( method_exists($this,"beforeUpdate") ) {
            if(!$this->beforeUpdate()) {
                return false;
            }
        }
        $this->_savePrimaryKey();

        $schema = FW_ActiveRecord_Metadata_Manager::getInstance()->getSchema(self::$_name);
        if ($schema===null) {
            throw new FW_ActiveRecord_Exception("...");
        }
        $properties = $schema->getModelProperties();
        $relations    = $schema->getRelations();
        foreach ($properties as $value) {
            $values [$value] = $this->$value;
        }
        if (count($relations)) {
            foreach ($relations as $relation) {                
                if ($relation["type"]!=="belongs_to") {
                    $property   = $relation["property"];                    
                    if (!is_null($this->$property)) {
                        $aux              = $this->$property->update();
                    }
                }  
            }
        }        
        $name          = get_called_class();        
        $builder       = new FW_ActiveRecord_QueryBuilder($name);
        $database   = new FW_Database();
        $query         = $builder->update($values,$this->_oldPrimaryKey)->getUpdate();
        $database->query($query);
        $rows           = $database->affectedRows();
        $result = true;        
        if ( method_exists($this,"afterUpdate") ) {
            if (!$this->afterUpdate()) {
                return false;
            }
        }
        return $result;
    }

    /**
     * Inserts data on the database
     *
     * @return bool
     */
    protected function _insert() {
        self::_configure();
        self::_configureDatabase();
        $values = array();
        $result = false;

        if ( method_exists($this,"beforeInsert") ) {
            if(!$this->beforeInsert()) {
                return false;
            }
        }
        $schema = FW_ActiveRecord_Metadata_Manager::getInstance()->getSchema(self::$_name);
        if ($schema===null) {
            throw new FW_ActiveRecord_Exception("...");
        }

        $properties = $schema->getModelProperties();
        foreach ($properties as $value) {
            $values [$value] = $this->$value;
        }
        $name         = get_called_class();        
        $builder      = new FW_ActiveRecord_QueryBuilder($name);
        $database = new FW_Database();
        $query        = $builder->insert($values)->getInsert();        
        $database->query($query);
        if ($database->affectedRows()>0) {
            $result = true;
        }
        else {
            $result = false;
        }
        if ( method_exists($this,"afterInsert") ) {
            if (!$this->afterInsert()) {
                return false;
            }
        }
        return $result;
    }


    public function valuesHaveChanged() {
        $result = false;
        foreach ($this->_oldValues as $key=>$value) {
            if (is_a($this->$key,"FW_ActiveRecord_Relation")) {
                if ($this->$key->getOldValue()!==$value) {
                    $result = true;
                }
            }
            else {
                if ($this->$key!==$value) {
                    $result = true;
                }
            }             
        }        
        return $result;        
    }

    public function validate() {
        return true;
        $validator =  new FW_ActiveRecord_Validator(get_class($this),$this);  
        return $validator->validate();
    }




};
?>