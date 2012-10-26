<?php

/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */ 
class FW_Util_XML {

    public static function XML2Array($xml="",$recursive=false) {
        $newArray = array () ;

        if ( ! $recursive ) {
            $array = simplexml_load_string ($xml,null,LIBXML_NOCDATA) ;
        }
        else {
            $array = $xml ;
        }
        $array = (array) $array ;

        foreach ($array as $key=>$value) {            
            $value = (array) $value ;
            if (isset($value[0])) {
                $newArray [$key]= trim($value[0]);
            }
            else {
                $newArray [$key]= self::XML2Array($value,true);
            }
        }
        return $newArray ;
    }




}
?>