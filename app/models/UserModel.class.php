<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getUser($username) {

        $this->db->query("SELECT * FROM users WHERE username = :username");
        $this->db->bind(":username", $username);
        return $this->db->getRow();
    }

    public function emailExists($email) {

        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(":email", $email);
        return $this->db->getRow();
    }

    private function checkUser($username, $password) {

        $hashed_pwd = $this->getUser($username)->pwd;
        if (empty($hashed_pwd))
            return  "Username not registered!";
        else if (password_verify($password, $hashed_pwd))
            return  "Success";
        else
            return  "Incorrect Password!";

    }

    public function getUsers() {
        $this->db->query("SELECT * FROM users");

        return $this->db->getAllRows();
    }

    public  function register($data) {
        $this->db->query('INSERT INTO `users`(username, email, pwd) VALUES(:username, :email, :pwd)');
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':pwd', $data['pwd']);

        return $this->db->execute();
    }

    public  function newEmailToken($username, $token) {
        $this->db->query('INSERT INTO `email_confirmation`(`username`, `token`) VALUES(:username, :token)');
        $this->db->bind(':username', $username);
        $this->db->bind(':token', $token);

        return $this->db->execute();
    }

    public  function newPwdToken($user) {
        $this->db->query('INSERT INTO `reset_pwd`(`username`, `token`) VALUES(:username, :token)');
        $this->db->bind(':username', $user->username);
        $this->db->bind(':token', $user->token);

        return $this->db->execute();
    }

    public function checkToken($data) {

        if ($data['type'] === 'reset_pwd')
            $this->db->query("SELECT * FROM `reset_pwd` WHERE `username` = :username AND `token` = :token");
        else
            $this->db->query("SELECT * FROM `email_confirmation` WHERE `username` = :username AND `token` = :token");
        $this->db->bind(":username", $data['username']);
        $this->db->bind(":token", $data['token']);
        if ($this->db->getRow()) {
            return ($this->removeToken($data)) ? true : false;
        }
        return false;
    }

    public function removeToken($data) {
        if ($data['type'] === 'reset_pwd')
            $this->db->query("DELETE FROM `reset_pwd` WHERE `username` = :username");
        else
            $this->db->query("DELETE FROM `email_confirmation` WHERE `username` = :username");
        $this->db->bind(":username", $data['username']);

        return $this->db->execute();
    }

    public  function resetPwd($data)
    {
        $this->db->query('UPDATE `users` SET `pwd`= :pwd WHERE `username` = :user');
        $this->db->bind(':user', $data['username']);
        $this->db->bind(':pwd', $data['new_pwd']);

        return $this->db->execute();
    }

    public  function changePwd($data) {
        if (($err_msg = $this->checkUser($data['username'], $data['pwd'])) === "Success") {
            $this->db->query('UPDATE `users` SET `pwd`= :pwd WHERE `username` = :user');
            $this->db->bind(':user', $data['username']);
            $this->db->bind(':pwd', $data['new_pwd']);

            $this->db->execute();
            return "OK";
        }   else {
            return  $err_msg;
        }
    }

    public function editUsername($data) {
        $this->db->query('UPDATE `users` SET `username`= :new_user WHERE `username` = :old_user');
        $this->db->bind(':new_user', $data['username']);
        $this->db->bind(':old_user', $data['old_user']);

        $this->db->execute();
    }

    public function editEmail($data) {
        $this->db->query('UPDATE `users` SET `email`= :email, active = 0 WHERE `username` = :user');
        $this->db->bind(':user', $data['username']);
        $this->db->bind(':email', $data['email']);

        $this->db->execute();
    }

    public function setNotifs($data) {
        $this->db->query('UPDATE `users` SET `send_notif`=:notif WHERE `username` = :user');
        $this->db->bind(':user', $data['username']);
        $this->db->bind(':notif', ($data['notif'] === 'active') ? 1 : 0, PDO::PARAM_BOOL);

        $this->db->execute();
    }

    public function setUserConfirmed($data) {
        $this->db->query('UPDATE `users` SET `active` = :active WHERE `username` = :user');
        $this->db->bind(':user', $data['username']);
        $this->db->bind(':active', 1, PDO::PARAM_BOOL);

        $this->db->execute();
    }

    private function isActive($user) {
        return  $this->getUser($user)->active;
    }

    public  function connect($data) {
        if (($err_msg = $this->checkUser($data['username'], $data['pwd'])) === "Success"
            && $this->isActive($data['username'])) {
            return "OK!";
        }   else {
            return  ($err_msg === 'Success')
                ? 'This account is not activated, go to your mailbox and click the fucking link we sent you!'
                : $err_msg;
        }
    }
}
?>