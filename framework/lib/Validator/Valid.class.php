<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Validator_Valid extends FW_Singleton {

    public function isAlpha($value) {
        return (bool) ( (preg_match('/^\pL++$/uD',$value)) || ctype_alpha($value) );
    }

    public function isAlphaNumeric($value) {
        return (bool) ( (preg_match('/^[\pL\pN]++$/uD',$value)) || ctype_alnum($value) );
    }



    public function isEqual($value,$required) {
        return ($value === $required);
    }
     
    public function hasLength($value,$length) {
        return (strlen($value)===$length);
    }
    
    public function maxLength($value,$length) {
        return (strlen($value)<=$length);
    }
    
    public function minLength($value,$length) {
        return (strlen($value)>=$length);
    }
    
    public function inRange($value,$min,$max) {
        return ( ($value>=$min) && ($value<=$max) );
    }
    
    public function regex($value,$pattern) {
        return (bool) preg_match($pattern,$value);
    }
    
    public function isNumeric($value) {
        return (is_numeric($value));
    }
    
    public function isNull($value) {
        return ($value===null || $value==="null" || $value===NULL || $value==="NULL");
    }
    
    public function isString($value) {
         return is_string((string) $value);
    }

    public function isInteger($value) {
        return (filter_var($value, FILTER_VALIDATE_INT));
    }

    public function isFloat($value) {
        return (filter_var($value,FILTER_VALIDATE_FLOAT));
    }
    
    public function isBoolean($value) {
        return (filter_var($value,FILTER_VALIDATE_BOOLEAN));
    }
    
    public function isNotEmpty($value) {
        if (is_object($value) && ($value instanceof ArrayObject)) {
            $value = $value->getArrayCopy();
        }        
        return !in_array($value,array(null,false,'', array()),true);
    }
    
 /**
     * Checks if a variable is a valid email
     *
     * @param mixed $variable
     * @return boolean
     */
    public function isValidEmail($variable) {
        if (filter_var($variable,FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }


    /**
     * Checks if a variable is a valid url
     *
     * @param mixed $variable
     * @return boolean
     */
    function isValidURL($value) {
        return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $value);
    }

    public function isValidIPV4($value) {
        $pattern = '/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/';
        if (preg_match($pattern,$value,$matches)) {
            return true;
        }
        else {
            return false;
        }
    }


    /**
     * Checks for a valid email and valid email domain
     * @param $email string The email to check
     * @return bool
     */
    public function isValidEmailDomain($email) {

        if(preg_match('/^\w[-.\w]*@(\w[-._\w]*\.[a-zA-Z]{2,}.*)$/', $email, $matches))  {

            if(function_exists('checkdnsrr')) {
                if(checkdnsrr($matches[1] . '.', 'MX')) {
                    return true;
                }
                if(checkdnsrr($matches[1] . '.', 'A')) {
                    return true;
                }
            }
        }
        else{
            return true;
        }
        return false;
    }
    
};
?>