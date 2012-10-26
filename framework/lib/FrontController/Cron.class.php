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
class FW_FrontController_Cron extends FW_FrontController_Application {

    


    protected function _sendHeaders() {
        $response = new FW_HttpResponse();
        $response->addMimeHeader("text/plain");
        $response->addContentLength($this->_length);
        $response->addNoCacheHeaders();        
        $response->render();        
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