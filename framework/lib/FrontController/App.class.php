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
 * AppEngine to dispatch PHP
 * PHP Version 5.2
 *
 * @package  Framework
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es>
 * @license  BSD Style
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * Class AppEngine
 *
 * @package  Framework
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es>
 * @license  BSD Style
 * @link     http://www.andresmartinezsoto.es
 *
 */
class FW_FrontController_App extends FW_FrontController_Base  {
    
    protected $_length;

    protected function _sendHeaders() {
        
    }
    protected function _notFound() {
        
    }

    /*
     * Renders an action
     *
     * @return bool
     */
    public function render() {        
        $contents     = '';
        $action       = $this->_route["action"];
        $context      = $this->_getContext();
        $type         = $context->router->type;
        $object       = $context->router->controller;
        $parameters   = $context->router->parameters;
         
        if ($parameters===null) {
            $parameters = array();
        }
        
        if (isset($this->_route["title"])) {
            $context->router->title = $this->_route["title"];
        }
        
        if (isset($this->_route["scripts"])) {            
            $context->router->scripts = $this->_route["scripts"];
        }
        
        if (isset($this->_route["styles"])) {
            $context->router->styles = $this->_route["styles"];
        }
        if (method_exists($object,"beforeRender")) {
            call_user_func(array($object,"beforeRender"));
        }
        //ob_clean();
        ob_start();        
        $contents = call_user_func_array(array($object,$action),$parameters);                    
        if (strlen($contents)===0) {
            $contents .= ob_get_contents();
        }            
        ob_clean();        
        if ($context->view!==null) {
            $params            = new FW_Container_Parameter();
            $params->template  = $context->view->layout;
            $params->slots     = $context->view->slots;
            $params->variables = $context->view->variables;
            
            $layout            = new FW_Layout();
            $layout->configure($params);
            $contents         .=   $layout->getContents();            
        }

        if (method_exists($object,"afterRender")) {
            call_user_func(array($object,"afterRender"));
        }        
        $this->_length = strlen($contents);
        $this->_sendHeaders();
        print $contents;
        
        if ($context->router->route["cache"]!==false) {                        
            if ($context->cache->data===null) {                                
                $this->_setCachedData(json_encode($context->cache->slots));
            }
        }
    }

    


};
?>