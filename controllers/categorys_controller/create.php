<?php

session_start() ;
unset($_SESSION['error']);
unset($_SESSION['success']);
include '../../connection/connection.php';


$ERRORS = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    isset($_POST['name']) ? $name = trim($_POST['name']) : $name = '';
    isset($_POST['limits']) ? $limits = trim($_POST['limits']) : $limits = null ;
    isset($_POST['description']) ? $description = trim($_POST['description']) : $description = null;
    isset($_POST['user_id']) ? $user_id = $_POST['user_id'] : $user_id = '';

    if (!$name) $ERRORS['name'] = 'name is required';
    if (!$user_id) $ERRORS['user_id'] = 'user_id is required';
    if (!$limits) $ERRORS['limits'] = 'limits is required';

    if (count($ERRORS)) {
        $connection->close();
        $_SESSION['error'] = $ERRORS ;
        header('location: ../../index.php?error=messing_values');
        exit;
    }

    if (!preg_match('/^[a-zA-Z0-9\s.,!?-]{3,100}$/', $name)) $ERRORS['name'] = 'Name must be 3-100 characters with valid characters';
    if (!preg_match('/^[1-9][0-9]*$/', $user_id)) $ERRORS['user_id'] = 'user_id is unvalid';
    if(!floatval($limits))  $ERRORS['limits'] = 'limits is unvalid';

    if (count($ERRORS)) {
        $connection->close();
        $_SESSION['error'] = $ERRORS ;
        header('location: ../../index.php?error=invalide_values');
        exit;
    }

    $name = htmlspecialchars($name);
    $limits = floatval($limits) ;
    if(!empty($description)) $description = htmlspecialchars($description);

    $statement = $connection->prepare('INSERT INTO categories (name , limits , description , user_id) VALUES( ? , ? , ? , ? )') ;
    if(!$statement){
        $ERRORS['connection'] = "connection: $connection->error" ;
        $connection->close();
        $_SESSION['error'] = $ERRORS ;
        header('location: ../../index.php?error=invalid_statement');
        exit;
    }


    $statement->bind_param('sdsi' , $name , $limits , $description , $user_id) ;
    $status = $statement->execute() ;

    if(!$status){
        $ERRORS['error'] = "sql error : $statement->error" ;
        $_SESSION['error'] = $ERRORS ;
        $connection->close();
        header('location: ../../index.php?error=invalide_values');
        exit;
    }

}

$connection->close();
$_SESSION['success'] = 'categiory created successfuly' ;
header('location: ../../index.php?success=categiory_created_successfuly');
exit;
