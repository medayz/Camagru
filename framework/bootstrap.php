<?php
require_once 'helpers/session_helpers.php';
require_once 'helpers/url_helpers.php';
require_once '../app/config/config.php';
require_once '../app/config/database.php';

spl_autoload_register(function ($className) {
    require_once 'libraries/' . $className . '.class.php';
});

?>