<?php

session_start() ;
require 'expense.php' ;
require_once('../connection/connection.php');


use Expense\Expense ;
unset($_SESSION['error'], $_SESSION['success']);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['AuthUser']) && $_SESSION['AuthUser']['id']) {

    if (isset($_POST['create'])) {
        $expense = new Expense($_POST["amount"], $_POST["category_id"], $_POST["description"]);
        $expense->create();
        header('location: ../index.php?expense_created_with_success');
        exit;
    }
    if (isset($_POST['update']) && $_POST['method'] == 'PUT') {
        $expense = new Expense();
        $expense->update($_POST['id'], $_POST['amount'], $_POST['description']);
        header('location: ../index.php?expense_created_with_success');
        exit;
    }
    if (isset($_POST['delete']) && $_POST['method'] == 'DELETE') {
        $expense = new Expense();
        $expense->delete($_POST['id']);
        header('location: ../index.php?expense_created_with_success');
        exit;
    }
    
    header('location: ../index.php?achawa_biti_diir');
    exit;


} else {
    header('location: ../index.php?invalid_method');
    exit;
}
