<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class FW_Soap_Client implements IComponent {


    /**
     * The location of the WSDL file
     *
     * @var string
     */
    private $_wsdl;

    /**
     * The SoapClient
     *
     * @var SoapClient
     */
    private $_client;


    /**
     * The username to use this webservice
     *
     * @var string
     */
    private $_username;

    /**
     * The password to use this webservice
     *
     * @var string
     */
    private $_password;

    /**
     * An array of options to use with the client
     *
     * @var array
     */
    private $_options;

    public function __construct($wsdl=null,$username="",$password="",array $options=array())  {
        $parameters = new FW_Container_Parameter();

        if ($wsdl!==null) {
            $parameters->wsdl = $wsdl;
        }

        if (empty($options)) {
            $options = array();
        }

        if (!empty($username)) {
            $options["login"]    = $username;
        }

        if (!empty($password)) {
            $options["password"] = $password;
        }
           
        if (!empty($options) && isset($options["cache"])) {
            $value = true;
            if ($options["cache"]===true) {
                $value = 1;
            }
            else {
                $value = 0;
            }
            ini_set("soap.wsdl_cache_enabled",$value);
            
        }

        $parameters->options = $options;

        $this->configure($parameters);
        $this->initialize(array());
    }

    /**
     * Configures the SOAP client
     *
     * @param FW_Container_Parameter $parameters Parameters to configure
     *
     * @return void
     */
    public function configure(FW_Container_Parameter $parameters=null) {

        if ($parameters!==null) {

            if ($parameters->options!==null) {
                $this->_options = $parameters->options;
            }

            if ($parameters->wsdl!==null) {
                $this->_wsdl    = $parameters->wsdl;
            }

        }


    }

    /**
     * Initializes the SOAP client
     *
     * @param array $arguments
     */
    public function initialize(array $arguments=array()) {

        $client        = null;
        if ($this->_wsdl!==null) {
            $client = new SoapClient($this->_wsdl,$this->_options);
        }
        else {
            $client = new SoapClient(null,$this->_options);
        }
        $this->_client = $client;

    }


    /**
     * Calls an operation into the webservice
     * 
     * @param string $operation The name of the operation
     * @param array $parameters An array of parameters
     * 
     * @return mixed
     */
    public function __call($operation,array $parameters=array())  {

        if ($this->_client===null)  {
            throw new FW_Soap_Exception("SOAP client object is not initialised");
        }

        $result = $this->_client->__soapCall($operation,$parameters);
        if (is_soap_fault($result)) {
            throw new Exception ("SOAP Fault: code: {$result->faultcode} | Message: {$result->faultstring}");
        }
        return $result;
    }
    
    /**
     * Obtains all the available operations in the SOAP service
     * 
     * @return array
     */
    public function getAvailableOperations() {
        return $this->_client->__getFunctions();
    }
    
    /**
     * Obtains the last request of this client
     * 
     * @return mixed
     */
    public function getLastRequest() {
        return $this->_client->__getLastRequest();
    }
    
    /**
     * Obtains the last response sent to this client
     * 
     * @return mixed
     */
    public function getLastResponse() {
        return $this->_client->__getLastResponse();
    }
    
    /**
     * Gets the complex types of a webservice
     * 
     * @return mixed
     */
    public function getComplexTypes() {
        return $this->_client->__getTypes();
    }

};
?>