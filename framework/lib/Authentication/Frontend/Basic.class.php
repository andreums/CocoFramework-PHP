<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Authentication_Frontend_Basic extends FW_Authentication_Frontend_Base {

    private $_realm;
    private $_cancel;

    protected function _configure(FW_Container_Parameter $parameters=null) {


        if ($parameters!==null) {

            if ($parameters->realm!==null) {
                $this->_realm  = $parameters->realm;
            }

            if ($parameters->cancel!==null) {
                $this->_cancel = $parameters->cancel;
            }
        }
    }

    public function display() {
        $response = new FW_HttpResponse();
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="'.$this->_realm.'"');
            header('HTTP/1.0 401 Unauthorized');
            print $this->_cancel;
            exit;
        }
        else {
            $username = $_SERVER["PHP_AUTH_USER"];
            $password = $_SERVER["PHP_AUTH_PW"];
            return array("username"=>$username,"password"=>$password);
        }
    }

    public function resetHeaders() {
        unset($_SERVER["PHP_AUTH_USER"]);
        unset($_SERVER["PHP_AUTH_PW"]);
    }

    public function sendHeaders() {}

};
?>