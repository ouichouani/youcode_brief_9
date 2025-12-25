<?php

session_start() ;
include '../../connection/connection.php';

unset($_SESSION['error']);
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['method'] == 'PUT') {

    $ERRORS = [];
    $params = [];
    $columns = [];
    $type = '';

    //IF VALUE NOT EXISTS , SET NULL IN VARIABLE 
    $id = $_POST['id'] ?? null;
    $amount = $_POST['amount'] ?? null;
    $description = trim($_POST['description'] ?? '', ' ');
    $id_card = $_POST['id_card'] ?? null;
    $category_id = $_POST['category_id'] ?? null;


    //FULL PARAMS TABLE AND TYPE STRING TO UPDATE ONLY NEEDED COLLUMNS
    if (!empty($amount)) {
        $amount = floatval($amount);
        $columns[] = $amount;
        $params[] = 'amount = ?';
        $type .= 'd';
    }

    if (!empty($description)) {
        $description  = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
        $columns[] = $description;

        $params[] = 'description = ?';
        $type .= 's';
    }

    if (!empty($id_card)) {
        $id_card  = intval($id_card);
        $columns[] = $id_card;
        $params[] = 'id_card = ?';
        $type .= 'i';
    }
    
    if (!empty($category_id)) {
        $category_id  = intval($category_id);
        $columns[] = $category_id;
        $params[] = 'category_id = ?';
        $type .= 'i';
    }

    //ID MUST BE EXESTS
    $id  = intval($id);
    $columns[] = $id;
    $type .= 'i';

    //ID VALIDATION
    if (!$id) {
        $ERRORS['id'] = 'id is required';
    } else if (!is_numeric($id) || $id <= 0) {
        $ERRORS['id'] = 'id must be a positive number';
    }

    //VALIDATION AMOUNT
    if (!empty($amount)) {
        if ($amount < 0) $ERRORS['amount']  = 'amount can\'t be negative';
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $amount)) $ERRORS['amount'] = 'amount is invalid';
    }

    //BREAK IF THERE IS NOTHING TO UPDATE
    if (empty($amount) && empty($description) && empty($id_card)) $ERRORS['error'] = 'ther is nothing to update';

    //IF THERE IS AN ERROR
    if (count($ERRORS)) {
        $_SESSION['error'] = $ERRORS;
        $connection->close();
        header('Location: ../../index.php?error=validation');
        exit;
    }

    //BUILD STATEMENT
    $stat = $connection->prepare('UPDATE expenses SET ' . implode(', ', $params) . ' WHERE id = ?');
    $stat->bind_param($type, ...$columns);


    if (!$stat) {
        $_SESSION['error'] = ["Database error: " . $connection->error];
        $connection->close();
        header("Location: ../../index.php?error=database");
        exit;
    }

    $status = $stat->execute();

    if (!$status) {
        $_SESSION['error'] = ['update failed' . $stat->error];
        $stat->close();
        $connection->close();
        header('Location: ../../index.php?error=update_failed');
        exit;
    }

    //UPDATE SUCCEED
    $stat->close();
    $_SESSION['success'] = 'expense updated successfully';
}

$connection->close();
header('Location: ../../index.php');
exit;




    // //CHECK IF CARD IS EXISTS
    // if(!empty($id_card)){
    //     $fetch_expence = $connection->prepare("SELECT * FROM cards WHERE id = ? ");
    //     $fetch_expence->bind_param('i' , $id_card)  ;
    //     $fetch_expence->execute() ;
    //     $data_object = $fetch_expence->get_result() ;
    //     $data_array = $data_object->fetch_assoc()   ; 
    // }