<?php
class SignupModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getUsers() {
        $this->db->query("SELECT * FROM users");

        return $this->db->getAllRows();
    }

    public  function register($data) {
        $this->db->query('INSERT INTO `users`(username, email, pwd) VALUES(:username, :email, :pwd);');
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':pwd', $data['pwd']);

        return $this->db->execute();
    }
}
?>