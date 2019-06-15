<?php
class SigninModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getUser($username, $password) {

        $this->db->query("SELECT * FROM users WHERE username = :username");
        $this->db->bind(":username", $username);
        $hashed_pwd = $this->db->getRow()->pwd;
        if (empty($hashed_pwd))
            return  "Username not registered!";
        else if (password_verify($password, $hashed_pwd))
            return  "Success";
        else
            return  "Incorrect Password!";

    }

    public  function connect($data) {
        if (($err_msg = $this->getUser($data['username'], $data['pwd'])) === "Success") {
//            $_SESSION['user'] = $data['username'];
            return "OK!";
        }   else {
            return  $err_msg;
        }
    }
}
?>