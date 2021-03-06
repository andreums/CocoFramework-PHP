<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class strMinValidator extends FW_Validator_Base {
        
        public function execute(&$value) {            
            if (empty($value)) {
                $this->setResult(new FW_Validator_Result(true,$this->getRequiredMessage()));
                return false;
            }
            $length = $this->get("length");            
            if  (strlen($value)<$length) {
                $this->setResult(new FW_Validator_Result(true,$this->getSuccessMessage()));
                return true;
            }
            $this->setResult(new FW_Validator_Result(true,$this->getErrorMessage()));
            return false;
        }
        
    };
?>