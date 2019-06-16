<?php
class   SigninController extends Controller {
    public function __construct() {
        $this->signInModel = $this->loadModel('Signin');
    }

    public function index() {
        $this->loadView('pages/signin');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = [
                'username' => trim($_POST['username']),
                'pwd' => $_POST['pwd'],
                'username_err' => '',
                'pwd_err' => '',
            ];
            if (!$_POST['username'])
                $data['username_err'] = "Put ur fucking username!";
            if (!$_POST['pwd'])
                $data['pwd_err'] = "I hope u're not trying to log in without a password!!";
            if (empty($data['username_err']) && empty($data['pwd_err'])) {
                $err_msg = $this->signInModel->connect($data);
                if ($err_msg === "OK!") {
                    $this->setUserSession($data);
                    redirect('home');
                } else {

                    if ($err_msg === "Incorrect Password!")
                        $data['pwd_err'] = $err_msg;
                    else
                        $data['username_err'] = $err_msg;
                    $this->loadView('pages/signin', $data);
                }
            }   else {

                $this->loadView('pages/signin', $data);
            }
        } else {

            $data = [
                'user' => '',
                'pwd' => '',
                'user_err' => '',
                'pwd_err' => ''
            ];
            $this->loadView('pages/signin', $data);
        }
    }

    private function setUserSession($data) {
        initialize_session($data);
    }
}
?>