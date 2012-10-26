<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_loaders extends FW_Singleton {

    public static function loadModels() {
        $path  = "app/lib/models/";
        if (self::loadPHPFilesInPath($path) ) {
            return true;
        }
        return false;
    }

    public static function loadPluginsModels() {
        $pluginRegistry = FW_Plugin_Registry::getInstance();
        $plugins        = $pluginRegistry->getPluginNames();
        if (count($plugins)>0) {
            foreach ($plugins as $plugin) {
                $path = "app/lib/plugins/{$plugin}/models/";
                if (self::loadPHPFilesInPath($path) ) {
                    return true;
                }
                return false;
            }
        }
    }


    public static function loadUserFilters() {
        $path = "app/lib/filters";
        if (self::loadPHPFilesInPath($path) ) {
            return true;
        }
        return false;
    }
 

    public static function loadHelpers() {
        $path = "framework/lib/helpers";
        if (self::loadPHPFilesInPath($path) ) {
            return true;
        }
        return false;
    }
     
    public static function loadUserHelpers() {
        $path  = "app/lib/helpers";
        if (self::loadPHPFilesInPath($path) ) {
            return true;
        }
        return false;
    }
     
     
    public static function loadUserClasses() {
        $path   = "app/lib/classes";
        if (self::loadPHPFilesInPath($path) ) {
            return true;
        }
        return false;
    }
     
    public static function loadUserWidgets() {
        $path  = "app/lib/widgets";
        if (self::loadPHPFilesInPath($path) ) {
            return true;
        }
        return false;
    }


    public static function loadPHPFilesInPath($path) {
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file[0]!='.') {
                        $file = "{$path}/{$file}";
                        if (!is_dir($file)) {
                            if (substr($file,-10)===".class.php") {
                                include $file;
                            }
                        }
                    }
                }
                closedir($dh);
                return true;
            }
        }
        return false;
    }


    public static function includeController($module,$controller,$internal=false) {

        $path     = "";
        $file        = "";

                 
        $controller = "{$controller}Controller";

        if ($internal===false) {
            $path = "app/modules/{$module}";
        }
        else {
            $path = "framework/app/modules/{$module}";
        }

        $file  = "{$path}/controller/{$controller}.class.php";
         
        try {
            require_once $file;
            return true;
        }
        catch (FW_Exception $exception) {

        }

        return false;
    }

};
?>