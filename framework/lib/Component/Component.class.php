<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Component {

    private $_name;
    private $_module;
    private $_isInternal;

    private $_request;
    private $_model;
    private $_user;
    private $_variables;

    private $_slots;



    public function __construct() {
        $this->_configure();
        $this->_initialize();
        $this->_loadModel();
    }


    private function _configure() {
        $this->_variables = array();
    }

    private function _initialize() {}

    public function __call($method,$arguments) {}

    public function __get($property) {
        if (!isset($this->$property)) {
             
        }
        return $this->$property;
    }

    protected function user() {
        if (!isset($this->_user)) {
            if (FW_Authentication::isLoggedIn()) {
                $this->_user = FW_Authentication::getUser();
            }
            else {
                $this->_user = false;
            }
        }
        return $this->_user;
    }

    protected function request() {
        if (!isset($this->_request)) {
            $this->_request = FW_Request::getInstance();
        }
        return $this->_request;
    }

    protected function model() {

        if (!isset($this->_model)) {
            $this->_loadModel();
        }
        return $this->_model;
    }

    private function _loadModel() {
        $path      = "";
        $module    = $this->_discoverModule();
        $model     = $this->_discoverName()."Model";
        $component = $this->_name;


        try {
            if ($this->_isInternal()) {
                $path = "framework/app/modules/{$module}/component/{$component}/model/{$component}Model.class.php";
            }
            else {
                $path = "app/modules/{$module}/component/{$component}/model/{$component}Model.class.php";
            }


            include_once $path;
            $this->_model = new $model;
        }
        catch (FW_Exception $exception) {
            print $ex->getMessage();
        }

    }


    private function _discoverName() {
        if ($this->_name===null) {
            $reflect     = new ReflectionClass($this);
            $this->_name = substr($reflect->name,0,strpos ($reflect->name,"Component"));
        }
        return $this->_name;
    }

    private function _discoverModule() {
        if ($this->_module===null) {
            $reflect  = new ReflectionClass($this);
            $fileName = $reflect->getFileName();
            $pos      = (strpos($fileName,"modules"));
            $fileName = (substr($fileName,$pos));
            $fileName = explode('/',$fileName);
            $module   = $fileName[1];
            $this->_module = $module;
        }
        return $this->_module;
    }

    protected final function _isInternal() {
        if ($this->_isInternal===null) {
            $reflect = new ReflectionClass($this);
            $fname   = $reflect->getFileName();
            if (strpos($fname,"framework/app/modules")!==false) {
                $this->_isInternal = true;
            }
            $this->_isInternal = false;
        }
        return $this->_isInternal;
    }


    protected function renderView($view) {
        $path       = "";
        $module     = $this->_discoverControllerModule();

        try {
            if ($this->_isInternal()) {
                $path = "framework/app/modules/{$module}/view/{$view}.php";
            }
            else {
                $path = "app/modules/{$module}/view/{$view}.php";
            }
            extract($this->_variables);
            include $path;
        }

        catch (FW_Exception $exception) {
        }
    }

    protected function renderPartialView($view) {
        $this->renderView("{$view}");
    }

    protected function renderError($name) {
        $view = "error/{$name}";
        $this->renderView($view);
        return;
    }


    protected function get($name) {
        if (isset($this->_variables[$name])) {
            return $this->_variables[$name];
        }
    }
    protected function set($name,$value) {
        return $this->_variables[$name] = $value;
    }
    protected function delete($name) {
        if (isset($this->_variables[$name])) {
            unset($this->_variables[$name]);
        }
    }

    protected function redirectToUrl($url) {
        if (!headers_sent()) {
            header("Location: {$url}");
            exit;
        }
    }



    protected function notFound() {
        return FW_Error_Handler::displayNotFoundError();
    }

    protected function forbidden() {
        return FW_Error_Handler::displayForbiddenError();
    }

    protected function escape($data,$stripTags=true) {
        return FW_Util_String::getInstance()->sanitizeInput($data,$stripTags);
    }


};
?>