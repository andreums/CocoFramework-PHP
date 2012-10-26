<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_BreadCrumb extends FW_Singleton {

        private $_url;
        private $_context;
        private $_config;
        private $_home;
        private $_locale;
        private $_request;
        private $_filename;
        private $_homeText;
        private $_replacements;
        private $_textTransform;
        private $_routeParameters;
        private $_routeInformation;

        public function __construct() {
            $this->_setUp();
        }

        private function _context() {
            if ( $this->_context === null ) {
                $this->_context = FW_Context::getInstance();
            }
            return $this->_context;
        }

        private function _config() {
            if ( $this->_config === null ) {
                $this->_config = FW_Config::getInstance();
            }
            return $this->_config;
        }
        
        private function _locale() {        
            if ($this->_locale===null) {
                $this->_locale = FW_Locale::getInstance();
            }
            return $this->_locale;
        }
        
        private function _request() {
            if ($this->_request===null) {
                $this->_request = FW_Request::getInstance();
            }
            return $this->_request;
        }

        private function _setUp() {
            $this->_home             = $this->_config()->get("core.global.baseURL");
            $this->_homeText         = $this->_config()->get("core.sections.breadcrumbs.homeText");
            $this->_textTransform    = $this->_config()->get("core.sections.breadcrumbs.textTransform");
            $this->_routeInformation = $this->_context()->router->route;
            $this->_routeParameters  = $this->_context()->router->parameters;            
            $this->_filename         = $this->_getBreadCrumbDescriptionFile();
            
        }

        private function _getBreadCrumbDescriptionFile() {
            $filename = "";
            $internal = $this->_routeInformation ["internal"];
            $module = $this->_routeInformation ["module"];

            if ( $internal ) {
                $filename = "framework/app/modules/";
            }
            else {
                $filename = "app/modules/";
            }
            $filename .= "{$module}/config/breadcrumbs.php";
            $filename = BASE_PATH.$filename;             
            return $filename;
        }

        private function _searchBreadCrumb() {
            $data         = null;            
            $breadcrumbs  = null;
            $controller   = $this->_routeInformation["controller"];
            $action       = $this->_routeInformation["action"];            
            
            if ( !is_file($this->_filename) ) {
                throw new FW_Exception("Breadcrumb description file ({$this->_filename}) not found!");
            }
            else {
                require($this->_filename);                
                if (isset($breadcrumbs["controllers"][$controller][$action])) {
                    $data = $breadcrumbs["controllers"][$controller][$action];                    
                    $data = $this->_processBreadCrumb($data);
                    
                } 
            }
            return $data;            
        }
        
        private function _processBreadCrumb($breadcrumb) {
            $processedBreadcrumb = array();
            if (count($breadcrumb)>0) {
                foreach ($breadcrumb as $element) {
                        
                    if ($element["link"]==="this") {
                        $element["link"] = $this->_request()->getFullURL();
                    } 
                    
                    if ($element["link"]!=="this") {                                                                        
                        if (strpos($element["link"],"mvc(")!==false) {
                            $link = $element["link"];
                            $link = substr($link,4,-1);                                                                                                                 
                            $link = explode(',',$link);
                            if (count($link)>2) {                                
                                
                                $module      = $link[0];
                                $controller = $link[1];
                                $action         = $link[2];           
                                
                                
                                if ($this->_url!==null && count($this->_url)>0) {
                                    $link = html::link_for_internal($module, $controller, $action,$this->_url);
                                }
                                else {
                                    $link = html::link_for_internal($module, $controller, $action);
                                }
                                
                                $baseURL = FW_Config::getInstance()->get("core.global.baseURL");
                                if ($link===$baseURL) {
                                    $link = html::link_for_internal($module, $controller, $action);
                                }
                                                             
                                $element["link"] = $link;
                            }                            
                        }
                    }
                    
                     if (isset($element["option"])) {
                    
                        if ($element["option"]==="translate") {
                            $locale                       = (isset($element["arguments"]["locale"])? $element["arguments"]["locale"] : null);
                            $element["text"] = $this->_translateBreadCrumbElement($element["text"],$locale);
                        }
                        
                        if ($element["option"]==="parameter") {
                            $method       = $element["arguments"]["method"];
                            $parameter  = $element["arguments"]["parameter"];
                            $value            = $this->_getParameter($parameter,$method);
                            if ($value!==false) {
                                $element["text"] = $value;
                            }                        
                        }
                        
                        if ($element["option"]==="replace") {
                            $parameter = $element["arguments"]["parameter"];                            
                            if (isset($this->_replacements[$parameter])) {
                                $element["text"] = $this->_replacements[$parameter];                                
                            }                           
                        }                   
                    }
                    $element["text"] = $this->_transformBreadCrumbElement($element["text"]);                
                    $processedBreadcrumb []= $element;                                  
                }
            }                            
            return $processedBreadcrumb;
        }
        
        private function _translateBreadCrumbElement($text,$locale=null) {
            $translated = "";            
            if ($locale!==null) {
                $this->_locale()->setLocale($locale);
            }            
            $translated = $this->_locale()->translate($text);            
            return $translated;                        
        }
        
        private function _getParameter($name,$method=null) {
            $return = "";
            
            if ($method===null) {
                if (isset($this->_routeParameters[$name])) {
                    $return = $this->_routeParameters[$name];
                }
            }
            else if ($method==="GET") {
                $value = $this->_request->get($name);
                if ($value!==null) {
                    $return = $value;
                }
            }
            else if ($method==="POST") {
                $value = $this->_request->post($name);
                if ($value!==null) {
                    $return = $value;
                }
            }
            $return = urldecode($return);
            return $return;
        }
        
        private function _transformBreadCrumbElement($text) {
            $transformed = $text;              
            if ($this->_textTransform==="uppercase") {
                $transformed = strtoupper($text);
            }
            else if ($this->_textTransform==="lowercase") {
                $transformed = strtolower($text);
            }
            else if ($this->_textTransform==="words") {
                $transformed = ucwords($text);                
            }
            return $transformed;
        }
        
        private function _getHomeElement() {
            $baseURL = FW_Context::getInstance()->getParameter("baseURL");
            $title         = FW_Config::getInstance()->get("core.global.title");
            $code        ="<li> <a href=\"{$baseURL}\"  alt=\"{$title}\" title=\"{$title}\">{$title}</a> </li>";
            return $code;
        }
        
        private function _getElementCode($element) {
            $link      = $element["link"];
            $text     = $element["text"];
            $code   ="<li> <a href=\"{$link}\"  alt=\"{$text}\" title=\"{$text}\">{$text}</a> </li>";
            return $code;
        }        
        
        public function getBreadCrumb(array $replacements=array(),array $url=array()) {                        
            if (count($replacements)) {                 
                $this->_replacements = $replacements;                
            }           
            if (count($url)) {
                $this->_url = $url;
            }
            $breadcrumb = $this->_searchBreadCrumb();
            $code       = "<div id=\"breadCrumb\"  class=\"breadCrumb\"> <ul>".$this->_getHomeElement();
            
            if ($breadcrumb!==null) {
                foreach ($breadcrumb as $element) {
                    $code .= $this->_getElementCode($element);
                }
            }            
            $code         .= "</ul> </div>";                        
            return $code;
        }
    };
?>