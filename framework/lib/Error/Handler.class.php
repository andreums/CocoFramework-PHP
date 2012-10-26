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
 * Error handling and logging
 * PHP Version 5.2
 *
 * @author   Andrés Ignacio Martínez Soto <anmarso4@fiv.upv.es>
 * @license  BSD Style
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * Class ErrorHandler
 * a class to handle an log the errors
 *
 * @author andreu
 * @package Framework
 *
 */

class FW_Error_Handler extends FW_Singleton {

    /**
     * A variable to hold the config object
     *
     * @var Config
     */
    private $_config;

    /**
     * The path to the error
     * handler and logs
     *
     * @var string
     */
    private $_errorPath;


    private static $_setUpDone;

    /**
     * The constructor of ErrorHandler
     *
     * @return void
     */
    public function __construct()  {
        return true;        
        set_error_handler(array($this,"handleError"));
        self::$_setUpDone = false;        
    }

    private function _setUp() {
        $this->_config     = FW_Config::getInstance();
        $this->_errorPath  = $this->_config->getParameter("error","default","errorPath");                
    }
    /**
     * Handles an error
     *
     * @param $type string The type of the error
     * @param $string string The message of the error
     * @return bool
     */
    public function handleError($type, $string)  {

        $msg = "";

        $caller = next(debug_backtrace());
        if (isset($caller["file"])) {
            $file = $caller["file"];
        }
        else {
            $file = "";
        }
        if (isset($caller["line"])) {
            $line = $caller["line"];
        }
        else {
            $line = 0;
        }

        $localTime = time();
        $time = localtime($localTime,true);

        $errorExploded = explode("|",$string);
        $type = $errorExploded[0];
        $type = trim($type);
        $type = strtolower($type);
        if (count($errorExploded)>=2) {
            $msg = trim($errorExploded[1]);
        }
        else {
            $type = "error";
            $msg = trim($string);
        }

        if ($msg=="") {
            $msg = $string;
        }

        $msg .= trim($this->get_debug_print_backtrace());
        $msg .= "\n\n";
        return $this->_logError($type,$line,$msg,$file);
    }


    /**
     * Displays Internal Server Error (Error 500)
     *
     * @static
     * @return bool
     */
    public static function displayError() {
        $config = FW_Config::getInstance();
        try {
            $errorPath = $config->getParameter("error","default","errorPath");
            $dir = $errorPath.'/'."view".DS;
            $customDir = $errorPath.'/'."view".'/'."customTemplates".DS;
            $listDir = @scandir($customDir,1);
            if ( count($listDir)==2 ) {
                $template = "defaultInternalError.php";
                $template = $dir.$template;
            }
            else {
                $template = (string) $config->getcustomInternalErrorTemplate();
                $template = $customDir.$dir;
            }

            if (is_file($template)) {
                include $template;
            }

            else {
                print "<h1>Error: 500</h1>";
            }

            $caller = debug_backtrace();
            $caller = $caller[1];
            if (isset($caller["file"])) {
                $file = $caller["file"];
            }
            else {
                $file = "";
            }
            if (isset($caller["line"])) {
                $line = $caller["line"];
            }
            else {
                $line = 0;
            }
            $get = print_r($_GET,TRUE);
            $post = print_r($_POST,TRUE);
            $files = print_r($_FILES,TRUE);
            $session = print_r($_SESSION,TRUE);
            $message = "500 Internal Server Error for {$_SERVER["REQUEST_URI"]} IP={$_SERVER["REMOTE_ADDR"]} Parameters: ( GET={$get} | POST={$post} | FILE={$files} | SESSION={$session}";
            return FW_Error_Handler::logError("servererror",$line,$message,$file);
        }

        catch (Exception $ex) {
            return false;
        }


    }

    /**
     * Displays Not Found Error (Error 404)
     *
     * @static
     * @return bool
     */
    public static function displayNotFoundError() {
        $config = FW_Config::getInstance();
        try {
            $errorPath = $config->getParameter("error","default","errorPath");
            $dir = "{$errorPath}/view";
            $customDir = $errorPath.'/'."view".'/'."customTemplates/";
            $listDir = @scandir($customDir,1);
            if ( count($listDir)==2 ) {
                $template = "defaultNotFoundError.php";
                $template = $dir.$template;
            }
            else {
                $template = (string) $config->getParameter("error","default","customNotFoundTemplate");
                $template = $customDir.$dir;
            }

            if (is_file($template)) {
                include $template;
            }

            else {
                print "<h1>Error: 404</h1>";
            }

            $caller = debug_backtrace();
            $caller = $caller[1];
            if (isset($caller["file"])) {
                $file = $caller["file"];
            }
            else {
                $file = "";
            }
            if (isset($caller["line"])) {
                $line = $caller["line"];
            }
            else {
                $line = 0;
            }
            $get = print_r($_GET,TRUE);
            $post = print_r($_POST,TRUE);
            $files = print_r($_FILES,TRUE);
            $session = print_r($_SESSION,TRUE);
            $message = "404 Not Found for {$_SERVER["REQUEST_URI"]} IP={$_SERVER["REMOTE_ADDR"]} Parameters: ( GET={$get} | POST={$post} | FILE={$files} | SESSION={$session}";
            return FW_Error_Handler::logError("notfound",$line,$message,$file);
        }

        catch (Exception $ex) {
            return false;
        }

    }

    /**
     * Displays Forbidden Error (403 Error)
     *
     * @static
     * @return bool
     */
    public static function displayForbiddenError() {        
        try {            
            $dir = "framework/lib/Error/templates/";
            $customDir = $errorPath.'/'."view".'/'."customTemplates/";
            $listDir = @scandir($customDir,1);
            if ( count($listDir)==2 ) {
                $template = "defaultForbiddenError.php";
                $template = $dir.$template;
            }
            else {
                
            }

            if (is_file($template)) {
                include $template;
            }

            else {
                print "<h1>Error: 403</h1>";
            }

            $caller = debug_backtrace();
            $caller = $caller[1];
            if (isset($caller["file"])) {
                $file = $caller["file"];
            }
            else {
                $file = "";
            }
            if (isset($caller["line"])) {
                $line = $caller["line"];
            }
            else {
                $line = 0;
            }
            $get = print_r($_GET,TRUE);
            $post = print_r($_POST,TRUE);
            $files = print_r($_FILES,TRUE);
            $session = print_r($_SESSION,TRUE);
            $message = "403 Forbidden for {$_SERVER["REQUEST_URI"]} IP={$_SERVER["REMOTE_ADDR"]} Parameters: ( GET={$get} | POST={$post} | FILE={$files} | SESSION={$session}";
            return FW_Error_Handler::logError("forbidden",$line,$message,$file);

        }

        catch (Exception $ex) {
            return false;
        }

    }

    /**
     * Logs an error
     *
     * @access private
     * @param $type string The type of the error
     * @param $line int The line of the error
     * @param $error string The message of the error
     * @param $script string The script that has produced the error
     * @return bool
     */
    private function _logError($type,$line,$error,$script) {
        
        $log = FW_Log::factory("file","error","{$type}.log",false,array("append"=>true));
        $log->log(new FW_Log_Message($error));        
        
        /*

        try {
            $info = pathinfo($_SERVER["SCRIPT_FILENAME"]);
            $dir = $info["dirname"];
            $dir = $dir.'/'."framework".'/'."lib".'/'."error".'/'."logs";

            $file = $dir.'/'."{$type}.log";
            if (!is_file($file)) {
                try {
                    touch($file);
                }
                catch (Exception $ex) {
                    throw new Exception ("Can't create file {$file}, please check permissions");
                }
            }
            $fp = fopen($file,"a+");
            $time = date("d/m/Y H:i:s");
            $line = "[{$time}] \tError: ({$error}) at line {$line} in {$script} \n";
            fputs($fp,$line);
            fclose($fp);

            $this->_logRotate();
            return true;
        }

        catch (Exception $ex) {
            return false;
        }*/

    }

    /**
     * Logs an error
     *
     * @static
     * @param $type string The type of the error
     * @param $line int The line of the error
     * @param $error string The message of the error
     * @param $script string The script that has produced the error
     * @return bool
     */
    public static function logError($type,$line,$error,$script) {

        try {
            $info = pathinfo($_SERVER["SCRIPT_FILENAME"]);
            $dir = $info["dirname"];
            $dir = "{$dir}/framework/lib/error/logs/";

            $file = $dir.'/'."{$type}.log";
            if (!is_file($file)) {
                try {
                    touch($file);
                }
                catch (Exception $ex) {
                    throw new Exception ("Can't create file {$file}, please check permissions");
                }
            }
            $fp = fopen($file,"a+");
            date_default_timezone_set("Europe/Madrid");
            $time = date("d/m/Y H:i:s");
            $line = "[{$time}] \tError: ({$error}) at line {$line} in {$script} \n";
            fputs($fp,$line);
            fclose($fp);
            return true;
        }

        catch (Exception $ex) {
            return false;
        }

    }

    /**
     * Rotates all the error logs
     * If a log exceds 5000 lines,
     * the log is compressed with gzip
     * (and added its contents if the
     * gzip file already exists)
     * then a new log file is created
     * to continue writing on the log
     *
     * @return bool
     */
    private function _logRotate() {
        try {
            $dir = getcwd();
            $dir = "{$dir}".'/'."Core".'/'."framework".'/'."lib".'/'."error".'/'."logs".DS;
            $logs = @scandir($dir,1);
            if (count($logs)>2) {
                foreach ($logs as $logFile) {
                    $filename = $dir.$logFile;
                    if ( !is_dir($filename) ) {
                        $info = pathinfo($filename);
                        $ext = $info["extension"];
                        if ($ext=="log") {
                            $lines = count(file($filename));

                            if ( ($lines>=5000) && ($lines%5000==0) ) {
                                $gzLines = array();
                                $gzFile = "{$filename}.gz";
                                if (is_file($gzFile)) {
                                    $logInfo = "";
                                    $lines = gzfile($gzFile);
                                    foreach ($lines as $line) {
                                        $logInfo.="{$line}";
                                    }

                                    $contents = "";
                                    $fp = fopen($filename,"r");
                                    if ($fp) {
                                        $contents .= fgets($fp,1024);
                                    }
                                    fclose($fp);

                                    $logInfo = $logInfo.$contents;

                                    $gzLog = gzopen ($gzFile,"w9");
                                    gzwrite ($gzLog,$logInfo);
                                    gzclose ($gzLog);
                                    unlink($filename);
                                    return true;
                                }
                                else {
                                    $contents = "";
                                    $fp = fopen($filename,"r");
                                    if ($fp) {
                                        $contents .= fgets($fp,1024);
                                    }
                                    fclose($fp);
                                    $gzLog = gzopen ($gzFile,"w9");
                                    gzwrite ($gzLog,$contents);
                                    gzclose ($gzLog);
                                    unlink($filename);
                                    return true;
                                }
                            }
                        }
                    }


                }
            }

        }

        catch (Exception $ex) {
            return false;
        }
    }

    public function get_debug_print_backtrace($traces_to_ignore = 1) {
        ob_start();
        debug_print_backtrace();
        $trace = ob_get_contents();
        ob_end_clean();

        // Remove first item from backtrace as it's this function which
        // is redundant.
        $trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1);

        // Renumber backtrace items.
        $trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);
        $trace = trim($trace);

        return $trace;
    }


};
?>