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
 * @author andreu
 *
 */
class FW_ActiveRecord_Pagination {


    /**
     * The name of the model
     * @var string
     */
    private $_model;

    /**
     * The number of model instances to retrieve
     * @var int
     */
    private $_limit;

    /**
     * The offset for the SQL Query
     * @var int
     */
    private $_offset;

    /**
     * An array of orders parameters
     * @var array
     */
    private $_order;

    /**
     * The number of instances of the model in the
     * table of the database
     * @var int
     */
    private $_count;

    /**
     * An array of conditions to find the model
     * @var array
     */
    private $_conditions;

    /**
     * Construct the pagination class
     *
     * @param string $model The name of the model to be used
     * with this class
     *
     * @return void
     */
    public function __construct($model="") {
        $this->_configure($model);
        $this->_initialize();
    }

    /**
     * Configures the component
     *
     * @param string $model The name of the model
     *
     * @return void
     */
    private function _configure($model) {
        if (!empty($model)) {
            $this->_model = $model;
        }
    }

    /**
     * Initializes the component
     *
     * @return void
     */
    private function _initialize() {
        $this->_limit      = false;
        $this->_offset     = false;
        $this->_order      = array();
        $this->_conditions = array();
    }

     
    /**
     * Forces to use the specified model with this component
     *
     * @param string $name The name of the model
     * @return $this
     */
    public function useModel($name) {
        $this->_model = $name;
        return $this;
    }

    /**
     * Sets the find conditions for the Query
     *
     * @param array $conditions An array of conditions
     *
     * @return $this
     */
    public function where(array $conditions=array()) {
        $this->_conditions = $conditions;
        return $this;
    }

     
    /**
     * Sets the order for the Query
     *
     * @param array $order An array specifying the order
     * for the query
     *
     * @return $this
     */
    public function order(array $order=array()) {
        $this->_order = $order;
        return $this;
    }


    /**
     * Sets the limit
     *
     * @param int $limit Limit for the query
     *
     * @return $this
     */
    public function show($limit) {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * Sets the offset
     *
     * @param int $offset Offset for the query
     * @return $this
     */
    public function offset($offset) {
        $this->_offset = $offset;
        return $this;
    }

    /**
     * Queries the database to find the selected
     * model with WHERE, LIMIT and OFFSET clauses
     *
     * @return FW_ActiveRecord_Result
     */
    public function display() {
        $model        = $this->_model;
        $results      = $model::findPaginated($this->_conditions,$this->_order,$this->_limit,$this->_offset);
        $this->_count = $model::count('*');
        return $results;
    }

    /**
     * Gets the number of instances of the model
     * in the database
     *
     * @return int
     */
    public function count() {
        $model = $this->_model;
        if ($this->_count===null) {
            $number     = $model::count();
            $this->_count = $number;
        }
        return $this->_count;
    }



};
?>