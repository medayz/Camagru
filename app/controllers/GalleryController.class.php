<?php
class GalleryController extends Controller {
    private $picturesModel;

    public function __construct() {
        $this->picturesModel = $this->loadModel("Pictures");
    }

    public function index() {
        $data['page'] = "Gallery";
        $data['pics'] = [];
        foreach ($this->picturesModel->getAllPics() as $pic)
            $data['pics'][] = $pic->path;
        $this->loadView('pages/gallery', $data);
    }

    public function newRowPics() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

//            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $index = intval($_POST['index']);
            $paths = $this->picturesModel->getRowPics($index);
            foreach ($paths as &$pics) {
                $pics->path = URL_ROOT . 'img/Users_pics/' . $pics->path;
            }
            echo json_encode($paths);
        }
    }

    public function likePic() {
        if (!empty($_SESSION['user'])) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

//                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                $likes = [];
                $data['pic'] = $_POST['pic'];
                $data['user'] = $_SESSION['user'];
                if ($this->picturesModel->getPic($data['pic']))
                    $this->picturesModel->newLike($data);

                $likes['loggedOn_user'] = $data['user'];
                $likes['all_likes'] = $this->picturesModel->getLikes();
                echo json_encode($likes);
            }
        }   else {
            echo 'ERROR';
        }
    }

    public function submitComment() {
        if (!empty($_SESSION['user'])) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

//                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                if (($data = json_decode($_POST['comment']))) {
                    if (isset($data->pic) && !empty($data->text)) {
                        $data->user = $_SESSION['user'];
                        if ($this->picturesModel->getPic($data->pic))
                            $this->picturesModel->newComment($data);
                    }
                }
            }
        }   else {
            echo 'ERROR';
        }
    }

    public function sendMail() {
        if (($data = json_decode($_POST['comment']))) {
            if (isset($data->pic) && !empty($data->text)) {
                $data->user = $_SESSION['user'];
                if (send_notif()) {
                    $subject = $data->user . ' commented on you post';
                    $msg = $data->user . ': ' . $data->text;
                    $to = $this->picturesModel->getPicOwner($data->pic)->email;
                    mail($to, $subject, $msg, EMAIL_HEADERS);
                }
            }
        }
    }

    public function deleteComment() {
        if (!empty($_SESSION['user'])) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                if (($comment = json_decode($_POST['comment'])) && isset($comment->id)) {
                    if ($this->picturesModel->removeComment($comment)) {
                        echo 'OK';
                    } else {
                        echo 'ERROR';
                    }
                }
            }
        }   else {
            echo 'ERROR';
        }
    }

    public function getComments() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['pic']))
                echo json_encode(array_reverse($this->picturesModel->getPicComments($_POST['pic'])));
            else
                echo json_encode(array());
        }
    }

    public function getLikes() {
        $likes['loggedOn_user'] = !empty($_SESSION['user'])
            ? $_SESSION['user'] : '';
        $likes['all_likes'] = $this->picturesModel->getLikes();
        echo json_encode($likes);
    }

    public function getPicLikes() {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['pic'])) {
                $pic = $_POST['pic'];
                echo json_encode($this->picturesModel->getPicLikes($pic));
            }
        }
    }

    public function unlikePic() {
        if (!empty($_SESSION['user'])) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                if (isset($_POST['pic'])) {
                    $data['pic'] = $_POST['pic'];
                    $data['user'] = $_SESSION['user'];
                    $this->picturesModel->removeLike($data);
                    echo 'OK';
                }
            }
        }   else {
            echo 'ERROR';
        }
    }
}
?>