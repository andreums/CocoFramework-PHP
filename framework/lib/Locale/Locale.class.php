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
     * A class for internationalizate web apps
     *
     * PHP Version 5.3
     *
     * @package  Locale
     * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
     * @license  MIT+LGPL
     * @link     http://www.andresmartinezsoto.es
     *
     */

    /**
     * A class for internationalizate web apps
     *
     * @package Locale
     * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
     *
     */
    class FW_Locale extends FW_Singleton {

        /**
         * A variable wich holds the locale
         * directory
         *
         * @var string
         */
        private $_path;

        public function __construct(FW_Container_Parameter$parameters=null) {
            $this -> configure($parameters);
        }

        /**
         * Configures the FW_locale_i18n component
         *
         * @static
         * @access private
         * @return void
         */
        public function configure(FW_Container_Parameter$parameters=null) {
            $this -> _path = "app/resources/locales";

        }

        public function initialize($locale) {
            if (strlen($locale)>0) {
                $this->setLocale($locale);
            }
            else {
                $this->setLocale(null);
            }
        }

        public function hasLocale($locale) {            
           $path = "{$this->_path}/{$locale}/LC_MESSAGES/messages.po";
            if(is_file($path)) {
                return true;
            }
            return false;
        }

        /**
         * Changes the locale of the system
         *
         * @param  string $locale The locale to be changed
         *
         * @access public
         * @return void
         */
        public function setLocale($locale=null) {
            
            if($locale === null) {
                $acceptedLanguages = FW_Request::getInstance()->getAcceptedLanguages();
                
                if(FW_Session::issetData("currentLocale", "locale")) {
                    $locale = FW_Session::get("currentLocale", "locale");
                }                
                if (!empty($acceptedLanguages)) {
                    $langs  = array_keys($acceptedLanguages);                    
                    $locale = $langs[0];                    
                }
                if ($locale===null) {
                    $locale = FW_Config::getInstance() -> get("core.sections.locale.default");
                }                
            }
            
            FW_Session::set("currentLocale", $locale, "locale");
            $hack = FW_Config::getInstance()->get("core.sections.locale.hack");
            if($hack !== null) {
                $locale = "{$locale}{$hack}";
            }
            $env = "LC_ALL={$locale}";            
            putenv($env);
            setlocale(LC_ALL, $locale);

            bindtextdomain("messages", $this->_path);
            textdomain("messages");
            
            FW_Session::set("currentLocale",$locale,"locale");
            FW_Context::getInstance()->locale = $locale;
        }

        /**
         * Translates a string to the loaded locale
         *
         * @param  string $string The string to be translated
         * @static
         * @access public
         * @return string
         */
        public static function translate($string="") {
            return  gettext($string);
        }

        /**
         * Gets the locale in use
         *
         * @access public
         * @return string
         */
        public function getLocale() {

            if(FW_Session::issetData("currentLocale", "locale")) {
                $locale = FW_Session::get("currentLocale", "locale");
            }
            else {
                $locale = FW_Config::getInstance()->get("core.sections.locale.default");
            }
            return $locale;
        }

    }
?>