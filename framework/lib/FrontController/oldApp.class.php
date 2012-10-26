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


    protected static $_loaders;
    protected $_title;

    
    protected function _sendHeaders() {        
        if ( $this->_action=="indexFirst") {
            $response = new FW_HttpResponse();
            $response->addMimeHeader("text/html");

            if ($this->_cacheId===false) {
                $response->addNoCacheHeaders();
            }
            if ($this->_cacheId!==false) {
                $response->addCacheHeader("public");
                $response->addExpiryHeader($this->_cacheLifeTime);
            }
            $response->render();
        }
    }

    /*
     * Renders an action
     *
     * @return bool
     */
    public function render() {
                
        /*if (self::$_loaders===null) {
            FW_loaders::loadUserHelpers();
            FW_loaders::loadUserWidgets();
            self::$_loaders = true;
        }*/

        $chachedData   = false;
        $controller    = "{$this->_controller}Controller";
        $controller    = new $controller();
        $action        = $this->_action;
         
        if ( method_exists($controller,$this->_action) ) {
            $this->_sendHeaders();

            if ( method_exists($controller,"beforeDispatch")) {
                if (!$controller->beforeDispatch()) {
                    return false;
                }
            }

            if ( ($this->_action!="indexFirst"  && $this->_action!="indexLast") && $this->_cache!==false) {
                $cachedData = $this->_getCachedData();

                if ($cachedData!=null && !$cachedData->hasExpired()) {
                    print html_entity_decode($cachedData->value,ENT_QUOTES,"UTF-8");
                }
                else {
                    ob_start();
                    call_user_func_array(array($controller,$this->_action),$this->_parameters);
                    $contents = ob_get_contents();
                    ob_clean();
                    print $contents;
                    $contents = htmlentities($contents,ENT_QUOTES,"UTF-8");
                    $this->_setCachedData($contents);
                }
            }
            else {
                call_user_func_array(array($controller,$action),$this->_parameters);
            }


            if ( method_exists($controller,"afterDispatch")) {
                if (!$controller->afterDispatch()) {
                    return false;
                }
            }
        }

        else {
            $this->_notFound();
        }
    }

    public function _notFound() {
        if (!headers_sent() && $this->_action=="indexFirst") {
            header("Content-Type: text/html",true);
            header("HTTP/1.0 404 Not Found");
        }
        FW_Error_Handler::displayNotFoundError();
        trigger_error("Dispatcher | Error: Action {$this->_action} does not exists in controller {$this->_controller}",E_USER_WARNING);
    }


    public static function getTitle() {
        $title = FW_Router::getTitle();
        $titleOriginal = "";
        $pos=strpos($title,"param:");
        if ($pos!==false) {
            $titleOriginal = substr($title,0,$pos);
            $titleSlice = substr($title,$pos);
            $titleExp = explode(":",$titleSlice);
            if (count($titleExp)>1) {
                $paramName = $titleExp[1];
                $paramName = trim($paramName);
                $paramValue = Request::getParameter($paramName);
                $paramValue = urldecode($paramValue);
                $title = "{$titleOriginal}{$paramValue}";
            }
        }
        return $title;
    }
};
?>