<?php
class   SignupController extends Controller {
    public function __construct() {
//        echo "Hello from SignupController";
    }
    public function index() {
//        echo "index method!\n";
        $this->loadView('pages/signup');
    }
    public function regiter() {
        echo "signup method!\n";
    }
}
?>
