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
class FW_FrontController_Rest extends FW_FrontController_Application {



    public function _sendHeaders() {
        $format   = "";
        $response = new FW_HttpResponse();

        $parameters = $this->_getContext()->router->parameters;
        if (!empty($parameters)) {
            if (isset($parameters["format"])) {
                $format = $parameters["format"];
            }
        }


        if ($format==="xml") {
            $response->addMimeHeader("text/xml");
        }

        else if ($format==="json") {
            $response->addMimeHeader("text/javascript");
        }

        else {
            $mime = "";
            if (isset($this->_route["mime"])) {
                $mime = $this->_route["mime"];
            }
            else {
                $mime = "xml";
            }
            $response->addMimeHeader($mime);
        }

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
        $format     = "xml";
        $mime       = "";
        $action     = $this->_route["action"];
        $context    = $this->_getContext();
        $type       = $context->router->type;
        $object     = $context->router->controller;
        $parameters = $context->router->parameters;

        if (isset($this->_route["mime"])) {
            $mime = $this->_route["mime"];
        }


        if (!empty($parameters)) {
            if (isset($parameters["format"])) {
                $format = $parameters["format"];
            }
        }


        if (method_exists($object,"beforeRender")) {
            call_user_func(array($object,"beforeRender"));
        }
         

        if ( ($this->_cache!==false) &&($this->_cache->data!==null) ) {
            if (empty($mime)) {
                $contents = $this->_cache->data;
            }
            else {
                $contents = base64_decode($this->_cache->data);
            }
        }
        else {
            ob_start();
            $contents = call_user_func_array(array($object,$action),$parameters);
            if (strlen($contents)===0) {
                $contents = ob_get_contents();
            }
            ob_clean();
        }


        if (method_exists($object,"afterRender")) {
            call_user_func(array($object,"afterRender"));
        }

        $this->_sendHeaders();

        if (empty($mime)) {

            if (!is_array($contents)) {
                $contents = array("error"=>"Error 500 internal server error");
            }

            if ($format==="json") {
                print json_encode($contents);
            }
            if ($format==="xml") {

                print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                $xml = FW_Util_ArrayToXML::toXML($contents,"result");
                $xml = (explode('<?xml',$xml));
                $xml = substr($xml[1],33);
                print $xml;
            }
        }
        else {
            print $contents;
        }


        if ($context->cache!==false) {
            if ($context->cache->data===null) {
                if (empty($mime)) {
                    $this->_setCachedData($contents);
                }
                else {
                    $this->_setCachedData(base64_encode($contents));
                }
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