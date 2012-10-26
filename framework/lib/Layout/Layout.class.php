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
    class FW_Layout {
        private $_context;
        private $_template;
        private $_slots;
        private $_route;
        private $_contents;
        private $_controller;
        private $_cachedSlots;
        public function __construct() {
            $this->_controller = FW_Context::getInstance()->router->controller;
        }

        private function _isInternal() {
            if ( $this->_route["type"] === "app" ) {
                return ($this->_route["internal"] === true);
            }
            return false;
        }

        protected function renderGlobalView($view, array $variables = array()) {
            if ( $this->_isInternal() ) {
                $path = "framework/app/view/{$view}.php";
            }
            else {
                $path = "app/view/{$view}.php";
            }
            if ( is_file($path) ) {
                extract($variables);
                include $path;
            }
            else {
                throw new FW_Layout_Exception("Couldn't load view {$view} ");
            }
        }

        protected function renderView($view, $variables = null) {
            if ( strlen($view) === 0 ) {
                if ( $variables !== null ) {
                    if ( is_array($variables) ) {
                        extract($variables);
                    }
                }
            }
            else {
                $path = "";
                if ( $this->_route["type"] === "app" || $this->_route["type"] === "mime" ) {
                    $module = $this->_route["module"];
                    if ( $this->_isInternal() ) {
                        $path = "framework/app/modules/{$module}/view/{$view}.php";
                    }
                    else {
                        $path = "app/modules/{$module}/view/{$view}.php";
                    }
                }
                if ( $this->_route["type"] === "plugin" ) {
                    $plugin = $this->_route["plugin"];
                    $path = "app/lib/plugins/{$plugin}/view/{$view}.php";
                }
                if ( $variables !== null ) {
                    if ( is_array($variables) ) {
                        extract($variables);
                    }
                }
                if ( is_file($path) ) {
                    include $path;
                }
                else {     
                    throw new FW_Layout_Exception("Couldn't load view {$view} ");
                }
            }
        }

        private function hasSlot($name) {
            return (isset($this->_slots[$name]));
        }

        private function _renderSlot($slot, $vars, $global) {
            ob_start();
            if ( $global === true ) {
                $this->renderGlobalView($slot, $vars);
            }
            else {
                $this->renderView($slot, $vars);
            }
            $contents = ob_get_clean();
            return $contents;
        }

        private function _setCacheSlot($name, $contents) {
            $cachedSlots = array();
            if ( $this->_context->cache->slots === null ) {
                $this->_context->cache->slots = array();
            }
            else {
                $cachedSlots = $this->_context->cache->slots;
            }
            $cachedSlots[$name] = $contents;
            $this->_context->cache->slots = $cachedSlots;
        }

        private function getSlot($name) {
            $contents = "";
            if ( isset($this->_slots[$name]) ) {
                $slot = $this->_slots[$name]["view"];
                $vars = $this->_slots[$name]["variables"];
                $global = $this->_slots[$name]["global"];
                $cacheable = $this->_slots[$name]["cacheable"];
                if ( $cacheable === true ) {
                    if ( $this->_cachedSlots !== null ) {
                        if ( isset($this->_cachedSlots[$name]) ) {
                            $contents = $this->_cachedSlots[$name];
                        }
                        else {
                            $contents = $this->_renderSlot($slot, $vars, $global);
                            $this->_setCacheSlot($name, $contents);
                        }
                    }
                    else {
                        $contents = $this->_renderSlot($slot, $vars, $global);
                        $this->_setCacheSlot($name, $contents);
                    }
                }
                else {
                    $contents = $this->_renderSlot($slot, $vars, $global);
                }
            }
            return $contents;
        }

        public function configure(FW_Container_Parameter $parameters = null) {
            $this->_template = "";
            $this->_slots = array();
            $this->_context = FW_Context::getInstance();
            $this->_route = $this->_context->router->route;
            if ( $this->_context->cache !== false ) {
                if ( $this->_context->cache->data !== null ) {
                    $this->_cachedSlots = json_decode($this->_context->cache->data, true);
                }
            }
            if ( $parameters !== null ) {
                if ( $parameters->hasParameter("template") ) {
                    $this->_template = $parameters->template;
                }
                if ( $parameters->hasParameter("slots") ) {
                    $this->_slots = $parameters->slots;
                }
            }
            $this->_loadTemplate();
        }

        private function _loadTemplate() {
            $this->_contents = "";
            $template = $this->_template;
            ob_start();
            if ( is_file($template) ) {
                require_once $template;
            }
            $contents = ob_get_contents();
            ob_clean();
            $this->_contents = $contents;
        }

        /**
         * Converts a text for display
         *
         * @param string $data The text to convert
         *
         * @return string
         */
        protected function display($data) {
            return FW_Util_String::getInstance()->convertForOutput($data);
        }

        public function getContents() {
            return $this->_contents;
        }

        public function __call($method, $arguments) {
            return call_user_func_array(array($this->_controller, $method), $arguments);
        }

    };
?>