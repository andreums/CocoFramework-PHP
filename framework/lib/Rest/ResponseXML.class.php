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
 * Class that implements a response to a REST request an provides methods
 * to provide XML data decoding ability
 *
 * @package REST
 * @author  Andrés Ignacio Martínez Soto <andresmartinezsoto@gmail.com>
 *
 */
class FW_Rest_ResponseXML extends FW_Rest_Response {
    
    /* 
     * Gets the body of the response as a SimpleXMLElement
     * 
     * @see framework/lib/Rest/FW_Rest_Response#getBody()
     * 
     * @return SimpleXMLElement
     */
    public function getBody() {
        return new SimpleXMLElement($this->_body);
    }
    
    /**
     * Gets the original XML data without decoding it
     * 
     * @return string
     */
    public function getXML() {
        return ($this->_body);
    }
};
?>