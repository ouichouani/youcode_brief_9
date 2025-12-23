<?php

include '../connection/connection.php';
$ERRORS = [];

if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST["method"] == 'DELETE') {
    $id = $_POST['id'] ?? null;


    //ID VALIDATION
    if (!$id) {
        $ERRORS['id'] = 'id is required';
    } else if (!is_numeric($id) || $id <= 0) {
        $ERRORS['id'] = 'id must be a positive number';
    }

    //IF THERE IS AN ERROR
    if (count($ERRORS)) {
        session_start();
        $_SESSION['ERRORS'] = $ERRORS;
        $connection->close();
        header('Location: ../index.php?error=validation');
        exit;
    }


    $statement = $connection->prepare('DELETE FROM incomes WHERE id = ? ');
    if(!$statement){
        session_start() ;
        $_SESSION['ERROR'] = "ERROR  $connection->error" ;
        $connection->close() ; 
        header('Location: ../index.php?error=server error') ;
        exit() ;
    }

    $statement->bind_param('i', $id);
    $state = $statement->execute();

    if(!$state){

        session_start() ;
        $_SESSION['ERROR'] = "ERROR  $statement->error" ;
        $connection->close() ; 
        header('Location: ../index.php?error=sql error') ;
        exit() ;
    }

    
    $statement->close();
    session_start();
    $_SESSION['SUCCESS'] = 'expense updated successfully';
}


$connection->close() ;
header("Location: ../index.php?success=delete successfully");
exit;
