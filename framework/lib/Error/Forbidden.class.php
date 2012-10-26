<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Error_Forbidden extends FW_Error_Base {
        
        public function __construct() {
            
            $causes = array (
                _("You <em>haven't got permission</em> to enter this area"),
                _("You have tried to enter in a restricted area <em>without being authorized</em>")
            );                 
            
            $actions = array(
                _("Verify that you have the right credentials to enter this resource or area"),                
                _("Try to login again"),
                _("Notify an error to the <em>webmaster</em>")            
            );
            
            parent::__construct(false,403,_("Access denied"),_("You haven't got valid credentials to enter this resource or area"),"template","defaultForbidden",$causes,$actions);
        }
        
        public function raise() {
            if ($this->_isLoggeable) {
                $url          = "";                
                $ref          = "";
                $data       = FW_Session::getInstance()->get("data","error");
                $request = FW_Request::getInstance();
                if ($data!==null) {
                    $url = $data["url"];  
                    $ref = $data["referrer"];
                    $message = "IP: {$request->getClientIP()}  URI: {$url}  Referrer: {$ref}";
                    $this->_log($message);                  
                }   
                //FW_Session::destroy();             
            }
            parent::raise();
        }
        
    };
?>