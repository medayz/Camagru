<?php
class GalleryController extends Controller {
    private $picturesModel;

    public function __construct() {
        $this->picturesModel = $this->loadModel("Pictures");
    }

    public function index() {
        $data['page'] = "Gallery";
        $data['n_likes'] = 15;
        $data['n_comments'] = 50;
        $data['pics'] = [];
        foreach ($this->picturesModel->getAllPics() as $pic)
            $data['pics'][] = $pic->path;
        $this->loadView('pages/gallery', $data);
    }

    public function newRowPics() {
        $index = intval($_POST['index']);
        $paths = $this->picturesModel->getRowPics($index);
        foreach ($paths as &$pics) {
            $pics->path = URL_ROOT . 'img/Users_pics/' . $pics->path;
        }
        echo json_encode($paths);
    }

    public function likePic() {
        $likes = [];
        $data['pic'] = $_POST['pic'];
        $data['user'] = $_SESSION['user'];
        $this->picturesModel->newLike($data);

        $likes['loggedOn_user'] = $data['user'];
        $likes['all_likes'] = $this->picturesModel->getLikes();
        echo json_encode($likes);
    }

    public function submitComment() {
        $data = json_decode($_POST['comment']);
        $data->user = $_SESSION['user'];
        $this->picturesModel->newComment($data);
        if (send_notif()) {
            $subject = $data->user . ' commented on you post';
            $msg = $data->user . ': ' . $data->text;
            $to = $this->picturesModel->getPicOwner($data->pic)->email;
            mail($to, $subject, $msg, EMAIL_HEADERS);
        }
//        echo json_encode($this->picturesModel->getComments());
    }

    public function deleteComment() {
        $cmnt = json_decode($_POST['comment']);
        if ($this->picturesModel->removeComment($cmnt)) {
            echo 'OK';
        }   else {
            echo 'ERROR';
        }
    }

    public function getComments() {
        echo json_encode(array_reverse($this->picturesModel->getPicComments($_POST['pic'])));
    }

    public function getLikes() {
        $likes['loggedOn_user'] = $_SESSION['user'];
        $likes['all_likes'] = $this->picturesModel->getLikes();
        echo json_encode($likes);
    }

    public function getPicLikes() {
        $pic = $_POST['pic'];
        echo json_encode($this->picturesModel->getPicLikes($pic));
    }

    public function unlikePic() {
        $data['pic'] = $_POST['pic'];
        $data['user'] = $_SESSION['user'];
        $this->picturesModel->removeLike($data);
        echo 'OK';
    }
}
?>