<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Filter_Execution extends FW_Filter {

    public function execute (FW_Filter_Chain $filterChain) {

        $context = $this->getContext(); 

        // implementar el código para envíar headers

        $type    = $context->router->type;       

        if ( in_array($type,array("app","xml","json","mime","cron") ) ) {            
            $module     = $context->router->route["module"];
            $internal   = $context->router->route["internal"];
            $controller = $context->router->route["controller"];     
            $object     = $context->router->controller;       
            $action     = $context->router->action;
            $parameters = $context->router->parameters;
                        
            if (method_exists($object,"beforeRender")) {                
                call_user_func(array($object,"beforeRender"));
            }
           
            
            if ($context->cache!==false) {                
                if ($context->cache->data!==null) {                
                    $contents = $context->cache->data;
                }
            }
            else {
                // añadir opción para gzip ...
                if ($type==="app") {
                    ob_start();            
                    call_user_func_array(array($object,$action),$parameters);
                    $contents = ob_get_contents();
                    ob_clean();
                }
                else {
                    $contents = call_user_func_array(array($object,$action),$parameters);
                }
            }
            
            if (method_exists($object,"afterRender")) {
                call_user_func(array($object,"afterRender"));
            }
            $context->response           = new FW_Container_Parameter();
            $context->response->contents = $contents;
        }

        if ($type==="redirect") {
            $url = $context->router->redirect;
            return FW_FrontController::redirect($url);
        }

        $filterChain->execute();
        return true;
    }


};
?>