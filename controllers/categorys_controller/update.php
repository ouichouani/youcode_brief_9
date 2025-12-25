<?php

session_start();
unset($_SESSION['error']);
unset($_SESSION['success']);
include '../../connection/connection.php';


$ERRORS = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['method']) && $_POST['method'] == 'PUT') {

    $type='';
    $columns = [];
    $params = [];

    isset($_POST['name']) ? $name = trim($_POST['name']) : $name = '';
    isset($_POST['limits']) ? $limits = trim($_POST['limits']) : $limits = '';
    isset($_POST['description']) ? $description = trim($_POST['description']) : $description = null;
    isset($_POST['id']) ? $id = $_POST['id'] : $id = '';


    if (!empty($name)) {
        if (!preg_match('/^[a-zA-Z0-9\s.,!?-]{3,100}$/', $name)) $ERRORS['name'] = 'Name must be 3-100 characters with valid characters';
        $name = htmlspecialchars($name);
        
        $type .= 's' ;
        $columns[] = 'name = ?' ;
        $params[] = $name ;
    }

    if (!empty($limits)) {
        if (floatval($limits)) $ERRORS['limits'] = 'limit is not valid';
        $limits = htmlspecialchars($limits);

        $type .= 'd' ;
        $columns[] = 'limits = ?' ;
        $params[] = $limits ;
    }
    if (!empty($description)) {
        $description = htmlspecialchars($description);
        
        $type .= 's' ;
        $columns[] = 'description = ?' ;
        $params[] = $description ;
    }
    
    if (!$id){
        $ERRORS['id'] = 'id is required';
        if (!preg_match('/^[1-9][0-9]*$/', $id)) $ERRORS['id'] = 'id is unvalid regex';
    }

    $type .= 'i' ;
    $params[] = $id ;

    if (count($ERRORS)) {
        $connection->close();
        $_SESSION['error'] = $ERRORS;
        header('location: ../../index.php?error=invalide_values');
        exit;
    }

    
    if (empty($name) && empty($description) && empty($balance)) {
        $connection->close(); 
        header('location: ../../index.php?error=nothing_to_update');
        exit;
    }

    $statement = $connection->prepare("UPDATE categories SET " . implode(' ,' , $columns) . " WHERE id = ? ");
    $statement->bind_param($type, ...$params);
    $status = $statement->execute();
    
    
    if (!$status) {
        $ERRORS['error'] = "sql error : $statement->error";
        $_SESSION['error'] = $ERRORS;
        $connection->close();
        header('location: ../../index.php?error=invalide_values');
        exit;
    }

    $connection->close();
    $_SESSION['success'] = 'categirie created successfuly';
    header('location: ../../index.php?success=categirie_created_successfuly');
    exit;
}

$connection->close();
$_SESSION['error'] = ['message'=>'method is not valid'];
header('location: ../../index.php?error=method_is_not_valid');
exit;
