<?php

$dsn = 'mysql:host='. DB_HOST . ';port=' . DB_PORT;

$options = array(
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
);

try {

    $dbh = new PDO($dsn, DB_USER, DB_PWD, $options);
    echo '<h1>';
    echo $dbh->exec("CREATE DATABASE IF NOT EXISTS `". DB_NAME ."`;") ?
        "DATABASE ". DB_NAME ." CREATED!" : "DATABASE NOT CREATED!";
    echo '</h1>';

    $dbh->exec("USE `". DB_NAME ."`;");

    echo '<ul>';

    echo '<li>';
    try {
        $dbh->exec(
            "CREATE TABLE IF NOT EXISTS `users` (
            `username` varchar(20) NOT NULL PRIMARY KEY,
            `email` varchar(50) NOT NULL,
            `pwd` varchar(500) NOT NULL,
            `active` tinyint(1) NOT NULL DEFAULT '0',
            `send_notif` tinyint(1) NOT NULL DEFAULT '1'
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ");
        echo "USERS TABLE CREATED!";
    }   catch (PDOException $e) {
        echo "TABLE NOT CREATED! ". $e->getMessage();
    }
    echo '</li>';

    echo '<li>';
    try {
        $dbh->exec(
            "CREATE TABLE IF NOT EXISTS `pics` (
             `path` varchar(40) NOT NULL PRIMARY KEY,
             `user` varchar(20) NOT NULL,
              CONSTRAINT `user_pics_casc` FOREIGN KEY (`user`) REFERENCES `users` (`username`) ON UPDATE CASCADE
           ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
         ");
        echo "PICTURES TABLE CREATED!";
    }   catch (PDOException $e) {
        echo "TABLE NOT CREATED! ". $e->getMessage();
    }
    echo '</li>';

    echo '<li>';
    try {
        $dbh->exec(
            "CREATE TABLE IF NOT EXISTS `likes` (
              `username` varchar(20) NOT NULL,
              `pic_path` varchar(40) NOT NULL,
              PRIMARY KEY (`username`,`pic_path`),
              CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`pic_path`) REFERENCES `pics` (`path`),
              CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ");
        echo "LIKES TABLE CREATED!";

    }   catch (PDOException $e) {

        echo "TABLE NOT CREATED! ". $e->getMessage();
    }
    echo '</li>';

    echo '<li>';
    try {

        $dbh->exec(
            "CREATE TABLE IF NOT EXISTS `comments` (
            `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `username` varchar(20) NOT NULL,
            `pic_path` varchar(40) NOT NULL,
            `content` varchar(500) NOT NULL,
            CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`pic_path`) REFERENCES `pics` (`path`),
            CONSTRAINT `user_comments` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON UPDATE CASCADE
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ");
        echo "COMMENTS TABLE CREATED!";

    }   catch (PDOException $e) {

        echo "TABLE NOT CREATED! ". $e->getMessage();
    }
    echo '</li>';

    echo '<li>';
    try {

        $dbh->exec(
            "CREATE TABLE IF NOT EXISTS `email_confirmation` (
            `username` varchar(20) NOT NULL,
            `token` varchar(60) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ");
        echo "CONFIRMATION EMAIL TABLE CREATED!";

    }   catch (PDOException $e) {

        echo "TABLE NOT CREATED! ". $e->getMessage();
    }
    echo '</li>';

    echo '<li>';
    try {

        $dbh->exec(
            "CREATE TABLE IF NOT EXISTS `reset_pwd` (
            `username` varchar(20) NOT NULL UNIQUE,
            `token` varchar(60) NOT NULL UNIQUE
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ");
        echo "RESET PASSWORD TABLE CREATED!";

    }   catch (PDOException $e) {

        echo "TABLE NOT CREATED! ". $e->getMessage();
    }
    echo '</li>';

    echo '</ul>';

} catch (PDOException $e) {
    echo $e->getMessage();
}
?>