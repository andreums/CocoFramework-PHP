<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Filter_Authentication extends FW_Filter {

    public function execute(FW_Filter_Chain$filterChain) {

        $context = $this->getContext();
        $route   = $context->router->route;
        $type    = $context->router->type;

        if ($type!=="cron") {
            
            if (isset($route["authentication"])) {                
                if($route["authentication"] !== false) {
                    $auth  = $route["authentication"];
                    if (count($auth)) {
                        $roles = $auth;
                    }
                    else {
                        $roles = array();
                    }                    
                    $user  = $context -> getParameter("user");
                                                                                               
                    if($user === null) {                        
                        $this->_forward403();
                        exit ;
                    }
                    else {
                        if(!$user->hasRole($roles)) {
                            $this->_forward403();                            
                            exit ;
                        }
                        else {
                            $context -> authenticated = true;
                        }
                    }
                }
            }
        }

        else {

            if($route["authentication"] !== false) {

                if ($type==="cron") {
                    if ($context->argv!==null) {
                        if (count($context->argv)>2) {
                            if (count($context->argv===4)) {
                                $username = $context->argv[2];
                                $password = $context->argv[3];

                                $_SERVER["PHP_AUTH_USER"] = $username;
                                $_SERVER["PHP_AUTH_PW"]   = $password;
                            }
                        }
                    }

                    $result = $this->_authenticationCron();
                    if ($result===false) {
                        $this->_send403Header();
                        die("Error 403");
                    }
                    else {
                        $context->authenticated = true;
                    }
                }
            }
        }

        $filterChain -> execute();
        return false;
    }


    private function _authenticationRest() {
        if (!isset($_SERVER["PHP_AUTH_USER"]) && !isset($_SERVER["PHP_AUTH_PW"]) ) {
            $params              = new FW_Container_Parameter();
            $params->realm       = "This service needs authentication";
            $params->cancel      = "401 Forbidden";
            $basic               = new FW_Authentication_Frontend_Basic($params);
            $response            = $basic->display();
            if ($response!==null) {
                $username = $response["username"];
                $password = $response["password"];
            }
            else {
                return false;
            }
        }
        else {
            $username = $_SERVER["PHP_AUTH_USER"];
            $password = $_SERVER["PHP_AUTH_PW"];
        }
        $result =  $this->_authenticate($username,$password);
        $roles  =  $this->getContext()->router->route["authentication"];
        if ( ($result===true) && (count($roles)) ) {
            $user = FW_Authentication::getUser();
            if ($user!==null) {
                if ($user->hasRole($roles)) {
                    return true;
                }
                else {
                    return false;
                }
            }
        }
        return $result;
    }

    private function _authenticationCron() {
        if (!isset($_SERVER["PHP_AUTH_USER"]) && !isset($_SERVER["PHP_AUTH_PW"]) ) {
            $params              = new FW_Container_Parameter();
            $params->realm       = "This service needs authentication";
            $params->cancel      = "401 Forbidden";
            $basic               = new FW_Authentication_Frontend_Basic($params);
            $response            = $basic->display();
            if ($response!==null) {
                $username = $response["username"];
                $password = $response["password"];
            }
            else {
                return false;
            }
        }
        else {
            $username = $_SERVER["PHP_AUTH_USER"];
            $password = $_SERVER["PHP_AUTH_PW"];
        }
        $result =  $this->_authenticate($username,$password);
        return $result;
    }



    private function _authenticate($username,$password) {
        $authRules               = $this->getConfig()->get("authentication.global.default");
        $parameters              = new FW_Container_Parameter();
        $parameters->credentials = new FW_Authentication_Credentials($username,$password);
        $parameters->type        = $authRules;
        $parameters->rules       = $authRules;
        $rules                   = new FW_Authentication_Rules($authRules);

        $authentication          = new FW_Authentication($parameters);
        $login                   = $authentication->login();        
        $success                 = $rules->codes->success;
        $error                   = $rules->codes->forbidden;
        if ($login===$success || $login===true) {
            return true;
        }

        return false;
    }

    private function _send403Header() {
        header("HTTP/1.1 403 Forbidden");
    }

    private function _forward403() {        
        $url           = "";
        $error       = $this -> config() -> get("error.sections.403");
        $baseurl  = $this -> config() -> get("core.global.baseURL");        
        $url           = FW_Router::getInstance() -> toURL($error["module"], $error["controller"], $error["action"], $error["internal"]);        
        $url           = "{$baseurl}{$url["url"]}";
        $session  = $this->getSession();
        $data       = array(
            "type"        => 403,
            "url"           => FW_Request::getInstance()->getUrl(),
            "referrer" => FW_Request::getInstance()->getHttpReferrer()
        );        
        $session->set("data",$data,"error");        
        return FW_HttpResponse::redirect($url);
    }



};
?>