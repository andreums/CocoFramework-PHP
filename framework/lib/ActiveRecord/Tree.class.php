<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_ActiveRecord_Tree extends FW_ActiveRecord_Model {

    /**
     * The key that identifies this node
     * @var string
     */
    private $_idColumn;

    /**
     * The foreign key to a parent node
     * @var string
     */
    private $_parentColumn;

    /**
     * An array that specifies the ordern of the
     * sibling nodes
     *
     * @var array
     */
    private $_siblingsOrders;

    public function __construct($data=null,$relations=true,&$parent=null) {
        parent::__construct($data,$relations,$parent);
        $this->_setUpActingAsTree();
    }



    /**
     * Configures the tree metadata
     *
     * @return void
     */
    private function _setUpActingAsTree() {
        $actings                                = $this->_getSchema()->getActings();        
        $this->_idColumn             = $actings["idColumn"];
        $this->_parentColumn   = $actings["parentColumn"];
        if ($actings["siblingsOrders"]!==false) {
            $this->_siblingsOrders = $actings["siblingsOrders"];
        }
        else {
            $this->_siblingsOrders = array();
        }
        return true;
    }

    /**
     * Checks if this node is the root of the tree
     *
     * @return bool
     */
    public function isRoot() {
        $result = false;
        $column = $this->_parentColumn;
        if ($this->$column===null) {
            $result = true;
        }
        return $result;
    }

    /**
     * Checks if this node has parent or is the root
     *
     * @return bool
     */
    public function hasParent() {
        $result = false;
        $column = $this->_parentColumn;

        if ($this->$column!==null) {
            $result = true;
        }
        return $result;
    }

    /**
     * Checks if this node has siblings
     *
     * @return bool
     */
    public function hasSiblings()  {
        $result       = false;
        $children     = null;
        $name         = $this->_getName();
        $parentColumn = $this->_parentColumn;
        $idColumn     = $this->_idColumn;

        if ($this->hasParent()) {
            $parent = $this->getParent();
            if ($parent!==null) {
                $id   = $parent->$idColumn;
                $myId = $this->$idColumn;

                $children   = $name->count('*',array(
                array (
                            "name"     => $parentColumn,
                            "operator" => "=",
                            "value"    => $id
                ),
                array (
                            "name"     => $idColumn,
                            "operator" => "<>",
                            "value"    => $myId
                )
                ));
                if ($children>0) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    /**
     * Checks if this node has children
     *
     * @return bool
     */
    public function hasChildren() {
        $result       = false;

        $name         = $this->_getName();
        $parentColumn = $this->_parentColumn;
        $idColumn     = $this->_idColumn;

        $id           = $this->$idColumn;

        $children     = $name::count('*',array(
        array (
                "name"     => $parentColumn,
                "operator" => "=",
                "value"    => $id
        )
        ));
        if ($children>0) {
            $result = true;
        }
        return $result;
    }

    /**
     * Gets the parent node for this node
     *
     * @return FW_ActiveRecord_Model
     */
    public function getParent() {
        $parent   = null;
        $name     = $this->_getName();
        $column   = $this->_parentColumn;
        $idColumn = $this->_idColumn;

        if ($this->hasParent()) {
            $id     = $this->$column;

            $conditions = array(
                array(
                    "name"     => $idColumn,
                    "operator" => "=",
                    "value"    => $id
                )
            );

            $parent = $name::find($conditions);

            if ($parent->hasResult()) {
                $parent = $parent->first();
            }
            else {
                $parent = null;
            }
        }
        return $parent;
    }

    /**
     * Gets the children nodes of this node
     *
     * @return FW_ActiveRecord_Result
     */
    public function getChildren() {
        $children     = null;

        $name         = $this->_getName();
        $parentColumn = $this->_parentColumn;
        $idColumn     = $this->_idColumn;

        $id           = $this->$idColumn;
        $conditions   = array(
            array (
	        	"name"     => $parentColumn,
	            "operator" => "=",
	            "value"    => $id
            )
        );
        $children     = $name::find($conditions,$this->_siblingsOrders);
        return $children;
    }

    /**
     * Gets the siblings nodes of this node
     *
     * @return FW_ActiveRecord_Result
     */
    public function getSiblings() {
        $siblings                 = null;
        $children               = null;
        $name                    = $this->_getName();
        $parentColumn = $this->_parentColumn;
        $idColumn            = $this->_idColumn;

        if ($this->hasParent()) {
            $parent = $this->getParent();
            if ($parent!==null) {
                $id   = $parent->$idColumn;
                $myId = $this->$idColumn;

                $children   = $name::find(array(
                array (
                            "name"     => $parentColumn,
                            "operator" => "=",
                            "value"    => $id
                ),
                array (
                            "name"     => $idColumn,
                            "operator" => "<>",
                            "value"    => $myId
                )
                ),$this->_siblingsOrders);                
                if ($children->count()) {
                    $siblings = $children;
                }
            }
        }
        return $siblings;
    }

    /**
     * Gets the root of the tree
     *
     * @return FW_ActiveRecord_Model
     */
    public function root() {
        $root     = null;
        $name     = $this->_getName();
        $column   = $this->_parentColumn;
        $idColumn = $this->_idColumn;
        $root = $this;
        if ($this->$column===null) {
            $root = $this;
        }
        else {
            do {
                if (isset($root->$column)) {
                    $id = $root->$column;
                    $aux = $name::find(
                    array(
                    array(
                            "name"     => $idColumn,
                            "operator" => "=",
                            "value"    => $id
                    )
                    )
                    );
                    if ($aux->hasResult()) {
                        $root = $aux;
                    }
                }
                else {
                    break;
                }

            } while ($root!==null);
        }
        return $root;
    }


    public function addChildren(FW_ActiveRecord_Tree $child) {
        $id                            = $this->_idColumn;
        $parent                 = $this->_parentColumn;
        $child->$colum = $this->$id;
        return $child->save();
    }


};
?>