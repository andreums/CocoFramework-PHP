<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_Config_Handler_Environment extends FW_Config_Handler {
    
    private static $_environmentInUse;
    
    public function __construct() {
        parent::__construct();
        self::$_environmentInUse = $this->getCurrentEnvironment();
				
    }

    /* (non-PHPdoc)
     * @see framework/lib/Config/FW_Config_Handler#info()
     */
    public function info() {
        return "EnvironmentConfig";
    }
	
	public function existsEnvironment($name) {
		$env = $this->getParameter("sections.{$name}");
        if ($env!==null) {
        	return true;
        }
		return false;
	}

    /**
     * Gets information about the current environment in use
     *
     * @return mixed
     */
    public function getCurrentEnvironment() {
        $env = $this->getParameter("global.environmentInUse");						
        if ( ($env!==null) && ($this->existsEnvironment($env)) ) {
            $database   = $this->getParameter("sections.{$env}.database");
            $log        = $this->getParameter("sections.{$env}.log");
            $debug      = $this->getParameter("sections.{$env}.debug");
            $error      = $this->getParameter("sections.{$env}.error");
            $cache      = $this->getParameter("sections.{$env}.cache");			
            return array("environment"=>$env,"database"=>$database,"log"=>$log,"debug"=>$debug,"error"=>$error,"cache"=>$cache);
        }
		else {
			throw new FW_Config_Exception("Couldn't load Environment {$env} data");
		}
        return null;
    }

    /**
     * Gets an environment
     *
     * @param string $name The name of the environment
     *
     * @return mixed
     */
    public function getEnvironment($name) {
        $env = $this->getParameter("sections.{$name}");
        return $env;
    }

    /**
     * Gets the database configuration
     *
     * @return array
     */
    public function getDatabaseInUse() {
        $database    = array();
        $environment = self::$_environmentInUse;		
        if ($environment!==null) {
            $dbEnv = $environment["database"];			
            foreach ($dbEnv as $db) {
                $database [$db]= $this->_config->database->getDataBase($db);
            }
        }        
        return $database;
    }

    /**
     * Gets the Cache configuration
     *
     * @return array
     */
    public function getCacheInUse() {
        $cache              = array();
        $environment = self::$_environmentInUse;

        if ($environment!==null) {
            $cacheEnv = $environment["cache"];
            foreach ($cacheEnv as $aux) {
                $cache [$aux]= (object) $this->_config->cache->getCache($aux);
            }
        }
        return $cache;

    }

};
?>