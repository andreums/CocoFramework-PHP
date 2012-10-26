<?php
class breadCrumb extends FW_Widget_Base {

    private static $_instance = null;
    private static $_breadCrumbElements = array();

    private $_elements;
     
    public function __construct() {
        return;



    }

    private function _setUp() {
        $baseURL = $this->config()->getParameter("base","default","baseurl");
        self::addElement($baseURL,"Home");
        self::addElement($baseURL,"Home");
        self::discoverBreadCrumb();
    }

    public function render($name="", $value = null) {
        return;

    }

    public static function discoverBreadCrumb() {

        $request = FW_Request::getInstance();


        $enroutingParams = FW_Router::getParameters();
        $module = $enroutingParams["module"];
        $controller = $enroutingParams["controller"];
        $basePATH = self::$_config->getParameter("base","default","basepath");
        $baseURL = self::$_config->getParameter("base","default","baseurl");
        $breadCrumbFile = $basePATH.'/'."app".'/'."modules".'/'.$module.'/'."config".'/'."breadcrumbs.xml";
        if (is_file($breadCrumbFile) && is_readable($breadCrumbFile)) {
            $xmlf = simplexml_load_file($breadCrumbFile);
            if (!$xmlf) {
                return false;
            }
            if (isset($xmlf->controller)) {
                foreach ($xmlf->controller as $bController) {
                    if ((string) $bController["name"] === $controller) {
                        $breadcrumb = $bController->breadcrumbs;
                        foreach ( $breadcrumb->breadcrumb as $breadcrumbSet) {
                            $action = (string) $breadcrumbSet["action"];
                            if ($action==$enroutingParams["action"]) {
                                if (isset($breadcrumbSet->element)) {
                                    foreach ($breadcrumbSet->element as $breadcrumbElement) {
                                        $title = (string) $breadcrumbElement->title;
                                        $titleOriginal = "";
                                        $pos=strpos($title,"param:");
                                        /*if ($pos!==false) {
                                         $titleOriginal = substr($title,0,$pos);
                                         $titleSlice = substr($title,$pos);
                                         $titleExp = explode(":",$titleSlice);
                                         if (count($titleExp)>1) {
                                         $paramName = $titleExp[1];
                                         $paramName = trim($paramName);
                                         $paramValue = Request::getParameter$paramName);
                                         $paramValue = urldecode($paramValue);
                                         $title = "{$titleOriginal}{$paramValue}";
                                         }
                                         }
                                         */

                                        $t1 = preg_replace('/\{\{/',"%",$title);
                                        $title = preg_replace('/\}\}/',"%",$t1);

                                        $posT = strpos($title,"{tr:");
                                        if ($posT!==false) {
                                            $posU = strpos($title,"}");
                                            $tr = (substr($title,$posT,$posU+1));
                                            $tr = substr($tr,4,-1);
                                            $translated = FW_locale_i18n::translate($tr);
                                            $title = str_replace("{tr:{$tr}}",$translated,$title);
                                        }
                                        $title = preg_replace('/\%(.*?)%/','{{$1}}',$title);

                                        if (preg_match_all("({{(.*?):(.*?)}})",$title,$matches)) {
                                            if (count($matches)>0) {
                                                $size = count($matches)-2;
                                                for ($i=0;$i<$size;$i++) {
                                                    $source   = trim($matches[1][$i]);
                                                    $variable = trim($matches[2][$i]);

                                                    $value = "";
                                                    switch ($source) {
                                                        case "param":
                                                            $tmp = $request->getParameter($variable);
                                                            break;

                                                        case "get":
                                                            $tmp = $request->getGetParameter($variable);
                                                            break;

                                                        case "post":
                                                            $tmp = $request->getPostParameter($variable);
                                                            break;

                                                    };
                                                    if ($tmp!=null) {
                                                        $title = str_replace("{{{$source}:{$variable}}}",$tmp,$title);
                                                    }
                                                }
                                            }
                                        }


                                        $url = (string) $breadcrumbElement->url;
                                        if ($url=="this") {
                                            $url = $request->getServerURL();
                                        }
                                        else {
                                            if (preg_match('{mvc:(.*)}',$url,$matches) ) {
                                                $mvc = $matches[1];
                                                $mvc = substr($mvc,0,-1);
                                                $result =  explode("|",$mvc);
                                                if (count($result)==3) {
                                                    $url = link_for_internal($result[0],$result[1],$result[2]);
                                                }
                                                else if (count($result)>3) {
                                                    $parameters = array();
                                                    if (preg_match_all("({{(.*?):(.*?)}})",$result[3],$matches)) {
                                                        if (count($matches)>0) {
                                                            $size = count($matches)-2;
                                                            for ($i=0;$i<$size;$i++) {
                                                                $source   = ($matches[1][$i]);
                                                                $variable = ($matches[2][$i]);

                                                                $value = "";
                                                                switch ($source) {
                                                                    case "param":
                                                                        $tmp = $request->getParameter($variable);
                                                                        break;

                                                                    case "get":
                                                                        $tmp = $request->getGetParameter($variable);
                                                                        break;

                                                                    case "post":
                                                                        $tmp = $request->getPostParameter($variable);
                                                                        break;

                                                                };
                                                                if ($tmp!=null) {
                                                                    $parameters[$variable] = $tmp;
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $url = link_for_internal($result[0],$result[1],$result[2],$parameters);
                                                }
                                            }
                                        }
                                        $url = ltrim($url,"/");
                                        self::addElement($url,$title);
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }


    }

    public static function getInstance() {
        if(!self::$_instance==null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function addElement($url,$title) {
        self::$_breadCrumbElements[] = array (
                    "title" => $title,
                    "url"   => $url
        );
    }


    public function process() {

        $html = "";
        $html .= '<div id="breadCrumb" class="breadCrumb" style="width: auto;">';
        $html .= '<ul>';
        if (count(self::$_breadCrumbElements)>0) {
            foreach (self::$_breadCrumbElements as $element) {
                $html .= '<li>';
                $html .= '<a href="'.$element["url"].'" title="'.$element["title"].'"> '.$element["title"].' </a>';
                $html .= '</li>';
            }
        }
        $html  .= '</ul>';
        $html  .= '</div>';

        $this->setHTML($html);
    }

    public function getStyle() {
    }


}
?>