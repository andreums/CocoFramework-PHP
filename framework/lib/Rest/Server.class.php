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
 * Class that implements an object to handle the REST requests and retrieve the 
 * data of those requests
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
 * Class that implements an object to handle the REST requests and retrieve the 
 * data of those requests
 *
 * @package REST
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Rest_Server extends FW_Singleton implements IComponent {
    

    /**
     * An access to the Request Component
     * 
     * @var FW_Request
     */
    private $_request;
    
    /**
     * RAW HTTP contents 
     * 
     * @var mixed
     */
    private $_contents;


    /**
     * Constructs this component
     *
     *  @return void
     */
    public function __construct() {        
        $this->configure(null);
        $this->initialize(null);
    }   
    

    /**
     * Configures the REST server
     * 
     * @param FW_Container_Parameter $parameters not used
     * 
     * @return void
     */
    public function configure(FW_Container_Parameter $parameters=null) {
        $this->request();        
    }
    
    /**
     * Initializes the REST server
     * 
     * @param array $arguments not used
     * 
     * @return void
     */
    public function initialize(array $arguments=null) {
        if (in_array($this->request()->getMethod(),array("GET","POST,PUT,DELETE"))) {
            $this->contents();
        }
    }
    
    
    /**
     * An access to the Request component
     * 
     * @return FW_Request
     */
    public function request() {
        if ($this->_request===null) {
            $this->_request = new FW_Request();
        }
        return $this->_request;
    }

    /**
     * Gets the raw data of the contents submitted via POST/PUT
     * methods
     *
     * @return mixed
     */
    public function contents() {        
        if ($this->_contents===null) {
            if ($this->isPUT()) {
                $this->_contents = file_get_contents("php://input");                
            }
            if ($this->isPOST()) {                                
                $this->_contents = $this->request()->getPostParameters();                
            }
                            
        }
        return $this->_contents;
    }

    /**
     * Tries to gets the contents (POST/PUT) from XML format
     * to array
     *
     * @return SimpleXMLElement
     */
    public function getContentsAsXML() {
        $contents = $this->contents();        
        if (is_array($contents)) {            
            foreach ($contents as $key=>$value) {
                $contents[$key] = @simplexml_load_string($contents[$key]);                
            } 
        }
        else {
            $contents = trim($contents);
            $contents = @simplexml_load_string($contents);
        }        
        return $contents;
    }

    /**
     * Tries to gets the contents (POST/PUT) from JSON format
     * to array
     *
     * @return array
     */
    public function getContentsAsJSON() {
        $contents = $this->contents();
        if (is_array($contents)) {            
            foreach ($contents as $key=>$value) {
                $contents[$key] = @json_decode($contents[$key],true);          
            } 
        }
        else {
            $contents = @json_decode($contents,true);
        }
        return $contents;
    }


    /**
     * Checks if this request is GET
     *
     * @return boolean
     */
    public function isGET() {
        return ($this->_request->getMethod()==="GET");
    }

    /**
     * Checks if this request is PUT
     *
     * @return boolean
     */
    public function isPUT() {
        return ($this->_request->getMethod()==="PUT");
    }

    /**
     * Checks if this request is POST
     *
     * @return boolean
     */
    public function isPOST() {
        return ($this->_request->getMethod()==="POST");
    }

    /**
     * Checks if this request is DELETE
     *
     * @return boolean
     */
    public function isDELETE() {
        return ($this->_request->getMethod()==="DELETE");
    }


    /**
     * Gets the format to serve the REST request
     *
     * @return string
     */
    public function getFormat() {
        $format = $this->_request->getGetParameter("format");
        if ($format===null) {
            $format = "xml";
        }
        return $format;
    }

};
?>