<?php
class Database {
    private $host = DB_HOST;
    private $name = DB_NAME;
    private $user = DB_USER;
    private $pwd = DB_PWD;

    private $dbh;
    private $stmt;
    private $error;

    public  function __construct() {
        $dsn = 'mysql:host='. $this->host . ';dbname:' . $this->name;
    }
}
?>
