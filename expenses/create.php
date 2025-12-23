<?php

include '../connection/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $ERRORS = [] ;

    //IF VALUE NOT EXISTS , SET NULL IN VARIABLE 
    $montant = $_POST['montant'] ?? null;
    $description = trim($_POST['description'] ?? '', ' ');
    $created_at = $_POST['created_at'] ?? null;

    //VALIDATION MONTANT
    if (!$montant) {
        $ERRORS['montant'] = 'montant is required';
    } else if ($montant < 0) {
        $ERRORS['montant']  = 'montant can\'t be negative';
    } else if (!preg_match('/^\d+(\.\d{1,2})?$/', $montant)) {
        $ERRORS['montant'] = 'montant is invalid';
    }



    //IF THERE IS AN ERROR
    if (count($ERRORS)) {
        session_start();
        $_SESSION['ERRORS'] = $ERRORS;
        $connection->close();
        header('Location: ../index.php?error=validation');
        exit;
    }

    $montant = floatval($montant); // MONTANT VALIDATION 
    $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

    $stat = $connection->prepare('INSERT INTO expenses (montant, description , created_at) VALUES (? , ?  , ?)');

    if (!$stat) {
        session_start();
        $_SESSION['ERRORS'] = ["Database error: " . $connection->error];
        $connection->close();
        header("Location: ../index.php?error=database");
        exit;
    }

    $stat->bind_param('dss', $montant, $description, $created_at);
    $status = $stat->execute();


    if (!$status) {
        session_start();
        $_SESSION['ERRORS'] = ['creation failed' . $stat->error];
        $stat->close();
        $connection->close();
        header('Location: ../index.php?error=creation_failed');
        exit;
    }

    //CREATE SUCCEED
    $stat->close();
    session_start();
    $_SESSION['SUCCESS'] = 'expense created successfully';
}

$connection->close();
header('Location: ../index.php');
exit;

