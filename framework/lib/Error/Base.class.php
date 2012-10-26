<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    abstract class FW_Error_Base {
        
        protected $_code;
        protected $_title;
        protected $_description;
        protected $_causes;
        protected $_actions;
        protected $_template;
        protected $_isUserError;
        protected $_isLoggeable;
        protected $_type;
        
        protected $_templateData;
        protected $_processedTemplate;
        
        public function __construct($user=false,$code,$title,$description,$type="template",$template="default",$causes=array(),$actions=array()) {
            $this->_isUserError = $user;
            $this->_code        = $code;
            $this->_title       = $title;
            $this->_description = $description;
            $this->_type        = $type;
            $this->_template    = $template;
            $this->_causes      = $causes;
            $this->_actions     = $actions;
            $this->_isLoggeable = true;                                    
        }

        
        public function setCode($code) {
            $this->_code = $code;
        }
        
        public function setTitle($title) {
            $this->_title = $title;            
        }
        
        public function setType($type) {
            if ($type==="template" || $type==="text" || $type==="xml" || $type==="json" )  {
                $this->_type = $type;
                return;
            }
            $this->_type = "template";            
        }
        
        public function setDescription($description) {
            $this->_description = $description;
        }
        
        public function setCauses(array $causes) {
            $this->_causes = $causes;
        }
        
        public function setActions(array $actions) {
            $this->_actions = $actions;
        }
        
        public function setTemplate($template) {
            $this->_template = $template;
        }
        
        public function isLoggeable() {
            return ($this->_isLoggeable===true);
        }
        
        public function getType() {
            return $this->_type;           
        }
        
        public function raise() {            
            if ($this->_type==="template") {
                if ($this->_loadTemplate()) {
                    $this->_processTemplate();
                    $this->_displayTemplate();
                }                
            }            
            return $this;               
        }
        
        private function _displayTemplate() {
            if (strlen($this->_processedTemplate)>0) {
                print $this->_processedTemplate;
            }
        }
        
        private function _loadTemplate() {
            $templateFile = "";           
            
                
            if ($this->_isUserError) {
                $templateFile  = "app/lib/error/templates/";
            }
            else {
                $templateFile  = "framework/lib/Error/templates/";                
            }
            
            $templateFile = "{$templateFile}{$this->_template}.php";            
            if (!is_file($templateFile)) {                
               $templateFile = "{$this->_template}.php";                                  
            }            
           
                
            if (is_file($templateFile)) {
                
                ob_start();
                require $templateFile;
                $contents = ob_get_contents();                
                ob_clean();                
                $this->_templateData = $contents;                
                return true;
            }
            else {
                
                // template file doesn't exists, fallback to the default template file
                return false;
            }            
            
        }
        
        
        private function _processTemplate() {
            if (strlen($this->_templateData)>0) {

                $content = $this->_templateData;
                $content = str_replace("{{ERR_CODE}}",$this->_code,$content);
                $content = str_replace("{{ERR_TITLE}}",$this->_title,$content);
                $content = str_replace("{{ERR_DESCRIPTION}}",$this->_description,$content);

                $causes  = "";
                if (!empty($this->_causes)) {
                    foreach ($this->_causes as $cause) {
                        $causes .= "<li> {$cause} </li>";
                    }
                }
                
                $actions  = "";
                if (!empty($this->_actions)) {
                    foreach ($this->_actions as $action) {                        
                        $actions .= "<li> {$action} </li>";
                    }
                }
                 
                $content = str_replace("{{ERR_CAUSES}}",$causes,$content);
                $content = str_replace("{{ERR_ACTIONS}}",$actions,$content);
                $this->_processedTemplate = $content; 
                return true;
            }
            return false;
        }
        
        
        protected function _log($message) {            
            $log = FW_Log::factory("file","error","{$this->_code}.log",false,array("append"=>true));
            $log->open();
            $log->log(new FW_Log_Message($message));
            return $this;            
        }
        
        
    };
?>