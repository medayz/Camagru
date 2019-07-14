<?php
    class HomeController extends Controller {

        private $picturesModel;

        public function __construct() {
            $this->picturesModel = $this->loadModel("Pictures");
        }

        public function submitPic() {

            if (!empty($_SESSION['user'])) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                    $err_msg = '';

                    if (!($img_obj = json_decode($_POST['img'])) || !isset($img_obj->pic)) {
                        echo json_encode(
                            [
                                'paths' => [],
                                'err' => 'image not found!'
                            ]
                        );
                        return;
                    }
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
                    $name = str_replace(".", "", microtime(true)) . '_' . $_SESSION['user'] . '.png';
					if (!file_exists(PUBLIC_PATH . 'img/Users_pics'))
						mkdir(PUBLIC_PATH . 'img/Users_pics');
                    $file = PUBLIC_PATH . 'img/Users_pics/' . $name;
                    $uri = substr($img_data, strpos($img_data, ",") + 1);
                    if (substr($img_data, 5, 5) === 'image') {

                        try {

                            file_put_contents($file, base64_decode($uri));
                            $watermark = imagecreatefrompng(PUBLIC_PATH . 'img/pic_watermark.png');
                            if (getimagesize($file)[2] === IMAGETYPE_PNG) {
                                if (!$picture = imagecreatefrompng($file))
                                    throw new Exception('image can\'t be created!');
                            } else if (getimagesize($file)[2] === IMAGETYPE_JPEG) {
                                if (!$picture = imagecreatefromjpeg($file))
                                    throw new Exception('image can\'t be created!');
                            } else {

                                throw new Exception('image can\'t be created!');
                            }

                            if ($img_obj && property_exists($img_obj, 'stickers')) {
                                if (!empty($img_obj->stickers)) {
                                    foreach ($img_obj->stickers as $sticker) {
                                        if (property_exists($sticker, 'name')
                                            && property_exists($sticker, 'x')
                                            && property_exists($sticker, 'y')
                                            && property_exists($sticker, 'width')
                                            && property_exists($sticker, 'height')
                                            && !empty($sticker->name) && $sticker->width && $sticker->height
                                        ) {

                                            $size = in_array($sticker->name, $faces) ? 300 : 100;
                                            $sticker->x = round((float)($sticker->x * $size) / (float)$sticker->width, PHP_ROUND_HALF_UP);
                                            $sticker->y = round((float)($sticker->y * $size / (float)$sticker->height), PHP_ROUND_HALF_UP);
                                        } else {

                                            throw new Exception('image can\'t be created!');
                                        }
                                        if (!($sticker_img = imagecreatefrompng(PUBLIC_PATH . 'img/Stickers/' . $sticker->name)))
                                            throw new Exception('sticker not found!');
                                        imagecopy($picture, $sticker_img, $sticker->x, $sticker->y, 0, 0, $size, $size);
                                    }
                                }
                            } else {

                                throw new Exception('sticker not found!');
                            }

                            imagecopy($picture, $watermark, 245, 420, 0, 0, 150, 24);

                            if (!imagepng($picture, $file, 9, PNG_ALL_FILTERS)) {
                                echo 'there was some problem with this picture !';
                            } else {
                                imagedestroy($picture);
                                $data['user'] = $_SESSION['user'];
                                $data['path'] = $name;
                                $this->picturesModel->storeImg($data);
                            }

                        } catch (Exception $e) {

                            unlink($file);
                            $err_msg = $e->getMessage();
                        }

                    } else {

                        $err_msg = 'You fucker didn\'t upload an image didn\'t you ?';
                    }


                    $paths = $this->getPics();
                    foreach ($paths as &$path) {
                        $path = URL_ROOT . 'img/Users_pics/' . $path;
                    }
                    echo json_encode(
                        [
                            'paths' => $paths,
                            'err' => $err_msg
                        ]
                    );
                }
            }
        }

        public function deletePic() {
            if (!empty($_SESSION['user'])) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                    if (isset($_POST['pic'])) {
                        $pic = $_POST['pic'];
                        $user = $_SESSION['user'];
                        $pic = $this->picturesModel->getPic($pic);
                        if ($pic && $pic->user === $user) {
                            if ($this->picturesModel->removeImg($pic->path)) {
								unlink(PUBLIC_PATH . 'img/Users_pics/' . $pic->path);
                                $paths = $this->getPics();
                                foreach ($paths as &$path) {
                                    $path = URL_ROOT . 'img/Users_pics/' . $path;
                                }
                                echo json_encode($paths);
                            }
                        }
                    }
                }
            }
        }

        public function index() {
            if (!empty($_SESSION['user'])) {
                $data['page'] = "Camera";
                $data['pics'] = $this->getPics();
                $data['stickers'] = $this->getStickers();
                $this->loadView('pages/home', $data);
            } else {
                redirect('users/signin');
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
            if (!empty($_SESSION['user'])) {
                $pics = [];
                foreach ($this->picturesModel->getUserPics($_SESSION['user']) as $pic) {
                    $pics[] = $pic->path;
                }
                return array_reverse($pics);
            }
        }
    }
?>
