<?php
class sample extends FW_Plugin_Base {


    protected function _initialize(array $parameters=null) {}
    protected function _configure(array $parameters=null) {}
    
    
    public function saludar($name) {
        print "Hola {$name}!";
        
    }


}

?>