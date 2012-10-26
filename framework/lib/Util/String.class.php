<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Util_String  {
    
    private static $_instance;
    
    public static function getInstance() {
        if (self::$_instance===null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() { }


    /**
     * Encodes a variable into UTF-8 charset
     *
     * @param $variable The variable to encode
     * @return string
     */
    public function encodeUTF8($variable) {
        return utf8_encode($variable);
    }

    /**
     * Decodes a variable into UTF-8 charset
     *
     * @param $variable The variable to decode
     * @return string
     */
    public function decodeUTF8($variable) {
        return utf8_decode($variable);
    }


    /**
     * Converts to htmlentities the variable
     *
     * @param mixed $variable The variable to convert
     * @return string
     */
    public function encodeHTML($variable) {
        return htmlentities($variable, ENT_QUOTES,'UTF-8');
    }

    /**
     * Converts to htmlspecialchars the variable
     *
     * @param mixed $variable The variable to convert
     * @return string
     */
    public function HTMLSpecialCharacters($variable) {
        return htmlspecialchars($variable, ENT_QUOTES,'UTF-8');
    }


    /**
     * Decodes an html string
     *
     * @param $variable The variable to convert
     * @return string
     */
    public function decodeHTML($variable) {
        return html_entity_decode($variable, ENT_QUOTES,'UTF-8');
    }


    /**
     * Adds slashes to the variable
     *
     * @param  string $variable The variable
     * @return string
     */
    public function addSlashes($variable) {
        return addslashes((string) $variable);
    }


    /**
     * Strip the slashes from the variable
     *
     * @param string $variable The variable
     * @return string
     */
    public function stripSlashes($variable) {
        return stripslashes((string) $variable);
    }

    /**
     * Strips html tags from the variable
     *
     * @param string $variable The variable
     * @return string
     */
    public function stripTags($variable) {
        return strip_tags((string) $variable);
    }

    /**
     * Decodes an urlencoded parameter
     *
     * @param $variable string The variable to decode
     * @return string
     */
    public function decodeURL($variable) {
        return urldecode($variable);
    }

    /**
     * Encodes a parameter into urlencode style
     *
     * @param $variable string The variable to decode
     * @return string
     */
    public function encodeURL($variable) {
        return urlencode($variable);
    }


    /**
     * Sanitizes an string input
     *
     * @param $variable string The variable to sanitize
     * @param $stripTags bool True to strip html tags
     * @return string
     */
    public function sanitizeInput($variable,$stripTags=true) {

        $variable   = $this->decodeUrl($variable);
        $variable   = $this->encodeUTF8($variable);
        $variable   = $this->encodeHTML($variable);

        if ($stripTags) {
            $variable   = $this->stripTags($variable);
            $variable   = $this->stripSlashes($variable);
        }
        return $variable;
    }

    public function sanitizeString($variable,$encodeUTF=false) {
        $variable   = $this->stripTags($variable);
        $variable   = $this->stripSlashes($variable);
        if ($encodeUTF) {
            $variable   = $this->decodeUTF8($variable);
        }
        return $variable;
    }


    public function convertForOutput($variable) {
        $variable   = $this->encodeUTF8($variable);
        $variable   = $this->decodeHTML($variable);
        return $variable;
    }


    public function lower($variable) {
        return $this->lowercase($variable);
    }

    public function lowercase($variable) {
        return strtolower($variable);
    }

    public function upper($variable) {
        return $this->uppercase($variable);
    }

    public function uppercase($variable) {
        return strtoupper($variable);
    }

    public function sanitize($variable,$stripTags=true)   {        
        if (is_array($variable)) {
            $output = array();
            foreach ($variable as $key=>$value) {
                $output[$key] = $this->sanitize($value,$stripTags);
            }            
            return $output;
        }
        else {
            $variable  = str_replace("scriptaccess","taccess",$variable);                        
            $variable  = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/','',$variable);            
            $search     = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()~`";:?+/={}[]-_|\'\\';
            for ($i=0;$i<strlen($search);$i++)   {
                $variable = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $variable);
                $variable = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $variable);                                
            }
            $keywords = array('javascript', 'vbscript', 'expression', 'applet', 'xml', 'blink', 'script', 
             'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'base','onabort', 'onactivate', 'onafterprint', 'onafterupdate',
             'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload',
             'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 
             'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave',
             'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp',
            'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove',
             'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 
             'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 
             'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
            
            $found = true;
            $count = count($keywords);
            while ($found === true)  {
                $aux = $variable;
                for ($i=0;$i<$count;$i++) {
                    $pattern = '/';
                    $len          = strlen($keywords[$i]);
                    if ($len>0) {
                        for ($j=0;$j<$len;$j++) {
                            $pattern .= '(';
                            $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                            $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                            $pattern .= ')?';                        
                            $pattern .= $keywords[$i][$j];
                        }
                    }
                    $pattern    .= '/i';                    
                    $replacement = substr($keywords[$i],0,2).'<x>'.substr($keywords[$i],2);
                    $variable    = preg_replace($pattern,$replacement,$variable);
                    if ($aux===$variable) {
                        $found = false;
                    }
                }                
            }
            $variable = str_replace("taccess","scriptaccess",$variable);
        }        
        
        if ($stripTags===true) {
            $variable = strip_tags($variable);
        }        
        $variable = htmlentities($variable,ENT_QUOTES,"UTF-8");        
        return $variable;        
    }

    public function isValidEmail($email){
       if (filter_var($email, FILTER_VALIDATE_EMAIL)!==false) {
           return true;
       }
       return false;            
    }
    
    public function isValidURL($url) {
        if (filter_var($url,FILTER_VALIDATE_URL)!==false) {
            return true;
        }
        return false;
    }

};
?>