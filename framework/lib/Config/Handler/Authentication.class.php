<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Config_Handler_Authentication extends FW_Config_Handler {
     

    /*
     * @see framework/lib/Config/FW_Config_Handler#info()
     */
    public function info() {
        return "AuthenticationConfig";
    }

    /**
     * Gets an authentication rule
     *
     * @param string $name The name of the rule
     * @return array
     */
    public function getAuthenticationRule($name) {
        $rules = "sections.{$name}";        
        if ($this->existsParameter($rules)) {
            return $this->getParameter($rules);
        }
    }

};
?>