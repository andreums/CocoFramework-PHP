<?php
session_start();
error_reporting(E_ALL | E_STRICT);

require_once "framework/bootstrap.php";
$bootstrap = bootstrap::getInstance();
$bootstrap->registerArgv();

if (isset($argv)) {
    FW_Context::getInstance()->argv = $argv;    
}
$frontController = new FW_FrontController("cron");
$frontController->render();


exit(0);
?>