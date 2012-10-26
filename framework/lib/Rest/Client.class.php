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
 * Class that implements an object to make request to a server
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
 * Class that implements an object to make request to a server
 *
 * @package REST
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Rest_Client implements IComponent {


    /**
     * The Response
     *
     * @var FW_Rest_Response
     */
    protected $_response;

    /**
     * The URL to connect
     *
     * @var string
     */
    protected $_endpoint;

    /**
     * The type of the response
     * (XML,JSON, ... )
     *
     * @var string
     */
    protected $_type;

    /**
     * Array with the data to send
     * in case of POST and PUT
     *
     * @var array
     */
    protected $_post;

    /**
     * The HTTP verb to make
     * the connection
     * only alloweb GET,POST,
     * PUT and DELETE
     *
     * @var string
     */
    protected $_method;

    /**
     * An username (if service requires authentication)
     *
     * @var string
     */
    protected $_username;

    /**
     * An password (if service requires authentication)
     *
     * @var string
     */
    protected $_password;
    
    


    public function __construct(FW_Container_Parameter $parameters=null){
        $this->configure($parameters);
        $this->initialize(array());
    }

    /*
     * Configures the FW_Rest_Client
     *
     * @see framework/lib/interfaces/IComponent#configure($parameters)
     */
    public function configure(FW_Container_Parameter $parameters=null) {
        $endpoint = "";
        $username = "";
        $password = "";
        $method   = "GET";
        $type     = "JSON";
        $post     = array();

        if ($parameters!==null) {

            if ($parameters->hasParameter("endpoint")) {
                $endpoint = $parameters->endpoint;
            }

            if ($parameters->hasParameter("username")) {
                $username = $parameters->username;
            }

            if ($parameters->hasParameter("password")) {
                $username = $parameters->password;
            }

            if ($parameters->hasParameter("method")) {
                $method   = $parameters->method;
            }

            if ($parameters->hasParameter("data")) {
                $post     = $parameters->data;
            }

            if ($parameters->hasParameter("type")) {
                $type     = $parameters->type;
            }
        }

        if (empty($endpoint)) {
            throw new FW_Rest_Exception("Endpoint can't be empty. Aborting");
        }

        if (FW_Util_Array::notIn($method,array("GET","POST","PUT","DELETE"))) {
            throw new FW_Rest_Exception("Invalid HTTP verb. Verb must be GET, POST,PUT or DELETE. Aborting");
        }

        $this->_endpoint = $endpoint;
        $this->_method   = $method;
        $this->_username = $username;
        $this->_password = $password;
        $this->_post     = $post;
        $this->_type     = $type;

    }

    /*
     * Initializes the FW_Rest_Client
     *
     * @see framework/lib/interfaces/IComponent#initialize($arguments)
     */
    public function initialize(array $arguments=array()) {
        $this->_connect();
        $this->_setCurlOpts();        
    }

    /**
     * Sets the options for CURL
     *
     * @return void
     */
    private function _setCurlOpts() {

        $curlOptions = array(
        CURLOPT_URL            => '',
        CURLOPT_ENCODING       => "",
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => array(),
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 5.1; ca-ES; rv:1.8.1.15) Gecko/2008111317  Firefox/3.0.4",
        CURLOPT_URL            => $this->_endpoint
        );

        switch (strtoupper($this->_method)) {
            case "GET":
            default:
                $curlOptions[CURLOPT_HTTPGET]       = true;
                break;

            case "POST":
                $fields                             = (is_array($this->_post)) ? http_build_query($this->_post) : $this->_post;
                $curlOptions[CURLOPT_POST]          = true;
                $curlOptions[CURLOPT_POSTFIELDS]    = $fields;

                break;

            case "PUT":
                $fields                             = (is_array($this->_post)) ? http_build_query($this->_post) : $this->_post;
                $curlOptions[CURLOPT_CUSTOMREQUEST] = "PUT";
                $curlOptions[CURLOPT_HTTPHEADER]    = array("Content-Length: ".strlen($fields));
                $curlOptions[CURLOPT_POSTFIELDS]    = $fields;                
                break;

            case "DELETE":
                $curlOptions[CURLOPT_CUSTOMREQUEST] = "DELETE";
                break;

        };

        if (!empty($this->_username) && !empty($this->_password)) {
            $curlOptions[CURLOPT_USERPWD]  = "{$this->_username}:{$this->_password}";
            $curlOptions[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
        }
        
        if (!empty($this->_type)) {
            $header = "";
            $type   = strtoupper($this->_type);
            if ($type==="XML") {
                $header = "application/xml";
            }
            if ($type==="json") {
                $header = "application/xml";
            }
            curl_setopt($this->_connection,CURLOPT_HTTPHEADER,array("Accept: {$header}"));
        }

        curl_setopt_array($this->_connection,$curlOptions);
    }

    /**
     * Initializes the CURL connection
     *
     * @return void
     */
    final private function _connect() {
        $this->_connection = curl_init();        
    }

    /**
     * Closes the active connection
     *
     * @return void
     */
    final private function _close() {
        curl_close($this->_connection);
        $this->_connection = null;
    }


    /**
     * Creates a new blank response
     *
     * @return FW_Rest_Response
     */
    final private function _createResponse() {
        $responseClass = "";
        $response      = null;
        $type          = strtoupper($this->_type);
        if ($type==="XML" || $type==="JSON") {
            $responseClass = "FW_Rest_Response{$type}";
        }
        else {
            $responseClass = "FW_Rest_Response";
        }        
        $response      = new $responseClass();
        return $response;
    }

    /**
     * Executes the CURL request and generates
     * the response
     *
     * @return bool
     */
    public function exec() {

        $response   = $this->_createResponse();
        $parameters = new FW_Container_Parameter();
               
        
        $rawData  = curl_exec($this->_connection);        
        $info     = curl_getinfo($this->_connection);
        $errno    = curl_errno($this->_connection);
        $error    = curl_error($this->_connection);
        
        
        $parameters->setParameter("response",$rawData);
        $parameters->setParameter("info",$info);
        $parameters->setParameter("errno",$errno);
        $parameters->setParameter("error",$error);

        $response->configure($parameters);
        $response->initialize(array());

        $this->_response = $response;        

        if (curl_errno($this->_connection)) {
            $this->_close();
            return false;
        }
        $this->_close();
        return true;
    }

    /**
     * Gets the response
     *
     * @return FW_Rest_Response
     */
    public function getResponse() {
        if ($this->_response===null) {            
            if ($this->exec()) {
                return $this->_response;
            }
        }
        return $this->_response;
    }

};
?>