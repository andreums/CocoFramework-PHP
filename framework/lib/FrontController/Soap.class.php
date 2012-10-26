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
class FW_FrontController_Soap extends FW_FrontController_Application {


    public function _sendHeaders() {
        if ( !headers_sent() ) {
            header("Content-Type: text/xml",true);
            if (!$this->_cacheId) {
                header ("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0",true);
                header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
            }
            else {
                header("Cache-Control: public",true);
                $expire = "Expires: ". gmdate("D, d M Y H:i:s", time() + $this->_cacheLifeTime) . " GMT";
                header($expire,true);
            }
        }
        print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    }

    public function render() {
        ob_clean();
        $server = new FW_Soap_Server();
    }

    public function _notFound() {
        print "<error> <code>404</code> <details><![CDATA[Error 404 not found, the action you requested doesn't exists]]></details> </error>";
        trigger_error("Dispatcher | Error: Action {$action} does not exists in controller {$this->_controllerName}",E_USER_WARNING);
    }

};
?>