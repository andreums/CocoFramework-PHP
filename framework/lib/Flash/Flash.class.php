<?php
    /**
     *  Andrés Ignacio Martínez Soto
     *  andresmartinezsoto@gmail.com
     * Coco-PHP
     * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto
     * The above copyright notice and this permission notice shall be included in
     * all copies or substantial portions of the Software.
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
     * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
     * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
     * DEALINGS IN THE SOFTWARE.
     */
    class FW_Flash extends FW_Singleton {
        private $_objects;
        private $_messages;
        private $_oldObjects;
        private $_oldMessages;
        public function __construct() {
            $this->_oldObjects = $this->_getSessionObjects();
            $this->_oldMessages = $this->_getSessionMessages();
            $this->_messages = array("success" => array(), "notice" => array(), "error" => array(), "info" => array());
            $this->_objects = array();
        }

        public function __destruct() {
            $this->_setSessionMessages();
            $this->_setSessionObjects();
            register_shutdown_function(array($this, "_setSessionMessages"));
            register_shutdown_function(array($this, "_setSessionObjects"));
        }

        private function _getSessionMessages() {
            $messages = array();
            if ( FW_Session::issetData("session_flash_messages", "flash") ) {
                $messages = FW_Session::get("session_flash_messages", "flash");
            }
            FW_Session::unsetData("session_flash_messages", "flash");
            return $messages;
        }

        private function _getSessionObjects() {
            $objects = array();
            if ( FW_Session::issetData("session_flash_objects", "flash") ) {
                $objects = unserialize(FW_Session::get("session_flash_objects", "flash"));
            }
            FW_Session::unsetData("session_flash_objects", "flash");
            return $objects;
        }

        private function _setSessionMessages() {
            $messages = $this->_messages;
            //FW_Session::unsetData("session_flash_messages","flash");
            FW_Session::set("session_flash_messages", $messages, "flash");
        }

        private function _setSessionObjects() {
            $objects = serialize($this->_objects);
            //FW_Session::unsetData("session_flash_objects","flash");
            FW_Session::set("session_flash_objects", $objects, "flash");
        }

        public function addInfo($message) {
            $this->_addMessage("info", $message);
        }

        public function addError($message) {
            $this->_addMessage("error", $message);
        }

        public function addNotice($message) {
            $this->_addMessage("notice", $message);
        }

        public function addSuccess($message) {
            $this->_addMessage("success", $message);
        }

        private function _addMessage($type, $message) {
            $this->_messages[$type][] = $message;
        }

        public function addObject($name, $value) {
            $this->_objects[$name] = $value;
        }

        public function getObject($name) {
            $objects = $this->_oldObjects;
            $objects = array_merge($objects, $this->_objects);
            if ( isset($objects[$name]) ) {
                return $objects[$name];
            }
        }

        public function getObjects() {
            $objects = $this->_oldObjects;
            $objects = array_merge($objects, $this->_objects);
            return $objects;
        }

        public function getFlashMessages() {
            return $this->_oldMessages;
        }

        public function displayMessages() {
            $code = "";
            $code .= $this->displayErrorMessages();
            $code .= $this->displayInfoMessages();
            $code .= $this->displayNoticeMessages();
            $code .= $this->displaySuccessMessages();
            return $code;
        }

        private function _getMessageCode($type, $message) {
            $type = strtolower($type);
            $code = "";
            $code .= "<div class=\"message {$type}\">";
            $code .= "<p>{$message}</p>";
            $code .= "</div>";
            return $code;
        }

        public function displayErrorMessages() {
            $code = "";
            $messages = array();
            if ( isset($this->_oldMessages["error"]) ) {
                $messages = array_merge($this->_oldMessages["error"], $messages);
            }
            $messages = array_merge($this->_messages["error"], $messages);
            $messages = array_unique($messages, SORT_STRING);
            if ( count($messages) ) {
                foreach ( $messages as $message ) {
                    $code .= $this->_getMessageCode("error", $message);
                }
            }
            return $code;
        }

        public function displaySuccessMessages() {
            $code = "";
            $messages = array();
            if ( isset($this->_oldMessages["success"]) ) {
                $messages = array_merge($this->_oldMessages["success"], $messages);
            }
            $messages = array_merge($this->_messages["success"], $messages);
            $messages = array_unique($messages, SORT_STRING);
            if ( count($messages) ) {
                foreach ( $messages as $message ) {
                    $code .= $this->_getMessageCode("success", $message);
                }
            }
            return $code;
        }

        public function displayNoticeMessages() {
            $code = "";
            $messages = array();
            if ( isset($this->_oldMessages["notice"]) ) {
                $messages = array_merge($this->_oldMessages["notice"], $messages);
            }
            $messages = array_merge($this->_messages["notice"], $messages);
            $messages = array_unique($messages, SORT_STRING);
            if ( count($messages) ) {
                foreach ( $messages as $message ) {
                    $code .= $this->_getMessageCode("notice", $message);
                }
            }
            return $code;
        }

        public function displayInfoMessages() {
            $code = "";
            $messages = array();
            if ( isset($this->_oldMessages["info"]) ) {
                $messages = array_merge($this->_oldMessages["info"], $messages);
            }
            $messages = array_merge($this->_messages["info"], $messages);
            $messages = array_unique($messages, SORT_STRING);
            if ( count($messages) ) {
                foreach ( $messages as $message ) {
                    $code .= $this->_getMessageCode("info", $message);
                }
            }
            return $code;
        }

        public function hasObjects() {
            $objects = $this->_oldObjects;
            $objects = array_merge($objects, $this->_objects);
            return (count($objects) > 0);
        }

        public function hasMessages() {
            $messages = $this->_mergeMessages();
            foreach ( array_keys($messages) as $key ) {
                if ( count($messages[$key]) > 0 ) {
                    return true;
                }
            }
            return false;
        }

        private function _mergeMessages() {
            $mergedMessages = array();
            $keys = array("success", "notice", "error", "info");
            if (count(array_keys($this->_oldMessages))===count(array_keys($this->_messages))) {
                foreach ( $keys as $key ) {
                   $mergedMessages[$key] = array_merge($this->_oldMessages[$key], $this->_messages[$key]);
                }                
            }
            return $mergedMessages;
        }

    };
?>