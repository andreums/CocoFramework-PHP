<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class FW_ActiveRecord_Metadata_Manager extends FW_Registry implements IComponent {

    private static $_instance;
    private $_isInitialized;

    private $_models;

    public static function getInstance()   {
        if (!(self::$_instance instanceof self))  {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public function __construct() {
        $this->configure(null);
        $this->initialize(array());
    }

    public function configure(FW_Container_Parameter $parameters=null) {
        $this->clear();
        $this->_models = array();
    }

    public function initialize(array $arguments=array()) {
        if ($this->_existsSerializedData()) {
            $this->_loadSerializedData();
        }
        else {
            $this->_discoverModels();
            $this->_generateModelSchemas();
            $this->_serializeModelSchemas();
        }
    }

    private function _existsSerializedData() {
        $file = BASE_PATH."framework/cache/framework/schemas/serialized_activerecord_manager.ser";
        return (is_file($file));
    }

    private function _loadSerializedData() {
        $file = BASE_PATH."framework/cache/framework/schemas/serialized_activerecord_manager.ser";
        $data = file_get_contents($file);
        if (strlen($data)>0) {
            $data           = unserialize($data);
            $this->clear();
            $this->_objects = $data;
            return true;
        }
        return false;
    }

    private function _generateModelSchemas() {
        $models = $this->_models;
        if (count($models)) {
            foreach ($models as $name=>$file) {
                include_once $file;
                $schema  = new FW_ActiveRecord_Metadata_Schema($name);
                $this->setSchema($name,$schema);
            }
        }
    }

    private function _serializeModelSchemas() {
        $file = BASE_PATH."framework/cache/framework/schemas/serialized_activerecord_manager.ser";
        $data = serialize($this->_objects);
        if (file_put_contents($file,$data)>0) {
            return true;
        }
        return false;
    }


    private function _discoverModels() {
        $models        = array();

        $models        = array_merge($models,$this->_discoverUserModels());
        $models        = array_merge($models,$this->_discoverSystemModels());
        $models        = array_merge($models,$this->_discoverPluginModels());

        $this->_models = $models;
    }

    private function _discoverUserModels() {
        $path   = BASE_PATH."app/lib/models";
        $models = $this->_getModelsInPath($path);
        return $models;
    }

    private function _discoverSystemModels() {
        $path   = BASE_PATH."framework/app/lib/models";
        $models = $this->_getModelsInPath($path);
        return $models;
    }

    private function _discoverPluginModels() {
        $models = array();
        /*$path   = "framework/app/models";
        $models = FW_Plugin_Registry::getInstance()->getPluginModels(); */
        return $models;
    }


    private function _getModelsInPath($path) {
        $models = array();        
        $files  = scandir($path);
        foreach ($files as $file) {
            $filename = "{$path}/{$file}";
            if (!is_dir($filename)) {
                if (substr($file,-10)===".class.php") {
                    $name     = substr($file,0,-10);
                    $models [$name]=$filename;
                }
            }
        }        
        return $models;
    }

    public function hasSchema($name) {
        if ($this->exists($name)) {
            return true;
        }
        else {
            $this->_discoverModels();
            $this->_generateModelSchemas();
            $this->_serializeModelSchemas();
            if ($this->exists($name)) {
                return true;
            }
        }
        return false;

    }

    public function getSchema($name) {
        $data = "";
        if ($this->exists($name)) {
            $data = $this->get($name);
            if ($data===$name) {
                $file   = "framework/cache/framework/schemas/{$name}.ser";
                $data   = file_get_contents($file);
                $data   = unserialize($data);
                $this->setSchema($name,$data);
            }
        }
        return $data;
    }

    public function setSchema($name,$schema) {
        if ($this->exists($name)) {
            return false;
        }
        else {
            $this->set($name,$schema);
            return true;
        }
        return false;
    }

    public function getSchemas() {
        return $this->keys();
    }


    public function __sleep() {
        return parent::__sleep();
    }

    public function __wakeup()  {
        return parent::__wakeup();
    }


};
?>