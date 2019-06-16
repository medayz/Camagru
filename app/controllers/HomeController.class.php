<?php
    class HomeController extends Controller {

        public function __construct() {
            $this->picturesModel = $this->loadModel("Pictures");
        }

        public function submitPic() {
            $data = [];
            $data['page'] = "Camera";
            $img_data = $_POST['img'];
//            echo microtime(true) . '\n';
            $name = $_SESSION['user'] . '_' . str_replace(".", "", microtime(true)) . '.png';
            $path = URL_ROOT . "img/Users_pics/" . $name;
            $file = PUBLIC_PATH . 'img/Users_pics/' . $name;
            $uri = substr($img_data,strpos($img_data, ",") + 1);
//            echo 'hello: ' . $uri;
            file_put_contents($file, base64_decode($uri));
            $data['user'] = $_SESSION['user'];
            $data['path'] = $name;
            if ($this->picturesModel->storeImg($data)) {
                echo json_encode($this->getPics());
            }
        }
        public function deletePic() {
            $pic = $_POST['pic'];
            if ($this->picturesModel->removeImg($pic)) {
                echo json_encode($this->getPics());
            }
        }

        public function index() {
            if (!empty($_SESSION['user'])) {
                $data['page'] = "Camera";
                $data['pics'] = $this->getPics();
                $this->loadView('pages/home', $data);
            }   else {
                redirect('signin');
            }
        }

        private function getPics() {
            $pics = [];
            foreach ($this->picturesModel->getUserPics($_SESSION['user']) as $pic)
                $pics[] = $pic->path;
            return array_reverse($pics);
        }

        public function logOut() {
            logout();
            redirect("signin");
        }
    }
?>