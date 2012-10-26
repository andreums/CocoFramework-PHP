<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Error_NotFound extends FW_Error_Base {
        
        public function __construct() {
            
            // $user=false,$code,$title,$description,$type="template",$template="default",$causes=array(),$actions=array()
            $parameters = array (
                "code"        => 404,
                "isUserError" => false,
                "type"        => "template",                  
                "template"    => "defaultNotFound",
                "title"       => _("Resource not found"),
                "description" => _("The resource you have requested doesn't exists  or isn't available now"),
                "causes"      => array (
                    _("The resource you have requested is not available"),
                    _("You have introduced a not existent <em>URL</em>"),
                    _("Your browser has acceded a non existent page")
                ),            
                "actions"     => array(
                    _("Check that the <em>URL</em> is well writen (system distinguishes uppercase and lowercase"),                                
                    _("Go back to ")."<a href=\"".FW_FrontController::getBaseURL()."/index.php\" title=\""._("home page")."\">"._("home page")."</a>"            
                )
            );
            
            parent::__construct(false,404,_("Resource not found"),_("The resource you have requested doesn't exists  or isn't available now"),"template","defaultNotFound",$parameters["causes"],$parameters["actions"]);
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