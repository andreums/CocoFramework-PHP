<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_HttpResponse extends FW_Singleton {

    private $_headers;
    private $_cache;

    public function __construct() {
        $this->_headers = array();
        $this->_cache   = array();
    }

    public function addCacheHeader($control) {
        $header           = "Cache control: {$control} ";
        $this->_headers []= $header;
    }

    public function addContentLength($length) {
        $header           = "Content-length: {$length} ";
        $this->_headers []= $header;
    }
    public function addNoCacheHeaders() {
        $header         = "Expires: Mon, 26 Jul 1997 05:00:00 GMT";
        $this->_cache []= $header;
        $header         = "Cache-Control: no-cache";
        $this->_cache []= $header;
        $header         = "Pragma: no-cache";
        $this->_cache []= $header;
    }

    public function addExpiryHeader($seconds) {
        $header          = "Expires: ".gmdate("D, d M Y H:i:s", time()+$seconds)." GMT";
        $this->_cache [] = $header;
    }

    public function addMimeHeader($type) {
        $this->addHeader("Content-Type: {$type}");
    }

    public function addForceDownloadHeader($filename) {
        $this->addHeader("Content-Disposition:attachment;filename='{$filename}'");
    }

    public function addBasicAuthenticationHeaders($realm) {
        $header = "WWW-Authenticate: Basic realm=\"{$realm}\"";
        $this->addHeader($header);
        $header = "HTTP/1.0 401 Unauthorized";
        $this->addHeader($header);
    }

    public function addHeader($header) {
        $this->_headers []= $header;
    }

    public function render() {
        if (!headers_sent()) {
            foreach ($this->_headers as $header) {
                header($header,false);
            }
            foreach ($this->_cache as $header) {
                header($header,false);
            }
        }
    }

    public static function redirect($url) {
        header("Location: {$url}");
    }
};
?>