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
    class html {
        private static $baseURL;
        private static $urls = array();
        public static function selectTag($id, $options) {
            $tag = "";
            $tag .= "<select id=\"{$id}\" name=\"{$id}\">";
            if ( count($options) > 0 ) {
                foreach ( $options as $key => $value ) {
                    $tag .= "<option value=\"{$value}\">{$key}</option>";
                }
            }
            $tag .= "</select>";
            return $tag;
        }

        public static function ajax_link($url, $title, array $options = array(), $data = array(), $confirm = "", $method = "", $disable = "", $remote = true) {
            if ( count($data) ) {
                $data = http_build_query($data);
                $options["data-params"] = $data;
            }
            if ( !empty($disable) ) {
                $options["data-disable-with"] = $disable;
            }
            if ( !empty($confirm) ) {
                $options["data-confirm"] = $confirm;
            }
            if ( !empty($method) ) {
                $options["data-method"] = $method;
            }
            if ( $remote ) {
                $options["data-remote"] = true;
                $options["rel"] = "nofollow";
            }
            $options["href"] = $url;
            $options["title"] = $title;
            $tag = html::tag("a", $options, true);
            $tag .= $title;
            $tag .= html::close_tag("a");
            return $tag;
        }

        public static function link_to($url, $title, array $options = array()) {
            $options["href"] = $url;
            $options["title"] = $title;
            $tag = html::tag("a", $options, true);
            $tag .= $title;
            $tag .= html::close_tag("a");
            return $tag;
        }

        public static function link_to_internal($module, $controller, $action, $title, $parameters = array(), $internal = false, $options = array()) {
            $url = html::link_for_internal($module, $controller, $action, $parameters, $internal);
            $options["href"] = $url;
            $options["title"] = $title;
            $tag = html::tag("a", $options, true);
            $tag .= $title;
            $tag .= html::close_tag("a");
            return $tag;
        }

        public static function link_for_internal($module, $controller, $action, $parameters = array(), $internal = false) {
            $url = "";
            $baseURL = FW_Context::getInstance()->getParameter("baseURL");            
            $key = md5(("{$module}|{$controller}|{$action}|{$internal}|") . implode('|', $parameters));
            if ( self::$urls === null ) {
                self::$urls = array();
            }
            if ( isset(self::$urls[$key]) ) {
                $url = self::$urls[$key];
            }
            else {
                if ( !$module || !$controller || !$action ) {
                    return $baseURL;
                }
                $route = FW_Router::getInstance()->toURL($module, $controller, $action, $internal, $parameters);
                if ( $route != null ) {
                    $url = $route["url"];
                    if ( isset($route["parameters"]) && count($route["parameters"]) > 0 ) {
                        foreach ( $route["parameters"] as $key => $value ) {
                            $pattern = "#:{$key}+([/-])?#";
                            if ( preg_match($pattern, $url, $matches) ) {
                                $match = $matches[0];
                                $end = $match[strlen($match) - 1];
                                if ( ($end !== '-') && ($end !== '/') ) {
                                    $end = '';
                                }
                                $parameterValue = null;
                                if ( isset($parameters[$key]) ) {
                                    $parameterValue = "{$parameters[$key]}{$end}";
                                }
                                else {
                                    if ( FW_Request::getInstance()->getParameter($key) !== null ) {
                                        $parameterValue = FW_Request::getInstance()->getParameter($key) . $end;
                                    }
                                }
                                if ( $parameterValue !== null ) {
                                    $url = str_replace($match, $parameterValue, $url);
                                }
                            }
                        }
                    }
                    $url = "{$baseURL}{$url}";
                    self::$urls[$key] = $url;
                }
                else {
                    $url = $baseURL;
                }
            }
            return $url;
        }

        public static function tag($name, $options = array(), $open = false) {
            if ( !$name ) {
                return '';
            }
            $tag = "<{$name}";
            $tag .= html::generateTagOptions($options);
            if ( $open ) {
                $tag .= ">";
            }
            if ( !$open ) {
                $tag .= "/>";
            }
            return $tag;
        }

        public static function close_tag($name) {
            return html::tag("/{$name}", null, true);
        }

        public static function generateTagOptions($options) {
            $html = "";
            if ( count($options) > 0 ) {
                foreach ( $options as $key => $value ) {
                    $html .= " {$key}=\"{$value}\" ";
                }
            }
            return $html;
        }

        public static function unorderedList(array $items = array(), $ulStyle = "", $ulClass = "", $liStyle = "", $liClass = "") {
            $html = "";
            $html .= "<ul";
            if ( strlen($ulStyle) > 0 ) {
                $html .= " style=\"{$ulStyle}\" ";
            }
            if ( strlen($ulClass) ) {
                $html .= " class=\"{$ulClass}\" ";
            }
            $html .= ">";
            if ( count($items) ) {
                foreach ( $items as $item ) {
                    $html .= "<li> ";
                    if ( strlen($liStyle) > 0 ) {
                        $html .= " style=\"{$liStyle}\" ";
                    }
                    if ( strlen($liClass) > 0 ) {
                        $html .= " class=\"{$liClass}\" ";
                    }
                    $html .= " {$item} </li>";
                }
            }
            $html .= "</ul>";
            return $html;
        }

        public static function script_tag($script) {
            $baseURL = FW_Context::getInstance()->getParameter("baseURL");
            if ( strpos($script, "http") > 0 || strpos($script, "http") === false ) {
                $script = "{$baseURL}/{$script}";
            }
            $pattern = '(:([^/,-][^&]+))';
            if ( preg_match_all($pattern, $script, $matches) ) {
                foreach ( $matches[1] as $match ) {                    
                    $value = FW_Request::getInstance()->getParameter($match);
                    $search = ":{$match}";
                    $script = str_replace($search, $value, $script);
                }
            }
            $html = "<script type=\"text/javascript\" src=\"{$script}\"> </script>";
            return $html;
        }

        public static function style_tag($style, $media, $alternate = false) {
            $baseURL = FW_Context::getInstance()->getParameter("baseURL");
            if ( strpos($style, "http") > 0 || strpos($style, "http") === false ) {
                $style = "{$baseURL}/{$style}";
            }
            $pattern = '(:([^/,-]+))';
            if ( preg_match_all($pattern, $style, $matches) ) {
                foreach ( $matches[1] as $match ) {
                    $value = FW_Request::getInstance()->getParameter($match);
                    $search = ":{$match}";
                    $style = str_replace($search, $value, $style);
                }
            }
            $tag = "<link type=\"text/css\" href=\"{$style}\" ";
            if ( $alternate ) {
                $tag .= " rel=\"stylesheet alternate\" ";
            }
            else {
                $tag .= " rel=\"stylesheet\" ";
            }
            if ( is_array($media) ) {
                $tag .= "media=\"" . implode(',', $media) . "\"";
            }
            else {
                $tag .= "media=\"{$media}\"";
            }
            $tag .= "/>";
            return $tag;
        }

    }
?>