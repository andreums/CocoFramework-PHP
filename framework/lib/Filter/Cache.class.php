<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Filter_Cache extends FW_Filter {

    public function execute (FW_Filter_Chain $filterChain) {
        $cacheId    = "";
        $lifetime   = 0;
        $context    = $this->getContext();
        $handler    = FW_Cache::getInstance();

        $route      = $context->router->route;
        $parameters = $context->router->parameters;
        if ($parameters===null) {
            $parameters = array();
        }        
        $type             = $context->router->type;
        $context->setParameter("cache",new FW_Container_Parameter());
                    
        if ( ($route!==null) && ($route["cache"]!==false)) {
	       $type       = $route["type"];
	       $cache      = $route["cache"];            
	
	       if ( in_array($type,array("app","xml","json","mime") ) ) {
	           $module     = $route["module"];
	           $controller = $route["controller"];
	           $action     = $route["action"];
	           $internal   = $route["internal"];
                    
	           $cacheId    = "{$module}|{$controller}|{$action}|{$internal}|".implode('|',$parameters);
	           $cacheId    = md5($cacheId);                    
            }
	        if ($type==="plugin") {
	            $plugin     = $route["plugin"];
	            $action     = $route["action"];
	            $cacheId    = "{$plugin}|{$action}|".implode('|',$parameters);	                	                
	            $cacheId    = md5($cacheId);	                
            }
	        if ($type==="static") {
	            // TODO: Write a cache handler for static routes 
	        }
	        
	        $lifetime                 = $route["cache"];
            $context->cache->id       = $cacheId;
            $context->cache->lifetime = $lifetime;    
	        $data                     = $handler->get($cacheId,"application");
	        	        	        
            if ($data!==null) {
                if (!$data->hasExpired()) {                        
	               $context->cache->data  =  $data->getContents();                                                
                }
	            else {
	                $context->cache->data = null;
	            }
	        }	                    
            else {
                $context->cache->data = null;
            }
        }
        else {
            //$context->cache = false;
        }        
        $filterChain->execute();
        return true;


    }

};
?>