<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    /**
     * Form helper
     * @author andreu
     *
     */
    class form {

        /**
         * Generates a form tag
         *
         * @param string $id The id for the form
         * @param string $action The action of the form
         * @param string $method The method of the form (post/get)
         * @param string $enctype The encoding of the form
         * @param string $class The class name of the form
         * @param string $style The style of the form
         *
         * @return string
         */
        public static function formTag($id, $action, $method, $enctype = "", $class = "", $style = "") {
            $code = "<form id=\"{$id}\" action=\"{$action}\"";
            $method = strtolower($method);

            if ( !in_array($method, array(
                "get",
                "post"
            )) ) {
                throw new FW_Exception("Form method can't be {$method}");
            }
            else {
                $code .= " method=\"{$method}\"";
            }

            if ( !empty($enctype) ) {
                $code .= " enctype=\"{$enctype}\" ";
            }

            if ( !empty($class) ) {
                $code .= " class=\"{$class}\" ";
            }

            if ( !empty($style) ) {
                $code .= " style=\"{$style}\" ";
            }

            $code .= " >\n";

            $token = self::generateFormToken($id);

            $code .= self::hiddenInput("sectoken", $token) . "\n";

            return $code;
        }

        /**
         * Generates a fieldset tag
         *
         * @return string
         */
        public static function openFieldsetTag() {
            return "<fieldset>";
        }

        /**
         * Generates a fieldset closing tag
         *
         * @return string
         */
        public static function closeFieldsetTag() {
            return "</fieldset>";
        }

        /**
         * Generates a form closing tag
         *
         * @return string
         */
        public static function closeFormTag() {
            return "</form>";
        }

        /**
         * Generates a textarea tag
         *
         * @param string $id The id of the textarea
         * @param string $value The value of the textarea
         * @param string $rows The number of rows of the textarea
         * @param string $cols The number of columns of the textarea
         * @param string $class The class name of the textarea
         * @param string $style The style of the textarea
         *
         * @return string
         */
        public static function textArea($id, $value = "", $rows = 0, $cols = 0, array $options = array(), $class = "", $style = "") {
            $code = "<textarea id=\"{$id}\" name=\"{$id}\" ";
            if ( $rows !== 0 ) {
                $code .= " rows=\"{$rows}\" ";
            }
            if ( $cols !== 0 ) {
                $code .= " cols=\"{$cols}\" ";
            }

            if ( !empty($class) ) {
                $code .= " class=\"{$class}\" ";
            }

            if ( !empty($style) ) {
                $code .= " style=\"{$style}\" ";
            }

            if ( isset($options ["disabled"]) && $options ["disabled"] !== false ) {
                $code .= "disabled=\"true\"";
            }

            $code .= ">";
            if ( !empty($value) ) {
                $code .= $value;
            }
            $code .= "</textarea>";
            return $code;
        }

        /**
         * Generates a label tag
         *
         * @param string $for The id of the element to label
         * @param string $label The text of the label
         *
         * @return string
         */
        public static function labelTag($for, $label) {
            $code = "<label for=\"{$for}\">{$label}</label>";
            return $code;
        }

        /**
         * Generates a lengend tag
         *
         * @param string $value The value of the legend
         *
         * @return string
         */
        public static function legendTag($value) {
            $code = "<legend>{$value}</legend>";
            return $code;
        }

        /**
         * Generates a select tag
         *
         * @param string $id The id of the select tag
         * @param array $options An array of options
         * @param bool $multiple Multiple option selecting
         * @param bool $disabled Disabled select
         * @param string $class The class name of the select
         * @param string $style The style of the select
         *
         *
         * @return string
         */
        public static function selectTag($id, array $options = array(), $multiple = false, $disabled = false, $class = "", $style = "") {
            $code = "<select id=\"{$id}\" name=\"{$id}\" ";
            if ( $multiple ) {
                $code .= " multiple=\"multiple\" ";
            }
            if ( $disabled ) {
                $code .= " disabled=\"disabled\" ";
            }
            if ( !empty($class) ) {
                $code .= " class=\"{$class}\" ";
            }
            if ( !empty($style) ) {
                $code .= " style=\"{$style}\" ";
            }
            $code .= ">";

            if ( count($options) > 0 ) {                
                $code .= self::generateOptionsForSelect($options);
            }

            $code .= "</select>";
            return $code;
        }

        /**
         * Generates optgroups and options tags
         *
         * @param array $options An array of options
         *
         * @return string
         */
        public static function generateOptionsForSelect(array $options = array()) {
            $code = "";

            foreach ( $options as $key => $value ) {

                $disabled = false;
                if ( is_array($value) && isset($value ["group"]) ) {
                    $disabled = isset($value ["disabled"]);
                    $code .= "<optgroup label=\"{$key}\" ";
                    if ( $disabled ) {
                        $code .= " disabled=\"disabled\" ";
                    }
                    $code .= ">";
                    $code .= self::generateSelectOptions($value ["options"]);
                    $code .= "</optgroup>";
                }
                else {
                    $disabled = false;
                    $selected = false;

                    if ( is_array($value) ) {
                        $disabled = ((isset($value ["disabled"])) && ($value ["disabled"] === true));
                        $selected = ((isset($value ["selected"])) && ($value ["selected"] === true));
                        if ( isset($value ["key"]) ) {
                            $key = $value ["key"];
                        }
                        $value = $value ["value"];

                    }

                    $code .= "<option value=\"{$key}\" ";

                    if ( $disabled === true ) {
                        $code .= " disabled=\"disabled\" ";
                    }
                    if ( $selected === true ) {
                        $code .= " selected=\"selected\" ";
                    }

                    $code .= ">{$value}</option>";
                }
            }
            return $code;
        }

        /**
         * Generates options tags
         *
         * @param array $options An array of options
         *
         * @return string
         */
        public static function generateSelectOptions(array $options = array()) {
            $code = "";
            foreach ( $options as $key => $value ) {

                $disabled = false;
                $selected = false;

                if ( is_array($value) ) {
                    $disabled = ((isset($value ["disabled"])) && ($value ["disabled"] === true));
                    $selected = ((isset($value ["selected"])) && ($value ["selected"] === true));
                    if ( $kkey !== false ) {
                        $key = $kkey;
                    }
                    $value = $value ["value"];
                }

                $code .= "<option value=\"{$key}\" ";

                if ( $disabled === true ) {
                    $code .= " disabled=\"disabled\" ";
                }
                if ( $selected === true ) {
                    $code .= " selected=\"selected\" ";
                }

                $code .= ">{$value}</option>";
            }
            return $code;
        }

        /**
         * Crates an input tag
         *
         * @param string $id The id for the input
         * @param string $value The value for the input
         * @param array $options An array of options
         * @param string $class The CSS class for the input
         * @param string $style The CSS style for the input
         *
         * @return string
         */
        public static function inputTag($id, $type, $value = "", array $options = array(), $class = "", $style = "") {            
            $code = "<input type=\"{$type}\" id=\"{$id}\" name=\"{$id}\" ";
            if ( !empty($value) ) {
                $code .= " value=\"{$value}\" ";
            }

            if ( $type === "checkbox" || $type === "radio" ) {                
                if ( isset($options ["checked"]) && ($options ["checked"] === true) ) {
                    $code .= " checked=\"checked\" ";
                }                
            }

            if ( $type === "file" ) {
                if ( isset($options ["accept"]) && (!empty($options ["accept"])) ) {
                    $code .= " accept=\"{$options["accept"]}\" ";
                }
            }

            if ( isset($options ["disabled"]) && $options ["disabled"] !== false ) {
                $code .= " disabled=\"true\" ";
            }
            if ( isset($options["size"]) ) {
                $code .= " size=\"{$options["size"]}\" ";
            }
            
            if (isset($options["maxlength"])) {
                $code .= " maxlength=\"{$options["maxlength"]}\" ";
            }            
            
            if (isset($options["readonly"])) {
                $code .= " readonly=\"{$options["readonly"]}\" ";
            }

            if ( !empty($class) ) {
                $code .= " class=\"{$class}\" ";
            }

            if ( !empty($style) ) {
                $code .= " style=\"{$style}\" ";
            }

            $code .= "/>";

            if ( $type === "checkbox" || $type === "radio" ) {
                if ( isset($options ["text"]) ) {
                    $code .= $options ["text"];
                }
            }            
            return $code;
        }

        /**
         * Creates a text input
         *
         * @param string $id The id for the input
         * @param string $value The value for the input
         * @param string $class The CSS class for the input
         * @param string $style The CSS style for the input
         */
        public static function textInput($id, $value, array $options = array(), $class = "", $style = "") {
            return self::inputTag($id, "text", $value, $options, $class, $style);
        }

        /**
         * Creates a password input
         *
         * @param string $id The id for the input
         * @param string $value The value for the input
         * @param string $class The CSS class for the input
         * @param string $style The CSS style for the input
         */
        public static function passwordInput($id, $value, array $options = array(), $class = "", $style = "") {
            return self::inputTag($id, "password", $value, $options, $class, $style);
        }

        /**
         * Creates a checkbox input
         *
         * @param string $id The id for the input
         * @param string $value The value for the input
         * @param string $text The text to display
         * @param bool $checked If the input is checked or not checked
         * @param string $class The CSS class for the input
         * @param string $style The CSS style for the input
         */
        public static function checkboxInput($id, $value, $text, $checked = false, $class = "", $style = "") {
            $options = array();
            if ( $checked === true ) {
                $options ["checked"] = true;
            }
            $options ["text"] = $text;
            return self::inputTag($id, "checkbox", $value, $options, $class, $style);
        }

        /**
         * Creates a radio input
         *
         * @param string $id The id for the input
         * @param string $value The value for the input
         * @param string $text The text to display
         * @param bool $checked If the input is checked or not checked
         * @param string $class The CSS class for the input
         * @param string $style The CSS style for the input
         */
        public static function radioInput($id, $value, $text, $checked=false, $class = "", $style = "") {
            $options = array();            
            if ( $checked === true ) {                
                $options ["checked"] = true;
            }
            $options ["text"] = $text;
            return self::inputTag($id, "radio", $value, $options, $class, $style);            
        }

        /**
         * Creates a file input
         *
         * @param string $id The id for the input
         * @param string $value The value for the input
         * @param string $accept The mime type that accepts this input
         * @param string $class The CSS class for the input
         * @param string $style The CSS style for the input
         */
        public static function fileInput($id, $value, $accept = "", $class = "", $style = "") {
            $options = array();
            if ( !empty($accept) ) {
                $options ["accept"] = $accept;
            }
            return self::inputTag($id, "file", $value, $options, $class, $style);
        }

        /**
         * Creates a button input
         *
         * @param string $id The id for the input
         * @param string $value The value for the input
         * @param string $class The CSS class for the input
         * @param string $style The CSS style for the input
         */
        public static function buttonInput($id, $value, $text, $type = "submit", $class = "", $style = "") {
            $code = "<button id=\"{$id}\" name=\"{$id}\" type=\"{$type}\" ";
            if ( !empty($class) ) {
                $code .= " class=\"{$class}\" ";
            }
            if ( !empty($style) ) {
                $code .= " style=\"{$style}\" ";
            }

            $code .= "> ";
            $code .= "{$text}";
            $code .= "</button>";

            return $code;
        }

        /**
         * Creates a hidden input
         *
         * @param string $id The id for the input
         * @param string $value The value for the input
         * @param string $class The CSS class for the input
         * @param string $style The CSS style for the input
         */
        public static function hiddenInput($id, $value, $class = "", $style = "") {
            return self::inputTag($id, "hidden", $value, array(), $class, $style);
        }

        /**
         * Creates a submit input
         *
         * @param string $id The id for the input
         * @param string $value The value for the input
         * @param string $class The CSS class for the input
         * @param string $style The CSS style for the input
         */
        public static function submitInput($id, $value, $class = "", $style = "") {
            return self::inputTag($id, "submit", $value, array(), $class, $style);
        }

        /**
         * Creates a reset input
         *
         * @param string $id The id for the input
         * @param string $value The value for the input
         * @param string $class The CSS class for the input
         * @param string $style The CSS style for the input
         */
        public static function resetInput($id, $value, $class = "", $style = "") {
            return self::inputTag($id, "reset", $value, array(), $class, $style);
        }

        public static function generateFormToken($form) {
            $token = md5(uniqid(microtime() . microtime(), true));
            $tokenTime = time();
            $form = "{$form}_token";
            FW_Session::set($form, array(
                'token' => $token,
                'time' => $tokenTime
            ), "FORMS");
            return $token;
        }

        public static function verifyFormToken($form,$token=null,$deltaTime = 0) {            
            $form = "{$form}_token";            
            
            if ($token===null) {
                $token = FW_Request::getInstance()->post("sectoken");                
            }
            
            // comprueba si hay un token registrado en sesión para el formulario
           if (FW_Session::get($form,"FORMS")===null ) {                
                return false;
            }

            // compara el token recibido con el registrado en sesión
            $sessionToken = FW_Session::get($form,"FORMS");
            if ($sessionToken["token"]!== $token ) {
                return false;
            }
            // si se indica un tiempo máximo de validez del ticket se compara la
            // fecha actual con la de generación del ticket
            if ( $deltaTime>0 ) {
                $tokenAge = time() - $sessionToken["time"];
                if ( $tokenAge<=$deltaTime ) {                    
                    return false;
                }
            }
            
            // TODO: Change            
            //FW_Session::unsetData($form,"FORMS");            
            return true;
        }

    };
?>