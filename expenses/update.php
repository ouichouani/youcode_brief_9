<?php

include '../connection/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['method'] == 'PUT') {


    $ERRORS = [] ;

    //IF VALUE NOT EXISTS , SET NULL IN VARIABLE 
    $montant = $_POST['montant'] ?? null;
    $description = trim($_POST['description'] ?? '', ' ');
    $created_at = $_POST['created_at'] ?? null;
    $id = $_POST['id'] ?? null;

    //VALIDATION MONTANT
    if (!$montant) {
        $ERRORS['montant'] = 'montant is required';
    } else if ($montant < 0) {
        $ERRORS['montant']  = 'montant can\'t be negative';
    } else if (!preg_match('/^\d+(\.\d{1,2})?$/', $montant)) {
        $ERRORS['montant'] = 'montant is invalid';
    }

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

    $montant = floatval($montant); // MONTANT VALIDATION 
    $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    $id = intval($id);



    $stat = $connection->prepare('UPDATE expenses SET montant = ? , description = ? , created_at = ? WHERE id = ?');

    if (!$stat) {
        session_start();
        $_SESSION['ERRORS'] = ["Database error: " . $connection->error];
        $connection->close();
        header("Location: ../index.php?error=database");
        exit;
    }

    $stat->bind_param('dssi', $montant, $description, $created_at, $id);
    $status = $stat->execute();


    if (!$status) {
        session_start();
        $_SESSION['ERRORS'] = ['update failed' . $stat->error];
        $stat->close();
        $connection->close();
        header('Location: ../index.php?error=update_failed');
        exit;
    }

    //UPDATE SUCCEED
    $stat->close();
    session_start();
    $_SESSION['SUCCESS'] = 'expense updated successfully';
}

$connection->close();
header('Location: ../index.php');
exit;
