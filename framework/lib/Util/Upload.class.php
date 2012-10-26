<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_util_Upload {

        /**
         * Uploads a file and stores it on the selected path with the selected
         * name
         *
         * @param string $file The name of the file in $_FILES
         * @param bool $replace Overwrites the file
         * @param string $dirName The name of the directory
         * @param string $fileName The name of the file
         *
         *  TODO: NEEDS REFACTORING
         *
         * @return bool
         */
        public static function uploadFile($file, $replace = true, $dirName = "", $fileName = "") {
            $dirName = rtrim($dirName, '/');
            $dirNameCopy = $dirName;
            $fileNameCopy = $fileName;
            $request = FW_Request::getInstance();
            $file = $request->getFileParameter($file);
            if ( $file === null ) {
                return false;
            }
            else {
                if ( is_uploaded_file($file ["tmp_name"]) ) {
                    try {
                        $basePath = FW_Config::getInstance()->get("core.global.basePath");

                        if ( $fileName == "" ) {
                            $fileName = $file ["name"];
                        }

                        if ( $dirName == "" ) {
                            $uploadDir = FW_Config::getInstance()->get("core.global.uploadPath");
                            $uploadDir = $basePath . '/' . $uploadDir;
                        }
                        else {
                            $uploadDir = $basePath . '/' . $dirName;
                        }                        

                        if ( !is_file($uploadDir . '/' . $fileName) ) {
                            if ( move_uploaded_file($file ["tmp_name"], $uploadDir . '/' . $fileName) ) {
                                return ($dirNameCopy . '/' . $fileNameCopy);
                            } 
                            else {
                                return false;
                            }
                        }
                        else {
                            if ( copy($file ["tmp_name"], $uploadDir . '/' . $fileName) && $replace ) {
                                return ($dirNameCopy . '/' . $fileNameCopy);
                            }
                            else {
                                return false;
                            }
                        }
                    }
                    catch (Exception $ex) {
                        trigger_error("Request | Request can't move the uploaded file {$file} to the upload dir", E_USER_WARNING);
                        trigger_error("Request | Request can't move the uploaded file to the upload dir reason {$ex->getMessage()}", E_USER_WARNING);
                        return false;
                    }
                }
            }
            return false;
        }

        // para upload con plUpload
        public static function ajaxUpload($filename, $replace = true) {
            
        }

    };
?>