<?php
    class indexController extends  FW_mvc_BaseController {
        	
        public function beforeRender() {
            $this->setBreadcrumb();
        }

        public function index() {        	
            $this->setSlot("content", "index");            
            $this->renderGlobalLayout("default");
        }        

        public function error403() {
            // loggejar error 403...
            $this->setSlot("content", "errors/error403");
            $this->renderGlobalLayout("default");
        }

        public function error404() {
            $this->setSlot("content", "errors/error404");
            $this->renderGlobalLayout("default");
        }
        
        
        public function jsonTest() {
            return json_encode(array("foo"=>"bar"));
        }
        public function xmlTest() {
            return "<time>".time()."</time>";
        }
        
        
        public function printLoremIpsum(){
            $this->renderView("testPrint");                        
        }     
           
       

    };
?>
