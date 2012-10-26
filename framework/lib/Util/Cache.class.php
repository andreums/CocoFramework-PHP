<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Util_Cache extends FW_Singleton {

    public static function cleanDataCache() {
        $path = "framework/cache/data";
        self::_rrmdir($path);
        mkdir($path);
    }
    
    public static function cleanRouterCache() {
        $path = "framework/cache/framework/router";
        self::_rrmdir($path);
        mkdir($path);
        FW_Router::getInstance();
    }
    
    public static function cleanStyleCache() {
        $path = "framework/cache/framework/styles";
        self::_rrmdir($path);
        mkdir($path);       
    }
    
    public static function cleanSchemaCache() {
        $path = "framework/cache/framework/schemas";
        self::_rrmdir($path);
        mkdir($path);
        FW_ActiveRecord_Metadata_Manager::getInstance();
    }
    

    private static function _rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                
                if ($object !== "." && $object !=="..") {
                    if (filetype($dir . "/" . $object) === "dir") {
                        self::_rrmdir($dir . "/" . $object);
                    }
                    else {
                        unlink($dir . "/" . $object);
                    }                    
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

};
?>