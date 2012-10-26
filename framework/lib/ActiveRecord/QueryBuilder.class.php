<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_ActiveRecord_QueryBuilder {

    protected $_name;
    protected $_schema;
    private        $_builder;
    protected $_manager;


    public function __construct($name) {
        $this->_configure($name);
        $this->_initialize();
    }

    private function _configure($name) {
        if (empty($name)) {
            throw new FW_ActiveRecord_Exception("Model name cannot be blank");
        }
        $this->_name = $name;
    }

    private function _initialize() {
        $this->_manager = FW_ActiveRecord_Metadata_Manager::getInstance();
        if ($this->_manager->hasSchema($this->_name)) {
            $this->_schema = $this->_manager->getSchema($this->_name);
        }
        else {
            throw new FW_ActiveRecord_Exception("ActiveRecord Metadata Manager hasn't got an schema for {$this->_name}. Be sure to have a model named {$this->_name} and clean the metadata cache");
        }
        $this->_loadBuilder();
    }

    private function _loadBuilder() {
        $name    = FW_Config::getInstance()->get("database.global.activeRecord.builder");
        if ($name!==null) {
            $name       = "FW_ActiveRecord_QueryBuilder_{$name}";
            $options    = FW_Config::getInstance()->get("database.global.activeRecord.builderOptions");

            $parameters = new FW_Container_Parameter();
            $parameters->setParameter("schema",$this->_schema);
            $parameters->setParameter("name",$this->_name);
            $parameters->setParameter("options",$options);
             
            $builder = new $name;
            $builder->configure($parameters);
            $this->_builder = $builder;
        }
        else {
            throw new FW_ActiveRecord_Exception("Cannot configure an Active Record Query Builder, please check or set the configuration for database.global.activeRecord.builder");
        }
    }


    public function __call($method,$arguments) {
        return call_user_func_array(array($this->_builder,$method),$arguments);
    }
};
?>