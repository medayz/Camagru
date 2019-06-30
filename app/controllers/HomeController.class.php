<?php
    class HomeController extends Controller {

        private $picturesModel;

        public function __construct() {
            $this->picturesModel = $this->loadModel("Pictures");
        }

        public function submitPic() {
            $img_obj = json_decode($_POST['img']);
            $faces = [
                "rasbiri.png",
                "10vitesse.png",
                "55.png",
                "aymane.png",
                "ozaazaa.png",
                "ayman2.png",
                "abida.png",
                "allali.png",
                "abida2.png",
                "afaddoul.png"
            ];

            $data = [];
            $data['page'] = "Camera";
            $img_data = $img_obj->pic;
            $name = $_SESSION['user'] . '_' . str_replace(".", "", microtime(true)) . '.png';
            $path = URL_ROOT . "img/Users_pics/" . $name;
            $file = PUBLIC_PATH . 'img/Users_pics/' . $name;
            $uri = substr($img_data,strpos($img_data, ",") + 1);
            file_put_contents($file, base64_decode($uri));

            // Load the stamp and the photo to apply the watermark to
            $watermark = imagecreatefrompng(PUBLIC_PATH . 'img/pic_watermark.png');
            if (getimagesize($file)[2] === IMAGETYPE_PNG) {
                $picture = imagecreatefrompng($file);
            }   else if (getimagesize($file)[2] === IMAGETYPE_JPEG) {
                $picture = imagecreatefromjpeg($file);
            }

            // Copy the stamp image onto our photo using the margin offsets and the photo
            // width to calculate positioning of the stamp.
            if (!empty($img_obj->stickers)) {
                foreach ($img_obj->stickers as $sticker) {
                    $size = in_array($sticker->name, $faces) ? 300 : 100;
//                    echo "size: " . $size . "\n";
//                    echo $sticker->y . " " . $sticker->x . "\n";
                    $sticker->x = round((float)($sticker->x * $size) / (float)$sticker->width, PHP_ROUND_HALF_UP);
                    $sticker->y = round((float)($sticker->y * $size / (float)$sticker->height), PHP_ROUND_HALF_UP);
//                    echo $sticker->y . " " . $sticker->x . "\n";
                    $sticker_img = imagecreatefrompng(PUBLIC_PATH . 'img/Stickers/' . $sticker->name);
                    imagecopy($picture, $sticker_img, $sticker->x, $sticker->y, 0, 0, $size, $size);
                }
            }
            imagecopy($picture, $watermark, 245, 420, 0, 0, 150, 24);

            // Output and free memory
            imagepng($picture, $file, 9, PNG_ALL_FILTERS);
            imagedestroy($picture);

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
                $data['stickers'] = $this->getStickers();
                $this->loadView('pages/home', $data);
            }   else {
                redirect('signin');
            }
        }

        private function getStickers() {
            $files = [];
            if ($stickers_folder = opendir(PUBLIC_PATH . 'img/Stickers')) {

                while (($file = readdir($stickers_folder))) {

                    if ($file != "." && $file != ".." && strstr($file, ".png")) {

                        $files[] = $file;
                    }
                }

                closedir($stickers_folder);
            }
            return $files;
        }

        private function getPics() {
            $pics = [];
            foreach ($this->picturesModel->getUserPics($_SESSION['user']) as $pic) {
                $pics[] = $pic->path;
            }
            return array_reverse($pics);
        }

        public function logOut() {
            logout();
            redirect("signin");
        }
    }
?>