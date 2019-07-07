<?php
session_start();

function    logout() {
    unset($_SESSION['user']);
    session_destroy();
}

function    initialize_session($data) {
    $_SESSION['user'] = $data->username;
    $_SESSION['email'] = $data->email;
    $_SESSION['active'] = $data->active;
    $_SESSION['send_notif'] = $data->send_notif;
}

function    is_active() {
    return  $_SESSION['active'] ? true : false;
}

function    send_notif() {
    return  $_SESSION['send_notif'] ? true : false;
}
?>