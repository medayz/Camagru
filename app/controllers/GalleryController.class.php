<?php
class GalleryController extends Controller {
    public function __construct() {
//        echo "Hello from GalleryController";
    }
    public function index() {
//        echo "index method!\n";
        $this->loadView('pages/gallery');
    }
    public function login() {
        echo "login method!\n";
    }
}
?>