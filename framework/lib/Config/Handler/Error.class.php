<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Config_Handler_Error extends FW_Config_Handler {

    public function info() {

    }

    public function getErrorConfiguration($code=404) {
        $customErrors = false;
        $customErrors = $this->getParameter("default","useCustomErrors");
        if ($customErrors==="true") {
            $customErrors = true;
        }
        else {
            $customErrors = false;
        }


        if ($customErrors) {

        }
        else {
            $errorConfig = $this->getParameter("default","errors");


            $module     = $this->getParameter("default","module");
            $controller = $this->getParameter("default","controller");
            $action     = $this->getParameter("default","action");
            $internal   = (bool) $this->getParameter("default","internal");

            $config     = array(
            	"module"     => $module,
                "controller" => $controller,
                "action"	 => $action,
                "internal"	 => $internal        
            );
        }

        return $config;
    }

};
?>