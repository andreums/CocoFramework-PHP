<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Filter_Render extends FW_Filter {

    public function execute (FW_Filter_Chain $filterChain) {
                
        FW_Locale::getInstance()->setLocale();
        $context = $this->getContext();        
        if ($context->router!==null) {            
            $route = $context->router->route;
            if ($route!==null) {
                $type       = $context->router->type;
                //TODO: ARREGLAR BUG 404

                if ( in_array($type,array("app","xml","json","mime","cron") ) ) {                    
                    $module     = $route["module"];
                    $controller = $route["controller"];
                    $action     = $route["action"];
                    $internal   = $route["internal"];
                    $class      = $controller."Controller";                    

                    if ($this->_includeController($module,$controller,$internal)) {                        
                        $controllerObj = new $class;                                                
                        if (!$this->_existsAction($controllerObj,$action)) {                            
                            return $this->_forward404Exception($controllerObj,$action);
                        }
                        $context->router->controller = $controllerObj;
                        $context->router->action     = $action;                       
                        
                    }
                    else {                    	                     
                        return $this->_forward404();
                    }
                }

                else if ($type==="plugin") {
                    $plugin     = $route["plugin"];
                    $action     = $route["action"];
                    $registry   = FW_Plugin_Registry::getInstance();
                    $plugin     = $registry->getPlugin($plugin);
                    if (!$this->_existsPluginAction($plugin,$action)) {                        
                        return $this->_forward404();
                    }
                    $context->router->plugin = $plugin;
                    $context->router->action = $action;

                }
                if ($type==="static") {
                    $file       = $route["file"];
                    $file       = BASE_PATH."app/resources/static/{$file}";
                    if (!is_file($file)) {
                        return $this->_forward404();
                    }
                }

                if ($type==="redirect") {
                    $redirect                  = $route["redirect"];
                    $context->router->redirect = $redirect;
                }
                $context->router->type = $type;
            }
            else {                
                return $this->_forward404();
            }
        }
        else {                        
            return $this->_forward404();
        }

        $filterChain->execute();
        return true;
    }

    private function _includeController($module,$controller,$internal) {
        $path       = "";
        $result     = false;
        $controller = "{$controller}Controller";

        if ($internal===false) {
            $path = "app/modules/{$module}";
        }
        else {
            $path = "framework/app/modules/{$module}";
        }

        $file  = BASE_PATH."{$path}/controller/{$controller}.class.php";        
         
        if (is_file($file)) {            
            require_once $file;
            $result = true;
        }
        return $result;
    }

    private function _existsPluginAction($plugin,$action) {
        if (method_exists($plugin,$action)) {
            return true;
        }
        return false;
    }

    private function _existsAction($controller,$action) {
        if (method_exists($controller,$action)) {
            return true;
        }
        return false;
    }
    
    private function _forward404Exception($controllerObj,$action) {
        throw new FW_Filter_Exception("Fatal error: Action {$action} doesn't exists in controller.");
    }

    private function _forward404() {        
        $this->_router = FW_Router::getInstance();
        $notfound      = FW_Config::getInstance()->get("error.sections.404");		
        $route         = $this->_router->toURL($notfound["module"],$notfound["controller"],$notfound["action"],$notfound["internal"]);        
        $param         = new FW_Container_Parameter();
        $param->result = true;
        $param->route  = $route;        
        $param->type   = $route["type"];
		
                
        FW_Context::getInstance()->setParameter("router",$param);
                           
        $type          = ucfirst($this->getContext()->router->type);
        if (strlen($type)===0) {
            $type = "App";
        }            
        $class         = "FW_FrontController_{$type}";        
        $controller    = new $class();
        $controller->render();
    } 
     

};
?>