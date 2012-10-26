<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class FW_FrontController implements IComponent {

	private $_service;
	private $_router;
	private $_result;
	private $_route;
	private $_context;

	private static $_isConfigured;
	private static $_isInitialized;
	private static $_instance;

	public static function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct($service = "app") {	    
		$parameters = new FW_Container_Parameter();
		$parameters -> setParameter("service", $service);
		$this -> configure($parameters);
		$this -> initialize(array());
	}

	public function configure(FW_Container_Parameter $parameters = null) {
		if ($parameters -> hasParameter("service")) {
			$service = $parameters -> getParameter("service");
			$this -> setService($service);
		}
	}

	public function initialize(array $arguments = array()) {
		$this -> _router = new FW_Router($this -> _service);
	}

	public function setService($service) {
		if (in_array($service, array("app", "cron"))) {
			$this -> _service = $service;
			return true;
		}
		return false;
	}

	public function getService() {
		return $this -> _service;
	}

	public function render() {
		$result = $this -> _router -> enroute();
		if ($result === true) {
			$type = ucfirst($this -> getContext() -> router -> type);
			$class = "FW_FrontController_{$type}";
			$controller = new $class();
			$controller -> render();
		} else {

			$service = $this -> _service;
			$type = $this -> getContext() -> router -> type;

			if ($service === "app") {
				$this -> _forward404();
			}
            else if ($service === "cron") {
				$this -> _renderCronError(404);
			}
			else {
				header('HTTP/1.0 404 Not Found');
				exit ;
			}
		}
	}

	public function redirectToAction($data) {
		$type = $data["type"];
		$module = $data["module"];
		$controller = $data["controller"];
		$action = $data["action"];
		$internal = false;
		if (isset($data["internal"])) {
			$internal = $data["internal"];
		}
		$parameters = array();
		if (isset($data["parameters"])) {
			$parameters = $data["parameters"];
		}

		FW_Context::getInstance() -> router -> type = $type;
		FW_Context::getInstance() -> router -> parameters = $parameters;
		FW_Context::getInstance() -> router -> action = $action;
		FW_Context::getInstance() -> router -> route = array("type" => $type, "cache" => false, "module" => $module, "controller" => $controller, "action" => $action, "internal" => $internal, "parameters" => $parameters);
		$type = ucfirst($type);
		$class = "FW_FrontController_{$type}";
		$controller = new $class();
		$controller -> render();
		return;
	}

	public static function redirect($url) {
		header("Location: {$url}");
	}

	private function _forward404() {
		$url = "";
		$notfound = FW_Config::getInstance() -> get("error.sections.404");

		$route = $this -> _router -> toURL($notfound["module"], $notfound["controller"], $notfound["action"], $notfound["internal"]);
		$param = new FW_Container_Parameter();
		$param -> result = true;
		$param -> route = $route;
		$param -> type = $route["type"];

		FW_Context::getInstance() -> setParameter("router", $param);

		$type = ucfirst($this -> getContext() -> router -> type);		
		if (strlen($type)===0) {
			$type = "App";
		}
		$class = "FW_FrontController_{$type}";				
		$controller = new $class();
		$controller -> render();

		/*$url             = FW_Router::getInstance()->toURL($notfound["module"],$notfound["controller"],$notfound["action"],$notfound["internal"]);
		 $url             = "{$baseurl}{$url["url"]}";
		 return FW_HttpResponse::redirect($url);*/
	}

	/* Error dispatching methods */
	private function _dispatchError($type = 404) {

		if ($this -> _service == "cron") {
			$this -> _dispatchCronError($type);
		}
		if ($this -> _service == "application") {
			$this -> _dispatchApplicationError($type);
		}				
		return false;
	}

	private function _renderRestError($type) {
		$error = "";
		$format = FW_Context::getInstance() -> request -> getGetParameter("format");
		$response = new FW_HttpResponse();

		if ($format !== "json" && $format != "xml") {
			$format = "xml";
		}
		if ($format == "json") {
			$response -> addMimeHeader("application/javascript");
			$error = "{\"error\":[\"Service doesn't exists\"]}";
		}
		if ($format === null || $format == "xml") {
			$response -> addMimeHeader("text/xml");
			$error = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			$error .= "<error type=\"{$type}\"><![CDATA[Service doesn't exists]]></error>";
		}
		$response -> render();
		print $error;
	}

	private function _renderCronError($type) {
		$stderr = fopen("php://stderr", "w");
		if ($type == 404) {
			fwrite($stderr, "Error, 404\n");
		}
		if ($type == 403) {
			fwrite($stderr, "Error, 403\n");
		} else {
			fwrite($stderr, "Error, 500\n");
		}
		fclose($stderr);
		return false;
	}	

	private function _dispatchApplicationError($type) {
		$engine = new FW_Dispatcher_Engine_App();
		/*if ($type==404) {
		 FW_Error_Handler::displayNotFoundError();
		 }
		 if ($type==403) {
		 FW_Error_Handler::displayForbiddenError();
		 }
		 if ($type==500) {
		 FW_Error_Handler::displayError();
		 }*/
		return true;
	}

	private function _dispatchRedirect() {
		$url = $this -> _result["redirect"];
		header("Location: {$url}");
	}

	private function _dispatchStatic() {
		$file = $this -> _result["file"];
		$file = "app/resources/static/{$file}";
		include $file;
	}

	private function _includeController($module, $controller, $internal) {
		$path = "";
		$result = false;
		$controller = "{$controller}Controller";

		if ($internal === false) {
			$path = BASE_PATH."app/modules/{$module}";
		} else {
			$path = BASE_PATH."framework/app/modules/{$module}";
		}

		$file = "{$path}/controller/{$controller}.class.php";

		if (is_file($file)) {
			require_once $file;
			$result = true;
		}
		return $result;
	}

	private function getContext() {
		if ($this -> _context === null) {
			$this -> _context = FW_Context::getInstance();
		}
		return $this -> _context;
	}

	public static function getBaseURL() {
		return FW_Config::getInstance() -> get("core.globals.baseURL");
	}

};
?>