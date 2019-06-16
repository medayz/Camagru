<?php
class   SignupController extends Controller {
    public function __construct() {
        $this->signUpModel = $this->loadModel('Signup');
    }

    public function index() {
        $this->loadView('pages/signup');
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'pwd' => $_POST['pwd'],
                'confirm_pwd' => '',
                'username_err' => '',
                'email_err' => '',
                'pwd_err' => '',
                'confirm_pwd_err' => ''
            ];
            if (!preg_match("/^[a-zA-Z]+[\w-]*$/", $data['username']))
                $data['username_err'] = "Your username must begin with a letter and can only contain alphanumeric characters dashes and underscores";
            if (!preg_match("/[!@#\$%\^&\*\(\),\.\?\\\"\:\{\}\|<>]+/", $data['pwd'])
                || !preg_match("/[A-Z]+/", $data['pwd'])
                || !preg_match("/[a-z]+/", $data['pwd'])
                || !preg_match("/\d+/", $data['pwd']))
                $data['pwd_err'] = "This password is weak af!";
            if ($_POST['pwd'] != $_POST['confirm_pwd'])
                $data['confirm_pwd_err'] = "are u dumb? this doesn't match!";
            if (!preg_match("/^[\w.!#$%&'*\+\/=?\^`{|}~-]+@(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/", $data['email']))
                $data['email_err'] = "WTF? this is not an e-mail!";
            if (!$_POST['username'])
                $data['username_err'] = "Put a fu*king username!";
            if (!$_POST['email'])
                $data['email_err'] = "Put a fucking email!";
            if (!$_POST['pwd'])
                $data['pwd_err'] = "Should I pick a fucking password for u ?";
            if (!$_POST['confirm_pwd'])
                $data['confirm_pwd_err'] = "Confirm your shi*ty password here!";
            if (empty($data['email_err']) && empty($data['username_err']) && empty($data['pwd_err']) && empty($data['confirm_pwd_err'])) {
                $data['pwd'] = password_hash($data['pwd'], PASSWORD_DEFAULT);
                if ($this->signUpModel->register($data)) {
                    $this->setUserSession($data);
                    redirect('home');
                }   else {

                }
            }
            else {

                $this->loadView('pages/signup', $data);
            }
        } else {
            $data = [
                'username' => '',
                'email' => '',
                'pwd' => '',
                'confirm_pwd' => '',
                'username_err' => '',
                'email_err' => '',
                'pwd_err' => '',
                'confirm_pwd_err' => ''
            ];
            $this->loadView('pages/signup', $data);
        }
    }

    private function setUserSession($data) {
        initialize_session($data);
    }
}
?>
