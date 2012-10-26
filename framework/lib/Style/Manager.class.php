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
 * Class that implements a manager to manage and switch between CSS styles
 * grouped into the concept of Themes
 *
 * PHP Version 5.3
 *
 * @package  Style
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * Class that implements a manager to manage and switch between CSS styles
 * grouped into the concept of Themes
 *
 * @package Style
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Style_Manager extends FW_Registry implements IComponent {

    /**
     * An instance of this component
     *
     * @static
     * @var FW_Style_Manager
     */
    private static $_instance;

    /**
     * An access to the config component
     *
     * @var FW_Config
     */
    private $_config;

    /**
     * If the Style_Manager is enabled
     * @var bool
     */
    private $_enabled;

    /**
     * The default theme
     *
     * @var string
     */
    private $_default;

    /**
     * The theme to use
     *
     * @var string
     */
    private $_theme;

    /**
     * Constructs the Style Manager component
     *
     * @return void
     */
    public function __construct() {
        $this->configure(null);
        $this->initialize(null);
    }


    /**
     * Gets the instance of the Style Manager
     *
     * @static
     * @return FW_Style_Manager
     */
    public static function getInstance() {
        if (self::$_instance===null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /* (non-PHPdoc)
     * @see framework/lib/interfaces/IComponent#configure($parameters)
     */
    public function configure(FW_Container_Parameter $parameters=null) {
        $this->_config  = FW_Config::getInstance();
        $this->_enabled = $this->_config->get("style.global.enabled");
        $this->_default = $this->_config->get("style.global.defaultTheme");
    }

    /* (non-PHPdoc)
     * @see framework/lib/interfaces/IComponent#initialize($arguments)
     */
    public function initialize(array $arguments=null) {
        $this->_loadThemes();
    }


    /**
     * Does the styles.ser file exists?
     *
     * @return bool
     */
    private function _existsSerializedThemes() {
        $filename = "framework/cache/framework/styles/styles.ser";
        return ( is_file($filename) && is_readable($filename) );
    }

    /**
     * Loads the themes serialized in styles.ser
     *
     * @return void
     */
    private function _loadSerializedThemes() {
        $filename       = "framework/cache/framework/styles/styles.ser";
        $data           = file_get_contents($filename);
        $data           = unserialize($data);
        $this->_objects = $data;
    }

    /**
     * Loads a theme
     *
     * @param string $theme The name of the theme
     * @param bool   $internal If the theme is external or internal
     *
     * @return void
     */
    private function _loadTheme($theme,$internal=false) {
        $name  = $theme;
        $theme = new FW_Style_Theme($theme,$internal);
        $this->set($name,$theme);
    }


    /**
     * Loads themes from disk
     *
     * @return void
     */
    private function _loadThemesFromDisk() {
        $path     = "style";
        if (!is_dir($path)) {
            $path = "app/resources/style";
        }

        $themes   = scandir($path);
        foreach ($themes as $theme) {
            if ( ($theme[0]!=='.') && (is_dir("style/{$theme}")) ) {
                $this->_loadTheme($theme,false);
            }
        }
    }


    /**
     * Loads the themes
     *
     * @return void
     */
    private function _loadThemes() {
        if ($this->_existsSerializedThemes()) {
            $this->_loadSerializedThemes();
        }

        else {
            $this->_loadThemesFromDisk();
            $this->_serializeThemes();
        }
    }

    /**
     * Serializes the themes
     *
     * @return bool
     */
    private function _serializeThemes() {
        if (!is_dir("framework/cache/framework/styles")) {
            mkdir("framework/cache/framework/styles",755);
        }
        $serializedStylesFile = "framework/cache/framework/styles/styles.ser";
        $serialized           = serialize($this->_objects);
        if (file_put_contents($serializedStylesFile,$serialized)>0){
            return true;
        }
        return false;
    }

    /**
     * Checks if a theme exists
     *
     * @param string $name The name of the theme
     *
     * @return bool
     */
    public function existsTheme($name) {
        return $this->exists($name);
    }

    /**
     * Gets a JSON string representing a collection of data about
     * the themes the system has loaded
     *
     * @return string
     */
    public function getThemes() {
        $json   = ' {"themes":[';
        $themes = $this->keys();
        if (count($themes)) {
            foreach ($themes as $theme) {
                $json .= json_encode($this->get($theme)->getInformation()).',';
            }
            $json   = substr($json,0,-1);
        }

        $json  .= ' ]}';
        return $json;
    }

    /**
     * Adds a theme
     *
     * @param FW_Style_Theme $theme The theme
     *
     * @return mixed
     */
    public function addTheme(FW_Style_Theme $theme) {
        return $this->set($theme->getName(),$theme);
    }

    /**
     * Gets a theme
     *
     * @param string $name The name of the theme
     *
     * @return FW_Style_Theme
     */
    public function getTheme($name) {
        return $this->get($name);
    }




    /**
     * Checks if this has a theme
     *
     * @param string $theme The name of the theme
     *
     * @return bool
     */
    public function hasTheme($theme) {
        return $this->exists($theme);
    }

    /**
     * Uses the selected theme
     *
     * @param string $theme The name of the theme
     *
     * @return void
     */
    public function useTheme($theme) {
        if ($this->hasTheme($theme)) {
            $this->_theme = $theme;
            FW_Session::getInstance()->set("theme",$theme,"framework");
        }
    }

    /**
     * Gets the theme to use
     *
     * @return string
     */
    private function _getThemeToUse() {

        $theme        = "";
        $useUserTheme = $this->_config->get("style.global.userThemeHasPriority");

        if ($this->_theme!==null) {
            $theme = $this->_theme;
        }
        else {
            if ($useUserTheme) {
                $userTheme    = $this->_getUserTheme();
                if ($userTheme!==null) {
                    $theme = $userTheme;
                }
            }

            if (empty($theme)) {
                $theme = $this->_getSessionTheme();
            }


            if (empty($theme)) {
                $theme = $this->_default;
                if ($theme===null) {
                    $theme = "default";
                }
            }
        }
        return $theme;

    }


    /**
     * Returns the style tags for a Theme
     *
     * @param array $parameters An array of parameters of an HMVC action
     *
     * @return string
     */
    public function displayStyle(array $parameters=array()) {

        $style     = "";
        $themeName = $this->_getThemeToUse();
        $theme     = $this->get($themeName);


        if ($theme===null) {
            throw new FW_Style_Exception("The selected theme {$themeName} appears to not exist");
        }

        if (empty($parameters)) {
            $style = $theme->getDefaultStyle();
        }
        else {
            if ($parameters["type"]==="app") {
                $internal   = $parameters["internal"];
                $module     = $parameters["module"];
                $controller = $parameters["controller"];

                if ($theme->hasStyleFor($module,$controller,$internal) ) {
                    $style = $theme->getStyleFor($module,$controller,$internal);
                }
                else if ($theme->hasDefaultStyle($module,$internal)) {
                    $style = $theme->getDefaultStyleFor($module,$internal);
                }
                else {
                    $style = $theme->getDefaultStyle();
                }
            }
            if ($parameters["type"]==="plugin") {
                $plugin    = $parameters["plugin"];

                if ($theme->hasStyleForPlugin($plugin)) {
                    $style = $theme->getStyleForPlugin($plugin);
                }
                else {
                    $style = $theme->getDefaultStyle();
                }
            }





        }
        return $style;
    }


    /**
     * Sets the theme in use in the session
     *
     * @param string $theme The name of the theme
     * @return void
     */
    private function _setSessionTheme($theme) {
        FW_Session::getInstance()->set();
    }

    /**
     * Gets the theme stored in the session
     *
     * @return string
     */
    private function _getSessionTheme() {
        $theme = FW_Session::getInstance()->get("style","framework");
        if ($theme!==null) {
            return $theme;
        }
        return "";
    }

    /**
     * Gets the theme of an user
     * (stored in the database)
     *
     * @return string
     */
    private function _getUserTheme() {

        $theme          = null;
        $table          = "";
        $column         = "";
        $usernameColumn = "";
        $username       = "";

        $authentication = new FW_Authentication();
        $database       = FW_Database::getInstance();
        $prefix         = $database->getPrefix();

        $datasource     = $this->_config->get("style.global.database");
        if ($datasource!==null) {
            $table          = $datasource["source"];
            $column         = $datasource["column"];
            $usernameColumn = $datasource["username"];
        }

        $user           = $authentication->user();
        if ($user!==null) {
            $username = $user->getUsername();
            $query = "SELECT {$column} FROM {$prefix}{$table} WHERE {$usernameColumn}='{$username}'";
            $database->query($query);
            if ($database->numRows()>0) {
                $theme = $database->fetchRow();
                $theme = $theme[0];
            }
        }
        return $theme;
    }


    /* (non-PHPdoc)
     * @see framework/lib/Registry/FW_Registry#__sleep()
     */
    public function __sleep() {
        return array("_objects");
    }

    /* (non-PHPdoc)
     * @see framework/lib/Registry/FW_Registry#__wakeup()
     */
    public function __wakeup() {}



};
?>