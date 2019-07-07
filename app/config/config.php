<?php


    define("DS", DIRECTORY_SEPARATOR);

    define("URL_ROOT", "http://localhost/camagru/");

    define("ROOT", dirname(dirname(dirname(__FILE__))) . DS);

    define("APP_PATH", dirname(dirname(__FILE__)) . DS);

    define("FRAMEWORK_PATH", ROOT . "framework" . DS);

    define("PUBLIC_PATH", ROOT . "public" . DS);

    define("CONFIG_PATH", APP_PATH . "config" . DS);

    define("CONTROLLER_PATH", APP_PATH . "controllers" . DS);

    define("MODEL_PATH", APP_PATH . "models" . DS);

    define("VIEW_PATH", APP_PATH . "views" . DS);


    define("CORE_PATH", FRAMEWORK_PATH . "core" . DS);

    define('DB_PATH', FRAMEWORK_PATH . "database" . DS);

    define("LIB_PATH", FRAMEWORK_PATH . "libraries" . DS);

    define("HELPER_PATH", FRAMEWORK_PATH . "helpers" . DS);

    define("UPLOAD_PATH", PUBLIC_PATH . "uploads" . DS);

    define("SITE_NAME", "Camagru");

    // email config
    define('EMAIL_HEADERS',
        'From: Camagru <est36berrechid@gmail.com>' . PHP_EOL .
        'Reply-To: Camagru <est36berrechid@gmail.com>' . PHP_EOL .
        'X-Mailer: PHP/' . phpversion() . PHP_EOL .
        'Content-Type: text/html; charset=UTF-8'
    );

?>