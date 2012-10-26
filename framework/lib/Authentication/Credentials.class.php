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
 * A class to represent the credentials of an authentication system
 *
 * PHP Version 5.3
 * 
 * @package  Authentication
 * @author   Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 * @license  MIT+LGPL
 * @link     http://www.andresmartinezsoto.es
 *
 */

/**
 * A class to represent the credentials of an authentication system
 *
 * @package Authentication
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Authentication_Credentials {

    /**
     * The username
     * 
     * @var string
     */
    private $_username;
    
    /**
     * The password
     * 
     * @var string
     */
    private $_password;
    
    /**
     * The Consumer Key
     * (to be used with OAuth)
     * @var string
     */
    private $_consumerKey;
    
    /**
     * The Consumer Secret
     * (to be used with OAuth)
     * 
     * @var string
     */
    private $_consumerSecret;


    
    /**
     * The constructor of the credentials
     * 
     * @param string $username The username
     * @param string $password The password
     * @param string $consumerKey The consumer key (for OAuth)
     * @param string $consumerSecret The consumer secret key (for OAuth)
     * @return void
     */
    public function __construct($username="",$password="",$consumerKey="",$consumerSecret="") {
        $this->_username       = $username;
        $this->_password       = $password;
        $this->_consumerKey    = $consumerKey;
        $this->_consumerSecret = $consumerSecret;
    }
    
    
    /**
     * Gets the username
     * 
     * @return string
     */
    public function getUsername() {
        return $this->_username;
    }

    
    /**
     * Gets the password of the user
     * 
     * @return string
     */
    public function getPassword() {
        return $this->_password;
    }


    /**
     * Gets the consumer key
     * (To be used with OAuth)
     * 
     * @return string
     */
    public function getConsumerKey() {
        return $this->_consumerKey;
    }

    /**
     * Gets the consumer secret key
     * (To be used with OAuth)
     * 
     * @return string
     */
    public function getConsumerSecret() {
        return $this->_consumerSecret;
    }

};
?>