<?php
    class exceptionController extends FW_mvc_BaseController {
        
        public function displayException($exception) {            
            $this->setSlot("content","exceptions/default",array("exception"=>$exception));
            $this->renderLayout("default");                        
        }        
        
    };
?>