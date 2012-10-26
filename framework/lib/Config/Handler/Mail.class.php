<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Config_Handler_Mail extends FW_Config_Handler {

    /* (non-PHPdoc)
     * @see framework/lib/Config/FW_Config_Handler#info()
     */
    public function info() {
        return "MailConfig";
    }
    
    public function getAccounts() {
        return array_keys($this->getSections());
    }
    
    public function getDefaultAccount() {
        $default = $this->getParameter("global.defaultAccount");
        $param   = "sections.{$default}";
        return $this->getParameter($param);
    }
    
    public function getAccount($account) {
        $param   = "sections.{$account}";
        if ($this->existsParameter($param)) {
            return $this->getParameter($param);
        }
    }
    
    public function getTemplateDir() {
        $param  = "global.paths.template";
        if ($this->existsParameter($param)) {
            return $this->getParameter($param);
        }
    }
};
?>