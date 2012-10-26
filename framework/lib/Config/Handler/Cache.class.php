<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Config_Handler_Cache extends FW_Config_Handler {



    /* (non-PHPdoc)
     * @see framework/lib/Config/FW_Config_Handler#info()
     */
    public function info() {
        return "CacheConfig";
    }


    /**
     * Gets a cache configuration
     *
     * @param string $name The name of the configuration
     *
     * @return array
     */
    public function getCache($name) {
        $data   = "";
        if ($this->existsParameter("sections.{$name}")) {
            $data = $this->getParameter("sections.{$name}");
        }
        return $data;
    }


};
?>