<?php
class   LoginController extends Controller {
    public function __construct() {
//        echo "Hello from LogiController";
    }
    public function index() {
//        echo "index method!\n";
        $this->loadView('pages/login');
    }
    public function login() {
        echo "login method!\n";
    }
}
?>