<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Filter_View extends FW_Filter {

    public function execute (FW_Filter_Chain $filterChain) {
        $context = $this->getContext();        
        $type    = $context->router->route["type"];
        if ($type==="app") {
            
        }
        if ($type==="xml" || $type==="json" || $type==="mime") {
            if ($type==="xml") {
                header("Content-Type: text/xml"); 
            }
            $contents = $context->response->contents;
            if ($contents!==null) {
                print $contents;
            }
        }
        
        $filterChain->execute();        
        return true;
    }
}
?>