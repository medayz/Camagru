<?php
session_start();

function    logout() {
    unset($_SESSION['user']);
    session_destroy();
}

function    initialize_session($data) {
    $_SESSION['user'] = $data['username'];
}
?>