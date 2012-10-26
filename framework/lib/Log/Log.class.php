<?php

/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
abstract class FW_Log implements IComponent {

    private static $_instances;

    /* RFC 3164
     0       Emergency: system is unusable
     1       Alert: action must be taken immediately
     2       Critical: critical conditions
     3       Error: error conditions
     4       Warning: warning conditions
     5       Notice: normal but significant condition
     6       Informational: informational messages
     7       Debug: debug-level messages
     */

    const Log_Emerg   = 0;
    const Log_Alert   = 1;
    const Log_Crit    = 2;
    const Log_Err     = 3;
    const Log_Warning = 4;
    const Log_Notice  = 5;
    const Log_Info    = 6;
    const Log_Debug   = 7;

    const timestampFormat = "%d %b %Y %H:%M:%S %Z";
    const lineFormat      = "[{{timestamp}}] {{identity}}: {{message}}\t{{file}} {{eol}}  {{backtrace}} {{eol}}";
    const eol             = "\n";


    protected $_messages;
    protected $_transaction;
    protected $_config;


    public static function getInstance($type,$identity,$name="",$transaction=true,$configuration=array()) {

        if (!isset(self::$_instances)) {
            self::$_instances = array();
        }

        if (empty($configuration)) {
            $configuration = FW_Config::getInstance()->get("log.sections.{$type}");            
        }

        $ident = implode('.',array($type,$identity,$name,$transaction,implode('|',$configuration)));
        $ident = md5($ident);
        if (!isset(self::$_instances[$ident])) {
            self::$_instances[$ident] = FW_Log::factory($type,$identity,$name,$transaction,$configuration);
        }
        return self::$_instances[$ident];
    }

    public static function factory($type,$identity,$name="",$transaction,array $configuration=array()) {
        $ctype      = ucfirst($type);
        $className  = "FW_Log_Adapter_{$ctype}";
        
        if (empty($configuration)) {
            $configuration = FW_Config::getInstance()->get("log.sections.{$type}");            
        }        
        $config = new FW_Container_Parameter();
        $config->fromArray($configuration);
        $configuration = $config;

        return new $className($identity,$name,$transaction,$configuration);
    }

    abstract public function open();
    abstract public function begin();
    abstract public function close();
    abstract public function end();
    abstract public function flush();
    abstract public function commit();
    abstract public function rollback();
    abstract public function log(FW_Log_Message $message);
     
    public function configure(FW_Container_Parameter $parameters=null) {
         
    }
    public function initialize(array $arguments=array()){

    }
     
    protected function _addMessage(FW_Log_Message $message) {
        $this->_messages []= $message;
    }
     
    protected function _clear() {
        if (!empty($this->_messages)) {
            $this->_messages = array();
        }
    }
     
    protected function _getMessage($message)  {
        $msg = "";
        if ( (is_object($message)) || ($message instanceof Exception) ) {
            if (method_exists($message,"getMessage")) {
                $msg = $message->getMessage();
            }
            if (method_exists($message,"toString") ) {
                $msg = $message->toString();
            }
            if (method_exists($message,"__tosString") ) {
                $msg = $message->__toString();
            }
        }
        if ( (is_array($message)) ) {
            if ( (isset($message['message'])) ) {
                if ( (is_array($message["message"])) || (is_object($message["message"])) ) {}
                if ( is_scalar($message["message"]) ) {
                    $msg = $message["message"];
                }
            }
        }
        if (strlen($msg)===0) {
            $msg = $message;
        }
        return $msg;
    }
     

    protected function _format($identity,FW_Log_Message $message,$format=null) {
        $line = "";
        $eol  = self::eol;

        if ($format===null) {
            $format = self::lineFormat;
        }
         
        $sTimestamp = strftime(self::timestampFormat,$message->getTimestamp());
         
        $line = $format;
        $line = str_replace("{{timestamp}}",$sTimestamp,$line);
        $line = str_replace("{{identity}}",$identity,$line);
        $line = str_replace("{{message}}",$message->getMessage(),$line);
        $line = str_replace("{{file}}",$message->getFile(),$line);
        $line = str_replace("{{backtrace}}",$message->getBacktrace(),$line);
        $line = str_replace("{{eol}}",$eol,$line);
         
        return $line;
    }

     

};
?>