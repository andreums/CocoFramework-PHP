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
class FW_FrontController_Plugin extends FW_FrontController_Base {

    private $_length;



    protected function _sendHeaders() {
        $mime = "text/html";
        if (isset($this->_route["mime"])) {
            $mime     = $this->_route["mime"];
        }

        $response = new FW_HttpResponse();
        $response->addMimeHeader($mime);
        $response->addContentLength($this->_length);

        if ($this->_cache===false) {
            $response->addNoCacheHeaders();
        }
        if ($this->_cache!==false) {
            $response->addCacheHeader("public");
            $response->addExpiryHeader($this->_cache->lifetime);
        }
        $response->render();
    }

    public function render() {
        $contents   = "";
        $action     = $this->_route["action"];
        $context    = $this->_getContext();
        $type       = $context->router->type;
        $plugin     = $context->router->plugin;
        $parameters = $context->router->parameters;

        if (isset($this->_route["title"])) {
            $context->router->title = $this->_route["title"];
        }

        if (isset($this->_route["scripts"])) {
            $context->router->scripts = $this->_route["scripts"];
        }

        if (isset($this->_route["styles"])) {
            $context->router->styles = $this->_route["styles"];
        }


        if (method_exists($plugin,"beforeRender")) {
            call_user_func(array($plugin,"beforeRender"));
        }


        if ( ($this->_cache!==false) &&($this->_cache->data!==null) ) {
            $contents = base64_decode($this->_cache->data);
        }
        else {
            ob_start();
            $contents = call_user_func_array(array($plugin,$action),$parameters);
            if (strlen($contents)===0) {
                $contents = ob_get_contents();
            }
            ob_clean();
        }

        if ($context->view!==null) {
            $params            = new FW_Container_Parameter();
            $params->template  = $context->view->layout;
            $params->slots     = $context->view->slots;
            $params->variables = $context->view->variables;

            $template          = new FW_Layout();
            $template->configure($params);
            print $template->getContents();
        }


        if (method_exists($plugin,"afterRender")) {
            call_user_func(array($plugin,"afterRender"));
        }

        $this->_sendHeaders();

        print $contents;

        if ($context->cache!==false) {
            if ($context->cache->data===null) {
                $this->_setCachedData(base64_encode($contents));
            }
        }
    }

    protected function _notFound() {
        if (!headers_sent() && $this->_action=="indexFirst") {
            header("Content-Type: text/html",true);
            header("HTTP/1.0 404 Not Found");
        }
        FW_Error_Handler::displayNotFoundError();
        trigger_error("Dispatcher | Error: Action {$this->_action} does not exists in controller {$this->_controller}",E_USER_WARNING);
    }

};
?>