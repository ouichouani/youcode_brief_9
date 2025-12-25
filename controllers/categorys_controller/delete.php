<?php

session_start() ;
include '../../connection/connection.php';
unset($_SESSION['error']);
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['method'] == 'DELETE') {

    $ERRORS = [];
    isset($_POST['id']) ? $id = $_POST['id'] : $id = null;

    if (!$id) {
        $ERRORS['id'] = 'id is required';
        if (!preg_match('/^[1-9][0-9]*$/', $id)) $ERRORS['id'] = 'id is unvalid regex';
    }

    if (count($ERRORS)) {
        $connection->close();
        $_SESSION['error'] = $ERRORS;
        header('location: ../../index.php?error=invalide_id_value');
        exit;
    }


    $statement = $connection->prepare('DELETE from categories WHERE id = ?');
    $statement->bind_param('i' , $id) ;
    $status = $statement->execute();

    if (!$status) {
        $ERRORS['error'] = "sql error : $statement->error";
        $_SESSION['error'] = $ERRORS;
        $connection->close();
        header('location: ../../index.php?error=delete_faild');
        exit;
    }
}


$connection->close();
$_SESSION['success'] = 'category deleted successfully';
header('location: ../../index.php?success=category_deleted_successfuly');
exit;

