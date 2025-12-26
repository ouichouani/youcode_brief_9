<?php

session_start();
require 'income.php';
require_once('../connection/connection.php');


use Income\Income;
unset($_SESSION['error'], $_SESSION['success']);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['AuthUser']) && $_SESSION['AuthUser']['id']) {

    if (isset($_POST['create'])) {
        $income = new Income($_POST["amount"], $_POST["category_id"], $_POST["description"]);
        $income->create();
        header('location: ../index.php?incom_created_with_success');
        exit;
    }
    if (isset($_POST['update']) && $_POST['method'] == 'PUT') {
        $income = new Income();
        $income->update($_POST['id'], $_POST['amount'], $_POST['description']);
        header('location: ../index.php?incom_created_with_success');
        exit;
    }
    if (isset($_POST['delete']) && $_POST['method'] == 'DELETE') {
        $income = new Income();
        $income->delete($_POST['id']);
        header('location: ../index.php?incom_created_with_success');
        exit;
    }
    
    header('location: ../index.php?achawa_biti_diir');
    exit;


} else {
    header('location: ../index.php?invalid_method');
    exit;
}
