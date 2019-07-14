<?php
class Core {
    protected   $currentController = 'UsersController';
    protected   $currentMethod = 'index';
    protected   $params = [];

    public function __construct() {
        $url = $this->getUrl();
        if ($url[0] === "setup") {
            require CONFIG_PATH . 'setup.php';
            exit();
        }
        if (file_exists('../app/controllers/' . ucwords($url[0]) . 'Controller.class.php')) {
            $this->currentController = ucwords($url[0]) . 'Controller';
            unset($url[0]);
        }   else if (!empty($url[0])) {
            require APP_PATH . 'views/pages/404.php';
            exit(0);
        }

        require_once '../app/controllers/' . $this->currentController . '.class.php';
        $this->currentController = new $this->currentController();

        if (isset($url[1])
            && method_exists($this->currentController, $url[1])
        ) {
            $this->currentMethod = $url[1];
            unset($url[1]);
        }

        $this->params = $url ? array_values($url) : [];
        call_user_func(
            [$this->currentController, $this->currentMethod]
            , $this->params
        );
    }

    public function getUrl() {
        if (isset($_GET['url'])) {
            $url = trim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
    }
}
?>
