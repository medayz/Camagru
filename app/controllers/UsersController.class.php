<?php
class   UsersController extends Controller {

    private $userModel;

    public function __construct() {
        $this->userModel = $this->loadModel('User');
    }

    public function index() {
        redirect("users/signin");
    }

    public function signin() {
        $this->loadView('pages/signin');
    }

    public function signup() {
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
                $data['username_err']
                    = "Your username must begin with a letter and can only contain alphanumeric characters dashes and underscores";
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
                if ($this->userModel->getUser($data['username'])) {
                    $data['pwd'] = '';
                    $data['username_err'] = "Unfortunately some Motherfu*ker is already using ur favorite username, pick another one!";
                    $this->loadView('pages/signup', $data);
                }   else if ($this->userModel->register($data)) {
                    $this->setUserSession($this->userModel->getUser($data['username']));
                    redirect('home');
                }   else {

                    $this->loadView('pages/signup', $data);
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
                $err_msg = $this->userModel->connect($data);
                if ($err_msg === "OK!") {
                    $this->setUserSession($this->userModel->getUser($data['username']));
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

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = [
                'username' => $_POST['username'],
                'username_err' => '',
                'email' => $_POST['email'],
                'email_err' => ''
            ];
            $data['page'] = 'Profile';
            if ($data['username'] === $_SESSION['user']
                && $data['email'] === $_SESSION['email']) {

                $this->loadView('pages/profile', $data);

            }   else {
                if ($data['username'] != $_SESSION['user']) {
                    if (empty($_POST['username']))
                        $data['username_err'] = "Put a fu*king username!";
                    if (!preg_match("/^[a-zA-Z]+[\w-]*$/", $data['username']))
                        $data['username_err']
                            = "Your username must begin with a letter and can only contain alphanumeric characters dashes and underscores";

                    if (empty($data['username_err'])) {
                        if ($this->userModel->getUser($data['username'])) {
                            $data['username_err'] = "Unfortunately some Motherfu*ker is already using ur favorite username, pick another one!";
                        }   else {
                            $data['old_user'] = $_SESSION['user'];
                            $this->userModel->editUsername($data);
                        }
                    }
                }
                if ($data['email'] != $_SESSION['email']) {
                    if (!preg_match("/^[\w.!#$%&'*\+\/=?\^`{|}~-]+@(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/"
                                    , $data['email']))
                        $data['email_err'] = "WTF? this is not an e-mail!";
                    if (empty($_POST['email']))
                        $data['email_err'] = "Put a fucking email!";

                    if (empty($data['email_err']) && empty($data['username_err'])) {
                        $data['old_email'] = $_SESSION['email'];
                        $this->userModel->editEmail($data);
                    }
                }
                if (empty($data['email_err']) && empty($data['username_err'])) {
                    logout();
                    redirect('users/signin');
                }   else {
                    $this->loadView('pages/profile', $data);
                }
            }
        }   else {

            $data = [
                'username' => $_SESSION['user'],
                'username_err' => '',
                'email' => $_SESSION['email'],
                'email_err' => ''
            ];
            $data['page'] = 'Profile';
            $this->loadView('pages/profile', $data);
        }
    }

    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = [
                'username' => $_SESSION['user'],
                'pwd' => $_POST['pwd'],
                'confirm_pwd' => $_POST['confirm_pwd'],
                'new_pwd' => $_POST['new_pwd'],
                'confirm_pwd_err' => '',
                'pwd_err' => '',
                'new_pwd_err' => ''
            ];
            $data['page'] = 'Password';
            if ($_POST['new_pwd'] != $_POST['confirm_pwd'])
                $data['confirm_pwd_err'] = "are u dumb? this doesn't match!";
            if (!preg_match("/[!@#\$%\^&\*\(\),\.\?\\\"\:\{\}\|<>]+/", $data['new_pwd'])
                || !preg_match("/[A-Z]+/", $data['new_pwd'])
                || !preg_match("/[a-z]+/", $data['new_pwd'])
                || !preg_match("/\d+/", $data['new_pwd']))
                $data['new_pwd_err'] = "This password is weak af!";
            if ($_POST['new_pwd'] == "")
                $data['new_pwd_err'] = "Are you drunk ?";
            if ($_POST['pwd'] == "")
                $data['pwd_err'] = "Are you drunk ?";
            if ($_POST['confirm_pwd'] == "")
                $data['confirm_pwd_err'] = "Are you drunk ?";
            if (empty($data['confirm_pwd_err']) && empty($data['pwd_err'])
                && empty($data['new_pwd_err'])) {
                $data['new_pwd'] = password_hash($data['new_pwd'], PASSWORD_DEFAULT);
                $err_msg = $this->userModel->changePwd($data);
                if ($err_msg === "OK") {
                    logout();
                    redirect('users/signin');
                } else {

                    $data['pwd_err'] = $err_msg;
                    $this->loadView('pages/password', $data);
                }
            }   else {
                $this->loadView('pages/password', $data);
            }
        }   else {
            $data = [
                'pwd' => '',
                'pwd_err' => '',
                'new_pwd' => '',
                'new_pwd_err' => '',
                'confirm_pwd' => '',
                'confirm_pwd_err' => ''
            ];
            $data['page'] = 'Password';
            $this->loadView('pages/password', $data);
        }
    }

    private function setUserSession($data) {
        initialize_session($data);
    }
}
?>