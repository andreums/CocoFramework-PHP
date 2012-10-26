<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Config_Handler_Layouts extends FW_Config_Handler {
        
        /* (non-PHPdoc)
         * @see framework/lib/Config/FW_Config_Handler#info()
         */
        public function info() {
            return "LayoutsConfig";
        }
        
        /**
         * Gets a layout configuration for an HMVC action
         * 
         * @param string $module The name of the module
         * @param bool $internal Indicates if the module is internal or external
         * @param string $controller The name of the controller
         * @param string $type The type of the action
         * 
         * @return array
         */
        public function getLayoutFor($module,$internal,$controller,$type) {
            
            $parameter = "layouts.sections.";
            if ($internal===false) {
                $parameter.= "external";
            }
            else {
                $parameter.= "internal";
            }
            $parameter.= ".{$module}";
            
            $parameterGlobal     = "{$parameter}.global.{$type}";
            $parameterController = "{$parameter}.controllers.{$controller}.{$type}";
            
         
            if ($this->existsParameter($parameterGlobal)) {
                return ($this->getParameter($parameterGlobal));
            }
            if ($this->existsParameter($parameterController)) {
                return ($this->getParameter($parameterController));
            }            
        }
        
        /**
         * Gets if is enabled the use of layouts
         * 
         * @return bool
         */
        public function enabled() {
            return ( (bool) $this->getParameter("layouts.global.allowLayouts") );
        }       
        
    };
?> 