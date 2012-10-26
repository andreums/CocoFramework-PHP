<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Util_Url  extends FW_Singleton {

    public static function seoUrl($string) {
        $string  = strtolower($string);
        //$string  = htmlspecialchars_decode($string,ENT_COMPAT,"UTF-8");        
        $find     = array('á', 'é', 'í', 'ó', 'ú', 'ñ','ä','ë','ï','ö','ü','â','ê','î','ô','û','ŷ','ç','ß',',','à','è','ì','ò','ù','à','è','ì','ò','ù','\'');
        $repl     = array('a', 'e', 'i', 'o', 'u', 'n','a','e','i','o','u','a','e','i','o','u','y','c','s','','a','e','i','o','u','a','e','i','o','u','-');
        $string = str_replace($find, $repl, $string);
        $find     = array(' ', '&', '\r\n', '\n', '+');
        $string = str_replace($find, '-', $string);
        $find    = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
        $repl    = array('', '-', '');
        $string = preg_replace($find, $repl, $string);
        return $string;
    }
    
    public static function isValidUrl($string) {
                return (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $string));                
    }

};
?> 