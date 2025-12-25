<?php

// session_start();
// include '../../connection/connection.php';
include 'connection/connection.php';

unset($_SESSION['error']);
unset($_SESSION['success']);

$TOTAL_EXPENSES = 0 ;
$EXPENCES = [];
$ERRORS = [];

$statement = $connection->query("SELECT e.* FROM expenses e 
INNER JOIN cards c 
ON e.id_card = c.id 
INNER JOIN users u 
ON c.user_id = u.id WHERE c.user_id = ".$_SESSION['AuthUser']['id'] ." AND e.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
// $statement = $connection->query('SELECT * FROM expenses WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');

if (!$statement) {
    $_SESSION['error'] = 'Database error: ' . $connection->error;
    $ERRORS[] = 'Query execution failed';
    $connection->close();
    header('Location: ../../index.php?error_expenses_fetched');
    exit;
}

if ($statement->num_rows > 0) {
    while ($row = $statement->fetch_assoc()) {
        $EXPENCES[] = $row;
        $TOTAL_EXPENSES += $row['amount'] ;
    }
    $_SESSION['EXPENCES'] = $EXPENCES;
    $_SESSION['TOTAL_EXPENSES'] = $TOTAL_EXPENSES;
    $_SESSION['success'] = 'expenses fetched successfully';
} else {
    $EXPENCES[] = ['message' => 'No expenses available'];
    $_SESSION['TOTAL_EXPENSES'] = 0 ;
    $_SESSION['EXPENCES'] = $EXPENCES;
    $_SESSION['info'] = 'No expenses available';
}

$statement->close();
$connection->close();
// header('Location: ../../index.php?expenses_fetched_successfully');
// exit;

