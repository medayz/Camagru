<?php
class PicturesModel
{
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function storeImg($data) {

        $this->db->query('INSERT INTO `pics`(`path`, `user`) VALUES(:path, :user)');
        $this->db->bind(':user', $data['user']);
        $this->db->bind(':path', $data['path']);

        return $this->db->execute();
    }

    public function newLike($data) {

        $this->db->query('INSERT INTO `likes`(`username`, `pic_path`) VALUES(:user, :pic)');
        $this->db->bind(':user', $data['user']);
        $this->db->bind(':pic', $data['pic']);

        try {
            $this->db->execute();
            return true;
        }   catch (Exception $e) {
            return false;
        }
    }

    public function removeLike($data)
    {
        $this->db->query('DELETE FROM `likes` WHERE `username` = :user AND `pic_path` = :pic');
        $this->db->bind(':user', $data['user']);
        $this->db->bind(':pic', $data['pic']);

        $this->db->execute();
    }

    public function getLikes() {
        $this->db->query("SELECT `username`, `pic_path` FROM `likes`");
        return $this->db->getAllRows();
    }

    public function getUserPics($user) {
        $this->db->query("SELECT path FROM pics WHERE user=:user");
        $this->db->bind(':user', $user);
        return $this->db->getAllRows();
    }

    public function getAllPics() {
        $this->db->query("SELECT path FROM pics");
        return $this->db->getAllRows();
    }
}
?>