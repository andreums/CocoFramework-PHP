<?php
class errorController extends FW_mvc_BaseController {

    public function error404() {
        print "Entro aquí!";
        $error = new FW_Error_NotFound();
        $error->setTemplate("framework/app/modules/system/view/errors/404");
        $error->raise();
    }

    public function error403() {
        $error = new FW_Error_Forbidden();
        $error->setTemplate("framework/app/modules/system/view/errors/403");
        $error->raise();
    }

    public function displayError($code=404) {
        if ($code===404) {
            $error = new FW_Error_NotFound();
            $error->setTemplate("framework/app/modules/system/view/errors/404.php");
            $error->raise();
        }
    }

};
?>