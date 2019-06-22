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
        $this->db->bind(':pwd', $data['new_pwd']);

        return $this->db->execute();
    }

    public  function change_pwd($data) {
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
        $this->db->query('UPDATE `users` SET `email`= :email WHERE `username` = :user');
        $this->db->bind(':user', $data['username']);
        $this->db->bind(':email', $data['email']);

        $this->db->execute();
    }

    public  function connect($data) {
        if (($err_msg = $this->checkUser($data['username'], $data['pwd'])) === "Success") {
//            $_SESSION['user'] = $data['username'];
            return "OK!";
        }   else {
            return  $err_msg;
        }
    }
}
?>