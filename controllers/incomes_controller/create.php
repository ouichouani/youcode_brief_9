<?php

include '../../connection/connection.php';
session_start() ;
unset($_SESSION['error']);
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $ERRORS = [] ;

    //IF VALUE NOT EXISTS , SET NULL IN VARIABLE 
    $amount = $_POST['amount'] ?? null;
    $description = trim($_POST['description'] ?? '', ' ');
    $id_card = trim($_POST['id_card'] ?? '', ' ');

    //VALIDATION AMOUNT
    if (!$amount) {
        $ERRORS['amount'] = 'amount is required';
    } else if ($amount < 0) {
        $ERRORS['amount']  = 'amount can\'t be negative';
    } else if (!preg_match('/^\d+(\.\d{1,2})?$/', $amount)) {
        $ERRORS['amount'] = 'amount is invalid';
    }

    //IF THERE IS AN ERROR
    if (count($ERRORS)) {
        session_start();
        $_SESSION['error'] = $ERRORS;
        $connection->close();
        header('Location: ../../index.php?error=validation');
        exit;
    }

    $find_card = $connection->prepare("SELECT * from cards WHERE id = ?") ;

    $find_card->bind_param('i' , $id_card) ;
    $find_card->execute() ;
    $result = $find_card->get_result() ;
    $row = $result->fetch_assoc() ;

    if(!$row) $ERRORS['id_card']  = 'card is not exists';


    $amount = floatval($amount); // amount VALIDATION 
    $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    $id_card = intval($id_card);

    $stat = $connection->prepare('INSERT INTO incomes (amount, description , id_card ) VALUES (? , ?  , ?)');


    if (!$stat) {
        session_start();
        $_SESSION['error'] = ["Database error: " . $connection->error];
        $connection->close();
        header("Location: ../../index.php?error=database");
        exit;
    }

    $stat->bind_param('dsi', $amount, $description, $id_card);
    $status = $stat->execute();


    if (!$status) {
        session_start();
        $_SESSION['error'] = ['creation failed' . $stat->error];
        $stat->close();
        $connection->close();
        header('Location: ../../index.php?error=creation_failed');
        exit;
    }

    //CREATE SUCCEED
    $stat->close();
    $_SESSION['success'] = 'expense created successfully';
}

$connection->close();
header('Location: ../../index.php');
exit;

