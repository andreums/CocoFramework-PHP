<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Log_Adapter_File extends FW_Log {

    private $_name;
    private $_lock;
    private $_append;
    private $_filename;
    private $_fileHandler;
    private $_identity;


    public function __construct($identity,$name="",$transaction=false,FW_Container_Parameter $configuration=null) {
        
        $configuration->name        = $name;
        $configuration->identity    = $identity;        
        $configuration->transaction = $transaction;
        
        $this->_configure($configuration);
        $this->open();
    }

    public function __destruct() {
        $this->close();
    }

    private function _configure (FW_Container_Parameter $configuration=null) {
        if ($configuration!==null) {
            
            if ($configuration->name!==null) {
                $this->_name     = $configuration->name;
            }
            
            if ($configuration->identity!==null) {
                $this->_identity = $configuration->indentity;
            }

            if ($configuration->path!==null) {
                $this->_filename = "{$configuration->path}/{$this->_name}";
            }
            else {
                $this->_filename = "framework/log/{$this->_name}";
            }
            
            if ($configuration->transaction!==null) {
                $this->_transaction = $configuration->transaction;
            }
            else {
                $this->_transaction = false;
            }
            
            if ($configuration->append!==null) {
                $this->_append = $configuration->append;
            }
            if ($configuration->lock_mode!==null) {
                $this->_lock   = $configuration->lock_mode;
            }

        }
        
        $this->_open = false;

    }



    public function open() {
        if (!$this->_open) {
            if (!is_dir(dirname($this->_filename))) {
                mkdir(dirname($this->_filename),750);
            }
            if (is_file($this->_filename)) {
                $this->_fileHandler = fopen($this->_filename,($this->_append)?"a+":"w+");
            }
            else {
                $this->_fileHandler = fopen($this->_filename,"w+");
            }
        }

        if ($this->_fileHandler!==false) {
            $this->_open = true;
        }
        else {
            $this->_open = false;
        }
    }
    public function begin() {
        if (!$this->_transaction) {
            $this->_transaction = true;
        }
    }
    public function close() {
        if ($this->_open) {
            fclose($this->_fileHandler);
        }
        $this->_open = false;
    }

    public function end() {
        $this->_transaction = false;
    }

    public function flush() {
        if ($this->_transaction) {
            if (count($this->_messages)) {
                if (flock($this->_fileHandler, LOCK_EX|LOCK_NB)) {
                    foreach ($this->_messages as $message) {
                        $line = $this->_format($this->_identity,$message);
                        fwrite($this->_fileHandler,$line);
                    }
                    flock($this->_fileHandler, LOCK_UN);
                }

                 
            }
        }
    }

    public function commit() {
        $this->flush();
    }
    public function rollback() {
        $this->_messages = array_pop($this->_messages);
    }
    public function log(FW_Log_Message $message) {

        if (!$this->_open) {
            throw new Exception("Log is not open");
        }
        else {
            if ($this->_transaction===true)  {
                $this->_addMessage($message);
            }
            else {
                if (flock($this->_fileHandler, LOCK_EX|LOCK_NB)) {
                    $line = $this->_format($this->_identity,$message);
                    fwrite($this->_fileHandler,$line);
                    flock($this->_fileHandler, LOCK_UN);
                }
            }
             
        }
    }


};
?>