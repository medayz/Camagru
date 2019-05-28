<?php
require_once '../app/config/config.php';
require_once 'libraries/Controller.class.php';
require_once 'libraries/Database.class.php';
require_once 'libraries/Core.class.php';

spl_autoload_register(function ($className) {
    require_once 'libraries/' . $className . '.class.php';
//    if (substr($classname, -10) == "Controller") {
//
//        require_once CURR_CONTROLLER_PATH . "$className.class.php";
//    } elseif (substr($classname, -5) == "Model") {
//
//        require_once  MODEL_PATH . "$className.class.php";
//    }
});
?>