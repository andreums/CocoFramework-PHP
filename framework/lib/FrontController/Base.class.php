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
 * @author andreu
 * 
 * @abstract
 *
 */
abstract class FW_FrontController_Base implements IComponent {

    protected $_route;
    protected $_cache;

    public function __construct() {
        $this->configure(null);
        $this->initialize(null);
    }

    /**
     * Configures the Front Controller
     * 
     * @param FW_Container_Parameter $parameters 
     * 
     * @return void
     */
    public function configure(FW_Container_Parameter $parameters=null) {
        $this->_cacheHandler     = FW_Cache::getInstance();
    }

    public function initialize(array $arguments=null) {
        $params  = new FW_Container_Parameter();
        
        $filters = FW_Config::getInstance()->get("filter.global.default");
        $params->filters = $filters;
        $chain   = new FW_Filter_Chain();
        $chain->configure($params);
        $result = $chain->execute();
        
        if ($result===true) {
            $this->_prepare();
        }
        else {
            $this->_notFound();
        }

    }

    protected function _prepare() {
        $context = $this->_getContext();
        $route   = $context->router->route;
        $cache   = $context->cache;

        $this->_route = $route;
        $this->_cache = $cache;

    }

    protected function _getContext() {
        return FW_Context::getInstance();
    }

    /**
     * Sends the HTTP Response headers
     * 
     * @abstract
     * @return void
     *  
     */
    abstract protected function _sendHeaders();
    abstract protected function _notFound();
    abstract public function render();

    protected function _setCachedData($contents) {
        $this->_cacheHandler->remove($this->_cache->id,"application");        
        if ($this->_cacheHandler->save($this->_cache->id,"application",$contents,$this->_cache->lifetime)) {
            return true;
        }
        return false;
    }

};
?>