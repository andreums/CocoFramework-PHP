<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_mvc_BaseController {

        private $_name;
        private $_module;
        private $_isInternal;

        private $_request;
        private $_model;
        private $_user;
        private $_variables;

        private $_slots;

        protected $_session;
        protected $_context;

        protected $_flash;
        
        protected $_type;

        /**
         * Construct the controller
         *
         * @return void
         */
        public function __construct() {
            $this->_configure();
            $this->_initialize();
        }

        /**
         * Destroys the controller
         *
         * @return void
         */
        public function __destruct() {
            if ( $this->context()->view !== null ) {
                $this->context()->view->variables = $this->_variables;
            }
        }

        /**
         * Configures the controller
         *
         * @return void
         */
        private function _configure() {
            $this->_variables = array();
            $this->_slots     = array();
            $this->_type      = FW_Context::getInstance()->router->type;
        }

        /**
         * Initializes the controller
         *
         * @return void
         */
        private function _initialize() {
            $this->_discoverControllerModule();
            $this->_discoverControllerName();
        }

        /**
         * Obtains the logged user
         *
         * @return FW_Authentication_User
         */
        public function user() {
            if ( !isset($this->_user) ) {
                if ( FW_Authentication::getInstance()->isLoggedIn() ) {
                    $this->_user = FW_Authentication::getUser();
                }
                else {
                    $this->_user = false;
                }
            }
            return $this->_user;
        }

        /**
         * Obtains the request object
         *
         * @return FW_Request
         */
        public function request() {
            if ( !isset($this->_request) ) {
                $this->_request = FW_Request::getInstance();
            }
            return $this->_request;
        }

        /**
         * Obtains the FW_Session object
         *
         * @return FW_Session
         */
        public function session() {
            if ( $this->_session === null ) {
                $this->_session = FW_Session::getInstance();
            }
            return $this->_session;
        }

        /**
         * Acceses to the context
         *
         * @return FW_Context
         */
        protected function context() {
            if ( !isset($this->_context) ) {
                $this->_context = FW_Context::getInstance();
            }
            return $this->_context;
        }

        /**
         * Gives access to the model
         *
         * @return FW_mvc_BaseModel
         */
        protected function model() {
            if ( !isset($this->_model) ) {
                $this->_loadModel();
            }
            return $this->_model;
        }

        /**
         * Loads the model
         *
         * @return void
         */
        private function _loadModel() {
            $path = "";
            $moduleName = $this->_discoverControllerModule();
            $model = $this->_discoverControllerName() . "Model";
            if ( $this->_isInternal() ) {
                $path = BASE_PATH."framework/app/modules/{$moduleName}/model/{$model}.class.php";
            }
            else {
                $path = BASE_PATH."app/modules/{$moduleName}/model/{$model}.class.php";
            }
            if ( is_file($path) ) {
                include_once $path;
                $this->_model = new $model; 
            }
            else {
                throw new FW_mvc_Exception("Can't load the model for this controller");
            }
        }

        /**
         * Discovers the name of this controller
         *
         * @return string
         */
        private function _discoverControllerName() {
            if ( $this->_name === null ) {
                $reflect = new ReflectionClass($this);
                $this->_name = substr($reflect->name, 0, strpos($reflect->name, "Controller"));
            }
            return $this->_name;
        }

        /**
         * Discovers the module of this controller
         *
         * @return string
         */
        private function _discoverControllerModule() {
            if ( $this->_module === null ) {
                $reflect = new ReflectionClass($this);
                $fileName = $reflect->getFileName();
                $pos = (strpos($fileName, "modules"));
                $fileName = (substr($fileName, $pos));
                $fileName = explode('/', $fileName);
                $module = $fileName [1];
                $this->_module = $module;
            }
            return $this->_module;
        }

        /**
         * Checks if this controller is internal
         *
         * @return bool 
         */
        protected final function _isInternal() {
            if ( $this->_isInternal === null ) {
                $reflect = new ReflectionClass($this);
                $fname = $reflect->getFileName();
                                
                if ( strpos($fname, "framework/app/modules") !== false ) {                    
                    $this->_isInternal = true;                    
                }
                else {
                    $this->_isInternal = false;
                }
            }
            return $this->_isInternal;
        }

        protected function renderView($view, array $variables = array()) {

            $path = "";
            $module = $this->_discoverControllerModule();
            
            if ( $this->_isInternal() ) {
                $path = BASE_PATH."framework/app/modules/{$module}/view/{$view}.php";
            }
            else {
                $path = BASE_PATH."app/modules/{$module}/view/{$view}.php";
            }
            if (is_file($path)) {
                extract($variables);                
                include $path;
            }
            else {
                throw new FW_mvc_Exception("Couldn't load view {$view} ");
            }

        }
        
        protected function renderGlobalView($view, array $variables = array()) {
            if ( $this->_isInternal() ) {
                $path = BASE_PATH."framework/app/view/{$view}.php";
            }
            else {
                $path = BASE_PATH."app/view/{$view}.php";
            }
            if ( is_file($path) ) {            
                extract($variables);                
                include $path;
            }           
        }


        protected function get($name) {
            if ( isset($this->_variables [$name]) ) {
                return $this->_variables [$name];
            }
        }

        protected function set($name, $value) {
            return $this->_variables [$name] = $value;
        }

        /**
         * Deletes a variav
         * @param unknown_type $name
         */
        protected function delete($name) {
            if ( isset($this->_variables [$name]) ) {
                unset($this->_variables [$name]);
            }
        }

        /**
         * Redirects to an URL
         *
         * @param data $data The URL to redirect
         *
         * @return void
         */
        protected function redirect($data) {
            if (is_string($data)) {
                if ( !headers_sent() ) {
                    FW_FrontController::redirect($data);
                    exit ;
                }
            }
            if (is_array($data)) {
                $module       = $data["module"];
                $controller  = $data["controller"];
                $action          = $data["action"];
                $internal       = false;
                if (isset($data["internal"])) {
                    $insernal   = $data["internal"];
                }
                $parameters = array();                
                if (isset($data["parameters"])) {
                    $parameters = $data["parameters"];
                }                
            }
        }

        /**
         * Redirects to a not found page
         *
         * @return mixed
         */
        protected function notFound() {
            return FW_Error_Handler::displayNotFoundError();
        }

        /**
         * Redirects to a forbidden page
         *
         * @return mixed
         */
        protected function forbidden() {
            return FW_Error_Handler::displayForbiddenError();
        }

        /**
         * Escapes a text
         *
         * @param string $data The text to convert
         * @param string $stripTags To strip the tags or not
         *
         * @return string
         */
        protected function escape($data, $stripTags = true) {
            return FW_Util_String::getInstance()->sanitizeInput($data, $stripTags);
        }

        /**
         * Sanitizes a text input
         *
         * @param string $data The text to convert
         *
         * @return string
         */
        protected function sanitize($variable,$stripTags=true) {
            $variable = FW_Util_String::getInstance()->sanitize($variable,$stripTags);            
            return $variable;
        }

        /**
         * Encodes a text input
         *
         * TODO: Rewrite in FW_Util_String class
         *
         * @param mixed $data The text to encode
         *
         * @return string
         */
        protected function encode($variable) {
            if ( is_array($variable) ) {
                foreach ( $variable as $key => $value ) {
                    $variable [$key] = htmlentities($value, ENT_COMPAT, "UTF-8");
                }
            }
            else {
                $variable = htmlentities($variable, ENT_COMPAT, "UTF-8");
            }
            return $variable;
        }

        /**
         * Decodes a text input
         *
         * TODO: Rewrite in FW_Util_String class
         *
         * @param mixed $data The text to decode
         *
         * @return string
         */
        protected function decode($variable) {
            if ( is_array($variable) ) {
                foreach ( $variable as $key => $value ) {
                    $variable [$key] = html_entity_decode($value, ENT_COMPAT, "UTF-8");
                }
            }
            else {
                $variable = html_entity_decode($variable, ENT_COMPAT, "UTF-8");
            }
            return $variable;
        }

        /**
         * Converts a text for display
         *
         * @param string $data The text to convert
         *
         * @return string
         */
        protected function display($data) {
            return FW_Util_String::getInstance()->convertForOutput($data);
        }

        /**
         * Sets the view data in the context
         *
         * @return void
         */
        private function _createViewContext() {
            $this->context()->view = new FW_Container_Parameter();
        }

        /**
         * Renders a layout
         *
         * @param string $layout The name of the layout
         *
         * @return void
         */
        protected function renderLayout($layout) {
            $path = "";
            $module = $this->_discoverControllerModule();

            if ( $this->_isInternal() ) {
                $path = BASE_PATH."framework/app/modules/{$module}/layout/{$layout}.php";
            }
            else {
                $path = BASE_PATH."app/modules/{$module}/layout/{$layout}.php";
            }
            if ( is_file($path) ) {
                if ( $this->context()->view === null ) {
                    $this->_createViewContext();
                }
                $this->context()->view->layout = $path;
                $this->context()->view->slots = $this->_slots;
            }
            else {
                throw new FW_mvc_Exception("Can't load layout {$layout} to render!");
            }

        }

        /**
         * Renders a global  layout
         *
         * @param string $layout The name of the layout
         *
         * @return void
         */
        protected function renderGlobalLayout($layout) {
            $path = "";

            if ( $this->_isInternal() ) {
                $path = BASE_PATH."framework/app/layout/{$layout}.php";
            }
            else {
                $path = BASE_PATH."app/layout/{$layout}.php";
            }
            if ( is_file($path) ) {
                if ( $this->context()->view === null ) {
                    $this->_createViewContext();
                }
                $this->context()->view->layout = $path;
                $this->context()->view->slots    = $this->_slots;				                
            }
            else {
                throw new FW_mvc_Exception("Can't load layout {$layout} to render!");
            }
        }

        /**
         * Sets the slot
         *
         * @param string $name The name of the slot
         * @param string $view The name of the view
         * @param array $variables an array of variables
         *
         * @return void
         */
        protected function setSlot($name, $view, array $variables = array(),$global=false,$cacheable=false) {
            $this->_slots [$name] = array(
                "view"             => $view,
                "variables"    => $variables,
                "global"         => $global,
                "cacheable" => $cacheable
            );
            
        }

        /**
         * Gets the contents of a slot
         *
         * @param string $name The name of the slot
         *
         * @return string
         */
        protected function getSlot($name) {
            $contents = "";
            if ( $this->hasSlot($name) ) {
                ob_start();
                $this->renderView($this->_slots [$name]);
                $contents = ob_get_contents();
                ob_clean();                
            }
            if ($name==="content") {
                $this->_context()->cache->contents = $contents;
            }
            return $contents;
        }

        /**
         * Checks if a slot is defined
         *
         * @param string $name The name of the slot
         *
         * @return bool
         */
        protected function hasSlot($name) {
            if ( $this->_slots [$name] !== null ) {
                return true;
            }
            return false;
        }

        /* to be refactorized and used in a futuro
         /* protected function getComponent($component,$module="") {
         $path  = "";
         $class = "{$component}Component";
         if(empty($module)) {
         $module     = $this->_discoverControllerModule();
         }

         try {
         if ($this->_isInternal()) {
         $path =
        "framework/app/modules/{$module}/component/{$component}/component/{$component}Component.class.php";
         }
         else {
         $path =
        "app/modules/{$module}/component/{$component}/component/{$component}Component.class.php";
         }

         include $path;
         return new $class();
         }
         catch (FW_Exception $exception) { }
         } */

        /**
         *  Gets the application base url
         *
         *
         * @return string
         */
        public function getBaseURL() {
            $url = FW_Config::getInstance()->get("core.global.baseURL");
            return $url;
        }
        
        public function getUrl() {
            return $this->request()->getFullURL();
        }

        /**
         *  Gets the  Flash messages
         *
         * @return string
         */
        public function flash() {
            if ( $this->_flash === null ) {
                $this->_flash = FW_Flash::getInstance();
            }
            return $this->_flash;
        }

        protected function setBreadcrumb(array $replacements = array(),array $url=array()) {
            if ($this->_type==="app") {                        
                $breadcrumb = FW_BreadCrumb::getInstance()->getBreadCrumb($replacements,$url);            
                $this->setSlot("breadcrumb", "common/breadcrumb", array("breadcrumb" => $breadcrumb),true,false);
            }
        }

    };
?>