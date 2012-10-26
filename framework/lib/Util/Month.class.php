<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Util_Month extends FW_Singleton {
        
        public function convertToWords($month) {
            $result = "";
            switch ($month) {
                case 1:
                    $result = _("Enero");
                break;
                
                case 2:
                    $result = _("Febrero");
                break;
                
                case 3:
                    $result = _("Marzo");
                break;
                
                case 4:
                    $result = _("Abril");
                break;
                
                case 5:
                    $result = _("Mayo");
                break;
                
                case 6:
                    $result = _("Junio");
                break;
                
                case 7:
                    $result = _("Julio");
                break;
                
                case 8:
                    $result = _("Agosto");
                break;
                
                case 9:
                    $result = _("Septiembre");
                break;
                
                case 10:
                    $result = _("Octubre");
                break;
                
                case 11:
                    $result = _("Noviembre");
                break;
                
                case 12:
                    $result = _("Diciembre");
                break;                
            };
            return $result;
        }
        
        
    };
?>