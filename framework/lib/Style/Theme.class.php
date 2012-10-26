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
 * A class to represent a Theme
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
 * A class to generate and represent CSS style Themes
 *
 * @package Style
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Style_Theme {

    /**
     * The filename of this theme
     *
     * @var string
     */
    private $_filename;

    /**
     * The name of this theme
     *
     * @var string
     */
    private $_name;

    /**
     * The version of this theme
     *
     * @var string
     */
    private $_version;

    /**
     * The description of the theme
     *
     * @var string
     */
    private $_description;

    /**
     * An screenshot of this theme
     *
     * @var string
     */
    private $_screenshot;

    /**
     * The status of the theme (enabled/disabled)
     *
     * @var bool
     */
    private $_status;

    /**
     * The developer of this theme
     *
     * @var string
     */
    private $_developer;


    /**
     * An array to hold all the data for every module
     * of this theme
     *
     * @var array
     */
    private $_modules;


    /**
     * An array to hold the data of this theme
     *
     * @var array
     */
    private $_data;


    /**
     * Is this the default theme
     *
     * @var bool
     */
    private $_defaultTheme;


    /**
     * The base url for this web application
     * @var string
     */
    private $_baseurl;

    private $_plugins;



    /**
     * The constructor of a theme
     *
     * @param string $name The name of the theme
     * @param bool $internal If this theme is bundled with the framework
     *
     * @return void
     */
    public function __construct($name,$internal=false) {
        $this->_configure($name,$internal);
        $this->_initialize();
    }

    /**
     * Configures this theme
     *
     * @param string $name The name of the theme
     * @param bool $internal If this theme is bundled with the framework
     *
     * @return void
     */
    private function _configure($name,$internal) {
        $this->_name        = $name;
        $this->_modules     = array(
                "internal" => array(),
                "external" => array(),
                "default"  => array()
        );
        $this->_plugins     = array();
        $filename           = "app/resources/style/{$name}/style.php";
        if ($internal) {
            $filename       = "framework/app/resources/style/{$name}/style.php";
        }
        $this->_filename = $filename;
    }

    /**
     * Initializes this theme
     *
     * @return unknown_type
     */
    private function _initialize() {
        $this->_baseurl     = FW_Config::getInstance()->get("core.global.baseURL");
        $this->_load();
    }


    /**
     * TODO: Refactor, split into methods...
     *
     * Loads all the data for this Theme
     *
     * @return void
     */
    private function _load() {
        if (is_file($this->_filename) && is_readable($this->_filename)) {
            require_once $this->_filename;
            $this->_data      = $theme;

            if ( $this->_setThemeInformation() ) {

                if (!isset($this->_data["modules"])) {
                    if (!isset($this->_data["default"])) {
                        throw new FW_Style_Exception("Can't load style information for {$this->_name} theme.");
                    }
                }
                $default                   = $this->_parseDefaultTheme();
                $this->_modules["default"] = $default;

                $external  = array();
                foreach ($this->_data["modules"]["external"] as $name=>$info) {
                    $module = $this->_parseModuleData($name,$info);
                    $this->_modules["external"][$name] = $module;
                }

                $internal  = array();
                foreach ($this->_data["modules"]["internal"] as $name=>$info) {
                    $module = $this->_parseModuleData($name,$info);
                    $this->_modules["internal"][$name] = $module;
                }
            }
        }

        $pluginRegistry = FW_Plugin_Registry::getInstance();
        $plugins        = $pluginRegistry->getPlugins();
        if (count($plugins)) {
            foreach ($plugins as $plugin)  {
                $file = "app/lib/plugins/{$plugin}/style/style.php";
                if (is_file($file)) {
                    require_once $file;
                    $data  = $styles;
                    $plug  = $this->_parsePluginData($name,$data);
                    $this->_plugins[$plugin] = $plug;
                }
            }
        }
    }
	/**
     * Parses the styles for a plugin
     *
     * @param $name
     * @param $info
     * @return unknown_type
     */
    private function _parsePluginData($name,$info) {
        $plugin           = array ("name"=>$name,"styles"=>array());
        $plugin["name"]   = $name;
        $plugin["styles"] = $info;
        return $plugin;
    }

    /**
     * Parses the styles for a module
     *
     * @param $name
     * @param $info
     * @return unknown_type
     */
    private function _parseModuleData($name,$info) {
        $module = array ("name"=>$name,"controllers"=>array(),"default"=>array());
        $global = array();

        if (isset($info["default"])) {
            $global            = $this->_parseModuleGlobalInformation($info["default"]);
            $module["default"] = $global;
        }

        if (isset($info["controllers"])) {
            $controllers = $info["controllers"];
            foreach ($controllers as $key=>$data) {
                $controller = $this->_parseControllerData($data);
                $module["controllers"][$key] = $controller;
            }
        }
        return $module;
    }

    /**
     * Parses data for a Controller
     *
     * @param array $data The data to parse
     *
     * @return array
     */
    private function _parseControllerData($data) {
        $styles = array();
        if (count($data)) {
            foreach ($data as $style) {
                $styles []= $style;
            }
        }
        return $styles;
    }


    /**
     * Sets the information of this theme
     *
     * @return bool
     */
    private function _setThemeInformation() {
        $this->_name           = $this->_data["name"];
        $this->_version        = $this->_data["version"];
        $this->_developer      = $this->_data["developer"];
        $this->_description    = $this->_data["description"];
        $this->_screenshot     = $this->_data["screenshot"];
        $this->_status         = $this->_data["enabled"];
        $this->_defaultTheme   = $this->_data["isDefaultTheme"];
        if ($this->_status) {
            return true;
        }
        return false;
    }

    /**
     * Parses the global information of a module
     *
     * @param array $data The data to parse
     *
     * @return array
     */
    private function _parseModuleGlobalInformation($data) {
        $styles = array();
        if (count($data)) {
            foreach ($data as $style) {
                $styles []= $style;
            }
        }
        return $styles;
    }

    /**
     * Parses the default theme
     *
     * @return array
     */
    private function _parseDefaultTheme() {
        $styles = array();
        if (isset($this->_data["default"])) {
            $default = $this->_data["default"];
            if (count($default)) {
                foreach ($default as $style) {
                    $styles []= $style;
                }
            }
        }
        return $styles;
    }

    /**
     * TODO: FIX
     * Parses the theme of a module
     *
     * @param string $name The name of the module
     * @param array $data The data
     * @param bool $internal If the module is internal or external
     *
     * @return void
     */
    private function _parseModuleTheme($name,$data,$internal) {
        $module = array("name"=>$name,"styles"=>array());
        if (count($data)) {
            foreach ($data as $style) {
                $module["styles"] []= $style;
            }

        }
        if ($internal==="internal") {

        }
        $module["styles"]    = $styles;
        $this->_modules[$for]= $module;
    }

    /**
     * Gets the name of this theme
     *
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Gets the modules which have style
     * in this Theme
     *
     * @return array
     */
    public function getModules() {
        $modules = array("internal"=>array(),"external"=>array());
        $modules["external"] = array_keys($this->_modules[$external]);
        $modules["internal"] = array_keys($this->_modules[$internal]);
        return $modules;
    }



    /**
     * Gets the XHTML/HTML5 style tag for a CSS file
     *
     * @param array $style The description of the style
     *
     * @return string
     */
    private function _getStyleTag(array $style=array()) {
        $tag     = "";
        if (!empty($style)) {
            $media     = $style["media"];
            $media     = implode(',',$media);
            $filename  = $style["file"];
            $alternate = $style["alternate"];

            $tag      = "<link type=\"text/css\" ";
            if (strpos($filename,"http")===false) {
                $filename = "style/{$this->_name}/{$filename}";
                $tag     .= "href=\"{$this->_baseurl}/{$filename}\"";
            }
            else {
                $tag .= "href=\"{$filename}\"";
            }

            if ($alternate) {
                $tag .= " rel=\"stylesheet alternate\" ";
            }
            else {
                $tag .= " rel=\"stylesheet\" ";
            }
            $tag .= "media=\"{$media}\"";
            $tag .= "/>\r\n";
        }
        return $tag;
    }

    /**
     * Checks if this theme has style for an HMVC action
     *
     * @param string $module The name of the module
     * @param string $controller the name of the controller
     * @param bool $internal If the module is internal or external
     *
     * @return bool
     */
    public function hasStyleFor($name,$controller,$internal) {
        $result = false;

        if ($internal===true) {
            if (isset($this->_modules["internal"][$name]["controllers"][$controller])) {
                $result = true;
            }
        }
        else {
            if (isset($this->_modules["external"][$name]["controllers"][$controller])) {
                $result = true;
            }
        }

        return $result;
    }

   public function hasStyleForPlugin($plugin) {
        $result = false;

        if (isset($this->_plugins[$plugin])) {
            $result = true;
        }

        return $result;
    }

    /**
     * Checks if this theme has the default style for module
     *
     * @param string $module The name of the module
     * @param bool $internal If the module is internal or external
     *
     * @return bool
     */
    public function hasDefaultStyle($module,$internal) {
        $result = false;
        if ($internal===true) {
            if (isset($this->_modules["internal"][$module]["default"])) {
                $result = true;
            }
        }
        else {
            if (isset($this->_modules["external"][$module]["default"])) {
                $result = true;
            }
        }

        return $result;
    }

 	/**
     * Gets the style for an HMVC module
     *
     * @param string $module The name of the module
     * @param string $controller The name of the controller
     * @param bool $internal If the module is internal or external
     *
     * @return string
     */
    public function getDefaultStyleFor($module,$internal) {
        $style  = "";
        $styles = null;

        if ($internal===true) {
            if (isset($this->_modules["internal"][$module]["default"])) {
                $styles = $this->_modules["internal"][$module]["default"];
            }
        }
        else {
            if (isset($this->_modules["external"][$module]["default"])) {
                $styles = $this->_modules["external"][$module]["default"];
            }
        }
        if ($styles!==null) {
            foreach ($styles as $aux) {
                $style .= $this->_getStyleTag($aux);
            }
        }

        return $style;
    }

    /**
     * Gets the style for an HMVC action
     *
     * @param string $module The name of the module
     * @param string $controller The name of the controller
     * @param bool $internal If the module is internal or external
     *
     * @return string
     */
    public function getStyleFor($module,$controller,$internal) {
        $style  = "";
        $styles = null;

        if ($internal===true) {
            if (isset($this->_modules["internal"][$module]["controllers"][$controller])) {
                $styles = $this->_modules["internal"][$module]["controllers"][$controller];
            }
        }
        else {
            if (isset($this->_modules["external"][$module]["controllers"][$controller])) {
                $styles = $this->_modules["external"][$module]["controllers"][$controller];
            }
        }
        if ($styles!==null) {
            foreach ($styles as $aux) {
                $style .= $this->_getStyleTag($aux);
            }
        }

        return $style;
    }

    public function getStyleForPlugin($name) {
        $style  = "";
        $styles = null;
        if (isset($this->_plugins[$name])) {
            $styles = $this->_plugins[$name]["styles"];
        }
        if ($styles!==null) {
            foreach ($styles as $aux) {
                $style .= $this->_getStyleTag($aux);
            }
        }

        return $style;
    }

    /**
     * Gets information about this theme
     *
     *  @return array
     */
    public function getInformation() {
        $info = array (
            "name"        => $this->_name,
            "developer"   => $this->_developer,
            "description" => $this->_description,
            "screenshot"  => "style/{$this->_name}/{$this->_screenshot}",
            "version"     => $this->_version
        );
        return $info;
    }

    /**
     * Gets the default style for a module in this Theme
     *
     * @return string
     */
    public function getDefaultStyle() {
        $style = "";
        if (isset($this->_modules[ "default"])) {
            $styles = $this->_modules["default"];
            if ($styles!==null) {
                foreach ($styles as $aux) {
                    $style .= $this->_getStyleTag($aux);
                }
            }
        }
        return $style;
    }

    /**
     * Checks if this theme is the default theme
     *
     * @return bool
     */
    public function isDefaultTheme() {
        return ($this->_defaultTheme);
    }

    /**
     * Method to serialize this object
     *
     * @return array
     */
    public function __sleep() {
        return array("_name","_version","_description","_status","_developer","_screenshot","_modules","_filename","_defaultTheme","_baseurl");
    }

    /**
     * Method to deserialize the Theme object
     *
     * @return unknown_type
     */
    public function __wakeup() {}





};
?>