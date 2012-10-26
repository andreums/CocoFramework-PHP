<?php
class FlashMessage extends FW_Widget_Base {

    public function __construct($message,$type) {
        if ($type=="error") {
            $this->error($message);
        }
        if ($type=="notice") {
            $this->notice($message);
        }
        if ($type=="success") {
            $this->success($message);
        }

        if ($type=="information") {
            $this->information($message);
        }
    }
    
    public function initialize() {
        
    }

    public function error($message) {

        $html ="<div class=\"flashError\">{$message}<hr class=\"space\" /></div>";
        $this->setHTML($html);
        $this->render();
    }

    public function notice($message) {

        $html ="<div class=\"flashNotice\">{$message}<hr class=\"space\" /></div>";
        $this->setHTML($html);
        $this->render();
    }

    public function success($message) {

        $html ="<div class=\"flashSuccess\">{$message}<hr class=\"space\" /></div>";
        $this->setHTML($html);
        $this->render();
    }

    public function information($message) {

        $html ="<div class=\"flashInformation\">{$message}<hr class=\"space\" /></div>";
        $this->setHTML($html);
        $this->render();
    }

    public function process() {

    }

    public function render($name="", $value = null) {
        return;

    }
}
?>