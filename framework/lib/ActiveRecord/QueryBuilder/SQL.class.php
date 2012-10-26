<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_ActiveRecord_QueryBuilder_SQL extends FW_ActiveRecord_QueryBuilder_Base {

    private $_prefix;
    private $_database;


    private $_modelName;
    private $_table;


    private $_select;
    private $_update;
    private $_insert;
    private $_delete;


    private $_distinct;
    private $_where;
    private $_group;
    private $_having;
    private $_function;
    private $_updateColumnsValues;
    private $_updateOldPk;
    private $_insertValues;
    private $_deleteColumnsValues;
    private $_deleteOldPk;


    private $_order;
    private $_limit;
    private $_offset;


    private $operation;
    private $joins;



    /**
     * Configures the SQL Query builder
     *
     * @param FW_Container_Parameter $parameters The parameters
     *
     * @return void
     */
    public function configure(FW_Container_Parameter $parameters=null) {
        parent::configure($parameters);

        $this->_database = new FW_Database();
        $this->_prefix   = $this->_database->getPrefix();
        $this->_order    = "";
        $this->getTableName();
    }

    /**
     * Initializes the SQL Query builder
     *
     * @return void
     */
    private function _initialize() {}

    /**
     * Gets the name of the table
     *
     * @return string
     */
    public function getTableName() {
        $this->_table = $this->_prefix.$this->_name;
        return $this->_table;
    }


    private function _isCondition(array $condition=array()) {
        $value    = ( (isset($condition["value"])) || ($condition["value"]===null) );
        $operator = ( (isset($condition["name"])) && (isset($condition["operator"])) );
                
        if ($value && $operator) {            
            return true;
        }        
        return false;
    }

    private function _isOperator(array $condition=array()) {
        if ( (!isset($condition["name"]) && !isset($condition["operator"]) && !isset($condition["value"])) && ( isset($condition["condition"]) )) {
            return true;
        }
        return false;
    }

    /**
     * Generates the conditions of the where
     * clause
     *
     * @param array $conditions The conditions of the where clause
     *
     * @return $this
     */
    public function where($conditions=array()) {
        $where = "";    
        if (is_array($conditions)) {        
            if (!empty($conditions)) {
                $numConditions = count($conditions);
                if ($numConditions>0) {
                    for ($i=0;$i<$numConditions;$i++) {                        
                        if ($this->_isOperator($conditions[$i])) {
                            if ($i<($numConditions-1)) {
                                $operator = $conditions[$i]["condition"];
                                $where .= " {$operator} ";
                                continue;
                            }
                        }
                        if ( !isset($conditions[($i-1)]["condition"]) && !isset($conditions[($i)]["condition"]) && !isset($conditions[($i+1)]["condition"]) ) {
                            if ($i>0 && $i<=($numConditions-1)) {
                                $where .= " AND ";
                            }
                        }
    
                        if ($this->_isCondition($conditions[$i])) {                        
    
                            $isFunction = false;
                            $functions  = array("SELECT","DATE","DAY","MINUTE","HOUR","MINUTE","TIME");
                            foreach ($functions as $function) {                            
                                if (strpos($conditions[$i]["value"],$function)!==false) {
                                    $isFunction = true;
                                }
                            }
                            if ($isFunction===true) {
                                $where .= " ( {$conditions[$i]["name"]} {$conditions[$i]["operator"]} {$conditions[$i]["value"]} )";
                            }
                            else {
                                if (in_array($conditions[$i]["value"],array("NULL","null",null,NULL)))  {                                                                                                                                        
                                    if (! isset($conditions[$i]["condition"])) {
                                        if ($conditions[$i]["value"]===null) {                                        
                                            $where .= "( {$conditions[$i]["name"]} IS NULL )";
                                        }
                                        else if (empty($conditions[$i]["value"])) {
                                            $where .= "( {$conditions[$i]["name"]}='')";                                            
                                        }                                    
                                    }
                                }
                                else if (in_array($conditions[$i]["operator"],array("IN","NOT IN"))) {
                                    $where .= "( {$conditions[$i]["name"]} {$conditions[$i]["operator"]} ({$conditions[$i]["value"]}) ) ";
                                }
                                else {
                                    $where .= "( {$conditions[$i]["name"]} {$conditions[$i]["operator"]} '{$conditions[$i]["value"]}' )";
                                }
                            }
                        }
                    }
                }
            }
        }         
        else {
            $where = $conditions;
        }
        $this->_where = $where;  
        return $this;
    }



    /**
     * Generates the conditions for the having clause
     *
     * @param array $conditions The conditions of the having clause
     *
     * @return $this
     */
    public function having(array  $conditions=array()) {
        $having = "";
        if (!empty($conditions)) {
            $numConditions = count($conditions);
            if ($numConditions>0) {
                for ($i=0;$i<$numConditions;$i++) {

                    if ($this->_isOperator($conditions[$i])) {
                        if ($i<($numConditions-1)) {
                            $operator = $conditions[$i]["condition"];
                            $having .= " {$operator} ";
                            continue;
                        }
                    }

                    if ( !isset($conditions[($i-1)]["condition"]) && !isset($conditions[($i)]["condition"]) && !isset($conditions[($i+1)]["condition"]) ) {
                        if ($i>0 && $i<=($numConditions-1)) {
                            $having .= " AND ";
                        }
                    }

                    if ($this->_isCondition($conditions[$i])) {
                        $isFunction = false;
                        $functions  = array("SELECT","DATE","DAY","MINUTE","HOUR","MINUTE","TIME");
                        foreach ($functions as $function) {
                            if (strpos($conditions[$i]["value"],$function)!==false) {
                                $isFunction = true;
                            }
                        }
                        if ($isFunction===true) {
                            $having .= " ( {$conditions[$i]["name"]} {$conditions[$i]["operator"]} {$conditions[$i]["value"]} )";
                        }
                        else {
                            if (in_array($conditions[$i]["value"],array("NULL","null",null,NULL)))  {
                                if (in_array($conditions[$i]["value"],array(0,"0"))) {
                                    $having .= "( {$conditions[$i]["name"]}='0' )";
                                }
                                else {
                                    if (! isset($conditions[$i]["condition"])) {
                                        $having .= "( {$conditions[$i]["name"]} IS NULL )";
                                    }
                                }
                            }
                            else if ($conditions[$i]["operator"]=="LIKE") {
                                $having .= "( {$conditions[$i]["name"]} {$conditions[$i]["operator"]} '{$conditions[$i]["value"]}' ) ";
                            }
                            else if (in_array($conditions[$i]["operator"],array("LIKE","IN","NOT IN"))) {
                                $having .= "( {$conditions[$i]["name"]}  {$conditions[$i]["operator"]}  {$conditions[$i]["value"]} )";
                            }
                            else {
                                $having .= "( {$conditions[$i]["name"]}{$conditions[$i]["operator"]}'{$conditions[$i]["value"]}' )";
                            }
                        }
                    }
                }
            }
        }
        $this->_having = $having;
        return $this;
    }


    /**
     * Builds the group by clause for the
     * select queries
     *
     * @param array $groups An array of group constraints
     *
     * @return $this
     */
    public function groupBy(array  $groups=array()) {
        $group = "";
        if (!empty($groups)) {
            foreach ($groups as $element) {
                $group .= " {$element} ,";
            }
            $group = substr($group,0,-1);
        }
        $this->_group = $group;
        return $this;
    }

    /**
     * Builds the order parameters for the
     * select queries
     *
     * @param array $orders An array of order constraints
     *
     * @return $this
     */
    public function orderBy(array  $orders=array()) {
        $order = "";
        if (count($orders)>0) {
            foreach ($orders as $element) {
                $order .= "{$element["column"]} {$element["type"]} ,";
            }
            $order = substr($order,0,-1);
        }
        $this->_order = $order;
        return $this;
    }

    /**
     * Adds a limit parameter to the
     * select queries
     *
     * @param int $limit The limit
     *
     * @return $this
     */
    public function limit($limit) {
        if (is_numeric($limit)) {
            $this->_limit = $limit;
        }
        return $this;
    }

    /**
     * Adds an offset parameter to the
     * select queries
     *
     * @param int $offset The offset
     *
     * @return $this
     */
    public function offset($offset) {
        if (is_numeric($offset)) {
            $this->_offset = $offset;
        }
        return $this;
    }







    /**
     * Configures the SUM query
     *
     * @param string $column The column to make
     * the SUM
     *
     * @return $this
     */
    public function sum($column) {
        $function = "SUM({$column})";
        $this->_function = $function;
        return $this;
    }

    /**
     * Configures the MIN query
     *
     * @param string $column The column to make
     * the MIN
     *
     * @return $this
     */
    public function min($column) {
        $function = "MIN({$column})";
        $this->_function = $function;
        return $this;
    }


    /**
     * Configures the MAX query
     *
     * @param string $column The column to make
     * the MAX
     *
     * @return $this
     */
    public function max($column) {
        $function = "MAX({$column})";
        $this->_function = $function;
        return $this;
    }

    /**
     * Configures the AVG query
     *
     * @param string $column The column to make
     * the AVG
     *
     * @return $this
     */
    public function avg($column) {
        $function = "AVG({$column})";
        $this->_function = $function;
        return $this;
    }

    /*
     * Configures the count query
     *
     * @param string $column The column to make the
     * count
     *
     * @return $this
     */
    public function count($column) {
        $function = "COUNT({$column})";
        $this->_function = $function;
        return $this;
    }

    /**
     * Configures the distinct query
     *
     * @param string $column The column to make
     * the distinct
     *
     * @return $this
     */
    public function distinct($column) {
        $distinct   = "";
        $disctinct .= "DISTINCT({$column})";
        $this->_distinct = $disctinct;
        return $this;
    }


    /**
     * Configures the select query
     *
     * @param array $columns The columns to select
     *
     * @return $this
     */
    public function select(array  $columns=array()) {
        $select = "";
        if (empty($columns)) {
            $columns = $this->_schema->getModelProperties();
        }
        $select .= $this->_createColumnList($columns);
        $this->_select = $select;
        return $this;
    }


    /**
     * Generates an exists query
     *
     * @param array $data The values of the model
     * @param bool $isNewRecord Indicates if this is a new record
     * @param array $oldPrimaryKey The values of the primary key (before changes)
     *
     * @return $this
     */
    public function exists(array $data=array(),$isNewRecord,array $oldPrimaryKey=array()) {        
        $conditions = array();
        $conditions = array_merge($conditions,$this->_createColumnArrayWithValues($data));
        $conditions = array_merge($conditions,$this->_createColumnArrayWithValues($oldPrimaryKey));
        $this->where($conditions);
        return $this;
    }

    /**
     * Configures the update query
     *
     * @param array $columns The values of the columns (excluding the primary key)
     * @param array $primaryKeyValues The values of the primary key columns
     *
     * @return $this
     */
    public function update(array  $columns=array(),array  $primaryKeyValues=array()) {
        $updateColumns  = "";
        $updateOldPks   = "";
        $updateColumns .= $this->_createColumnListWithValues($columns);
        $updateOldPks  .= $this->_createColumnListWithValues($primaryKeyValues);

        $this->_updateColumnsValues = $updateColumns;
        $this->_updateOldPk         = $updateOldPks;
        return $this;
    }

    /**
     * Configures the delete query
     *
     * @param array $columns The values of the columns (excluding the primary key)
     * @param array $primaryKeyValues The values of the primary key columns
     *
     * @return $this
     */
    public function delete(array  $columns=array(),array  $primaryKeyValues=array()) {
        $deleteColumns  = "";
        $deleteOldPks   = "";
        $deleteColumns .= $this->_createColumnListWithValuesAnd($columns);
        $deleteOldPks  .= $this->_createColumnListWithValuesAnd($primaryKeyValues);

        $this->_deleteColumnsValues = $deleteColumns;
        $this->_deleteOldPk         = $deleteOldPks;
        return $this;
    }

    /**
     * Configures the insert query
     *
     * @param array $values The values to insert
     *
     * @return $this
     */
    public function insert(array  $values=array()) {
        $insert              = "";
        $insert             .= $this->_createValueList($values);
        $this->_insertValues = $insert;
        return $this;
    }


    /**
     * Builds exists SQL query
     *
     * @return $this
     */
    private function _buildExists() {
        $query = "SELECT ".$this->_createModelColumnList()." FROM ".$this->getTableName();
        $query .= " WHERE  ( ".$this->_where. " ) LIMIT 1";
        $this->_select = $query;
        return $this;
    }

    /**
     * Builds a query for a column function
     *
     * @return $this
     */
    private function _buildColumnFunctionSelect() {
        $query = "SELECT ".$this->_function." FROM ".$this->getTableName();
        if (!empty($this->_where)) {
            $query .= " WHERE ( ".$this->_where." ) ";
        }
        if (!empty($this->_group)) {
            $query .= " GROUP BY ".$this->_group." ";
        }
        if (!empty($this->_having)) {
            $query .= " HAVING ( ".$this->_having." ) ";
        }
        if (!empty($this->_order)) {
            $query .= " ORDER BY  ".$this->_order."  ";
        }
        if ($this->_limit>0) {
            $query .= " LIMIT ".$this->_limit." ";
        }
        if ($this->_offset>0) {
            $query .= " OFFSET ".$this->_offset." ";
        }
        $this->_select = $query;
        return $this;
    }

    /**
     * Builds the select query
     *
     * @return $this
     */
    private function _buildSelect() {
        $query = "SELECT ".$this->_select." FROM ".$this->getTableName();
        if (!empty($this->_where)) {
            $query .= " WHERE ( ".$this->_where." ) ";
        }
        if (!empty($this->_group)) {
            $query .= " GROUP BY ".$this->_group." ";
        }
        if (!empty($this->_having)) {
            $query .= " HAVING ( ".$this->_having." ) ";
        }
        if (!empty($this->_order) && ($this->_order!="")) {
            $query .= " ORDER BY  ".$this->_order."  ";
        }
        if ($this->_limit>0) {
            $query .= " LIMIT ".$this->_limit." ";
        }
        if ($this->_offset>0) {
            $query .= " OFFSET ".$this->_offset." ";
        }
        $this->_select = $query;
        return $this;
    }

    /**
     * Builds the Distinct query
     *
     * @return $this
     */
    private function _buildDistinct() {
        $query = "SELECT ".$this->_distinct.",".$this->_select." FROM ".$this->getTableName();
        if (!empty($this->_where)) {
            $query .= " WHERE ( ".$this->_where." ) ";
        }
        if (!empty($this->_group)) {
            $query .= " GROUP BY ".$this->_group." ";
        }
        if (!empty($this->_having)) {
            $query .= " HAVING ( ".$this->_having." ) ";
        }
        if (!empty($this->_order) && ($this->_order!="")) {
            $query .= " ORDER BY  ".$this->_order."  ";
        }
        if ($this->_limit>0) {
            $query .= " LIMIT ".$this->_limit." ";
        }
        if ($this->_offset>0) {
            $query .= " OFFSET ".$this->_offset." ";
        }
        $this->_select = $query;
        return $this;
    }



    /**
     * Builds the delete query
     *
     * @return $this
     */
    private function _buildDelete() {
        $query = "DELETE FROM ".$this->getTableName();

        $query .= " WHERE ( ";
        $query .= $this->_deleteColumnsValues;
        $query .= " AND ";
        $query .= $this->_deleteOldPk;
        $query .= " ) ";

        $this->_delete = $query;
        return $this;
    }



    /**
     * Generates an update query
     *
     * @return $this
     */
    private function _buildUpdate() {
        $query  = "UPDATE ".$this->getTableName();
        $query .= " SET ".$this->_updateColumnsValues;
        $query .= "WHERE ".$this->_updateOldPk;
        $this->_update = $query;
        return $this;
    }

    /**
     * Generates an insert query
     *
     * @return $this
     */
    private function _buildInsert() {
        $query = "INSERT INTO ".$this->getTableName();
        $query .=" ( ".$this->_createModelColumnList()." ) ";
        $query .= " VALUES ( ".$this->_insertValues." )";
        $this->_insert = $query;
        return $this;
    }

    /**
     * Gets an SQL query for insert
     *
     * @return string
     */
    public function getInsert() {
        $this->_buildInsert();
        return $this->_insert;
    }

    /**
     * Gets an SQL query for update
     *
     * @return string
     */
    public function getUpdate() {
        $this->_buildUpdate();
        return $this->_update;
    }

    /**
     * Gets an SQL query for select
     *
     * @return string
     */
    public function getSelect() {
        $this->_buildSelect();
        return $this->_select;
    }

    /**
     * Gets an SQL query for distinct
     *
     * @return string
     */
    public function getDistinct() {
        $this->_buildDistinct();
        return $this->_select;
    }

    /**
     * Gets an SQL query for delete
     *
     * @return string
     */
    public function getDelete() {
        $this->_buildDelete();
        return $this->_delete;
    }

    /**
     * Gets an SQL query for a column function
     *
     * @return string
     */
    public function getFunctionSelect() {
        $this->_buildColumnFunctionSelect();
        return $this->_select;
    }

    /**
     * Gets the exists SQL Query
     *
     * @return string
     */
    public function getExists() {
        $this->_buildExists();
        return $this->_select;
    }


    /**
     * Gets SQL query for the relation
     *
     * @param array $relation Data of the relation
     *
     * @return string
     */
    public function getSQLForRelation(array $relation=array(),$conditions="",array $orders=array(),$limit=0,$offset=0) {
        $query = "";        

        if (!empty($relation)) {
            if ($relation["type"] === "has_one") {
                $query = $this->_buildHasOneRelationQuery($relation,$conditions);
            }

            if ($relation["type"] === "has_many") {
                $query = $this->_buildHasManyRelationQuery($relation,$conditions);
            }

            if ($relation["type"] === "belongs_to") {
                $query = $this->_buildBelongsToRelationQuery($relation,$conditions);
            }

            if ($relation["type"] === "has_many_and_belongs_to")  {
                $query = $this->_buildHasManyAndBelongsToRelationQuery($relation,$conditions);
            }

            if ($relation["type"] === "has_many_by_sql") {
                $query = $this->_buildHasManyBySQLRelationQuery($relation,$conditions);
            }

            if ($relation["type"] === "has_many_through")  {
                $query = $this->_buildHasManyThroughRelationQuery($relation,$conditions);
            }

            if ($relation["type"] === "has_one_through")  {
                $query = $this->_buildHasOneThroughRelationQuery($relation,$conditions);
            }
        }

        if (!empty($orders)) {
            $this->orderBy($orders);
        }

        if ($limit!==0) {
            $this->limit($limit);
            $this->offset($offset);
        }

        if (!empty($this->_order) && ($this->_order!="")) {
            $query .= " ORDER BY  ".$this->_order."  ";
        }
        if ($this->_limit>0) {
            $query .= " LIMIT ".$this->_limit." ";
        }
        if ($this->_offset>0) {
            $query .= " OFFSET ".$this->_offset." ";
        }
        return $query;
    }

    /**
     * Builds the SQL query for the Has One By SQL relations
     *
     * @param array $relation Data of the relation
     *
     * @return string
     */
    private function _buildHasOneRelationQuery(array $relation=array(),$conditions="") {
        $property  = $relation["property"];

        $table     = $relation["dstTable"];
        $table     = $this->_prefix.$table;

        $src       = $relation["srcColumn"];
        $dst       = $relation["dstColumn"];


        $condition = "{$dst}='?' ";

        if (!empty($conditions)) {
            $this->where($conditions);
            $query     = "SELECT * FROM {$table} WHERE ( {$condition} ) AND ( {$this->_where} ) LIMIT 1";
        }
        else {
            $query     = "SELECT * FROM {$table} WHERE ( {$condition} ) LIMIT 1";
        }

        return $query;
    }

    /**
     * Builds the SQL query for the Has Many relations
     *
     * @param array $relation Data of the relation
     *
     * @return string
     */
    private function _buildHasManyRelationQuery(array $relation=array(),$conditions="") {
        $property  = $relation["property"];

        $table     = $relation["dstTable"];
        $table     = $this->_prefix.$table;

        $src       = $relation["srcColumn"];
        $dst       = $relation["dstColumn"];

        $condition = "{$dst}='?' ";

        if (!empty($conditions)) {
            $this->where($conditions);
            $query     = "SELECT * FROM {$table} WHERE ( {$condition} ) AND ( {$this->_where} )";
        }
        else {
            $query     = "SELECT * FROM {$table} WHERE ( {$condition} )";
        }

        return $query;
    }

    /**
     * Builds the SQL query for the Belongs To relations
     *
     * @param array $relation Data of the relation
     *
     * @return string
     */
    private function _buildBelongsToRelationQuery(array $relation=array(),$conditions="") {
        $property  = $relation["property"];

        $table     = $relation["dstTable"];
        $table     = $this->_prefix.$table;

        $src       = $relation["srcColumn"];
        $dst       = $relation["dstColumn"];

        $condition = "{$dst}='?' ";

        if (!empty($conditions)) {
            $this->where($conditions);
            $query     = "SELECT * FROM {$table} WHERE ( {$condition} ) AND ( {$this->_where} ) LIMIT 1";
        }
        else {
            $query     = "SELECT * FROM {$table} WHERE ( {$condition}) LIMIT 1";
        }


        return $query;
    }

    /**
     * Builds the SQL query for the Has Many And Belongs To
     * relations
     *
     * @param array $relation Data of the relation
     *
     * @return string
     */
    private function _buildHasManyAndBelongsToRelationQuery(array $relation=array(),$conditions="") {

        $property         = $relation["property"];

        $srcTable         = $relation["srcTable"];
        $srcTable         = $this->_prefix.$srcTable;

        $dstTable         = $relation["dstTable"];
        $dstTable         = $this->_prefix.$dstTable;

        $srcColumn        = $relation["srcColumn"];
        $dstColumn        = $relation["dstColumn"];

        $throughTable     = $relation["throughTable"];
        $throughTable     = $this->_prefix.$throughTable;
        $throughSrcColumn = $relation["throughTableSrcColumn"];
        $throughDstColumn = $relation["throughTableDstColumn"];

        if (!empty($conditions)) {
            $this->where($conditions);

            $condition        = " {$srcTable} a INNER JOIN {$throughTable} t INNER JOIN {$dstTable} b ON ( (a.{$srcColumn}=t.{$throughSrcColumn}) ";
            $condition       .= " AND (b.{$dstColumn}=t.{$throughDstColumn}) ) WHERE (a.{$srcColumn}='?' ) AND ( {$this->_where} )";
        }
        else {
            $condition        = " {$srcTable} a INNER JOIN {$throughTable} t INNER JOIN {$dstTable} b ON ( (a.{$srcColumn}=t.{$throughSrcColumn}) ";
            $condition       .= " AND (b.{$dstColumn}=t.{$throughDstColumn}) ) WHERE (a.{$srcColumn}='?')";
        }

        $query            = "SELECT b.* FROM {$condition}";

        return $query;
    }

    /**
     * Builds the SQL query for the Has Many By SQL relations
     *
     * @param array $relation Data of the relation
     *
     * @return string
     */
    private function _buildHasManyBySQLRelationQuery(array $relation=array(),$conditions="") {

        $conditions = array_merge($relation["conditions"],$conditions);

        $property   = $relation["property"];

        $dstTable   = $relation["dstTable"];
        $dstTable   = $this->_prefix.$dstTable;

        $srcColumn  = $relation["srcColumn"];
        $dstColumn  = $relation["dstColumn"];

        $this->where($conditions);
        $condition  = "{$dstColumn}='?' ";

        if (!empty($conditions)) {
            $query      = "SELECT * FROM {$dstTable} WHERE ( {$condition} ) AND ( {$this->_where} )";
        }

        return $query;
    }

    /**
     * Builds the SQL query for the Has Many Through relations
     *
     * @param array $relation Data of the relation
     *
     * @return array
     */
    private function _buildHasManyThroughRelationQuery(array $relation=array(),$conditions="") {
        $query            = array();

        $property         = $relation["property"];

        $srcTable         = $relation["srcTable"];
        $srcTable         = $this->_prefix.$srcTable;

        $dstTable         = $relation["dstTable"];
        $dstTable         = $this->_prefix.$dstTable;

        $srcColumn        = $relation["srcColumn"];
        $dstColumn        = $relation["dstColumn"];

        $throughTable     = $relation["throughTable"];
        $throughTable     = $this->_prefix.$throughTable;
        $throughSrcColumn = $relation["throughTableSrcColumn"];
        $throughDstColumn = $relation["throughTableDstColumn"];

        if (!empty($conditions)) {
            $this->where($conditions);
            $condition        = " {$srcTable} a INNER JOIN {$throughTable} t INNER JOIN {$dstTable} b ON ( (a.{$srcColumn}=t.{$throughSrcColumn}) ";
            $condition       .= " AND (b.{$dstColumn}=t.{$throughDstColumn}) ) WHERE (a.{$srcColumn}='?' ) AND  ( {$this->_where} )";
        }
        else {
            $condition        = " {$srcTable} a INNER JOIN {$throughTable} t INNER JOIN {$dstTable} b ON ( (a.{$srcColumn}=t.{$throughSrcColumn}) ";
            $condition       .= " AND (b.{$dstColumn}=t.{$throughDstColumn}) ) WHERE (a.{$srcColumn}='?')";
        }

        $query          []= "SELECT t.* FROM {$condition} ";
        $query          []= "SELECT b.* FROM {$condition} ";

        return $query;
    }

    /**
     * Builds the SQL query for the Has One Through relations
     *
     * @param array $relation Data of the relation
     *
     * @return array
     */
    private function _buildHasOneThroughRelationQuery(array $relation=array(),$conditions="") {
        $query            = array();

        $property         = $relation["property"];

        $srcTable         = $relation["srcTable"];
        $srcTable         = $this->_prefix.$srcTable;

        $dstTable         = $relation["dstTable"];
        $dstTable         = $this->_prefix.$dstTable;

        $srcColumn        = $relation["srcColumn"];
        $dstColumn        = $relation["dstColumn"];

        $throughTable     = $relation["throughTable"];
        $throughTable     = $this->_prefix.$throughTable;
        $throughSrcColumn = $relation["throughTableSrcColumn"];
        $throughDstColumn = $relation["throughTableDstColumn"];

        if (!empty($conditions)) {
            $this->where($conditions);
            $condition        = " {$srcTable} a INNER JOIN {$throughTable} t INNER JOIN {$dstTable} b ON ( (a.{$srcColumn}=t.{$throughSrcColumn}) ";
            $condition       .= " AND (b.{$dstColumn}=t.{$throughDstColumn}) ) WHERE (a.{$srcColumn}='?') AND ( {$this->_where} )";
        }
        else {
            $condition        = " {$srcTable} a INNER JOIN {$throughTable} t INNER JOIN {$dstTable} b ON ( (a.{$srcColumn}=t.{$throughSrcColumn}) ";
            $condition       .= " AND (b.{$dstColumn}=t.{$throughDstColumn}) ) WHERE (a.{$srcColumn}='?')";
        }

        $query          []= "SELECT t.* FROM {$condition} LIMIT 1";
        $query          []= "SELECT b.* FROM {$condition} LIMIT 1";

        return $query;
    }





    private function _createModelColumnList() {
        $list     = "";
        $columns  = $this->_schema->getModelProperties();
        $list    .= $this->_createColumnList($columns);
        return $list;
    }


    private function _createColumnListWithValues(array $columns=array()) {
        $list = "";
        if (count($columns)) {
            foreach ($columns as $key=>$value) {
                if ($value instanceof FW_ActiveRecord_Relation) {
                    $value = $value->getOldValue();
                }
                if ($value===null) {                    
                    $list    .= " {$key} = null, ";
                }
                else {
                    $list .= " {$key}='{$value}' ,";
                }
            }
        }
        $list = substr($list,0,-2);
        $list = "{$list} ";        
        return $list;
    }

    private function _createColumnList(array $columns=array()) {
        $list = "";
        if (count($columns)) {
            foreach ($columns as $column) {
                $list .= " {$column} ,";
            }
        }
        $list = substr($list,0,-1);
        return $list;
    }

    private function _createValueList(array $columns=array()) {
        $list = "";
        if (count($columns)) {
            foreach ($columns as $key=>$value) {
                if ($value instanceof FW_ActiveRecord_Relation) {
                    $value = $value->getOldValue();
                }
                if ($value===null) {
                    $list .= " null  ,";
                }
                else {
                    $list .= " '{$value}' ,";
                }
            }
        }
        $list = substr($list,0,-1);
        return $list;
    }

    private function _createColumnListWithValuesAnd(array $columns=array()) {
        $list = "";
        if (count($columns)) {
            $i    = 0;
            $max  = count($columns);
            foreach ($columns as $key=>$value) {
                $list .= " {$key}='{$value}' ";
                if ($i<($max-1)) {
                    $list .= " AND ";
                }
                $i++;
            }
        }
        return $list;
    }

    private function _createColumnArrayWithValues(array $columns=array()) {
        $array = array();
        if (!empty($columns)) {
            foreach ($columns as $key=>$value) {
                if ($value instanceof FW_ActiveRecord_Relation) {
                    $value = $value->getOldValue();
                }
                if ($value===null) {
                    $value = NULL;
                }

                $array []= array (
                            "name" 	    => $key,
                            "operator"=> "=",
                            "value"		=> $value
                );
                $array []= array ("condition"=>"AND");
            }
        }
        array_pop($array);
        return $array;
    }


};
?>