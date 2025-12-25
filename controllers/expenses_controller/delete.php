<?php

session_start() ;
include '../../connection/connection.php';
unset($_SESSION['error']);
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST["method"] == 'DELETE') {
    $ERRORS = [];
    $id = $_POST['id'] ?? null;


    //ID VALIDATION
    if (!$id) {
        $ERRORS['id'] = 'id is required';
    } else if (!is_numeric($id) || $id <= 0) {
        $ERRORS['id'] = 'id must be a positive number';
    }

    //IF THERE IS AN ERROR
    if (count($ERRORS)) {
        $_SESSION['ERRORS'] = $ERRORS;
        $connection->close();
        header('Location: ../../index.php?error=validation');
        exit;
    }


    $statement = $connection->prepare('DELETE FROM expenses WHERE id = ? ');
    if(!$statement){
        $_SESSION['ERROR'] = "ERROR  $connection->error" ;
        $connection->close() ; 
        header('Location: ../../index.php?error=server error') ;
        exit() ;
    }

    $statement->bind_param('i', $id);
    $state = $statement->execute();

    if(!$state){
        $_SESSION['ERROR'] = "ERROR  $statement->error" ;
        $connection->close() ; 
        header('Location: ../../index.php?error=sql error') ;
        exit() ;
    }

    
    $statement->close();
    $_SESSION['SUCCESS'] = 'expense updated successfully';
}


$connection->close() ;
header("Location: ../../index.php?success=delete successfully");
exit;
