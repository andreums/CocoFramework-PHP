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
 * Class that implements a response to a REST request
 *
 * PHP Version 5.3
 *
 * @package  REST
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * Class that implements a response to a REST request
 *
 * @package REST
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Rest_Response implements IComponent {

    /**
     * Full information about
     * the request raw text
     *
     * @var string
     */
    protected $_response;

    /**
     * Information about the response
     *
     * @var array
     */
    protected $_info;

    /**
     * HTTP Headers of this response
     *
     * @var array
     */
    protected $_headers;

    /**
     * The body of the response
     *
     * @var string
     */
    protected $_body;

    /**
     * The errors (if any)
     * of the response
     *
     * @var string
     */
    protected $_error;

    /**
     * A flag to indicate if the response
     * has been successful
     *
     * @var bool
     */
    protected $_success;

    /**
     * HTTP Error codes for response
     *
     * @var array
     */
    protected $_codes;

    /**
     * Constructs this component
     *
     * @return void
     */
    public function __construct() {}

    /*
     * Configures this component
     * (non-PHPdoc)
     * @see framework/lib/interfaces/IComponent#configure($parameters)
     */
    public function configure(FW_Container_Parameter $parameters=null) {
        $this->_body         = null;
        $this->_error        = null;
        $this->_responseInfo = null;
        $this->_response     = null;

        $this->_codes        = array(
        0   => "buuuu",
        100 => "Continue",
        101 => "Switching Protocols",
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",
        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        306 => "(Unused)",
        307 => "Temporary Redirect",
        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Timeout",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Request Entity Too Large",
        414 => "Request-URI Too Long",
        415 => "Unsupported Media Type",
        416 => "Requested Range Not Satisfiable",
        417 => "Expectation Failed",
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Timeout",
        505 => "HTTP Version Not Supported"
        );

        if ($parameters!==null) {
            $this->_response                        = $parameters->response;
            $this->_responseInfo                    = $parameters->info;
            $this->_responseInfo["errors"]["errno"] = $parameters->errno;
            $this->_responseInfo["errors"]["error"] = $parameters->error;
        }

        return $this;
    }

    /*
     * Initializes this component
     * (non-PHPdoc)
     * @see framework/lib/interfaces/IComponent#initialize($arguments)
     */
    public function initialize(array $arguments=array()) {
        $this->_render();
    }


    /**
     * Renders the response
     *
     * @return $this
     */
    protected function _render(){

        if ( $this->_responseInfo["errors"]["errno"]!==0 ) {
            $this->_success = false;
            $this->_error = $this->_responseInfo["errors"]["error"];
        }
        else if ( $this->_responseInfo["http_code"]!==200 ) {

            $this->_success = false;
            $this->_error   = $this->_codes[$this->_responseInfo["http_code"]];
        }
        else {
            $header = substr($this->_response, 0, $this->_responseInfo['header_size']);
            $body   = substr($this->_response, $this->_responseInfo['header_size']);

            $this->_body    = $body;
            $this->_headers = $header;
        }
        return $this;
    }


    /**
     * Returns true if the response
     * has been successful (w/o errors)
     *
     * @return bool
     */
    public function isSuccessful() {
        return ($this->_success == true);
    }

    /**
     * Gets the headers of the response
     *
     * @return mixed
     */
    public function getHeaders(){
        return $this->_headers;
    }

    /**
     * Gets the body of the response
     *
     * @return mixed
     */
    public function getBody() {
        return $this->_body;
    }

    /**
     * Gets the response
     *
     * @return mixed
     */
    public function getResponse(){
        return $this->_response;
    }

    /**
     * Gets the information about a curl
     * request
     *
     * @return array
     */
    public function getInfo(){
        return $this->_info;
    }

    /**
     * Gets the errors of a curl
     * request
     *
     * @return mixed
     */
    public function getError(){
        return $this->_error;
    }


};
?>