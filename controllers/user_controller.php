<?php

session_start() ;
require 'user.php' ;
require_once('../connection/connection.php');

use User\User ;
unset($_SESSION['error'], $_SESSION['success']);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['register'])){
        $user = new User($_POST['email'] , $_POST['password'] , $_POST['fullname'] , $_POST['confirm_password']);
        $user->create() ;
    }
    
    if (isset($_POST['login'])) {
        User::login($_POST['email'] , $_POST['password'] ) ;
    }
    if (isset($_POST['logout'])){
        User::logout() ;
    }

    header('location: ../index.php');
}
