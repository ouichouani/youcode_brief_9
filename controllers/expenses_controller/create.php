<?php

session_start() ;
include '../../connection/connection.php';
unset($_SESSION['error']);
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $ERRORS = [] ;

    //IF VALUE NOT EXISTS , SET NULL IN VARIABLE 
    $amount = $_POST['amount'] ?? null;
    $description = trim($_POST['description'] ?? '', ' ');
    $id_card = trim($_POST['id_card'] ?? '', ' ');
    $category_id = trim($_POST['category_id'] ?? '', ' ');

    //VALIDATION AMOUNT
    if (!$amount) {
        $ERRORS['amount'] = 'amount is required';
    } else if ($amount < 0) {
        $ERRORS['amount']  = 'amount can\'t be negative';
    } else if (!preg_match('/^\d+(\.\d{1,2})?$/', $amount)) {
        $ERRORS['amount'] = 'amount is invalid';
    }

    // $find_card = $connection->prepare("SELECT * from cards WHERE id = ?") ;
    // $find_card->bind_param('i' , $id_card) ;
    // $find_card->execute() ;
    // $result = $find_card->get_result() ;
    // $row = $result->fetch_assoc() ;

    // if(!$rom) $ERRORS['id_card']  = 'card is not exists';


    //IF THERE IS AN ERROR
    if (count($ERRORS)) {
        $_SESSION['ERRORS'] = $ERRORS;
        $connection->close();
        header('Location: ../../index.php?error=validation');
        exit;
    }

    $amount = floatval($amount); 
    $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    $id_card = intval($id_card);
    $category_id = intval($category_id);

    $stat = $connection->prepare('INSERT INTO expenses (amount, description , id_card , category_id ) VALUES (? , ?  , ? , ?)');

    if (!$stat) {
        $_SESSION['ERRORS'] = ["Database error: " . $connection->error];
        $connection->close();
        header("Location: ../../index.php?error=database");
        exit;
    }

    $stat->bind_param('dsii', $amount, $description, $id_card , $category_id);
    $status = $stat->execute();


    if (!$status) {
        $_SESSION['ERRORS'] = ['creation failed' . $stat->error];
        $stat->close();
        $connection->close();
        header('Location: ../../index.php?error=creation_failed');
        exit;
    }

    //CREATE SUCCEED
    $stat->close();
    $_SESSION['SUCCESS'] = 'expense created successfully';
}

$connection->close();
header('Location: ../../index.php');
exit;

