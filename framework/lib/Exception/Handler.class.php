<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Exception_Handler extends FW_Singleton {
        private static $_config;
        private static $_setup;
        private $_enabled;
        public function __construct() {
            if ( self::$_setup === null ) {
                $this->_setUp();
            }
        }

        private function _setUp() {
        }

        public function handler(Exception $e) {
            try {
                // Get the exception information
                $type = get_class($e);
                $code = $e->getCode();
                $message = $e->getMessage();
                $file = $e->getFile();
                $line = $e->getLine();
                // Get the exception backtrace
                $trace = $e->getTrace();
                /*if ($e instanceof ErrorException) {
                 if (isset(Kohana_Exception::$php_errors[$code]))
                 {
                 // Use the human-readable error name
                 $code = Kohana_Exception::$php_errors[$code];
                 }
                 if (version_compare(PHP_VERSION, '5.3', '<'))
                 {
                 // Workaround for a bug in ErrorException::getTrace() that
                // exists in
                 // all PHP 5.2 versions. @see
                // http://bugs.php.net/bug.php?id=45895
                 for ($i = count($trace) - 1; $i > 0; --$i)
                 {
                 if (isset($trace[$i - 1]['args']))
                 {
                 // Re-position the args
                 $trace[$i]['args'] = $trace[$i - 1]['args'];
                 // Remove the args
                 unset($trace[$i - 1]['args']);
                 }
                 }
                 }
                 }*/
                // Create a text version of the exception
                $error = FW_Exception::text($e);
                /*if (is_object(Kohana::$log))  {
                 // Add this exception to the log
                 Kohana::$log->add(Log::ERROR, $error);
                 $strace = Kohana_Exception::text($e)."\n--\n" .
                $e->getTraceAsString();
                 Kohana::$log->add(Log::STRACE, $strace);
                 // Make sure the logs are written
                 Kohana::$log->write();
                 }
                 if (Kohana::$is_cli)
                 {
                 // Just display the text of the exception
                 echo "\n{$error}\n";
                 exit(1);
                 } */
                /*if ( ! headers_sent())    {
                 // Make sure the proper http header is sent
                 $http_header_status = ($e instanceof HTTP_Exception) ? $code :
                500;
                 header('Content-Type:
                '.Kohana_Exception::$error_view_content_type.';
                charset='.Kohana::$charset, TRUE, $http_header_status);
                 }*/
                if ( FW_Request::getInstance()->isAjaxRequest() ) {
                    // Just display the text of the exception
                    echo "\n{$error}\n";
                    exit(1);
                }
                // Start an output buffer
                $exceptionDisplay = array("type"=>"app","module"=>"system","controller"=>"exception","action"=>"displayException","internal"=>true,"parameters"=>array("exception"=>$e));                                                
                FW_FrontController::getInstance()->redirectToAction($exceptionDisplay);                
                exit(1);
            }
            catch (Exception $e) {
                // Clean the output buffer if one exists
                (ob_get_level() && ob_clean());
                // Display the exception text
                echo FW_Exception::text($e), "\n";
                // Exit with an error status
                exit(1);
            }
        }

    };
?>