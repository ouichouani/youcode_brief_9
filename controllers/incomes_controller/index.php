<?php

// session_start();
// include '../../connection/connection.php';
include 'connection/connection.php';
unset($_SESSION['error']);
unset($_SESSION['success']);

$TOTAL_INCOMES = 0 ;
$INCOMES = [];
$ERRORS = [];

// $statement = $connection->query('SELECT * FROM incomes WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
$statement = $connection->query("SELECT i.* FROM incomes i 
INNER JOIN cards c 
ON i.id_card = c.id 
INNER JOIN users u 
ON c.user_id = u.id WHERE c.user_id = ".$_SESSION['AuthUser']['id'] ." AND i.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");

if (!$statement) {
    $_SESSION['error'] = 'Database error: ' . $connection->error;
    $ERRORS[] = 'Query execution failed';
    $connection->close();
    header('Location: ../../index.php?error_incomes_fetched');
    exit;
}

if ($statement->num_rows > 0) {
    while ($row = $statement->fetch_assoc()) {
        $INCOMES[] = $row;
        $TOTAL_INCOMES += $row['amount'] ;
    }
    $_SESSION['INCOMES'] = $INCOMES;
    $_SESSION['TOTAL_INCOMES'] = $TOTAL_INCOMES;
    $_SESSION['success'] = 'incomes fetched successfully';
} else {
    $_SESSION['TOTAL_INCOMES'] = 0 ;
    $INCOMES[] = ['message' => 'No incomes available'];
    $_SESSION['INCOMES'] = $INCOMES;
    $_SESSION['info'] = 'No incomes available';
}

$statement->close();
$connection->close();
// header('Location: ../../index.php?incomes_fetched_successfully');
// exit;